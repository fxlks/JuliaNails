<template>
  <div class="am-fs__rs" :class="[{'am-fs__rs-mobile': checkScreen}, props.globalClass]" :style="cssVars">
    <template v-if="amCustomize && amCustomize.recurringStep && amCustomize.recurringStep.options.heading.visibility">
      <p>{{ amLabels.recurrence }}</p>
      <span>{{ amLabels.recurrence_choosing_time }}</span>
    </template>
    <p>{{ amLabels.repeat_every }}:</p>
    <div class="am-fs__rs-every">
      <AmInputNumber
        v-model="repeatInterval"
        :min="1"
        :max="100"
      />
      <AmSelect
        v-model="repeatType"
        :disabled="props.service.recurringCycle !== 'all'"
      >
        <AmOption
          v-for="(type, i) in repeatTypes"
          :key="i"
          :label="type.label"
          :value="type.value"
        />
      </AmSelect>
    </div>
    <div v-if="repeatType === 'weekly'">
      <p>{{ `${amLabels.recurrence_repeat_on}:` }}</p>
      <div class="am-fs__rs-on-week">
        <div
          v-for="(day, i) in weekDays"
          :key="i"
          :class="{'am-fs__rs-on-week-selected' : weekDays.find(i => i.value === day.value).selected}"
          @click="selectWeekDay(day.value)"
        >
          {{ day.label }}
        </div>
      </div>
    </div>
    <div v-if="repeatType === 'monthly'">
      <p>{{ `${amLabels.recurrence_repeat_on}:` }}</p>
      <div class="am-fs__rs-on-month">
        <AmSelect v-model="repeatMonthly">
          <AmOption
            v-for="(type, i) in monthlyTypes"
            :key="i"
            :label="type.label + (i !== 0 ? (' ' + selectedDayInMonth) : '')"
            :value="type.value"
          />
        </AmSelect>
      </div>
    </div>
    <p>{{ `${amLabels.recurrence_ends}:` }}</p>
    <span>{{ amLabels.recurrence_choose_ends }}</span>
    <div class="am-fs__rs-ends">
      <div class="am-fs__rs-ends-choose">
        <AmRadioGroup
          v-model="occurrenceType"
        >
          <AmRadio value="On">{{ amLabels.recurrence_on }}</AmRadio>
          <AmRadio value="After">{{ amLabels.recurrence_after }}</AmRadio>
        </AmRadioGroup>
      </div>
      <div class="am-fs__rs-ends-options">
        <AmDatePickerFull
          :calendar-minimum-date="props.startDate"
          :disabled="occurrenceType !== 'On'"
          :input-placeholder="amLabels.recurrence_select_date"
          :calendar-maximum-date="props.maxDate"
          @selected-date="selectedOccurrenceDate"
        />
        <div class="am-fs__rs-ends-after">
          <AmInputNumber
            v-model="occurrenceCount"
            :disabled="occurrenceType !== 'After'"
            :min="1"
            :max="100"
          />
          <span>{{ ` ${amLabels.occurrences}` }}</span>
        </div>
      </div>
    </div>
    <div class="am-fs__rs-summary">
      <p>{{ `${amLabels.appointment_repeats}:` }}</p>
      <p>
        {{ `${amLabels.recurrence_every} ${repeatInterval > 1 ? repeatInterval : ''} ${repeatTypes.find((item) => item.value === repeatType) ? repeatTypes.find((item) => item.value === repeatType).label : ''}` }}
        <span v-if="repeatType === 'weekly'">
          {{ `${amLabels.repeats_on} ${weekDays.filter(i => i.selected).map(w => w.labelFull).join(', ')}` }}
        </span>
        <span v-if="repeatType === 'monthly'">
          {{ ` ${amLabels.repeats_on} ${monthlyTypes[repeatMonthly].label}` }}
          <span v-if="repeatMonthly !== 0">
            {{ ` ${selectedDayInMonth}` }}
          </span>,
        </span>
        <span>
          {{ ` ${amLabels.repeats_from} ${getFrontedFormattedDate(props.startDate)} ${amLabels.repeats_at} ${getFrontedFormattedTime(props.startTime)}` }}
        </span>
      </p>
      <p>
        <span v-if="occurrenceType === 'After'">
          {{ amLabels.number_of_recurrences }} {{ ` ${occurrenceCount}` }}
        </span>
        <span v-else-if="occurrenceDate">
          {{ amLabels.ends_on }} {{ ` ${getFrontedFormattedDate(useStringFromDate(occurrenceDate))}` }}
        </span>
      </p>
    </div>
  </div>
</template>

