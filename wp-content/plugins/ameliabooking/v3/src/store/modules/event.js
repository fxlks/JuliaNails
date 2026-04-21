function getDefaultEvent() {
  return {
    applyGlobally: false,
    utc: false,

    id: null,
    parentId: null,
    name: '',
    locationId: 0,
    customLocation: null,
    tags: [],
    providers: [],
    organizerId: null,
    zoomUserId: null,
    timeZone: null,
    notifyParticipants: false,
    description: '',
    descriptionMode: 'text',
    gallery: [],
    translations: null,

    periods: [
      { startDate: null, endDate: null, startTime: null, endTime: null },
    ],
    bookingOpens: {
      disabled: true,
      date: '',
      time: null,
    },
    bookingOpensRec: true,
    bookingCloses: {
      disabled: true,
      date: '',
      time: null,
    },
    bookingClosesRec: true,

    recurringEnabled: false,
    recurring: {
      cycle: '',
      cycleInterval: 1,
      monthDate: null,
      monthlyOnDay: 'monday',
      monthlyOnRepeat: 'first',
      monthlyRepeat: 'each',
      order: null,
      until: null,
    },

    maxCapacity: 1,
    price: 0,
    aggregatedPrice: true,
    bringingAnyone: true,
    bookMultipleTimes: true,

    depositEnabled: false,
    deposit: 0,
    depositPayment: 'percentage',
    depositPerPerson: 1,
    fullPayment: 0,

    maxCustomCapacity: 1,
    maxCustomCapacityEnabled: null,
    customPricing: false,
    customTickets: [],
    customTicketsRangesEnabled: false,
    customTicketsRanges: [],

    closeAfterMinEnabled: false,
    closeAfterMin: 1,
    closeAfterMinBookings: false,

    maxExtraPeopleEnabled: false,
    maxExtraPeople: 0,

    color: '#1788FB',
    show: true,

    settings: {
      general: {
        minimumTimeRequirementPriorToCanceling: null,
        redirectUrlAfterAppointment: null,
      },
      payments: {
        paymentLinks: {
          enabled: null,
          redirectUrl: null,
        },
        onSite: null,
        wc: {
          enabled: null,
          productId: null,
        },
        payPal: {
          enabled: null,
        },
        stripe: {
          enabled: null,
        },
        mollie: {
          enabled: null,
        },
        razorpay: {
          enabled: null,
        },
        square: {
          enabled: null,
        },
        barion: {
          enabled: null,
        }
      },
      googleMeet: {
        enabled: null,
      },
      microsoftTeams: {
        enabled: null,
      },
      lessonSpace: {
        enabled: null,
      },
      waitingList: {
        enabled: false,
        addingMethod: 'Manually',
        maxCapacity: 1,
        maxExtraPeopleEnabled: false,
        maxExtraPeople: 0,
      },
    },

    active: false,
  }
}

