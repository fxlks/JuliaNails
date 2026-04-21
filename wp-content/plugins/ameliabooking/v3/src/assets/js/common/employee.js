import moment from "moment";
import {settings} from "../../../plugins/settings";
import {useTimeInSeconds} from "./date";
import {useAppointmentServicePrice} from "./appointments";

function useParsedCustomPricing (service) {
    if (!('customPricing' in service) || service.customPricing === null) {
        service.customPricing = {enabled: null, durations: {}, persons: {}, periods: {default: [], custom: []}}

        service.customPricing.durations[service.duration] = {price: service.price, rules: []}
    } else {
        let customPricing = (typeof service.customPricing === 'object')
          ? service.customPricing
          : JSON.parse(service.customPricing)

        service.customPricing = {
            enabled: null,
            durations: {},
            persons: {},
            periods: 'periods' in customPricing ? customPricing.periods : {default: [], custom: []}
        }

        service.customPricing.durations[service.duration] = {price: service.price, rules: []}

        service.customPricing.durations = Object.assign(
          service.customPricing.durations,
          customPricing.durations
        )

        customPricing.persons = 'persons' in customPricing ? customPricing.persons : {}

        let persons = {}

        if (Object.keys(customPricing.persons).length) {
            let range = Object.keys(customPricing.persons)[0] - 1

            persons[range] = {
                from: 1,
                range: range,
                price: service.price,
                rules: []
            }

            Object.keys(customPricing.persons).forEach((person, index) => {
                range = index !== Object.keys(customPricing.persons).length - 1
                  ? Object.keys(customPricing.persons)[index + 1] - 1
                  : service.maxCapacity

                persons[range] = {
                    from: parseInt(person),
                    range: range,
                    price: customPricing.persons[person].price,
                    rules: customPricing.persons[person].rules
                }
            })
        }

        service.customPricing.persons = persons

        if (customPricing.enabled === 'duration' || customPricing.enabled === true) {
            service.customPricing.enabled = 'duration'
        } else if (customPricing.enabled === 'person') {
            service.customPricing.enabled = 'person'
        } else if (customPricing.enabled === 'period') {
            service.customPricing.enabled = 'period'
        }
    }

    return service.customPricing
}

function getEmployeeServicePrice (store, providerId, serviceId) {
    let employeeService = store.getters['entities/getEmployeeService'](providerId, serviceId)

    let duration = store.getters['booking/getBookingDuration'] ? store.getters['booking/getBookingDuration'] : employeeService.duration

    let persons = store.getters['booking/getBookingPersons']

    return useAppointmentServicePrice(employeeService, persons, duration)
}

function sortForEmployeeSelection (store, employeesIds, serviceId) {
    switch (settings.appointments.employeeSelection) {
        case 'roundRobin': {
            let lastBookedProviderId = store.getters['booking/getLastBookedProviderId']
            employeesIds = employeesIds.map(e => parseInt(e)).sort((a,b) => a-b)
            // ! employeeId was not used in the loop
            for (let employeeId of employeesIds) {
                if (parseInt(employeesIds[0]) > parseInt(lastBookedProviderId)) {
                    break
                }
                employeesIds.push(employeesIds.shift())

            }
            return employeesIds
        }
        case 'lowestPrice':
            return employeesIds.sort((emp1, emp2) => {
                let price1 = getEmployeeServicePrice(store, emp1, serviceId)
                let price2 = getEmployeeServicePrice(store, emp2, serviceId)
                if (price1 < price2) {
                    return -1
                } else if (price1 === price2) {
                    return emp1 < emp2 ? -1 : 1
                } else {
                    return 1
                }
            })
        case 'highestPrice':
            return employeesIds.sort((emp1, emp2) => {
                let price1 = getEmployeeServicePrice(store, emp1, serviceId)
                let price2 = getEmployeeServicePrice(store, emp2, serviceId)
                if (price1 < price2) {
                    return 1
                } else if (price1 === price2) {
                    return emp1 < emp2 ? -1 : 1
                } else {
                    return -1
                }
            })
        case 'random': default:
            return employeesIds
    }
}

