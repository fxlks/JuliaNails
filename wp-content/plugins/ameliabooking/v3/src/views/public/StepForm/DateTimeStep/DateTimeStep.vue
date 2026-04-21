<template>
  <div
    ref="dateTimeRef"
    class="am-fs-dt__calendar"
    :class="[props.globalClass, {'am-oxvisible': (recurringPopupVisibility || packagesVisibility)}]"
    tabindex="0"
  >
    <div v-if="limitPerEmployeeError" ref="limitError" class="am-fs__payments-error" >
      <AmAlert
        type="error"
        :title="limitPerEmployeeError"
        :show-icon="true"
        :closable="false"
      >
      </AmAlert>
    </div>

    <Calendar
      :id="0"
      :preselect-slot="false"
      :load-counter="loadCounter"
      :end-time="amCustomize.dateTimeStep.options.endTimeVisibility.visibility"
      :time-zone="amCustomize.dateTimeStep.options.timeZoneVisibility.visibility"
      :show-busy-slots="busyTimeSlotsVisibility"
      :show-estimated-pricing="estimatedPricingVisibility"
      :show-indicator-pricing="indicatorPricingVisibility"
      :show-slot-pricing="slotPricingVisibility"
      :show-people-waiting="peopleWaitingVisibility"
      :label-slots-selected="amLabels.date_time_slots_selected"
      :label-waiting-list="amLabels.waiting_list"
      :fetched-slots="null"
      :service-id="cartItem ? cartItem.serviceId : 0"
      :date="date"
      :slots-params="slotsParams"
      :tax-visibility="taxVisibility"
      :tax-label="amLabels.total_tax_colon"
      :tax-label-incl="amLabels.incl_tax"
      :is-package="false"
    ></Calendar>

    <!-- Recurring Appointment -->
    <AmSlidePopup
      v-if="service.recurringCycle !== 'disabled' && notLastDay && cart.length <= 1"
      :visibility="recurringPopupVisibility"
      class="am-fs-dt__calendar__recurring"
    >
      <div class="am-fs-dt__rec_popup">
        <p>
          {{recurringLabels.repeat_appointment}}
        </p>
        <p v-if="amCustomize.recurringPopup.options.content.visibility">
          {{recurringLabels.repeat_appointment_quest}}
        </p>
      </div>
      <template #footer>
        <AmButton
          category="secondary"
          :type="amCustomize.recurringPopup.options.secondaryButton.buttonType"
          @click="recurringStep(false)"
        >
          {{recurringLabels.no}}
        </AmButton>
        <AmButton
          :type="amCustomize.recurringPopup.options.primaryButton.buttonType"
          @click="recurringStep(true)"
        >
          {{recurringLabels.yes}}
        </AmButton>
      </template>
    </AmSlidePopup>
    <!--/ Recurring Appointment -->

    <!-- Packages Popup -->
    <PackagesPopup @continue-with-service="packagesVisibility = false"></PackagesPopup>
    <!--/ Packages Popup -->

  </div>

</template>

<script setup>
import { useStore } from "vuex";
import {
  ref,
  provide,
  inject,
  computed,
  onMounted,
  watchEffect,
  reactive
} from "vue";
import AmButton from "../../../_components/button/AmButton.vue";
import AmSlidePopup from "../../../_components/slide-popup/AmSlidePopup.vue";
import Calendar from "../../Parts/Calendar.vue";
import {
  useAppointmentParams,
  useRange,
  useSelectedDuration,
  useSelectedDate,
  useSelectedTime,
  useDeselectedDate,
  useSlotsCallback,
} from "../../../../assets/js/public/slots.js"
import { useFormattedPrice } from '../../../../assets/js/common/formatting.js'
import {
  useFillAppointments,
  useDuration,
  usePaymentError,
  useBusySlots,
} from "../../../../assets/js/common/appointments.js";
import {
  useCart,
  useCartItem
} from '../../../../assets/js/public/cart'
import PackagesPopup from "../PakagesStep/parts/PackagesPopup";
import { useCurrentUser } from "../../../../assets/js/public/user";
import { defaultCustomizeSettings } from "../../../../assets/js/common/defaultCustomize";
import AmAlert from "../../../_components/alert/AmAlert.vue";
import { useScrollTo } from "../../../../assets/js/common/scrollElements";
import { useTaxVisibility } from "../../../../assets/js/common/pricing";

