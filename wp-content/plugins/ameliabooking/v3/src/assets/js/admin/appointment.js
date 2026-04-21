import { useConvertedUtcToLocalDateTime } from "../common/date";

function useAppointmentDuration(store, appointment) {
  let service = store.getters['entities/getService'](
    appointment.serviceId
  )

  let largestBookingDuration = 0

  let largestBookingExtrasDuration = 0

  appointment.bookings.forEach((booking) => {
    if (['approved', 'pending'].includes(booking.status)) {
      let bookingDuration = booking.duration ? booking.duration : service.duration

      if (bookingDuration > largestBookingDuration) {
        largestBookingDuration = bookingDuration
      }

      let bookingExtrasDuration = 0

      booking.extras.forEach((bookingExtra) => {
        let extra = service.extras.find(i => i.id === bookingExtra.extraId)

        bookingExtrasDuration += extra.duration * bookingExtra.quantity
      })

      if (bookingExtrasDuration > largestBookingExtrasDuration) {
        largestBookingExtrasDuration = bookingExtrasDuration
      }
    }
  })

  return largestBookingDuration + largestBookingExtrasDuration
}

function useParsedAppointments (appointments, timeZone, allBookings) {
  for (let dateString in appointments) {
    appointments[dateString].appointments.forEach(appointment => {
      let appointmentCustomerBookings = {}

      appointment.bookings.forEach((booking) => {
        if (!(booking.customerId in appointmentCustomerBookings)) {
          appointmentCustomerBookings[booking.customerId] = []
        }

        appointmentCustomerBookings[booking.customerId][booking.id] = booking.status
      })

      let customerBookings = {}

      for (let customerId in appointmentCustomerBookings) {
        for (let bookingId in appointmentCustomerBookings[customerId]) {
          if (!(customerId in customerBookings) ||
            (
              appointmentCustomerBookings[customerId][bookingId] === 'approved' ||
              appointmentCustomerBookings[customerId][bookingId] === 'pending'
            )
          ) {
            customerBookings[customerId] = bookingId
          }
        }
      }

      appointment.bookings.forEach(booking => {
        if (booking.customerId in customerBookings &&
          parseInt(booking.id) !== parseInt(customerBookings[booking.customerId])
        ) {
          return
        }

        if (timeZone === '') {
          appointment.bookingStart = useConvertedUtcToLocalDateTime(appointment.bookingStart)
          appointment.bookingEnd = useConvertedUtcToLocalDateTime(appointment.bookingEnd)
        }

        if (!allBookings) {
          appointment.bookings = [booking]
        }
      })
    })
  }

  if (timeZone === '') {
    let parsedGroupedAppointments = {}

    for (let dateString in appointments) {
      appointments[dateString].appointments.forEach(appointment => {
        let appointmentDateString = appointment.bookingStart.split(' ')[0]

        if (!(appointmentDateString in parsedGroupedAppointments)) {
          parsedGroupedAppointments[appointmentDateString] = {
            appointments: [appointment],
            date: appointmentDateString
          }
        } else {
          parsedGroupedAppointments[appointmentDateString].appointments.push(appointment)
        }
      })
    }

    return parsedGroupedAppointments
  }

  return appointments
}

export {
  useAppointmentDuration,
  useParsedAppointments,
}
