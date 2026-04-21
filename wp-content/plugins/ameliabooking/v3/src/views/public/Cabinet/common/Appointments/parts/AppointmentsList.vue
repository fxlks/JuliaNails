<template>
  <div
    class="am-capa__wrapper"
    :class="[{'am-no-border': props.groupedAppointments && Object.keys(props.groupedAppointments).length === 1}, props.responsiveClass]"
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
          {'am-today': getFrontedFormattedDate(dateKey) === getFrontedFormattedDate(moment().format('YYYY-MM-DD'))},
          {'am-no-flag': props.groupedAppointments && Object.keys(props.groupedAppointments).length === 1},
          props.responsiveClass
        ]"
      >
        {{getFrontedFormattedDate(dateKey)}}
      </div>
      <template v-for="(appointment, index) in item.appointments" :key="index">
        <CollapseCard
          v-if="store.getters['entities/getService'](appointment.serviceId)"
          :start="getFrontedFormattedTime(appointment.bookingStart.split(' ')[1].slice(0, 5))"
          :name="store.getters['entities/getService'](appointment.serviceId).name"
          :employee="appointmentEmployee(appointment.provider.id)"
          :customers="appointmentCustomers(appointment)"
          :price="getPrice(appointment)"
          :duration="useAppointmentDuration(store, appointment)"
          :periods="[]"
          :extras="useExtrasData(appointment.bookings, store.getters['entities/getService'](appointment.serviceId))"
          :tickets="[]"
          :custom-fields="useCustomFieldsData(appointment.bookings, cabinetType)"
          :location="appointment.locationId ? store.getters['entities/getLocation'](appointment.locationId) : null"
          :google-meet-link="appointment.googleMeetUrl"
          :microsoft-teams-link="appointment.microsoftTeamsUrl"
          :zoom-link="appointment.zoomMeeting ? appointment.zoomMeeting.joinUrl : ''"
          :lesson-space-link="appointment.lessonSpace"
          :bookable="appointment.service"
          :reservation="appointment"
          :booking="appointment.bookings[0]"
          :is-package-booking="props.isPackageBooking"
          :responsive-class="props.responsiveClass"
          :parent-width="props.pageWidth"
          :customized-options="customizedOptions(props.stepKey)"
          @cancel-booking="(data) => { targetBooking = data }"
          @rescheduling="rescheduling"
          @edit-appointment="(data) => {emits('editAppointment', data)}"
          @status-change="(message, type) => emits('statusChange', message, type)"
        ></CollapseCard>
      </template>
    </div>
  </div>

  <CancelPopup
    v-if="originKey === 'capc'"
    :visibility="targetBooking !== null"
    :title="customizedLabels('cancelAppointment').cancel_appointment"
    :description="customizedLabels('cancelAppointment').confirm_cancel_appointment"
    :close-btn-text="customizedLabels('cancelAppointment').close"
    :confirm-btn-text="customizedLabels('cancelAppointment').confirm"
    :customized-options="customizedOptions('cancelAppointment')"
    :loading="waitingForCancelation"
    @close="targetBooking = null"
    @decline="targetBooking = null"
    @confirm="cancelBooking"
  >
  </CancelPopup>

  <AppointmentBooking
    v-if="originKey === 'capc'"
    :visibility="targetAppointment !== null"
    :appointment="targetAppointment"
    :slots-params="slotsParams"
    :labels="customizedLabels('rescheduleAppointment')"
    :customized-options="customizedOptions('rescheduleAppointment')"
    @close="targetAppointment = null"
    @success="successfulAddBooking"
    @error="() => {}"
  >
  </AppointmentBooking>
</template>

<script setup>
// * Import from libraries
import moment from "moment/moment";

// * Import from Axios
import httpClient from "../../../../../../plugins/axios";

