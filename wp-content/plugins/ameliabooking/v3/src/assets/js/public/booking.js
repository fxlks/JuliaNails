import moment from "moment";
import {useAppointmentsAmountInfo} from "../common/appointments";
import {useCart} from "./cart";
import httpClient from "../../../plugins/axios.js";
import {
  settings,
  locale
} from "../../../plugins/settings.js"
import {
  useUtcValue,
  useLocalValue,
  useUtcValueOffset,
  useStringFromDate
} from "../common/date.js";
import {
  reactive,
  ref,
  computed
} from "vue";
import {
  useCartItem
} from "./cart";
import {
  useServices
} from "../common/appointments";
import {
  usePackageAmount
} from "./package";
import useAction from "./actions";
import {
  useUrlQueryParams
} from "../common/helper";
import {
  useAmount,
  useEntityTax
} from "../common/pricing";

const globalLabels = reactive(window.wpAmeliaLabels)
let errorMessage = ref()

function usePaymentError (store, callback) {
  store.commit('booking/setLoading', false)

  callback()
}

function buildFormData (formData, data, parentKey) {
  if (data && typeof data === 'object' && !(data instanceof Date) && !(data instanceof File)) {
    Object.keys(data).forEach(key => {
      buildFormData(formData, data[key], parentKey ? `${parentKey}[${key}]` : key)
    })
  } else {
    formData.append(parentKey, data !== null ? data : '')
  }
}

