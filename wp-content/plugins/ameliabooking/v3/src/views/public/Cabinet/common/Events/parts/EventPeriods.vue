<template>
  <el-form
    ref="periodsFormRef"
    :model="periodsFormData"
    label-position="top"
    class="am-capei-per"
    :style="cssVars"
  >
    <div class="am-capei-per__segment">
      <template v-for="(item, index) in periodsFormData.periods" :key="index">
        <!-- Event Start Date -->
        <el-form-item
          :prop="'periods.' + index + '.startDate'"
          :rules="{
            required: true,
            message: amLabels.select_date_warning,
            trigger: ['blur', 'change'],
          }"
          :label="`${amLabels.start_date}:`"
          class="am-capei-per__item am-w-50"
          :class="responsiveClass"
        >
          <AmDatePicker
            v-model="item.startDate"
            :format="momentDateFormat()"
            :lang="localLanguage"
            :disabled-date="(date) => isDisabledStartDate(date, item.endDate)"
          />
        </el-form-item>
        <!-- / Event Start Date -->

        <!-- Event End Date -->
        <el-form-item
          :prop="'periods.' + index + '.endDate'"
          :rules="{
            required: true,
            message: amLabels.select_date_warning,
            trigger: ['blur', 'change'],
          }"
          :label="`${amLabels.end_date}:`"
          class="am-capei-per__item am-w-50"
          :class="responsiveClass"
        >
          <AmDatePicker
            v-model="item.endDate"
            :format="momentDateFormat()"
            :lang="localLanguage"
            :disabled-date="(date) => isDisabledEndDate(date, item.startDate)"
          />
        </el-form-item>
        <!-- /Event End Date -->

        <!-- Event Start Time -->
        <el-form-item
          :prop="'periods.' + index + '.startTime'"
          :rules="{
            required: true,
            message: amLabels.select_time_warning,
            trigger: ['blur', 'change'],
          }"
          :label="`${amLabels.start_time}:`"
          class="am-capei-per__item am-w-50"
          :class="responsiveClass"
        >
          <AmTimeSelect
            v-model="item.startTime"
            :start="'00:00'"
            :end="
              useSecondsInTime(
                useTimeInSeconds('24:00') - amSettings.general.timeSlotLength
              )
            "
            :min-time="null"
            :max-time="item.endTime"
            :step="useSecondsInTime(amSettings.general.timeSlotLength)"
            :clearable="false"
            :placeholder="amLabels.select_time"
          />
        </el-form-item>
        <!-- /Event Start Time -->

        <!-- Event End Time -->
        <el-form-item
          :prop="'periods.' + index + '.endTime'"
          :rules="{
            required: true,
            message: amLabels.select_time_warning,
            trigger: ['blur', 'change'],
          }"
          :label="`${amLabels.end_time}:`"
          class="am-capei-per__item am-w-50"
          :class="responsiveClass"
        >
          <AmTimeSelect
            v-model="item.endTime"
            :disabled="!item.startTime"
            :start="'00:00'"
            :end="'24:00'"
            :min-time="item.startTime"
            :max-time="null"
            :step="useSecondsInTime(amSettings.general.timeSlotLength)"
            :clearable="false"
            :placeholder="amLabels.select_time"
          />
        </el-form-item>
        <!-- /Event End Time -->

        <div
          v-if="periodsFormData.periods.length > 1"
          class="am-capei-per__delete"
        >
          <AmButton
            :size="'default'"
            category="danger"
            @click="removePeriod(index)"
          >
            {{ amLabels.delete_period }}
          </AmButton>
        </div>
      </template>

      <AmButton size="default" @click="addPeriod()">
        {{ amLabels.add_period }}
      </AmButton>
    </div>

    <div class="am-capei-per__segment">
      <AmCheckbox
        v-model="periodsFormData.bookingOpensDisabled"
        :label="amLabels.event_booking_opens_now"
      />

      <template v-if="!periodsFormData.bookingOpensDisabled">
        <!-- Opens Date -->
        <el-form-item
          :label="`${amLabels.date}:`"
          :prop="'bookingOpensDate'"
          :rules="{
            required: true,
            message: amLabels.select_date_warning,
            trigger: ['blur', 'change'],
          }"
          class="am-capei-per__item am-w-50"
          :class="responsiveClass"
        >
          <AmDatePicker
            v-model="periodsFormData.bookingOpensDate"
            :format="momentDateFormat()"
            :lang="localLanguage"
            :disabled-date="isInPeriod"
          />
        </el-form-item>
        <!-- /Opens Date -->

        <!-- Opens Time -->
        <el-form-item
          :label="`${amLabels.time}:`"
          :prop="'bookingOpensTime'"
          :rules="{
            required: true,
            message: amLabels.select_time_warning,
            trigger: ['blur', 'change'],
          }"
          class="am-capei-per__item am-w-50"
          :class="responsiveClass"
        >
          <AmTimeSelect
            v-model="periodsFormData.bookingOpensTime"
            :placeholder="amLabels.select_time"
            :step="useSecondsInTime(amSettings.general.timeSlotLength)"
            :start="'00:00'"
            :end="'24:00'"
            :clearable="false"
          />
        </el-form-item>
        <!-- /Opens Time -->
      </template>

      <AmCheckbox
        v-if="recurringEnabled"
        v-model="periodsFormData.bookingOpensRec"
      >
        {{ amLabels.apply_to_all }}
        <el-tooltip effect="dark" placement="top">
          <template #content>
            <div v-html="amLabels.event_booking_closes_apply"></div>
          </template>
          <IconComponent class="am-tooltip__trigger" icon="info-reverse" />
        </el-tooltip>
      </AmCheckbox>
    </div>

    <div class="am-capei-per__segment">
      <AmCheckbox
        v-model="periodsFormData.bookingClosesDisabled"
        :label="amLabels.event_booking_closes_after"
      />

      <template v-if="!periodsFormData.bookingClosesDisabled">
        <!-- Closes Date -->
        <el-form-item
          :label="`${amLabels.date}:`"
          :prop="'bookingClosesDate'"
          :rules="{
            required: true,
            message: amLabels.select_date_warning,
            trigger: ['blur', 'change'],
          }"
          class="am-capei-per__item am-w-50"
          :class="responsiveClass"
        >
          <AmDatePicker
            v-model="periodsFormData.bookingClosesDate"
            :format="momentDateFormat()"
            :lang="localLanguage"
            :disabled-date="isInPeriod"
          />
        </el-form-item>
        <!-- /Closes Date -->

        <!-- Closes Time -->
        <el-form-item
          :label="`${amLabels.time}:`"
          :prop="'bookingClosesTime'"
          :rules="{
            required: true,
            message: amLabels.select_time_warning,
            trigger: ['blur', 'change'],
          }"
          class="am-capei-per__item am-w-50"
          :class="responsiveClass"
        >
          <AmTimeSelect
            v-model="periodsFormData.bookingClosesTime"
            :step="useSecondsInTime(amSettings.general.timeSlotLength)"
            :start="'00:00'"
            :end="'24:00'"
            :clearable="false"
            :placeholder="amLabels.select_time"
          />
        </el-form-item>
        <!-- /Closes Time -->
      </template>

      <AmCheckbox
        v-if="recurringEnabled"
        v-model="periodsFormData.bookingClosesRec"
      >
        {{ amLabels.apply_to_all }}
        <el-tooltip effect="dark" placement="top">
          <template #content>
            <div v-html="amLabels.event_booking_closes_apply"></div>
          </template>
          <IconComponent class="am-tooltip__trigger" icon="info-reverse" />
        </el-tooltip>
      </AmCheckbox>
    </div>
  </el-form>
