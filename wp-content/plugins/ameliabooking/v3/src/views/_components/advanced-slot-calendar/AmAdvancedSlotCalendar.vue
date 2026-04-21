<template>
  <div class="am-advsc__wrapper" :style="cssVars">
    <div
      v-show="headerMonthOptions.length && calendarStartDate"
      class="am-advsc__header"
      :class="[
        { 'am-advsc__header-mobile': checkScreen },
        { 'am-advsc__header-mobile-s': mobileS },
      ]"
    >
      <AmSelect
        v-model="calMonth"
        size="medium"
        aria-label="month selection"
        @change="changeMonth(calYear + '-' + calMonth)"
      >
        <AmOption
          v-for="month in headerMonthOptions"
          v-show="month.available"
          :key="month.value"
          :value="month.value"
          :label="month.label"
        />
      </AmSelect>
      <AmSelect
        v-model="calYear"
        size="medium"
        aria-label="year selection"
        @change="changeYear()"
      >
        <AmOption
          v-for="year in headerYearOption"
          :key="year"
          :value="year"
          :label="year"
        />
      </AmSelect>

      <AmButtonGroup>
        <AmButton
          aria-label="previous month"
          category="secondary"
          type="plain"
          size="medium"
          icon-only
          :icon="AmIconArrowLeft"
          @click="prevMonth"
        />
        <AmButton
          aria-label="next month"
          category="secondary"
          type="plain"
          size="medium"
          icon-only
          :icon="AmIconArrowRight"
          @click="nextMonth"
        />
      </AmButtonGroup>
    </div>

    <div class="am-advsc__duration">
      <AmSelect
        v-if="calendarServiceDurations.length > 1"
        v-model="calendarServiceDuration"
        @change="selectDuration"
      >
        <AmOption
          v-for="item in calendarServiceDurations"
          :key="item.duration"
          :label="
            useSecondsToDuration(item.duration, amLabels.h, amLabels.min) +
            (item.priceLabel === ''
              ? ''
              : '(' + item.priceLabel + ')' + taxText)
          "
          :value="item.duration"
        >
          <AmOptionTemplate1
            :identifier="item.duration"
            :label="
              useSecondsToDuration(item.duration, amLabels.h, amLabels.min)
            "
            :price-string="`${item.priceLabel} ${taxText}`"
            icon-string="clock"
          ></AmOptionTemplate1>
        </AmOption>
      </AmSelect>
    </div>

    <div v-if="props.timeZone" class="am-advsc__time-zone">
      <span>{{ timeZoneString }}</span>
    </div>

    <div
      v-if="
        props.periodPricing &&
        props.periodPricing.price &&
        props.showEstimatedPricing
      "
      class="am-advsc__period-pricing"
    >
      <span
        v-if="props.periodPricing.price.low !== null"
        class="period-pricing-low"
        >{{ useFormattedPrice(props.periodPricing.price.low)
        }}{{ !props.periodPricing.price.uniqueMin ? '+' : '' }}</span
      >
      <span
        v-if="props.periodPricing.price.mid !== null"
        class="period-pricing-mid"
        >{{ useFormattedPrice(props.periodPricing.price.mid)
        }}{{ !props.periodPricing.price.uniqueMid ? '+' : '' }}</span
      >
      <span
        v-if="props.periodPricing.price.high !== null"
        class="period-pricing-high"
        >{{ useFormattedPrice(props.periodPricing.price.high)
        }}{{ !props.periodPricing.price.uniqueMax ? '+' : '' }}</span
      >
    </div>

    <FullCalendar
      ref="advCalendarRef"
      class="am-advsc"
      :am-mobile="checkScreen"
      :am-mobile-s="mobileS"
      :options="options"
    ></FullCalendar>

    <div
      v-if="shouldShowSlots"
      ref="slotsWrapper"
      class="am-advsc__slots-wrapper"
    >
      <div class="am-advsc__slots-heading">
        {{ slotsHeading }}
      </div>
      <div class="am-advsc__slots">
        <!-- Unified chronological list (regular + waiting) -->
        <div
          v-for="slotObj in mergedSlots"
          :key="(slotObj.waiting ? 'wl-' : 'std-') + slotObj.time"
          class="am-advsc__slots-item"
          :class="[
            {
              'am-advsc__slots-item__selected': calendarEventSlot === slotObj.time,
              'am-advsc__slots-item__low': calendarEventDate && props.periodPricing && props.periodPricing.dates && calendarEventDate in props.periodPricing.dates && slotObj.time in props.periodPricing.dates[calendarEventDate].slots && props.periodPricing.dates[calendarEventDate].slots[slotObj.time].type === 'low',
              'am-advsc__slots-item__high': calendarEventDate && props.periodPricing && props.periodPricing.dates && calendarEventDate in props.periodPricing.dates && slotObj.time in props.periodPricing.dates[calendarEventDate].slots && props.periodPricing.dates[calendarEventDate].slots[slotObj.time].type === 'high',
            },
            { 'am-advsc__slots-item-mobile': checkScreen },
            {
              'am-advsc__slots-item-disabled':
                calendarEventBusySlots.includes(slotObj.time) &&
                props.showBusySlots &&
                !calendarEventSlots.includes(slotObj.time) &&
                !slotObj.waiting
            },
            {'am-width-full': props.showSlotPricing},
            {'am-advsc__slots-item-waiting': slotObj.waiting}
          ]"
          tabindex="0"
          @click="
            !slotObj.waiting &&
            props.showBusySlots &&
            calendarEventBusySlots.includes(slotObj.time) &&
            !calendarEventSlots.includes(slotObj.time)
              ? null
              : slotSelected(slotObj)
          "
          @keydown.enter="
            !slotObj.waiting &&
            props.showBusySlots &&
            calendarEventBusySlots.includes(slotObj.time) &&
            !calendarEventSlots.includes(slotObj.time)
              ? null
              : slotSelected(slotObj)
          "
        >
          <div
            class="am-advsc__slots-item__inner"
            :class="{'am-contains-waiting-tag': slotObj.waiting && props.showPeopleWaiting}"
          >
            {{
              `${getFrontedFormattedTime(slotObj.time)} ${
                props.endTime
                  ? ' - ' +
                    getFrontedFormattedTime(
                      addSeconds(slotObj.time, calendarSlotDuration)
                    )
                  : ''
              }`
            }}

            <span
              v-if="
                props.showSlotPricing &&
                calendarEventDate &&
                props.periodPricing &&
                props.periodPricing.dates &&
                calendarEventDate in props.periodPricing.dates &&
                slotObj.time in
                  props.periodPricing.dates[calendarEventDate].slots &&
                props.periodPricing.dates[calendarEventDate].slots[slotObj.time]
                  .price !== null
              "
            >
              {{
                useFormattedPrice(
                  props.periodPricing.dates[calendarEventDate].slots[
                    slotObj.time
                  ].price
                )
              }}
            </span>
            <span
              v-if="slotObj.waiting && props.showPeopleWaiting"
              class="am-advsc__waiting-tag am-icon-clock"
            >
              {{ props.labelWaitingList + ' (' + slotObj.peopleWaiting + ')' }}
            </span>
          </div>
        </div>
      </div>
    </div>
    <div
      v-if="isDateSelected && !calendarEventSlots.length && !waitingListDisplayTimes.length"
      style="text-align: center"
    >
      {{ props.labelSlotsSelected }}
    </div>
  </div>
