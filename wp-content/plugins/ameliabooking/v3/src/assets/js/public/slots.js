import moment from 'moment'
import httpClient from '../../../plugins/axios'
import { useSortedDateStrings, useUrlParams } from '../common/helper'
import { settings } from '../../../plugins/settings'
import { useAvailableSlots, useDuration } from '../common/appointments'
import { useCartItem } from './cart'

function useLocalFromUtcSlots (slots) {
  let formattedSlots = {}

  for (let date in slots) {
    for (let time in slots[date]) {
      let dateTime = moment
        .utc(date + ' ' + time, 'YYYY-MM-DD HH:mm')
        .local()
        .format('YYYY-MM-DD HH:mm')
        .split(' ')

      if (!(dateTime[0] in formattedSlots)) {
        formattedSlots[dateTime[0]] = {}
      }

      formattedSlots[dateTime[0]][dateTime[1]] = slots[date][time]
    }
  }

  return formattedSlots
}

function useAppointmentParams (store) {
  let employeeId = store.getters['booking/getEmployeeId']

  return {
    queryTimeZone: settings.general.showClientTimeZone ? Intl.DateTimeFormat().resolvedOptions().timeZone : null,
    monthsLoad: 1,
    locationId: store.getters['booking/getLocationId'],
    serviceId: store.getters['booking/getServiceId'],
    serviceDuration: store.getters['booking/getBookingDuration'],
    providerIds: !employeeId ? store.getters['entities/filteredEmployees'](
      store.getters['booking/getSelection']
    ).map(item => item.id) : [employeeId],
    extras: JSON.stringify(
      store.getters['entities/getService'](
        store.getters['booking/getServiceId']
      ).extras.map(
        extra => extra.quantity ? (
          {
            id: extra.id,
            quantity: extra.quantity
          }
        ) : null
      ).filter(
        extra => extra !== null
      )
    ),
    group: 1,
    page: 'booking',
    structured: true,
    persons: store.getters['booking/getBookingPersons']
  }
}

function useAppointmentSlots (params, fetchedSlots, callback, customCallback, waitingListOptions = null) {
  httpClient.get(
    '/slots',
    {params: useUrlParams(params)}
  ).then(response => {
    let resultSlots = 'queryTimeZone' in params && params.queryTimeZone
      ? useLocalFromUtcSlots(response.data.data.slots) : response.data.data.slots

    let slots = fetchedSlots !== null ? fetchedSlots : resultSlots

    if (fetchedSlots !== null) {
      Object.keys(resultSlots).forEach((date) => {
        slots[date] = resultSlots[date]
      })
    }

    let occupied = 'queryTimeZone' in params && params.queryTimeZone
      ? useLocalFromUtcSlots(response.data.data.occupied) : response.data.data.occupied

    // Waiting list slots provided by backend (preferred)
    let waitingListSlots = {}

    // Fallback: derive on frontend if backend doesn't yet supply and waiting list enabled
    if (!Object.keys(waitingListSlots).length) {
      const wlEnabled = !!(waitingListOptions && waitingListOptions.enabled)
      if (wlEnabled) {
        Object.keys(occupied || {}).forEach(date => {
          Object.keys(occupied[date] || {}).forEach(time => {
            if (!(date in slots) || !(time in slots[date])) {
              const providersData = occupied[date][time] || []
              const serviceId = params.serviceId
              const duration = params.serviceDuration
              // Consider only rows for this service; require at least one match
              const serviceRows = providersData.filter(p => p.s === serviceId)
              if (duration && (serviceRows.length && serviceRows[0].d !== duration / 60)) return
              // Filter for selected providers only
              const selectedProviderRows = params.providerIds && params.providerIds.length
                ? serviceRows.filter(p => params.providerIds.includes(p.e))
                : serviceRows

              // Check if ALL selected providers are fully booked (no capacity available)
              const allFullyBooked = selectedProviderRows.length > 0 && selectedProviderRows.every(p => p.c <= 0)

              if (!allFullyBooked) return

              // Filter providers that have waiting list capacity
              const waitingListProviders = selectedProviderRows.filter(p => {
                return (
                  !waitingListOptions.maxCapacity ||
                  (p.w || 0) < waitingListOptions.maxCapacity
                )
              })

              // Add to waiting list only if all providers are fully booked and at least one has waiting capacity
              if (waitingListProviders.length > 0) {
                // IMPORTANT FIX: Check if this time slot exists in regular slots
                // If it does, don't add to waiting list because user can book the regular slot
                let regularSlotExists = slots[date] && slots[date][time];

                if (!regularSlotExists) {
                  if (!(date in waitingListSlots)) waitingListSlots[date] = {}
                  waitingListSlots[date][time] = waitingListProviders
                }
              }
            }
          })
        })
      }
    }

    callback(
      slots,
      occupied,
      response.data.data.minimum,
      response.data.data.maximum,
      response.data.data.busyness,
      response.data.data.appCount,
      {providerId: response.data.data.lastProvider, fromBackend: true},
      customCallback,
      waitingListSlots
    )
  })
}

function useRange(store) {
  return store.getters['booking/getMultipleAppointmentsRange']
}

function useSelectedDuration(store, value) {
  let cartItem = useCartItem(store)

  store.commit('booking/setDuration', value)

  let service = store.getters['entities/getService'](cartItem.serviceId)

  let extrasIds = store.getters['booking/getSelectedExtras'].map(i => i.extraId)

  return useDuration(value, service.extras.filter(i => extrasIds.includes(i.id)))
}

function useSelectedDate(store, date, range) {
  store.commit('booking/setMultipleAppointmentsDate', date)

  store.commit('booking/setMultipleAppointmentsRange', range)

  return useAvailableSlots(store)
}