</template>

<script setup>
// * Import from Vue
import { ref, inject, computed } from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Import from library
import moment from 'moment/moment'

// * Import Composables
import {
  momentDateFormat,
  useSecondsInTime,
  useTimeInSeconds,
} from '../../../../../../assets/js/common/date'
import { useColorTransparency } from '../../../../../../assets/js/common/colorManipulation'
import { useResponsiveClass } from '../../../../../../assets/js/common/responsive'

// * Import Components
import AmDatePicker from '../../../../../_components/datePicker/AmDatePicker.vue'
import AmTimeSelect from '../../../../../_components/time-select/AmTimeSelect.vue'
import AmButton from '../../../../../_components/button/AmButton.vue'

// * Import Parts
import AmCheckbox from '../../../../../_components/checkbox/AmCheckbox.vue'
import IconComponent from '../../../../../_components/icons/IconComponent.vue'

let props = defineProps({
  pageWidth: {
    type: Number,
    default: 0,
  },
})

let responsiveClass = computed(() => {
  return useResponsiveClass(props.pageWidth)
})

// * Store
const store = useStore()

// * Settings
const amSettings = inject('settings')

// * Labels
let amLabels = inject('amLabels')

// * Language
let localLanguage = inject('localLanguage')

let minDate = ref(moment())

function isInPeriod (value) {
  let validEnds = periodsFormData.value.periods
    .map(item => item.endDate)
    .filter(end => end instanceof Date && !isNaN(end))

  return moment(value) < minDate.value || moment(value) > moment(new Date(Math.max(...validEnds)))
}

function isDisabledStartDate(value, endDate) {
  const isBeforeToday = moment(value) < minDate.value
  const isAfterEndDate = endDate ? moment(value) > moment(endDate) : false
  return isAfterEndDate || isBeforeToday
}

function isDisabledEndDate(value, startDate) {
  if (startDate) return moment(value) < moment(startDate)
  return moment(value) < minDate.value
}

