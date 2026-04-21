<template>
  <div
    v-if="!loading"
    class="am-capai"
    :class="[{ 'am-no-border': true }, props.responsiveClass]"
    :style="cssVars"
  >
    <AmAlert
      v-if="alertVisibility"
      class="am-asi__top-message am-asi__top-message-error"
      type="error"
      :show-icon="true"
      :show-border="true"
      :close-after="5000"
      @close="closeAlert"
      @trigger-close="closeAlert"
    >
      <template #title>
        {{ errorMessage }}
      </template>
    </AmAlert>

    <el-tabs v-model="activeTab">
      <!-- Details -->
      <el-tab-pane :label="amLabels.details" name="details">
        <AppointmentDetails
          ref="appointmentDetailsRef"
          :slots-props="slotsProps"
          :slots-loading="slotsLoading"
          :free-slots="freeSlots"
          :is-disabled-date="isDisabledDate"
          :times="times"
          :get-free-times="getFreeTimes"
          @remove-slots="removeSlots"
          @fetch-slots="fetchSlots"
          @select-date="selectDate"
          @select-time="selectTime"
        />
      </el-tab-pane>
      <!-- /Details -->

      <!-- Customers -->
      <el-tab-pane v-if="service" :label="amLabels.customers" name="customers">
        <AppointmentCustomers
          ref="appointmentCustomersRef"
          :responsive-class="props.responsiveClass"
          :capacity-warning="capacityWarning"
          :appointment-capacity="appointmentCapacity"
          :saved-appointment="savedAppointment"
          @changed-slot-condition="changedSlotCondition"
        />
      </el-tab-pane>
      <!-- /Customers -->

      <!-- Extras -->
      <el-tab-pane
        v-if="
          amSettings.featuresIntegrations.extras.enabled &&
          service &&
          service.extras.length &&
          store.getters['appointment/getBookings'].length
        "
        :label="amLabels.extras"
        name="extras"
      >
        <AppointmentExtras
          :responsive-class="props.responsiveClass"
          @changed-slot-condition="changedSlotCondition"
        />
      </el-tab-pane>
      <!-- /Extras -->

      <!-- Custom Fields -->
      <el-tab-pane
        v-if="
          amSettings.featuresIntegrations.customFields.enabled &&
          service &&
          customFields.length &&
          store.getters['appointment/getBookings'].length
        "
        :label="amLabels.custom_fields"
        name="customFields"
      >
        <AppointmentCustomFields
          ref="appointmentCustomFieldsRef"
          :responsive-class="props.responsiveClass"
        />
      </el-tab-pane>
      <!-- /Custom Fields -->

      <!-- Linked -->
      <el-tab-pane
        v-if="store.getters['appointment/getId'] && linkedAppointments.length"
        :label="amLabels.linked"
        name="linked"
      >
        <AppointmentLinked
          :linked-appointments="linkedAppointments"
          :responsive-class="props.responsiveClass"
          @edit-linked-appointment="(data) => emits('editLinkedAppointment', data)"
        />
      </el-tab-pane>
      <!-- /Linked -->

      <!-- Recurring -->
      <el-tab-pane
        v-if="
          amSettings.featuresIntegrations.recurringAppointments.enabled &&
          service &&
          service.recurringCycle !== 'disabled' &&
          store.getters['appointment/getStartDate'] &&
          store.getters['appointment/getStartTime'] &&
          !store.getters['appointment/getId'] &&
          !!store.getters['customerInfo/getCustomersIds'].length
        "
        :label="amLabels.recurring"
        name="recurring"
      >
        <AppointmentRecurring
          :free-slots="freeSlots"
          :slots-props="slotsProps"
          :maximum-booking-start-date="maximumBookingStartDate"
          :get-free-times="getFreeTimes"
          :get-selected-dates-times="getSelectedDatesTimes"
          :is-disabled-date="isDisabledDate"
          :slots-loading="slotsLoading"
          :responsive-class="props.responsiveClass"
          @fetch-slots="fetchSlots"
        />
      </el-tab-pane>
      <!-- /Recurring -->

      <!-- Payment -->
      <el-tab-pane
        v-if="store.getters['appointment/getId']"
        :label="amLabels.payment"
        name="payment"
      >
        <AppointmentPayment
          :responsive-class="props.responsiveClass"
          :saved-appointment="savedAppointment"
        />
      </el-tab-pane>
      <!-- /Payment -->
    </el-tabs>

    <div class="am-capai__footer">
      <AmButton
        category="secondary"
        :size="'default'"
        :type="'plain'"
        @click="() => {emits('close')}"
      >
        {{ amLabels.cancel }}
      </AmButton>
      <AmButton :size="'default'" @click="validateSave">
        {{ amLabels.save }}
      </AmButton>
    </div>
  </div>
  <Skeleton v-else></Skeleton>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  provide,
  inject,
  computed,
  onMounted
} from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

