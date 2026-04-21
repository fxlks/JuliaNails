import moment from "moment";
import {settings} from "../../../plugins/settings";
import {usePopulateSettings} from "./settings";

function useFrontEvent (store, event) {
  let defaultEvent = store.getters['event/getDefaultEvent']

  let customTicketsRanges = []

  let bookedTickets = {}

  if (event.customTickets.length) {
    event.bookings.forEach((booking) => {
      booking.ticketsData.forEach((ticket) => {
        bookedTickets[ticket.eventTicketId] = true
      })
    })
  }

  event.customTickets.forEach((customTicket) => {
    JSON.parse(customTicket.dateRanges).forEach((customTicketRange, index) => {
      if (!(index in customTicketsRanges)) {
        customTicketsRanges.push({
          enabled: customTicketRange.enabled,
          range: [
            moment(customTicketRange.startDate, 'YYYY-MM-DD').toDate(),
            moment(customTicketRange.endDate, 'YYYY-MM-DD').toDate()
          ],
          tickets: [],
        })
      }

      customTicketsRanges[index].tickets.push({price: customTicketRange.price})
    })

    customTicket.booked = customTicket.id in bookedTickets
  })

  let descriptionMode = !event.description || event.description.startsWith('<!-- Content -->') ? 'text' : 'html'

  let description = event.description ? event.description.replace('<!-- Content -->', '') : ''
  
  return Object.assign(
    {},
    event,
    {
      description: description,
      descriptionMode: descriptionMode,
      tags: event.tags.map(i => i.name),
      organizerId: event.organizerId,
      periods: event.periods.map(i => new Object({
        id: i.id,
        eventId: i.eventId,
        appleCalendarEventId: i.appleCalendarEventId,
        googleCalendarEventId: i.googleCalendarEventId,
        googleMeetUrl: i.googleMeetUrl,
        outlookCalendarEventId: i.outlookCalendarEventId,
        startDate: moment(i.periodStart.split(' ')[0], 'YYYY-MM-DD').toDate(),
        endDate: moment(i.periodEnd.split(' ')[0], 'YYYY-MM-DD').toDate(),
        startTime: i.periodStart.split(' ')[1].substring(0, 5),
        endTime: i.periodEnd.split(' ')[1].substring(0, 5),
      })),
      bookingOpens: {
        disabled: event.bookingOpens === null,
        date: event.bookingOpens
          ? moment(event.bookingOpens.split(' ')[0], 'YYYY-MM-DD').toDate()
          : null,
        time: event.bookingOpens
          ? event.bookingOpens.split(' ')[1].substring(0, 5)
          : null,
      },
      bookingOpensRec: event.bookingOpensRec === 'same',
      bookingCloses: {
        disabled: event.bookingCloses === null,
        date: event.bookingCloses
          ? moment(event.bookingCloses.split(' ')[0], 'YYYY-MM-DD').toDate()
          : null,
        time: event.bookingCloses
          ? event.bookingCloses.split(' ')[1].substring(0, 5)
          : null,
      },
      bookingClosesRec: event.bookingClosesRec === 'same',
      recurringEnabled: event.recurring && event.recurring.cycle !== null,
      recurring: event.recurring !== null
        ? Object.assign(
          {},
          event.recurring,
          {
            until: event.recurring.until
              ? moment(event.recurring.until.split(' ')[0], 'YYYY-MM-DD').toDate()
              : null,
            monthDate: event.recurring.monthDate
              ? moment(event.recurring.monthDate.split(' ')[0], 'YYYY-MM-DD').toDate()
              : null,
          }
        )
        : defaultEvent.recurring,
      depositEnabled: event.depositPayment !== 'disabled',
      depositPayment: event.depositPayment !== 'disabled' ? event.depositPayment : 'percentage',
      customTicketsRangesEnabled: customTicketsRanges.length > 0,
      customTicketsRanges: customTicketsRanges,
      maxCustomCapacity: event.maxCustomCapacity === null ? 1 : event.maxCustomCapacity,
      maxCustomCapacityEnabled: event.maxCustomCapacity !== null,
      closeAfterMinEnabled: event.closeAfterMin !== null,
      closeAfterMinBookings: event.closeAfterMinBookings,
      closeAfterMin: event.closeAfterMin === null ? 1 : event.closeAfterMin,
      maxExtraPeopleEnabled: event.maxExtraPeople !== null,
      maxExtraPeople: event.maxExtraPeople === null ? 0 : event.maxExtraPeople,
      locationId: event.locationId === null && event.customLocation ? 0 : event.locationId,
      settings: usePopulateSettings(
        settings,
        defaultEvent.settings,
        event.settings ? JSON.parse(event.settings) : {},
        {}
      ),
    }
  )
}

