<template>
  <AmSlidePopup
    :visibility="props.visibility"
    :style="cssDialogVars"
    :custom-class="`am-csd am-csd__timeOut${pageWidth <= 400 ? ' am-w-100' : ''}`"
    position="center"
    @update:visibility="cancelEditTimeOut"
  >
    <template v-if="props.title" #header>
      <div class="am-csd__timeOut-header">
        {{ props.title }}
      </div>
    </template>
    <div
      v-if="timeOut"
      ref="popupTimeOutRef"
      class="am-csd__timeOut-content"
    >
      <div class="am-csd__timeOut-content__inner">
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
          ref="timeOutFormRef"
          class="am-csd__timeOut-form"
          :rules="rules"
          :model="breakFormData"
          label-position="top"
        >
          <!-- Start Time -->
          <el-form-item
            :label="`${amLabels.time}:`"
            :prop="'startTime'"
            class="am-csd__timeOut-form__item am-w-50"
            :class="responsiveClass"
          >
            <AmTimeSelect
              v-model="timeOut.startTime"
              :placeholder="amLabels.from"
              :start="getNextSlot(periods[0].startTime)"
              :end="getPreviousSlot(periods[periods.length - 1].endTime)"
              :step="useSecondsInTime(amSettings.general.timeSlotLength)"
              :max-time="timeOuts[props.timeOutIndex].endTime"
            />
          </el-form-item>
          <!-- /Start Time -->

          <!-- End Time -->
          <el-form-item
            :label="`${amLabels.time}:`"
            :prop="'endTime'"
            class="am-csd__timeOut-form__item am-w-50"
            :class="responsiveClass"
          >
            <AmTimeSelect
              v-model="timeOut.endTime"
              :placeholder="amLabels.to_upper"
              :disabled="!timeOut.startTime"
              :start="getNextSlot(periods[0].startTime)"
              :end="getPreviousSlot(periods[periods.length - 1].endTime)"
              :min-time="timeOuts[props.timeOutIndex].startTime"
              :step="useSecondsInTime(amSettings.general.timeSlotLength)"
            />
          </el-form-item>
          <!-- /End Time -->
        </el-form>
      </div>
    </div>
    <template #footer>
      <div
        class="am-csd__timeOut-footer"
        :class="responsiveClass"
      >
        <AmButton
          category="secondary"
          type="plain"
          :size="pageWidth > 430 ? 'default' : 'small'"
          @click="cancelEditTimeOut"
        >
          {{ amLabels.cancel }}
        </AmButton>
        <AmButton
          :size="pageWidth > 430 ? 'default' : 'small'"
          @click="saveTimeOut"
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
import AmAlert from "../../../../../_components/alert/AmAlert.vue";
import AmTimeSelect from "../../../../../_components/time-select/AmTimeSelect.vue";

import { useColorTransparency } from "../../../../../../assets/js/common/colorManipulation.js";
import { useSecondsInTime, useTimeInSeconds } from "../../../../../../assets/js/common/date";
import { useResponsiveClass } from "../../../../../../assets/js/common/responsive";
import { useElementSize } from "@vueuse/core";

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
  timeOutIndex: {
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
let popupTimeOutRef = ref(null)

let popupReady = computed(() => props.visibility)

const { width: popupWidth } = useElementSize(popupTimeOutRef)

// * Responsive class
const responsiveClass = computed(() => useResponsiveClass(popupWidth.value))

watch(popupReady, (newVal) => {
  if (newVal) {
    setTimeout(() => {
      editedTimeOut.value = JSON.parse(JSON.stringify(timeOuts.value[props.timeOutIndex]))
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
let editedTimeOut = ref(null)

// ! breakFormData needs to be refactored
let breakFormData = ref({
  startTime: computed({
    get: () => timeOut.value.startTime,
    set: (val) => {
      timeOut.value.startTime = val
    }
  }),
  endTime: computed({
    get: () => timeOut.value.endTime,
    set: (val) => {
      timeOut.value.endTime = val
    }
  }),
})

let timeOutFormRef = ref(null)

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

let timeOutRef = ref()

provide('formWrapper', timeOutRef)

// ! TimeOuts needs to be refactored
let timeOuts = computed(() => {
  return store.getters['employee/getEmployee'][props.origin][props.originIndex].timeOutList
})

let periods = computed(() => {
  return store.getters['employee/getEmployee'][props.origin][props.originIndex].periodList
})

// ! TimeOut needs to be refactored
let timeOut = computed(() => {
  return props.timeOutIndex !== null ? timeOuts.value[props.timeOutIndex] : null
})

// ! minTime was not used enywhere in the code
let minTime = computed(() => periods.value.length ? getNextSlot(periods.value[0].startTime) : '00:00')

// ! maxTime was not used enywhere in the code
let maxTime = computed(() => periods.value.length ? getPreviousSlot(periods.value[periods.value.length - 1].endTime) : '00:00')

function getPreviousSlot (time) {
  return useSecondsInTime(useTimeInSeconds(time) - amSettings.general.timeSlotLength)
}

function getNextSlot (time) {
  let nextSlot = useSecondsInTime(useTimeInSeconds(time) + amSettings.general.timeSlotLength)

  return nextSlot === '00:00' ? null : nextSlot
}

function saveTimeOut () {
  timeOutFormRef.value.validate((valid) => {
    if (valid) {
      emits('close')
    }
  })
}


// ! cancelEditTimeOut needs to be refactored
function cancelEditTimeOut () {
  if (props.isNew) {
    timeOuts.value.pop()
  } else {
    timeOuts.value[props.timeOutIndex] = editedTimeOut.value
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
  name: 'WorkTimeOut'
}
</script>

<style lang="scss">
@mixin am-cabinet-slide-dialog {
  // csd - cabinet slide dialog
  &.am-csd {
    border-radius: 8px;
    padding: 0;

    $mainClass: '.am-csd__timeOut';

    * {
      font-family: var(--am-font-family), sans-serif;
      box-sizing: border-box;
    }

    &__timeOut {
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
            margin-bottom: 20px;
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