function checkLimitPerEmployee (employeesIds, bookingIndex, bookings, booking, appCount, chosenEmployees, serviceId) {
    let filteredEmployeeIds = []
    for (let employeeId of employeesIds) {
        let count = appCount && appCount[employeeId] && appCount[employeeId][booking.date] ? appCount[employeeId][booking.date] : 0

        let otherServiceBookings = chosenEmployees.filter(e => e.providerId === employeeId && e.date === booking.date && e.serviceId !== serviceId && !e.existingApp)
        let otherBookings        = bookings.filter((e, index) => e.providerId === employeeId && e.date === booking.date && bookingIndex !== index && !e.existingApp)

        if (otherBookings.length + otherServiceBookings.length + count < settings.roles.limitPerEmployee.numberOfApp) {
            filteredEmployeeIds.push(employeeId)
        }
    }
    if (filteredEmployeeIds.length === 0) {
        return {'employeeIds': filteredEmployeeIds, 'bookingFailed' : bookingIndex}
    }
    return {'employeeIds': filteredEmployeeIds, 'bookingFailed' : null}
}

function getFrontendPeriodList (periodList) {
    periodList.forEach((period) => {
        period.startTime = period.startTime.substring(0, 5)
        period.endTime = period.endTime.substring(0, 5)
        period.locationId = period.locationId ? period.locationId : null
        period.periodServiceList = period.periodServiceList.map(i => i.serviceId)
        period.periodLocationList = period.periodLocationList.map(i => i.locationId)
    })

    return periodList.sort((a, b) => {
        return useTimeInSeconds(a.startTime) - useTimeInSeconds(b.startTime);
    })
}

function useFrontendEmployeeServiceList (store, employeeServiceList) {
    let serviceList = {}

    store.getters['entities/getCategories'].forEach((category) => {
        category.serviceList.forEach((service) => {
            let employeeService = employeeServiceList.find(s => s.id === service.id)

            if (!(service.categoryId in serviceList)) {
                serviceList[service.categoryId] = {}
            }

            if (!(service.id in serviceList[service.categoryId])) {
                serviceList[service.categoryId][service.id] = {}
            }

            serviceList[service.categoryId][service.id] = typeof employeeService === 'undefined' ? {
                enabled: false,
                price: parseFloat(service.price),
                minCapacity: service.minCapacity,
                maxCapacity: service.maxCapacity,
                customPricing: service.customPricing,
            } : {
                enabled: true,
                price: parseFloat(employeeService.price),
                minCapacity: employeeService.minCapacity,
                maxCapacity: employeeService.maxCapacity,
                customPricing: employeeService.customPricing,
            }
        })
    })

    return serviceList
}