</template>

<script setup>
// * Import Libraries
import moment from 'moment'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import allLocales from '@fullcalendar/core/locales-all'

// * Import _components
import AmSelect from '../select/AmSelect.vue'
import AmOption from '../select/AmOption.vue'
import AmButtonGroup from '../button/AmButtonGroup'
import AmButton from '../button/AmButton.vue'
import AmIconArrowRight from '../icons/IconArrowRight.vue'
import AmIconArrowLeft from '../icons/IconArrowLeft.vue'
import AmOptionTemplate1 from '../../_components/select/parts/AmOptionTemplate1.vue'

// * Import from Vue
import {
  ref,
  reactive,
  onBeforeMount,
  inject,
  computed,
  watch,
  onMounted,
} from 'vue'

// * Composables
import {
  months,
  getFrontedFormattedTime,
  getFrontedFormattedDate,
  addSeconds,
  getFirstDayOfWeek,
  useSecondsToDuration,
} from '../../../assets/js/common/date'
import { shortLocale } from '../../../plugins/settings.js'
import { useColorTransparency } from '../../../assets/js/common/colorManipulation'
import { useScrollTo } from '../../../assets/js/common/scrollElements.js'
import { useSortedTimeStrings } from '../../../assets/js/common/helper.js'
import { useFormattedPrice } from '../../../assets/js/common/formatting'

/**
 * Component Props
 * @type {Readonly<ExtractPropTypes<{}>>}
 */
const props = defineProps({
  initialView: {
    type: String,
    default: 'dayGridMonth',
  },
  periodPricing: {
    type: Object,
    default: () => {},
  },
  weekDaysVisibility: {
    type: Boolean,
    default: true,
  },
  weekDaysEnabled: {
    type: Boolean,
    default: true,
  },
  notMultiple: {
    type: Boolean,
    default: true,
  },
  calendarMinimumDate: {
    type: String,
    default: '',
  },
  calendarMaximumDate: {
    type: String,
    default: '',
  },
  id: {
    type: Number,
    default: 0,
  },
  serviceId: {
    type: Number,
    default: 0,
  },
  date: {
    type: String,
    default: '',
  },
  endTime: {
    type: Boolean,
    default: true,
  },
  timeZone: {
    type: Boolean,
    default: true,
  },
  showBusySlots: {
    type: Boolean,
    default: false,
  },
  showEstimatedPricing: {
    type: Boolean,
    default: false,
  },
  showIndicatorPricing: {
    type: Boolean,
    default: false,
  },
  showSlotPricing: {
    type: Boolean,
    default: false,
  },
  showPeopleWaiting: {
    type: Boolean,
    default: true,
  },
  labelSlotsSelected: {
    type: String,
    default: '',
  },
  labelWaitingList: {
    type: String,
    default: '',
  },
  nestedItem: {
    type: Object,
    default: () => {
      return {
        inCollapse: false,
      }
    },
  },
  busyness: {
    type: Object,
    default: () => {
      return {}
    },
  },
  taxVisibility: {
    type: Boolean,
    default: false,
  },
  taxLabel: {
    type: String,
    default: '',
  },
  taxLabelIncl: {
    type: String,
    default: '',
  },
})

let slotsWrapper = ref(null)
let containerWrapper = inject('formWrapper', ref(null))

// timeZoneString: this.$root.settings.general.showClientTimeZone ? Intl.DateTimeFormat().resolvedOptions().timeZone : this.$root.settings.wordpress.timezone
const amSettings = inject('settings')
let timeZoneString = ref(
  amSettings.general.showClientTimeZone
    ? Intl.DateTimeFormat().resolvedOptions().timeZone
    : amSettings.wordpress.timezone
)

let taxText = computed(() => {
  if (props.taxVisibility) {
    if (amSettings.payments.taxes.excluded) {
      return props.taxLabel
    } else {
      return props.taxLabelIncl
    }
  }

  return ''
})

// * Calendar Reference
const advCalendarRef = ref(null)

let isDateSelected = ref(false)

// * Duration
let calendarServiceDurations = inject('calendarServiceDurations', [])

// * Labels
let amLabels = inject('labels')

// * Calendar Header
// Month
let calMonth = ref('')

let headerMonthOptions = ref([])

// Year
let calYear = ref(1)
let headerYearOption = reactive([])

function selectDuration(duration) {
  emits('selectedDuration', duration)
}

function getYearMonthString() {
  return moment(advCalendarRef.value.getApi().getDate()).format('YYYY-MM')
}

function getHeaderMonths(year) {
  let minimumDate = moment(props.calendarMinimumDate, 'YYYY-MM-DD HH:mm')

  let maximumDate = moment(props.calendarMaximumDate, 'YYYY-MM-DD HH:mm')

  let availableMonths = []

  for (let i = 1; i <= 12; i++) {
    let monthString = i < 10 ? '0'.concat(i.toString()) : i.toString()

    let monthStartDate = moment(year + '-' + monthString + '-01', 'YYYY-MM-DD')
    let monthEndDate = moment(
      year + '-' + monthString + '-01',
      'YYYY-MM-DD'
    ).endOf('month')

    availableMonths.push({
      value: monthString,
      label: months[i - 1],
      available:
        minimumDate.isSameOrBefore(monthEndDate) &&
        maximumDate.isSameOrAfter(monthStartDate),
    })
  }

  return availableMonths
}

function changeYear() {
  let availableMonths = getHeaderMonths(calYear.value)

  advCalendarRef.value
    .getApi()
    .gotoDate(
      calYear.value +
        '-' +
        availableMonths.filter((i) => i.available)[0].value +
        '-01'
    )

  emits('changedMonth', getYearMonthString())
}

function changeMonth() {
  advCalendarRef.value
    .getApi()
    .gotoDate(calYear.value + '-' + calMonth.value + '-01')

  emits('changedMonth', getYearMonthString())
}

function prevMonth() {
  advCalendarRef.value.getApi().prev()

  emits('changedMonth', getYearMonthString())
}

function nextMonth() {
  advCalendarRef.value.getApi().next()

  emits('changedMonth', getYearMonthString())
}

function renderedMonth(data) {
  emits('renderedMonth', {
    start: moment(data.start).format('YYYY-MM-DD'),
    end: moment(data.end).format('YYYY-MM-DD'),
    yearMonth: getYearMonthString(),
  })
}

let calendarStartDate = inject('calendarStartDate')

