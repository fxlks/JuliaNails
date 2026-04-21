<template>
  <div
    class="am-caedo"
    :style="cssVars"
  >
    <span
      v-if="globalDayOffList.length"
      class="am-caedo__title_emp"
    >
      {{ amLabels.employee_days_off }}
    </span>

    <div
      v-if="dayOffList.length || globalDayOffList.length"
      class="am-caedo__heading"
      :class="props.responsiveClass"
    >
      <div class="am-caedo__heading-item am-w-50">
        {{ amLabels.date }}
      </div>
      <div class="am-caedo__heading-item am-w-30">
        {{ amLabels.name }}
      </div>
      <div class="am-caedo__heading-item am-w-20">
        {{ amLabels.days_off_repeat_yearly }}
      </div>
    </div>

    <div
      v-for="(dayOff, dayOffIndex) in dayOffList"
      :key="dayOffIndex"
      class="am-caedo__content"
    >
      <div class="am-caedo__content-item">
        <div
          class="am-caedo__content-item__inner am-w-50"
          :class="props.responsiveClass"
        >
          <div
            class="am-caedo__content-item__label"
            :class="props.responsiveClass"
          >
            {{ amLabels.date }}
          </div>
          {{ datesDisplay(dayOff) }}
        </div>

        <div
          class="am-caedo__content-item__inner am-w-30"
          :class="props.responsiveClass"
        >
          <div
            class="am-caedo__content-item__label"
            :class="props.responsiveClass"
          >
            {{ amLabels.name }}
          </div>
          {{ dayOff.name }}
        </div>

        <div
          class="am-caedo__content-item__inner am-w-20"
          :class="props.responsiveClass"
        >
          <div
            class="am-caedo__content-item__label"
            :class="props.responsiveClass"
          >
            {{ amLabels.days_off_repeat_yearly }}
          </div>
          {{ dayOff.repeat ? amLabels.yes : amLabels.no }}
        </div>

        <DotsPopup
          v-if="editedDayOff.index === null"
          :index="dayOffIndex"
          @remove="removeDayOff(dayOffIndex)"
          @edit="editDayOff(dayOffIndex)"
        />
      </div>

      <div
        v-if="editedDayOff.index === dayOffIndex"
        class="am-caedo__wrapper"
      >
        <el-form
          :ref="el => setFormRef(el, dayOffIndex)"
          class="am-caedo__form"
          :rules="rules"
          :model="dayOffFormData"
          label-position="top"
        >
          <el-form-item
            :label="`${amLabels.date}:`"
            :prop="'range'"
          >
            <AmDatePicker
              v-model="dayOffFormData.range"
              type="daterange"
              :popper-class="'am-cap__datepicker-popper'"
              :format="momentDateFormat()"
              :placeholder="amLabels.select_date"
              :lang="localLanguage"
            />
          </el-form-item>

          <el-form-item
            :label="`${amLabels.name}:`"
            :prop="'name'"
          >
            <AmInput
              v-model="dayOffFormData.name"
            />
          </el-form-item>

          <el-form-item
            :prop="'repeat'"
          >
            <AmCheckbox
              v-model="dayOffFormData.repeat"
              :label="amLabels.days_off_repeat_yearly"
            >
            </AmCheckbox>
          </el-form-item>
        </el-form>

        <div class="am-caedo__footer">
          <AmButton
            category="secondary"
            :size="'small'"
            type="plain"
            @click="cancelEditDayOff(dayOffIndex)"
          >
            {{ amLabels.cancel }}
          </AmButton>

          <AmButton
            :size="'small'"
            @click="saveDayOff(dayOffIndex)"
          >
            {{ amLabels.save }}
          </AmButton>
        </div>
      </div>
    </div>

    <EmptyState
      v-if="dayOffList.length === 0 && globalDayOffList.length === 0"
      :heading="amLabels.no_days_off_yet"
    />

    <div v-if="editedDayOff.index === null" :class="dayOffList.length === 0 && globalDayOffList.length === 0 ? 'add-btn' : ''">
      <AmButton
        :size="'small'"
        @click="addDayOff()"
      >
        {{ amLabels.add_day_off }}
      </AmButton>
    </div>

    <span
      v-if="globalDayOffList.length"
      class="am-caedo__title_global"
    >
      {{ amLabels.company_days_off }}
    </span>

    <div
      v-if="globalDayOffList.length"
      class="am-caedo__heading"
      :class="props.responsiveClass"
    >
      <div class="am-caedo__heading-item am-w-50">
        {{ amLabels.date }}
      </div>
      <div class="am-caedo__heading-item am-w-30">
        {{ amLabels.name }}
      </div>
      <div class="am-caedo__heading-item am-w-20">
        {{ amLabels.days_off_repeat_yearly }}
      </div>
    </div>

    <div
      v-for="(dayOff, dayOffIndex) in globalDayOffList"
      :key="dayOffIndex"
      class="am-caedo__content"
    >
      <div class="am-caedo__content-item">
        <div
          class="am-caedo__content-item__inner am-w-50"
          :class="props.responsiveClass"
        >
          <div
            class="am-caedo__content-item__label"
            :class="props.responsiveClass"
          >
            {{ amLabels.date }}
          </div>
          {{ datesDisplay(dayOff) }}
        </div>

        <div
          class="am-caedo__content-item__inner am-w-30"
          :class="props.responsiveClass"
        >
          <div
            class="am-caedo__content-item__label"
            :class="props.responsiveClass"
          >
            {{ amLabels.name }}
          </div>
          {{ dayOff.name }}
        </div>

        <div
          class="am-caedo__content-item__inner am-w-20"
          :class="props.responsiveClass"
        >
          <div
            class="am-caedo__content-item__label"
            :class="props.responsiveClass"
          >
            {{ amLabels.days_off_repeat_yearly }}
          </div>
          {{ dayOff.repeat ? amLabels.yes : amLabels.no }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  reactive,
  ref,
  inject,
  computed,
  onMounted
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
import AmDatePicker from "../../../../../_components/datePicker/AmDatePicker.vue";
import DotsPopup from "../../../../Parts/DotsPopup.vue";
import AmButton from "../../../../../_components/button/AmButton.vue";
import AmInput from "../../../../../_components/input/AmInput.vue";
import AmCheckbox from "../../../../../_components/checkbox/AmCheckbox.vue";
import EmptyState from '../../parts/EmptyState.vue'

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

// * Settings
const amSettings = inject('settings')

let dayOffList = computed(() => {
  return store.getters['employee/getEmployee'].dayOffList
})

let globalDayOffList = ref([])

let editedDayOff = reactive({
  data: null,
  index: null,
})

let dayOffFormData = ref({
  range: computed({
    get: () => store.getters['employee/getEmployee'].dayOffList[editedDayOff.index].range,
    set: (val) => {
      store.getters['employee/getEmployee'].dayOffList[editedDayOff.index].range = val
    }
  }),
  name: computed({
    get: () => store.getters['employee/getEmployee'].dayOffList[editedDayOff.index].name,
    set: (val) => {
      store.getters['employee/getEmployee'].dayOffList[editedDayOff.index].name = val
    }
  }),
  repeat: computed({
    get: () => store.getters['employee/getEmployee'].dayOffList[editedDayOff.index].repeat,
    set: (val) => {
      store.getters['employee/getEmployee'].dayOffList[editedDayOff.index].repeat = val
    }
  }),
})

let dayOffFormRef = ref([])

let rules = computed(() => {
  return {
    range: [
      {
        required: true,
        message: amLabels.select_date_warning,
        trigger: ['submit', 'change'],
      }
    ],
    name: [
      {
        required: true,
        message: amLabels.days_off_name_warning,
        trigger: ['submit', 'change'],
      }
    ],
  }
})

function removeDayOff (dayOffIndex) {
  dayOffList.value.splice(dayOffIndex, 1)
}

function editDayOff (dayOffIndex) {
  editedDayOff.index = dayOffIndex

  editedDayOff.data = JSON.parse(JSON.stringify(dayOffList.value[dayOffIndex]))
}

function saveDayOff (index) {
  dayOffFormRef.value[index].validate((valid) => {
    if (valid) {
      editedDayOff.index = null

      editedDayOff.data = null
    }
  })
}

function cancelEditDayOff (dayOffIndex) {
  if (Object.keys(editedDayOff.data).length === 0) {
    dayOffList.value.pop()
  } else {
    dayOffList.value[dayOffIndex] = editedDayOff.data
  }

  editedDayOff.index = null

  editedDayOff.data = null
}

function addDayOff () {
  dayOffList.value.push({
    id: null,
    name: '',
    repeat: false,
    range: [],
  })

  editedDayOff.index = dayOffList.value.length - 1

  editedDayOff.data = {}
}

function datesDisplay (dayOff) {
  let dayFirst = dayOff.range[0] ? (getFrontedFormattedDate(moment(dayOff.range[0]).format('YYYY-MM-DD'))) : ''
  let dayLast = dayOff.range[1] ? (getFrontedFormattedDate(moment(dayOff.range[1]).format('YYYY-MM-DD'))) : ''
  return  `${dayFirst} - ${dayLast}`
}

function setFormRef (el, index) {
  if (el) {
    dayOffFormRef.value[index] = el;
  } else {
    dayOffFormRef.value.splice(index, 1);
  }
}

function validate () {
  return new Promise((resolve) => {
    if (editedDayOff.index !== null && dayOffFormRef.value[editedDayOff.index]) {
      dayOffFormRef.value[editedDayOff.index].validate((valid) => {
        resolve(valid)
      })
    } else {
      resolve(true)
    }
  })
}

function commitEditedState () {
  editedDayOff.index = null
  editedDayOff.data = null
}

defineExpose({
  validate,
  commitEditedState,
})

onMounted(() => {
  globalDayOffList.value = amSettings.daysOff.map(
    i => new Object({
      range: [i.startDate, i.endDate],
      name: i.name,
      repeat: i.repeat,
    })
  )
})

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-caedo-bgr': amColors.value.colorMainBgr,
    '--am-c-caedo-text': amColors.value.colorMainText,
    '--am-c-caedo-text-op80': useColorTransparency(amColors.value.colorMainText, 0.8),
    '--am-c-caedo-text-op03': useColorTransparency(amColors.value.colorMainText, 0.03),
  }
})
</script>