import moment from 'moment'

// * Composables
import { useTimeInSeconds } from '../../../../../../assets/js/common/date'
import { useAppointmentSlots, useSlotsPricing } from '../../../../../../assets/js/public/slots'
import { useAuthorizationHeaderObject } from '../../../../../../assets/js/public/panel'

// * Components
import AmButton from '../../../../../_components/button/AmButton.vue'

// * Dedicated components
import Skeleton from '../../Authentication/parts/Skeleton.vue'
import AppointmentDetails from './AppointmentDetails.vue'
import AppointmentCustomers from './AppointmentCustomers.vue'
import AppointmentCustomFields from './AppointmentCustomFields.vue'
import AppointmentExtras from './AppointmentExtras.vue'
import AppointmentRecurring from './AppointmentRecurring.vue'
import AppointmentLinked from './AppointmentLinked.vue'
import AppointmentPayment from './AppointmentPayment.vue'
import AmAlert from "../../../../../_components/alert/AmAlert.vue";
import CancelPopup from "../../parts/CancelPopup.vue";

import httpClient from '../../../../../../plugins/axios'
import { useColorTransparency } from '../../../../../../assets/js/common/colorManipulation'
import {
  useAppointmentBookingAmountData,
  useAppointmentServicePrice,
  useChangedBookingPrice,
} from '../../../../../../assets/js/common/appointments'

// * Component properties
let props = defineProps({
  responsiveClass: {
    type: String,
    default: '',
  },
  pageWidth: {
    type: Number,
  },
  linkedAppointments: {
    type: Array,
    default: () => {
      return []
    },
  },
})

let store = useStore()

// * Components emits
let emits = defineEmits(['close', 'save', 'editLinkedAppointment'])

// * Labels
let amLabels = inject('amLabels')

// * Settings
const amSettings = inject('settings')

// * Plugin Licence
let licence = inject('licence')

/********
 * Tabs *
 ********/
const activeTab = ref('details')

/***********
 * Details *
 ***********/
const appointmentDetailsRef = ref(null)
let service = computed(() => store.getters['appointment/getEmployeeService'])

/*************
 * Customers *
 *************/
let appointmentCustomersRef = ref(null)

let capacityWarning = ref('')

let appointmentCapacity = computed(() => {
  return store.getters['appointment/getBookings'].length
    ? store.getters['appointment/getBookings'].filter(i => i.status === 'approved' || i.status === 'pending')
        .map((b) => b.persons)
        .reduce((sum, p) => sum + p, 0)
    : 0
})

/*****************
 * Custom Fields *
 *****************/
let appointmentCustomFieldsRef = ref(null)
let customFields = computed(() => store.getters['appointment/getCustomFields'])

/*************
 * Recurring *
 *************/
let recurringList = ref(null)
provide('recurringList', recurringList)