function useBookingData (store, formData, mandatoryJson = false, paymentData = {}, recaptcha = null) {
  let bookableType = store.getters['bookableType/getType'] ? store.getters['bookableType/getType'] : store.getters['booking/getBookableType']

  let customFields = {}

  let customerCustomFields = {}

  let bookableTypeCustomFields = bookableType !== 'event' ? store.getters['entities/getCustomFields'] : store.getters['customFields/getFilteredCustomFieldsArray']

  let allCustomFields = bookableTypeCustomFields.reduce((acc, field) => {
    acc[field.id] = field
    return acc
  }, {})

  let availableCustomFields = bookableType !== 'event' ? store.getters['booking/getAvailableCustomFields'] : store.getters['customFields/getCustomFields']

  let attachments = bookableType !== 'event' ? store.getters['booking/getAttachments'] : {}

  if (bookableType !== 'event') {
    for (let id in availableCustomFields) {
      if (availableCustomFields[id].type === 'file' &&
        Object.keys(attachments).length &&
        id in attachments && attachments[id].length
      ) {
        let uploadCustomField = {
          label: availableCustomFields[id].label,
          value: [],
          type: 'file'
        }

        for (let i = 0; i < attachments[id].length; i++) {
          uploadCustomField.value.push({
            name: attachments[id][i].name
          })
        }

        customFields[id] = uploadCustomField
      } else if (availableCustomFields[id].type !== 'content') {
        customFields[id] = availableCustomFields[id]
      }

      if (availableCustomFields[id].type === 'datepicker') {
        customFields[id].value = availableCustomFields[id].value ?
          useStringFromDate(new Date(availableCustomFields[id].value)) : null
      }
    }
  } else {
    for (let key in availableCustomFields) {
      if (availableCustomFields[key].type !== 'content') {
        customFields[availableCustomFields[key].id] = {
          label: availableCustomFields[key].label,
          type: availableCustomFields[key].type,
          value: availableCustomFields[key].value,
          components: availableCustomFields[key].components
        }
      }

      if (availableCustomFields[key].type === 'file') {
        customFields[availableCustomFields[key].id].value = []
        if (availableCustomFields[key].value.length) {
          attachments[availableCustomFields[key].id] = availableCustomFields[key].value
        }
        for (let i = 0; i < availableCustomFields[key].value.length; i++) {
          customFields[availableCustomFields[key].id].value.push({
            name: availableCustomFields[key].value[i].name
          })
        }
      }

      if (availableCustomFields[key].type === 'datepicker') {
        customFields[availableCustomFields[key].id].value = availableCustomFields[key].value ?
          useStringFromDate(new Date(availableCustomFields[key].value)) : null
      }
    }
  }

  for (let key in customFields) {
    if (allCustomFields[key].saveType === "customer") {
      customerCustomFields[key] = customFields[key]
      delete customFields[key]
    }
  }

  let deposit = bookableType !== 'event' ? !store.getters['booking/getPaymentDeposit'] : !store.getters['payment/getPaymentDeposit']

  let componentProps = formData ? {
    state: bookableType !== 'event' ? store.state.booking : useEventsStoreData(store),
    form: formData,
  } : null

  if (formData && 'form' in componentProps && 'sidebar' in componentProps.form) {
    componentProps.form.sidebar.forEach((item) => {
      item.data.forEach((data) => {
        if (data && 'value' in data) {
          data.value = data.value.replace(/"/g, "'")
        }
      })
    })
  }

  if (formData && 'state' in componentProps && 'customFields' in componentProps.state && 'customFields' in componentProps.state.customFields) {
    Object.keys(componentProps.state.customFields.customFields).forEach((key) => {
      if ('options' in componentProps.state.customFields.customFields[key]) {
        delete componentProps.state.customFields.customFields[key].options
      }
    })
  }

  if (formData && 'state' in componentProps && 'tickets' in componentProps.state && 'tickets' in componentProps.state.tickets) {
    componentProps.state.tickets.tickets.forEach((ticket, index) => {
      if ('translations' in ticket) {
        delete componentProps.state.tickets.tickets[index].translations
      }
    })
  }

  const gateway = bookableType !== 'event' ? store.getters['booking/getPaymentGateway'] : store.getters['payment/getPaymentGateway']

  let jsonData = {
    type: bookableType,
    bookings: [
      {
        customFields: customFields,
        deposit: deposit,
        locale: locale,
        utcOffset: null,
        customerId: bookableType !== 'event' ? store.getters['booking/getCustomerId'] : store.getters['customerInfo/getCustomerId'],
        customer: {
          id: bookableType !== 'event' ? store.getters['booking/getCustomerId'] : store.getters['customerInfo/getCustomerId'],
          firstName: bookableType !== 'event' ? store.getters['booking/getCustomerFirstName'] : store.getters['customerInfo/getCustomerFirstName'],
          lastName: bookableType !== 'event' ? store.getters['booking/getCustomerLastName'] : store.getters['customerInfo/getCustomerLastName'],
          email: bookableType !== 'event' ? store.getters['booking/getCustomerEmail'] : store.getters['customerInfo/getCustomerEmail'],
          phone: bookableType !== 'event' ? store.getters['booking/getCustomerPhone'] : store.getters['customerInfo/getCustomerPhone'],
          subscribeToMailchimp: bookableType !== 'event' ? store.getters['booking/getCustomerSubscribe'] : store.getters['customerInfo/getCustomerSubscribe'],
          countryPhoneIso: bookableType !== 'event' ? store.getters['booking/getCustomerCountryPhoneIso'] : store.getters['customerInfo/getCustomerCountryPhoneIso'],
          externalId: bookableType !== 'event' ? store.getters['booking/getCustomerExternalId'] : store.getters['customerInfo/getCustomerExternalId'],
          translations: store.getters['booking/getCustomerTranslations'],
          customFields: customerCustomFields,
        }
      }
    ],
    payment: Object.assign(
      {
        gateway: gateway,
        currency: settings.payments.currencyCode,
      },
      {
        data: paymentData
      }
    ),
    recaptcha: recaptcha,
    locale: locale,
    timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
    urlParams: useUrlQueryParams(window.location.href),
    componentProps: componentProps,
    returnUrl: location.href,
  }

  let coupon = null

  switch (bookableType) {
    case ('appointment'): {
      coupon = store.getters['booking/getCoupon']

      jsonData.couponCode = coupon && (coupon.required || (coupon.discount || coupon.deduction)) ? coupon.code : null

      jsonData.notifyParticipants = 1

      let appointments = useAppointmentBookingData(store)

      jsonData.isCart = useCart(store).length > 1 ? 1 : 0

      jsonData.bookings[0].utcOffset = appointments[0].utcOffset

      jsonData.bookings[0].extras = appointments[0].extras

      jsonData.bookings[0].persons = appointments[0].persons

      jsonData.bookings[0].duration = appointments[0].duration

      jsonData.recurring = appointments.slice(1)

      jsonData.package = []

      if (store.getters['appointmentWaitingListOptions/getIsWaitingListSlot']) {
        jsonData.bookings[0].status = 'waiting'
      }

      jsonData = Object.assign(jsonData, appointments[0])

      break
    }

    case ('package'):
      jsonData = Object.assign(jsonData, usePackageBookingData(store))
      jsonData.bookings[0].extras = []
      jsonData.bookings[0].persons = 1

      break

    case ('event'):
      jsonData = Object.assign(jsonData, {
        eventId: store.getters['eventBooking/getSelectedEventId']
      })

      coupon = store.getters['coupon/getCoupon']

      jsonData = Object.assign(jsonData, {
        couponCode: coupon && (coupon.required || (coupon.discount || coupon.deduction)) ? coupon.code : null,
      })

      jsonData.bookings[0] = Object.assign(jsonData.bookings[0], {
        ticketsData: store.getters['tickets/getTicketsData']
      })

      jsonData.bookings[0].persons = store.getters['persons/getPersons']

      jsonData.bookings[0].utcOffset = settings.general.showClientTimeZone ?
          useUtcValueOffset(store.getters['eventEntities/getEvent'](store.getters['eventBooking/getSelectedEventId'])['periods'][0]['periodStart']) :
          null

      if (store.getters['eventWaitingListOptions/getAvailability']) {
        jsonData.bookings[0].status = 'waiting'
      }

      break
  }

  let bookingData = jsonData
  let requestOptions = {}

  if (
      bookingData.componentProps
      && bookingData.componentProps.state
      && bookableType !== 'event'
  ) {
    bookingData.componentProps.state.appointments.forEach(a => {
      Object.keys(a.services).forEach(s => {
        a.services[s].slots = [];
      })
    })
  }

  const cacheComponentProps = bookingData.componentProps

  if (Object.keys(attachments).length && !mandatoryJson) {
    bookingData = new FormData()

    buildFormData(bookingData, jsonData)

    for (let id in attachments) {
      attachments[id].forEach((item, index) => {
        bookingData.append('files[' + id + '][' + index + ']', item.raw)
      })
    }

    requestOptions = {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }
  }

  if (gateway === 'mollie' || gateway === 'wc' || gateway === 'barion') {
    sessionStorage.setItem("ameliaCacheData", JSON.stringify({request: cacheComponentProps, paymentMethod: gateway, type: bookableType}));
  }

  return {
    data: bookingData,
    options: requestOptions
  }
}

function useEventsStoreData (store) {
  return {
    eventId: store.getters['eventBooking/getSelectedEventId'],
    bookableType: store.getters['bookableType/getType'],
    coupon: store.getters['coupon/getCoupon'],
    customerInfo: store.getters['customerInfo/getAllData'],
    customFields: store.getters['customFields/getAllData'],
    payment: store.getters['payment/getAllData'],
    tickets: store.getters['tickets/getAllData'],
    persons: store.getters['persons/getAllData'],
    pagination: store.getters['pagination/getAllData'],
    params: store.getters['params/getAllData'],
    eventsDisplay: store.getters['eventEntities/getEventsDisplay']
  }
}

function useAppointmentBookingData (store) {
  let appointments = []

  useCart(store).forEach((item) => {
    if (item.serviceId && (item.serviceId in item.services)) {
      item.services[item.serviceId].list.forEach((appointment) => {
        let bookingStart = appointment.date ? (appointment.date + ' ' + appointment.time) : null

        appointments.push(
          {
            serviceId: item.serviceId,
            providerId: appointment.providerId,
            locationId: appointment.locationId,
            bookingStart: settings.general.showClientTimeZone ? useUtcValue(bookingStart) : bookingStart,
            utcOffset: settings.general.showClientTimeZone ? useUtcValueOffset(bookingStart) : null,
            extras: appointment.extras,
            persons: appointment.persons,
            duration: appointment.duration,
          }
        )
      })
    }
  })

  return appointments
}

function usePackageBookingData (store) {
  let appointments = []

  let rules = []

  let cartItem = useCartItem(store)

  let activeItemServices = cartItem.services

  for (let serviceId in activeItemServices) {
    activeItemServices[serviceId].list.forEach((appointment) => {
      if (appointment.date && appointment.time) {
        let utcOffset = null

        let bookingStart = appointment.date + ' ' + appointment.time

        if (settings.general.showClientTimeZone) {
          bookingStart = useUtcValue(bookingStart)

          utcOffset = useUtcValueOffset(bookingStart)
        }

        appointments.push({
          bookingStart: bookingStart,
          serviceId: parseInt(serviceId),
          providerId: appointment.providerId,
          locationId: appointment.locationId,
          utcOffset: utcOffset,
          notifyParticipants: 1,
        })
      }
    })

    rules.push({
      serviceId: parseInt(serviceId),
      providerId: activeItemServices[serviceId].providerId ? activeItemServices[serviceId].providerId : null,
      locationId: activeItemServices[serviceId].locationId ? activeItemServices[serviceId].locationId : null
    })
  }

  let coupon = store.getters['booking/getCoupon']

  return {
    package: appointments,
    packageId: store.getters['booking/getPackageId'],
    packageRules: rules,
    utcOffset: useUtcValueOffset(null),
    deposit: !store.getters['booking/getPaymentDeposit'],
    couponCode: coupon && (coupon.required || (coupon.discount || coupon.deduction)) ? coupon.code : null
  }
}

function createBooking (store, requestData, successCallback, errorCallback) {
  httpClient.post(
    '/bookings',
    requestData.data,
    requestData.options
  ).then(successCallback).catch(errorCallback)
}

function useCreateBooking (store, requestData, successCallback, errorCallback) {
  let bookableType = store.getters['bookableType/getType'] ? store.getters['bookableType/getType'] : store.getters['booking/getBookableType']
  useAction(
    store,
    useBookingData(
      store,
      null,
      true,
      {},
      null
    ).data,
    'beforeBooking',
    bookableType,
    () => {
      createBooking(store, requestData, successCallback, errorCallback)
    },
    message => {
      errorCallback({response:{data:{data:{message: message}}}})
    }
  )
}

function useBookingError (response, store) {
  let bookableType = store.getters['bookableType/getType'] ? store.getters['bookableType/getType'] : store.getters['booking/getBookableType']
  let message = globalLabels['payment_error_default']

  if ('onSitePayment' in response.data && response.data.onSitePayment === true) {
    store.commit('payment/setOnSitePayment', true)
  }

  if ('data' in response) {
    if ('customerAlreadyBooked' in response.data && response.data.customerAlreadyBooked === true) {
      message = bookableType === 'event' ? globalLabels['customer_already_booked_ev'] : globalLabels['customer_already_booked_app']
    } else if ('timeSlotUnavailable' in response.data && response.data.timeSlotUnavailable === true) {
      message = bookableType === 'event' ? globalLabels['maximum_capacity_reached'] : globalLabels['time_slot_unavailable']
    }  else if ('customerBlocked' in response.data && response.data.customerBlocked === true) {
      message = globalLabels['customer_blocked']
    } else if ('bookingsLimitReached' in response.data && response.data.bookingsLimitReached === true) {
      message = globalLabels['bookings_limit_reached']
    } else if ('eventBookingUnavailable' in response.data && response.data.eventBookingUnavailable === true) {
      message = globalLabels['event_booking_unavailable']
    } else if ('emailError' in response.data && response.data.emailError === true) {
      message = globalLabels['email_exist_error']
    } else if ('phoneError' in response.data && response.data.phoneError === true) {
      message = globalLabels['phone_exist_error']
    } else if ('couponUnknown' in response.data && response.data.couponUnknown === true) {
      message = globalLabels['coupon_unknown']
    } else if ('couponInvalid' in response.data && response.data.couponInvalid === true) {
      message = globalLabels['coupon_invalid']
    } else if ('couponExpired' in response.data && response.data.couponExpired === true) {
      message = globalLabels['coupon_expired']
    } else if ('couponMissing' in response.data && response.data.couponMissing === true) {
      message = globalLabels['coupon_missing']
    } else if ('emailRequired' in response.data && response.data.emailRequired === true) {
      message = globalLabels['email_required']
    } else if ('paymentSuccessful' in response.data && response.data.paymentSuccessful === false) {
      if (response.data.message) {
        message = response.data.message
      } else {
        message = globalLabels['payment_error']
      }
    } else if ('bookingAlreadyInWcCart' in response.data && response.data.bookingAlreadyInWcCart === true) {
      message = globalLabels['booking_already_in_wc_cart']
    } else if ('wcError' in response.data && response.data.wcError === true) {
      message = globalLabels['wc_error']
    } else if ('recaptchaError' in response.data && response.data.recaptchaError === true) {
      message = globalLabels['recaptcha_invalid_error']
    } else if ('packageBookingUnavailable' in response.data && response.data.packageBookingUnavailable === true) {
      message = globalLabels['package_booking_unavailable']
    } else if ('message' in response.data) {
      message = response.data.message
    }
  }

  return message
}

function useNotify (store, response, success, error) {
  if (!settings.general.runInstantPostBookingActions) {
    let request = {}

    switch (response.type) {
      case ('appointment'):
        request = getAppointmentNotifyData(store, response)

        break

      case('package'):
        request = getPackageNotifyData(response)

      break

      case ('event'):
        request = getEventNotifyData(response)

        break
    }

    httpClient.post(
        '/bookings/success/' + request.id + '&nocache=' + (new Date().getTime()),
        request.data,
        {}
    ).then(success).catch(error)
  }
}

function getAppointmentNotifyData (store, response) {
  let recurringData = []

  response.recurring.forEach((recurring) => {
    recurringData.push(
      {
        type: 'appointment',
        id: recurring.booking.id,
        appointmentStatusChanged: recurring.appointmentStatusChanged
      }
    )
  })

  return {
    id: response.booking.id,
    data: {
      type: useCart(store).length > 1 ? 'cart' : 'appointment',
      appointmentStatusChanged: response.appointmentStatusChanged,
      recurring: recurringData,
      packageId: null,
      customer: response.customer,
      paymentId: 'paymentId' in response && response.paymentId ? response.paymentId : null,
      packageCustomerId: null,
      isPackageAppointment: response.isPackageAppointment
    }
  }
}

function getPackageNotifyData (response) {
  let bookings  = []
  let bookingId = 0
  response.package.forEach((packData, index) => {
    if (index > 0) {
      bookings.push(
          {
            type: 'appointment',
            id: packData.booking.id,
            appointmentStatusChanged: packData.appointmentStatusChanged
          }
      )
    } else {
      bookingId = packData.booking.id
    }
  })

  return {
    id: bookingId,
    data: {
      type: 'package',
      appointmentStatusChanged: response.appointmentStatusChanged,
      recurring: bookings,
      packageId: response.packageId,
      customer: response.customer,
      paymentId: 'paymentId' in response && response.paymentId ? response.paymentId : null,
      packageCustomerId: response.packageCustomerId,
      isPackageAppointment: true
    }
  }
}

function getEventNotifyData (response) {
  return {
    id: response.booking.id,
    data: {
      type: 'event',
      appointmentStatusChanged: response.appointmentStatusChanged,
      paymentId: 'paymentId' in response && response.paymentId ? response.paymentId : null
    }
  }
}

function getSingleAppointmentData (store, date, data) {
  let services = useServices(store)

  let bookable = services.find(i => i.id === data.appointment.serviceId)

  return {
    appointmentId: data.appointment ? data.appointment.id : '',
    bookings: [data.booking],
    bookingId: data.booking.id,
    serviceId: data.appointment.serviceId,
    providerId: data.appointment.providerId,
    locationId: data.appointment.locationId,
    title: bookable.name,
    description: bookable.description,
    start: settings.general.showClientTimeZone ? useLocalValue(date.start) : data.appointment.bookingStart,
    end: settings.general.showClientTimeZone ? useLocalValue(date.end) : data.appointment.bookingEnd,
    utcStart: moment.utc(date.start.replace(/ /g, 'T')).toDate(),
    utcEnd: moment.utc(date.end.replace(/ /g, 'T')).toDate(),
    cfAddress: getAddressFromCF(store)
  }
}

function runAction (store, response) {
  useAction(
    store,
    {
      appointmentId: response.appointment ? response.appointment.id : null,
      payment: Object.assign(response.payment, {currency: settings.payments.currencyCode}),
      ...(response.isCart && {
        providerId: response.appointment ? response.appointment.providerId : null,
        locationId: response.appointment ? response.appointment.locationId : null,
        serviceId: response.appointment ? response.appointment.serviceId : null,
        isCartAppointment: response.isCart
      })
    },
    response.payment.gateway === 'onSite' ? 'Schedule' : 'Purchase',
    response.type,
    null,
    null
  )

  if (response.recurring.length) {
    response.recurring.forEach((item) => {
      useAction(
        store,
        {
          appointmentId: item.appointment ? item.appointment.id : null,
          payment: Object.assign(item.booking.payments[0], {currency: settings.payments.currencyCode}),
          ...(response.isCart && {
            providerId: item.appointment ? item.appointment.providerId : null,
            locationId: item.appointment ? item.appointment.locationId : null,
            serviceId: item.appointment ? item.appointment.serviceId : null,
            isCartAppointment: true
          })
        },
        item.booking.payments[0].gateway === 'onSite' ? 'Schedule' : 'Purchase',
        item.type,
        null,
        null
      )
    })
  }
}

function getAddressFromCF (store) {
  let bookingCustomFields = store.getters['booking/getAvailableCustomFields']
  if (bookingCustomFields) {
    for (let i = 0; i < Object.values(bookingCustomFields).length; i++) {
      let cf = Object.values(bookingCustomFields)[i]
      let cfId = Object.keys(bookingCustomFields)[i]
      let customField = store.getters['entities/getCustomField'](cfId)
      if (cf.value && customField && customField.type === 'address' && customField.useAsLocation) {
        return cf.value
      }
    }
  }
  return null
}

function useAppointmentCalendarData (store, response) {
  let appointments = []

  let payments = []
  let paymentAmount = 0

  response.utcTime.forEach((date) => {
    appointments.push(getSingleAppointmentData(store, date, response))

    payments.push(response.payment)

    paymentAmount = response.booking.payments[0].amount
  })

  response.recurring.forEach((data) => {
    data.utcTime.forEach((date) => {
      appointments.push(getSingleAppointmentData(store, date, data))
    })

    if (useCart(store).length > 1 || response.bookable.recurringPayment > 0) {
      payments.push(data.booking.payments[0])

      paymentAmount += data.booking.payments[0].amount
    }
  })

  runAction(store, response)

  let amountInfo = useAppointmentsAmountInfo(store)

  let totalAmount = 0

  amountInfo.forEach((item) => {
    totalAmount += item.prepaid.totalAmount + item.postpaid.totalAmount -
      item.prepaid.discountAmount - item.postpaid.discountAmount +
      item.prepaid.taxAmount + item.postpaid.taxAmount
  })

  return {
    type: 'appointment',
    data: appointments,
    token: response.booking.token,
    payments: payments,
    paymentAmount: paymentAmount,
    price: totalAmount,
    customerCabinetUrl: response.customerCabinetUrl,
  }
}

function usePackageCalendarData (store, response) {
  let token = ''

  let appointments = []

  response.package.forEach((data, index) => {
    if (index === 0) {
      token = data.booking.token
    }

    data.utcTime.forEach((date) => {
      appointments.push(getSingleAppointmentData(store, date, data))
    })
  })

  runAction(store, response)

  let pack = store.getters['entities/getPackage'](
    store.getters['booking/getPackageId']
  )

  let tax = useEntityTax(store, store.getters['booking/getPackageId'], 'package')

  let amountData = useAmount(
      pack,
      store.getters['booking/getCoupon'],
      tax && settings.payments.taxes.enabled ? Object.assign({}, tax, {excluded: settings.payments.taxes.excluded}) : null,
      usePackageAmount(pack),
      false
  )

  return {
    type: 'package',
    data: appointments,
    token: token,
    payments: [response.payment],
    paymentAmount: response.payment.amount,
    payment: response.payment,
    price: amountData.deposit && response.payment.gateway !== 'onSite' ? amountData.deposit : amountData.price - amountData.discount + amountData.tax,
    customerCabinetUrl: response.customerCabinetUrl,
  }
}

function useEventCalendarData (store, response) {
  let locationAddress = ''

  if (response.event.location) {
    locationAddress = response.event.location.address
  } else if (response.event.customLocation) {
    locationAddress = response.event.customLocation
  }

  let data = []

  let responsePeriods = settings.general.showClientTimeZone ? response.utcTime : response.event.periods

  let periodsArr = []
  responsePeriods.forEach(function (date) {
    let keyStart =  settings.general.showClientTimeZone ? 'start' : 'periodStart'
    let keyEnd =  settings.general.showClientTimeZone ? 'end' : 'periodEnd'
    let periodStartDate = moment(date[keyStart].split(' ')[0], 'YYYY-MM-DD')
    let periodEndDate = moment(date[keyEnd].split(' ')[0], 'YYYY-MM-DD')
    let periodStartTime = moment(date[keyStart].split(' ')[1], 'HH:mm:ss').format('HH:mm:ss')
    let periodEndTime = moment(date[keyEnd].split(' ')[1], 'HH:mm:ss').format('HH:mm:ss')

    if (periodEndTime === '00:00:00') {
      periodEndTime = '24:00:00'
      periodEndDate.subtract(1, 'days')
    }

    /** if the period in the event lasts for several days */
    if (periodStartDate.diff(periodEndDate, 'days') < 0) {
      let periodDates = []

      while (periodStartDate.isSameOrBefore(periodEndDate)) {
        periodDates.push(periodStartDate.format('YYYY-MM-DD'))
        periodStartDate.add(1, 'days')
      }

      periodDates.forEach(dayPeriod => {
        periodsArr.push({
          start: settings.general.showClientTimeZone ? useLocalValue(dayPeriod + ' ' + periodStartTime) : dayPeriod + ' ' + periodStartTime,
          end: settings.general.showClientTimeZone ? useLocalValue(dayPeriod + ' ' + periodEndTime) : dayPeriod + ' ' + periodEndTime
        })
      })
    } else {
      periodsArr.push({
        start: settings.general.showClientTimeZone ? useLocalValue(periodStartDate.format('YYYY-MM-DD') + ' ' + periodStartTime) : periodStartDate.format('YYYY-MM-DD') + ' ' + periodStartTime,
        end: settings.general.showClientTimeZone ? useLocalValue(periodEndDate.format('YYYY-MM-DD') + ' ' + periodEndTime) : periodEndDate.format('YYYY-MM-DD') + ' ' + periodEndTime
      })
    }
  })

  periodsArr.sort((a, b) => moment(a.start, 'YYYY-MM-DD HH:mm:ss') - moment(b.start, 'YYYY-MM-DD HH:mm:ss'))
    .forEach(item => {
      data.push(
        {
          eventId: response.event.id,
          bookingId: response.booking.id,
          locationId: response.event.locationId,
          organizerId: response.event.organizerId,
          title: response.event.name,
          description: response.event.description,
          start: item.start,
          end: item.end,
          utcStart: moment.utc(item.start.replace(/ /g, 'T')).toDate(),
          utcEnd: moment.utc(item.end.replace(/ /g, 'T')).toDate(),
          cfAddress: getAddressFromCF(store)
        }
      )
    })

  let ticketsData = []

  response.booking.ticketsData.forEach(ticket => {
    if (ticket.persons) {
      let ticketInEvent = response.event.customTickets.find(t => t.id === ticket.eventTicketId)
      ticket.name = ticketInEvent.name

      ticketsData.push(ticket)
    }
  })

  let amSettings = computed(() => store.getters['getSettings'])
  let afterBookingUrl = computed(() => {
    let entitySettings = response.event.settings ? JSON.parse(response.event.settings) : amSettings.value
    let url = ''
    if (
      'general' in entitySettings
      && 'redirectUrlAfterAppointment' in entitySettings.general
      && entitySettings.general.redirectUrlAfterAppointment
    ) {
      url = entitySettings.general.redirectUrlAfterAppointment
    } else if (amSettings.value.general.redirectUrlAfterAppointment) {
      url = amSettings.value.general.redirectUrlAfterAppointment
    }

    return url
  })
  let customerPanelUrl = computed(() => {
    let entitySettings = response.event.settings ? JSON.parse(response.event.settings) : amSettings.value
    let url = ''
    if (
      'roles' in entitySettings
      && 'customerCabinet' in entitySettings.roles
      && 'pageUrl' in entitySettings.roles.customerCabinet
      && entitySettings.roles.customerCabinet.pageUrl
    ) {
      url = entitySettings.roles.customerCabinet.pageUrl
    } else if (amSettings.value.roles.customerCabinet.pageUrl) {
      url = amSettings.value.roles.customerCabinet.pageUrl
    }

    return url
  })

  runAction(store, response)

  let event = response.event

  let price = 0

  if (event.customPricing) {
    response.booking.ticketsData.forEach(t => {
      if (t.persons) {
        price += event.aggregatedPrice ? t.price * t.persons : t.price
      }
    })
  } else {
    price = event.aggregatedPrice ? event.price * response.booking.persons : event.price
  }

  let tax = useEntityTax(store, store.getters['eventBooking/getSelectedEventId'], 'event')

  let amountData = useAmount(
      event,
      store.getters['coupon/getCoupon'],
      settings.payments.taxes.enabled ? Object.assign({}, tax, {excluded: settings.payments.taxes.excluded}) : null,
      price,
      false
  )

  return {
    type: response.type,
    active: settings.general.addToCalendar,
    data: data,
    event: response.event,
    bringingAnyone: response.event.bringingAnyone,
    customPricing: response.event.customPricing,
    ticketsData: ticketsData,
    address: locationAddress,
    token: response.booking.token,
    persons: response.booking.persons,
    payments: [response.payment],
    paymentAmount: response.payment.amount,
    price: amountData.deposit && response.payment.gateway !== 'onSite' ? amountData.deposit : amountData.price - amountData.discount + amountData.tax,
    redirectAfterBookingUrl: afterBookingUrl.value,
    customerCabinetUrl: customerPanelUrl.value,
  }
}

function useCreateBookingSuccess (store, response, callback) {
  if (response.data.data) {
    let bookableType = store.getters['bookableType/getType'] ? store.getters['bookableType/getType'] : store.getters['booking/getBookableType']
    store.commit((bookableType === 'event' ? 'setLoading' : 'booking/setLoading'), false)

    switch (bookableType) {
      case ('appointment'):
        store.commit('booking/setBooked', useAppointmentCalendarData(store, response.data.data))

        break

      case ('package'):
        store.commit('booking/setBooked', usePackageCalendarData(store, response.data.data))

        break

      case ('event'):
        store.commit('eventBooking/setBooked', useEventCalendarData(store, response.data.data))

        break
    }

    useNotify(store, response.data.data, () => {}, () => {})
  }

  if (typeof callback !== 'undefined') {
    callback()
  }

}

function useCreateBookingError (store, response, callback) {
  if ('data' in response) {
    errorMessage.value = useBookingError(response, store)
  }

  if (store.getters['bookableType/getType'] === 'event') {
    store.commit('setLoading', false)
  } else {
    store.commit('booking/setLoading', false)
  }

  if (typeof callback !== 'undefined') {
    callback()
  }
}

function getErrorMessage () {
  return errorMessage.value
}

export {
  usePaymentError,
  useBookingData,
  useCreateBooking,
  useCreateBookingSuccess,
  useCreateBookingError,
  useAppointmentCalendarData,
  usePackageCalendarData,
  useEventCalendarData,
  getErrorMessage,
  useBookingError,
  useNotify,
  useAppointmentBookingData,
  usePackageBookingData
}
