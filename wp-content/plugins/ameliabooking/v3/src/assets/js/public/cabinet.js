import { reactive } from "vue";
import moment from "moment";
import {settings} from "../../../plugins/settings";
import httpClient from "../../../plugins/axios";
import {
  useAppointmentBookingAmountData,
} from "../common/appointments";
import { usePackageBookingPrice } from "../admin/package";
import {useEventBookingsPrice} from "../admin/event";

const globalLabels = reactive(window.wpAmeliaLabels)

function usePaymentLink (store, method, reservation) {
  if (reservation.type === 'package') store.commit('cabinet/setPackageLoading', true)
  if (reservation.type === 'appointment') store.commit('cabinet/setAppointmentsLoading', true)
  if (reservation.type === 'event') store.commit('cabinet/setEventsLoading', true)

  store.commit('cabinet/setPaymentLinkLoader', reservation.bookings ? reservation.bookings[0].id : reservation.id)

  let appointmentData = JSON.parse(JSON.stringify(reservation))

  let customer = JSON.parse(JSON.stringify(store.getters['auth/getProfile']))

  customer.birthday = null

  appointmentData['customer'] = customer

  if (appointmentData['type'] !== 'package') {
    appointmentData[appointmentData['type']] = reservation

    appointmentData['booking'] = reservation.bookings[0]

    appointmentData['paymentId'] = reservation.bookings[0].payments[0].id
  } else {
    appointmentData = Object.assign(appointmentData, appointmentData.package)
  }

  httpClient.post(
    '/payments/link',
    {
      data: appointmentData,
      paymentMethod: method
    }).then((response) => {
      if (!response.data.data.error && response.data.data.paymentLink) {
        window.location.href = response.data.data.paymentLink
      } else {
        store.commit('cabinet/setPaymentLinkError', {value: true, type: reservation.type})
      }
    }).catch(error => {
      store.commit('cabinet/setPaymentLinkError', {value: true, type: reservation.type})
    }).finally(() => {
      store.commit('cabinet/setPaymentLinkLoader', null)
      if (reservation.type === 'package') store.commit('cabinet/setPackageLoading', false)
      if (reservation.type === 'appointment') store.commit('cabinet/setAppointmentsLoading', false)
      if (reservation.type === 'event') store.commit('cabinet/setEventsLoading', false)
    })
}

function usePaymentFromCustomerPanel (reservation, entitySettings) {
  if (reservation.type !== 'package' && (!reservation.bookings || reservation.bookings.length === 0)) {
    return false
  }

  if (typeof entitySettings === 'string') {
    entitySettings = JSON.parse(entitySettings)
  } else if (!entitySettings) {
    entitySettings = {}
  }

  let paymentLinksEnabled = entitySettings
  && 'payments' in entitySettings
  && entitySettings.payments
  && 'paymentLinks' in entitySettings.payments
  && entitySettings.payments.paymentLinks
  && 'enabled' in entitySettings.payments.paymentLinks
    ? entitySettings.payments.paymentLinks
    : settings.payments.paymentLinks

  return usePaymentMethods(settings).length &&
    settings &&
    paymentLinksEnabled &&
    paymentLinksEnabled.enabled
}

function usePaymentMethods (entitySettings) {
  if (typeof entitySettings === 'string') {
    entitySettings = JSON.parse(entitySettings)
  } else if (!entitySettings) {
    entitySettings = {}
  }

  let paymentOptions = []
  entitySettings = entitySettings.payments || null

  if (settings.payments.wc.enabled) {
    paymentOptions.push({
      value: 'wc',
      label: globalLabels.wc
    })
  } else if (settings.payments.mollie.enabled && (!entitySettings || !('mollie' in entitySettings) || entitySettings.mollie.enabled)) {
    paymentOptions.push({
      value: 'mollie',
      label: globalLabels.on_line
    })
  } else {
    if (settings.payments.payPal.enabled && (!entitySettings || !('payPal' in entitySettings) || entitySettings.payPal.enabled)) {
      paymentOptions.push({
        value: 'payPal',
        label: globalLabels.pay_pal
      })
    }

    if (settings.payments.stripe.enabled && (!entitySettings || !('stripe' in entitySettings) || entitySettings.stripe.enabled)) {
      paymentOptions.push({
        value: 'stripe',
        label: globalLabels.credit_card
      })
    }

    if (settings.payments.razorpay.enabled && (!entitySettings || !('razorpay' in entitySettings) || entitySettings.razorpay.enabled)) {
      paymentOptions.push({
        value: 'razorpay',
        label: globalLabels.razorpay
      })
    }

    if (settings.payments.square.enabled && (!entitySettings || !('square' in entitySettings) || entitySettings.square.enabled)) {
      paymentOptions.push({
        value: 'square',
        label: globalLabels.square
      })
    }

    if (settings.payments.barion.enabled && (!entitySettings || !('barion' in entitySettings) || entitySettings.barion.enabled)) {
      paymentOptions.push({
        value: 'barion',
        label: globalLabels.barion
      })
    }
  }

  return paymentOptions
}

function usePayable (store, reservation) {
  switch (reservation.type) {
    case ('appointment'): {
      let amountData = useAppointmentBookingAmountData(
        store,
        {
          price: reservation.bookings[0].price,
          persons: reservation.bookings[0].persons,
          aggregatedPrice: reservation.bookings[0].aggregatedPrice,
          extras: reservation.bookings[0].extras,
          serviceId: null,
          tax: reservation.bookings[0].tax,
          coupon: reservation.bookings[0].coupon
        },
        false
      )

      return amountData.total - amountData.discount + amountData.tax > reservation.bookings[0].payments.filter(p => p.status !== 'refunded' && p.status !== 'pending').reduce((partialSum, a) => partialSum + a.amount, 0)
    }

    case ('event'):
      return useEventBookingsPrice(reservation) > reservation.bookings[0].payments.filter(p => p.status !== 'refunded' && p.status !== 'pending').reduce((partialSum, a) => partialSum + a.amount, 0)
    case ('package'):
      return usePackageBookingPrice(reservation) > reservation.payments.filter(p => p.status !== 'refunded' && p.status !== 'pending').reduce((partialSum, a) => partialSum + a.amount, 0)
  }
}

export {
  usePayable,
  usePaymentMethods,
  usePaymentFromCustomerPanel,
  usePaymentLink,
}