function getSelectedDatesTimes(ignoredRecurringIndex, ignoredMain) {
  let date = moment(store.getters['appointment/getStartDate']).format(
    'YYYY-MM-DD'
  )

  let selectedDateTimes = {}

  if (
    store.getters['appointment/getStartDate'] &&
    store.getters['appointment/getStartTime'] &&
    !ignoredMain
  ) {
    selectedDateTimes[date] = {}

    selectedDateTimes[date][store.getters['appointment/getStartTime']] = true
  }

  if (recurringList.value) {
    recurringList.value.forEach((item, index) => {
      if (
        item.time &&
        (ignoredRecurringIndex !== null
          ? index !== ignoredRecurringIndex
          : true)
      ) {
        let recurringDate = moment(item.date).format('YYYY-MM-DD')

        if (!(recurringDate in selectedDateTimes)) {
          selectedDateTimes[recurringDate] = {}
        }

        selectedDateTimes[recurringDate][item.time] = true
      }
    })
  }

  return selectedDateTimes
}

function getFreeTimes(ignoredRecurringIndex, data, isMain) {
  if (data.date === null) {
    return []
  }

  let date = moment(data.date).format('YYYY-MM-DD')

  let selectedDatesTimes = getSelectedDatesTimes(ignoredRecurringIndex, isMain)

  let timeInSeconds = data.time ? useTimeInSeconds(data.time) : null

  if (date in selectedDatesTimes) {
    let remainingTimes = data.times.filter(
      (t) => !Object.keys(selectedDatesTimes[date]).includes(t)
    )

    let availableTimes = []

    remainingTimes.forEach((remainingTime) => {
      let remainingTimeInSeconds = useTimeInSeconds(remainingTime)

      let available = true

      Object.keys(selectedDatesTimes[date])
        .map((i) => useTimeInSeconds(i))
        .sort((a, b) => {
          return a - b
        })
        .forEach((selectedTimeInSeconds) => {
          if (remainingTimeInSeconds < selectedTimeInSeconds) {
            if (
              remainingTimeInSeconds +
                service.value.duration +
                service.value.timeAfter >
              selectedTimeInSeconds
            ) {
              available = false
            }
          } else {
            if (
              selectedTimeInSeconds +
                service.value.duration +
                service.value.timeAfter >
              remainingTimeInSeconds
            ) {
              available = false
            }
          }
        })

      if (available) {
        if (
          timeInSeconds !== null &&
          remainingTimeInSeconds > timeInSeconds &&
          availableTimes.indexOf(data.time) === -1
        ) {
          availableTimes.push(data.time)
        }

        availableTimes.push(remainingTime)
      }
    })

    return filterOut(date, selectedDatesTimes, availableTimes)
  }

  return filterOut(date, selectedDatesTimes, data.times)
}

function filterOut(date, selectedDatesTimes, availableTimes) {
  let filtered = []

  let hasFiltered = false

  let dayBeforeDate = moment(date, 'YYYY-MM-DD')
    .subtract(1, 'days')
    .format('YYYY-MM-DD')

  if (dayBeforeDate in selectedDatesTimes) {
    let dayBeforeSelectedTimesInSeconds = Object.keys(
      selectedDatesTimes[dayBeforeDate]
    )
      .map((i) => useTimeInSeconds(i))
      .sort((a, b) => {
        return a - b
      })

    let dayBeforeLastTimeInSeconds =
      dayBeforeSelectedTimesInSeconds[
        dayBeforeSelectedTimesInSeconds.length - 1
      ] +
      service.value.duration +
      service.value.timeAfter

    if (dayBeforeLastTimeInSeconds > 86400) {
      availableTimes.forEach((time) => {
        if (dayBeforeLastTimeInSeconds - 86400 <= useTimeInSeconds(time)) {
          filtered.push(time)
        }
      })

      availableTimes = filtered

      hasFiltered = true
    }
  }

  let dayAfterDate = moment(date, 'YYYY-MM-DD')
    .add(1, 'days')
    .format('YYYY-MM-DD')

  if (dayAfterDate in selectedDatesTimes) {
    let dayAfterSelectedTimesInSeconds = Object.keys(
      selectedDatesTimes[dayAfterDate]
    )
      .map((i) => useTimeInSeconds(i))
      .sort((a, b) => {
        return a - b
      })

    if (
      dayAfterSelectedTimesInSeconds[0] -
        service.value.duration -
        service.value.timeAfter <
      0
    ) {
      availableTimes.forEach((time) => {
        let timeInSeconds = useTimeInSeconds(time)

        if (
          timeInSeconds + service.value.duration + service.value.timeAfter <=
            86400 ||
          dayAfterSelectedTimesInSeconds[0] >=
            timeInSeconds +
              service.value.duration +
              service.value.timeAfter -
              86400
        ) {
          filtered.push(time)
        }
      })

      hasFiltered = true
    }
  }

  return hasFiltered ? filtered : availableTimes
}

