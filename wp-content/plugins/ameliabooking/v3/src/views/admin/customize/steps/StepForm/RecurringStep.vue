<template>
  <div
    class="am-fs__rs"
    :class="[{ 'am-fs__rs-mobile': checkScreen }, props.globalClass]"
    :style="cssVars"
  >
    <template
      v-if="amCustomize[pageRenderKey].recurringStep.options.heading.visibility"
    >
      <p>{{ labelsDisplay('recurrence') }}</p>
      <span class="am-fa__rs-light">{{
        labelsDisplay('recurrence_choosing_time')
      }}</span>
    </template>
    <p>{{ `${labelsDisplay('repeat_every')}:` }}</p>
    <div class="am-fs__rs-every">
      <AmInputNumber v-model="repeatInterval" :min="1" :max="100" />
      <AmSelect v-model="repeatType">
        <AmOption
          v-for="(type, i) in repeatTypes"
          :key="i"
          :label="type.label"
          :value="type.value"
        />
      </AmSelect>
    </div>
    <div v-if="repeatType === 'weekly'">
      <p>{{ `${labelsDisplay('recurrence_repeat_on')}:` }}</p>
      <div class="am-fs__rs-on-week">
        <div
          v-for="(day, i) in weekDays"
          :key="i"
          :class="{
            'am-fs__rs-on-week-selected': weekDays.find(
              (i) => i.value === day.value
            ).selected,
          }"
          @click="selectWeekDay(day.value)"
        >
          {{ day.label }}
        </div>
      </div>
    </div>
    <div v-if="repeatType === 'monthly'">
      <p>{{ `${labelsDisplay('recurrence_repeat_on')}:` }}</p>
      <div class="am-fs__rs-on-month">
        <AmSelect v-model="repeatMonthly">
          <AmOption
            v-for="(type, i) in monthlyTypes"
            :key="i"
            :label="type.label"
            :value="type.value"
          />
        </AmSelect>
      </div>
    </div>
    <p>{{ `${labelsDisplay('recurrence_ends')}:` }}</p>
    <span class="am-fa__rs-light">{{
      labelsDisplay('recurrence_choose_ends')
    }}</span>
    <div class="am-fs__rs-ends">
      <div class="am-fs__rs-ends-choose">
        <AmRadioGroup v-model="occurrenceType">
          <AmRadio value="on">{{ labelsDisplay('recurrence_on') }}</AmRadio>
          <AmRadio value="after">{{
            labelsDisplay('recurrence_after')
          }}</AmRadio>
        </AmRadioGroup>
      </div>
      <div class="am-fs__rs-ends-options">
        <AmDatePickerFull
          :calendar-minimum-date="moment().format('YYYY-MM-DD')"
          :disabled="occurrenceType !== 'on'"
          :input-placeholder="labelsDisplay('recurrence_select_date')"
          @selected-date="selectedOccurrenceDate"
        />
        <div class="am-fs__rs-ends-after">
          <AmInputNumber
            v-model="occurrenceCount"
            :disabled="occurrenceType !== 'after'"
            :min="1"
            :max="100"
          />
          <span>{{ labelsDisplay('occurrences') }}</span>
        </div>
      </div>
    </div>
    <div class="am-fs__rs-summary">
      <p>{{ `${labelsDisplay('appointment_repeats')}:` }}</p>
      <p>
        {{ labelsDisplay('recurrence_every') }} {{ repeatInterval }}
        {{
          repeatTypes.find((item) => item.value === repeatType)
            ? repeatTypes.find((item) => item.value === repeatType).label
            : ''
        }}
        <span v-if="repeatType === 'weekly'" class="am-fa__rs-light"
          >{{ labelsDisplay('repeats_on') }}
          {{ weekDays.filter((i) => i.enabled).join(', ') }},</span
        >
        <span
          v-if="repeatType === 'monthly'" class="am-fa__rs-light"
        >
          {{ `${labelsDisplay('repeats_on')} ${monthlyTypes[repeatMonthly].label}, ` }}
        </span>
        <span class="am-fa__rs-light">
          {{ labelsDisplay('repeats_from') }}
          {{ moment().format('YYYY-MM-DD') }} {{ labelsDisplay('repeats_at') }}
          {{ moment().format('hh:mm') }}</span
        >
      </p>
      <p>
        <span v-if="occurrenceType === 'after'" class="am-fa__rs-light"
          >{{ labelsDisplay('number_of_recurrences') }}
          {{ occurrenceCount }}</span
        >
        <span v-else class="am-fa__rs-light"
          >{{ labelsDisplay('ends_on') }} {{ occurrenceDate }}</span
        >
      </p>
    </div>
  </div>
</template>

<script setup>
import moment from 'moment'
import AmInputNumber from '../../../../_components/input-number/AmInputNumber'
import AmSelect from '../../../../_components/select/AmSelect'
import AmOption from '../../../../_components/select/AmOption'
import AmRadioGroup from '../../../../_components/radio/AmRadioGroup'
import AmRadio from '../../../../_components/radio/AmRadio'
import AmDatePickerFull from '../../../../_components/date-picker-full/AmDatePickerFull.vue'
import { computed, ref, inject } from 'vue'
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation.js'
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

