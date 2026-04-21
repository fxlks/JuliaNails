<template>
  <AmSlidePopup
    :visibility="props.visibility"
    :style="cssDialogVars"
    :custom-class="`am-csd am-csd__period${pageWidth <= 400 ? ' am-w-100' : ''}`"
    position="center"
    @update:visibility="cancelEditPeriod"
  >
    <template v-if="props.title" #header>
      <div class="am-csd__period-header">
        {{ props.title }}
      </div>
    </template>
    <div
      v-if="period"
      ref="popupPeriodRef"
      class="am-csd__period-content"
    >
      <div class="am-csd__period-content__inner">
        <AmAlert
          v-if="alertVisibility"
          ref="alertContainer"
          type="error"
          :show-border="true"
          :close-after="10000"
          custom-class="am-csd__alert"
          @close="closeAlert"
          @trigger-close="closeAlert"
        >
          <template #title>
            <span class="am-icon-clearable"/> {{ alertMessage }}
          </template>
        </AmAlert>

        <el-form
          ref="periodFormRef"
          class="am-csd__period-form"
          :rules="rules"
          :model="periodFormData"
          label-position="top"
        >
          <!-- Start Time -->
          <el-form-item
            :label="`${amLabels.time}:`"
            :prop="'startTime'"
            class="am-csd__period-form__item am-w-50"
            :class="responsiveClass"
          >
            <AmTimeSelect
              v-model="period.startTime"
              :placeholder="amLabels.from"
              start="00:00"
              :end="getPreviousSlot('24:00')"
              :min-time="props.periodIndex === 0 ? null : getPreviousSlot(periods[props.periodIndex - 1].endTime)"
              :max-time="props.periodIndex === periods.length - 1 ? null : period.endTime"
              :step="useSecondsInTime(amSettings.general.timeSlotLength)"
            />
          </el-form-item>
          <!-- /Start Time -->

          <!-- End Time -->
          <el-form-item
            :label="`${amLabels.time}:`"
            :prop="'endTime'"
            class="am-csd__period-form__item am-w-50"
            :class="responsiveClass"
          >
            <AmTimeSelect
              v-model="period.endTime"
              :placeholder="amLabels.to_upper"
              :disabled="!period.startTime"
              :start="getNextSlot('00:00')"
              end="24:00"
              :min-time="period.startTime"
              :max-time="props.periodIndex === periods.length - 1 ? null : getNextSlot(periods[props.periodIndex + 1].startTime)"
              :step="useSecondsInTime(amSettings.general.timeSlotLength)"
            />
          </el-form-item>
          <!-- /End Time -->

          <!-- Services -->
          <el-form-item
            :label="`${amLabels.heading_services}:`"
            :prop="'periodServiceList'"
            class="am-csd__period-form__item"
          >
            <AmSelect
              v-model="period.periodServiceList"
              :placeholder="`${amLabels.all_services}`"
              multiple
              clearable
            >
              <AmOption
                v-for="entity in services"
                :key="entity.id"
                :value="entity.id"
                :label="entity.name"
              />
            </AmSelect>
          </el-form-item>
          <!-- /Services -->

          <!-- Location -->
          <el-form-item
            v-if="locations.length > 0"
            :label="`${amLabels.location}:`"
            :prop="'periodLocationList'"
            class="am-csd__period-form__item"
          >
            <AmSelect
              v-model="period.periodLocationList"
              :placeholder="`${amLabels.all_locations}`"
              multiple
              clearable
            >
              <AmOption
                v-for="entity in locations"
                :key="entity.id"
                :value="entity.id"
                :label="entity.name"
              />
            </AmSelect>
          </el-form-item>
          <!-- /Location -->
        </el-form>
      </div>
    </div>
    <template #footer>
      <div
        class="am-csd__period-footer"
        :class="responsiveClass"
      >
        <AmButton
          category="secondary"
          type="plain"
          :size="pageWidth > 430 ? 'default' : 'small'"
          @click="cancelEditPeriod"
        >
          {{ amLabels.cancel }}
        </AmButton>
        <AmButton
          :size="pageWidth > 430 ? 'default' : 'small'"
          @click="savePeriod"
        >
          {{ amLabels.save }}
        </AmButton>
      </div>
    </template>
  </AmSlidePopup>