/************
 * Calendar *
 ************/
let slotsPricing = ref({})
provide('slotsPricing', slotsPricing)

let maximumBookingStartDate = ref('')

let freeSlots = ref({})

let times = ref([])

function selectDate(date) {
  times.value = Object.keys(freeSlots.value[moment(date).format('YYYY-MM-DD')])
}

function selectTime(time) {
  let locationId = freeSlots.value[moment(store.getters['appointment/getStartDate']).format('YYYY-MM-DD')][time][0].l

  if (locationId) {
    store.commit('appointment/setLocationId', locationId)
  }
}

function isDisabledDate(value, inspectTimes, isMain, monthOrYear) {
  let date = moment(value).format('YYYY-MM-DD')

  return typeof monthOrYear === 'undefined' && !(
    date in freeSlots.value &&
    (inspectTimes
      ? getFreeTimes(
          null,
          {
            date: value,
            times: Object.keys(freeSlots.value[date]),
          },
          isMain
        ).length
      : true)
  )
}

/*********
 * Slots *
 *********/
let slotsLoading = ref(false)

let slotsProps = computed(() => {
  let extras = {}

  store.getters['appointment/getBookings'].filter(i => (i.status === 'pending' || i.status === 'approved')).forEach((booking) => {
    booking.extras.forEach((bookingExtra) => {
      if (bookingExtra.quantity && !(bookingExtra.extraId in extras)) {
        extras[bookingExtra.extraId] = {id: bookingExtra.extraId, quantity: bookingExtra.quantity}
      }
    })
  })

  return {
    serviceId: store.getters['appointment/getServiceId'],
    serviceDuration: Math.max.apply(
      Math,
      store.getters['appointment/getBookings']
        .filter((b) => b.status !== 'canceled' && b.status !== 'rejected')
        .map((b) => b.duration)
    ),
    providerIds: store.getters['appointment/getProviderId']
      ? [store.getters['appointment/getProviderId']]
      : [],
    locationId: store.getters['appointment/getLocationId'],
    extras: JSON.stringify(Object.values(extras)),
    persons: appointmentCapacity.value ? appointmentCapacity.value : 1,
    excludeAppointmentId: store.getters['appointment/getId'],
    group: store.getters['appointment/getId'] ? 0 : 1,
    timeZone: store.getters['cabinet/getTimeZone'],
    monthsLoad: 1,
    page: 'appointments',
    structured: true,
  }
})

function changedSlotCondition(isMount = false) {
  if (store.getters['appointment/getStartDate']) {
    fetchSlots(store.getters['appointment/getStartDate'], null, false, isMount)
  }
}

