export default {
  namespaced: true,

  state: () => ({
    booked: null,
    eventId: null,
  }),

  getters: {
    getSelectedEventId (state) {
      return state.eventId
    },

    getBooked (state) {
      return state.booked
    }
  },

  mutations: {
    setEventId (state, payload) {
      state.eventId = payload
    },

    setBooked (state, payload) {
      state.booked = payload
    }
  }
}
