// * Imported from Vue
import {
  reactive,
  ref
} from "vue";

import { useFormattedPrice } from "../common/formatting.js";

const globalSettings = reactive(window.wpAmeliaSettings)
const globalLabels = reactive(window.wpAmeliaLabels)

function useAvailableServiceIdsInCategory (shortcodeData, category, entities, employeeId = null, locationId = null) {
  let serviceIdInCategory = []
  let preselectedServices = shortcodeData && shortcodeData.service ? shortcodeData.service.split(',').map(s => parseInt(s)) : null
  if (category) {
    category.serviceList.forEach(service => {
      if (employeeId) {
        if (
          employeeId in entities.entitiesRelations
          && service.id in entities.entitiesRelations[employeeId]
          && (locationId ? entities.entitiesRelations[employeeId][service.id].find(a => a === locationId) : true)
          && service.status === 'visible'
          && service.show
          && !serviceIdInCategory.filter(el => el === service.id).length
          && (!preselectedServices || preselectedServices.includes(service.id))
        ) {
          serviceIdInCategory.push(service.id)
        }
      } else {
        entities.employees.forEach(employee => {
          if (
            employee.id in entities.entitiesRelations
            && employee.show
            && service.id in entities.entitiesRelations[employee.id]
            && (locationId ? entities.entitiesRelations[employee.id][service.id].find(a => a === locationId) : true)
            && service.status === 'visible'
            && service.show
            && !serviceIdInCategory.filter(el => el === service.id).length
            && (!preselectedServices || preselectedServices.includes(service.id))
          ) {
            serviceIdInCategory.push(service.id)
          }
        })
      }
    })
  }

  return serviceIdInCategory
}

function useEmployeesServiceCapacity(entities, serviceId) {
  let arrMax = []
  let arrMin = []

  entities.employees.forEach(employee => {
    if (
      employee.id in entities.entitiesRelations
      && serviceId in entities.entitiesRelations[employee.id]
    ) {
      let employeeService = employee.serviceList.find(service => service.id === serviceId)
      arrMax.push(employeeService.maxCapacity)
      arrMin.push(employeeService.minCapacity)
    }
  })

  let serviceMinCapacity = arrMin.reduce((prev, curr) => {
    return curr < prev ? curr : prev
  }, arrMin[0])

  let serviceMaxCapacity = arrMax.reduce((prev, curr) => {
    return curr > prev ? curr : prev
  }, arrMax[0])

  if (serviceMinCapacity !== serviceMaxCapacity) {
    return `${serviceMinCapacity}/${serviceMaxCapacity}`
  }

  return serviceMinCapacity;
}

function useServiceDuration(seconds) {
  let hours = Math.floor(seconds / 3600)
  let minutes = seconds / 60 % 60

  return (hours ? (hours + globalLabels.h + ' ') : '') + ' ' + (minutes ? (minutes + globalLabels.min) : '')
}

function useServicePrice(entities, serviceId) {
  let arrPrice = []

  entities.employees.forEach(employee => {
    if (employee.id in entities.entitiesRelations && serviceId in entities.entitiesRelations[employee.id]) {
      let employeeService = employee.serviceList.find(service => service.id === serviceId)
      arrPrice.push(employeeService.price)
    }
  })

  let serviceMinPrice = arrPrice.reduce((prev, curr) => {
    return curr < prev ? curr : prev
  }, arrPrice[0])

  let serviceMaxPrice = arrPrice.reduce((prev, curr) => {
    return curr > prev ? curr : prev
  }, arrPrice[0])

  if (serviceMinPrice !== serviceMaxPrice) {
    return {
      price: `${useFormattedPrice(serviceMinPrice, !globalSettings.payments.hideCurrencySymbolFrontend)} - ${useFormattedPrice(serviceMaxPrice, !globalSettings.payments.hideCurrencySymbolFrontend)}`,
      min: serviceMinPrice,
      max: serviceMaxPrice
    }
  }

  return {
    price: useFormattedPrice(serviceMinPrice, globalSettings.payments.hideCurrencySymbolFrontend),
    min: serviceMinPrice,
    max: serviceMaxPrice
  }
}