// * Amelia Calendar Options
const options = ref({
  initialDate: calendarStartDate.value,
  plugins: [dayGridPlugin, interactionPlugin],
  initialView: props.initialView,
  dayCellDidMount: function (info) {
    if (!info.el.classList.contains('am-advsc__dayGridMonth-disabled')) {
      info.el.setAttribute('tabindex', '0')
      info.el.setAttribute(
        'aria-label',
        `Select date ${info.date.toDateString()}`
      )
      info.el.setAttribute('role', 'button')
      info.el.setAttribute('aria-describedby', 'calendar-instructions')
    }

    // Enhanced keyboard navigation
    info.el.addEventListener('keydown', (e) => {
      const calendarApi = info.view.calendar

      if (e.key === 'Enter' || e.keyCode === 13 || e.key === ' ') {
        e.preventDefault()

        // Check if this date cell is disabled
        if (info.el.classList.contains('am-advsc__dayGridMonth-disabled')) {
          return
        }

        // Use moment to format the date the same way as mouse clicks do
        // This avoids timezone issues with toISOString()
        const localDateStr = moment(info.date).format('YYYY-MM-DD')

        // Build the event object manually (as FullCalendar would)
        const dateClickEvent = {
          date: info.date,
          dateStr: localDateStr, // Use local date string instead of ISO string
          allDay: true,
          dayEl: info.el,
          jsEvent: e,
          view: info.view,
        }

        // First trigger dateClick
        calendarApi.trigger('dateClick', dateClickEvent)

        // Then check for events on this date and trigger eventClick
        const events = calendarApi.getEvents()

        // Find events that occur on this specific date
        const matchingEvents = events.filter((event) => {
          if (!event.start) return false
          // Use moment to format the event start date in the same way
          const eventStartDate = moment(event.start).format('YYYY-MM-DD')
          return eventStartDate === localDateStr
        })

        // If there are events on this date, trigger eventClick for the first one
        if (matchingEvents.length > 0) {
          const eventClickData = {
            el: info.el,
            event: matchingEvents[0],
            jsEvent: e,
            view: info.view,
          }

          // Trigger the eventClick handler
          calendarApi.trigger('eventClick', eventClickData)
        }
      }
    })

    // Add focus and blur event handlers for better visual feedback
    info.el.addEventListener('focus', () => {
      if (!info.el.classList.contains('am-advsc__dayGridMonth-disabled')) {
        info.el.style.outline = '1px solid var(--am-c-cal-init)'
        info.el.style.outlineOffset = '-2px'
        info.el.style.borderRadius = '6px'
      }
    })

    info.el.addEventListener('blur', () => {
      info.el.style.outline = ''
      info.el.style.outlineOffset = ''
      info.el.style.borderRadius = ''
    })
  },
  headerToolbar: false,
  views: {
    dayGridMonth: {},
    dayGridWeek: {},
  },
  slotLabelFormat: {
    hour: 'numeric',
    minute: '2-digit',
    hour12: false,
  },
  eventTimeFormat: {
    hour: 'numeric',
    minute: '2-digit',
    hour12: false,
  },
  aspectRatio: 1.45,
  firstDay: getFirstDayOfWeek(),
  dayMaxEvents: true,
  selectLongPressDelay: 0,
  dayHeaderClassNames: calendarDayHeaderClassBuilder,
  dayCellClassNames: calendarDayClassBuilder,
  // dayCellContent: calendarDayCellContent,
  dateClick: calendarDateClick,
  eventClassNames: 'am-advsc__occupancy',
  eventContent: calendarEventContent,
  eventClick: calendarEventClick,
  // weekends: true,
  // hiddenDays: [0, 6],
  weekends: props.weekDaysVisibility,
  events: inject('calendarEvents'),
  datesSet: renderedMonth,
  locales: allLocales,
  locale: shortLocale,
})

let cWidth = inject('containerWidth', 0)
let checkScreen = computed(
  () => cWidth.value < 560 || (cWidth.value > 560 && cWidth.value < 640)
)
let mobileS = computed(() => cWidth.value < 340)

const emits = defineEmits([
  'selectedDate',
  'selectedTime',
  'renderedMonth',
  'changedMonth',
  'selectedDuration',
  'unselectDate',
  'waitingListSlot',
])

let calendarEventSlots = inject('calendarEventSlots')

let calendarEventDate = inject('calendarEventDate')

let calendarEventBusySlots = inject('calendarEventBusySlots', [])

// Waiting list times for selected date
let calendarWaitingListTimes = inject('calendarWaitingListTimes', ref([]))

let calendarEventSlot = inject('calendarEventSlot')

let calendarChangeSideBar = inject('calendarChangeSideBar', ref({}))

let calendarSlotDuration = inject('calendarSlotDuration')

let calendarServiceDuration = inject('calendarServiceDuration')

let calendarWaitingListSlots = inject('calendarWaitingListSlots')

// * Slots heading
let slotsHeading = ref(props.date ? getFrontedFormattedDate(props.date) : '')

// Computed arrays for display
const combinedStandardSlots = computed(() =>
  useSortedTimeStrings([
    ...new Set(
      calendarEventSlots.value.concat(
        props.showBusySlots ? calendarEventBusySlots.value : []
      )
    ),
  ]).filter((s) => !calendarWaitingListTimes.value.includes(s))
)

const waitingListDisplayTimes = computed(() =>
  useSortedTimeStrings(calendarWaitingListTimes.value)
)

// Merge standard and waiting list slots chronologically while preserving status
const mergedSlots = computed(() => {
  // Helper: extract people waiting for a given time on selected date
  const getPeopleWaiting = (time) => {
    try {
      const dateKey = calendarEventDate?.value
        ? moment(calendarEventDate.value).format('YYYY-MM-DD')
        : null
      if (!dateKey) return 0

      const dayObj = calendarWaitingListSlots?.value?.[dateKey]
      if (!dayObj) return 0

      const slotArr = dayObj[time]
      if (!Array.isArray(slotArr) || !slotArr.length) return 0

      const rec = slotArr[0]
      const w = rec && (typeof rec.w === 'number' ? rec.w : Number(rec.w))
      return Number.isFinite(w) ? w : 0
    } catch (e) {
      return 0
    }
  }

  const std = combinedStandardSlots.value.map((t) => ({
    time: t,
    waiting: false,
  }))

  const wl = waitingListDisplayTimes.value.map((t) => ({
    time: t,
    waiting: true,
    peopleWaiting: getPeopleWaiting(t),
  }))

  return [...std, ...wl].sort((a, b) => a.time.localeCompare(b.time))
})

// Only show slots on iOS when a date is currently selected; other platforms unchanged
const shouldShowSlots = computed(() => {
  if (iOS()) {
    return isDateSelected.value && (calendarEventSlots.value.length || waitingListDisplayTimes.value.length)
  }

  return calendarEventSlots.value.length || waitingListDisplayTimes.value.length
})

/**
 * Html class builder for calendar header day cell
 * @param data
 * @returns {string[]}
 */
function calendarDayHeaderClassBuilder(data) {
  let classCollector = [`am-advsc__${data.view.type}-header-cell`]

  // * Week days class
  if (data.date.getDay() === 0 || data.date.getDay() === 6) {
    classCollector.push(`am-advsc__${data.view.type}-header-weekend`)
  }

  return classCollector
}

