export default {
  namespaced: true,

  state: () => ({
    enabled: false,
    maxCapacity: 0,
    maxExtraPeople: 0,
    maxExtraPeopleEnabled: false,
    peopleWaiting: 0,
    isWaitingListSlot: false,
    selectedProviderId: null,
  }),

  getters: {
    getIsWaitingListSlot (state) {
      return state.isWaitingListSlot
    },

    getWaitingListOptions (state) {
      return {
        enabled: state.enabled,
        maxCapacity: state.maxCapacity,
        maxExtraPeople: state.maxExtraPeople,
        maxExtraPeopleEnabled: state.maxExtraPeopleEnabled,
        peopleWaiting: state.peopleWaiting,
        isWaitingListSlot: state.isWaitingListSlot,
        selectedProviderId: state.selectedProviderId,
      }
    },

    getOptions (state) {
      return {
        enabled: state.enabled,
        maxCapacity: state.maxCapacity,
        maxExtraPeople: state.maxExtraPeople,
        maxExtraPeopleEnabled: state.maxExtraPeopleEnabled,
        peopleWaiting: state.peopleWaiting,
        isWaitingListSlot: state.isWaitingListSlot,
        selectedProviderId: state.selectedProviderId,
      }
    }
  },

  mutations: {
    setAllData (state, payload) {
      state.enabled = payload.enabled
      state.maxCapacity = payload.maxCapacity
      state.maxExtraPeople = payload.maxExtraPeople
      state.maxExtraPeopleEnabled = payload.maxExtraPeopleEnabled
      state.peopleWaiting = payload.peopleWaiting
      state.isWaitingListSlot = 'isWaitingListSlot' in payload ? payload.isWaitingListSlot : false
      state.selectedProviderId = 'selectedProviderId' in payload ? payload.selectedProviderId : null
    },
    setIsWaitingListSlot (state, payload) {
      state.isWaitingListSlot = !!payload
    },
    setPeopleWaiting (state, payload) {
      state.peopleWaiting = payload
    },
    setSelectedProviderId (state, payload) {
      state.selectedProviderId = payload
    }
  },

  actions: {
    resetWaitingOptions ({commit}) {
      commit('setAllData', {
        enabled: false,
        maxCapacity: 0,
        maxExtraPeople: 0,
        maxExtraPeopleEnabled: false,
        peopleWaiting: 0,
        isWaitingListSlot: false,
        selectedProviderId: null,
      })
    }
  }
}
