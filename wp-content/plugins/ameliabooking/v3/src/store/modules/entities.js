import httpClient from "../../plugins/axios.js";
import {
  settings,
  shortLocale,
  longLocale
} from "../../plugins/settings.js";
import {useUrlParams, useUrlQueryParams} from "../../assets/js/common/helper";
import { getBadgeTranslated, useTranslateEntities } from "../../assets/js/public/translation";
import { useFeaturesAndIntegrations, useParsedCustomPricing } from "../../assets/js/common/employee";

function isEmployeeServiceLocation (relations, employeeId, serviceId, locationId = null) {
  if (locationId) {
    return employeeId in relations && serviceId in relations[employeeId] && relations[employeeId][serviceId].indexOf(locationId) !== -1
  }

  return employeeId in relations && serviceId in relations[employeeId]
}

function setLiteService () {
  return {
    extras: [],
    maxCapacity: 1,
    minCapacity: 1,
    timeAfter: '',
    timeBefore: '',
    bringingAnyone: false,
    aggregatedPrice: true,
    settings: null,
    recurringCycle: 'disabled',
    recurringSub: 'future',
    recurringPayment: 0,
    deposit: 0,
    depositPayment: 'disabled',
    depositPerPerson: 1,
    fullPayment: false,
    translations: null,
    minSelectedExtras: null,
    mandatoryExtra: false
  }
}

function setStarterService () {
  return {
    timeAfter: '',
    timeBefore: '',
    deposit: 0,
    depositPayment: 'disabled',
    depositPerPerson: 1,
    fullPayment: false,
    translations: null
  }
}

function useLiteEntities (entities) {
  entities.categories.forEach((category, categoryIndex) => {
    category.serviceList.forEach((service, serviceIndex) => {
      entities.categories[categoryIndex].serviceList[serviceIndex] = Object.assign(
        service,
        setLiteService()
      )
    })
  })

  entities.employees.forEach((employee, employeeIndex) => {
    employee.serviceList.forEach((service, serviceIndex) => {
      entities.employees[employeeIndex].serviceList[serviceIndex] = Object.assign(
        service,
        setLiteService()
      )
    })
  })

  entities.packages = []
  entities.locations = []
  entities.customFields = []

  return entities
}

function useStarterEntities (entities) {
  entities.categories.forEach((category, categoryIndex) => {
    category.serviceList.forEach((service, serviceIndex) => {
      entities.categories[categoryIndex].serviceList[serviceIndex] = Object.assign(
        service,
        setStarterService()
      )
    })
  })

  entities.employees.forEach((employee, employeeIndex) => {
    employee.serviceList.forEach((service, serviceIndex) => {
      entities.employees[employeeIndex].serviceList[serviceIndex] = Object.assign(
        service,
        setStarterService()
      )
    })
  })

  entities.packages = []
  entities.locations = []
  entities.customFields = []

  return entities
}