let props = defineProps({
  globalClass: {
    type: String,
    default: ''
  },
  showCart: {
    type: Boolean,
    default: false
  }
})

let dateTimeRef = ref(null)
provide('formWrapper', dateTimeRef)

let amCustomize = inject('amCustomize')

// * Busy Time Slots Visibility
let busyTimeSlotsVisibility = computed(() => {
  if ('busyTimeSlotsVisibility' in amCustomize.dateTimeStep.options) {
    return amCustomize.dateTimeStep.options.busyTimeSlotsVisibility.visibility
  }

  return defaultCustomizeSettings.sbsNew.dateTimeStep.options.busyTimeSlotsVisibility.visibility
})

// * Estimated Pricing Visibility
let estimatedPricingVisibility = computed(() => {
  if ('estimatedPricingVisibility' in amCustomize.dateTimeStep.options) {
    return amCustomize.dateTimeStep.options.estimatedPricingVisibility.visibility
  }

  return defaultCustomizeSettings.sbsNew.dateTimeStep.options.estimatedPricingVisibility.visibility
})

// * Indicator Pricing Visibility
let indicatorPricingVisibility = computed(() => {
  if ('indicatorPricingVisibility' in amCustomize.dateTimeStep.options) {
    return amCustomize.dateTimeStep.options.indicatorPricingVisibility.visibility
  }

  return defaultCustomizeSettings.sbsNew.dateTimeStep.options.indicatorPricingVisibility.visibility
})

// * Slot Pricing Visibility
let slotPricingVisibility = computed(() => {
  if ('slotPricingVisibility' in amCustomize.dateTimeStep.options) {
    return amCustomize.dateTimeStep.options.slotPricingVisibility.visibility
  }

  return defaultCustomizeSettings.sbsNew.dateTimeStep.options.slotPricingVisibility.visibility
})

// * Waiting List - Show People Waiting
let peopleWaitingVisibility = computed(() => {
  if ('peopleWaitingVisibility' in amCustomize.dateTimeStep.options) {
    return amCustomize.dateTimeStep.options.peopleWaitingVisibility.visibility
  }

  return defaultCustomizeSettings.sbsNew.dateTimeStep.options.peopleWaitingVisibility.visibility
})

const store = useStore()

// * Amelia Settings
const amSettings = inject('settings')

