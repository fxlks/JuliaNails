<template>
  <div
    class="am-capei-recurr"
    :style="cssVars"
  >
    <div class="am-capei-recurr__header">
      <div class="am-capei-recurr__header-text">
        {{ amLabels.event_recurring_check }}
      </div>
      <Am-Switch
        v-model="recurringFormData.enabled"
        :disabled="!periods.length || !periods[0].startDate || !periods[0].endDate"
        :aria-label="amLabels.event_recurring_check"
      />
    </div>

    <!-- Recurring form -->
    <el-form
      v-if="recurringFormData.enabled && periods.length && periods[0].startDate && periods[0].endDate"
      ref="recurringFormRef"
      :rules="rules"
      :model="recurringFormData"
      label-position="top"
      class="am-capei-recurr__form"
    >
      <!-- Repeat number -->
      <el-form-item
        :label="`${amLabels.repeat_every}`"
        class="am-capei-recurr__form-item am-ws-150"
        :class="responsiveClass"
      >
        <AmInputNumber
          v-model="recurringFormData.cycleInterval"
          :min="1"
          :disabled="props.recurringUntil !== null"
        />
      </el-form-item>
      <!-- /Repeat number -->

      <!-- Repeat type -->
      <el-form-item
        :prop="'cycle'"
        class="am-capei-recurr__form-item am-inline-ws-150 am-align-end"
        :class="responsiveClass"
      >
        <AmSelect
          v-model="recurringFormData.cycle"
          clearable
          :placeholder="amLabels.select_repeat_period"
          :disabled="props.recurringUntil !== null"
        >
          <AmOption
            v-for="(entity, index) in recurringPeriods"
            :key="index"
            :value="entity.value"
            :label="entity.label"
          />
        </AmSelect>
      </el-form-item>
      <!-- /Repeat type -->

      <AmRadioGroup
        v-if="recurringFormData.cycle === 'monthly'"
        v-model="recurringFormData.monthlyRepeat"
        class="am-w-100"
        :disabled="props.recurringUntil !== null"
      >
        <el-form-item
          class="am-capei-recurr__form-item"
        >
          <AmRadio
            :value="'each'"
          >
            {{ amLabels.recurrence_each }}
          </AmRadio>
        </el-form-item>

        <el-form-item
          :prop="'on'"
          class="am-capei-recurr__form-item"
        >
          <AmRadio
            :value="'on'"
          >
            {{ amLabels.recurrence_on }}
          </AmRadio>
        </el-form-item>

        <el-form-item
          class="am-capei-recurr__form-item am-w-50"
          :class="responsiveClass"
        >
          <AmSelect
            v-model="recurringFormData.monthlyOnRepeat"
            :disabled="recurringFormData.monthlyRepeat === 'each' || props.recurringUntil !== null"
            :placeholder="amLabels.select_repeat_period"
          >
            <AmOption
              v-for="(entity, index) in monthlyWeekDayRepeat"
              :key="index"
              :value="entity.value"
              :label="entity.label"
            />
          </AmSelect>
        </el-form-item>

        <el-form-item
          class="am-capei-recurr__form-item am-w-50"
          :class="responsiveClass"
        >
          <AmSelect
            v-model="recurringFormData.monthlyOnDay"
            :disabled="recurringFormData.monthlyRepeat === 'each'"
            :placeholder="amLabels.select_repeat_period"
          >
            <AmOption
              v-for="(entity, index) in weekDays"
              :key="index"
              :value="entity.value"
              :label="entity.label"
            />
          </AmSelect>
        </el-form-item>
      </AmRadioGroup>

      <!-- Repeat range -->
      <el-form-item
        :label="amLabels.event_recurring_until"
        :prop="'until'"
        class="am-capei-recurr__form-item"
      >
        <AmDatePicker
          v-model="recurringFormData.until"
          :format="momentDateFormat()"
          :lang="localLanguage"
          :disabled-date="isDisabledRecurringUntil"
          :placeholder="amLabels.select_date"
        />
      </el-form-item>
      <!-- /Repeat range -->
    </el-form>
    <!-- /Recurring form -->
  </div>