function useServiceLocation(entities, serviceId) {
  let arr = []

  entities.employees.forEach(employee => {
    if (
      employee.id in entities.entitiesRelations
      && serviceId in entities.entitiesRelations[employee.id]
      && entities.entitiesRelations[employee.id][serviceId].length
    ) {
      entities.locations.forEach(a => {
        if (
          entities.entitiesRelations[employee.id][serviceId].some(b => b === a.id)
          && !arr.find(b => b === a.id)
        ) {
          arr.push(a)
        }
      })
    }
  })

  return arr
}

function useDisabledPackageService (entities, pack) {
  let detector = []
  let employeesIds = Object.keys(entities.entitiesRelations)

  pack.bookable.forEach(item => {
    let serviceEmployees = []
    employeesIds.forEach((employeeId) => {
      if (
        entities.entitiesRelations[employeeId]
        && entities.entitiesRelations[employeeId][item.service.id]
        && !serviceEmployees.find(a => a ? a.id === parseInt(employeeId) : true)
      ) {
        serviceEmployees.push(entities.employees.find(a => a.id === parseInt(employeeId)))
      }
    })

    if (!serviceEmployees.length) {
      detector.push(false)
    }
  })

  return detector.filter(a => a === false).length
}

function usePackageAvailabilityByEmployeeAndLocation (entities, pack, shortcodeData) {
  let displayPack = []
  let employeesIds = Object.keys(entities.entitiesRelations)
  let preselectedEmployees = shortcodeData && shortcodeData.employee ? shortcodeData.employee.split(',').map(s => parseInt(s)) : null
  let unfilteredEmployees = ref(preselectedEmployees ? entities.unfilteredEmployees.filter(a => preselectedEmployees.map(id => parseInt(id)).includes(a.id)) : entities.unfilteredEmployees)

  pack.bookable.forEach(item => {
    let arr = []

    if (item.providers.length) {
      item.providers.forEach(p => {
        if (item.locations.length) {
          item.locations.forEach(l => {
            if (
              unfilteredEmployees.value.find(a => a.id === p.id)
              && entities.entitiesRelations[p.id]
              && entities.entitiesRelations[p.id][item.service.id]
              && entities.entitiesRelations[p.id][item.service.id].indexOf(l.id) !== -1
              && !arr.find(a => a.id === p.id)
            ) {
              arr.push(unfilteredEmployees.value.find(a => a.id === p.id))
            }
          })
        } else {
          if (
            unfilteredEmployees.value.find(a => a.id === p.id)
            && !arr.find(a => a.id === p.id)
          ) {
            arr.push(unfilteredEmployees.value.find(a => a.id === p.id))
          }
        }
      })
    } else {
      employeesIds.forEach((employeeId) => {
        if (item.locations.length) {
          item.locations.forEach(l => {
            if (
              entities.entitiesRelations[employeeId]
              && entities.entitiesRelations[employeeId][item.service.id]
              && entities.entitiesRelations[employeeId][item.service.id].indexOf(l.id) !== -1
              && unfilteredEmployees.value.find(a => a.id === parseInt(employeeId))
              && !arr.find(a => a.id === parseInt(employeeId))
            ) {
              arr.push(unfilteredEmployees.value.find(a => a.id === parseInt(employeeId)))
            }
          })
        } else {
          if (
            entities.entitiesRelations[employeeId]
            && entities.entitiesRelations[employeeId][item.service.id]
            && unfilteredEmployees.value.find(a => a.id === parseInt(employeeId))
            && !arr.find(a => a.id === parseInt(employeeId))
          ) {
            arr.push(unfilteredEmployees.value.find(a => a.id === parseInt(employeeId)))
          }
        }
      })
    }

    displayPack.push(!!arr.length)
  })

  return !displayPack.filter(a => a === false).length
}

