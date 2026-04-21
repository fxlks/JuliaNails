function getDefaultAppointment() {
  return {
    id: null,
    categoryId: null,
    serviceId: null,
    locationId: null,
    lessonSpace: null,
    providerId: null,
    startDate: '',
    startTime: '',
    notifyParticipants: true,
    createPaymentLinks: true,
    internalNotes: '',
    bookings: [],
    customFields: [],
    customFieldsRules: {},
    employeeService: null,
    targetedDate: null,
    active: false,
  }
}

export default {
  namespaced: true,

  state: getDefaultAppointment(),

  getters: {
    getSelection(state) {
      return {
        categoryId: state.categoryId,
        serviceId: state.serviceId,
        providerId: state.providerId,
        locationId: state.locationId,
      }
    },

    getId(state) {
      return state.id
    },

    getCategoryId(state) {
      return state.categoryId
    },

    getServiceId(state) {
      return state.serviceId
    },

    getProviderId(state) {
      return state.providerId
    },

    getLocationId(state) {
      return state.locationId
    },

    getLessonSpace(state) {
      return state.lessonSpace
    },

    getStartDate(state) {
      return state.startDate
    },

    getStartTime(state) {
      return state.startTime
    },

    getNotifyParticipants(state) {
      return state.notifyParticipants
    },

    getCreatePaymentLinks(state) {
      return state.createPaymentLinks
    },

    getInternalNotes(state) {
      return state.internalNotes
    },

    getBooking: (state) => (index) => {
      return state.bookings[index]
    },

    getBookings(state) {
      return state.bookings
    },

    getAppointmentData(state) {
      return {
        id: state.id,
        categoryId: state.categoryId,
        serviceId: state.serviceId,
        locationId: state.locationId,
        lessonSpace: state.lessonSpace,
        providerId: state.providerId,
        startDate: state.startDate,
        startTime: state.startTime,
        notifyParticipants: state.notifyParticipants,
        internalNotes: state.internalNotes,
        bookings: state.bookings,
      }
    },

    getEmployeeService(state) {
      return state.employeeService
    },

    getTargetedDate(state) {
      return state.targetedDate
    },

    getActive(state) {
      return state.active
    },

    getCustomFields(state) {
      return state.customFields
    },

    getCustomFieldsRules(state) {
      return state.customFieldsRules
    },
  },

  mutations: {
    resetAppointment(state, payload) {
      Object.assign(state, getDefaultAppointment(), payload)
    },

    setAppointment(state, payload) {
      Object.assign(state, payload)
    },

    setCategoryId(state, payload) {
      state.categoryId = payload
    },

    setServiceId(state, payload) {
      state.serviceId = payload
    },

    setProviderId(state, payload) {
      state.providerId = payload
    },

    setLocationId(state, payload) {
      state.locationId = payload
    },

    setLessonSpace(state, payload) {
      state.lessonSpace = payload
    },

    setStartDate(state, payload) {
      state.startDate = payload
    },

    setStartTime(state, payload) {
      state.startTime = payload
    },

    setNotifyParticipants(state, payload) {
      state.notifyParticipants = payload
    },

    setCreatePaymentLinks(state, payload) {
      state.createPaymentLinks = payload
    },

    setInternalNotes(state, payload) {
      state.internalNotes = payload
    },

    setBooking(state, payload) {
      if (state.bookings.length === payload.index) {
        state.bookings.push(payload.value)
      } else {
        state.bookings[payload.index] = payload.value
      }
    },

    setCustomFields(state, payload) {
      state.customFields = payload
    },

    setEmployeeService(state, payload) {
      state.employeeService = payload
    },

    setTargetedDate(state, payload) {
      state.targetedDate = payload
    },

    setActive(state, payload) {
      state.active = payload
    },

    setBookingStatus(state, payload) {
      state.bookings[payload.index].status = payload.value
    },

    setAllBookingsStatus(state, payload) {
      state.bookings.forEach((booking) => {
        booking.status = payload
      })
    },

    setBookingPersons(state, payload) {
      state.bookings[payload.index].persons = payload.value
    },

    setBookingDuration(state, payload) {
      state.bookings[payload.index].duration = payload.value
    },
  },

  actions: {
    serviceCustomFieldsFiltration({ state, commit, rootGetters }) {
      if (state.employeeService !== null) {
        let customFieldsEntity = rootGetters['entities/getCustomFields']
        let filteredCustomFields = customFieldsEntity.filter(
          (i) =>
            i.allServices ||
            (i.services.map((s) => s.id).indexOf(state.employeeService.id) !==
              -1 &&
              i.type !== 'content')
        )

        commit('setCustomFields', filteredCustomFields)
      } else {
        commit('setCustomFields', [])
      }
    },

    updateEmployeeService({ state, commit, dispatch, rootGetters }, payload) {
      if (payload) {
        let employeeService = rootGetters['entities/getEmployees']
          .find((i) => i.id === state.providerId)
          .serviceList.find((i) => i.id === payload)

        commit(
          'setEmployeeService',
          typeof employeeService !== 'undefined'
            ? employeeService
            : rootGetters['entities/getServices']
              .find((i) => i.id === state.serviceId)
        )
        // * Set custom fields based on service selection
        dispatch('serviceCustomFieldsFiltration')
      } else {
        state.employeeService = null
      }
    },

    resetBookingsExtras({ state }, index) {
      state.bookings[index].extras = state.employeeService.extras.map(
        (e) => new Object({ extraId: e.id, quantity: 0 })
      )
    },

    deleteAllBookingCustomFields({ state }) {
      state.bookings.forEach((booking) => {
        booking.customFields = {}
      })
    },

    resetBookingCustomFields({ state, getters }, key) {
      state.bookings[key].customFields = getters['getCustomFields'].reduce(
        (actual, current) => {
          actual[current.id] = {
            label: current.label,
            type: current.type,
            value:
              current.type === 'checkbox' || current.type === 'file' ? [] : '',
          }

          return actual
        },
        {}
      )
    },

    updateBookingCustomField({ state }, payload) {
      const { bookingKey, fieldId, value } = payload
      state.bookings[bookingKey].customFields[fieldId].value = value
    },

    deleteAllCustomFieldsFormRules({ state }) {
      state.customFieldsRules = {}
    },

    addCustomFieldsFormRules({ state, getters }, payload) {
      const { key, label } = payload
      getters['getCustomFields'].forEach((field) => {
        state.customFieldsRules[`${key}cf${field.id}`] = [
          {
            message: label,
            required: field.required,
            trigger: ['submit', 'change'],
          },
        ]
      })
    },

    addAllCustomFieldsFormRules({ state, getters }, payload) {
      const { label } = payload
      state.bookings.forEach((booking, index) => {
        getters['getCustomFields'].forEach((field) => {
          state.customFieldsRules[`${index}cf${field.id}`] = [
            {
              message: label,
              required: field.required,
              trigger: ['submit', 'change'],
            },
          ]
        })
      })
    },

    recreateAllBookingCustomFields({ state, dispatch }, payload) {
      const { label } = payload

      if (state.bookings.length) {
        // * Delete all booking custom fields
        dispatch('deleteAllBookingCustomFields')
        // * Delete all custom fields form rules
        dispatch('deleteAllCustomFieldsFormRules')
      }

      state.bookings.forEach((booking, index) => {
        // * Reset booking extras
        dispatch('resetBookingsExtras', index)

        // * Reset (Add blank) booking custom fields
        dispatch('resetBookingCustomFields', index)

        // * Add custom fields form rules
        dispatch('addCustomFieldsFormRules', { key: index, label })
      })
    },

    removeBooking({ state, getters ,commit}, payload) {
      state.bookings.splice(payload, 1)

      commit('customerInfo/setCustomersIds', getters['getBookings'].map((i) => i.customer.id), { root: true })
    },
  },
}