function useFrontendEmployee (store, employee) {
    let weekDayList = []

    settings.weekSchedule.forEach((weekDay, index) => {
        let employeeWeekDay = employee.weekDayList.find(i => parseInt(i.dayIndex) === index + 1)

        let timeOutList = []

        if (typeof employeeWeekDay !== 'undefined') {
            employeeWeekDay.timeOutList.forEach((timeOut) => {
                timeOutList.push({
                    startTime: timeOut.startTime.substring(0, 5),
                    endTime: timeOut.endTime.substring(0, 5),
                })
            })
        }

        weekDayList.push(
          typeof employeeWeekDay === 'undefined'
            ? {
                enabled: false,
                id: null,
                dayIndex: index + 1,
                startTime: '',
                endTime: '',
                periodList: [],
                timeOutList: [],
            }
            : Object.assign(
              {},
              employeeWeekDay,
              {
                  enabled: true,
                  startTime: employeeWeekDay.startTime.substring(0, 5),
                  endTime: employeeWeekDay.endTime.substring(0, 5),
                  periodList: getFrontendPeriodList(employeeWeekDay.periodList),
                  timeOutList: timeOutList,
              }
            )
        )
    })

    let specialDayList = []

    employee.specialDayList.forEach((specialDay) => {
        specialDayList.push({
            id: specialDay.id,
            range: [moment(specialDay.startDate).toDate(), moment(specialDay.endDate).toDate()],
            periodList: getFrontendPeriodList(specialDay.periodList),
        })
    })

    let dayOffList = []

    employee.dayOffList.forEach((dayOff) => {
        dayOffList.push({
            id: dayOff.id,
            name: dayOff.name,
            repeat: dayOff.repeat,
            range: [moment(dayOff.startDate).toDate(), moment(dayOff.endDate).toDate()],
        })
    })

    let descriptionMode = !employee.description || employee.description.startsWith('<!-- Content -->') ? 'text' : 'html'

    let description = employee.description ? employee.description.replace('<!-- Content -->', '') : ''

    return {
        id: employee.id,
        firstName: employee.firstName,
        lastName: employee.lastName,
        email: employee.email,
        phone: employee.phone,
        description: description,
        descriptionMode: descriptionMode,
        externalId: employee.externalId,
        googleCalendar: employee.googleCalendar,
        outlookCalendar: employee.outlookCalendar,
        appleCalendarId: employee.appleCalendarId,
        googleCalendarId: employee.googleCalendarId,
        outlookCalendarId: employee.outlookCalendarId,
        employeeAppleCalendar: employee.employeeAppleCalendar ? employee.employeeAppleCalendar : {
            iCloudId: null,
            appSpecificPassword: null
        },
        stripeConnect: employee.stripeConnect,
        zoomUserId: employee.zoomUserId,
        locationId: employee.locationId ? employee.locationId : '',
        serviceList: employee.serviceList,
        weekDayList: weekDayList,
        specialDayList: specialDayList,
        dayOffList: dayOffList,
        badgeId: employee.badgeId,
    }
}

function getBackendPeriodList (store, periodList) {
    let dayPeriodList = []

    let servicesIds = useEmployeeServices(store).map(i => i.id)

    periodList.forEach((period) => {
        dayPeriodList.push({
            id: period.id,
            locationId: period.locationId ? period.locationId : null,
            startTime: period.startTime + ':00',
            endTime: period.endTime + ':00',
            periodServiceList: period.periodServiceList
              .filter(id => servicesIds.indexOf(id) !== -1)
              .map(id => new Object({id: null, serviceId: id})),
            periodLocationList: period.periodLocationList.map(id => new Object({id: null, locationId: id})),
        })
    })

    return dayPeriodList
}

function getBackendTimeOutList (store, timeOutList) {
    let dayTimeOutList = []

    timeOutList.forEach((timeOut) => {
        dayTimeOutList.push({
            id: timeOut.id,
            startTime: timeOut.startTime + ':00',
            endTime: timeOut.endTime + ':00',
        })
    })

    return dayTimeOutList
}

function haveSameUniqueSortedValues (arr1, arr2) {
  const unique1 = [...new Set(arr1)].sort()
  const unique2 = [...new Set(arr2)].sort()

  return unique1.length === unique2.length && unique1.every((v, i) => v === unique2[i])
}

function isChangedDayOff (savedDayOff, dayOff) {
  return savedDayOff.name !== dayOff.name ||
  savedDayOff.startDate !== dayOff.startDate ||
  savedDayOff.endDate !== dayOff.endDate ||
  savedDayOff.repeat !== dayOff.repeat
}