function fetchSlots(selectedDate, recurringIndex = null, isNavigation = false, isMount = false) {
  if (store.getters['appointment/getServiceId']) {
    slotsLoading.value = true

    useAppointmentSlots(
      Object.assign(
        {
          startDateTime: selectedDate
            ? moment(selectedDate)
                .startOf('month')
                .subtract(6, 'days')
                .format('YYYY-MM-DD')
            : null,
          endDateTime: selectedDate
            ? moment(selectedDate)
                .endOf('month')
                .add(6, 'days')
                .format('YYYY-MM-DD')
            : null,
        },
        slotsProps.value
      ),
      {},
      (slots, occupied, minimum, maximum) => {
        maximumBookingStartDate.value = maximum

        if (
          store.getters['appointment/getStartDate'] &&
          recurringIndex === null &&
          !isNavigation
        ) {
          let startDateString = moment(
            store.getters['appointment/getStartDate']
          ).format('YYYY-MM-DD')

          if (startDateString in slots) {
            if (
              store.getters['appointment/getStartTime'] &&
              !(
                store.getters['appointment/getStartTime'] in
                slots[startDateString]
              ) &&
              !isMount
            ) {
              store.commit('appointment/setStartTime', null)
            }

            times.value = Object.keys(slots[startDateString])

            freeSlots.value = slots
          } else if (!isMount) {
            freeSlots.value = {}

            store.commit('appointment/setStartTime', null)
            store.commit('appointment/setStartDate', null)
            store.commit('appointment/setTargetedDate', null)

            times.value = []
          }
        } else {
          freeSlots.value = slots
        }

        let slotsPricingResult = !licence.isLite && !licence.isStarter && !licence.isBasic
         ? useSlotsPricing(store, slots, store.getters['appointment/getServiceId'])
         : null

        slotsPricing.value = slotsPricingResult ? slotsPricingResult.dates : {}

        slotsLoading.value = false
      },
      () => {}
    )
  }
}

function removeSlots() {
  freeSlots.value = {}
  maximumBookingStartDate.value = null
  recurringList.value = null
}

// * No Show Function
function noShowData(noShowCount) {
  if (noShowCount) {
    if (noShowCount === 1) {
      return {
        class: 'am-no-show-1',
        icon: 'no-show-1',
      }
    }
    if (noShowCount === 2) {
      return {
        class: 'am-no-show-2',
        icon: 'no-show-2',
      }
    }

    if (noShowCount > 2) {
      return {
        class: 'am-no-show-3',
        icon: 'no-show-3',
      }
    }
  }

  return {
    class: '',
    icon: '',
  }
}
provide('noShowData', { noShowData })

/********
 * Form *
 ********/
let alertVisibility = ref(false)

let errorMessage = ref('')

function closeAlert () {
  alertVisibility.value = false
  errorMessage.value = ''
}

let loading = ref(false)

let savedAppointment = ref(null)


function getAmount(serviceId, bookings) {
  let amount = 0

  let employeeService = store.getters['entities/getEmployeeService'](
    store.getters['appointment/getProviderId'],
    store.getters['appointment/getServiceId']
  )

  bookings
    .filter(i => i.status !== 'canceled' && i.status !== 'rejected')
    .forEach((booking) => {
      let changedBookingPrice = useChangedBookingPrice(
        store.getters['appointment/getAppointmentData'],
        savedAppointment.value,
        booking,
        service.value
      )

      let data = {
        id: 'id' in booking ? booking.id : null,
        price: !changedBookingPrice
          ? booking.price
          : useAppointmentServicePrice(employeeService, booking.persons, booking.duration),
        persons: booking.persons,
        aggregatedPrice: 'aggregatedPrice' in booking ? booking.aggregatedPrice : employeeService.aggregatedPrice,
        extras: booking.extras.filter((e) => 'id' in e && e.id),
        serviceId: serviceId,
        coupon: booking.coupon,
      }

      if ('tax' in booking) {
        data.tax = booking.tax
      }

      let bookingAmountData = useAppointmentBookingAmountData(
        store,
        data,
        false
      )

      amount += bookingAmountData.total - bookingAmountData.discount + bookingAmountData.tax
    }
  )

  return amount
}