export default {
  namespaced: true,

  state: getDefaultEvent(),

  getters: {
    getDefaultEvent() {
      return getDefaultEvent()
    },

    getEvent(state) {
      return state
    },

    getId(state) {
      return state.id
    },

    getName(state) {
      return state.name
    },

    getLocationId(state) {
      return state.locationId
    },

    getCustomLocation(state) {
      return state.customLocation
    },

    getOrganizerId(state) {
      return state.organizerId
    },

    getZoomUserId(state) {
      return state.zoomUserId
    },

    getDescription(state) {
      return state.description
    },

    getDescriptionMode(state) {
      return state.descriptionMode
    },

    getGallery(state) {
      return state.gallery
    },

    getNotifyParticipants(state) {
      return !!state.notifyParticipants
    },

    getParentId(state) {
      return state.parentId
    },

    getTranslations(state) {
      return state.translations
    },

    getTags(state) {
      return state.tags
    },

    getProviders(state) {
      return state.providers
    },

    getPeriods(state) {
      return state.periods
    },

    getBookingOpensDisabled(state) {
      return state.bookingOpens.disabled
    },

    getBookingOpensDate(state) {
      return state.bookingOpens.date
    },

    getBookingOpensTime(state) {
      return state.bookingOpens.time
    },

    getBookingClosesDisabled(state) {
      return state.bookingCloses.disabled
    },

    getBookingClosesDate(state) {
      return state.bookingCloses.date
    },

    getBookingClosesTime(state) {
      return state.bookingCloses.time
    },

    getRecurringEnabled(state) {
      return state.recurringEnabled
    },

    getRecurring(state) {
      return state.recurring
    },

    getRecurringCycle(state) {
      return state.recurring.cycle
    },

    getRecurringCycleInterval(state) {
      return state.recurring.cycleInterval
    },

    getRecurringMonthDate(state) {
      return state.recurring.monthDate
    },

    getRecurringMonthlyRepeat(state) {
      return state.recurring.monthlyRepeat
    },

    getRecurringMonthlyOnDay(state) {
      return state.recurring.monthlyOnDay
    },

    getRecurringMonthlyOnRepeat(state) {
      return state.recurring.monthlyOnRepeat
    },

    getRecurringOrder(state) {
      return state.recurring.order
    },

    getRecurringUntil(state) {
      return state.recurring.until
    },

    getBookingOpensRec(state) {
      return state.bookingOpensRec
    },

    getBookingClosesRec(state) {
      return state.bookingClosesRec
    },

    getPrice(state) {
      return state.price
    },

    getMaxCapacity(state) {
      return state.maxCapacity
    },

    getBringingAnyone(state) {
      return state.bringingAnyone
    },

    getAggregatedPrice(state) {
      return state.aggregatedPrice
    },

    getBookMultipleTimes(state) {
      return state.bookMultipleTimes
    },

    getDepositEnabled(state) {
      return state.depositEnabled
    },

    getDeposit(state) {
      return state.deposit
    },

    getDepositPayment(state) {
      return state.depositPayment
    },

    getDepositPerPerson(state) {
      return state.depositPerPerson
    },

    getFullPayment(state) {
      return state.fullPayment
    },

    getCustomPricing(state) {
      return state.customPricing
    },

    getCustomTickets(state) {
      return state.customTickets
    },

    getCustomTicketsRangesEnabled(state) {
      return state.customTicketsRangesEnabled
    },

    getCustomTicketsRanges(state) {
      return state.customTicketsRanges
    },

    getMaxCustomCapacity(state) {
      return state.maxCustomCapacity
    },

    getMaxCustomCapacityEnabled(state) {
      return state.maxCustomCapacityEnabled
    },

    getCloseAfterMinEnabled(state) {
      return state.closeAfterMinEnabled
    },

    getCloseAfterMin(state) {
      return state.closeAfterMin
    },

    getCloseAfterMinBookings(state) {
      return state.closeAfterMinBookings
    },

    getMaxExtraPeopleEnabled(state) {
      return state.maxExtraPeopleEnabled
    },

    getMaxExtraPeople(state) {
      return state.maxExtraPeople
    },

    getColor(state) {
      return state.color
    },

    getShow(state) {
      return state.show
    },

    getSettingsGeneralMinimumTimeRequirementPriorToCanceling(state) {
      return state.settings.general.minimumTimeRequirementPriorToCanceling
    },

    getSettingsGeneralRedirectUrlAfterAppointment(state) {
      return state.settings.general.redirectUrlAfterAppointment
    },

    getSettingsPaymentsPaymentLinksEnabled(state) {
      return state.settings.payments.paymentLinks.enabled
    },

    getSettingsPaymentsPaymentLinksRedirectUrl(state) {
      return state.settings.payments.paymentLinks.redirectUrl
    },

    getSettingsPaymentsOnSite(state) {
      return state.settings.payments.onSite
    },

    getSettingsPaymentsWcEnabled(state) {
      return state.settings.payments.wc.enabled
    },

    getSettingsPaymentsWcProductId(state) {
      return state.settings.payments.wc.productId
    },

    getSettingsPaymentsStripeEnabled(state) {
      return state.settings.payments.stripe.enabled
    },

    getSettingsPaymentsPayPalEnabled(state) {
      return state.settings.payments.payPal.enabled
    },

    getSettingsPaymentsMollieEnabled(state) {
      return state.settings.payments.mollie.enabled
    },

    getSettingsPaymentsRazorpayEnabled(state) {
      return state.settings.payments.razorpay.enabled
    },

    getSettingsPaymentsBarionEnabled(state) {
      return state.settings.payments.barion.enabled
    },

    getSettingsPaymentsSquareEnabled(state) {
      return state.settings.payments.square.enabled
    },

    getSettingsIntegrationsGoogleMeetEnabled(state) {
      return state.settings.googleMeet.enabled
    },

    getSettingsIntegrationsMicrosoftTeamsEnabled(state) {
      return state.settings.microsoftTeams.enabled
    },

    getSettingsIntegrationsLessonSpaceEnabled(state) {
      return state.settings.lessonSpace.enabled
    },

    getWaitingListEnabled(state) {
      return state.settings.waitingList.enabled
    },

    getWaitingListMaxCapacity(state) {
      return state.settings.waitingList.maxCapacity
    },

    getWaitingListMaxExtraPeopleEnabled(state) {
      return state.settings.waitingList.maxExtraPeopleEnabled
    },

    getWaitingListMaxExtraPeople(state) {
      return state.settings.waitingList.maxExtraPeople
    },

    getSettings(state) {
      return state.settings
    },

    getActive(state) {
      return state.active
    },
  },

  mutations: {
    setEvent(state, payload = {}) {
      Object.assign(state, getDefaultEvent(), payload)
    },

    setId(state, payload) {
      state.id = payload
    },

    setName(state, payload) {
      state.name = payload
    },

    setLocationId(state, payload) {
      state.locationId = payload
    },

    setCustomLocation(state, payload) {
      state.customLocation = payload
    },

    setOrganizerId(state, payload) {
      state.organizerId = payload
    },

    setZoomUserId(state, payload) {
      state.zoomUserId = payload
    },

    setDescription(state, payload) {
      state.description = payload
    },

    setDescriptionMode(state, payload) {
      state.descriptionMode = payload
    },

    setGallery(state, payload) {
      state.gallery = payload
    },

    setNotifyParticipants(state, payload) {
      state.notifyParticipants = payload
    },

    setParentId(state, payload) {
      state.parentId = payload
    },

    setTranslations(state, payload) {
      state.translations = payload
    },

    setTags(state, payload) {
      state.tags = payload
    },

    setProviders(state, payload) {
      state.providers = payload
    },

    setPeriods(state, payload) {
      state.periods = payload
    },

    setBookingOpensDisabled(state, payload) {
      state.bookingOpens.disabled = payload
    },

    setBookingOpensDate(state, payload) {
      state.bookingOpens.date = payload
    },

    setBookingOpensTime(state, payload) {
      state.bookingOpens.time = payload
    },

    setBookingClosesDisabled(state, payload) {
      state.bookingCloses.disabled = payload
    },

    setBookingClosesDate(state, payload) {
      state.bookingCloses.date = payload
    },

    setBookingClosesTime(state, payload) {
      state.bookingCloses.time = payload
    },

    setRecurringEnabled(state, payload) {
      state.recurringEnabled = payload
    },

    setRecurring(state, payload) {
      state.recurring = payload
    },

    setRecurringCycle(state, payload) {
      state.recurring.cycle = payload
    },

    setRecurringCycleInterval(state, payload) {
      state.recurring.cycleInterval = payload
    },

    setRecurringMonthDate(state, payload) {
      state.recurring.monthDate = payload
    },

    setRecurringMonthlyRepeat(state, payload) {
      state.recurring.monthlyRepeat = payload
    },

    setRecurringMonthlyOnDay(state, payload) {
      state.recurring.monthlyOnDay = payload
    },

    setRecurringMonthlyOnRepeat(state, payload) {
      state.recurring.monthlyOnRepeat = payload
    },

    setRecurringOrder(state, payload) {
      state.recurring.order = payload
    },

    setRecurringUntil(state, payload) {
      state.recurring.until = payload
    },

    setBookingOpensRec(state, payload) {
      state.bookingOpensRec = payload
    },

    setBookingClosesRec(state, payload) {
      state.bookingClosesRec = payload
    },

    setPrice(state, payload) {
      state.price = payload
    },

    setMaxCapacity(state, payload) {
      state.maxCapacity = payload
    },

    setBringingAnyone(state, payload) {
      state.bringingAnyone = payload
    },

    setAggregatedPrice(state, payload) {
      state.aggregatedPrice = payload
    },

    setBookMultipleTimes(state, payload) {
      state.bookMultipleTimes = payload
    },

    setDepositEnabled(state, payload) {
      state.depositEnabled = payload
    },

    setDeposit(state, payload) {
      state.deposit = payload
    },

    setDepositPayment(state, payload) {
      state.depositPayment = payload
    },

    setDepositPerPerson(state, payload) {
      state.depositPerPerson = payload
    },

    setFullPayment(state, payload) {
      state.fullPayment = payload
    },

    setCustomPricing(state, payload) {
      state.customPricing = payload
    },

    setCustomTickets(state, payload) {
      state.customTickets = payload
    },

    setCustomTicketsRangesEnabled(state, payload) {
      state.customTicketsRangesEnabled = payload
    },

    setCustomTicketsRanges(state, payload) {
      state.customTicketsRanges = payload
    },

    setMaxCustomCapacity(state, payload) {
      state.maxCustomCapacity = payload
    },

    setMaxCustomCapacityEnabled(state, payload) {
      state.maxCustomCapacityEnabled = payload
    },

    setCloseAfterMinEnabled(state, payload) {
      state.closeAfterMinEnabled = payload
    },

    setCloseAfterMin(state, payload) {
      state.closeAfterMin = payload
    },

    setCloseAfterMinBookings(state, payload) {
      state.closeAfterMinBookings = payload
    },

    setMaxExtraPeopleEnabled(state, payload) {
      state.maxExtraPeopleEnabled = payload
    },

    setMaxExtraPeople(state, payload) {
      state.maxExtraPeople = payload
    },

    setColor(state, payload) {
      state.color = payload
    },

    setShow(state, payload) {
      state.show = payload
    },

    setSettingsGeneralMinimumTimeRequirementPriorToCanceling(state, payload) {
      state.settings.general.minimumTimeRequirementPriorToCanceling = payload
    },

    setSettingsGeneralRedirectUrlAfterAppointment(state, payload) {
      state.settings.general.redirectUrlAfterAppointment = payload
    },

    setSettingsPaymentsPaymentLinksEnabled(state, payload) {
      state.settings.payments.paymentLinks.enabled = payload
    },

    setSettingsPaymentsPaymentLinksRedirectUrl(state, payload) {
      state.settings.payments.paymentLinks.redirectUrl = payload
    },

    setSettingsPaymentsOnSite(state, payload) {
      state.settings.payments.onSite = payload
    },

    setSettingsPaymentsWcEnabled(state, payload) {
      state.settings.payments.wc.enabled = payload
    },

    setSettingsPaymentsWcProductId(state, payload) {
      state.settings.payments.wc.productId = payload
    },

    setSettingsPaymentsStripeEnabled(state, payload) {
      state.settings.payments.stripe.enabled = payload
    },

    setSettingsPaymentsPayPalEnabled(state, payload) {
      state.settings.payments.payPal.enabled = payload
    },

    setSettingsPaymentsMollieEnabled(state, payload) {
      state.settings.payments.mollie.enabled = payload
    },

    setSettingsPaymentsRazorpayEnabled(state, payload) {
      state.settings.payments.razorpay.enabled = payload
    },

    setSettingsPaymentsBarionEnabled(state, payload) {
      state.settings.payments.barion.enabled = payload
    },

    setSettingsPaymentsSquareEnabled(state, payload) {
      state.settings.payments.square.enabled = payload
    },

    setSettingsIntegrationsGoogleMeetEnabled(state, payload) {
      state.settings.googleMeet.enabled = payload
    },

    setSettingsIntegrationsMicrosoftTeamsEnabled(state, payload) {
      state.settings.microsoftTeams.enabled = payload
    },

    setSettingsIntegrationsLessonSpaceEnabled(state, payload) {
      state.settings.lessonSpace.enabled = payload
    },

    setWaitingListEnabled(state, payload) {
      state.settings.waitingList.enabled = payload
    },

    setWaitingListMaxCapacity(state, payload) {
      state.settings.waitingList.maxCapacity = payload
    },

    setWaitingListMaxExtraPeopleEnabled(state, payload) {
      state.settings.waitingList.maxExtraPeopleEnabled = payload
    },

    setWaitingListMaxExtraPeople(state, payload) {
      state.settings.waitingList.maxExtraPeople = payload
    },

    setSettings(state, payload) {
      state.settings = payload
    },

    setActive(state, payload) {
      state.active = payload
    },
  },
}
