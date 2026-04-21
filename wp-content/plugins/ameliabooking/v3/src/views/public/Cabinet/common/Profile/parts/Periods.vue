<template>
  <div
    v-show="props.enabled"
    class="am-caeper"
    :style="cssVars"
  >
    <div
      v-if="props.headerVisible && periods.length"
      class="am-caeper__heading"
      :class="props.responsiveClass"
    >
      <div class="am-caeper__heading-item am-w-20">
        {{ amLabels.time }}
      </div>
      <div class="am-caeper__heading-item am-w-40">
        {{ amLabels.service_colon }}
      </div>
      <div class="am-caeper__heading-item am-w-40">
        {{ amLabels.location }}
      </div>
    </div>

    <div
      v-for="(period, periodIndex) in periods"
      :key="periodIndex"
      class="am-caeper__content"
    >
      <div
        class="am-caeper__content-item am-w-20"
        :class="props.responsiveClass"
      >
        {{ period.startTime ? period.startTime.substring(0, 5) : '' }} - {{ period.endTime ? period.endTime.substring(0, 5) : '' }}
      </div>
      <div
        class="am-caeper__content-item am-w-40"
        :class="[props.responsiveClass, {'am-cabinet-empty': !period.periodServiceList.length}]"
      >
        {{ period.periodServiceList.filter(id => employeeServices.map(i => i.id).indexOf(id) !== -1).map(id => services.find(s => s.id === id).name).join(', ') }}
      </div>
      <div
        class="am-caeper__content-item am-w-40"
        :class="[props.responsiveClass, {'am-cabinet-empty': !period.periodLocationList.length}]"
      >
        {{ period.periodLocationList.map(id => locations.find(l => l.id === id).name).join(', ') }}
      </div>
      <DotsPopup
        v-if="props.editVisible"
        :index="periodIndex"
        @remove="periods.splice(periodIndex, 1)"
        @edit="editedPeriodIndex = periodIndex"
      >
      </DotsPopup>
    </div>

    <span v-if="timeOuts.length">{{ amLabels.breaks }}</span>

    <div
      v-if="props.headerVisible && timeOuts.length"
      class="am-caeper__heading"
      :class="props.responsiveClass"
    >
      <div class="am-caeper__heading-item am-w-20">
        {{ amLabels.time }}
      </div>
    </div>

    <div
      v-for="(timeOut, breakIndex) in timeOuts"
      :key="breakIndex"
      class="am-caeper__content"
    >
      <div
        class="am-caeper__content-item am-w-20"
        :class="props.responsiveClass"
      >
        {{ timeOut.startTime ? timeOut.startTime.substring(0, 5) : '' }} - {{ timeOut.endTime ? timeOut.endTime.substring(0, 5) : '' }}
      </div>
      <DotsPopup
        v-if="props.editVisible"
        :index="breakIndex"
        @remove="timeOuts.splice(breakIndex, 1)"
        @edit="editedTimeOutIndex = breakIndex"
      >
      </DotsPopup>
    </div>

    <div
      v-if="props.footerVisible"
      class="am-caeper__footer"
      :class="[{ 'am-error': props.error }, props.responsiveClass]"
    >
      <div v-if="props.error" class="am-caeper__error">
        {{ props.error }}
      </div>
      <AmButton
        v-if="canAddPeriod()"
        prefix="plus"
        size="small"
        @click="addPeriod()"
      >
        {{ amLabels.add_period }}
      </AmButton>
<!--      <AmButton-->
<!--        v-if="periods.length"-->
<!--        prefix="plus"-->
<!--        size="small"-->
<!--        @click="addBreak()"-->
<!--      >-->
<!--        {{ amLabels.add_break }}-->
<!--      </AmButton>-->
      <AmButton
        v-if="props.applyToAllDaysVisible"
        size="small"
        category="secondary"
        type="plain"
        @click="applyToAllDays"
      >
        {{ amLabels.apply_to_all_days }}
      </AmButton>
    </div>

    <Period
      :title="props.title"
      :visibility="editedPeriodIndex !== null"
      :origin="props.type"
      :period-index="editedPeriodIndex"
      :origin-index="index"
      :is-new="isNew"
      @close="() => {
        editedPeriodIndex = null
        isNew = null
      }"
    >
    </Period>

    <TimeOut
      :title="amLabels.break_hours"
      :visibility="editedTimeOutIndex !== null"
      :origin="props.type"
      :time-out-index="editedTimeOutIndex"
      :origin-index="index"
      :is-new="false"
      :is-break="true"
      @close="() => {
        editedTimeOutIndex = null
      }"
    >
    </TimeOut>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  inject,
  computed,
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Composables
import { useColorTransparency } from "../../../../../../assets/js/common/colorManipulation";
import { useEmployeeServices } from "../../../../../../assets/js/common/employee";

// * Dedicated components
import Period from "./Period.vue";
import TimeOut from "./TimeOut.vue";
import DotsPopup from "../../../../Parts/DotsPopup.vue";
import AmButton from "../../../../../_components/button/AmButton.vue";

