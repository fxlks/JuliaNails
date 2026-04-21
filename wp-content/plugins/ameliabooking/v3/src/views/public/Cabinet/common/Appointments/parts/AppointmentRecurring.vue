<template>
  <div class="am-capai-recurr" :style="cssVars">
    <div class="am-capai-recurr__enable">
      <AmCheckbox
        v-model="enableRecurring"
        :label="'Enable Recurring appointment'"
      />
    </div>
    <div v-if="enableRecurring" class="am-capai-recurr__content">
      <RecurringSetup
        v-if="!recurringLoading && !recurringList"
        :start-date="
          moment(store.getters['appointment/getStartDate']).format('YYYY-MM-DD')
        "
        :start-time="store.getters['appointment/getStartTime']"
        :max-date="props.maximumBookingStartDate"
        :service="employeeService"
      >
      </RecurringSetup>
      <template v-else>
        <Skeleton v-if="!recurringList" :count="expectedDatesCount"></Skeleton>
        <div v-else class="am-capai-recurr__list">
          <div class="am-capai-recurr__list-heading">
            <div class="am-capai-recurr__list-heading__text">
              <span class="am-icon-refresh"></span>
              <div>{{ `${recurringList.length} appointments total` }}</div>
            </div>
            <div class="am-capai-recurr__list-heading__action" @click="() => { showRecurringList = !showRecurringList}">
              {{ showRecurringList ? amLabels.show_less : amLabels.show_more}}
            </div>
          </div>

          <template v-if="showRecurringList">
            <!-- Recurring Warning -->
            <WarningBlock
              v-if="recurringList.filter((i) => i.isSubstituteDate || i.isSubstituteTime).length"
              :heading="amLabels.recurring_unavailable_slots"
              :text="amLabels.recurring_sub_message1"
            />
            <!-- /Recurring Warning -->

            <!-- Recurring List Table -->
            <div class="am-capai-recurr__table">
              <!-- Row -->
              <div
                v-for="(item, index) in recurringList"
                :key="index"
                class="am-capai-recurr__row"
                :class="[{'am-warn': item.isSubstituteDate || item.isSubstituteTime}, props.responsiveClass]"
              >
                <!-- Index -->
                <div
                  class="am-capai-recurr__row-num"
                  :class="props.responsiveClass"
                >
                  {{ index + 1 }}
                </div>
                <!-- /Index -->

                <!-- Date -->
                <div
                  class="am-capai-recurr__row-date"
                  :class="[{ 'am-disable': !item.editing }, props.responsiveClass]"
                >
                  <AmDatePicker
                    v-model="item.date"
                    type="date"
                    :format="momentDateFormat()"
                    placeholder="Pick a day"
                    size="small"
                    :lang="localLanguage"
                    :disabled-date="(date, monthOrYear) => {return props.isDisabledDate(date, item.editing, false, monthOrYear)}"
                    :popper-class="props.slotsLoading ? 'slots-loader' : ''"
                    @panel-change="(date) => {fetchSlots(date, index, true)}"
                    @change="selectRecurringDate(index)"
                    @focus="fetchSlots(item.date, index, true)"
                  />
                </div>
                <!-- /Date -->

                <!-- Time -->
                <div
                  class="am-capai-recurr__row-time"
                  :class="[{ 'am-disable': !item.editing }, props.responsiveClass]"
                >
                  <AmSelect
                    v-model="item.time"
                    size="small"
                    :placeholder="`${amLabels.select_time}`"
                    :disabled="!item.date"
                  >
                    <AmOption
                      v-for="slot in item.editing
                      ? props.getFreeTimes(index, recurringList[index], false)
                      : recurringList[index].times"
                      :key="slot"
                      :value="slot"
                      :label="slot"
                    />
                  </AmSelect>
                </div>
                <!-- /Time -->

                <!-- Row Actions -->
                <div
                  class="am-capai-recurr__row-actions"
                  :class="props.responsiveClass"
                >
                  <DotsPopup
                    v-if="!item.editing"
                    :index="index"
                    @remove="removeRecurringAppointment"
                    @edit="editRecurringAppointment"
                  />
                  <AmButton
                    v-else
                    category="primary"
                    size="small"
                    @click="saveRecurringAppointment(index)"
                  >
                    {{ amLabels.save }}
                  </AmButton>
                </div>
                <!-- /Row Actions -->
              </div>
              <!-- /Row -->
            </div>
            <!-- /Recurring List Table -->
          </template>
        </div>
      </template>

      <div v-if="!recurringLoading" class="am-capai-recurr__actions">
        <AmButton
          v-if="!recurringList"
          category="primary"
          size="medium"
          @click="setRecurringAppointments()"
        >
          {{ amLabels.recurring_list }}
        </AmButton>

        <AmButton
          v-if="recurringList"
          category="secondary"
          type="plain"
          size="medium"
          @click="unsetRecurringAppointments()"
        >
          {{ amLabels.recurring_settings }}
        </AmButton>
      </div>
    </div>
  </div>
