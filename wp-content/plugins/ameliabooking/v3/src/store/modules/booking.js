import {settings} from "../../plugins/settings";

export default {
  namespaced: true,

  state: () => ({
    appointment: {
      bookingStartDate: '',
      bookingStartTime: '',
      bookings: [{
        customer: {
          email: '',
          externalId: null,
          firstName: '',
          id: null,
          lastName: '',
          phone: '',
          countryPhoneIso : '',
          translations: null,
          customFields: null,
          subscribeToMailchimp: settings.mailchimp.checkedByDefault,
        },
        customFields: {},
        customerId: 0,
        extras: [],
        persons: 1,
        haveMandatoryExtras: false,
        minSelectedExtras: 0
      }],
      duration: 0,
      group: false,
      notifyParticipants:'',
      payment: {
        amount: 0,
        gateway: '',
        deposit: false,
        data: {}
      },
      categoryId: null,
      serviceId: null,
      locationId: null,
      providerId: null,
      packageId: null,
    },
    coupon: {
      code: '',
      discount: '',
      deduction: '',
      limit: '',
      required: false,
      bookingsCount: 0
    },
    appointmentsIndex: 0,
    currentCartItem: null,
    shownCart: false,
    appointments: [
      {
        packageId: null,
        serviceId: null,
        index: 0,
        services: {},
      }
    ],
    attachments: {},
    recurringRules: {
      repeat: {
        type: 'daily',
        interval: 1,
      },
      occurrence: {
        type: 'After',
        date: null,
        count: 1,
      },
      days: [
        {value: 'monday', selected: false},
        {value: 'tuesday', selected: false},
        {value: 'wednesday', selected: false},
        {value: 'thursday', selected: false},
        {value: 'friday', selected: false},
        {value: 'saturday', selected: false},
        {value: 'sunday', selected: false}
      ],
      monthly: 0
    },
    loading: false,
    booked: null,
    ready: false,
    packageId: null,
    error: '',
    busyness: [],
    lastBookedProviderId: null
  }),

  getters: {
    getSelection (state) {
      return {
        packageId: state.packageId,
        categoryId: state.appointment.categoryId,
        serviceId: state.appointment.serviceId,
        providerId: state.appointment.providerId,
        locationId: state.appointment.locationId,
        type: state.appointment.type,
      }
    },

    getServiceProviderSelection (state) {
      return {
        serviceId: state.appointment.serviceId,
        providerId: state.appointment.providerId,
        locationId: state.appointment.locationId,
      }
    },

    getPackageId (state) {
      return state.packageId
    },

    getCategoryId (state) {
      return state.appointment.categoryId
    },

    getServiceId (state) {
      return state.appointment.serviceId
    },

    getEmployeeId (state) {
      return state.appointment.providerId
    },

    getLocationId (state) {
      return state.appointment.locationId
    },

    getBooking (state) {
      return state.appointment.bookings[0]
    },

    getBookingPersons (state) {
      let i = state.appointmentsIndex

      return state.appointments[i]
        .services[state.appointments[i].serviceId]
        .list[state.appointments[i].index]
        .persons
    },

    getBookingDuration (state) {
      let i = state.appointmentsIndex

      return state.appointments[i]
        .services[state.appointments[i].serviceId]
        .list[state.appointments[i].index]
        .duration
    },

    getBookableType (state) {
      return state.appointment.type
    },

    getAllMultipleAppointments (state) {
      return state.appointments
    },

    getMultipleAppointmentsServiceSlots (state) {
      let selection = state.appointments[state.appointmentsIndex].services[state.appointments[state.appointmentsIndex].serviceId]

      if (selection.providerId || selection.locationId) {
        let slots = {}

        for (let date in selection.slots) {
          for (let time in selection.slots[date]) {
            for (let i = 0; i < selection.slots[date][time].length; i++) {
              if ((selection.providerId && selection.slots[date][time][i].e === selection.providerId) ||
                (selection.locationId && selection.slots[date][time][i].l === selection.locationId)
              ) {
                if (!(date in slots)) {
                  slots[date] = {}
                }

                slots[date][time] = selection.slots[date][time]
              }
            }
          }
        }

        return slots
      }

      return state.appointments[state.appointmentsIndex].services[state.appointments[state.appointmentsIndex].serviceId].slots
    },

    getMultipleAppointmentsRange (state) {
      let i = state.appointmentsIndex

      return state.appointments[i]
        .services[state.appointments[i].serviceId]
        .list[state.appointments[i].index]
        .range
    },

    getMultipleAppointmentsAppCount: (state) => (serviceId) => {
      let i = state.appointmentsIndex

      return state.appointments[i]
          .services[serviceId]
          .appCount
    },

    getMultipleAppointmentsDate (state) {
      let i = state.appointmentsIndex

      return state.appointments[i]
          .services[state.appointments[i].serviceId]
          .list[state.appointments[i].index]
          .date
    },

    getMultipleAppointmentsOccupied (state) {
      return state.appointments[state.appointmentsIndex].services[state.appointments[state.appointmentsIndex].serviceId].occupied
    },

    getMultipleAppointmentsTime (state) {
      let i = state.appointmentsIndex

      return state.appointments[i]
          .services[state.appointments[i].serviceId]
          .list[state.appointments[i].index]
          .time
    },

    getMultipleAppointmentsSlots (state) {
      let i = state.appointmentsIndex

      return state.appointments[i]
        .services[state.appointments[i].serviceId]
        .slots
    },

    getMultipleAppointmentsLastDate (state) {
      let i = state.appointmentsIndex

      return state.appointments[i]
          .services[state.appointments[i].serviceId]
          .lastDate
    },

    getSelectedExtras (state) {
      let i = state.appointmentsIndex

      return state.appointments[i]
        .services[state.appointments[i].serviceId]
        .list[state.appointments[i].index]
        .extras
    },

    getCustomerId (state) {
      return state.appointment.bookings[0].customer.id
    },

    getCustomerFirstName (state) {
      return state.appointment.bookings[0].customer.firstName
    },

    getCustomerLastName (state) {
      return state.appointment.bookings[0].customer.lastName
    },

    getCustomerEmail (state) {
      return state.appointment.bookings[0].customer.email
    },

    getCustomerPhone (state) {
      return state.appointment.bookings[0].customer.phone
    },

    getCustomerCountryPhoneIso (state) {
      return state.appointment.bookings[0].customer.countryPhoneIso
    },

    getCustomerSubscribe (state) {
      return state.appointment.bookings[0].customer.subscribeToMailchimp
    },

    getCustomerExternalId (state) {
      return state.appointment.bookings[0].customer.externalId
    },

    getCustomerTranslations (state) {
      return state.appointment.bookings[0].customer.translations
    },

    getCustomerCustomFields (state) {
      return state.appointment.bookings[0].customer.customFields
    },

    getAvailableCustomFields (state) {
      return state.appointment.bookings[0].customFields
    },

    getRecurringRepeatType (state) {
      return state.recurringRules.repeat.type
    },

    getRecurringRepeatInterval (state) {
      return state.recurringRules.repeat.interval
    },

    getRecurringOccurrenceType (state) {
      return state.recurringRules.occurrence.type
    },

    getRecurringOccurrenceDate (state) {
      return state.recurringRules.occurrence.date
    },

    getRecurringOccurrenceCount (state) {
      return state.recurringRules.occurrence.count
    },

    getRecurringDays (state) {
      return state.recurringRules.days
    },

    getRecurringMonthly (state) {
      return state.recurringRules.monthly
    },

    getAttachments (state) {
      return state.attachments
    },

    getCoupon (state) {
      return state.coupon
    },

    getCouponCode (state) {
      return state.coupon.code
    },

    getCouponValidated (state) {
      return !state.coupon.required || (state.coupon.code !== '')
    },

    getPaymentGateway (state) {
      return state.appointment.payment.gateway
    },

    getPaymentDeposit (state) {
      return state.appointment.payment.deposit
    },

    getLoading (state) {
      return state.loading
    },

    getBooked (state) {
      return state.booked
    },

    getError (state) {
      return state.error
    },

    getBusyness (state) {
      return state.busyness
    },

    getLastBookedProviderId (state) {
      return state.lastBookedProviderId
    },

    getCurrentCartItem (state) {
      return state.currentCartItem
    },

    getShownCart (state) {
      return state.shownCart
    },

    getCartItemIndex (state) {
      return state.appointmentsIndex
    },
  },

  mutations: {
    setPackageId (state, payload) {
      state.packageId = payload
    },

    setCategoryId (state, payload) {
      state.appointment.categoryId = payload
    },

    setServiceId (state, payload) {
      state.appointment.serviceId = payload
    },

    setEmployeeId (state, payload) {
      state.appointment.providerId = payload
    },

    setLocationId (state, payload) {
      state.appointment.locationId = payload
    },

    setCartItem (state, payload) {
      state.appointments[state.appointmentsIndex] = payload
    },

    setCurrentCartItem (state, payload) {
      state.currentCartItem = payload
    },

    setShownCart (state, payload) {
      state.shownCart = payload
    },

    setCartItemIndex (state, payload) {
      state.appointmentsIndex = payload
    },

    setMultipleAppointments (state, payload) {
      state.appointments = payload
    },

    setMultipleAppointmentsServiceId (state, payload) {
      state.appointments[state.appointmentsIndex].serviceId = payload
    },

    setMultipleAppointmentsIndex (state, payload) {
      state.appointments[state.appointmentsIndex].index = payload
    },

    unsetMultipleAppointmentsData (state, payload) {
      let i = state.appointmentsIndex

      if (payload !== '') {
        state.appointments[i]
            .services[state.appointments[i].serviceId]
            .list[payload] = {
          date: null,
          time: null,
          providerId: null,
          locationId: null,
          persons: state.appointments[i].services[state.appointments[i].serviceId].list[payload].persons,
          extras: state.appointments[i].services[state.appointments[i].serviceId].list[payload].extras,
          duration: null,
          slots: [],
          price: null,
        }
      }
    },

    unsetRecurringItems (state) {
      let i = state.appointmentsIndex

      state.appointments[i].services[state.appointments[i].serviceId].list.length = 1
    },

    setMultipleAppointmentsRange (state, payload) {
      let i = state.appointmentsIndex

      state.appointments[i]
        .services[state.appointments[i].serviceId]
        .list[state.appointments[i].index]
        .range = payload
    },

    setMultipleAppointmentsExistingApp (state, payload) {
      let i = state.appointmentsIndex

      state.appointments[i]
          .services[state.appointments[i].serviceId]
          .list[state.appointments[i].index]
          .existingApp = payload
    },

    setMultipleAppointmentsAppCount (state, payload) {
      let i = state.appointmentsIndex

      state.appointments[i]
          .services[state.appointments[i].serviceId]
          .appCount = payload
    },

    setMultipleAppointmentsDate (state, payload) {
      let i = state.appointmentsIndex

      state.appointments[i]
        .services[state.appointments[i].serviceId]
        .list[state.appointments[i].index]
        .date = payload
    },

    setMultipleAppointmentsTime (state, payload) {
      let i = state.appointmentsIndex

      state.appointments[i]
        .services[state.appointments[i].serviceId]
        .list[state.appointments[i].index]
        .time = payload
    },

    setMultipleAppointmentsSlots (state, payload) {
      let i = state.appointmentsIndex

      state.appointments[i]
        .services[state.appointments[i].serviceId]
        .slots = payload
    },

    setMultipleAppointmentsOccupied (state, payload) {
      let i = state.appointmentsIndex

      state.appointments[i]
        .services[state.appointments[i].serviceId]
        .occupied = payload
    },

    setMultipleAppointmentsLastDate (state, payload) {
      let i = state.appointmentsIndex

      state.appointments[i]
          .services[state.appointments[i].serviceId]
          .lastDate = payload
    },

    setMultipleAppointmentsServiceProvider (state, payload) {
      let i = state.appointmentsIndex

      state.appointments[i]
        .services[state.appointments[i].serviceId]
        .providerId = payload ? parseInt(payload) : null
    },

    setMultipleAppointmentsServiceLocation (state, payload) {
      let i = state.appointmentsIndex

      state.appointments[i]
        .services[state.appointments[i].serviceId]
        .locationId = payload ? parseInt(payload) : null
    },

    setBookingPersons (state, payload) {
      let i = state.appointmentsIndex

      state.appointments[i]
        .services[state.appointments[i].serviceId]
        .list[state.appointments[i].index]
        .persons = payload + (settings.appointments.bringingAnyoneLogic === 'additional' ? 1 : 0)
    },

    setBookingDuration (state, payload) {
      let i = state.appointmentsIndex

      state.appointments[i]
        .services[state.appointments[i].serviceId]
        .list[state.appointments[i].index]
        .duration = payload
    },

    setAvailableCustomFields(state, payload) {
      let customFields = state.appointment.bookings[0].customer?.customFields
      let populateAvailableCustomFields = { ...payload }

      if (state.appointment.bookings[0].customer?.id && customFields) {
        let customerCustomFields = JSON.parse(customFields)

        for (let id in customerCustomFields) {
          if (populateAvailableCustomFields.hasOwnProperty(id)) {
            populateAvailableCustomFields[id].value = customerCustomFields[id].value
          }
        }
      }

      state.appointment.bookings[0].customFields = populateAvailableCustomFields
    },

    setBookableType (state, payload) {
      state.appointment.type = payload
    },

    setSelectedExtras (state, payload) {
      let i = state.appointmentsIndex

      state.appointments[i]
        .services[state.appointments[i].serviceId]
        .list[state.appointments[i].index]
        .extras = payload ? payload : []
    },

    setCustomerId (state, payload) {
      state.appointment.bookings[0].customer.id = payload
    },

    setCustomerFirstName (state, payload) {
      state.appointment.bookings[0].customer.firstName = payload
    },

    setCustomerLastName (state, payload) {
      state.appointment.bookings[0].customer.lastName = payload
    },

    setCustomerEmail (state, payload) {
      state.appointment.bookings[0].customer.email = payload
    },

    setCustomerPhone (state, payload) {
      state.appointment.bookings[0].customer.phone = payload
    },

    setCustomerCountryPhoneIso (state, payload) {
      state.appointment.bookings[0].customer.countryPhoneIso = payload
    },

    setCustomerSubscribe (state, payload) {
      state.appointment.bookings[0].customer.subscribeToMailchimp = payload
    },

    setCustomerExternalId (state, payload) {
      state.appointment.bookings[0].customer.externalId = payload
    },

    setRecurringRepeatType (state, payload) {
      state.recurringRules.repeat.type = payload
    },

    setRecurringRepeatInterval (state, payload) {
      state.recurringRules.repeat.interval = payload
    },

    setRecurringOccurrenceType (state, payload) {
      state.recurringRules.occurrence.type = payload
    },

    setRecurringOccurrenceDate (state, payload) {
      state.recurringRules.occurrence.date = payload
    },

    setRecurringOccurrenceCount (state, payload) {
      state.recurringRules.occurrence.count = payload
    },

    setRecurringDays (state, payload) {
      state.recurringRules.days.find(day => day.value === payload.value).selected = payload.selected
    },

    setRecurringMonthly (state, payload) {
      state.recurringRules.monthly = payload
    },

    setCustomerTranslations (state, payload) {
      state.appointment.bookings[0].customer.translations = payload
    },

    setCustomerCustomFields (state, payload) {
      state.appointment.bookings[0].customer.customFields = payload
    },

    setAttachment (state, payload) {
      state.attachments[payload.id] = payload.raw
    },

    setCoupon (state, payload) {
      state.coupon = payload
    },

    setCouponCode (state, payload) {
      state.coupon.code = payload
    },

    setCouponRequired (state, payload) {
      state.coupon.required = payload
    },

    setBookingsCount (state, payload) {
      state.coupon.bookingsCount = payload
    },

    setPaymentGateway (state, payload) {
      state.appointment.payment.gateway = payload
    },

    setPaymentDeposit (state, payload) {
      state.appointment.payment.deposit = payload
    },

    setLoading (state, payload) {
      state.loading = payload
    },

    setBooked (state, payload) {
      state.booked = payload
    },

    setError (state, payload) {
      state.error = payload
    },

    setBusyness (state, payload) {
      state.busyness = payload
    },

    setLastBookedProviderId (state, payload) {
      state.lastBookedProviderId = !payload.fromBackend || (state.lastBookedProviderId === null && payload.providerId !==null) ? payload.providerId : state.lastBookedProviderId
    },
  },
}