// * Labels
const globalLabels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...globalLabels})

  if (amSettings.customizedData && amSettings.customizedData.sbsNew && amSettings.customizedData.sbsNew.dateTimeStep.translations) {
    let customizedLabels = amSettings.customizedData.sbsNew.dateTimeStep.translations
    Object.keys(customizedLabels).forEach(labelKey => {
      if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
        computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
})

let recurringLabels = computed(() => {
  let computedLabels = reactive({...globalLabels})

  if (amSettings.customizedData && amSettings.customizedData.sbsNew && amSettings.customizedData.sbsNew.recurringPopup.translations) {
    let customizedLabels = amSettings.customizedData.sbsNew.recurringPopup.translations
    Object.keys(customizedLabels).forEach(labelKey => {
      if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
        computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
})

let date = computed(() => {
  return cartItem.value &&
      cartItem.value.index !== '' &&
      cartItem.value.services[cartItem.value.serviceId].list.length &&
      cartItem.value.services[cartItem.value.serviceId].list[cartItem.value.index].date
})

let time = computed(() => {
  return cartItem.value &&
    cartItem.value.index !== '' &&
    cartItem.value.services[cartItem.value.serviceId].list.length &&
    cartItem.value.services[cartItem.value.serviceId].list[cartItem.value.index].time
})

let cart = useCart(store)

let cartItem = computed(() => useCartItem(store))

let taxVisibility = computed(() => {
  let visible = amCustomize.dateTimeStep.options.tax?.visibility ?? true
  if (cartItem.value && visible) {
    return useTaxVisibility(store, cartItem.value.serviceId, 'service')
  }
  return false
})

let slotsParams = computed(() => {
  return useAppointmentParams(store)
})

// * Package

let stepIndex = inject('stepIndex')
let shortcodeData = inject('shortcodeData')

let packagesOptions = computed(() => store.getters['entities/filteredPackages'](
    store.getters['booking/getSelection']
))

let packagesVisibility = ref(
  packagesOptions.value.length > 0 &&
  shortcodeData.value.show !== 'services' &&
  stepIndex.value === 0 &&
  cart.length <= 1
)
provide('packagesVisibility', packagesVisibility)


/**************
 * Navigation *
 *************/

const {
  nextStep,
  footerButtonReset,
  footerBtnDisabledUpdater,
  footerButtonClicked,
  headerButtonPreviousClicked
} = inject('changingStepsFunctions', {
  nextStep: () => {},
  footerButtonReset: () => {},
  footerBtnDisabledUpdater: () => {},
  footerButtonClicked: {
    value: false
  },
  headerButtonPreviousClicked: {
    value: false
  },
})

let limitPerEmployeeError = ref('')
let limitError = ref(null)

watchEffect(() => {
  if (footerButtonClicked.value && date.value && time.value) {
    footerButtonReset()
    limitPerEmployeeError.value = ''

    usePaymentError(store, '')

    if (
      service.recurringCycle !== 'disabled' && service.recurringCycle !== null && notLastDay.value && cart.length <= 1
      && !store.getters['appointmentWaitingListOptions/getIsWaitingListSlot']
    ) {
      recurringPopupVisibility.value = true
    } else {
      let bookingFailed = useFillAppointments(store)

      if (bookingFailed) {
        limitPerEmployeeError.value = amLabels.value.employee_limit_reached
      } else {
        nextStep()
      }
    }
  }
})

watchEffect(() => {
  if (limitPerEmployeeError.value && limitError.value) {
      useScrollTo(dateTimeRef.value, limitError.value, 0, 300)
  }
})

watchEffect(() => {
  footerBtnDisabledUpdater(!date.value || !time.value)
}, { flush: 'post' })


/*****************
 * Calendar Data *
 ****************/

let loadCounter = ref(0)

let calendarChangeSideBar = ref(true)

provide('calendarChangeSideBar', calendarChangeSideBar)

let calendarSlotDuration = ref(0)

provide('calendarSlotDuration', calendarSlotDuration)

let calendarServiceDuration = ref(0)

provide('calendarServiceDuration', calendarServiceDuration)

let calendarServiceDurations = ref([])

provide('calendarServiceDurations', calendarServiceDurations)

provide('useSlotsCallback', useSlotsCallback)

provide('useRange', useRange)

provide('useSelectedDuration', useSelectedDuration)

provide('useBusySlots', useBusySlots)

provide('useSelectedDate', useSelectedDate)

provide('useSelectedTime', useSelectedTime)

provide('useDeselectedDate', useDeselectedDate)


/*********
 * Other *
 ********/

let service = reactive({})

let recurringPopupVisibility = ref(false)

let { goToRecurringStep } = inject('goToRecurringStep', {
  goToRecurringStep: () => {}
})

let { removeRecurringStep } = inject('removeRecurringStep', {
  removeRecurringStep: () => {}
})

let notLastDay = computed(() => {
  if (cartItem.value &&
      cartItem.value.index !== '' &&
      cartItem.value.services[cartItem.value.serviceId].list.length
  ) {
    let lastDate = store.getters['booking/getMultipleAppointmentsLastDate']
    let chosenDate = store.getters['booking/getMultipleAppointmentsDate']
    return lastDate !== chosenDate
  }
  return false
})

function recurringStep (repeat) {
  if (repeat) {
    goToRecurringStep()
  } else {
    if (cartItem.value &&
      'serviceId' in cartItem.value &&
      cartItem.value.serviceId in cartItem.value.services &&
      cartItem.value.services[cartItem.value.serviceId].list.length > 1
    ) {
      store.commit('booking/unsetRecurringItems')
    }

    limitPerEmployeeError.value = ''
    let bookingFailed = useFillAppointments(store)

    if (bookingFailed) {
      limitPerEmployeeError.value = amLabels.value.employee_limit_reached
    } else {
      nextStep()
    }
  }
}

function setDurationsPrices (service, durations) {
  if (service.customPricing.enabled === 'duration') {
    Object.keys(service.customPricing.durations).forEach((duration) => {
      if (!(duration in durations)) {
        durations[parseInt(duration)] = []
      }

      durations[parseInt(duration)].push(service.customPricing.durations[duration].price)
    })
  }

  return durations
}

function getDurations () {
  let durations = {}

  let serviceId = store.getters['booking/getServiceId']

  let employeeId = store.getters['booking/getEmployeeId']

  if (!employeeId && serviceId) {
    let employees = store.getters['entities/filteredEmployees'](
      store.getters['booking/getSelection']
    )

    employees.forEach((provider) => {
      let service = provider.serviceList.find(service => service.id === serviceId)

      durations = setDurationsPrices(service, durations)
    })
  } else {
    let employee = store.getters['entities/getEmployee'](store.getters['booking/getEmployeeId'])

    let service = employee.serviceList.find(service => service.id === serviceId)

    durations = setDurationsPrices(service, durations)
  }

  let result = []

  Object.keys(durations).sort((a, b) => a - b).forEach((duration) => {
    let min = Math.min(...durations[duration])

    let max = Math.max(...durations[duration])

    result.push(
      {
        duration: parseInt(duration),
        priceLabel: min !== max
          ? useFormattedPrice(min) + ' - ' + useFormattedPrice(max)
          : (min === 0 ? '' : useFormattedPrice(min))
      }
    )
  })

  return result
}

onMounted(() => {
  if (props.showCart && !store.getters['booking/getShownCart']) {
    return
  }

  service = store.getters['entities/getService'](
    cartItem.value.serviceId
  )

  calendarServiceDurations.value = getDurations()

  let serviceDuration = store.getters['booking/getBookingDuration']

  serviceDuration = serviceDuration && calendarServiceDurations.value.filter(i => i.duration === serviceDuration).length
    ? serviceDuration : service.duration

  store.commit('booking/setBookingDuration', serviceDuration)

  let extrasIds = store.getters['booking/getSelectedExtras'].map(i => i.extraId)

  calendarSlotDuration.value = useDuration(serviceDuration, service.extras.filter(i => extrasIds.includes(i.id)))

  calendarServiceDuration.value = serviceDuration

  let index = store.getters['booking/getCartItemIndex']

  let items = store.getters['booking/getAllMultipleAppointments']

  let hasItem = index in items

  if (
    (hasItem && cartItem.value.services[cartItem.value.serviceId].list.length > 1) ||
    store.getters['appointmentWaitingListOptions/getIsWaitingListSlot']
  ) {
    removeRecurringStep()
  }

  useCurrentUser(store, shortcodeData.value.hasApiCall)

  if ('serviceId' in cartItem.value &&
    cartItem.value.serviceId in cartItem.value.services &&
    cartItem.value.index in cartItem.value.services[cartItem.value.serviceId].list
  ) {
    cartItem.value.services[cartItem.value.serviceId].list[cartItem.value.index].providerId = null

    cartItem.value.services[cartItem.value.serviceId].list[cartItem.value.index].locationId = null
  }

  loadCounter.value++
})
</script>

<script>
export default {
  name: 'DateTimeStep',
  key: 'dateTimeStep',
  sidebarData: {
    label: 'date_time',
    icon: 'date-time',
    stepSelectedData: [],
    finished: false,
    selected: false,
  }
}
</script>

<style lang="scss">
// am -- amelia
// fs -- form steps

.amelia-v2-booking #amelia-container {
  // Amelia Form Steps
  .am-fs {
    &__main {
      &-content.am-fs-dt__calendar {
        &.am-oxvisible {
          overflow-x: visible;
        }
      }
    }

    // Container Wrapper
    &__main {
      &-heading {
        &-inner {
          display: flex;
          align-items: center;

          .am-heading-prev {
            margin-right: 12px;
          }
        }
      }
      &-inner {
        &#{&}-dt {
          padding: 0 20px;
        }
      }
    }

    &-dt__rec_popup {
      margin: 16px 0 48px 0;
      & > p {
        font-size: 15px;
        line-height: 1.6;
        color: var(--am-c-main-text);

        &:first-child {
          font-weight: 500;
          margin-bottom: 7px;
        }

        &:last-child {
          font-weight: 400;
        }
      }
    }
  }
}
</style>