function isChangedSpecialDay (savedSpecialDay, specialDay) {
  let savedPeriods = {}

  savedSpecialDay.periodList.forEach((period) => {
    savedPeriods[period.startTime + '-' + period.endTime] = {
      locationId: period.locationId,
      servicesIds: period.periodServiceList.map(s => s.serviceId),
      locationsIds: period.periodLocationList.map(l => l.locationId),
    }
  })

  let periods = {}

  specialDay.periodList.forEach((period) => {
    periods[period.startTime + '-' + period.endTime] = {
      locationId: period.locationId,
      servicesIds: period.periodServiceList.map(s => s.serviceId),
      locationsIds: period.periodLocationList.map(l => l.locationId),
    }
  })

  let changedSpecialDay =
    savedSpecialDay.startDate !== specialDay.startDate ||
    savedSpecialDay.endDate !== specialDay.endDate ||
    !haveSameUniqueSortedValues(Object.keys(savedPeriods), Object.keys(periods))

  if (!changedSpecialDay) {
    Object.keys(savedPeriods).forEach((key) => {
      if (
        !(key in periods) ||
        savedPeriods[key].locationId !== periods[key].locationId ||
        !haveSameUniqueSortedValues(
          savedPeriods[key].servicesIds,
          periods[key].servicesIds
        ) ||
        !haveSameUniqueSortedValues(
          savedPeriods[key].locationsIds,
          periods[key].locationsIds
        )
      ) {
        changedSpecialDay = true
      }
    })
  }

  return changedSpecialDay
}

function modifyDayList (store, employee) {
  let modifiedDayOffList = []

  let removedDayOffList = []

  store.getters['employee/getSavedDayOffList'].forEach((savedDay) => {
    let index = employee.dayOffList.findIndex(i => i.id === savedDay.id)

    if (index === -1) {
      removedDayOffList.push({id: savedDay.id})
    } else if (isChangedDayOff(savedDay, employee.dayOffList[index])) {
      modifiedDayOffList.push(employee.dayOffList[index])
    }
  })

  employee.dayOffList.forEach((day) => {
    if (day.id === null) {
      modifiedDayOffList.push(day)
    }
  })

  employee.dayOffList = modifiedDayOffList

  employee.removedDayOffList = removedDayOffList

  let modifiedSpecialDayList = []

  let removedSpecialDayList = []

  store.getters['employee/getSavedSpecialDayList'].forEach((savedDay) => {
    let index = employee.specialDayList.findIndex(i => i.id === savedDay.id)

    if (index === -1) {
      removedSpecialDayList.push({id: savedDay.id})
    } else if (isChangedSpecialDay(savedDay, employee.specialDayList[index])) {
      modifiedSpecialDayList.push(employee.specialDayList[index])
    }
  })

  employee.specialDayList.forEach((day) => {
    if (day.id === null) {
      modifiedSpecialDayList.push(day)
    }
  })

  employee.specialDayList = modifiedSpecialDayList

  employee.removedSpecialDayList = removedSpecialDayList

  delete employee.savedSpecialDayList

  delete employee.savedDayOffList
}