function setEntities ({ commit, rootState }, entities, types, licence, showHidden, isPanel) {
  commit('setShowHidden', showHidden)

  let availableTranslationsShort = settings.general.usedLanguages.map(
    key => key.length > 2 ? key.slice(0, 2) : key
  )

  if (licence.isLite) {
    entities = useLiteEntities(entities)
  }

  if (licence.isStarter) {
    entities = useStarterEntities(entities)
  }

  if (settings.general.usedLanguages.indexOf(longLocale) !== -1 ||
    availableTranslationsShort.indexOf(shortLocale) !== -1
  ) {
    useTranslateEntities(entities)

    rootState.settings.roles.providerBadges.badges?.forEach(badge => {
      badge.content = getBadgeTranslated(badge)
    })
  }

  if (!isPanel && !settings.featuresIntegrations.customFields.enabled) {
    entities['customFields'] = []
  }

  if (!isPanel && !settings.featuresIntegrations.packages.enabled) {
    entities['packages'] = []
  }

  types.forEach(ent => {
    if (!(ent in entities)) {
      entities[ent] = []
    }

    if (ent === 'categories') {
      if (settings.activation.stash) {
        entities[ent].sort((a, b) => a.position - b.position)
      }

      entities[ent].forEach((category, categoryIndex) => {
        if (settings.activation.stash) {
          category.serviceList.sort((a, b) => a.position - b.position)
        }
        category.serviceList.forEach((service, serviceIndex) => {
          useFeaturesAndIntegrations(service, isPanel)

          useFeaturesAndIntegrations(entities[ent][categoryIndex].serviceList[serviceIndex], isPanel)

          entities[ent][categoryIndex].serviceList[serviceIndex].customPricing = useParsedCustomPricing(service)
        })
      })
    }

    if (ent === 'employees') {
      let arr = []
      entities[ent].forEach((employee, employeeIndex) => {
        employee.serviceList.forEach((employeeService, serviceIndex) => {
          let service = entities.categories.find(
            c => c.serviceList.filter(s => parseInt(s.id) === parseInt(employeeService.id)).length
          ).serviceList.find(s => parseInt(s.id) === parseInt(employeeService.id))

          let employeePrice = entities[ent][employeeIndex].serviceList[serviceIndex].price
          let employeeMinCapacity = entities[ent][employeeIndex].serviceList[serviceIndex].minCapacity
          let employeeMaxCapacity = entities[ent][employeeIndex].serviceList[serviceIndex].maxCapacity

          entities[ent][employeeIndex].serviceList[serviceIndex] = JSON.parse(JSON.stringify(service))

          entities[ent][employeeIndex].serviceList[serviceIndex].price = employeePrice
          entities[ent][employeeIndex].serviceList[serviceIndex].minCapacity = employeeMinCapacity
          entities[ent][employeeIndex].serviceList[serviceIndex].maxCapacity = employeeMaxCapacity

          useFeaturesAndIntegrations(entities[ent][employeeIndex].serviceList[serviceIndex], isPanel)

          useFeaturesAndIntegrations(employeeService, isPanel)

          useFeaturesAndIntegrations(service, isPanel)

          entities[ent][employeeIndex].serviceList[serviceIndex].customPricing = useParsedCustomPricing(
            employeeService.customPricing ? employeeService : service
          )
        })

        if (employee.badgeId) {
          employee.badge = rootState.settings.roles.providerBadges.badges?.find(badge => badge.id === employee.badgeId)
        } else {
          employee.badge = null
        }

        if (showHidden || (employee.status !== 'hidden' && employee.show)) {
          arr.push(employee)
        }
      })
      commit('setUnfilteredEmployees', arr)
    }

    if (ent === 'locations') {
      let arr = []
      entities[ent].forEach((location) => {
        if (showHidden || location.status !== 'hidden') {
          arr.push(location)
        }
      })
      commit('setUnfilteredLocations', arr)
    }

    commit(
      'set' + ent.charAt(0).toUpperCase() + ent.slice(1),
      ent === 'customFields' ? entities['customFields'].sort(function(a, b) {
        if (a['saveType'] === b['saveType']) {
          return a['position'] - b['position']
        }
        return a['saveType'].localeCompare(b['saveType'])
      }) : entities[ent]
    )
  })

  commit('setPreselectedFromUrl')
  commit('setPreselectedValues')
  commit('setReady', true)
}

function getEntitiesVariableName () {
  return 'ameliaAppointmentEntities' in window
    ? 'ameliaAppointmentEntities'
    : ('ameliaEntities' in window ? 'ameliaEntities' : false)
}

