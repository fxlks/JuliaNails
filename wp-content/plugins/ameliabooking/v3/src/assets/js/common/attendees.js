import { reactive } from "vue";
import moment from 'moment'
import {useAmount} from "./pricing";

const globalLabels = reactive(window.wpAmeliaLabels)

function useFrontAttendee(attendee, defaultAttendee = {}) {
  return Object.assign(
    defaultAttendee,
    {
      id: attendee.id,
      eventId: attendee.event.id,
      customer: attendee.customer,
      customerId: attendee.customer.id,
      persons: attendee.persons,
      customFields: attendee.customFields ? JSON.parse(attendee.customFields) : {},
      ticketsData: attendee.ticketsData,
      status: attendee.status,
      tax: attendee.tax,
      price: attendee.price,
      coupon: attendee.coupon,
      payments: attendee.payments,
      qrCodes: attendee.qrCodes,
    }
  )
}

function useBackAttendee(store) {
  let customFields = {}

  Object.keys(store.getters['attendee/getCustomFields']).forEach((id) => {
    customFields[id] = {
      label: store.getters['attendee/getCustomFields'][id].label,
      type: store.getters['attendee/getCustomFields'][id].type,
      value: store.getters['attendee/getCustomFields'][id].type === 'datepicker'
        ? store.getters['attendee/getCustomFields'][id].value
          ? moment(store.getters['attendee/getCustomFields'][id].value).format('YYYY-MM-DD')
          : null
        : store.getters['attendee/getCustomFields'][id].value,
    }
  })

  return {
    id: store.getters['attendee/getId'],
    type: 'event',
    eventId: store.getters['attendee/getEventId'],
    bookings: [
      {
        customerId: store.getters['attendee/getCustomerId'],
        persons: store.getters['attendee/getPersons'],
        customFields: customFields,
        ticketsData: store.getters['attendee/getTickets'],
        status: store.getters['attendee/getStatus'],
        coupon: store.getters['attendee/getCoupon'],
      }
    ],
    status: store.getters['attendee/getStatus'],
    validateCoupon: false,
    isBackendOrCabinet: true,
    createPaymentLinks: false,
    payment: {gateway: 'onSite'},
  }
}

function useInitAttendee(store, attendee, event = null) {
  let customFields = attendee.customFields

  let ticketsData = event && event.customPricing
    ? event.customTickets.map(
      (i, index) => new Object({
        eventTicketId: i.id,
        persons: index === 0 && !Object.keys(attendee.ticketsData).length ? 1 : 0,
        price: i.price,
      })
    )
    : []

  if (event) {
    store.getters['entities/getCustomFields']
      .filter(
        (i) => i.allEvents || (i.events.map((s) => s.id).indexOf(event.id) !== -1 && i.type !== 'content' && i.type !== 'file')
      ).forEach((i) => {
      customFields[i.id] = {
        type: i.type,
        label: i.label,
        value: i.id in customFields
          ? customFields[i.id].value
          : i.type === 'checkbox' || i.type === 'file' ? [] : '',
        width: i.width,
        options: i.options,
        id: i.id,
        required: i.required,
      }
    })

    if (event.customPricing) {
      Object.keys(attendee.ticketsData).forEach((id) => {
        let ticketData = ticketsData.find(i => i.eventTicketId === attendee.ticketsData[id].eventTicketId)

        ticketData.id = attendee.ticketsData[id].id

        ticketData.persons = attendee.ticketsData[id].persons

        ticketData.price = attendee.ticketsData[id].price

        ticketData.customerBookingId = attendee.ticketsData[id].customerBookingId
      })
    }
  }

  return Object.assign(
    {},
    attendee,
    {
      eventId: event ? event.id : null,
      customFields: customFields,
      ticketsData: ticketsData,
    }
  )
}

function useAttendeeStatuses() {
  return [
    {
      label: globalLabels.approved,
      value: 'approved',
      icon: 'check',
      color: 'var(--am-c-success)',
    },
    {
      label: globalLabels.waiting,
      value: 'waiting',
      icon: 'clock',
      color: 'var(--am-c-warning)',
    },
    {
      label: globalLabels.canceled,
      value: 'canceled',
      icon: 'close',
      color: 'var(--am-c-error)',
    },
    {
      label: globalLabels['no-show'],
      value: 'no-show',
      icon: 'no-show',
      color: '#808A90',
    },
  ]
}

function useAttendeeAmount(store) {
  let price = 0

  if (store.getters['attendee/getTickets'].length) {
    store.getters['attendee/getTickets'].forEach(t => {
      if (t.persons) {
        price += store.getters['attendee/getAggregatedPrice'] ? t.price * t.persons : t.price
      }
    })
  } else {
    price = store.getters['attendee/getAggregatedPrice']
      ? store.getters['attendee/getPrice'] * store.getters['attendee/getPersons']
      : store.getters['attendee/getPrice']
  }

  return useAmount(
    null,
    store.getters['attendee/getCoupon'],
    store.getters['attendee/getTax'] && store.getters['attendee/getTax'].length
      ? store.getters['attendee/getTax'][0]
      : null,
    price,
    false
  )
}

export {
  useInitAttendee,
  useFrontAttendee,
  useBackAttendee,
  useAttendeeStatuses,
  useAttendeeAmount,
}