// * Import from Vue
import {
  ref,
  inject,
  computed,
  reactive
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Composables
import {
  getFrontedFormattedDate,
  getFrontedFormattedTime
} from "../../../../../../assets/js/common/date";
import {
  useAppointmentDuration,
} from "../../../../../../assets/js/admin/appointment";
import {
  useAppointmentBookingAmountData,
} from "../../../../../../assets/js/common/appointments";
import {
  useCustomFieldsData,
  useExtrasData
} from "../../../../../../assets/js/admin/booking";
import {
  useAuthorizationHeaderObject
} from "../../../../../../assets/js/public/panel";
import {
  useColorTransparency
} from "../../../../../../assets/js/common/colorManipulation";

// * Dedicated components
import CollapseCard from "../../parts/CollapseCard/CollapseCard.vue";
import AppointmentBooking from "../../parts/AppointmentBooking.vue";
import CancelPopup from "../../parts/CancelPopup.vue";

// * Component properties
let props = defineProps({
  groupedAppointments: {
    type: Object,
    default: null
  },
  responsiveClass: {
    type: String,
    default: ''
  },
  pageWidth: {
    type: Number,
  },
  isPackageBooking: {
    type: Boolean,
    default: false
  },
  stepKey: {
    type: String,
    required: true
  }
})

let store = useStore()

// * Components emits
let emits = defineEmits([
  'booked',
  'canceled',
  'editAppointment',
  'statusChange'
])

let originKey = inject('originKey')

// * Settings
const amSettings = inject('settings')

// * Labels
let amLabels = inject('amLabels')

// * Cabinet type
let cabinetType = inject('cabinetType')

let targetBooking = ref(null)

let targetAppointment = ref(null)

let slotsParams = ref(null)

function successfulAddBooking (passedData = null) {
  targetAppointment.value = null
  emits('booked', passedData)
}

let waitingForCancelation = ref(false)

function cancelBooking () {
  waitingForCancelation.value = true
  httpClient.post(
    '/bookings/cancel/' + targetBooking.value.id,
    {type: 'appointment'},
    Object.assign(useAuthorizationHeaderObject(store), {params: {source: 'cabinet-' + cabinetType.value}})
  ).then(() => {
    targetBooking.value = null
    emits('canceled', {message: amLabels.value.appointment_canceled})
  }).catch((e) => {
    console.log(e)
  }).finally(() => {
    waitingForCancelation.value = false
  })
}

function getPrice (appointment) {
  let amountData = {
    total: 0,
    bookable: 0,
    discount: 0,
    tax: 0,
  }

  appointment.bookings.forEach((booking) => {
    if (booking.status === 'approved' || booking.status === 'pending') {
      let bookingAmountData = useAppointmentBookingAmountData(
        store,
        {
          price: booking.price,
          persons: booking.persons,
          aggregatedPrice: booking.aggregatedPrice,
          extras: booking.extras,
          tax: booking.tax,
          coupon: booking.coupon
        },
        false
      )

      amountData.total += bookingAmountData.total
      amountData.bookable += bookingAmountData.bookable
      amountData.discount += bookingAmountData.discount
      amountData.tax += bookingAmountData.tax
    }
  })

  return amountData.total - amountData.discount + amountData.tax
}

function rescheduling (data) {
  let extras = []

  let duration = 0

  let appointment = data

  appointment.bookings.forEach((bookItem) => {
    bookItem.extras.forEach((extItem) => {
      extras.push({
        id: extItem.extraId,
        quantity: extItem.quantity
      })
    })

    if ('duration' in bookItem &&
      bookItem.duration &&
      bookItem.duration > duration &&
      (bookItem.status === 'approved' || bookItem.status === 'pending')
    ) {
      duration = bookItem.duration
    }
  })

  let params = {
    serviceId: appointment.serviceId,
    serviceDuration: duration ? duration : null,
    locationId: appointment.locationId,
    providerIds: [appointment.providerId],
    extras: JSON.stringify(extras),
    excludeAppointmentId: !appointment.isGroup ? appointment.id : null,
    group: 1,
    timeZone: store.getters['cabinet/getTimeZone'],
    page: 'cabinet',
    structured: true,
  }

  targetAppointment.value = data
  slotsParams.value = params
}

function appointmentEmployee(id) {
  return store.getters['entities/getEmployee'](id)
}

function appointmentCustomers(appointment) {
  return appointment.bookings
    .filter((b) => b.status !== 'rejected' && b.status !== 'canceled')
    .map((b) => {
      let customer
      if ('info' in b && b.info) {
        customer = 'customer' in b ? b.customer : {}
        customer = Object.assign(customer, JSON.parse(b.info))
      } else {
        customer = b.customer
      }
      
      // Add booking status to customer data
      customer.bookingStatus = b.status
      return customer
    })
}

// * Customized form data
let amCustomize = inject('amCustomize')

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

function customizedLabels (step) {
  let computedLabels = reactive({...labels})

  let customizedLabels = amCustomize.value[step].translations
  if (customizedLabels) {
    Object.keys(customizedLabels).forEach(labelKey => {
      if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
        computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
}

function customizedOptions (step) {
  return amCustomize.value[step].options
}

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-capa-bgr': amColors.value.colorMainBgr,
    '--am-c-capa-text': amColors.value.colorMainText,
    '--am-c-capa-text-op70': useColorTransparency(amColors.value.colorMainText, 0.7),
    '--am-c-capa-text-op25': useColorTransparency(amColors.value.colorMainText, 0.25),
    '--am-c-capa-primary': amColors.value.colorPrimary,
  }
})
</script>

<script>
export default {
  name: 'CabinetAppointmentsList'
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
        background-image: linear-gradient(180deg, transparent, transparent 50%, var(--am-c-capa-bgr) 50%, var(--am-c-capa-bgr) 100%), linear-gradient(180deg, var(--am-c-capa-text-op25), var(--am-c-capa-text-op25), var(--am-c-capa-text-op25), var(--am-c-capa-text-op25));
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
        &:before, &:after {
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

.amelia-v2-booking #amelia-container {
  @include am-cabinet-appointments;
}
</style>