function validateSave() {
  // Create promises for each validation
  const customFieldsPromise = new Promise((resolve) => {
    if (appointmentCustomFieldsRef.value?.customFieldsFormRef) {
      appointmentCustomFieldsRef.value.customFieldsFormRef.validate((valid) => {
        if (!valid) {
          activeTab.value = 'customFields'
          resolve(false)
        } else {
          resolve(true)
        }
      })
    } else {
      resolve(true) // No custom fields form, so consider it valid
    }
  })

  const customersPromise = new Promise((resolve) => {
    if (appointmentCustomersRef.value?.customersFormRef) {
      appointmentCustomersRef.value.customersFormRef.validate((valid) => {
        if (!valid || !store.getters['customerInfo/getCustomersIds'].length) {
          activeTab.value = 'customers'
          resolve(false)
        } else {
          resolve(true)
        }
      })
    } else {
      resolve(true) // No customers form, so consider it valid
    }
  })

  const detailsPromise = new Promise((resolve) => {
    if (appointmentDetailsRef.value?.detailsFormRef) {
      appointmentDetailsRef.value.detailsFormRef.validate((valid) => {
        if (!valid ||
          !store.getters['appointment/getServiceId'] ||
          !store.getters['appointment/getStartDate'] ||
          !store.getters['appointment/getStartTime']) {
          activeTab.value = 'details'
          resolve(false)
        } else {
          resolve(true)
        }
      })
    } else {
      resolve(true) // No details form, so consider it valid
    }
  })

  // Wait for all validations to complete
  Promise.all([customFieldsPromise, customersPromise, detailsPromise])
    .then((results) => {
      // If any validation failed, stop here
      if (results.includes(false)) {
        return
      }

      // Continue with capacity check
      capacityWarning.value =
        store.getters['appointment/getBookings']
          .filter(i => i.status !== 'canceled' && i.status !== 'rejected' && i.status !== 'waiting')
          .map((i) => i.persons)
          .reduce((accumulator, currentValue) => {
            return accumulator + currentValue
          }, 0) > service.value.maxCapacity
          ? amLabels.value.select_max_customer_count_warning +
          ' ' +
          service.value.maxCapacity
          : ''

      // Check capacity warning
      if (capacityWarning.value) {
        activeTab.value = 'customers'
        return
      }

      saveAppointment()
    })
}

