import moment from "moment";
import { useConvertedUtcToLocalDateTime } from "../common/date";
import {
  getNameTranslated,
  getTicketTranslated
} from "../public/translation";
import {useAmount} from "../common/pricing";

function useParsedEvents (events, timeZone, store) {
  let eventsDay = {}

  events.forEach(event => {
    if (timeZone === '') {
      event.periods.forEach(period => {
        period.periodStart = useConvertedUtcToLocalDateTime(period.periodStart)
        period.periodEnd = useConvertedUtcToLocalDateTime(period.periodEnd)
      })
    }

    let startDate = moment(event.periods[0].periodStart, 'YYYY-MM-DD HH:mm:ss')
    let dateString = startDate.format('YYYY-MM-DD')

    let availableTranslationsShort = store.getters['getSettings'].general.usedLanguages.map(
      key => key.length > 2 ? key.slice(0, 2) : key
    )

    let useTranslations = store.getters['getSettings'].general.usedLanguages.indexOf(store.getters['getLocalLanguage']) !== -1 ||
      availableTranslationsShort.indexOf(store.getters['getLocalLanguage'].split('_')[0]) !== -1

    if (useTranslations) {
      event.name = getNameTranslated(event)

      event.customTickets.forEach((ticket) => {
        ticket.name = getTicketTranslated(ticket)
      })
    }

    if (event.recurring && event.recurring.until) {
      event.recurring.until = event.recurring.until.split(' ')[0]
    }

    if (!(dateString in eventsDay)) {
      eventsDay[dateString] = {
        date: dateString,
        events: []
      }
    }

    let isWaiting = event.bookings.some(booking => booking.status === 'waiting')

    if (event.full && event.status === 'approved') {
      event.status = 'full'
    } else if (event.upcoming && event.status === 'approved') {
      event.status = 'upcoming'
    }

    if (isWaiting) {
      event.status = 'waiting'
    }

    if (store.getters['auth/getProfile'].type === 'provider') {
      let clonedEvent = JSON.parse(JSON.stringify(event))

      eventsDay[dateString].events.push(clonedEvent)
    } else {
      event.bookings.forEach((booking) => {
        let clonedEvent = JSON.parse(JSON.stringify(event))

        clonedEvent.bookings = [booking]

        eventsDay[dateString].events.push(clonedEvent)
      })
    }
  })

  return eventsDay
}

function useEventBookingsPrice (event) {
  let price = 0

  event.bookings.forEach((booking) => {
    if (['approved', 'pending'].includes(booking.status)) {
      let subTotal = 0

      if (booking.ticketsData.length > 0) {
        for (let i = 0; i < booking.ticketsData.length; i++) {
          subTotal += booking.ticketsData[i].persons * booking.ticketsData[i].price
        }
      } else {
        subTotal += event.price * booking.persons
      }

      let amountData = useAmount(
        event,
        booking.coupon,
        booking.tax ? booking.tax[0] : null,
        subTotal,
        false,
      )

      price += amountData.price - amountData.discount + amountData.tax
    }
  })

  return price >= 0 ? price : 0
}

function usePeriodsData(connectedPeriods) {
  let result = []

  connectedPeriods.forEach((period) => {
    let startDate = moment(period.periodStart.split(' ')[0], 'YYYY-MM-DD')
    let endDate = moment(period.periodEnd.split(' ')[0], 'YYYY-MM-DD')

    let startTime = period.periodStart.split(' ')[1].slice(0, 5)
    let endTime = period.periodEnd.split(' ')[1].slice(0, 5)

    if (endTime === '00:00') {
      endTime = '24:00'
      endDate.subtract(1, 'days')
    }

    let periodDates = []

    while (startDate.isSameOrBefore(endDate)) {
      periodDates.push(startDate.format('YYYY-MM-DD'))

      startDate.add(1, 'days')
    }

    periodDates.forEach((dateString) => {
      result.push({
        date: dateString,
        startTime: startTime,
        endTime: endTime,
        periodStart: dateString + ' ' + startTime,
        periodEnd: dateString + ' ' + endTime,
        zoomLink: connectedPeriods.length > 1 && period.zoomMeeting ? period.zoomMeeting.joinUrl : '',
        lessonSpaceLink: connectedPeriods.length > 1 && period.lessonSpace ? period.lessonSpace : '',
        googleMeetLink: connectedPeriods.length > 1 && period.googleMeetUrl ? period.googleMeetUrl : '',
        microsoftTeamsLink: connectedPeriods.length > 1 && period.microsoftTeamsUrl ? period.microsoftTeamsUrl : '',
      })
    })
  })

  return result
}

function useTicketsData(event) {
  let result = {}

  event.bookings.forEach((booking) => {
    if (['approved', 'pending'].includes(booking.status) && booking.ticketsData && booking.ticketsData.length) {
      booking.ticketsData.forEach((ticketData) => {
        if (!(ticketData.eventTicketId in result)) {
          result[ticketData.eventTicketId] = {
            persons: 0,
            price: ticketData.price,
            name: event.customTickets.find(i => i.id === ticketData.eventTicketId).name,
          }
        }

        result[ticketData.eventTicketId].persons = result[ticketData.eventTicketId].persons + ticketData.persons
      })
    }
  })

  return result
}

function useEventLocation (store, event) {
  let locations = store.getters['entities/getLocations']

  if (event.locationId && locations.length) {
    let location = locations.find(location => location.id === event.locationId)
    return typeof location !== 'undefined' ? location : null
  } else if (event.customLocation) {
    return {
      address: '',
      name: event.customLocation
    }
  }

  return null
}

function useEventQrCodes (event) {
  let qrCodesArr = []

  event.bookings.forEach((booking) => {
    if (['approved', 'pending'].includes(booking.status) && booking.qrCodes) {
      let arr = JSON.parse(booking.qrCodes)
      arr = arr.map(qr => {
        return {
          ...qr,
          bookingId: booking.id,
          eventId: event.id
        }
      })

      qrCodesArr = qrCodesArr.concat(arr)
    }
  })

  return qrCodesArr
}

export {
  useParsedEvents,
  useEventBookingsPrice,
  usePeriodsData,
  useTicketsData,
  useEventLocation,
  useEventQrCodes
}