</template>

<script setup>
// * import from Vue
import {
  ref,
  computed,
  inject,
  watch,
  provide,
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * _components
import AmSlidePopup from "../../../../../_components/slide-popup/AmSlidePopup.vue";
import AmButton from "../../../../../_components/button/AmButton.vue";
import AmOption from "../../../../../_components/select/AmOption.vue";
import AmSelect from "../../../../../_components/select/AmSelect.vue";
import AmAlert from "../../../../../_components/alert/AmAlert.vue";
import AmTimeSelect from "../../../../../_components/time-select/AmTimeSelect.vue";

import { useColorTransparency } from "../../../../../../assets/js/common/colorManipulation.js";
import { useSecondsInTime, useTimeInSeconds } from "../../../../../../assets/js/common/date";
import { useResponsiveClass } from "../../../../../../assets/js/common/responsive";
import { useElementSize } from "@vueuse/core";
import { useEmployeeServices } from "../../../../../../assets/js/common/employee";

// * Vars
let store = useStore()

// * Component emits
const emits = defineEmits(['close'])

// * Settings
const amSettings = inject('settings')

// * Labels
let amLabels = inject('amLabels')

// * Page Width
let pageWidth = inject('pageWidth')

/********
 * Form *
 ********/
let props = defineProps({
  title: {
    type: String,
    default: ''
  },
  visibility: {
    type: Boolean,
    default: false
  },
  origin: {
    type: String,
    default: ''
  },
  periodIndex: {
    type: Number,
    default: 0
  },
  originIndex: {
    type: Number,
    default: 0
  },
  isNew: {
    type: Boolean,
    default: false
  },
})

// * Popup content width
let popupPeriodRef = ref(null)

let popupReady = computed(() => props.visibility)
const { width: popupWidth } = useElementSize(popupPeriodRef)

// * Responsive class
const responsiveClass = computed(() => useResponsiveClass(popupWidth.value))

watch(popupReady, (newVal) => {
  if (newVal) {
    setTimeout(() => {
      editedPeriod.value = JSON.parse(JSON.stringify(periods.value[props.periodIndex]))
    }, 300)
  }
})

// * Alert
let alertVisibility = ref(false)
let alertContainer = ref(null)
let alertMessage = ref('')

function closeAlert () {
  alertVisibility.value = false
  alertMessage.value = ''
}

/********
 * Form *
 ********/
let editedPeriod = ref(null)

// ! periodFormData needs to be refactored
let periodFormData = ref({
  startTime: computed({
    get: () => period.value.startTime,
    set: (val) => {
      period.value.startTime = val
    }
  }),
  endTime: computed({
    get: () => period.value.endTime,
    set: (val) => {
      period.value.endTime = val
    }
  }),
  periodServiceList: computed({
    get: () => period.value.periodServiceList,
    set: (val) => {
      period.value.periodServiceList = val
    }
  }),
  periodLocationList: computed({
    get: () => period.value.periodLocationList,
    set: (val) => {
      period.value.periodLocationList = val
    }
  }),
})

let periodFormRef = ref(null)

// * Form validation rules
let rules = computed(() => {
  return {
    startTime: [
      {
        required: true,
        message: amLabels.select_time_warning,
        trigger: ['submit', 'change'],
      }
    ],
    endTime: [
      {
        required: true,
        message: amLabels.select_time_warning,
        trigger: ['submit', 'change'],
      }
    ]
  }
})

let periodRef = ref()

provide('formWrapper', periodRef)

let locations = ref(store.getters['entities/getLocations'])

let services = computed(() => {
  let employeeServices = useEmployeeServices(store)

  let missingServicesIds = period.value.periodServiceList.filter(id => employeeServices.map(i => i.id).indexOf(id) === -1)

  return employeeServices.concat(store.getters['entities/getServices'].filter(i => missingServicesIds.indexOf(i.id) !== -1))
})

// ! Periods needs to be refactored
let periods = computed(() => {
  return store.getters['employee/getEmployee'][props.origin][props.originIndex].periodList
})

// ! Period needs to be refactored
let period = computed(() => {
  return props.periodIndex !== null ? periods.value[props.periodIndex] : null
})

function getPreviousSlot (time) {
  return useSecondsInTime(useTimeInSeconds(time) - amSettings.general.timeSlotLength)
}

function getNextSlot (time) {
  let nextSlot = useSecondsInTime(useTimeInSeconds(time) + amSettings.general.timeSlotLength)

  return nextSlot === '00:00' ? null : nextSlot
}

function savePeriod () {
  periodFormRef.value.validate((valid) => {
    if (valid) {
      emits('close')
    }
  })
}


// ! cancelEditPeriod needs to be refactored
function cancelEditPeriod () {
  if (props.isNew) {
    periods.value.pop()
  } else {
    periods.value[props.periodIndex] = editedPeriod.value
  }

  emits('close')
}

/*************
 * Customize *
 *************/
// * Fonts
let amFonts = inject('amFonts')

// * Colors block
let amColors = inject('amColors')

let cssDialogVars = computed(() => {
  return {
    '--am-c-csd-text': amColors.value.colorMainText,
    '--am-c-csd-bgr': amColors.value.colorMainBgr,
    '--am-c-csd-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
    '--am-c-scroll-op30': useColorTransparency(amColors.value.colorPrimary, 0.3),
    '--am-c-scroll-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),
    '--am-font-family': amFonts.value.fontFamily,
  }
})
</script>

<script>
export default {
  name: 'WorkPeriod'
}
</script>

<style lang="scss">
@mixin am-cabinet-slide-dialog {
  // csd - cabinet slide dialog
  &.am-csd {
    border-radius: 8px;
    padding: 0;

    $mainClass: '.am-csd__period';

    * {
      font-family: var(--am-font-family), sans-serif;
      box-sizing: border-box;
    }

    &__period {
      width: 80%;
      overflow: hidden;
      display: flex;
      flex-direction: column;

      &.am-w-100 {
        width: 100%;
      }

      // * Header
      .am-slide-popup__block {
        &-header{
          padding: 16px 16px 16px 24px;
          background-color: var(--am-c-csd-bgr);
        }
      }

      #{$mainClass} {
        // * Header inner
        &-header {
          font-size: 15px;
          font-weight: 500;
          color: var(--am-c-csd-text);
        }

        // * Content
        &-content {
          overflow-x: hidden;
          background-color: var(--am-c-csd-bgr);

          &::-webkit-scrollbar {
            width: 6px;
          }

          &::-webkit-scrollbar-thumb {
            border-radius: 6px;
            background: var(--am-c-scroll-op30);
          }

          &::-webkit-scrollbar-track {
            border-radius: 6px;
            background: var(--am-c-scroll-op10);
          }

          &__inner {
            padding: 0 24px;
            margin-top: 10px;
            margin-bottom: 10px;
          }
        }

        // * Form
        &-form {
          display: flex;
          flex-wrap: wrap;
          gap: 24px 16px;

          &__item {
            width: 100%;
            margin: 0;

            &.am-w-50 {
              width: calc(50% - 8px);

              &.am-rw-400 {
                width: 100%;
              }
            }

            &.el-form-item {
              display: flex;
              flex-direction: column;
              gap: 4px;

              .el-form-item {
                &__label {
                  width: 100%;

                  &::before {
                    color: var(--am-c-error);
                  }
                }

                &__content {
                  width: 100%;
                }

                &__error {
                  padding: 0;
                  color: var(--am-c-error);
                }
              }
            }
          }
        }

        // * Footer
        &-footer {
          width: 100%;
          display: flex;
          justify-content: flex-end;
          padding: 16px 24px;
          gap: 16px;
          background-color: var(--am-c-csd-bgr);

          &.am-rw-400 {
            justify-content: space-between;
          }
        }
      }
    }

    // * Alert
    &__alert {
      margin: 0 0 24px;

      .el-alert {
        padding: 4px 12px 4px 0;
        box-sizing: border-box;

        &__content {
          .el-alert__closebtn {
            top: 50%;
            transform: translateY(-50%);
          }
        }

        &__title {
          display: flex;
          align-items: center;
          font-size: 16px;
          line-height: 1.5;

          .am-icon-clearable {
            font-size: 28px;
            line-height: 1;
            color: var(--am-c-alerte-bgr);
          }
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container .am-slide-popup__block-inner.am-position-center {
  @include am-cabinet-slide-dialog;
}
</style>