watch(calendarStartDate, () => {
  const advCalendar = advCalendarRef.value.getApi()

  advCalendar.gotoDate(calendarStartDate.value)
})

// iOS: ensure first available slot auto-selects once slots load, and keep heading in sync
watch(calendarEventSlots, (val) => {
  if (!iOS() || !isDateSelected.value) return

  const hasSlots = Array.isArray(val) && val.length > 0

  if (!hasSlots) {
    slotsHeading.value = ''
    return
  }

  const currentDate = calendarEventDate?.value || null
  const needsSelection = !calendarEventSlot.value || !val.includes(calendarEventSlot.value)

  if (needsSelection) {
    calendarEventSlot.value = val[0]
    emits('selectedTime', calendarEventSlot.value)
  }

  const headingDate = currentDate ? getFrontedFormattedDate(currentDate) : currentDate
  slotsHeading.value =
    headingDate +
    (calendarEventSlot.value && val.includes(calendarEventSlot.value)
      ? ' - ' + getFrontedFormattedTime(calendarEventSlot.value)
      : '')
})

/**
 * Html class builder for calendar day cell
 * @param data
 * @returns {*[]}
 */
function calendarDayClassBuilder(data) {
  let classCollector = [`am-advsc__${data.view.type}-cell`]

  // * Week days class
  if (
    !props.weekDaysEnabled &&
    (data.date.getDay() === 0 || data.date.getDay() === 6)
  ) {
    classCollector.push(`am-advsc__${data.view.type}-weekend`)
  }

  let dateString = moment(data.date).format('YYYY-MM-DD')

  // * Determine which day has slots => [] - no slots || [...] has slots
  let eventIdentifier = options.value.events.filter(
    (item) => moment(item.start).format('YYYY-MM-DD') === dateString
  )

  // TODO - this condition should be refactorized after implementation in the system
  // * Disabled class
  if (
    (eventIdentifier.length &&
      'slotsAvailable' in eventIdentifier[0].extendedProps &&
      !eventIdentifier[0].extendedProps.slotsAvailable) ||
    !eventIdentifier.length
  ) {
    classCollector.push(`am-advsc__${data.view.type}-disabled`)
  } else if (
    props.periodPricing &&
    props.periodPricing.dates &&
    dateString in props.periodPricing.dates &&
    (props.periodPricing.dates[dateString].type === 'low' ||
      props.periodPricing.dates[dateString].type === 'high')
  ) {
    classCollector.push(
      `am-advsc__${data.view.type}-` +
        props.periodPricing.dates[dateString].type
    )
  }

  if (props.date && props.date === moment(data.date).format('YYYY-MM-DD')) {
    classCollector.push(`am-advsc__${data.view.type}-selected`)
  }

  return classCollector
}

/**
 * Click on Day Cell handler
 * @param data
 */

// * Guard repeated iOS date-clicks fired twice for a single tap
let lastIOSDateClick = ref({ date: '', ts: 0 })

// * this function must be removed and its content should be placed inside the calendarDateClick function after releasing the event calendar
function calendarDateClickFunctionality(data) {
  const advCalendar = advCalendarRef.value.getApi()
  let advCalendarNode = advCalendarRef.value.$el

  const advCalendarType = advCalendar.currentData.currentViewType

  // * Adding or Removing html class that determines selection state
  let selectedDayClass = `am-advsc__${advCalendarType}-selected`
  let disabledDayClass = `am-advsc__${advCalendarType}-disabled`

  if (data.dayEl.classList.contains(disabledDayClass)) {
    return
  }

  if (data.dayEl.classList.contains(selectedDayClass)) {
    isDateSelected.value = false

    data.dayEl.classList.remove(selectedDayClass)

    slotsHeading.value = ''

    // Only force-clear slots immediately on iOS; other platforms keep existing flow
    if (iOS()) {
      calendarEventSlots.value = []
      calendarEventSlot.value = ''
      calendarEventBusySlots.value = []
    }

    emits('unselectDate')

  } else {
    isDateSelected.value = true

    emits('selectedDate', data.dateStr)

    if (
      calendarEventSlot.value &&
      !calendarEventSlots.value.includes(calendarEventSlot.value)
    ) {
      calendarEventSlot.value = calendarEventSlots.value[0]
      emits('selectedTime', calendarEventSlot.value)
    } else if (
      calendarEventSlot.value &&
      calendarEventSlots.value.includes(calendarEventSlot.value)
    ) {
    } else if (calendarEventSlots.value.length) {
      calendarEventSlot.value = calendarEventSlots.value[0]
      emits('selectedTime', calendarEventSlot.value)
    }

    slotsHeading.value =
      (data.dateStr ? getFrontedFormattedDate(data.dateStr) : data.dateStr) +
      (calendarEventSlots.value.includes(calendarEventSlot.value)
        ? ' - ' + getFrontedFormattedTime(calendarEventSlot.value)
        : '')

    if (advCalendarNode.querySelectorAll(`.${selectedDayClass}`).length) {
      for (
        let i = 0;
        i < advCalendarNode.querySelectorAll(`.${selectedDayClass}`).length;
        i++
      ) {
        advCalendarNode
          .querySelectorAll(`.${selectedDayClass}`)
          [i].classList.remove(selectedDayClass)
      }
    }

    if (!data.dayEl.classList.contains(disabledDayClass)) {
      data.dayEl.classList.add(selectedDayClass)
    }

    if (iOS()) {
      setTimeout(() => {
        if (
          containerWrapper.value &&
          calendarEventSlots.value.length &&
          slotsWrapper.value
        ) {
          useScrollTo(
            containerWrapper.value,
            slotsWrapper.value,
            20,
            300,
            props.nestedItem
          )
        }
      }, 500)
    } else {
      if (
        containerWrapper.value &&
        calendarEventSlots.value.length &&
        slotsWrapper.value
      ) {
        useScrollTo(
          containerWrapper.value,
          slotsWrapper.value,
          20,
          300,
          props.nestedItem
        )
      }
    }
  }

  let selectedSlot = {
    reference: 'slot',
    position: 1,
    value: '',
  }
  selectedSlot.value = slotsHeading.value ? `${slotsHeading.value}` : ''
  if (!props.notMultiple && props.date) {
    selectedSlot.reference = 'package-slot ' + props.id + ' ' + props.serviceId
  }

  let isWaiting = waitingListDisplayTimes.value.includes(selectedSlot.value)
  emits('waitingListSlot', isWaiting)

  sidebarDataCollector(selectedSlot)
}

// * this function had to return to its previous state after event calendar release
function calendarDateClick(data) {
  if (iOS()) {
    const now = Date.now()
    const last = lastIOSDateClick.value

    // Ignore the second click if iOS fires two events for a single tap
    if (last.date === data.dateStr && now - last.ts < 250) {
      return
    }

    lastIOSDateClick.value = { date: data.dateStr, ts: now }

    // Give eventClick/emit time to propagate state before painting selection
    return setTimeout(() => {
      calendarDateClickFunctionality(data)
    }, 50)
  }

  setTimeout(() => calendarDateClickFunctionality(data), 50)
}

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

/**
 * Click on Event handler
 * @param eventData
 */