function usePackageEmployees (entities, pack, shortcodeData) {
  let arr = []
  let employeesIds = Object.keys(entities.entitiesRelations)
  let preselectedEmployees = shortcodeData && shortcodeData.employee ? shortcodeData.employee.split(',').map(s => parseInt(s)) : null
  let unfilteredEmployees = ref(preselectedEmployees ? entities.unfilteredEmployees.filter(a => preselectedEmployees.map(id => parseInt(id)).includes(a.id)) : entities.unfilteredEmployees)

  pack.bookable.forEach(item => {
    if (item.providers.length) {
      item.providers.forEach(p => {
        if (item.locations.length) {
          item.locations.forEach(l => {
            if (
              unfilteredEmployees.value.find(a => a.id === p.id)
              && entities.entitiesRelations[p.id]
              && entities.entitiesRelations[p.id][item.service.id]
              && entities.entitiesRelations[p.id][item.service.id].indexOf(l.id) !== -1
              && !arr.find(a => a.id === p.id)
            ) {
              arr.push(unfilteredEmployees.value.find(a => a.id === p.id))
            }
          })
        } else {
          if (
            unfilteredEmployees.value.find(a => a.id === p.id)
            && !arr.find(a => a.id === p.id)
          ) {
            arr.push(unfilteredEmployees.value.find(a => a.id === p.id))
          }
        }
      })
    } else {
      employeesIds.forEach((employeeId) => {
        if (item.locations.length){
          item.locations.forEach(l => {
            if (
              entities.entitiesRelations[employeeId]
              && entities.entitiesRelations[employeeId][item.service.id]
              && entities.entitiesRelations[employeeId][item.service.id].indexOf(l.id) !== -1
              && unfilteredEmployees.value.find(a => a.id === parseInt(employeeId))
              && !arr.find(a => a.id === parseInt(employeeId))
            ) {
              arr.push(unfilteredEmployees.value.find(a => a.id === parseInt(employeeId)))
            }
          })
        } else {
          if (
            entities.entitiesRelations[employeeId]
            && entities.entitiesRelations[employeeId][item.service.id]
            && unfilteredEmployees.value.find(a => a.id === parseInt(employeeId))
            && !arr.find(a => a.id === parseInt(employeeId))
          ) {
            arr.push(unfilteredEmployees.value.find(a => a.id === parseInt(employeeId)))
          }
        }
      })
    }
  })

  return arr
}

function usePackageLocations (entities, pack, shortcodeData) {
  let arr = []

  let employeesIds = Object.keys(entities.entitiesRelations)
  let preselectedLocations = shortcodeData && shortcodeData.location ? shortcodeData.location.split(',').map(s => parseInt(s)) : null
  let unfilteredLocations =  ref(preselectedLocations ? entities.unfilteredLocations.filter(a => preselectedLocations.map(id => parseInt(id)).includes(a.id)) : entities.unfilteredLocations)

  pack.bookable.forEach(b => {
    if (b.locations.length) {
      b.locations.forEach(l => {
        if (
          unfilteredLocations.value.find(a => a.id === l.id)
          && !arr.find(a => a.id === l.id)
        ) {
          arr.push(unfilteredLocations.value.find(a => a.id === l.id))
        }
      })
    } else {
      employeesIds.forEach(e => {
        unfilteredLocations.value.forEach(l => {
          if (
            e in entities.entitiesRelations
            && b.service.id in entities.entitiesRelations[e]
            && entities.entitiesRelations[e][b.service.id].indexOf(l.id) !== -1
            && unfilteredLocations.value.find(a => a.id === l.id)
            && !arr.find(a => a.id === parseInt(l.id))
          ) {
            arr.push(unfilteredLocations.value.find(a => a.id === l.id))
          }
        })
      })
    }
  })

  return arr
}

function useAvailableCategories (entities, shortcodeData, employeeId = null, locationId = null) {
  let arr = []
  entities.categories.forEach(category => {
    let serviceIdsInCategory = useAvailableServiceIdsInCategory(shortcodeData, category, entities, employeeId, locationId)
    /* Service ids in category */
    category.serviceIdList = serviceIdsInCategory

    /* Packages in category */
    category.packageList = []
    entities.packages.forEach(pack => {
      serviceIdsInCategory.forEach(service => {
        if (
          pack.bookable.filter(a => a.service.id === service).length
          && !category.packageList.filter(b => b === pack.id).length
          && pack.available
          && pack.status === 'visible'
          && !useDisabledPackageService(entities, pack)
          && usePackageAvailabilityByEmployeeAndLocation(entities, pack, shortcodeData)
        ) {
          category.packageList.push(pack.id)
        }
      })
    })

    if (
      category.status === 'visible'
      && category.serviceList.length
      && !!serviceIdsInCategory.length
      && (shortcodeData.show === 'packages' ? !!category.packageList.length : true)
    ) {
      arr.push(category)
    }
  })

  return arr
}

export {
  useAvailableServiceIdsInCategory,
  useEmployeesServiceCapacity,
  useServiceDuration,
  useServicePrice,
  useServiceLocation,
  useDisabledPackageService,
  usePackageAvailabilityByEmployeeAndLocation,
  usePackageEmployees,
  usePackageLocations,
  useAvailableCategories
}
