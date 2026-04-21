export default {
  namespaced: true,

  state: () => ({
    addingMethod: "Manually",
    enabled: false,
    maxCapacity: 0,
    maxExtraPeople: 0,
    maxExtraPeopleEnabled: false,
    peopleWaiting: 0,
    // * maybe we should change "isAvailable" property name
    isAvailable: false,
  }),

  getters: {
    getAvailability (state) {
      return state.isAvailable
    },

    getOptions (state) {
      return {
        enabled: state.enabled,
        maxCapacity: state.maxCapacity,
        maxExtraPeople: state.maxExtraPeople,
        maxExtraPeopleEnabled: state.maxExtraPeopleEnabled,
        peopleWaiting: state.peopleWaiting
      }
    }
  },

  mutations: {
    setAllData (state, payload) {
      state.addingMethod = payload.addingMethod
      state.enabled = payload.enabled
      state.maxCapacity = payload.maxCapacity
      state.maxExtraPeople = payload.maxExtraPeople
      state.maxExtraPeopleEnabled = payload.maxExtraPeopleEnabled
      state.peopleWaiting = payload.peopleWaiting
      state.isAvailable = payload.isAvailable
    }
  },

  actions: {
    resetWaitingOptions ({commit}) {
      commit('setAllData', {
        addingMethod: "Manually",
        enabled: false,
        maxCapacity: 0,
        maxExtraPeople: 0,
        maxExtraPeopleEnabled: false,
        peopleWaiting: 0,
        isAvailable: false,
      })
    }
  }
}