</template>

<script setup>
// * Import libraries
import moment from 'moment/moment'

// * Import from Vue
import { ref, inject, computed, watch } from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Import Composables
import { momentDateFormat } from '../../../../../../assets/js/common/date'
import {
  useExpectedDates,
  useProposedDates,
} from '../../../../../../assets/js/common/recurring'
import { useSortedDateStrings } from '../../../../../../assets/js/common/helper'
import { useAppointmentSlots } from '../../../../../../assets/js/public/slots'
import { useColorTransparency } from '../../../../../../assets/js/common/colorManipulation'

// * Import Components
import AmSelect from '../../../../../_components/select/AmSelect.vue'
import AmDatePicker from '../../../../../_components/datePicker/AmDatePicker.vue'
import AmOption from '../../../../../_components/select/AmOption.vue'
import AmButton from '../../../../../_components/button/AmButton.vue'
import AmCheckbox from '../../../../../_components/checkbox/AmCheckbox.vue'

// * Import Parts
import RecurringSetup from '../../../../Parts/RecurringSetup.vue'
import Skeleton from '../../Authentication/parts/Skeleton.vue'
import DotsPopup from '../../../../Parts/DotsPopup.vue'
import WarningBlock from "./common/WarningBlock.vue";

// * Props
let props = defineProps({
  responsiveClass: {
    type: String,
    default: '',
  },
  maximumBookingStartDate: {
    type: String,
    required: true,
  },
  slotsLoading: {
    type: Boolean,
    required: true,
  },
  slotsProps: {
    type: Object,
    required: true,
  },
  freeSlots: {
    type: Object,
    required: true,
  },
  getFreeTimes: {
    type: Function,
    required: true,
  },
  isDisabledDate: {
    type: Function,
    required: true,
  },
  getSelectedDatesTimes: {
    type: Function,
    required: true,
  },
})

// * Emits
let emits = defineEmits(['fetchSlots'])

// * Store
const store = useStore()

// * Settings
const amSettings = inject('settings')

// * Labels
let amLabels = inject('amLabels')

// * Language
let localLanguage = inject('localLanguage')

// * Employee Service
let employeeService = computed(
  () => store.getters['appointment/getEmployeeService']
)

let enableRecurring = ref(false)

let expectedDatesCount = ref(2)

let recurringLoading = ref(false)

let recurringList = inject('recurringList')

let showRecurringList = ref(true)

function setRecurringAppointments() {
  let endDate =
    store.getters['recurring/getOccurrenceType'] === 'On'
      ? moment(store.getters['recurring/getOccurrenceDate']).format(
          'YYYY-MM-DD'
        )
      : null

  let expectedDates = useExpectedDates(
    props.maximumBookingStartDate,
    moment(store.getters['appointment/getStartDate']).format('YYYY-MM-DD'),
    endDate,
    amSettings.general.numberOfDaysAvailableForBooking,
    store.getters['recurring/getRepeatType'],
    store.getters['recurring/getRepeatInterval'],
    store.getters['recurring/getDays'],
    store.getters['recurring/getMonthly']
  )

  expectedDatesCount.value =
    store.getters['recurring/getOccurrenceType'] === 'On'
      ? Object.keys(expectedDates).length
      : store.getters['recurring/getOccurrenceCount']

  recurringLoading.value = true

  let expectedDatesStrings = useSortedDateStrings(Object.keys(expectedDates))

  useAppointmentSlots(
    Object.assign(
      {
        startDateTime: moment(store.getters['appointment/getStartDate'])
          .startOf('month')
          .subtract(6, 'days')
          .format('YYYY-MM-DD'),
        endDateTime: expectedDatesStrings.length
          ? moment(expectedDatesStrings[expectedDatesStrings.length - 1])
              .endOf('month')
              .add(11, 'days')
              .format('YYYY-MM-DD')
          : moment()
              .add(amSettings.general.numberOfDaysAvailableForBooking, 'days')
              .format('YYYY-MM-DD'),
      },
      props.slotsProps
    ),
    null,
    (slots) => {
      let proposedDates = useProposedDates(
        employeeService.value,
        slots,
        expectedDates,
        moment(store.getters['appointment/getStartDate']).format('YYYY-MM-DD'),
        store.getters['appointment/getStartTime'],
        endDate,
        store.getters['recurring/getOccurrenceType'] === 'After'
          ? store.getters['recurring/getOccurrenceCount']
          : null
      )

      recurringList.value = []

      Object.keys(proposedDates).forEach((date) => {
        recurringList.value.push(
          Object.assign(
            {
              date: moment(date).toDate(),
              editing: false,
            },
            proposedDates[date]
          )
        )
      })

      recurringLoading.value = false
    }
  )
}

function unsetRecurringAppointments() {
  recurringList.value = null
}

function selectRecurringDate(index) {
  let date = moment(recurringList.value[index].date).format('YYYY-MM-DD')

  recurringList.value[index].times = Object.keys(props.freeSlots[date])

  let selectedDatesTimes = props.getSelectedDatesTimes(index, false)

  if (
    date in selectedDatesTimes &&
    recurringList.value[index].time in selectedDatesTimes[date]
  ) {
    recurringList.value[index].time = ''
  }
}

