<template>
  <div
    v-if="!calendarSlotsLoading"
    class="am-fs-dt__calendar"
    @pointerdown.capture="onCalendarPointerDown"
    @click.capture="onCalendarClick"
  >
    <AmAdvancedSlotCalendar
      :id="props.id"
      :slots="calendarEvents"
      :calendar-minimum-date="calendarMinimumDate"
      :calendar-maximum-date="calendarMaximumDate"
      :not-multiple="props.notMultiple"
      :end-time="props.endTime"
      :time-zone="props.timeZone"
      :show-busy-slots="props.showBusySlots"
      :show-estimated-pricing="props.showEstimatedPricing"
      :show-indicator-pricing="props.showIndicatorPricing"
      :show-slot-pricing="props.showSlotPricing"
      :show-people-waiting="props.showPeopleWaiting"
      :nested-item="nested"
      :label-slots-selected="props.labelSlotsSelected"
      :label-waiting-list="props.labelWaitingList"
      :busyness="busyness"
      :date="props.date"
      :service-id="props.serviceId"
      :tax-visibility="props.taxVisibility"
      :tax-label="props.taxLabel"
      :tax-label-incl="props.taxLabelIncl"
      :period-pricing="periodPricing"
      @selected-date="setSelectedDate"
      @selected-time="setSelectedTime"
      @changed-month="changeMonth"
      @rendered-month="renderedMonth"
      @unselect-date="unselectDate"
      @selected-duration="setSelectedDuration"
      @waiting-list-slot="setWaitingListSlot"
    ></AmAdvancedSlotCalendar>
  </div>

  <!-- Skeleton -->
  <el-skeleton v-else animated class="am-skeleton-slots" :class="checkScreen ? 'am-skeleton-slots-mobile':''">
    <template #template>
      <div class="am-skeleton-slots-filters">
        <el-skeleton-item v-for="item in new Array(4)" :key="item" variant="text" />
      </div>
      <div class="am-skeleton-slots-weekdays">
        <el-skeleton-item v-for="item in new Array(7)" :key="item" variant="text" />
      </div>
      <div class="am-skeleton-slots-days">
        <el-skeleton-item v-for="item in new Array(42)" :key="item" variant="text" />
      </div>
    </template>
  </el-skeleton>
  <!-- /Skeleton -->
</template>

<script setup>
// * Import from Vue
import {
  ref,
  provide,
  inject,
  computed,
  nextTick
} from "vue";

// * Import from vuex
import { useStore } from "vuex";

//  * Dedicated component
import AmAdvancedSlotCalendar from "../../_components/advanced-slot-calendar/AmAdvancedSlotCalendar";

// * Composables
import {
  useCartHasItems,
  useCartItem,
  useCartStep,
} from "../../../assets/js/public/cart.js";
import {
  useCalendarEvents,
  useDuration,
} from "../../../assets/js/common/appointments.js";
import {
  useAppointmentSlots,
  useSlotsPricing,
} from "../../../assets/js/public/slots";

// * Plugin Licence
let licence = inject('licence')

// * Component props
const props = defineProps({
  slotsParams: {
    type: Object,
    default: () => {}
  },
  id: {
    type: Number,
    default: 0
  },
  serviceId: {
    type: Number,
    default: 0
  },
  date: {
    type: String,
    default: ''
  },
  loadCounter: {
    type: Number,
    default: 0
  },
  preselectSlot: {
    type: Boolean,
    default: false
  },
  notMultiple: {
    type: Boolean,
    default: true
  },
  endTime: {
    type: Boolean,
    default: true
  },
  timeZone: {
    type: Boolean,
    default: true
  },
  labelSlotsSelected: {
    type: String,
    default: ''
  },
  labelWaitingList: {
    type: String,
    default: ''
  },
  fetchedSlots: {
    type: Object,
    default: () => {}
  },
  inCollapse: {
    type: Boolean,
    default: false
  },
  showBusySlots: {
    type: Boolean,
    default: false
  },
  showEstimatedPricing: {
    type: Boolean,
    default: false
  },
  showIndicatorPricing: {
    type: Boolean,
    default: false
  },
  showPeopleWaiting: {
    type: Boolean,
    default: true,
  },
  showSlotPricing: {
    type: Boolean,
    default: false
  },
  isPackage: {
    type: Boolean,
    default: false
  },
  taxVisibility: {
    type: Boolean,
    default: false
  },
  taxLabel: {
    type: String,
    default: ''
  },
  taxLabelIncl: {
    type: String,
    default: ''
  },
  disableWaitingList: {
    type: Boolean,
    default: false
  }
})