</template>

<script setup>
// * Import from Vue
import { ref, inject, computed } from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Import from library
import moment from "moment/moment";

// * Import Composables
import {
  momentDateFormat,
} from "../../../../../../assets/js/common/date";
import { useColorTransparency } from "../../../../../../assets/js/common/colorManipulation";
import { useResponsiveClass } from "../../../../../../assets/js/common/responsive";

// * Import Components
import AmSelect from "../../../../../_components/select/AmSelect.vue"
import AmDatePicker from "../../../../../_components/datePicker/AmDatePicker.vue"
import AmOption from "../../../../../_components/select/AmOption.vue";

// * Import Parts
import AmInputNumber from "../../../../../_components/input-number/AmInputNumber.vue";
import AmRadio from "../../../../../_components/radio/AmRadio.vue";
import AmRadioGroup from "../../../../../_components/radio/AmRadioGroup.vue";
import AmSwitch from "../../../../../_components/switch/AmSwitch.vue";

// * Props
let props = defineProps({
  pageWidth: {
    type: Number,
    default: 0,
  },
  recurringUntil: {
    type: Object,
    default: null,
  },
});

let responsiveClass = computed(() => {
  return useResponsiveClass(props.pageWidth)
});

// * Store
const store = useStore()

// * Labels
let amLabels = inject('amLabels')

// * Language
let localLanguage = inject('localLanguage')

let recurringPeriods = ref([
  {
    label: amLabels.value.day,
    value: 'daily'
  },
  {
    label: amLabels.value.week,
    value: 'weekly'
  },
  {
    label: amLabels.value.month,
    value: 'monthly'
  },
  {
    label: amLabels.value.year,
    value: 'yearly'
  }
])

let monthlyWeekDayRepeat = ref([
  {
    label: amLabels.value.recurring_date_first,
    value: 'first'
  },
  {
    label: amLabels.value.recurring_date_second,
    value: 'second'
  },
  {
    label: amLabels.value.recurring_date_third,
    value: 'third'
  },
  {
    label: amLabels.value.recurring_date_fourth,
    value: 'fourth'
  },
  {
    label: amLabels.value.recurring_date_fifth,
    value: 'fifth'
  },
  {
    label: amLabels.value.recurring_date_last,
    value: 'last'
  }
])

let weekDays = [
  {
    label: amLabels.value.weekday_monday,
    value: 'monday'
  },
  {
    label: amLabels.value.weekday_tuesday,
    value: 'tuesday'
  },
  {
    label: amLabels.value.weekday_wednesday,
    value: 'wednesday'
  },
  {
    label: amLabels.value.weekday_thursday,
    value: 'thursday'
  },
  {
    label: amLabels.value.weekday_friday,
    value: 'friday'
  },
  {
    label: amLabels.value.weekday_saturday,
    value: 'saturday'
  },
  {
    label: amLabels.value.weekday_sunday,
    value: 'sunday'
  }
]

/********
 * Form *
 ********/
let recurringFormRef = ref(null)

let recurringFormData = ref({
  enabled: computed({
    get: () => store.getters['event/getRecurringEnabled'],
    set: (val) => {
      store.commit('event/setRecurringEnabled', val)
    }
  }),
  cycle: computed({
    get: () => store.getters['event/getRecurringCycle'],
    set: (val) => {
      store.commit('event/setRecurringCycle', val)
    }
  }),
  cycleInterval: computed({
    get: () => store.getters['event/getRecurringCycleInterval'],
    set: (val) => {
      store.commit('event/setRecurringCycleInterval', val)
    }
  }),
  monthDate: computed({
    get: () => store.getters['event/getRecurringMonthDate'],
    set: (val) => {
      store.commit('event/setRecurringMonthDate', val)
    }
  }),
  monthlyOnDay: computed({
    get: () => store.getters['event/getRecurringMonthlyOnDay'],
    set: (val) => {
      store.commit('event/setRecurringMonthlyOnDay', val)
    }
  }),
  monthlyOnRepeat: computed({
    get: () => store.getters['event/getRecurringMonthlyOnRepeat'],
    set: (val) => {
      store.commit('event/setRecurringMonthlyOnRepeat', val)
    }
  }),
  monthlyRepeat: computed({
    get: () => store.getters['event/getRecurringMonthlyRepeat'],
    set: (val) => {
      store.commit('event/setRecurringMonthlyRepeat', val)
    }
  }),
  order: computed({
    get: () => store.getters['event/getRecurringOrder'],
    set: (val) => {
      store.commit('event/setRecurringOrder', val)
    }
  }),
  until: computed({
    get: () => store.getters['event/getRecurringUntil'],
    set: (val) => {
      store.commit('event/setRecurringUntil', val)
    }
  }),
})

