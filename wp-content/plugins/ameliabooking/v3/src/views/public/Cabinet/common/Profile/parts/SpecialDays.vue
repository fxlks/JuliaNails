<template>
  <div
    class="am-caesd"
    :style="cssVars"
  >
    <div
      v-for="(specialDay, specialDayIndex) in specialDayList"
      :key="specialDayIndex"
      class="am-caesd__content"
    >
      <div class="am-caesd__item">
        <div class="am-caesd__item-header">
          <template v-if="editedSpecialDay.index !== specialDayIndex">
            <div>
              {{ specialDay.range[0] ? (getFrontedFormattedDate(moment(specialDay.range[0]).format('YYYY-MM-DD'))) : '' }} - {{ specialDay.range[1] ? (getFrontedFormattedDate(moment(specialDay.range[1]).format('YYYY-MM-DD'))) : '' }}
            </div>

            <DotsPopup
              v-if="editedSpecialDay.index === null"
              :index="specialDayIndex"
              :have-duplicate="true"
              @remove="removeSpecialDay(specialDayIndex)"
              @edit="editSpecialDay(specialDayIndex)"
              @duplicate="duplicateSpecialDay(specialDayIndex)"
            />
          </template>

          <el-form
            v-if="editedSpecialDay.index === specialDayIndex"
            :ref="el => setFormRef(el, specialDayIndex)"
            class="am-caesd__form"
            :rules="rules"
            :model="specialDayFormData"
            label-position="top"
          >
            <el-form-item
              :prop="'range'"
            >
              <AmDatePicker
                v-model="specialDayFormData.range"
                type="daterange"
                size="small"
                :popper-class="'am-cap__datepicker-popper'"
                :prefix-icon="markRaw({
                  components: { IconComponent },
                  template: `<IconComponent icon='calendar'/>`
                })"
                :format="momentDateFormat()"
                :placeholder="amLabels.select_date"
                :lang="localLanguage"
              />
            </el-form-item>
          </el-form>
          <div v-if="periodsErrorDisplay">

          </div>
        </div>

        <div
          class="am-caesd__item-inner"
          :class="{'am-editable': editedSpecialDay.index === specialDayIndex}"
        >
          <Periods
            :title="amLabels.special_days"
            :enabled="true"
            :index="specialDayIndex"
            :type="'specialDayList'"
            :apply-to-all-days-visible="false"
            :header-visible="editedSpecialDay.index === specialDayIndex"
            :edit-visible="editedSpecialDay.index === specialDayIndex"
            :footer-visible="editedSpecialDay.index === specialDayIndex"
            :error="editedSpecialDay.index === specialDayIndex ? periodsErrorDisplay : ''"
            :responsive-class="props.responsiveClass"
            @add-period="() => {periodsErrorDisplay = ''}"
          />
        </div>
      </div>

      <template v-if="editedSpecialDay.index === specialDayIndex">
        <div class="am-caesd__footer">
          <AmButton
            category="secondary"
            type="plain"
            size="small"
            @click="cancelEditSpecialDay(specialDayIndex)"
          >
            {{ amLabels.cancel }}
          </AmButton>

          <AmButton
            :size="'small'"
            @click="saveSpecialDay(specialDayIndex)"
          >
            {{ amLabels.save }}
          </AmButton>
        </div>
      </template>
    </div>

    <EmptyState
      v-if="specialDayList.length === 0"
      :heading="amLabels.no_special_days_yet"
    />

    <div v-if="editedSpecialDay.index === null" :class="specialDayList.length === 0 ? 'add-btn' : ''">
      <AmButton
        :size="'small'"
        @click="addSpecialDay()"
      >
        {{ amLabels.add_special_day }}
      </AmButton>
    </div>

  </div>
</template>

