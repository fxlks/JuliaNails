export default {
  namespaced: true,

  state: {
    id: null,
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    locationId: '',
    serviceList: {},
    outlookCalendar: {
      id: null,
      calendarId: '',
      token: null,
    },
    googleCalendar: {
      id: null,
      calendarId: '',
      token: null,
    },
    appleCalendarId: '',
    googleCalendarId: '',
    outlookCalendarId: '',
    employeeAppleCalendar: {
      iCloudId: null,
      appSpecificPassword: null
    },
    stripeConnect: null,
    zoomUserId: '',
    note: '',
    description: '',
    descriptionMode: 'html',
    weekDayList: [],
    specialDayList: [],
    savedSpecialDayList: [],
    dayOffList: [],
    savedDayOffList: [],
    badgeId: null,
  },

  getters: {
    getEmployee (state) {
      return state
    },

    getId (state) {
      return state.id
    },

    getFirstName (state) {
      return state.firstName
    },

    getLastName (state) {
      return state.lastName
    },

    getEmail (state) {
      return state.email
    },

    getPhone (state) {
      return state.phone
    },

    getLocationId (state) {
      return state.locationId
    },

    getServiceList (state) {
      return state.serviceList
    },

    getZoomUserId (state) {
      return state.zoomUserId
    },

    getOutlookCalendarId (state) {
      return state.outlookCalendar.calendarId
    },

    getOutlookToken (state) {
      return state.outlookCalendar.token
    },

    getGoogleCalendarId (state) {
      return state.googleCalendar.calendarId
    },

    getGoogleToken (state) {
      return state.googleCalendar.token
    },

    getAppleCalendarId (state) {
      return state.appleCalendarId
    },

    getUserTableGoogleCalendarId (state) {
      return state.googleCalendarId
    },

    getUserTableOutlookCalendarId (state) {
      return state.outlookCalendarId
    },

    getEmployeeAppleCalendarICloudId (state) {
      return state.employeeAppleCalendar.iCloudId
    },

    getEmployeeAppleCalendarAppSpecificPassword (state) {
      return state.employeeAppleCalendar.appSpecificPassword
    },

    getStripeConnect (state) {
      return state.stripeConnect
    },

    getNote (state) {
      return state.note
    },

    getDescription (state) {
      return state.description
    },

    getDescriptionMode (state) {
      return state.descriptionMode
    },

    getSavedSpecialDayList (state) {
      return state.savedSpecialDayList
    },

    getSavedDayOffList (state) {
      return state.savedDayOffList
    },
  },

  mutations: {
    setEmployee (state, payload) {
      Object.assign(state, payload)
    },

    setId (state, payload) {
      state.id = payload
    },

    setFirstName (state, payload) {
      state.firstName = payload
    },

    setLastName (state, payload) {
      state.lastName = payload
    },

    setEmail (state, payload) {
      state.email = payload
    },

    setPhone (state, payload) {
      state.phone = payload
    },

    setLocationId (state, payload) {
      state.locationId = payload
    },

    setServiceList (state, payload) {
      state.serviceList = payload
    },

    setZoomUserId (state, payload) {
      state.zoomUserId = payload
    },

    setOutlookId (state, payload) {
      state.outlookCalendar.id = payload
    },

    setOutlookCalendarId (state, payload) {
      state.outlookCalendar.calendarId = payload
    },

    setOutlookToken (state, payload) {
      state.outlookCalendar.token = payload
    },

    setGoogleId (state, payload) {
      state.googleCalendar.id = payload
    },

    setGoogleCalendarId (state, payload) {
      state.googleCalendar.calendarId = payload
    },

    setGoogleToken (state, payload) {
      state.googleCalendar.token = payload
    },

    setEmployeeOutlookCalendarId(state, payload) {
      state.outlookCalendarId = payload
    },

    setAppleCalendarId (state, payload) {
      state.appleCalendarId = payload
    },

    setEmployeeGoogleCalendarId (state, payload) {
      state.googleCalendarId = payload
    },

    setEmployeeAppleCalendarICloudId (state, payload) {
      state.employeeAppleCalendar.iCloudId = payload
    },

    setEmployeeAppleCalendarAppSpecificPassword (state, payload) {
      state.employeeAppleCalendar.appSpecificPassword = payload
    },

    setStripeConnect (state, payload) {
      state.stripeConnect = payload
    },

    setNote (state, payload) {
      state.note = payload
    },

    setDescription (state, payload) {
      state.description = payload
    },

    setDescriptionMode (state, payload) {
      state.descriptionMode = payload
    },

    setServiceEnabled (state, payload) {
      let { serviceId, categoryId, value } = payload
      state.serviceList[categoryId][serviceId].enabled = value
    },

    setSavedSpecialDayList (state, payload) {
      state.savedSpecialDayList = payload
    },

    setSavedDayOffList (state, payload) {
      state.savedDayOffList = payload
    },
  },

  actions: {
  }
}