function addPeriod() {
  let validData = true

  periodsFormRef.value.validate((valid) => {
    if (!valid) {
      validData = false
    }
  })

  if (!validData) {
    return
  }

  periodsFormData.value.periods.push({
    eventId: store.getters['event/getId'] ? store.getters['event/getId'] : null,
    startDate: null,
    endDate: null,
    startTime: null,
    endTime: null,
  })
}

function removePeriod (index) {
  if (store.getters['event/getId'] && periodsFormData.value.periods[index]?.id && periodsFormData.value.periods[index].id) {
    periodsFormData.value.periods.forEach((period, periodIndex) => {
      if (periodIndex !== index && !period?.id) {
        periodsFormData.value.periods[periodIndex].id = periodsFormData.value.periods[index].id
        periodsFormData.value.periods[periodIndex].eventId = periodsFormData.value.periods[index].eventId
        periodsFormData.value.periods[periodIndex].appleCalendarEventId = periodsFormData.value.periods[index].appleCalendarEventId
        periodsFormData.value.periods[periodIndex].googleCalendarEventId = periodsFormData.value.periods[index].googleCalendarEventId
        periodsFormData.value.periods[periodIndex].googleMeetUrl = periodsFormData.value.periods[index].googleMeetUrl
        periodsFormData.value.periods[periodIndex].outlookCalendarEventId = periodsFormData.value.periods[index].outlookCalendarEventId
        periodsFormData.value.periods[periodIndex].microsoftTeamsUrl = periodsFormData.value.periods[index].microsoftTeamsUrl
      }
    })
  }

  periodsFormData.value.periods.splice(index, 1)
}

let recurringEnabled = computed(() => {
  return store.getters['event/getRecurringEnabled']
})

let periodsFormRef = ref(null)

let periodsFormData = ref({
  periods: computed({
    get: () => store.getters['event/getPeriods'],
    set: (val) => {
      store.commit('event/setPeriods', val)
    },
  }),
  bookingOpensDisabled: computed({
    get: () => store.getters['event/getBookingOpensDisabled'],
    set: (val) => {
      store.commit('event/setBookingOpensDisabled', val)
    },
  }),
  bookingOpensDate: computed({
    get: () => store.getters['event/getBookingOpensDate'],
    set: (val) => {
      store.commit('event/setBookingOpensDate', val)
    },
  }),
  bookingOpensTime: computed({
    get: () => store.getters['event/getBookingOpensTime'],
    set: (val) => {
      store.commit('event/setBookingOpensTime', val)
    },
  }),
  bookingOpensRec: computed({
    get: () => store.getters['event/getBookingOpensRec'],
    set: (val) => {
      store.commit('event/setBookingOpensRec', val)
    },
  }),
  bookingClosesDisabled: computed({
    get: () => store.getters['event/getBookingClosesDisabled'],
    set: (val) => {
      store.commit('event/setBookingClosesDisabled', val)
    },
  }),
  bookingClosesDate: computed({
    get: () => store.getters['event/getBookingClosesDate'],
    set: (val) => {
      store.commit('event/setBookingClosesDate', val)
    },
  }),
  bookingClosesTime: computed({
    get: () => store.getters['event/getBookingClosesTime'],
    set: (val) => {
      store.commit('event/setBookingClosesTime', val)
    },
  }),
  bookingClosesRec: computed({
    get: () => store.getters['event/getBookingClosesRec'],
    set: (val) => {
      store.commit('event/setBookingClosesRec', val)
    },
  }),
})

// * Expose
defineExpose({
  periodsFormRef,
})

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-capei-per-text': amColors.value.colorMainText,
    '--am-c-capei-per-text-op03': useColorTransparency(
      amColors.value.colorMainText,
      0.03
    ),
    '--am-c-capei-per-inp-bgr-op03': useColorTransparency(
      amColors.value.colorInpBgr,
      0.03
    ),
  }
})
</script>

<style lang="scss">
@mixin cabinet-event-periods-block {
  // am - amelia
  // capei - cabinet-panel-event-item
  // per - periods
  .am-capei-per {
    display: flex;
    flex-direction: column;
    flex-wrap: wrap;
    gap: 16px;
    align-items: center;
    width: 100%;

    &__segment {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      align-items: center;
      width: 100%;
      border-radius: 8px;
      padding: 16px;
      background-color: var(--am-c-capei-per-text-op03);
    }

    &__item {
      width: 100%;

      &.am-w-50 {
        width: calc(50% - 8px);

        &.am-rw-460 {
          width: 100%;
        }
      }

      &.el-form-item {
        margin-bottom: 0;

        &.is-required:not(.is-no-asterisk) > .el-form-item__label:before {
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

    &__delete {
      width: 100%;
      border-bottom: 1px solid var(--am-c-inp-border);
      padding: 0 0 16px;
    }

    .am-checkbox {
      min-height: auto;

      .el-checkbox {
        &__input {
          padding: 0;
          align-self: center;
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include cabinet-event-periods-block;
}
</style>