<script setup>
import { useStore } from 'vuex';
import { getFrontedFormattedTime, getFrontedFormattedDate, useStringFromDate, weekDaysLocale, weekDaysShortLocale } from "../../../assets/js/common/date";
import AmInputNumber from "../../_components/input-number/AmInputNumber";
import AmSelect from "../../_components/select/AmSelect";
import AmOption from "../../_components/select/AmOption";
import AmRadioGroup from "../../_components/radio/AmRadioGroup";
import AmRadio from "../../_components/radio/AmRadio";
import AmDatePickerFull from "../../_components/date-picker-full/AmDatePickerFull";
import moment from "moment";
import { shortLocale } from "../../../plugins/settings.js";

import {
  computed,
  onMounted,
  ref,
  inject,
} from "vue";
import {useColorTransparency} from "../../../assets/js/common/colorManipulation";

let props = defineProps({
  service: {
    type: Object,
    default: () => {}
  },
  startDate: {
    type: String,
    default: ''
  },
  startTime: {
    type: String,
    default: ''
  },
  maxDate: {
    type: String,
    default: ''
  },
  globalClass: {
    type: String,
    default: ''
  }
})

let amLabels = inject('amLabels')

let amCustomize = inject('amCustomize')

// * Store
let store = useStore();

// Container Width
let cWidth = inject('containerWidth', 0)
let checkScreen = computed(() => cWidth.value < 560 || (cWidth.value - 240 < 520))

/************
 * Computed *
 ***********/
let repeatType = computed({
  get: () => store.getters['recurring/getRepeatType'],
  set: (val) => {
    store.commit('recurring/setRepeatType', val)
  }
})

let repeatInterval = computed({
  get: () => store.getters['recurring/getRepeatInterval'],
  set: (val) => {
    store.commit('recurring/setRepeatInterval', val)
  }
})

let occurrenceType = computed({
  get: () => store.getters['recurring/getOccurrenceType'],
  set: (val) => {
    store.commit('recurring/setOccurrenceType', val)
  }
})

let occurrenceDate = computed({
  get: () => store.getters['recurring/getOccurrenceDate'] ? store.getters['recurring/getOccurrenceDate'] : new Date(props.startDate),
  set: (val) => {
    store.commit('recurring/setOccurrenceDate', val)
  }
})

let occurrenceCount = computed({
  get: () => store.getters['recurring/getOccurrenceCount'],
  set: (val) => {
    store.commit('recurring/setOccurrenceCount', val)
  }
})

let repeatMonthly = computed({
  get: () => store.getters['recurring/getMonthly'],
  set: (val) => {
    store.commit('recurring/setMonthly', val)
  }
})


const repeatTypes = ref([
  {label: amLabels.value.recurrence_day, labelPlural: amLabels.value.recurrence_days, value: 'daily'},
  {label: amLabels.value.recurrence_week, labelPlural: amLabels.value.recurrence_weeks, value: 'weekly'},
  {label: amLabels.value.recurrence_month, labelPlural: amLabels.value.recurrence_months, value: 'monthly'}
])

const monthlyTypes = ref([
  {
    label: amLabels.value.recurrence_specific_date,
    value: 0
  },
  {
    label: amLabels.value.recurrence_first,
    value: 1
  },
  {
    label: amLabels.value.recurrence_second,
    value: 2
  },
  {
    label: amLabels.value.recurrence_third,
    value: 3
  },
  {
    label: amLabels.value.recurrence_fourth,
    value: 4
  },
  {
    label: amLabels.value.recurrence_last,
    value: 5
  }
])

let currentDay = ref(moment(props.startDate).format('dddd').toLowerCase())

let weekDays = ref([
  {label: 'Mon', labelFull: 'Monday', value: 'monday', selected: false},
  {label: 'Tue', labelFull: 'Tuesday', value: 'tuesday', selected: false},
  {label: 'Wed', labelFull: 'Wednesday', value: 'wednesday', selected: false},
  {label: 'Thu', labelFull: 'Thursday', value: 'thursday', selected: false},
  {label: 'Fri', labelFull: 'Friday', value: 'friday', selected: false},
  {label: 'Sat', labelFull: 'Saturday', value: 'saturday', selected: false},
  {label: 'Sun', labelFull: 'Sunday', value: 'sunday', selected: false}
])

let selectedDayInMonth = computed(() => {
  return weekDays.value.find(i => i.value === moment(props.startDate, "YYYY-MM-DD").format('dddd').toLowerCase()).labelFull
})

function selectedOccurrenceDate (dateString) {
  occurrenceDate.value = moment(dateString, 'YYYY-MM-DD').toDate()
}

function selectWeekDay (value, preselected = false) {
  let weekDay = weekDays.value.find(i => i.value === value)

  weekDay.selected = preselected ? true : !weekDay.selected

  store.commit('recurring/setDays', {value: value, selected: weekDay.selected})
}

