export default {
  namespaced: true,

  state: () => ({
    dates: [],
    customers: [],
    services: [],
    events: [],
    packages: [],
    providers: [],
    locations: [],
    options: {
      customers: [],
      services: [],
      events: [],
      packages: [],
      providers: [],
      locations: []
    }
  }),

  getters: {
    getDates (state) {
      return state.dates
    },

    getCustomers (state) {
      return state.customers
    },

    getServices (state) {
      return state.services
    },

    getProviders (state) {
      return state.providers
    },

    getLocations (state) {
      return state.locations
    },

    getEvents (state) {
      return state.events
    },

    getPackages (state) {
      return state.packages
    },

    getAppointmentsFilters (state) {
      return {
        dates: state.dates,
        customers: state.customers,
        services: state.services,
        providers: state.providers,
        locations: state.locations
      }
    },

    getEventsFilters (state) {
      return {
        dates: state.dates,
        customers: state.customers,
        events: state.events,
        providers: state.providers,
        locations: state.locations
      }
    },

    getPackagesFilters (state) {
      return {
        packages: state.packages,
        services: state.services,
        providers: state.providers,
        locations: state.locations
      }
    },

    getAppointmentFilterOptions: (state) => (type) => {
      if (type === 'employee') {
        return {
          customers: state.options.customers,
          services: state.options.services,
          locations: state.options.locations
        }
      }
      return {
        services: state.options.services,
        providers: state.options.providers,
        locations: state.options.locations
      }
    },

    getEventFiltersOption: (state) => (type) => {
      if (type === 'employee') {
        return {
          events: state.options.events,
          customers: state.options.customers,
          locations: state.options.locations
        }
      }

      return {
        events: state.options.events,
        providers: state.options.providers,
        locations: state.options.locations
      }
    },

    getPackageFilterOptions (state) {
      return {
        packages: state.options.packages,
        services: state.options.services,
        providers: state.options.providers,
        locations: state.options.locations
      }
    }
  },

  mutations: {
    setDates (state, payload) {
      state.dates = payload
    },

    setCustomers (state, payload) {
      state.customers = payload
    },

    setServices (state, payload) {
      state.services = payload
    },

    setProviders (state, payload) {
      state.providers = payload
    },

    setLocations (state, payload) {
      state.locations = payload
    },

    setEvents (state, payload) {
      state.events = payload
    },

    setPackages (state, payload) {
      state.packages = payload
    },

    setCustomerOptions (state, payload) {
      state.options.customers = payload
    },

    setServiceOptions (state, payload) {
      state.options.services = payload
    },

    setProviderOptions (state, payload) {
      state.options.providers = payload
    },

    setLocationOptions (state, payload) {
      state.options.locations = payload
    },

    setEventsOptions (state, payload) {
      state.options.events = payload
    },

    setPackagesOptions (state, payload) {
      state.options.packages = payload
    },

    setResetFilters (state) {
      state.customers = []
      state.services = []
      state.events = []
      state.packages = []
      state.providers = []
      state.locations = []
    }
  },

  actions: {
    injectCustomerOptions ({ commit}, payload) {
      commit('setCustomerOptions', payload)
    },

    injectServiceOptions ({ commit, rootGetters}, payload) {
      let services = rootGetters['entities/getServices'].filter(s => payload.includes(s.id))
      commit('setServiceOptions', services)
    },

    injectProviderOptions ({ commit, rootGetters}, payload) {
      let providers = rootGetters['entities/getEmployees'].filter(e => payload.includes(e.id))
      commit('setProviderOptions', providers)
    },

    injectLocationOptions ({ commit, rootGetters}, payload) {
      let locations = rootGetters['entities/getLocations'].filter(l => payload.includes(l.id))
      commit('setLocationOptions', locations)
    },

    injectEventsOptions ({ commit}, payload) {
      commit('setEventsOptions', payload)
    },

    injectPackagesOptions ({ commit, rootGetters}, payload) {
      let packages = rootGetters['entities/getPackages'].filter(p => payload.includes(p.id))
      commit('setPackagesOptions', packages)
    }
  }
}