<script setup>
// * Import from Vue
import {
  reactive,
  ref,
  inject,
  computed, markRaw,
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

import moment from "moment";

// * Composables
import {
  getFrontedFormattedDate,
  momentDateFormat,
} from "../../../../../../assets/js/common/date";
import { useColorTransparency } from "../../../../../../assets/js/common/colorManipulation";

// * Dedicated components
import Periods from "./Periods.vue";
import AmDatePicker from "../../../../../_components/datePicker/AmDatePicker.vue";
import DotsPopup from "../../../../Parts/DotsPopup.vue";
import AmButton from "../../../../../_components/button/AmButton.vue";
import IconComponent from "../../../../../_components/icons/IconComponent.vue";
import EmptyState from "../../parts/EmptyState.vue";

let props = defineProps({
  responsiveClass: {
    type: String,
    default: '',
  }
})

let store = useStore()

// * Labels
let amLabels = inject('amLabels')

// * Language
let localLanguage = inject('localLanguage')

let specialDayList = computed(() => {
  return store.getters['employee/getEmployee'].specialDayList
})

let editedSpecialDay = reactive({
  data: null,
  index: null,
})

let specialDayFormData = ref({
  range: computed({
    get: () => store.getters['employee/getEmployee'].specialDayList[editedSpecialDay.index].range,
    set: (val) => {
      store.getters['employee/getEmployee'].specialDayList[editedSpecialDay.index].range = val
    }
  }),
})

let specialDayFormRef = ref([])

let rules = computed(() => {
  return {
    range: [
      {
        required: true,
        message: amLabels.select_date_warning,
        trigger: ['submit', 'change'],
      }
    ],
  }
})

function removeSpecialDay (specialDayIndex) {
  specialDayList.value.splice(specialDayIndex, 1)
}

function editSpecialDay (specialDayIndex) {
  editedSpecialDay.index = specialDayIndex

  editedSpecialDay.data = JSON.parse(JSON.stringify(specialDayList.value[specialDayIndex]))
}

function duplicateSpecialDay (specialDayIndex) {
  let clonedPeriods = []

  specialDayList.value[specialDayIndex].periodList.forEach((period) => {
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

  store.getters['employee/getEmployee'].specialDayList.push({
    id: null,
    range: [],
    periodList: clonedPeriods,
  })

  editedSpecialDay.index = specialDayList.value.length - 1

  editedSpecialDay.data = {}
}

let periodsErrorDisplay = ref('')

function saveSpecialDay (index) {
  let periodList = store.getters['employee/getEmployee'].specialDayList[index].periodList
  specialDayFormRef.value[index].validate((valid) => {
    if (valid && periodList.length > 0) {
      periodsErrorDisplay.value = ''

      editedSpecialDay.index = null

      editedSpecialDay.data = null
    } else {
      periodsErrorDisplay.value = amLabels.select_day_period_warning
    }
  })
}

function cancelEditSpecialDay (specialDayIndex) {
  if (Object.keys(editedSpecialDay.data).length === 0) {
    specialDayList.value.pop()
  } else {
    specialDayList.value[specialDayIndex] = editedSpecialDay.data
  }

  editedSpecialDay.index = null

  editedSpecialDay.data = null

  periodsErrorDisplay.value = ''
}

function addSpecialDay () {
  specialDayList.value.push({
    id: null,
    range: [],
    periodList: [],
  })

  editedSpecialDay.index = specialDayList.value.length - 1

  editedSpecialDay.data = {}
}

function setFormRef (el, index) {
  if (el) {
    specialDayFormRef.value[index] = el;
  } else {
    specialDayFormRef.value.splice(index, 1);
  }
}

function validate () {
  return new Promise((resolve) => {
    if (editedSpecialDay.index !== null && specialDayFormRef.value[editedSpecialDay.index]) {
      let periodList = store.getters['employee/getEmployee'].specialDayList[editedSpecialDay.index].periodList
      specialDayFormRef.value[editedSpecialDay.index].validate((valid) => {
        if (valid && periodList.length > 0) {
          resolve(true)
        } else {
          periodsErrorDisplay.value = periodList.length === 0 ? amLabels.select_day_period_warning : ''
          resolve(false)
        }
      })
    } else {
      resolve(true)
    }
  })
}

function commitEditedState () {
  editedSpecialDay.index = null
  editedSpecialDay.data = null
  periodsErrorDisplay.value = ''
}

defineExpose({
  validate,
  commitEditedState,
})

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-caesd-bgr': amColors.value.colorMainBgr,
    '--am-c-caesd-text': amColors.value.colorMainText,
    '--am-c-caesd-text-op03': useColorTransparency(amColors.value.colorMainText, 0.03),
    '--am-c-caesd-text-op80': useColorTransparency(amColors.value.colorMainText, 0.8),
  }
})
</script>

<script>
export default {
  name: 'SpecialDays'
}
</script>

<style lang="scss">
@mixin am-cabinet-employee-special-days {
  // am - amelia
  // caesd - cabinet employee special days
  .am-caesd {
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-width: 100%;

    .add-btn {
      display: flex;
      align-self: center;
    }

    &__content {
      display: flex;
      flex-direction: column;
      gap: 8px;
      background-color: var(--am-c-caesd-text-op03);
      border-radius: 8px;
      padding: 16px 24px;
    }

    &__item {
      display: flex;
      flex-direction: column;
      gap: 16px;

      &-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        color: var(--am-c-main-text);

        .am-date-picker-wrapper .el-input__inner{
          width: 100%;
        }
      }

      &-inner {
        .am-caeper__content {
          padding: 0;
          background-color: transparent;
        }

        &.am-editable {
          .am-caeper__content {
            padding: 16px 24px;
            background-color: var(--am-c-caeper-bgr);
          }
        }
      }
    }

    &__footer {
      display: flex;
      flex-direction: row;
      justify-content: flex-start;
      gap: 16px;
    }

    .am-caeper {
      &__footer {
        padding: 8px 0 0;
      }
    }

    .el-form-item__error {
      color: var(--am-c-error);
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-employee-special-days;
}
</style>