export default {
  namespaced: true,

  state: () => ({
    settings: [],
    taxes: [],
    categories: [],
    services: [],
    employees: [],
    unfilteredEmployees: [],
    locations: [],
    unfilteredLocations: [],
    packages: [],
    entitiesRelations: {},
    customFields: [],
    tags: [],
    spaces: [],
    ready: false,
    showHidden: false,
    originalPreselected: {},
    preselected: {}
  }),

  getters: {
    getSettings (state) {
      return state.settings
    },

    getSpaces (state) {
      return state.spaces
    },

    getEntitiesRelations (state) {
      return state.entitiesRelations
    },

    getOriginalPreselected (state) {
      return JSON.parse(JSON.stringify(state.originalPreselected))
    },

    getPreselected (state) {
      return state.preselected
    },

    getTaxes (state) {
      return state.taxes
    },

    getTax: (state) => (id) => {
      return state.taxes.find(i => parseInt(i.id) === parseInt(id)) || null
    },

    getCategories (state) {
      return state.categories
    },

    getCategory: (state) => (id) => {
      return state.categories.find(i => parseInt(i.id) === parseInt(id)) || null
    },

    getPackages (state) {
      return state.packages
    },

    getPackage: (state) => (id) => {
      return state.packages.find(i => parseInt(i.id) === parseInt(id)) || null
    },

    getServices (state) {
      return state.services
    },

    getService: (state) => (id) => {
      return state.services.find(i => parseInt(i.id) === parseInt(id)) || null
    },

    getUnfilteredEmployees (state) {
      return state.unfilteredEmployees
    },

    getUnfilteredEmployee: (state) => (id) => {
      return state.unfilteredEmployees.find(a => parseInt(a.id) === parseInt(id)) || null
    },

    getEmployees (state) {
      return state.employees
    },

    getEmployee: (state) => (id) => {
      return state.employees.find(i => parseInt(i.id) === parseInt(id)) || null
    },

    getEmployeeService: (state) => (providerId, serviceId) => {
      return state.employees.find(
        i => parseInt(i.id) === parseInt(providerId)
      ).serviceList.find(
        i => parseInt(i.id) === parseInt(serviceId)
      )
    },

    getUnfilteredLocations (state) {
      return state.unfilteredLocations
    },

    getUnfilteredLocation: (state) => (id) => {
      return state.unfilteredLocations.find(i => parseInt(i.id) === parseInt(id)) || null
    },

    getLocations (state) {
      return state.locations
    },

    getLocation: (state) => (id) => {
      return state.locations.find(i => parseInt(i.id) === parseInt(id)) || null
    },

    getTags (state) {
      return state.tags
    },

    getCustomFields (state) {
      return state.customFields
    },

    getCustomField: (state) => (id) => {
      return state.customFields.find(i => parseInt(i.id) === parseInt(id)) || null
    },

    filteredCategories: (state, getters) => (data) => {
      let categories = []

      let categoriesIds = getters.filteredServices(data).filter(service => !data.serviceId || service.id === data.serviceId).map(service => service.categoryId)

      state.categories.forEach((category) => {
        if (categoriesIds.indexOf(category.id) !== -1) {
          let availableCategory = Object.assign(
            {},
            category
          )
          availableCategory.serviceList = getters.filteredServices(data).filter(service => service.categoryId === category.id)
          categories.push(availableCategory)
        }
      })

      return categories
    },

    filteredServices: (state, getters) => (data) => {
      return state.services.filter(service =>
        (!data.categoryId ? true : service.categoryId === data.categoryId) &&
        (!data.providerId ? true : isEmployeeServiceLocation(state.entitiesRelations, data.providerId, service.id)) &&
        (!data.locationId ? true :
          getters.filteredEmployees(data).filter(
            employee => isEmployeeServiceLocation(state.entitiesRelations, employee.id, service.id, data.locationId)
          ).length > 0
        )
      )
    },

    filteredPackagesPreselected: (state, getters) => () => {
      let preselected = getters.getOriginalPreselected
      return state.packages.filter(pack =>
          (preselected.service.length === 0 ? true :
              pack.bookable.filter(
                  b => preselected.service.map(s => parseInt(s)).includes(b.service.id)
              ).length > 0)
          && (preselected.employee.length === 0 ? true :
              pack.bookable.filter(
                  b => b.providers.length ? b.providers.find(p => preselected.employee.includes(p.id)) :
                      preselected.employee.map(e => parseInt(e)).filter(provider => isEmployeeServiceLocation(state.entitiesRelations, provider, b.service.id)).length > 0
              ).length === pack.bookable.length)
          && (preselected.category.length === 0 ? true :
              pack.bookable.filter(
                  b => preselected.category.map(c => parseInt(c)).includes(b.service.categoryId)
              ).length === pack.bookable.length)
          && (preselected.location.length === 0 ? true :
              pack.bookable.filter(
                  b => (b.locations.length ? b.locations.find(l => preselected.location.map(l => parseInt(l)).includes(l.id)) : (
                      (b.providers.length > 0 ? b.providers : state.employees).filter(
                          employee =>
                              preselected.location.map(l => parseInt(l)).filter(l => isEmployeeServiceLocation(state.entitiesRelations, employee.id, b.service.id, l)).length > 0
                      ).length > 0
                  ))
              ).length === pack.bookable.length)
      )
    },

    filteredPackages: (state, getters) => (data) => {
      let packagesFiltered = getters.filteredPackagesPreselected()

      return packagesFiltered.filter(pack =>
        (state.showHidden || pack.status === 'visible')
        && pack.bookable.length
        && pack.available
        && (!data.serviceId ? true :
          pack.bookable.filter(
            b => b.service.id === data.serviceId
          ).length > 0)
        && (!data.providerId ? true :
          pack.bookable.filter(
            b => b.providers.length ? b.providers.find(p => data.providerId === p.id) : isEmployeeServiceLocation(state.entitiesRelations, data.providerId, b.service.id)
          ).length === pack.bookable.length)
        && (!data.categoryId ? true :
            (
              state.originalPreselected.category.length
                ? pack.bookable.filter(
                    b => b.service.categoryId === data.categoryId
                  ).length === pack.bookable.length
                : pack.bookable.filter(
                    b => b.service.categoryId === data.categoryId
                  ).length > 0
            )
          )
        && (!data.locationId ? true :
          pack.bookable.filter(
            b => (b.locations.length ? b.locations.find(l => data.locationId === l.id) : (
                (b.providers.length > 0 ? b.providers : getters.filteredEmployees(data)).filter(
                  employee => isEmployeeServiceLocation(state.entitiesRelations, employee.id, b.service.id, data.locationId)
                ).length > 0
            ))
          ).length === pack.bookable.length)
      )
    },

    filteredLocations: (state, getters) => (data) => {
      return state.locations.filter(location =>
        (!data.providerId ? true :
          state.employees.length ? state.employees.find(i => i.id === data.providerId).serviceList.filter(
            employeeService => {
              return (state.showHidden || employeeService.status === 'visible') &&
                isEmployeeServiceLocation(state.entitiesRelations, data.providerId, employeeService.id, location.id)
            }).length > 0 : false
        ) &&
        (!data.serviceId || data.packageId ? true :
          getters.filteredEmployees(data).find(
            employee => isEmployeeServiceLocation(state.entitiesRelations, employee.id, data.serviceId, location.id)
          ) !== undefined
        ) &&
        (!data.packageId ? true :
          state.packages.find(i => i.id === data.packageId).bookable.filter((book) => {
            return book.locations.length ? book.locations.find(l => l.id === location.id) :
              (getters.filteredEmployees(data).filter(
                  employee => isEmployeeServiceLocation(state.entitiesRelations, employee.id, book.service.id, location.id)
              ).length > 0)
          }).length > 0
        )
      )
    },

    filteredEmployees: (state) => (data) => {
      return state.employees.filter(employee =>
        employee.serviceList.find(
          service =>
            (state.showHidden || service.status === 'visible') &&
            // service.maxCapacity >= data.persons &&
            (!data.serviceId ? true : isEmployeeServiceLocation(state.entitiesRelations, employee.id, service.id) && service.id === data.serviceId) &&
            (!data.locationId ? true : isEmployeeServiceLocation(state.entitiesRelations, employee.id, service.id, data.locationId))
        ) !== undefined
      )
    },

    getServiceEmployees: (state) => (serviceId) => {
      return state.employees.filter((item) => {
        return item.serviceList.some((service) => {
          return parseInt(service.id) === parseInt(serviceId)
        })
      })
    },

    getEmployeeServices: (state, getters) => (data) => {
      let employeeServices = []

      if (data.serviceId) {
        let service = getters.getService(data.serviceId)
        if (data.providerId) {
          let possibleEmployee = state.employees.find(i => i.id === data.providerId)
          if (!possibleEmployee) return {}
          let employeeService = possibleEmployee.serviceList.filter(service => service.id === data.serviceId)
          return employeeService.map(eS => Object.assign(eS, {bringingAnyone: service.bringingAnyone, aggregatedPrice: service.aggregatedPrice, maxExtraPeople: service.maxExtraPeople}))
        }

        getters.filteredEmployees(data).forEach((employee) => {
          employee.serviceList.forEach((employeeService) => {
            if (employeeService.id === data.serviceId) {
              employeeServices.push(Object.assign(employeeService, {bringingAnyone: service.bringingAnyone, aggregatedPrice: service.aggregatedPrice, maxExtraPeople: service.maxExtraPeople}))
            }
          })
        })
      }

      return employeeServices
    },

    getBookableFromBookableEntities: (state) => (data) => {
      switch (data.type) {
        case ('appointment'):
          return state.services.find(i => i.id === data.serviceId)
        case ('package'):
          return state.packages.find(i => i.id === data.packageId)
      }
    },

    getReady (state) {
      return state.ready
    },

    getShowHidden (state) {
      return state.showHidden
    },

    getPackageEntities: (state, getters) => (packageId) => {
      let entities = {services: [], providers: [], locations: [], packages: []}
      let pack = getters.getPackage(packageId)
      if (pack) {
        pack.bookable.forEach(bookable => {
          entities.services.push(bookable.service.id)
          let employees = []
          if (bookable.providers.length > 0) {
            employees = bookable.providers.map(p => p.id)
          } else {
            employees = state.employees.filter(e => e.serviceList.find(s => s.id === bookable.service.id)).map(e => e.id)
          }
          entities.providers = entities.providers.concat(employees)
          let locations = []
          if (bookable.locations.length > 0) {
            locations = bookable.locations.map(p => p.id)
          } else {
            state.locations.forEach(location =>
                employees.forEach(e => {
                  if (isEmployeeServiceLocation(state.entitiesRelations, e, bookable.service.id, location.id)) {
                    locations.push(location.id)
                  }
                })
            )
          }
          entities.locations = entities.locations.concat(locations)
        })

        entities.packages.push(pack.id)
      }
      return entities
    }
  },

  mutations: {
    setSettings (state, payload) {
      state.settings = payload
    },

    setSpaces (state, payload) {
      state.spaces = payload
    },

    setTaxes (state, payload) {
      state.taxes = payload
    },

    setCategories (state, payload) {
      state.categories = payload

      let services = []

      state.categories.forEach((category) => {
        category.serviceList.forEach((service) => {
          services.push(service)
        })
      })

      state.services = services
    },

    setUnfilteredEmployees (state, payload) {
      state.unfilteredEmployees = payload
    },

    setUnfilteredLocations (state, payload) {
      state.unfilteredLocations = payload
    },

    setEmployees (state, payload) {
      state.employees = payload
    },

    setLocations (state, payload) {
      state.locations = payload
    },

    setTags (state, payload) {
      state.tags = payload
    },

    setPackages (state, payload) {
      payload.forEach(pack => {
        let isAvailable = true

        pack.bookable.forEach(book => {
          if (!book.service.name) {
            let service = state.services.find(s => s.id === book.service.id)
            if (service) {
              book.service = service
            }
          }

          if (state.showHidden ? false : book.service.status !== 'visible') {
            isAvailable = false
          }
        })

        if (isAvailable) {
          state.packages.push(pack)
        }
      })

      state.packages.sort((a, b) => a.position - b.position)
    },

    setCustomFields (state, payload) {
      state.customFields = [...Object.values(payload)]
    },

    setEntitiesRelations (state, payload) {
      state.entitiesRelations = payload
    },

    setReady (state, payload) {
      state.ready = payload
    },

    setShowHidden (state, payload) {
      state.showHidden = payload
    },

    setPreselected (state, payload) {
      state.preselected = payload
      state.preselected = Object.assign({}, state.preselected,
          {category: Array.isArray(state.preselected.category) ? state.preselected.category : (state.preselected.category ? state.preselected.category.toString().split(',').map(c=>parseInt(c)) : []),
        service: Array.isArray(state.preselected.service) ? state.preselected.service : (state.preselected.service ? state.preselected.service.toString().split(',').map(c=>parseInt(c)) : []),
        employee: Array.isArray(state.preselected.employee) ? state.preselected.employee : (state.preselected.employee ? state.preselected.employee.toString().split(',').map(c=>parseInt(c)) : []),
        location: Array.isArray(state.preselected.location) ? state.preselected.location : (state.preselected.location ? state.preselected.location.toString().split(',').map(c=>parseInt(c)) : []),
        package: Array.isArray(state.preselected.package) ? state.preselected.package : (state.preselected.package ? state.preselected.package.toString().split(',').map(c=>parseInt(c)) : [])
      })
    },

    setPreselectedFromUrl (state) {
      let urlParameters = useUrlQueryParams(window.location.href)
      if (urlParameters) {
        if (urlParameters.ameliaServiceId) {
          state.preselected.service = urlParameters.ameliaServiceId.split(',').map(a => parseInt(a))
        }
        if (urlParameters.ameliaEmployeeId) {
          state.preselected.employee = urlParameters.ameliaEmployeeId.split(',').map(a => parseInt(a))
        }
        if (urlParameters.ameliaLocationId) {
          state.preselected.location = urlParameters.ameliaLocationId.split(',').map(a => parseInt(a))
        }
        if (urlParameters.ameliaCategoryId) {
          state.preselected.category = urlParameters.ameliaCategoryId.split(',').map(a => parseInt(a))
        }
      }
    },

    setPreselectedValues (state) {
      state.originalPreselected = JSON.parse(JSON.stringify(state.preselected))

      state.employees = state.employees.filter(e => (state.showHidden || (e.status === 'visible' && e.show)))
      state.services = state.services.filter(s => (state.showHidden ? true : (s.status === 'visible' && s.show)) && state.employees.filter(e => e.serviceList.find(eS => eS.id === s.id)).length)
      state.locations = state.locations.filter(l => state.showHidden || l.status === 'visible')

      if (!state.showHidden) {
        state.employees.forEach(e => {
          if (!e.show) {
            delete state.entitiesRelations[e.id]
          }
        })
      }

      if ('category' in state.preselected && state.preselected.category.length > 0) {
        state.categories = state.categories.filter(c => state.preselected.category.map(id => parseInt(id)).includes(c.id))
        state.services = state.services.filter(s => state.preselected.category.map(id => parseInt(id)).includes(s.categoryId))
        state.employees = state.employees.filter(e => e.serviceList.filter(s => state.preselected.category.map(id => parseInt(id)).includes(s.categoryId)).length > 0)
        state.locations = state.locations.filter(l =>
            state.employees.filter(e =>
                state.services.filter(s => isEmployeeServiceLocation(state.entitiesRelations, e.id, s.id, l.id) && state.preselected.category.map(id => parseInt(id)).includes(s.categoryId)).length > 0
            ).length > 0
        )
      }

      if ('service' in state.preselected && state.preselected.service.length > 0) {
        state.services = state.services.filter(s => state.preselected.service.map(id => parseInt(id)).includes(s.id))
        state.categories = state.categories.filter(c => state.services.map(serv => serv.categoryId).includes(c.id))
        state.employees = state.employees.filter(e => e.serviceList.filter(s => state.preselected.service.map(id => parseInt(id)).includes(s.id)).length > 0)
        state.locations = state.locations.filter(l =>
            state.employees.filter(e => state.preselected.service
                .filter(serviceId =>
                  isEmployeeServiceLocation(state.entitiesRelations, e.id, parseInt(serviceId), l.id)
                ).length > 0
            ).length > 0
        )
      }

      if ('employee' in state.preselected && state.preselected.employee.length > 0) {
        state.employees = state.employees.filter(e => state.preselected.employee.map(id => parseInt(id)).includes(e.id))
        if (state.employees.length > 0) {
          state.services = state.services.filter(s => state.employees.filter(e => e.serviceList.filter(serv => serv.id === s.id).length > 0).length > 0)

          state.categories = state.categories.filter(c => state.services.filter(s => s.categoryId === c.id).length > 0)
          state.locations = state.locations.filter(l =>
            state.services.filter(s => state.preselected.employee
                .filter(employeeId =>
                    isEmployeeServiceLocation(state.entitiesRelations, parseInt(employeeId), s.id, l.id)
                ).length > 0
            ).length > 0
          )
        }
      }

      if ('location' in state.preselected && state.preselected.location.length > 0) {
        state.locations = state.locations.filter(e => state.preselected.location.map(id => parseInt(id)).includes(e.id))
        state.employees = state.employees.filter(e => e.serviceList.filter(
              s => state.preselected.location
                  .filter(locationId =>
                      isEmployeeServiceLocation(state.entitiesRelations, e.id, s.id, parseInt(locationId))
                  ).length > 0
            ).length > 0)
        state.services = state.services.filter(s => state.employees.filter(e => e.serviceList.filter(serv => serv.id === s.id).length > 0).length > 0)
        state.categories = state.categories.filter(c => state.services.filter(s => s.categoryId === c.id).length > 0)
      }

      if ('package' in state.preselected && state.preselected.package.length > 0) {
        state.packages = state.packages.filter(p => state.preselected.package.map(id => parseInt(id)).includes(p.id))
        state.preselected.show = 'packages'
        state.services = state.services.filter(s => state.packages.filter(p => p.bookable.filter(b => b.service.id === s.id).length > 0).length > 0)
        state.categories = state.categories.filter(c => state.services.filter(s => s.categoryId === c.id).length > 0)
      }

      if (state.services.length === 1 && state.preselected.show !== 'packages') {
        state.preselected = Object.assign({}, state.preselected, {service: [(state.services[0].id).toString()]})
      }
      if (state.categories.length === 1 && state.preselected.show !== 'packages') {
        state.preselected = Object.assign({}, state.preselected, {category: [(state.categories[0].id).toString()]})
      }
      if (state.employees.length === 1 && state.preselected.show !== 'packages') {
        state.preselected = Object.assign({}, state.preselected, {employee: [(state.employees[0].id).toString()]})
      }
      if (state.locations.length === 1 && state.preselected.show !== 'packages') {
        state.preselected = Object.assign({}, state.preselected, {location: [(state.locations[0].id).toString()]})
      }


      // if all employees have the same price
      state.services.forEach(s => {
        let services = state.employees.map(e => e.serviceList.filter(service => service.id === s.id)).flat()
        let samePrice = services.every(service => service.price === services[0].price)
        if (services.length && samePrice) {
          s.price = services[0].price
        }
      })
    },
  },

  actions: {
    getEntities ({ commit, rootState }, payload) {
      let types = payload.types

      if (payload.loadEntities && (payload.isPanel || !getEntitiesVariableName())) {
        httpClient.get('/entities', { params: useUrlParams({types: types, page: 'booking', lite: true}) }).then(response => {
          window.ameliaAppointmentEntities = response.data.data

          let entities = JSON.parse(JSON.stringify(window.ameliaAppointmentEntities))

          setEntities({ commit, rootState }, entities, types, payload.licence, payload.showHidden, payload.isPanel)
        })
      } else {
        let ameliaApiInterval = setInterval(
          () => {
            let name = getEntitiesVariableName()

            if (name) {
              clearInterval(ameliaApiInterval)

              let entities = JSON.parse(JSON.stringify(window[name]))

              setEntities({ commit, rootState }, entities, types, payload.licence, payload.showHidden, payload.isPanel)
            }
          },
          1000
        )
      }
    }
  },
}