function calendarEventClick(eventData) {
  emits('selectedDate', moment(eventData.event.start).format('YYYY-MM-DD'))
}

/**
 * Calendar Event Content Block render function
 * @param eventCalendarData
 * @returns {{html: string}}
 */
function calendarEventContent(eventCalendarData) {
  let eventContent

  const slotsAvailablePercentage =
    props.busyness[moment(eventCalendarData.event.start).format('YYYY-MM-DD')]

  eventContent = `<div class="am-advsc__slot-wrapper" style="height: ${slotsAvailablePercentage}%"></div>`

  return { html: eventContent }
}

const { sidebarDataCollector } = inject('sidebarStepsFunctions', {
  sidebarDataCollector: () => {},
})

// * Click on slot
function slotSelected(slot) {
  if (calendarChangeSideBar.value) {
    let selectedSlot = {
      reference: 'slot',
      // position will depend on fields order
      position: 1,
      value: '',
    }

    selectedSlot.value = !slotsHeading.value.includes(' - ')
      ? slotsHeading.value + ' - ' + getFrontedFormattedTime(slot.time)
      : slotsHeading.value.split(' - ')[0] +
        ' - ' +
        getFrontedFormattedTime(slot.time)

    slotsHeading.value = selectedSlot.value

    if (!props.notMultiple && props.date) {
      selectedSlot.reference =
        'package-slot ' + props.id + ' ' + props.serviceId
    }

    sidebarDataCollector(selectedSlot)
  }

  calendarEventSlot.value = slot.time

  let isWaiting = waitingListDisplayTimes.value.includes(slot.time)
  
  // Get the first qualifying provider ID for waiting list slot
  let selectedProviderId = null
  if (isWaiting && slot.waiting) {
    const dateKey = calendarEventDate?.value
      ? moment(calendarEventDate.value).format('YYYY-MM-DD')
      : null
    if (dateKey && calendarWaitingListSlots?.value?.[dateKey]?.[slot.time]) {
      const slotProviders = calendarWaitingListSlots.value[dateKey][slot.time]
      if (slotProviders && slotProviders.length > 0) {
        // Get first qualifying provider
        const qualifyingProvider = slotProviders.find(p => {
          const isFullyBooked = p.c <= 0
          const hasWaitingSpace = !p.maxWaitingCapacity || (p.w || 0) < p.maxWaitingCapacity
          return isFullyBooked && hasWaitingSpace
        })
        if (qualifyingProvider) {
          selectedProviderId = qualifyingProvider.e
        }
      }
    }
  }

  emits('waitingListSlot', isWaiting, (slot.waiting ? slot.peopleWaiting : 0), selectedProviderId)

  emits('selectedTime', slot.time)
}

window.addEventListener('resize', resize)
// * resize function
function resize() {
  if (advCalendarRef.value) {
    advCalendarRef.value.options.aspectRatio = mobileS.value
      ? 1
      : checkScreen.value
      ? 1.2
      : 1.45
    advCalendarRef.value.getApi().render()

    if (shortLocale === 'zh' || shortLocale === 'ja') {
      document.querySelectorAll('.fc-daygrid-day-number').forEach(el => {
        const ariaLabel = el.getAttribute('aria-label');
        if (ariaLabel) {
          // Match all digit groups: ["2025", "9", "18"]
          const numbers = ariaLabel.match(/\d+/g);
          if (numbers && numbers.length) {
            const day = numbers[numbers.length - 1]; // take the last one → "18"
            setTimeout(() => {
              el.textContent = day;
            }, 500);
          }
        }
      })
    }
  }
}

// * Color Vars
let amColors = inject('amColors', {
  amColors: {
    value: {
      colorPrimary: '#1246D6',
      colorSuccess: '#019719',
      colorError: '#B4190F',
      colorWarning: '#CCA20C',
      colorMainBgr: '#FFFFFF',
      colorMainHeadingText: '#33434C',
      colorMainText: '#1A2C37',
      colorSbBgr: '#17295A',
      colorSbText: '#FFFFFF',
      colorInpBgr: '#FFFFFF',
      colorInpBorder: '#D1D5D7',
      colorInpText: '#1A2C37',
      colorInpPlaceHolder: '#1A2C37',
      colorDropBgr: '#FFFFFF',
      colorDropBorder: '#D1D5D7',
      colorDropText: '#0E1920',
      colorCalCell: '#1246D6',
      colorCalCellText: '#1246D6',
      colorCalCellLow: '#1246D6',
      colorCalCellLowText: '#1246D6',
      colorCalCellHigh: '#1246D6',
      colorCalCellHighText: '#1246D6',
      colorCalCellSelected: '#1246D6',
      colorCalCellSelectedText: '#FFFFFF',
      colorCalCellDisabled: '#B4190F',
      colorCalCellDisabledText: '#1A2C37',
      colorBtnPrim: '#265CF2',
      colorBtnPrimText: '#FFFFFF',
      colorBtnSec: '#1A2C37',
      colorBtnSecText: '#FFFFFF',
    },
  },
})