// * Component properties
let props = defineProps({
  title: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: ''
  },
  index: {
    type: Number,
    default: 0
  },
  enabled: {
    type: Boolean,
    default: false
  },
  headerVisible: {
    type: Boolean,
    default: true
  },
  applyToAllDaysVisible: {
    type: Boolean,
    default: true
  },
  footerVisible: {
    type: Boolean,
    default: true
  },
  editVisible: {
    type: Boolean,
    default: true
  },
  error: {
    type: String,
    default: ''
  },
  responsiveClass: {
    type: String,
    default: '',
  }
})

// * Emits
const emits = defineEmits(['addPeriod'])

// * Store
let store = useStore()

// * Labels
let amLabels = inject('amLabels')

let locations = computed(() => store.getters['entities/getLocations'])

let services = computed(() => store.getters['entities/getServices'])

let employeeServices = computed(() => useEmployeeServices(store))

let periods = computed(() => {
  let employee = store.getters['employee/getEmployee']

  switch (props.type) {
    case 'weekDayList':
      return employee.weekDayList[props.index].periodList

    case 'specialDayList':
      return employee.specialDayList[props.index].periodList
  }

  return []
})

let timeOuts = computed(() => {
  let employee = store.getters['employee/getEmployee']

  if (props.type === 'weekDayList') {
    return employee.weekDayList[props.index].timeOutList
  }

  return []
})

let editedPeriodIndex = ref(null)

let editedTimeOutIndex = ref(null)

let isNew = ref(null)

function addPeriod () {
  periods.value.push({
    id: null,
    locationId: null,
    startTime: '',
    endTime: '',
    periodLocationList: [],
    periodServiceList: [],
    editing: true,
  })

  isNew.value = true

  editedPeriodIndex.value = periods.value.length - 1

  emits('addPeriod')
}

// function addBreak () {
//   timeOuts.value.push({
//     id: null,
//     startTime: '',
//     endTime: '',
//     editing: true,
//   })
//
//   editedTimeOutIndex.value = timeOuts.value.length - 1
// }

function applyToAllDays () {
  let clonedPeriods = []

  store.getters['employee/getEmployee'].weekDayList[props.index].periodList.forEach((period) => {
    clonedPeriods.push({
      id: null,
      locationId: null,
      startTime: period.startTime,
      endTime: period.endTime,
      periodLocationList: [...period.periodLocationList],
      periodServiceList: [...period.periodServiceList],
      editing: false,
    })
  })

  store.getters['employee/getEmployee'].weekDayList.forEach((weekDay, index) => {
    if (index !== props.index) {
      weekDay.periodList = JSON.parse(JSON.stringify(clonedPeriods))
    }
  })
}

function canAddPeriod () {
  return !periods.value.length ||
    !periods.value.filter(i => i.startTime && i.endTime).length ||
    periods.value[periods.value.length - 1].endTime !== '00:00'
}

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-caeper-bgr': amColors.value.colorMainBgr,
    '--am-c-caeper-text': amColors.value.colorMainText,
    '--am-c-caeper-text-op80': useColorTransparency(amColors.value.colorMainText, 0.8),
  }
})
</script>

<script>
export default {
  name: 'WorkPeriods'
}
</script>

<style lang="scss">
@mixin am-cabinet-employee-periods {
  // am - amelia
  // caeper - cabinet employee periods
  .am-caeper {
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-width: 100%;
    width: 100%;

    &__heading {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      padding: 4px 24px 4px 16px;
      gap: 16px;
      border-radius: 6px;
      background-color: var(--am-c-caeper-bgr);

      &.am-rw-600 {
        display: none;
      }

      &-item {
        display: flex;
        align-items: flex-start;
        font-size: 14px;
        font-style: normal;
        font-weight: 500;
        line-height: 20px;
        color: var(--am-c-caeper-text-op80);

        &.am-w-20 {
          width: calc(20% - 10px);
        }

        &.am-w-40 {
          width: calc(40% - 11px);
        }
      }
    }

    &__content {
      position: relative;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 16px;
      padding: 16px 24px;
      border-radius: 6px;
      background-color: var(--am-c-caeper-bgr);

      &-item {
        display: block;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 20px;
        color: var(--am-c-caeper-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;

        &.am-w-20 {
          width: calc(20% - 11px);

          &.am-rw-600 {
            width: 100%;
          }
        }

        &.am-w-40 {
          width: calc(40% - 11px);
          &.am-rw-600 {
            width: 100%;
            white-space: break-spaces;
            overflow: unset;
            text-overflow: unset;

            &.am-cabinet-empty {
              display: none;
            }
          }
        }
      }

      .am-cc__edit-btn {
        position: absolute;
        top: 50%;
        right: 0;
        transform: translateY(-50%);
        cursor: pointer;
        color: var(--am-c-main-text);
      }
    }

    &__footer {
      display: flex;
      justify-content: flex-end;
      gap: 8px;
      padding: 8px 0 16px;
      border-radius: 6px;

      &.am-rw- {
        &360 {
          flex-direction: column;
        }
      }

      &.am-error {
        align-items: center;
        justify-content: space-between;
      }

      [class^="am-icon-"], [class*=" am-icon-"] {
        font-size: 22px;
        line-height: 20px;
      }

      .am-button__inner {
        line-height: 1;
      }
    }

    &__error {
      font-size: 12px;
      font-style: normal;
      font-weight: 500;
      line-height: 20px;
      color: var(--am-c-error);
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-employee-periods;
}
</style>
