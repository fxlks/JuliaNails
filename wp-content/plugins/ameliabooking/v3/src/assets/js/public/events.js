import httpClient from "../../../plugins/axios";
import {useUrlParams} from "../common/helper";

function useEvents (params, callback) {
  httpClient.get(
    '/events',
    {params: useUrlParams(Object.assign({bookings: false}, params))}
  ).then(response => {
    callback(response.data.data.events)
  }).catch(() => {
    callback([])
  })
}

function useEventLocation (event, locations) {
    let locationAddress = ''

    if (event.locationId && locations.find(l => l.id === event.locationId)) {
        let location = locations.filter(location => location.id === event.locationId)[0]
        locationAddress = location.address

        if (!locationAddress) {
            locationAddress = location.name
        }
    }

    if (event.customLocation) {
        locationAddress = event.customLocation
    }

    return locationAddress
}

function useMinTicketPrice (event) {
  let priceArray = []

  event.customTickets.forEach(ticket => {
    if (ticket.enabled) {
      let tPrice = ticket.price
      if (ticket.dateRangePrice) {
        tPrice = ticket.dateRangePrice
      }
      priceArray.push(tPrice)
    }
  })

  return Math.min(...priceArray)
}

function useCheckIfEventNotFree (event) {
  let priceArray = []

  if (event.customPricing) {
    event.customTickets.forEach(ticket => {
      if (ticket.enabled) {
        let tPrice = ticket.price
        if (ticket.dateRangePrice) {
          tPrice = ticket.dateRangePrice
        }

        if (tPrice > 0) {
          priceArray.push(tPrice)
        }
      }
    })

    return priceArray.length > 0
  }

  return event.price > 0
}

// function getEventAvailability (evt, labels) {
//   if (evt.status === 'approved' || evt.status === 'pending') {
//     if (evt.full) {
//       return {
//         label: labels.full,
//         class: 'full'
//       }
//     }
//     if (evt.upcoming) {
//       return {
//         label: labels.upcoming,
//         class: 'upcoming'
//       }
//     }
//     return !evt.bookable ? {
//       label: 'Closed', //amLabels.closed,
//       class: 'closed'
//     } : {
//       label: labels.open,
//       class: 'open'
//     }
//   } else {
//     return {
//       label: labels.canceled,
//       class: 'canceled'
//     }
//   }
// }

function showEventCapacity (status) {
    switch (status) {
        case 'open':
        case 'upcoming':
            return true
        case 'canceled':
        case 'closed':
        case 'full':
            return false
        default:
            return true
    }
}

function useEventStatus (evt) {
  if (evt.status === 'approved' || evt.status === 'pending') {
    if (evt.full) return 'full'
    if (evt.upcoming) return 'upcoming'
    return !evt.bookable ? 'closed' : 'open'
  } else {
    return 'canceled'
  }
}

function useWaitingListAvailability(evt) {
  if (evt?.closed || !evt?.waitingList?.enabled) {
    return false;
  }

  let capacityRule;
  let waitingAlreadyStarted = 0

  if (evt.customPricing) {
    let capacityRulePerTicket = []
    const peoplePerTicket = evt.customTickets.reduce((total, ticket) => {
      if (!evt.maxCustomCapacity) {
        capacityRulePerTicket.push(ticket.waitingListSpots > ticket.waiting)
      } else {
        total += ticket.waiting;
      }
      return total + ticket.waiting;
    }, 0);

    if (!evt.maxCustomCapacity) {
      capacityRule = capacityRulePerTicket.some(rule => rule === true)
    } else {
      capacityRule = evt.waitingList.maxCapacity > evt.waitingList.peopleWaiting;
    }

    waitingAlreadyStarted = peoplePerTicket
  } else {
    capacityRule = evt.waitingList.maxCapacity > evt.waitingList.peopleWaiting;
    waitingAlreadyStarted = evt.waitingList.peopleWaiting;
  }

  return capacityRule && (evt.full || waitingAlreadyStarted !== 0)
}

function useWaitingListOccupancy (evt) {
  let spots = 0

  if (evt.customPricing) {
    evt.customTickets.forEach(ticket => {
      spots += ticket.waiting
    })
  } else {
    spots = evt.waitingList.peopleWaiting
  }

  return spots
}

export {
  // getEventAvailability,
  useEvents,
  useEventLocation,
  useMinTicketPrice,
  showEventCapacity,
  useEventStatus,
  useCheckIfEventNotFree,
  useWaitingListAvailability,
  useWaitingListOccupancy
}