let props = defineProps({
  globalClass: {
    type: String,
    default: '',
  },
})

// Container Width
let cWidth = inject('containerWidth', 0)
let checkScreen = computed(() => cWidth.value < 560)

let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value].recurringStep.translations &&
      amCustomize.value[pageRenderKey.value].recurringStep.translations[
        label
      ] &&
      amCustomize.value[pageRenderKey.value].recurringStep.translations[label][
        langKey.value
      ]
      ? amCustomize.value[pageRenderKey.value].recurringStep.translations[
          label
        ][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

/************
 * Computed *
 ***********/

let repeatType = ref('day')

let repeatInterval = ref(1)

let occurrenceType = ref('after')

let occurrenceDate = ref(moment().format('YYYY-MM-DD'))

let occurrenceCount = ref(1)

let repeatMonthly = ref(0)

const repeatTypes = computed(() => {
  return [
    {
      label: labelsDisplay('recurrence_day'),
      labelPlural: labelsDisplay('recurrence_days'),
      value: 'daily',
    },
    {
      label: labelsDisplay('recurrence_week'),
      labelPlural: labelsDisplay('recurrence_weeks'),
      value: 'weekly',
    },
    {
      label: labelsDisplay('recurrence_month'),
      labelPlural: labelsDisplay('recurrence_months'),
      value: 'monthly',
    },
  ]
})

const monthlyTypes = computed(() => {
  return [
    {
      label: labelsDisplay('recurrence_specific_date'),
      value: 0,
    },
    {
      label: labelsDisplay('recurrence_first'),
      value: 1,
    },
    {
      label: labelsDisplay('recurrence_second'),
      value: 2,
    },
    {
      label: labelsDisplay('recurrence_third'),
      value: 3,
    },
    {
      label: labelsDisplay('recurrence_fourth'),
      value: 4,
    },
    {
      label: labelsDisplay('recurrence_last'),
      value: 5,
    },
  ]
})

const weekDays = ref([
  { label: 'Mon', value: 'monday', selected: false },
  { label: 'Tue', value: 'tuesday', selected: false },
  { label: 'Wed', value: 'wednesday', selected: false },
  { label: 'Thu', value: 'thursday', selected: false },
  { label: 'Fri', value: 'friday', selected: false },
  { label: 'Sat', value: 'saturday', selected: false },
  { label: 'Sun', value: 'sunday', selected: false },
])

function selectedOccurrenceDate(data) {
  occurrenceDate.value = data
}

// let selectedDayInMonth = computed(() => moment(activeAppointment.date, "YYYY-MM-DD").format('dddd'))

/**************
 * Navigation *
 *************/

function selectWeekDay(value) {
  let weekDay = weekDays.value.find((i) => i.value === value)
  weekDay.selected = !weekDay.selected
}

// * Colors
let amColors = inject('amColors')
let cssVars = computed(() => {
  return {
    '--am-c-rs-text': amColors.value.colorMainText,
    '--am-c-rs-text-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
    '--am-c-rs-bgr': amColors.value.colorMainBgr,
    '--am-c-rs-primary': amColors.value.colorPrimary,
    '--am-c-rs-inp-border': amColors.value.colorInpBorder,
  }
})
</script>

<script>
export default {
  name: 'RecurringStep',
  key: 'recurringStep',
  sidebarData: {
    label: 'recurring_step',
    icon: 'calendar-recurring',
    stepSelectedData: [],
    finished: false,
    selected: false,
  },
}
</script>

<style lang="scss">
#amelia-app-backend-new {
  #amelia-container {
    // rs - Recurring Step
    .am-fs__rs {
      & > * {
        $count: 10;
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

      p {
        margin-top: 16px;
        font-weight: 500;
        font-size: 15px;
        line-height: 24px;
        /* $shade-900 */
        color: var(--am-c-rs-text);
        margin-bottom: 4px;
      }

      span.am-fa__rs-light {
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
        align-items: center;
        margin: 16px 0;
        gap: 12px;
        max-width: 100%;

        &-choose {
          display: flex;
          flex: 0 0 auto;

          .am-radio-group {
            display: flex;
            gap: 16px;

            .am-radio-wrapper {
              display: flex;
              height: 40px;
              align-items: center;

              .am-radio {
                display: flex;
                align-items: center;

                .el-radio__input,
                .el-radio__label {
                  align-self: center;
                }
              }
            }
          }
        }

        &-options {
          display: flex;
          flex-direction: column;
          gap: 16px;
          max-width: 100%;
          width: 100%;

          .am-input-wrapper {
            max-width: 174px;
            width: 100%;
          }
        }

        &-after {
          display: flex;
          gap: 12px;
          width: 100%;

          .am-input-number {
            max-width: 174px;
            width: 100%;
          }

          & > span {
            flex: 0 0 auto;
            font-weight: 400;
            font-size: 15px;
            line-height: 40px;
            text-align: center;
            /* $shade-900 */
            color: var(--am-c-rs-text);
          }
        }
      }
      &-summary {
        p,
        span {
          font-weight: 400;
          font-size: 14px;
          line-height: 20px;
          /* $shade-600 */
          color: var(--am-c-rs-text-op60);
          margin: 0;
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
            //.el-input-number {
            //  max-width: 60%;
            //}
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
