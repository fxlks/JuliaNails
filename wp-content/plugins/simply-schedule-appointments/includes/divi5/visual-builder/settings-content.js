/**
 * SSA Booking Module - Content Settings
 *
 * @package Simply_Schedule_Appointments
 * @since 5.9.0
 */

import React from 'react';

const __ = window?.vendor?.wp?.i18n?.__ || window?.wp?.i18n?.__ || ((text) => text);
const { FieldContainer } = window?.divi?.module || {};
const { GroupContainer } = window?.divi?.modal || {};
const { SelectContainer } = window?.divi?.fieldLibrary || {};

export const SSABookingSettingsContent = (props) => {
  // Get appointment types from localized data
  const appointmentTypes = window.ssaAppointmentTypes || [{ label: 'All Types', value: '' }];
  
  // Convert to Divi format: { value: { label: 'Label', value: 'value' } }
  const options = {};
  if (Array.isArray(appointmentTypes)) {
    appointmentTypes.forEach(type => {
      options[String(type.value)] = {
        label: String(type.label),
        value: String(type.value)
      };
    });
  }

  return React.createElement(
    React.Fragment,
    null,
    React.createElement(
      GroupContainer,
      {
        id: "ssa_booking_settings",
        title: __('Booking Settings', 'simply-schedule-appointments')
      },
      React.createElement(
        FieldContainer,
        {
          attrName: "module.innerContent.appointmentType",
          label: __('Appointment Type', 'simply-schedule-appointments'),
          description: __('Select which appointment type to display', 'simply-schedule-appointments'),
          features: { sticky: false }
        },
        React.createElement(SelectContainer, { options: options })
      )
    )
  );
};