const cssVars = computed(() => {
  return {
    '--am-c-cal-indicator':
      'showIndicatorPricing' in props && props.showIndicatorPricing
        ? 'visible'
        : 'hidden',
    '--am-c-cal-low':
      'colorCalCellLow' in amColors.value
        ? amColors.value.colorCalCellLow
        : amColors.value.colorCalCell,
    '--am-c-cal-high':
      'colorCalCellHigh' in amColors.value
        ? amColors.value.colorCalCellHigh
        : amColors.value.colorCalCell,
    '--am-c-cal-low-text':
      'colorCalCellLowText' in amColors.value
        ? amColors.value.colorCalCellLowText
        : amColors.value.colorCalCellText,
    '--am-c-cal-high-text':
      'colorCalCellHighText' in amColors.value
        ? amColors.value.colorCalCellHighText
        : amColors.value.colorCalCellText,
    '--am-c-cal-low-op10': useColorTransparency(
      'colorCalCellLow' in amColors.value
        ? amColors.value.colorCalCellLow
        : amColors.value.colorCalCell,
      0.1
    ),
    '--am-c-cal-low-op20': useColorTransparency(
      'colorCalCellLow' in amColors.value
        ? amColors.value.colorCalCellLow
        : amColors.value.colorCalCell,
      0.2
    ),
    '--am-c-cal-waiting':
      'colorCalCellWaiting' in amColors.value
        ? amColors.value.colorCalCellWaiting
        : amColors.value.colorMainBgr,
    '--am-c-cal-waiting-text':
      'colorCalCellWaitingText' in amColors.value
        ? amColors.value.colorCalCellWaitingText
        : amColors.value.colorMainText,
    '--am-c-cal-waiting-op40':
      'colorCalCellWaitingText' in amColors.value
        ? useColorTransparency(amColors.value.colorCalCellWaitingText,0.4)
        : useColorTransparency(amColors.value.colorMainText,0.4),
    '--am-c-cal-low-op30': useColorTransparency(
      'colorCalCellLow' in amColors.value
        ? amColors.value.colorCalCellLow
        : amColors.value.colorCalCell,
      0.3
    ),
    '--am-c-cal-low-op60': useColorTransparency(
      'colorCalCellLow' in amColors.value
        ? amColors.value.colorCalCellLow
        : amColors.value.colorCalCell,
      0.6
    ),
    '--am-c-cal-low-op80': useColorTransparency(
      'colorCalCellLow' in amColors.value
        ? amColors.value.colorCalCellLow
        : amColors.value.colorCalCell,
      0.8
    ),
    '--am-c-cal-high-op10': useColorTransparency(
      'colorCalCellHigh' in amColors.value
        ? amColors.value.colorCalCellHigh
        : amColors.value.colorCalCell,
      0.1
    ),
    '--am-c-cal-high-op20': useColorTransparency(
      'colorCalCellHigh' in amColors.value
        ? amColors.value.colorCalCellHigh
        : amColors.value.colorCalCell,
      0.2
    ),
    '--am-c-cal-high-op30': useColorTransparency(
      'colorCalCellHigh' in amColors.value
        ? amColors.value.colorCalCellHigh
        : amColors.value.colorCalCell,
      0.3
    ),
    '--am-c-cal-high-op60': useColorTransparency(
      'colorCalCellHigh' in amColors.value
        ? amColors.value.colorCalCellHigh
        : amColors.value.colorCalCell,
      0.6
    ),
    '--am-c-cal-high-op80': useColorTransparency(
      'colorCalCellHigh' in amColors.value
        ? amColors.value.colorCalCellHigh
        : amColors.value.colorCalCell,
      0.8
    ),
    '--am-c-advsc-bgr-op10': useColorTransparency(
      amColors.value.colorMainText,
      0.1
    ),
    '--am-c-cal-init': amColors.value.colorCalCell,
    '--am-c-cal-init-text': amColors.value.colorCalCellText,
    '--am-c-cal-init-op10': useColorTransparency(
      amColors.value.colorCalCell,
      0.1
    ),
    '--am-c-cal-init-op20': useColorTransparency(
      amColors.value.colorCalCell,
      0.2
    ),
    '--am-c-cal-init-op30': useColorTransparency(
      amColors.value.colorCalCell,
      0.3
    ),
    '--am-c-cal-init-op60': useColorTransparency(
      amColors.value.colorCalCell,
      0.6
    ),

    '--am-c-cal-selected': amColors.value.colorCalCellSelected,
    '--am-c-cal-selected-text': amColors.value.colorCalCellSelectedText,
    '--am-c-cal-selected-op80': useColorTransparency(
      amColors.value.colorCalCellSelected,
      0.8
    ),

    '--am-c-cal-disabled': amColors.value.colorCalCellDisabled,
    '--am-c-cal-disabled-text': amColors.value.colorCalCellDisabledText,
    '--am-c-cal-disabled-op10': useColorTransparency(
      amColors.value.colorCalCellDisabled,
      0.1
    ),
    '--am-c-cal-disabled-op60': useColorTransparency(
      amColors.value.colorCalCellDisabled,
      0.4
    ),
  }
})

/**
 * Lifecycle Hooks
 */
onBeforeMount(() => {
  let minimumDate = moment(props.calendarMinimumDate, 'YYYY-MM-DD HH:mm')
  let maximumDate = moment(props.calendarMaximumDate, 'YYYY-MM-DD HH:mm')

  let pastYears = parseInt(minimumDate.format('YYYY'))
  let futureYears = parseInt(maximumDate.format('YYYY'))

  for (pastYears; pastYears <= futureYears; pastYears++) {
    headerYearOption.push(pastYears)
  }

  let date = moment(calendarStartDate.value, 'YYYY-MM-DD')

  calMonth.value = date.format('MM')

  let year = parseInt(date.format('YYYY'))

  headerMonthOptions.value = getHeaderMonths(year)

  calYear.value = year
})

onMounted(() => {
  if (advCalendarRef.value) {
    advCalendarRef.value.options.aspectRatio = mobileS.value
      ? 1
      : checkScreen.value
      ? 1.2
      : 1.45

    if (shortLocale === 'zh' || shortLocale === 'ja') {
      document.querySelectorAll('.fc-daygrid-day-number').forEach(el => {
        const ariaLabel = el.getAttribute('aria-label');
        if (ariaLabel) {
          // Match all digit groups: ["2025", "9", "18"]
          const numbers = ariaLabel.match(/\d+/g);
          if (numbers && numbers.length) {
            const day = numbers[numbers.length - 1]; // take the last one → "18"
            setTimeout(() => {
              el.textContent = day;
            }, 500);
          }
        }
      })
    }
  }

  if (
    containerWrapper.value &&
    calendarEventSlots.value.length &&
    slotsWrapper.value
  ) {
    let calendarHeight = advCalendarRef.value
      ? advCalendarRef.value.getApi().el.offsetHeight + 25
      : 20
    useScrollTo(
      containerWrapper.value,
      slotsWrapper.value,
      calendarHeight,
      300,
      props.nestedItem
    )
  }
})
</script>

<script>
export default {
  name: 'AmAdvancedCalendar',
}
</script>