function removeRecurringAppointment(index) {
  recurringList.value.splice(index, 1)
}

function editRecurringAppointment(index) {
  recurringList.value[index].editing = true
}

function saveRecurringAppointment(index) {
  recurringList.value[index].editing = false
}

let targetedDate = ref(null)

function fetchSlots (selectedDate, recurringIndex, isNavigation = false) {
  if (!selectedDate && !targetedDate.value) {
    selectedDate = moment().toDate()
  } else if (!selectedDate && targetedDate.value) {
    selectedDate = targetedDate.value
  }

  targetedDate.value = selectedDate && isNavigation ? selectedDate : targetedDate.value

  emits('fetchSlots', selectedDate, recurringIndex, isNavigation)
}

// * Colors
let amColors = inject('amColors')
// * CSS Variables
let cssVars = computed(() => {
  return {
    '--am-c-recurr-bgr-op05': useColorTransparency(
      amColors.value.colorPrimary,
      0.05
    ),
    '--am-c-warning-op10': useColorTransparency(
      amColors.value.colorWarning,
      0.1
    ),
    '--am-c-main-text-op10': useColorTransparency(
      amColors.value.colorMainText,
      0.1
    ),
  }
})

watch(enableRecurring, (value) => {
  if (!value) {
    recurringList.value = null
  }
})
</script>

<style lang="scss">
@mixin am-cabinet-appointment-recurring {
  // am     - amelia
  // capai  - cabinet-panel-appointment-item
  // recurr - recurring
  .am-capai-recurr {
    display: flex;
    flex-direction: column;
    padding: 16px;
    border: 1px solid var(--am-c-inp-border);
    border-radius: 8px;
    gap: 16px;

    // Enable
    &__enable {
      .am-checkbox {
        min-height: unset;
      }
    }

    &__content {
      padding: 16px;
      background-color: var(--am-c-recurr-bgr-op05);
    }

    &__actions {
      display: flex;
      justify-content: flex-end;
      padding: 13px 0 0;
      margin: 24px 0 0;
      border-top: 1px solid var(--am-c-inp-border);
    }

    // Recurring Settings
    .am-fs__rs-on-week {
      width: unset;
      justify-content: unset;
      flex-wrap: wrap;
      gap: 10px;

      & > div {
        background-color: var(--am-c-main-bgr);

        &.am-fs__rs-on-week-selected {
          background-color: var(--am-c-primary);
          color: var(--am-c-main-bgr);
        }
      }
    }

    // Recurring List
    &__list {
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 24px;

      // Heading
      &-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 16px 0 0;

        &__text {
          display: flex;
          align-items: center;
          gap: 8px;
          font-size: 14px;
          font-style: normal;
          font-weight: 400;
          line-height: 1.42857;
          color: var(--am-c-main-text);

          .am-icon-refresh {
            font-size: 24px;
            color: var(--am-c-primary);
          }
        }

        &__action {
          font-size: 14px;
          font-style: normal;
          font-weight: 500;
          line-height: 1.42857;
          color: var(--am-c-primary);
          cursor: pointer;
        }
      }
    }

    &__table {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    &__row {
      display: flex;
      align-items: center;
      padding: 4px 8px;
      gap: 10px;
      border: 1px solid var(--am-c-main-bgr);
      background-color: var(--am-c-main-bgr);
      border-radius: 8px;

      &.am-rw {
        &-500 {
          flex-wrap: wrap;
          gap: 4px 10px;
        }
      }

      &.am-warn {
        border-color: var(--am-c-warning);
        background-color: var(--am-c-warning-op10);
      }

      &-num {
        width: 24px;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 1.42857;
        color: var(--am-c-main-text);
      }

      &-date {
        width: calc(60% - 60px);

        &.am-disable {
          pointer-events: none;

          .am-date-picker-wrapper .el-input .el-input__inner {
            background-color: transparent;
            border: none;
          }
        }

        &.am-rw {
          &-500 {
            width: calc(60% - 5px);
            order: 2;
          }

          &-400 {
            width: 100%;
          }
        }
      }

      &-time {
        width: calc(40% - 60px);

        &.am-disable {
          pointer-events: none;

          .am-select {
            .el-input__inner {
              background-color: transparent;
              border: none;
              box-shadow: none;
            }

            .el-input__suffix {
              display: none;
            }
          }
        }

        &.am-rw {
          &-500 {
            width: calc(40% - 5px);
            order: 2;
          }

          &-400 {
            width: 100%;
          }
        }
      }

      &-actions {
        width: 66px;
        display: flex;
        justify-content: flex-end;

        &.am-rw {
          &-500 {
            width: calc(100% - 35px);
            order: 1;
          }

          &-400 {
            .am-button {
              width: 100%;
            }
          }
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-appointment-recurring;
}
</style>