onMounted(() => {
  if (shortLocale) {
    weekDays.value = weekDays.value.map((day, i) => {
      return {
        ...day,
        label: weekDaysShortLocale[i],
        labelFull: weekDaysLocale[i]
      }
    })
  }

  let selectedDays = store.getters['recurring/getDays'].filter(day => day.selected)
  
  if (selectedDays.length === 0) {
    selectWeekDay(weekDays.value.find(day => currentDay.value && day.value === currentDay.value).value, true)
  } else {
    selectedDays.forEach(val => {
      selectWeekDay(val.value, true)
    })
  }

  if (props.service.recurringCycle !== 'all') {
    store.commit('recurring/setRepeatType', props.service.recurringCycle)
  }
})

// * Colors
let amColors = inject('amColors')
let cssVars = computed(() => {
  return {
    '--am-c-rs-text': amColors.value.colorMainText,
    '--am-c-rs-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-c-rs-bgr': amColors.value.colorMainBgr,
    '--am-c-rs-primary': amColors.value.colorPrimary,
    '--am-c-rs-inp-border': amColors.value.colorInpBorder,
  }
})
</script>

<script>
export default {
  name: 'RecurringSetup',
}
</script>

<style lang="scss">
.amelia-v2-booking {
  #amelia-container {
    // rs - Recurring Step
    .am-fs__rs {
      & > * {
        $count: 10;
        @for $i from 0 through $count {
          &:nth-child(#{$i + 1}) {
            animation: 600ms cubic-bezier(.45,1,.4,1.2) #{$i*100}ms am-animation-slide-up;
            animation-fill-mode: both;
          }
        }
      }

      p {
        margin-top: 16px;
        font-weight: 500;
        font-size: 15px;
        line-height: 24px;
        /* $shade-900 */
        color: var(--am-c-rs-text);
        margin-bottom: 4px;
      }

      span {
        font-weight: 400;
        font-size: 14px;
        line-height: 20px;
        /* $shade-500 */
        color: var(--am-c-rs-text-op60);
      }

      &-every {
        display: flex;
        .am-select {
          width: 103px;
        }
        & > div {
          &:first-child {
            margin-right: 8px;
          }
        }
      }

      &-on-week {
        display: flex;
        width: 100%;
        justify-content: space-between;
        /* $shade-900 */
        color: var(--am-c-rs-text);
        & > div {
          font-weight: 400;
          font-size: 15px;
          line-height: 24px;
          text-align: center;
          margin: 0;
          cursor: pointer;
          /* $shade-250 */
          border: 1px solid var(--am-c-rs-inp-border);
          box-shadow: 0 1px 1px rgba(115, 134, 169, 0.06);
          border-radius: 4px;
          width: 52px;
          padding: 8px 0;
          align-content: center;

          &.am-fs__rs-on-week-selected {
            /* $blue-900 */
            background: var(--am-c-rs-primary);
            /* $blue-1000 */
            border: 1px solid var(--am-c-rs-primary);
            /* $white */
            color: var(--am-c-rs-bgr);
          }
        }
      }
      &-ends {
        display: flex;
        margin: 16px 0;
        max-width: 100%;

        &-choose {
          .am-radio-group {
            display: flex;

            & > div:first-child {
              margin-bottom: 16px;
            }
          }
          .am-radio {
            margin-top: 5px;
            & > span:nth-child(2) {
              font-size: 15px;
              font-weight: 400;
              line-height: 1.6;
              word-break: keep-all;
              /* $shade-900 */
              color: var(--am-c-rs-text);
            }
          }
        }

        &-options {
          display: flex;
          flex-direction: column;
          .am-input-wrapper {
            max-width: 174px;
            width: 100%
          }

          & > div:first-child {
            margin-bottom: 16px;
          }
        }

        &-after {
          .am-input {
            width: 40px;
          }
          span {
            font-weight: 400;
            font-size: 15px;
            line-height: 40px;
            text-align: center;
            /* $shade-900 */
            color: var(--am-c-rs-text);
            @media only screen and (max-width: 375px) {
              display: flex;
            }
          }
        }
      }
      &-summary {
        p, span {
          font-weight: 400;
          font-size: 14px;
          line-height: 20px;
          /* $shade-600 */
          color: var(--am-c-rs-text-op60);
          margin: 0
        }
      }

      &-mobile {
        .el-radio {
          &__label {
            white-space: nowrap;
          }
        }

        .am-fs__rs-every {
          & > div {
            max-width: 162px;
            width: 100%;
          }
          & .am-select {
            width: 100%;
          }
        }

        .am-fs__rs-ends {

          &-after {
            .am-input-wrapper {
              max-width: 40px;

              .el-input__inner {
                text-align: center;
                padding: 6px;
              }
            }
            .el-input-number {
              max-width: 60%;
            }
          }
        }
      }

      &-on-month {
        .am-select-wrapper {
          max-width: 180px;
        }
      }
    }
  }
}
</style>