let rules = computed(() => {
  return {
    cycle: [
      {
        required: true,
        message: amLabels.value.select_cycle_warning,
        trigger: ['submit', 'change'],
      }
    ],
    until: [
      {
        required: true,
        message: amLabels.value.select_date_warning,
        trigger: ['submit', 'change'],
      }
    ],
    monthDate: [
      {
        required: true,
        message: amLabels.value.select_date_warning,
        trigger: ['submit', 'change'],
      }
    ],
  }
})

let periods = computed(() => store.getters['event/getPeriods'])

let minDate = computed(() => {
  return props.recurringUntil !== null
    ? props.recurringUntil
    : (periods.value.length ? moment(periods.value[0].startDate, 'YYYY-MM-DD') : moment())
})

function isDisabledRecurringUntil(value) {
  return moment(value) <= minDate.value
}

// * Expose
defineExpose({
  recurringFormRef
})

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-capei-recurr-text': amColors.value.colorMainText,
    '--am-c-capei-recurr-text-op03': useColorTransparency(amColors.value.colorMainText, 0.03),
    '--am-c-capei-recurr-inp-bgr-op03': useColorTransparency(amColors.value.colorInpBgr, 0.03),
    '--am-c-capei-recurr-bgr-op03': useColorTransparency(amColors.value.colorMainText, 0.03),
  }
})
</script>

<style lang="scss">
@mixin cabinet-event-recurring-block {
  // am     - amelia
  // capei  - cabinet-panel-event-item
  // recurr - recurring
  .am-capei-recurr {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 16px;
    align-items: center;
    width: 100%;
    border: 1px solid var(--am-c-inp-border);
    border-radius: 8px;
    padding: 16px;

    &__header {
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      width: 100%;

      &-text {
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        color: var(--am-c-capei-recurr-text);
      }
    }

    .am-checkbox {
      min-height: auto;

      &-wrapper {
        width: 100%;
        max-width: 100%;
      }

      &__input {
        padding: 0;
        align-self: center;
      }
    }

    &__form {
      width: 100%;
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      background-color: var(--am-c-capei-recurr-bgr-op03);
      padding: 16px;

      &-item {
        width: 100%;

        // ws - width static
        &.am-ws-150 {
          width: 150px;
        }

        &.am-inline-ws-150 {
          width: calc(100% - 166px);

          &.am-rw-460 {
            width: 100%;
          }
        }

        &.am-w-50 {
          width: calc(50% - 8px);

          &.am-rw-460 {
            width: 100%;
          }
        }

        &.am-align {
          &-end {
            align-self: flex-end;
          }
        }

        &.el-form-item {
          margin-bottom: 0;

          &.is-required:not(.is-no-asterisk)>.el-form-item__label:before {
            color: var(--am-c-error);
          }
        }

        .el-form-item {
          &__error {
            color: var(--am-c-error);
            padding: 0;
            line-height: 1.6;
          }
        }
      }
    }

    .am-radio-group {
      width: 100%;
      max-width: 100%;
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      gap: 16px;

      .el-radio {
        min-height: auto;
      }

      &-wrapper {
        &.am-w-100 {
          width: 100%;
          max-width: 100%;
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include cabinet-event-recurring-block;
}
</style>