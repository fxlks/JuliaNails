<template>
  <div ref="dateTimeRef" class="am-fs-dt__calendar">
    <AmAdvancedSlotCalendar
      v-if="showCalendar"
      :id="0"
      :slots="calendarEvents"
      :calendar-minimum-date="moment().format('YYYY-MM-DD hh:mm')"
      :calendar-maximum-date="moment().add(1,'year').format('YYYY-MM-DD hh:mm')"
      :time-zone="features.timeZones && amCustomize[pageRenderKey][stepRecognition].options.timeZoneVisibility.visibility"
      :end-time="amCustomize[pageRenderKey][stepRecognition].options.endTimeVisibility.visibility"
      :show-estimated-pricing="stepRecognition === 'rescheduleAppointment' ? amCustomize[pageRenderKey][stepRecognition].options.estimatedPricingVisibility.visibility : false"
      :show-indicator-pricing="stepRecognition === 'rescheduleAppointment' ? amCustomize[pageRenderKey][stepRecognition].options.indicatorPricingVisibility.visibility : false"
      :show-slot-pricing="stepRecognition === 'rescheduleAppointment' ? amCustomize[pageRenderKey][stepRecognition].options.slotPricingVisibility.visibility : false"
      :label-slots-selected="labelsDisplay('date_time_slots_selected')"
      :period-pricing="stepRecognition === 'rescheduleAppointment' ? periodPricing : {}"
      @selected-date="setSelectedDate"
      @selected-time="setSelectedTime"
      @unselect-date="unselectDate"
    ></AmAdvancedSlotCalendar>
  </div>
</template>

<script setup>
// * Import from libraries
import moment from 'moment'

// * Import from Vue
import { ref, provide, inject, computed, watchEffect, onMounted } from 'vue'

// * Import composables
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

//  * Dedicated component
import AmAdvancedSlotCalendar from '../../../../_components/advanced-slot-calendar/AmAdvancedSlotCalendar'

// * Customize
const { amCustomize } = useReactiveCustomize()

// * Features
let features = inject('features')

// * Languages
let langKey = inject('langKey')

// * Global Labels
let amLabels = inject('labels')

// * Plugin Licence
let licence = inject('licence')

// * Step key name
let stepName = inject('stepName')

// * Sub step key name
let subStepName = inject('subStepName')

let stepRecognition = computed(() => {
  if (subStepName.value) return subStepName.value
  return stepName.value
})

let customOptions = computed(() => {
  return amCustomize.value[pageRenderKey.value][stepRecognition.value].options
})

// * Form key
let pageRenderKey = inject('pageRenderKey')

/*****************
 * Calendar Data *
 ****************/
let showCalendar = ref(false)

let periodPricing = ref({})

function getCustomPeriodsPricing () {
  let result = {}
  let today = moment()

  let current = today.clone()

  for (let i = 0; i <= 31; i++) {
    let type = current.day() === 6 || current.day() === 0
      ? 'high'
      : (current.day() === 1 || current.day() === 2 ? 'low' : 'mid')

    result[current.format('YYYY-MM-DD')] = {
      type: type,
      slots: {
        '09:00': {
          type: type === 'mid' ? 'low' : type,
          price: type === 'mid' ? 5 : (type === 'low' ? 5 : 15)
        },
        '09:30': {
          type: type === 'mid' ? 'low' : type,
          price: type === 'mid' ? 5 : (type === 'low' ? 5 : 15)
        },
        '10:00': {
          type: type,
          price: type === 'mid' ? 10 : (type === 'low' ? 5 : 15)
        },
        '10:30': {
          type: type,
          price: type === 'mid' ? 10 : (type === 'low' ? 5 : 15)
        },
        '11:00': {
          type: type,
          price: type === 'mid' ? 10 : (type === 'low' ? 5 : 15)
        },
        '11:30': {
          type: type,
          price: type === 'mid' ? 10 : (type === 'low' ? 5 : 15)
        },
        '12:00': {
          type: type === 'mid' ? 'high' : type,
          price: type === 'mid' ? 15 : (type === 'high' ? 15 : 5)
        },
        '12:30': {
          type: type === 'mid' ? 'high' : type,
          price: type === 'mid' ? 15 : (type === 'high' ? 15 : 5)
        },
      }
    }

    current.add(1, 'day')
  }

  return {
    price: {
      low: 5,
      mid: 10,
      high: 15,
      uniqueMin: true,
      uniqueMid: true,
      uniqueMax: true
    },
    dates: result
  }
}

let dateTimeRef = ref(null)
provide('formWrapper', dateTimeRef)

let calendarEvents = ref([])

let calendarEventDate = ref('')

let calendarServiceDuration = ref('0')

let calendarEventSlots = ref([])

let calendarEventBusySlots = ref()

watchEffect(() => {
  calendarEventBusySlots.value =
    'busyTimeSlotsVisibility' in customOptions.value &&
    customOptions.value.busyTimeSlotsVisibility.visibility
      ? ['08:00', '08:30', '09:30', '12:30', '14:00']
      : []
})

let today = moment().format('YYYY-MM-DD')

for (let i = 0; i <= 31; i++) {
  let block = {
    display: 'background',
    extendedProps: {
      slots: { '09:00': [7, 3] },
      slotsAvailable: 1,
      slotsTotal: 100,
    },
    start: moment(today).add(i, 'd').format('YYYY-MM-DD'),
    title: 'e',
  }

  calendarEvents.value.push(block)
}

let calendarEventSlot = ref('')
let calendarStartDate = ref(moment().format('YYYY-MM-DD'))
let calendarChangeSideBar = ref(true)

provide('calendarEvents', calendarEvents)
provide('calendarEventDate', calendarEventDate)
provide('calendarEventSlots', calendarEventSlots)
provide('calendarEventBusySlots', calendarEventBusySlots)
provide('calendarEventSlot', calendarEventSlot)
provide('calendarStartDate', calendarStartDate)
provide('calendarChangeSideBar', calendarChangeSideBar)
provide('calendarServiceDuration', calendarServiceDuration)

let calendarSlotDuration = 1800
provide('calendarSlotDuration', calendarSlotDuration)

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value][stepRecognition.value]
      .translations &&
      amCustomize.value[pageRenderKey.value][stepRecognition.value]
        .translations[label] &&
      amCustomize.value[pageRenderKey.value][stepRecognition.value]
        .translations[label][langKey.value]
      ? amCustomize.value[pageRenderKey.value][stepRecognition.value]
          .translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

onMounted(() => {
  periodPricing.value = !licence.isBasic && !licence.isStarter && !licence.isLite ? getCustomPeriodsPricing() : {}

  showCalendar.value = true
})

function setSelectedDate (value) {
  unselectDate()

  for (let i = 0; i < 18; i++) {
    if (i > 8 && i < 13) {
      calendarEventSlots.value.push(`${i < 10 ? '0'+i:i}:00`)
      calendarEventSlots.value.push(`${i < 10 ? '0'+i:i}:30`)
    }
  }

  if (calendarEventSlots.value.length) {
    setSelectedTime(calendarEventSlots.value[0])
  }

  calendarEventDate.value = value
}

function unselectDate() {
  calendarEventBusySlots.value =
    'busyTimeSlotsVisibility' in customOptions.value &&
    customOptions.value.busyTimeSlotsVisibility.visibility
      ? ['08:00', '08:30', '09:30', '12:30', '14:00']
      : []

  calendarEventSlots.value = []

  calendarEventSlot.value = ''

  calendarEventDate.value = ''
}

function setSelectedTime(value) {
  calendarEventSlot.value = value
}
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
      padding: 0;

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