function useBackendEmployee (store, timeZone) {
    let employee = store.getters['employee/getEmployee']

    let serviceList = []

    Object.keys(employee.serviceList).forEach((categoryId) => {
        Object.keys(employee.serviceList[categoryId]).forEach((serviceId) => {
            if (employee.serviceList[categoryId][serviceId].enabled) {
                let service = store.getters['entities/getCategory'](categoryId).serviceList.find(i => i.id === parseInt(serviceId))

                let price = employee.serviceList[categoryId][serviceId].customPricing.enabled === 'duration'
                  ? parseFloat(employee.serviceList[categoryId][serviceId].customPricing.durations[service.duration].price)
                  : parseFloat(employee.serviceList[categoryId][serviceId].price)

                let employeeService = {
                    id: parseInt(serviceId),
                    minCapacity: parseInt(employee.serviceList[categoryId][serviceId].minCapacity),
                    maxCapacity: parseInt(employee.serviceList[categoryId][serviceId].maxCapacity),
                    price: price,
                    customPricing: {
                        enabled: employee.serviceList[categoryId][serviceId].customPricing.enabled,
                        durations: {},
                        persons: {},
                        periods: employee.serviceList[categoryId][serviceId].customPricing.periods,
                    },
                }

                Object.keys(employee.serviceList[categoryId][serviceId].customPricing.durations).forEach((duration) => {
                    employeeService.customPricing.durations[duration] = {
                        price: parseFloat(employee.serviceList[categoryId][serviceId].customPricing.durations[duration].price),
                        rules: [],
                    }
                })

                Object.keys(employee.serviceList[categoryId][serviceId].customPricing.persons).forEach((range, index) => {
                    if (index !== 0) {
                        employeeService.customPricing.persons[employee.serviceList[categoryId][serviceId].customPricing.persons[range].from] = {
                            price: parseFloat(employee.serviceList[categoryId][serviceId].customPricing.persons[range].price),
                            rules: [],
                        }
                    }
                })

                delete employeeService.customPricing.durations[service.duration]

                employeeService.customPricing = JSON.stringify(employeeService.customPricing)

                serviceList.push(employeeService)
            }
        })
    })

    let weekDayList = []

    employee.weekDayList.forEach((weekDay) => {
        if (weekDay.enabled) {
            let periodList = getBackendPeriodList(store, weekDay.periodList)

            if (periodList.length) {
                let timeOutList = getBackendTimeOutList(store, weekDay.timeOutList)

                weekDayList.push({
                    id: weekDay.id,
                    dayIndex: weekDay.dayIndex,
                    startTime: periodList.length ? periodList[0].startTime : weekDay.startTime,
                    endTime: periodList.length ? periodList[periodList.length - 1].endTime : weekDay.endTime,
                    periodList: periodList,
                    timeOutList: timeOutList,
                })
            }
        }
    })

    let specialDayList = []

    employee.specialDayList.forEach((specialDay) => {
        let periodList = getBackendPeriodList(store, specialDay.periodList)

        if (periodList.length) {
            specialDayList.push({
                id: specialDay.id,
                startDate: moment(specialDay.range[0]).format('YYYY-MM-DD'),
                endDate: moment(specialDay.range[1]).format('YYYY-MM-DD'),
                periodList: periodList,
            })
        }
    })

    let dayOffList = []

    employee.dayOffList.forEach((dayOff) => {
        dayOffList.push({
            id: dayOff.id,
            name: dayOff.name,
            startDate: moment(dayOff.range[0]).format('YYYY-MM-DD'),
            endDate: moment(dayOff.range[1]).format('YYYY-MM-DD'),
            repeat: dayOff.repeat,
        })
    })

    let result = Object.assign(
      {},
      employee,
      {
          description: store.getters['employee/getDescription'] && store.getters['employee/getDescriptionMode'] === 'text'
            ? '<!-- Content -->' + store.getters['employee/getDescription']
            : store.getters['employee/getDescription'],
          serviceList: serviceList,
          weekDayList: weekDayList,
          specialDayList: specialDayList,
          dayOffList: dayOffList,
          timeZone: store.getters['cabinet/getTimeZone'] === timeZone
            ? ''
            : store.getters['cabinet/getTimeZone'],
      }
    )

    modifyDayList(store, result)

    delete result.descriptionMode

    return result
}

function useEmployeeServices (store) {
    let serviceIds = []

    Object.keys(store.getters['employee/getServiceList']).forEach(categoryId => {
        Object.keys(store.getters['employee/getServiceList'][categoryId]).forEach((serviceId) => {
            if (store.getters['employee/getServiceList'][categoryId][serviceId].enabled) {
                serviceIds.push(parseInt(serviceId))
            }
        })
    })

    return store.getters['entities/getServices'].filter(i => serviceIds.indexOf(i.id) !== -1)
}

function useFeaturesAndIntegrations (service, isPanel) {
  if (!isPanel && !settings.featuresIntegrations.extras.enabled) {
    service.extras = []
  }

  if (!settings.featuresIntegrations.customPricing.enabled) {
    service.customPricing = null
  }

  if (!settings.featuresIntegrations.depositPayment.enabled) {
    service.depositPayment = 'disabled'
  }

  if (!settings.featuresIntegrations.recurringAppointments.enabled) {
    service.recurringCycle = 'disabled'
  }
}

export {
    useEmployeeServices,
    useParsedCustomPricing,
    sortForEmployeeSelection,
    checkLimitPerEmployee,
    useFrontendEmployee,
    useBackendEmployee,
    useFrontendEmployeeServiceList,
    useFeaturesAndIntegrations,
}