<style lang="scss">
//Amelia Calendar
$amCalClass: am-advsc;
@mixin am-advsc-block {
  .am-advsc {
    margin-bottom: 24px;

    &__wrapper {
      --am-fs-advsc: 15px;
      --am-ln-advsc: 1.6;
      --am-c-advsc-text: var(--am-c-main-text);
      // Calendar cell
      --am-c-advsc-cell-bgr: var(--am-c-cal-init-op10);
      --am-c-advsc-cell-busy: var(--am-c-cal-init-op30);
      --am-c-advsc-cell-border: var(--am-c-cal-init-op60);
      --am-c-advsc-cell-text: var(--am-c-cal-init-text);
      // Slot
      --am-fs-asdvsc-slot: 15px;
      --am-c-advsc-slot-text: var(--am-c-cal-init-text);
      --am-rad-advsc-slot: 4px;

      * {
        font-family: var(--am-font-family);
        box-sizing: border-box;
      }

      & > div {
        $count: 5;
        @for $i from 0 through $count {
          &:nth-child(#{$i + 1}) {
            animation: 600ms
              cubic-bezier(0.45, 1, 0.4, 1.2)
              #{$i *
              100}ms
              am-animation-slide-up;
            animation-fill-mode: both;
          }
        }
      }
    }

    &__time-zone {
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 12px 0 4px;

      span {
        font-size: 15px;
        font-weight: 400;
        line-height: 1;
        background-color: var(--am-c-advsc-cell-bgr);
        border-radius: 8px;
        color: var(--am-c-advsc-cell-text);
        padding: 4px 8px;
      }
    }

    &__period-pricing {
      display: flex;
      justify-content: right;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      margin: 12px 0 4px;

      span {
        font-size: 15px;
        font-weight: 400;
        line-height: 1;
        border-radius: 8px;
        padding: 4px 8px;
      }

      span.period-pricing-low {
        background-color: var(--am-c-cal-low-op10);
        color: var(--am-c-cal-low-text);
      }

      span.period-pricing-mid {
        background-color: var(--am-c-advsc-cell-bgr);
        color: var(--am-c-advsc-cell-text);
      }

      span.period-pricing-high {
        background-color: var(--am-c-cal-high-op10);
        color: var(--am-c-cal-high-text);
      }
    }

    &__duration {
      margin-top: 10px;
    }

    table,
    tr,
    th {
      background-color: transparent;
    }

    &.fc {
      &-theme-standard {
        .fc {
          &-scrollgrid {
            border: none;
          }
        }

        th,
        td {
          border: none;
        }

        // Calendar header cell
        th.#{$amCalClass} {
          // Month View
          &__dayGridMonth {
            &-header {
              &-cell {
                font-size: 16px;
                line-height: 1.5;
                color: #8c959a;
                padding: 4px 6px;
                .fc-col-header-cell-cushion {
                  font-size: var(--am-fs-advsc);
                  text-transform: initial;
                  text-decoration: none;
                  line-height: 1;
                  letter-spacing: 0;
                  color: var(--am-c-advsc-text);
                  padding: 12px 0;
                  white-space: nowrap;
                }
              }

              &-weekend {
                color: #d1d5d7;
              }
            }
          }
        }

        // Calendar cell
        td.#{$amCalClass} {
          // Month view
          &__dayGridMonth {
            // Calendar cell
            &-cell {
              position: relative;
              border: none;
              padding: 4px 6px;

              // Calendar today day
              &.fc-day-today {
                position: relative;
                background: none;

                // Calendar cell state
                &.#{$amCalClass} {
                  // Selected cell
                  &__dayGridMonth-selected {
                    .fc-daygrid-day-frame {
                      background-color: var(--am-c-advsc-cell-bgr);
                      border-color: var(--am-c-advsc-cell-border);

                      &:after {
                        background-color: var(--am-c-advsc-cell-text);
                      }
                    }
                  }

                  // Disabled cell
                  &__dayGridMonth-disabled {
                    .fc-daygrid-day {
                      &-frame {
                        --am-c-advsc-cell-bgr: var(--am-c-cal-disabled-op10);
                        --am-c-advsc-cell-border: transparent;

                        &:hover {
                          --am-c-advsc-cell-bgr: var(--am-c-cal-disabled-op10);
                          --am-c-advsc-cell-border: transparent;
                          cursor: not-allowed;
                        }
                      }

                      &-number {
                        --am-c-advsc-cell-text: var(--am-c-cal-disabled-text);
                      }
                    }
                  }
                }

                .fc-daygrid-day-frame {
                  // Today marker - dot
                  &:after {
                    content: '';
                    display: block;
                    position: absolute;
                    top: 4px;
                    right: 4px;
                    width: 4px;
                    height: 4px;
                    border-radius: 50%;
                    background-color: var(--am-c-advsc-cell-text);
                  }
                }
              }

              // Calendar cell state
              &.#{$amCalClass} {
                // Disabled cell
                &__dayGridMonth-disabled {
                  .fc-daygrid-day {
                    &-frame {
                      --am-c-advsc-cell-bgr: var(--am-c-cal-disabled-op10);
                      --am-c-advsc-cell-border: transparent;

                      &:hover {
                        --am-c-advsc-cell-bgr: var(--am-c-cal-disabled-op10);
                        --am-c-advsc-cell-border: transparent;
                        cursor: not-allowed;
                      }
                    }

                    &-number {
                      --am-c-advsc-cell-text: var(--am-c-cal-disabled-text);
                    }
                  }
                }

                // Pricing cell
                &__dayGridMonth-low {
                  .fc-daygrid-day {
                    &-frame {
                      --am-c-advsc-cell-bgr: var(--am-c-cal-low-op10);
                      --am-c-advsc-cell-border: var(--am-c-cal-low-op60);
                      --am-c-advsc-cell-text: var(--am-c-cal-low-text);
                      --am-c-advsc-cell-busy: var(--am-c-cal-low-op30);

                      &:hover {
                        --am-c-advsc-cell-bgr: var(--am-c-cal-low-op30);
                      }
                    }

                    &-frame::before {
                      visibility: var(--am-c-cal-indicator);
                      content: '↘';
                      position: absolute;
                      top: -2px;
                      left: 2px;
                      font-size: 10px;
                      color: var(--am-c-cal-low-op80);
                      font-weight: bold;
                    }
                  }
                }

                &__dayGridMonth-high {
                  .fc-daygrid-day {
                    &-frame {
                      --am-c-advsc-cell-bgr: var(--am-c-cal-high-op10);
                      --am-c-advsc-cell-border: var(--am-c-cal-high-op60);
                      --am-c-advsc-cell-text: var(--am-c-cal-high-text);
                      --am-c-advsc-cell-busy: var(--am-c-cal-high-op30);

                      &:hover {
                        --am-c-advsc-cell-bgr: var(--am-c-cal-high-op30);
                      }
                    }

                    &-frame::before {
                      visibility: var(--am-c-cal-indicator);
                      content: '↗';
                      position: absolute;
                      top: -2px;
                      left: 2px;
                      font-size: 10px;
                      color: var(--am-c-cal-high-op80);
                      font-weight: bold;
                    }
                  }
                }

                // Selected cell
                &__dayGridMonth-selected {
                  .fc-daygrid-day-frame {
                    --am-c-advsc-cell-text: var(--am-c-cal-selected-text);
                    --am-c-advsc-cell-bgr: var(--am-c-cal-selected);
                    --am-c-advsc-cell-border: var(--am-c-cal-selected);

                    &:hover {
                      --am-c-advsc-cell-bgr: var(--am-c-cal-selected-op80);
                      --am-c-advsc-cell-border: var(--am-c-cal-selected-op80);
                    }

                    .#{$amCalClass}__slot-wrapper {
                      display: none;
                    }
                  }
                }

                // Weekend days cell
                &__dayGridMonth-weekend {
                  .fc-daygrid-day {
                    &-frame {
                      background-color: transparent;
                      border-color: transparent;

                      &:hover {
                        border-color: transparent;
                        cursor: not-allowed;
                      }
                    }

                    &-number {
                      color: #d1d5d7;
                    }
                  }
                }
              }

              // Calendar inner cell items
              .fc-daygrid-day {
                // Calendar inner cell wrapper
                &-frame {
                  position: absolute;
                  width: calc(100% - 12px);
                  height: calc(100% - 8px);
                  min-height: auto;
                  top: 4px;
                  left: 6px;
                  background-color: var(--am-c-advsc-cell-bgr);
                  border: 1px solid var(--am-c-advsc-cell-border);
                  border-radius: 4px;
                  cursor: pointer;

                  &:hover {
                    --am-c-advsc-cell-bgr: var(--am-c-cal-init-op20);
                    --am-c-advsc-cell-border: var(--am-c-cal-init);
                    transition: all 0.3s ease-in-out;
                  }
                }

                // Calendar date slot availability wrapper
                &-bg {
                  .fc-bg-event {
                    background: none;
                    opacity: 1;

                    // Calendar date slot availability
                    .#{$amCalClass}__slot-wrapper {
                      position: absolute;
                      width: 100%;
                      bottom: 0;
                      left: 0;
                      background-color: var(--am-c-advsc-cell-busy);
                    }
                  }
                }

                // Inner cell date wrapper
                &-top {
                  position: absolute;
                  top: 50%;
                  left: 50%;
                  transform: translate(-50%, -50%);
                }

                // Inner cell date holder
                &-number {
                  color: var(--am-c-advsc-cell-text);
                  text-decoration: none;
                  line-height: 1;
                  padding: 0;
                  white-space: nowrap;
                }
              }
            }
          }
        }

        // Tighter inset for small screens so the selection frame doesn't hug edges
        @media (max-width: 560px) {
          &-frame {
            width: calc(100% - 6px);
            height: calc(100% - 6px);
            top: 3px;
            left: 3px;
          }
        }
      }
    }

    // Mobile Calendar cell
    &[am-mobile='true'] {
      &.fc {
        &-theme-standard {
          td.#{$amCalClass} {
            // Month view
            &__dayGridMonth {
              // Calendar cell TODO
              &-cell {
                padding: 3px;
                .fc-daygrid-day {
                  &-frame {
                    top: 3px;
                    left: 4px;
                    width: calc(100% - 8px);
                    height: calc(100% - 6px);
                  }
                }
              }
            }
          }
        }
      }
    }
    // Mobile S (<340px) Calendar cell
    &[am-mobile-s='true'] {
      &.fc {
        &-theme-standard {
          height: 230px;
          th.#{$amCalClass} {
            // Month view
            &__dayGridMonth {
              &-header-cell {
                --am-fs-advsc: 12px;
                padding: 2px 3px;
                line-height: 1.2;
              }
            }
          }
          td.#{$amCalClass} {
            // Month view
            &__dayGridMonth {
              // Calendar cell TODO
              &-cell {
                padding: 2px;
                .fc-daygrid-day {
                  &-frame {
                    top: 2px;
                    left: 2px;
                    &:after {
                      right: 1px;
                      top: 1px;
                    }
                  }
                }
              }
            }
          }
        }
      }
    }

    &__header {
      display: flex;
      gap: 24px;

      .am-button {
        font-size: 18px;
        padding: 8px;
      }

      &-mobile {
        gap: 8px;
      }

      &-mobile-s {
        flex-direction: column;
        align-items: center;
        gap: 8px;
      }
    }

    &__slots {
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: center;
      flex-wrap: wrap;
      gap: 8px 12px;

      &-heading {
        font-size: var(--am-fs-advsc);
        line-height: var(--am-ln-advsc);
        color: var(--am-c-advsc-text);
        margin-bottom: 8px;
        padding: 0 6px;
      }

      &-item {
        --am-c-advsc-slot-bgr: var(--am-c-cal-init-op10);
        --am-c-advsc-slot-border: var(--am-c-cal-init-op60);
        display: inline-flex;
        width: calc(50% - 6px);

        &.am-width-full {
          width: 100%;

          &:last-child {
            max-width: 100%;
          }

          .am-advsc__waiting-tag {
            margin-top: -4px;

            &:before {
              margin-top: 2px;
            }
          }
        }

        &:focus {
          .am-advsc__slots-item__inner {
            --am-c-advsc-slot-border: var(--am-c-cal-selected);
          }
        }

        span {
          background-color: var(--am-c-cal-init-op10);
          padding: 0 4px 0 4px;
          border-radius: 4px;
        }

        $count: 1000;
        @for $i from 0 through $count {
          &:nth-child(#{$i + 1}) {
            animation: 600ms
              cubic-bezier(0.45, 1, 0.4, 1.2)
              #{$i *
              100}ms
              am-animation-slide-up;
            animation-fill-mode: both;
          }
        }

        &:last-child {
          max-width: 50%;
        }

        &__low {
          --am-c-advsc-slot-bgr: var(--am-c-cal-low-op10);
          --am-c-advsc-slot-border: var(--am-c-cal-low-op60);
          --am-c-advsc-slot-text: var(--am-c-cal-low-text);

          span {
            background-color: var(--am-c-cal-low-op10);
            padding: 0 4px 0 4px;
            border-radius: 4px;
          }
        }

        &__high {
          --am-c-advsc-slot-bgr: var(--am-c-cal-high-op10);
          --am-c-advsc-slot-border: var(--am-c-cal-high-op60);
          --am-c-advsc-slot-text: var(--am-c-cal-high-text);

          span {
            background-color: var(--am-c-cal-high-op10);
            padding: 0 4px 0 4px;
            border-radius: 4px;
          }
        }

        &__selected {
          --am-c-advsc-slot-bgr: var(--am-c-cal-selected);
          --am-c-advsc-slot-border: var(--am-c-cal-selected);
          --am-c-advsc-slot-text: var(--am-c-cal-selected-text);
        }

        &__inner {
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          justify-content: center;
          gap: 0 12px;
          width: 100%;
          font-size: var(--am-fs-asdvsc-slot);
          font-weight: 400;
          line-height: 1.6;
          color: var(--am-c-advsc-slot-text);
          padding: 7px;
          border: 1px solid var(--am-c-advsc-slot-border);
          border-radius: var(--am-rad-advsc-slot);
          background-color: var(--am-c-advsc-slot-bgr);
          cursor: pointer;
        }

        &-mobile {
          // TODO - for time view
          padding: 0 4px;
          --am-fs-asdvsc-slot: 12px;

          .am-advsc__slots-item__inner {
            padding: 4px;
          }
        }

        &-disabled {
          .am-advsc__slots-item__inner {
            background: var(--am-c-cal-disabled-op10);
            border: transparent;
            cursor: not-allowed;
            color: var(--am-c-cal-disabled-text);
          }
        }

        // Waiting list specific styling
        &-waiting {
          border-color: var(--am-c-rs-text-op60);
          .am-advsc__slots-item__inner {
            background: var(--am-c-cal-waiting);
            border: 1px solid var(--am-c-cal-waiting-op40);
            flex-direction: column;
            line-height: 1.6;
            color: var(--am-c-cal-waiting-text);

            &.am-contains-waiting-tag {
              padding: 3px 12px;
            }
          }
          .am-advsc__waiting-tag {
            background: transparent;
            display: flex;
            font-size: 10px;
            line-height: 1;
            border-radius: 10px;
            text-transform: lowercase;
            margin-top: -3px;

            &:before {
              margin-top: 1px;
            }
          }

          &.am-advsc__slots-item-mobile {
            .am-advsc__slots-item__inner.am-contains-waiting-tag {
              line-height: 1.1;
              white-space: nowrap;
            }
          }

          &.am-advsc__slots-item__selected {
            .am-advsc__slots-item__inner {
              background: var(--am-c-cal-selected);
              border: 1px solid var(--am-c-cal-selected);
              color: var(--am-c-cal-selected-text);
            }
          }
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-advsc-block;
}

#amelia-app-backend-new {
  @include am-advsc-block;
}
</style>