<script>
export default {
  name: 'DaysOff'
}
</script>

<style lang="scss">
@mixin am-cabinet-profile {
  .am-caedo {
    display: flex;
    flex-wrap: wrap;
    flex-direction: column;
    gap: 8px;

    .add-btn {
      display: flex;
      align-self: center;
    }

    &__title_emp {
      margin: 10px;
      padding: 10px;
    }

    &__title_global {
      margin: 10px;
      padding: 10px;
      border-top: 1px solid #747775 !important;
    }

    // * Heading
    &__heading {
      width: 100%;
      display: flex;
      flex-wrap: wrap;
      padding: 4px 24px 4px 16px;
      gap: 8px;
      border-radius: 6px;
      background-color: var(--am-c-caedo-text-op03);

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
        color: var(--am-c-caedo-text);

        &.am-w-50 {
          width: calc(50% - 10px);
        }

        &.am-w-30 {
          width: calc(30% - 11px);
        }

        &.am-w-20 {
          width: calc(20% - 11px);
        }
      }
    }

    // * Content
    &__content {
      position: relative;
      display: flex;
      flex-direction: column;
      gap: 8px;
      max-width: 100%;
      width: 100%;

      &-item {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        padding: 16px 24px 16px 16px;
        border-radius: 6px;
        background-color: var(--am-c-caedo-text-op03);

        &__inner {
          display: block;
          font-size: 14px;
          font-style: normal;
          font-weight: 400;
          line-height: 20px;
          color: var(--am-c-caedo-text);
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;

          &.am-w-50 {
            width: calc(50% - 10px);

            &.am-rw-600 {
              width: 100%;
              white-space: break-spaces;
              overflow: unset;
              text-overflow: unset;
            }
          }

          &.am-w-30 {
            width: calc(30% - 11px);
            &.am-rw-600 {
              width: 100%;
              white-space: break-spaces;
              overflow: unset;
              text-overflow: unset;
            }
          }

          &.am-w-20 {
            width: calc(20% - 11px);
            &.am-rw-600 {
              width: 100%;
              white-space: break-spaces;
              overflow: unset;
              text-overflow: unset;
            }
          }
        }

        &__label {
          display: none;
          align-items: center;
          font-size: 14px;
          font-style: normal;
          font-weight: 500;
          line-height: 20px;
          color: var(--am-c-caedo-text);

          &.am-rw-600 {
            display: block;
          }
        }

        .am-cc__edit-btn {
          position: absolute;
          top: 50%;
          right: 0;
          transform: translateY(-50%);
          color: var(--am-c-main-text);
        }
      }
    }

    // * Wrapper
    &__wrapper {
      display: flex;
      flex-direction: column;
      padding: 16px 24px;
      border-radius: 6px;
      background-color: var(--am-c-caedo-text-op03);
    }

    // * Form
    &__form {
      display: flex;
      flex-direction: column;
      gap: 8px;

      .el-form-item {
        width: 100%;

        &__error {
          color: var(--am-c-error);
          padding: 0;
        }
      }

      .am-date-picker {
        width: 100%;
      }
    }

    // * Footer
    &__footer {
      display: flex;
      flex-direction: row;
      justify-content: flex-start;
      gap: 16px;
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-profile;
}
</style>