const emits = defineEmits([
  'loadingSlots'
])

let nested = computed(() => {
  return {
    inCollapse: props.inCollapse
  }
})

const store = useStore()

let searchStart = ref(null)

let searchEnd = ref(null)

let selectedYearMonth = ref('')

function loadSlots (customCallback) {
  let range = useRange(store)

  if (range) {
    searchStart.value = range.start

    searchEnd.value = range.end
  }

  getSlots(customCallback)
}

watch(() => props.loadCounter, () => {
  loadSlots(() => {})
})

let cWidth = inject('containerWidth', 0)

let checkScreen = computed(() => cWidth.value < 560 || (cWidth.value - 240 < 520))

let busyness = computed(() => store.getters['booking/getBusyness'])

/*****************
 * Calendar Data *
 ****************/

let calendarMinimumDate = ref(null)

let calendarMaximumDate = ref(null)

let calendarSlotsLoading = ref(true)

let calendarEvents = ref([])

let calendarEventDate = ref('')

let calendarEventSlots = ref([])

let calendarEventBusySlots = ref([])

// All waiting list slots returned from backend (date->time->providers array)
let calendarWaitingListSlots = ref({})
// Waiting list times for currently selected date (array of time strings)
let calendarWaitingListTimes = ref([])

let calendarEventSlot = ref('')

let calendarStartDate = ref(null)

let calendarChangeSideBar = inject('calendarChangeSideBar')

let calendarSlotDuration = inject('calendarSlotDuration')

let calendarServiceDuration = inject('calendarServiceDuration')

let useSlotsCallback = inject('useSlotsCallback')
let useSelectedDuration = inject('useSelectedDuration')
let useSelectedDate = inject('useSelectedDate')
let useBusySlots = inject('useBusySlots')
let useSelectedTime = inject('useSelectedTime')
let useDeselectedDate = inject('useDeselectedDate')
let useRange = inject('useRange')
// Cart step controls (optional injections - not needed in customer panel)
const injectedAddCartStep = inject('addCartStep', null)
const injectedRemoveCartStep = inject('removeCartStep', null)
// Steps data (to verify if CartStep exists before add/remove)
const stepsArray = inject('stepsArray', null)
// Origin key to detect context (capc = customer panel, cape = employee panel)
const originKey = inject('originKey', null)

provide('calendarEvents', calendarEvents)

provide('calendarEventDate', calendarEventDate)

provide('calendarEventSlots', calendarEventSlots)

provide('calendarEventBusySlots', calendarEventBusySlots)

provide('calendarWaitingListTimes', calendarWaitingListTimes)

provide('calendarWaitingListSlots', calendarWaitingListSlots)

provide('calendarEventSlot', calendarEventSlot)

provide('calendarStartDate', calendarStartDate)

provide('calendarChangeSideBar', calendarChangeSideBar)

// Capture DOM clicks inside calendar to compare real day-number clicks vs. gap/padding
function onCalendarClick (e) {
  if (iOS()) return
  let target = e.target
  let cell = target && typeof target.closest === 'function'
    ? target.closest('td[data-date],td.fc-day,td.fc-daygrid-day')
    : null
  if (!cell) return

  let dateAttr = (cell.dataset && cell.dataset.date) || cell.getAttribute('data-date')
  if (!dateAttr) return

  const classList = cell.classList ? Array.from(cell.classList) : []
  const isDisabledCell = classList.some(cls => cls && cls.toLowerCase().includes('disabled'))
  if (isDisabledCell) {
    return
  }

  // Toggle off when clicking the already selected date
  if (dateAttr === calendarEventDate.value) {
    unselectDate()
    if (typeof e.stopPropagation === 'function') e.stopPropagation()
    if (typeof e.preventDefault === 'function') e.preventDefault()
    return
  }

  // Always treat day-cell click as selecting that date
  setSelectedDate(dateAttr)
  if (typeof e.stopPropagation === 'function') e.stopPropagation()
  if (typeof e.preventDefault === 'function') e.preventDefault()
}

