<template>
  <div ref="dateTimeRef" class="am-fs-dt__calendar">
    <AmAdvancedSlotCalendar
      v-if="showCalendar"
      :id="0"
      :slots="calendarEvents"
      :calendar-minimum-date="moment().format('YYYY-MM-DD hh:mm')"
      :calendar-maximum-date="moment().add(1,'year').format('YYYY-MM-DD hh:mm')"
      :time-zone="features.timeZones && amCustomize[pageRenderKey].dateTimeStep.options.timeZoneVisibility.visibility"
      :show-busy-slots="amCustomize[pageRenderKey].dateTimeStep.options.busyTimeSlotsVisibility.visibility"
      :show-estimated-pricing="amCustomize[pageRenderKey].dateTimeStep.options.estimatedPricingVisibility.visibility"
      :show-indicator-pricing="amCustomize[pageRenderKey].dateTimeStep.options.indicatorPricingVisibility.visibility"
      :show-slot-pricing="amCustomize[pageRenderKey].dateTimeStep.options.slotPricingVisibility.visibility"
      :show-people-waiting="amCustomize[pageRenderKey].dateTimeStep.options?.peopleWaitingVisibility?.visibility"
      :label-slots-selected="labelsDisplay('date_time_slots_selected', 'dateTimeStep')"
      :label-waiting-list="labelsDisplay('waiting_list', 'dateTimeStep')"
      :period-pricing="periodPricing"
      @selected-date="setSelectedDate"
      @unselect-date="unselectDate"
      @selected-time="setSelectedTime"
    ></AmAdvancedSlotCalendar>

    <!-- Recurring Appointment -->
    <AmSlidePopup
      :visibility="recurringPopupVisibility"
      class="am-fs-dt__calendar__recurring"
    >
      <div class="am-fs-dt__rec_popup">
        <p>
          {{ labelsDisplay('repeat_appointment', 'recurringPopup') }}
        </p>
        <p
          v-if="
            amCustomize[pageRenderKey].recurringPopup.options.content.visibility
          "
        >
          {{ labelsDisplay('repeat_appointment_quest', 'recurringPopup') }}
        </p>
      </div>
      <template #footer>
        <AmButton
          category="secondary"
          :type="
            amCustomize[pageRenderKey].recurringPopup.options.secondaryButton
              .buttonType
          "
        >
          {{ labelsDisplay('no', 'recurringPopup') }}
        </AmButton>
        <AmButton
          :type="
            amCustomize[pageRenderKey].recurringPopup.options.primaryButton
              .buttonType
          "
        >
          {{ labelsDisplay('yes', 'recurringPopup') }}
        </AmButton>
      </template>
    </AmSlidePopup>
    <!--/ Recurring Appointment -->
  </div>
</template>

<script setup>
import AmSlidePopup from '../../../../_components/slide-popup/AmSlidePopup.vue';
import AmButton from '../../../../_components/button/AmButton.vue';
import AmAdvancedSlotCalendar from '../../../../_components/advanced-slot-calendar/AmAdvancedSlotCalendar.vue'
import moment from 'moment'
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

import { ref, provide, inject, computed, onMounted, watchEffect } from 'vue'

const amSettings = inject('settings')

let langKey = inject('langKey')
let amLabels = inject('labels')

let subStepName = inject('subStepName')
let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()
// * Plugin Licence
let licence = inject('licence')

// * Features
let features = inject('features')

// * Container Width
// let cWidth = inject('containerWidth', 0)
// let checkScreen = computed(() => cWidth.value < 560 || (cWidth.value - 240 < 520))

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
      high: 15
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

let calendarEventBusySlots = ref([])

let calendarWaitingListSlots = ref([])

let calendarWaitingListTimes = ref(
  !licence.isBasic && !licence.isStarter && !licence.isLite &&
  amSettings.featuresIntegrations?.waitingListAppointments?.enabled ? ['10:30'] : []
)

watchEffect (() => {
  calendarEventBusySlots.value = amCustomize.value[pageRenderKey.value].dateTimeStep.options.busyTimeSlotsVisibility.visibility ?
      ['08:00', '08:30', '09:30', '12:30', '14:00'] : []
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
provide('calendarWaitingListSlots', calendarWaitingListSlots)
provide('calendarWaitingListTimes', calendarWaitingListTimes)

let calendarSlotDuration = 1800
provide('calendarSlotDuration', calendarSlotDuration)

// * Recurring popup
let recurringPopupVisibility = computed(() => {
  return subStepName.value === 'recurringPopup'
})

// * Label computed function
function labelsDisplay(label, stepKey) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value][stepKey].translations &&
      amCustomize.value[pageRenderKey.value][stepKey].translations[label] &&
      amCustomize.value[pageRenderKey.value][stepKey].translations[label][
        langKey.value
      ]
      ? amCustomize.value[pageRenderKey.value][stepKey].translations[label][
          langKey.value
        ]
      : amLabels[label]
  })

  return computedLabel.value
}

onMounted(() => {
  periodPricing.value = !licence.isBasic && !licence.isStarter && !licence.isLite ? getCustomPeriodsPricing() : {}

  showCalendar.value = true
})

function setSelectedDate(value) {
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
  calendarEventBusySlots.value = amCustomize.value[pageRenderKey.value]
    .dateTimeStep.options.busyTimeSlotsVisibility.visibility
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
  name: 'DateTimeStep',
  key: 'dateTimeStep',
  sidebarData: {
    label: 'date_time',
    icon: 'date-time',
    stepSelectedData: [],
    finished: false,
    selected: false,
  },
}
</script>

<style lang="scss">
// am -- amelia
// fs -- form steps

#amelia-app-backend-new #amelia-container {
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

  // Skeleton
  .am-skeleton-slots {
    &-mobile {
      padding: 16px;
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