function useSelectedTime(store, time) {
  store.commit('booking/setMultipleAppointmentsTime', time)
}

function useDeselectedDate(store) {
  let cartItem = useCartItem(store)

  store.commit('booking/unsetMultipleAppointmentsData', cartItem.index)
}

function useSlotsCallback(
  store,
  slots,
  occupied,
  minimumDateTime,
  maximumDateTime,
  busyness,
  appCount,
  lastBookedProviderId,
  searchStart,
  searchEnd
) {
  store.commit('booking/setMultipleAppointmentsSlots', slots)
  store.commit('booking/setMultipleAppointmentsOccupied', occupied)
  store.commit('booking/setMultipleAppointmentsLastDate', maximumDateTime)
  store.commit('booking/setBusyness', busyness)
  store.commit('booking/setLastBookedProviderId', lastBookedProviderId)
  store.commit('booking/setMultipleAppointmentsAppCount', appCount)

  let result = {}

  let cartItem = useCartItem(store)

  let activeService = cartItem.services[cartItem.serviceId]

  if (cartItem.index !== '' && activeService.list.length) {
    let dates = useSortedDateStrings(Object.keys(slots))

    result['calendarStartDate'] = activeService.list[cartItem.index].date
      ? activeService.list[cartItem.index].date : (dates.length ? dates[0] : null)

    result['calendarEventDate'] = activeService.list[cartItem.index].date ? activeService.list[cartItem.index].date : ''

    if (!(activeService.list[cartItem.index].date in slots)) {
      store.commit('booking/setMultipleAppointmentsDate', null)
      store.commit('booking/setMultipleAppointmentsTime', null)

      result['calendarEventSlot'] = ''

      result['calendarEventSlots'] = []

    } else if (activeService.list.length &&
      !(activeService.list[cartItem.index].time in slots[activeService.list[cartItem.index].date])
    ) {
      store.commit('booking/setMultipleAppointmentsTime', null)

      result['calendarEventSlot'] = ''
    }

    if (activeService.list.length &&
      activeService.list[cartItem.index].date &&
      (searchStart.value ? moment(activeService.list[cartItem.index].date).isSameOrAfter(searchStart.value) : true) &&
      (searchEnd.value ? moment(activeService.list[cartItem.index].date).isSameOrBefore(searchEnd.value) : true)
    ) {
      if (activeService.list[cartItem.index].date in activeService.slots) {
        let availableSlots = useAvailableSlots(store)

        result['calendarEventSlots'] = availableSlots.length ?
          availableSlots : Object.keys(activeService.slots[activeService.list[cartItem.index].date])

        if (activeService.list[cartItem.index].time) {
          result['calendarEventSlot'] = activeService.list[cartItem.index].time
        }
      }
    }
  }

  return result
}

function useSlotsPricing (store, slots, serviceId) {
  let employeesPrices = {}

  store.getters['entities/getEmployees'].forEach((item) => {
    let employeeService = item.serviceList.find(i => i.id === serviceId)

    if (typeof employeeService !== 'undefined') {
      employeesPrices[item.id] = item.serviceList.find(i => i.id === serviceId).price
    }
  })

  let result = {}

  let minPrice = null

  let midPrice = null

  let maxPrice = null

  let multipleMin = false

  let multipleMid = false

  let multipleMax = false

  Object.keys(slots).forEach((date) => {
    result[date] = {slots: {}}

    Object.keys(slots[date]).forEach((time) => {
      let haveHigh = false

      let haveLow = false

      let haveMid = false

      let minDatePrice = null

      let maxDatePrice = null

      slots[date][time].forEach((i) => {
        let price = i.p === null ? employeesPrices[i.e] : i.p

        if (minDatePrice === null || price < minDatePrice) {
          minDatePrice = price
        }

        if (maxDatePrice === null || price > maxDatePrice) {
          maxDatePrice = price
        }

        if (price === employeesPrices[i.e]) {
          if (midPrice !== null && price !== midPrice) {
            multipleMid = true
          }

          if (midPrice === null || price < midPrice) {
            midPrice = price
          }

          haveMid = true
        } else if (price < employeesPrices[i.e]) {
          if (minPrice !== null && price !== minPrice) {
            multipleMin = true
          }

          if (minPrice === null || minDatePrice < minPrice) {
            minPrice = minDatePrice
          }

          haveLow = true
        } else if (price > employeesPrices[i.e]) {
          if (maxPrice !== null && price !== maxPrice) {
            multipleMax = true
          }

          if (maxPrice === null || maxDatePrice < maxPrice) {
            maxPrice = maxDatePrice
          }

          haveHigh = true
        }
      })

      result[date].slots[time] = {
        type: haveLow && !haveHigh && !haveMid ? 'low' : (haveHigh && !haveLow && !haveMid ? 'high' : 'mid'),
        price: minDatePrice
      }
    })

    result[date].price = Object.values(result[date].slots).filter(i => i.price === null).length === 0

    let types = Object.values(result[date].slots).map(i => i.type)

    if (types.filter(i => i === 'low').length === types.length) {
      result[date].type = 'low'
    } else if (types.filter(i => i === 'high').length === types.length) {
      result[date].type = 'high'
    } else {
      result[date].type = 'mid'
    }
  })

  return {
    price: {
      low: minPrice,
      mid: midPrice,
      high: maxPrice,
      uniqueMin: !multipleMin,
      uniqueMid: !multipleMid,
      uniqueMax: !multipleMax
    },
    dates: result
  }
}

export {
  useRange,
  useSelectedDuration,
  useSelectedDate,
  useSelectedTime,
  useDeselectedDate,
  useSlotsCallback,
  useAppointmentSlots,
  useAppointmentParams,
  useSlotsPricing,
}