function saveAppointment() {
  let bookings = []

  store.getters['appointment/getBookings'].forEach((booking) => {
    let customFields = {}

    Object.keys(booking.customFields).forEach((id) => {
      customFields[id] = {
        label: booking.customFields[id].label,
        type: booking.customFields[id].type,
        value:
          booking.customFields[id].type === 'datepicker'
            ? booking.customFields[id].value
              ? moment(booking.customFields[id].value).format('YYYY-MM-DD')
              : ''
            : booking.customFields[id].value,
      }
    })

    bookings.push(
      Object.assign({}, booking, {
        customerId: booking.customer.id,
        extras: booking.extras.filter((e) => e.quantity),
        customFields: amSettings.featuresIntegrations.customFields.enabled || store.getters['appointment/getId']
          ? customFields
          : null,
      })
    )
  })

  let removedBookings = []

  if (savedAppointment.value) {
    let oldBookingsIds = savedAppointment.value.bookings.map(
      (i) => 'id' in i && i.id
    )

    let newBookingsIds = bookings.map((j) => 'id' in j && j.id)

    removedBookings = savedAppointment.value.bookings
      .filter((booking) =>
        oldBookingsIds
          .filter((x) => !newBookingsIds.includes(x))
          .includes(booking.id)
      )
      .map(
        (booking) =>
          new Object({
            id: booking.id,
          })
      )
  }

  let timeZone = store.getters['cabinet/getTimeZone']

  let data = {
    serviceId: store.getters['appointment/getServiceId'],
    providerId: store.getters['appointment/getProviderId'],
    locationId: store.getters['appointment/getLocationId'],
    bookings: bookings,
    removedBookings: removedBookings,
    bookingStart:
      moment(store.getters['appointment/getStartDate']).format('YYYY-MM-DD') +
      ' ' +
      store.getters['appointment/getStartTime'],
    utc: timeZone === 'UTC',
    timeZone: timeZone === 'UTC' ? null : timeZone,
    notifyParticipants: store.getters['appointment/getNotifyParticipants']
      ? 1
      : 0,
    internalNotes: store.getters['appointment/getInternalNotes'],
    id: store.getters['appointment/getId'],
    payment: {
      gateway: 'onSite',
    },
    recurring: recurringList.value
      ? recurringList.value.map(
          (i) =>
            new Object({
              bookingStart: moment(i.date).format('YYYY-MM-DD') + ' ' + i.time,
              providerId: i.slot[0][0],
              locationId: i.slot[0][1],
            })
        )
      : [],
    lessonSpace: store.getters['appointment/getLessonSpace']
      ? 'https://www.thelessonspace.com/space/' + store.getters['appointment/getLessonSpace']
      : null,
    createPaymentLinks: store.getters['appointment/getCreatePaymentLinks']
        ? 1
        : 0,
  }

  loading.value = true

  httpClient
    .post(
      '/appointments' +
        (store.getters['appointment/getId']
          ? '/' + store.getters['appointment/getId']
          : ''),
      data,
      Object.assign(
        useAuthorizationHeaderObject(store),
        {
          params: { source: 'cabinet-provider' },
        }
      )
    )
    .then((result) => {
      if (result.data.data.appointment.id) {
        close('save')
      }
    })
    .catch((e) => {
      errorMessage.value = amLabels.value.error

      if ('response' in e && 'data' in e.response && 'data' in e.response.data) {
        if ('timeSlotUnavailable' in e.response.data.data && e.response.data.data.timeSlotUnavailable === true) {
          errorMessage.value = amLabels.value.time_slot_unavailable

          activeTab.value = 'details'
        }

        if ('customerAlreadyBooked' in e.response.data.data && e.response.data.data.customerAlreadyBooked === true) {
          errorMessage.value = amLabels.value.customer_already_booked

          activeTab.value = 'customers'
        }
      }

      alertVisibility.value = true
    })
    .finally(() => {
      loading.value = false
    })
}

function close(type) {
  store.commit('appointment/setActive', false)

  emits(type)
}

onMounted(() => {
  store.commit('appointment/setActive', true)

  if (store.getters['appointment/getId']) {
    savedAppointment.value = JSON.parse(
      JSON.stringify(store.getters['appointment/getAppointmentData'])
    )

    let locations = store.getters['entities/filteredLocations'](
      {
        categoryId: null,
        serviceId: null,
        providerId: store.getters['appointment/getProviderId'],
        locationId: null,
      }
    )

    let services = store.getters['entities/filteredServices'](
      {
        categoryId: null,
        serviceId: null,
        providerId: store.getters['appointment/getProviderId'],
        locationId: null,
      }
    )

    let detachedEntity = (
      store.getters['appointment/getId'] &&
      store.getters['appointment/getServiceId'] &&
      services.map(i => i.id).indexOf(store.getters['appointment/getServiceId']) === -1
    ) || (
      store.getters['appointment/getId'] &&
      store.getters['appointment/getLocationId'] &&
      locations.map(i => i.id).indexOf(store.getters['appointment/getLocationId']) === -1
    )

    if (!detachedEntity) {
      changedSlotCondition(true)
    }
  }

  if (store.getters['appointment/getServiceId']) {
    store.dispatch(
      'appointment/updateEmployeeService',
      store.getters['appointment/getServiceId']
    )
    store.dispatch('appointment/addAllCustomFieldsFormRules', {
      label: amLabels.value.required_field,
    })
  }
})

