function getDefaultAttendee() {
  return {
    id: null,
    type: 'event',
    eventId: null,
    customerId: null,
    customer: null,
    persons: 1,
    customFields: {},
    ticketsData: [],
    status: 'approved',
    couponCode: '',
    validateCoupon: false,
    isBackendOrCabinet: true,
    createPaymentLinks: false,
    tax: null,
    price: 0,
    aggregatedPrice: true,
    coupon: null,
    payment: {gateway: 'onSite'},
    payments: [],
    active: false,
  }
}

export default {
  namespaced: true,

  state: getDefaultAttendee(),

  getters: {
    getDefaultAttendee() {
      return getDefaultAttendee()
    },

    getAttendee(state) {
      return state
    },

    getId(state) {
      return state.id
    },

    getEventId(state) {
      return state.eventId
    },

    getCustomerId(state) {
      return state.customerId
    },

    getCustomer(state) {
      return state.customer
    },

    getStatus(state) {
      return state.status
    },

    getPersons(state) {
      return state.persons
    },

    getTickets(state) {
      return state.ticketsData
    },

    getCustomFields(state) {
      return state.customFields
    },

    getTax(state) {
      return state.tax
    },

    getPrice(state) {
      return state.price
    },

    getAggregatedPrice(state) {
      return state.aggregatedPrice
    },

    getCoupon(state) {
      return state.coupon
    },

    getPayment(state) {
      return state.payment
    },

    getPayments(state) {
      return state.payments
    },

    getActive(state) {
      return state.active
    },
  },

  mutations: {
    setAttendee(state, payload = {}) {
      Object.assign(state, getDefaultAttendee(), payload)
    },

    setCustomerId(state, payload) {
      state.customerId = payload
    },

    setStatus(state, payload) {
      state.status = payload
    },

    setPersons(state, payload) {
      state.persons = payload
    },

    setTickets(state, payload) {
      state.tickets = payload
    },

    setCustomFields(state, payload) {
      state.customFields[payload.id].value = payload.value
    },

    setAggregatedPrice(state, payload) {
      state.aggregatedPrice = payload
    },

    setCoupon(state, payload) {
      state.coupon = payload
    },

    setActive(state, payload) {
      state.active = payload
    },
  },
}