// Stop early pointer/mouse events from reaching the inner calendar when they would toggle unselect
function onCalendarPointerDown (e) {
  if (iOS()) return
  let target = e.target
  let cell = target && typeof target.closest === 'function'
    ? target.closest('td[data-date],td.fc-day,td.fc-daygrid-day')
    : null
  if (!cell) return

  const classList = cell.classList ? Array.from(cell.classList) : []
  const isDisabledCell = classList.some(cls => cls && cls.toLowerCase().includes('disabled'))
  if (isDisabledCell) {
    return
  }
}
// iOS detection helper
function iOS() {
  return (
    [
      'iPad Simulator',
      'iPhone Simulator',
      'iPod Simulator',
      'iPad',
      'iPhone',
      'iPod',
    ].includes(navigator.platform) ||
    // iPad on iOS 13 detection
    (navigator.userAgent.includes('Mac') && 'ontouchend' in document)
  )
}
// Helper: prune waiting list times using backend minimumDateTime and current time (today)
function filterWaitingListTimes (dateStr, timesArray, minimumDateTime) {
  if (!Array.isArray(timesArray) || !timesArray.length) return []
  const todayStr = new Date().toISOString().slice(0,10)
  let minTimeForDate = null
  if (minimumDateTime) {
    const parts = minimumDateTime.split(' ')
    if (parts.length === 2) {
      const [minDate, minTime] = parts
      if (minDate === dateStr) {
        minTimeForDate = minTime.slice(0,5) // HH:mm
      }
    }
  }
  if (dateStr === todayStr) {
    const now = new Date()
    const nowTime = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`
    if (!minTimeForDate || nowTime.localeCompare(minTimeForDate) > 0) {
      minTimeForDate = nowTime
    }
  }
  let filtered = timesArray
  if (minTimeForDate) {
    filtered = filtered.filter(t => t.localeCompare(minTimeForDate) > 0)
  }
  return filtered.sort((a,b) => a.localeCompare(b))
}


/*********
 * Other *
 ********/

let periodPricing = ref({})

function setSelectedDuration (value) {
  store.commit('booking/setBookingDuration', value)

  let cartItem = useCartItem(store)

  let service = store.getters['entities/getService'](cartItem.serviceId)

  let extrasIds = store.getters['booking/getSelectedExtras'].map(i => i.extraId)

  calendarSlotDuration.value = useDuration(value, service.extras.filter(i => extrasIds.includes(i.id)))

  calendarServiceDuration.value = value

  nextTick(() => {
    getSlots(() => {})
  })
}

function setSelectedDate (value) {
  calendarEventSlots.value = useSelectedDate(
    store,
    value,
    {
      start: searchStart.value,
      end: searchEnd.value,
    }
  )

  calendarEventBusySlots.value = useBusySlots(store)

  // Derive waiting list times for the selected date
  if (!skipWaitingListSlots() && value && (value in calendarWaitingListSlots.value)) {
    calendarWaitingListTimes.value = filterWaitingListTimes(value, Object.keys(calendarWaitingListSlots.value[value] || {}), calendarMinimumDate.value)
  } else {
    calendarWaitingListTimes.value = []
  }

  if (props.preselectSlot && calendarEventSlots.value.length) {
    setSelectedTime(calendarEventSlots.value[0])
  }

  calendarEventDate.value = value
}

function setSelectedTime (value) {
  useSelectedTime(store, value)

  calendarEventSlot.value = value
}

function setWaitingListSlot (isWaiting, peopleWaiting, providerId = null) {
  store.commit('appointmentWaitingListOptions/setIsWaitingListSlot', !!isWaiting)
  store.commit('appointmentWaitingListOptions/setPeopleWaiting', peopleWaiting)
  store.commit('appointmentWaitingListOptions/setSelectedProviderId', providerId)
  // Determine if CartStep currently present
  let hasCartStep = false
  if (stepsArray && Array.isArray(stepsArray.value)) {
    hasCartStep = stepsArray.value.some(s => s && s.name === 'CartStep')
  }
  if (isWaiting) {
    // Remove cart step only if it exists
    if (hasCartStep && injectedRemoveCartStep && typeof injectedRemoveCartStep.removeCartStep === 'function') {
      injectedRemoveCartStep.removeCartStep()
    }
  } else {
    // Re-add cart step only if missing and cart feature enabled
    if (
      !props.disableWaitingList && !props.isPackage && !hasCartStep && useCartStep(store)
      && injectedAddCartStep && typeof injectedAddCartStep.addCartStep === 'function'
    ) {
      injectedAddCartStep.addCartStep()
      // Retry once on next tick in case steps array mutates asynchronously
      nextTick(() => {
        let existsAfter = stepsArray && Array.isArray(stepsArray.value) && stepsArray.value.some(s => s && s.name === 'CartStep')
        if (!existsAfter) {
          if (useCartStep(store)) {
            injectedAddCartStep.addCartStep()
          }
        }
      })
    }
  }
}

// Skip waiting list slots for: recurring / cart / package / customer panel (capc)
function skipWaitingListSlots () {
  return props.disableWaitingList || props.isPackage || useCartHasItems(store) || (originKey && originKey.value === 'capc')
}

function unselectDate () {
  useDeselectedDate(store)

  calendarEventSlots.value = []

  calendarEventSlot.value = ''

  calendarEventDate.value = ''

  calendarEventBusySlots.value = []

  calendarWaitingListTimes.value = []
}

function changeMonth (yearMonth) {
  selectedYearMonth.value = yearMonth

  getSlots(() => {})
}

function renderedMonth (data) {
  searchStart.value = data.start

  searchEnd.value = data.end
}

function getSlotsCallback (slots, occupied, minimumDateTime, maximumDateTime, busyness, appCount, lastBookedProviderId, customCallback, waitingListSlots = {}) {
  calendarMinimumDate.value = minimumDateTime
  calendarMaximumDate.value = maximumDateTime

  calendarEvents.value = useCalendarEvents(slots, waitingListSlots)

  calendarWaitingListSlots.value = waitingListSlots || {}

  let result = useSlotsCallback(
    store,
    slots,
    occupied,
    minimumDateTime,
    maximumDateTime,
    busyness,
    appCount,
    lastBookedProviderId,
    searchStart,
    searchEnd
  )

  if (props.serviceId) {
    let service = store.getters['entities/getService'](props.serviceId)

    let periodPricingResult = !props.isPackage && service.customPricing.enabled === 'period' && !licence.isLite && !licence.isStarter && !licence.isBasic
      ? useSlotsPricing(store, slots, service.id)
      : null

    periodPricing.value = periodPricingResult ? periodPricingResult : null
  }

  if ('calendarStartDate' in result) {
    calendarStartDate.value = selectedYearMonth.value ? selectedYearMonth.value + '-01' : result.calendarStartDate
  }

  if ('calendarEventSlot' in result) {
    calendarEventSlot.value = result.calendarEventSlot
  }

  if ('calendarEventSlots' in result) {
    calendarEventSlots.value = result.calendarEventSlots
  }

  if ('calendarEventDate' in result) {
    calendarEventDate.value = result.calendarEventDate
  }

  calendarEventBusySlots.value = calendarEventDate.value ? useBusySlots(store) : []

  // Refresh waiting list times for currently selected date (clear if none)
  if (!skipWaitingListSlots()) {
    const selectedDate = store.getters['booking/getMultipleAppointmentsDate']
    if (selectedDate && calendarWaitingListSlots.value[selectedDate]) {
      calendarWaitingListTimes.value = filterWaitingListTimes(selectedDate, Object.keys(
        calendarWaitingListSlots.value[selectedDate] || {}
      ), minimumDateTime)
    } else {
      calendarWaitingListTimes.value = []
    }
  } else {
    calendarWaitingListTimes.value = []
  }

  nextTick(() => {
    customCallback()

    calendarSlotsLoading.value = false

    emits('loadingSlots', false)
  })
}

function getSlots (customCallback) {
  calendarSlotsLoading.value = true

  emits('loadingSlots', true)

  useAppointmentSlots(
    Object.assign(
      {
        startDateTime: searchStart.value,
        endDateTime: searchEnd.value,
      },
      props.slotsParams
    ),
    props.fetchedSlots,
    getSlotsCallback,
    customCallback,
    store.getters['appointmentWaitingListOptions/getOptions']
  )
}

function unsetData () {
  calendarEventSlots.value = []

  calendarEventSlot.value = ''
}

defineExpose({
  loadSlots,
  unsetData,
  calendarSlotsLoading
})
</script>

<script>
export default {
  name: 'CalendarBlock',
}
</script>

<style lang="scss">
// am -- amelia
// fs -- form steps

.amelia-v2-booking #amelia-container {
  // Amelia Form Steps
  .am-fs {
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
  }

  // Skeleton
  .am-skeleton-slots {

    &-mobile {
      padding: 0px;

      .am-skeleton-slots-days {
        gap: 6px;

        .el-skeleton__item {
          height: 28px;
          max-width: 56px;
        }
      }

    }

    &-filters {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      padding: 0 0 24px;

      .el-skeleton__item {
        height: 36px;
        width: 20%;
      }

      :first-child {
        width: 26%;
        margin-right: 16px;
      }

      :last-child {
        width: 16%;
        margin-left: 16px;
      }
    }

    &-weekdays {
      padding-bottom: 12px;
      display: flex;
      flex-direction: row;
      justify-content: space-around;

      .el-skeleton__item {
        max-width: 30px;
        height: 24px;
      }
    }

    &-days {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr;
      gap: 8px;

      .el-skeleton__item {
        margin: 0 1.5px;
        height: 40px;
        max-width: 56px;
      }
    }
  }
}
</style>