// * Colors
let amColors = inject('amColors')
// * CSS Variables
let cssVars = computed(() => {
  return {
    '--am-c-capai-text': amColors.value.colorMainText,
    '--am-c-capai-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
    '--am-c-capai-primary': amColors.value.colorPrimary,

    '--am-c-cust-no1': amColors.value.colorMainText,
    '--am-c-cust-no1-bgr': useColorTransparency(
      amColors.value.colorMainText,
      0.1
    ),
    '--am-c-cust-no2': amColors.value.colorWarning,
    '--am-c-cust-no2-bgr': useColorTransparency(
      amColors.value.colorWarning,
      0.1
    ),
    '--am-c-cust-no3': amColors.value.colorError,
    '--am-c-cust-no3-bgr': useColorTransparency(amColors.value.colorError, 0.1),
    '--am-c-cust-link': useColorTransparency(amColors.value.colorMainText, 0.5),
    '--am-c-cust-text': amColors.value.colorMainText,
  }
})
</script>

<script>
export default {
  name: 'CabinetAppointment',
}
</script>

<style lang="scss">
@mixin am-cabinet-appointment {
  // am    - amelia
  // capai - cabinet-panel-appointment-item
  .am-capai {

    // Customer
    &-customer {
      &__name {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 18px;
        font-weight: 500;
        line-height: 1.555556;
        color: var(--am-c-cust-text);

        &.am-pb-16 {
          padding-bottom: 16px;
        }

        &[class*='am-no-show-'] {
          border: 1px solid var(--am-c-no-show);
          padding: 0 8px 0 4px;
          border-radius: 6px;
          background-color: var(--am-c-no-show-bgr);
        }

        [class^='am-icon-'] {
          font-size: 28px;
          line-height: 1;
          color: var(--am-c-no-show);
        }

        &.am-no-show-1 {
          --am-c-no-show: var(--am-c-cust-no1);
          --am-c-no-show-bgr: var(--am-c-cust-no1-bgr);
        }

        &.am-no-show-2 {
          --am-c-no-show: var(--am-c-cust-no2);
          --am-c-no-show-bgr: var(--am-c-cust-no2-bgr);
        }

        &.am-no-show-3 {
          --am-c-no-show: var(--am-c-cust-no3);
          --am-c-no-show-bgr: var(--am-c-cust-no3-bgr);
        }
      }

      // Customer Email and Phone
      &__data {
        display: flex;
        align-items: center;
        gap: 0 4px;

        &.am-rw {
          &-420 {
            width: 100%;
          }
        }

        &-wrapper {
          width: 100%;
          display: flex;
          align-items: center;
          gap: 8px;
          padding: 4px 0 16px;

          &.am-rw {
            &-420 {
              flex-wrap: wrap;
              gap: 4px;
            }
          }
        }

        a {
          font-size: 14px;
          font-weight: 400;
          line-height: 1.42857;
          color: var(--am-c-cust-link);
          text-decoration: none;
        }

        [class^='am-icon-'] {
          font-size: 28px;
          line-height: 0;
          color: var(--am-c-cust-text);
        }
      }
    }

    // Collapse
    .am-capai-collapse {
      display: flex;
      flex-direction: column;
      gap: 8px;

      &__item {
        padding: 16px;
        border: 1px solid var(--am-c-inp-border);
        border-radius: 8px;
        background-color: var(--am-c-main-bgr);

        .el-collapse {
          &-item {
            &__header {
              display: flex;
              align-items: center;
              justify-content: space-between;
              height: unset;
              padding: 0;
              border: none;
              background-color: transparent;
            }
          }
        }
      }
    }

    // Divider
    .am-divider {
      width: 100%;
      height: 1px;
      background-color: var(--am-c-inp-border);
      margin: 6px 0;
    }

    &__footer {
      position: absolute;
      bottom: 16px;
      right: 32px;

      .am-button {
        margin-left: 20px;
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-appointment;
}
</style>