function useBackEvent (store) {
  let defaultEvent = store.getters['event/getDefaultEvent']

  let customTickets = []

  store.getters['event/getCustomTickets'].forEach((customTicket, index) => {
    let dateRanges = []

    store.getters['event/getCustomTicketsRanges'].filter(i => i.range[0] && i.range[1]).forEach((customTicketRange) => {
      dateRanges.push({
        startDate: moment(customTicketRange.range[0]).format('YYYY-MM-DD'),
        endDate: moment(customTicketRange.range[1]).format('YYYY-MM-DD'),
        price: customTicketRange.tickets[index].price,
      })
    })

    customTickets.push(Object.assign({}, customTicket, {dateRanges: JSON.stringify(dateRanges)}))
  })

  let eventSettings = usePopulateSettings(
    usePopulateSettings(settings, defaultEvent.settings, {}, {}),
    defaultEvent.settings,
    store.getters['event/getSettings'],
    {}
  )

  Object.keys(store.getters['event/getSettings']).forEach((groupKey) => {
    Object.keys(store.getters['event/getSettings'][groupKey]).forEach((subKey) => {
      if (store.getters['event/getSettings'][groupKey][subKey] !== null &&
        typeof store.getters['event/getSettings'][groupKey][subKey] === 'object'
      ) {
        Object.keys(store.getters['event/getSettings'][groupKey][subKey]).forEach((key) => {
          if (store.getters['event/getSettings'][groupKey][subKey][key] === settings[groupKey][subKey][key] ||
            (
              settings[groupKey][subKey][key] &&
              (
                store.getters['event/getSettings'][groupKey][subKey][key] === '' ||
                store.getters['event/getSettings'][groupKey][subKey][key] === null
              )
            )
          ) {
            delete eventSettings[groupKey][subKey][key]
          }
        })

        if (typeof eventSettings[groupKey][subKey] === 'object' &&
          Object.keys(eventSettings[groupKey][subKey]).length === 0
        ) {
          delete eventSettings[groupKey][subKey]
        }
      } else {
        if (groupKey in settings &&
          subKey in settings[groupKey] &&
          (
            eventSettings[groupKey][subKey] === settings[groupKey][subKey] ||
            (
              settings[groupKey][subKey] &&
              (
                store.getters['event/getSettings'][groupKey][subKey] === '' ||
                store.getters['event/getSettings'][groupKey][subKey] === null
              )
            )
          )
        ) {
          delete eventSettings[groupKey][subKey]
        }
      }
    })

    if (Object.keys(eventSettings[groupKey]).length === 0) {
      delete eventSettings[groupKey]
    }
  })

  return {
    aggregatedPrice: store.getters['event/getAggregatedPrice'],
    bookMultipleTimes: store.getters['event/getBookMultipleTimes'],
    bookingCloses:
      !store.getters['event/getBookingClosesDisabled'] &&
      store.getters['event/getBookingClosesDate'] &&
      store.getters['event/getBookingClosesTime']
        ? moment(store.getters['event/getBookingClosesDate']).format('YYYY-MM-DD') + ' ' + store.getters['event/getBookingClosesTime'] + ':00'
        : null,
    bookingClosesRec: store.getters['event/getBookingClosesRec'] ? 'same' : 'calculate',
    bookingOpens:
      !store.getters['event/getBookingOpensDisabled'] &&
      store.getters['event/getBookingOpensDate'] &&
      store.getters['event/getBookingOpensTime']
        ? moment(store.getters['event/getBookingOpensDate']).format('YYYY-MM-DD') + ' ' + store.getters['event/getBookingOpensTime'] + ':00'
        : null,
    bookingOpensRec: store.getters['event/getBookingOpensRec'] ? 'same' : 'calculate',
    bringingAnyone: store.getters['event/getBringingAnyone'],
    closeAfterMin: store.getters['event/getCloseAfterMinEnabled'] ? store.getters['event/getCloseAfterMin'] : null,
    closeAfterMinBookings: store.getters['event/getCloseAfterMinBookings'],
    color: store.getters['event/getColor'],
    customLocation: store.getters['event/getCustomLocation'],
    customPricing: store.getters['event/getCustomPricing'],
    customTickets: customTickets,
    deposit: store.getters['event/getDeposit'],
    depositPayment: store.getters['event/getDepositEnabled'] ? store.getters['event/getDepositPayment'] : 'disabled',
    depositPerPerson: store.getters['event/getDepositPerPerson'],
    description: store.getters['event/getDescription'] && store.getters['event/getDescriptionMode'] === 'text'
      ? '<!-- Content -->' + store.getters['event/getDescription']
      : store.getters['event/getDescription'],
    fullPayment: store.getters['event/getFullPayment'],
    gallery: store.getters['event/getGallery'],
    id: store.getters['event/getId'],
    locationId: store.getters['event/getLocationId'] === 0 ? null : store.getters['event/getLocationId'],
    maxCapacity: store.getters['event/getMaxCapacity'],
    maxCustomCapacity: store.getters['event/getMaxCustomCapacityEnabled'] ? store.getters['event/getMaxCustomCapacity'] : null,
    maxExtraPeople: store.getters['event/getMaxExtraPeopleEnabled'] ? store.getters['event/getMaxExtraPeople'] : null,
    name: store.getters['event/getName'],
    notifyParticipants: store.getters['event/getNotifyParticipants'],
    organizerId: store.getters['event/getOrganizerId'],
    parentId: store.getters['event/getParentId'],
    periods: useEventPeriods(store),
    price: store.getters['event/getPrice'],
    providers: !store.getters['event/getId']
      ? [{
        id: store.getters['auth/getProfile'].id,
        firstName: store.getters['auth/getProfile'].firstName,
        lastName: store.getters['auth/getProfile'].lastName,
        email: store.getters['auth/getProfile'].email,
        type: 'provider',
      }]
      : store.getters['event/getProviders'],
    recurring: store.getters['event/getRecurringEnabled']
      ? Object.assign(
        {},
        store.getters['event/getRecurring'],
        {
          until: moment(store.getters['event/getRecurringUntil']).format('YYYY-MM-DD'),
          monthDate: store.getters['event/getRecurringMonthDate']
            ? moment(store.getters['event/getRecurringMonthDate']).format('YYYY-MM-DD')
            : null,
        }
      )
      : null,
    settings: Object.keys(eventSettings).length ? JSON.stringify(eventSettings) : null,
    show: store.getters['event/getShow'],
    tags: store.getters['event/getTags'].map(i => new Object({name: i})),
    timeZone: store.getters['cabinet/getTimeZone'],
    translations: store.getters['event/getTranslations'],
    utc: store.getters['cabinet/getTimeZone'] === 'UTC',
    zoomUserId: store.getters['event/getZoomUserId'],
  }
}

function useEventPeriods (store) {
  return store.getters['event/getPeriods'].map(
    i => new Object(
      {
        id: i.id,
        eventId: i.eventId,
        appleCalendarEventId: i.appleCalendarEventId,
        googleCalendarEventId: i.googleCalendarEventId,
        googleMeetUrl: i.googleMeetUrl,
        outlookCalendarEventId: i.outlookCalendarEventId,
        microsoftTeamsUrl: i.microsoftTeamsUrl,
        periodStart: store.getters['cabinet/getTimeZone'] === 'UTC'
          ? moment(i.startDate).utc().format('YYYY-MM-DD') + ' ' + i.startTime + ':00'
          : moment(i.startDate).format('YYYY-MM-DD') + ' ' + i.startTime + ':00',
        periodEnd: store.getters['cabinet/getTimeZone'] === 'UTC'
          ? moment(i.endDate).utc().format('YYYY-MM-DD') + ' ' + i.endTime + ':00'
          : moment(i.endDate).format('YYYY-MM-DD') + ' ' + i.endTime + ':00',
      }
    )
  )
}

export {
  useFrontEvent,
  useBackEvent,
  useEventPeriods,
}
