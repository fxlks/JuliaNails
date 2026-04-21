export default {
  namespaced: true,

  state: () => ({
    paymentLinkLoader: null,
    timeZone: null,
    appointmentsLoading: false,
    packageLoading: false,
    eventsLoading: false,
    paymentLinkError: {
      appointment: false,
      event: false,
      package: false
    }
  }),

  getters: {
    getPaymentLinkLoader (state) {
      return state.paymentLinkLoader
    },
    getTimeZone (state) {
      return state.timeZone
    },
    getAppointmentsLoading (state) {
      return state.appointmentsLoading
    },
    getPackageLoading (state) {
      return state.packageLoading
    },
    getEventsLoading (state) {
      return state.eventsLoading
    },
    getPaymentLinkError (state) {
      return state.paymentLinkError
    },
  },

  mutations: {
    setPaymentLinkLoader (state, payload) {
      state.paymentLinkLoader = payload
    },
    setTimeZone (state, payload) {
      state.timeZone = payload
    },
    setAppointmentsLoading (state, payload) {
      state.appointmentsLoading = payload
    },
    setPackageLoading (state, payload) {
      state.packageLoading = payload
    },
    setEventsLoading (state, payload) {
      state.eventsLoading = payload
    },
    setPaymentLinkError (state, payload) {
      state.paymentLinkError[payload.type] = payload.value
    },
  },

  actions: {}
}
