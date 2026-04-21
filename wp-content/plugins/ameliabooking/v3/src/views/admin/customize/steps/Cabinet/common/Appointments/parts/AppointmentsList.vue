<template>
  <div
    class="am-capa__wrapper"
    :class="[
      {
        'am-no-border':
          props.groupedAppointments &&
          Object.keys(props.groupedAppointments).length === 1,
      },
      props.responsiveClass,
    ]"
    :style="cssVars"
  >
    <div
      v-for="(item, dateKey) in props.groupedAppointments"
      :key="dateKey"
      class="am-capa"
    >
      <div
        class="am-capa__date"
        :class="[
          {
            'am-today':
              getFrontedFormattedDate(dateKey) ===
              getFrontedFormattedDate(moment().format('YYYY-MM-DD')),
          },
          {
            'am-no-flag':
              props.groupedAppointments &&
              Object.keys(props.groupedAppointments).length === 1,
          },
          props.responsiveClass,
        ]"
      >
        {{ getFrontedFormattedDate(dateKey) }}
      </div>
      <CollapseCard
        v-for="(appointment, index) in item.appointments"
        :key="index"
        :start="
          getFrontedFormattedTime(
            appointment.bookingStart.split(' ')[1].slice(0, 5)
          )
        "
        :name="appointment.service.name"
        :employee="appointment.provider"
        :customers="appointment.bookings.map(b => b.customer)"
        :price="125"
        :duration="1800"
        :periods="[]"
        :extras="appointment.bookings[0].extras"
        :tickets="[]"
        :custom-fields="appointment.bookings[0].customFields"
        :location="appointment.location"
        :google-meet-link="appointment.googleMeetUrl"
        :microsoft-teams-link="appointment.microsoftTeamsUrl"
        :zoom-link="
          appointment.zoomMeeting ? appointment.zoomMeeting.joinUrl : ''
        "
        :lesson-space-link="appointment.lessonSpace"
        :bookable="appointment.service"
        :reservation="appointment"
        :booking="appointment.bookings[0]"
        :responsive-class="props.responsiveClass"
        :parent-width="props.pageWidth"
        :is-package-booking="props.isPackageBooking"
        :customized-options="amCustomize[pageRenderKey][stepName].options"
      ></CollapseCard>
    </div>
  </div>

  <CancelPopup
    v-if="cancelVisibility"
    :visibility="cancelVisibility"
    title-key="cancel_appointment"
    content-key="confirm_cancel_appointment"
  >
  </CancelPopup>

  <AppointmentBooking
    v-if="rescheduleVisibility"
    :visibility="rescheduleVisibility"
  >
  </AppointmentBooking>
</template>

<script setup>
// * Import from libraries
import moment from 'moment/moment'

// * Import from Vue
import { inject, computed } from 'vue'

// * Composables
import {
  getFrontedFormattedDate,
  getFrontedFormattedTime,
} from '../../../../../../../../assets/js/common/date'
import { useColorTransparency } from '../../../../../../../../assets/js/common/colorManipulation'
import { useReactiveCustomize } from '../../../../../../../../assets/js/admin/useReactiveCustomize.js'

// * Dedicated components
import CollapseCard from '../../parts/CollapseCard/CollapseCard.vue'
import AppointmentBooking from '../../parts/AppointmentBooking.vue'
import CancelPopup from '../../parts/CancelPopup.vue'

// * Component properties
let props = defineProps({
  groupedAppointments: {
    type: Object,
    default: null,
  },
  responsiveClass: {
    type: String,
    default: '',
  },
  pageWidth: {
    type: Number,
  },
  isPackageBooking: {
    type: Boolean,
    default: false,
  },
})

// * Customize
const { amCustomize } = useReactiveCustomize()
let pageRenderKey = inject('pageRenderKey')
let stepName = inject('stepName')
let subStepName = inject('subStepName')

// * Reschedule appointment
let rescheduleVisibility = computed(() => {
  return subStepName.value === 'rescheduleAppointment'
})

// * Cancel appointment
let cancelVisibility = computed(() => {
  return subStepName.value === 'cancelAppointment'
})

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-capa-bgr': amColors.value.colorMainBgr,
    '--am-c-capa-text': amColors.value.colorMainText,
    '--am-c-capa-text-op70': useColorTransparency(
      amColors.value.colorMainText,
      0.7
    ),
    '--am-c-capa-text-op25': useColorTransparency(
      amColors.value.colorMainText,
      0.25
    ),
    '--am-c-capa-primary': amColors.value.colorPrimary,
  }
})
</script>

<script>
export default {
  name: 'CabinetAppointmentsList',
}
</script>

<style lang="scss">
@mixin am-cabinet-appointments {
  // capa -- cabinet panel appointments
  .am-capa {
    position: relative;

    &__wrapper {
      position: relative;
      padding: 0 0 0 32px;

      &:before {
        content: '';
        display: block;
        width: 1px;
        height: calc(100% - 4px);
        position: absolute;
        top: 4px;
        left: 8px;
        background-image: linear-gradient(
            180deg,
            transparent,
            transparent 50%,
            var(--am-c-capa-bgr) 50%,
            var(--am-c-capa-bgr) 100%
          ),
          linear-gradient(
            180deg,
            var(--am-c-capa-text-op25),
            var(--am-c-capa-text-op25),
            var(--am-c-capa-text-op25),
            var(--am-c-capa-text-op25)
          );
        background-size: 3px 12px, 100% 20px;
      }

      &.am-no-border {
        padding: 0;

        &:before {
          display: none;
        }
      }

      &.am-rw-500 {
        padding: 0;

        &:before {
          display: none;
        }
      }
    }

    &__date {
      font-size: 15px;
      font-weight: 500;
      line-height: 1.6;
      color: var(--am-c-capa-text-op70);
      margin: 24px 0 8px;

      &:before {
        content: '';
        display: block;
        width: 16px;
        height: 16px;
        position: absolute;
        top: 4px;
        left: -32px;
        background-color: var(--am-c-capa-text-op70);
        border-radius: 50%;
        z-index: 2;
      }

      &:after {
        content: '';
        display: block;
        width: 16px;
        height: 16px;
        position: absolute;
        top: 4px;
        left: -32px;
        background-color: var(--am-c-capa-bgr);
        border-radius: 50%;
        z-index: 1;
      }

      &.am-today {
        &:before {
          background-color: var(--am-c-capa-primary);
        }
      }

      &.am-no-flag {
        &:before,
        &:after {
          display: none;
        }
      }

      &.am-rw-500 {
        display: flex;
        align-items: center;
        &:before {
          position: static;
          margin-right: 8px;
        }

        &:after {
          display: none;
        }
      }
    }
  }
}

#amelia-app-backend-new #amelia-container {
  @include am-cabinet-appointments;
}
</style>
