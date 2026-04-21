<template>
  <AmSlidePopup
    :visibility="props.visibility"
    custom-class="am-csd am-csd__reschedule"
    position="center"
    :style="cssDialogVars"
  >
    <div ref="popupInnerRef" class="am-csd__inner">
      <div class="am-csd__header">
        <div class="am-csd__header-text">
          {{ labelsDisplay('no_selected_slot_requirements') }}
        </div>
        <div class="am-csd__header-btn">
          <IconComponent icon="close"></IconComponent>
        </div>
      </div>
      <div ref="rescheduleRef" class="am-csd__content">
        <!-- Packages filters -->
        <el-form
          v-if="
            subStepName === 'bookAppointment' &&
            (amCustomize[pageRenderKey][stepRecognition].options.employee
              .visibility ||
              amCustomize[pageRenderKey][stepRecognition].options.location
                .visibility)
          "
          ref="packageFormRef"
          class="am-csd__filter-wrapper"
          :rules="rules"
          :model="packageFormData"
          label-position="top"
        >
          <div class="am-csd__filter" :class="responsiveClass">
            <el-form-item
              v-if="
                amCustomize[pageRenderKey][stepRecognition].options.employee
                  .visibility
              "
              :class="[
                {
                  'am-csd__filter-item-full':
                    !amCustomize[pageRenderKey][stepRecognition].options
                      .location.visibility,
                },
                responsiveClass,
              ]"
              :label="`${labelsDisplay('package_appointment_employee')}:`"
              :prop="'employee'"
            >
              <AmSelect
                v-model="packageFormData.employee"
                clearable
                :filterable="
                  amCustomize[pageRenderKey][stepRecognition].options.employee
                    .filterable
                "
                :placeholder="`${labelsDisplay('package_select_employee')}...`"
                :fit-input-width="true"
                :popper-class="'am-csd__filter-employees'"
              >
                <AmOption
                  v-for="provider in employees"
                  :key="provider.id"
                  :value="provider.id"
                  :label="`${provider.firstName} ${provider.lastName}`"
                >
                  <AmOptionTemplate2
                    :identifier="provider.id"
                    :label="`${provider.firstName} ${provider.lastName}`"
                    :price="0"
                    :image-thumb="provider.pictureThumbPath"
                    :description="provider.description"
                    :dialog-title="
                      labelsDisplay('employee_information_package')
                    "
                    :dialog-button-text="
                      labelsDisplay('select_this_employee_package')
                    "
                    :badge="provider.badge"
                  ></AmOptionTemplate2>
                </AmOption>
              </AmSelect>
            </el-form-item>

            <el-form-item
              v-if="
                amCustomize[pageRenderKey][stepRecognition].options.location
                  .visibility
              "
              :class="[
                {
                  'am-csd__filter-item-full':
                    !amCustomize[pageRenderKey][stepRecognition].options
                      .employee.visibility,
                },
                responsiveClass,
              ]"
              :label="`${labelsDisplay('package_appointment_location')}:`"
              :prop="'location'"
            >
              <AmSelect
                v-model="packageFormData.location"
                clearable
                :filterable="
                  amCustomize[pageRenderKey][stepRecognition].options.location
                    .filterable
                "
                :placeholder="`${labelsDisplay('package_select_location')}...`"
                :fit-input-width="true"
              >
                <AmOption
                  v-for="location in locations"
                  :key="location.id"
                  :value="location.id"
                  :label="location.name"
                ></AmOption>
              </AmSelect>
            </el-form-item>
          </div>
        </el-form>
        <Calendar
          :label-slots-selected="labelsDisplay('date_time_slots_selected')"
        ></Calendar>
      </div>
    </div>
    <template #footer>
      <div class="am-csd__footer">
        <AmButton
          category="secondary"
          :size="popupInnerWidth <= 360 ? 'small' : 'default'"
          :type="
            amCustomize[pageRenderKey][stepRecognition].options.cancelBtn
              .buttonType
          "
        >
          {{ labelsDisplay('cancel') }}
        </AmButton>
        <AmButton
          :size="popupInnerWidth <= 360 ? 'small' : 'default'"
          :type="
            amCustomize[pageRenderKey][stepRecognition].options.continueBtn
              .buttonType
          "
        >
          {{ labelsDisplay('continue') }}
        </AmButton>
      </div>
    </template>
  </AmSlidePopup>
</template>

<script setup>
// * import from Vue
import { ref, computed, inject, watch, onMounted, nextTick } from 'vue'

// * _components
import AmSlidePopup from '../../../../../../_components/slide-popup/AmSlidePopup.vue'
import IconComponent from '../../../../../../_components/icons/IconComponent.vue'
import AmButton from '../../../../../../_components/button/AmButton.vue'
import AmSelect from '../../../../../../_components/select/AmSelect.vue'
import AmOption from '../../../../../../_components/select/AmOption.vue'
import AmOptionTemplate2 from '../../../../../../_components/select/parts/AmOptionTemplate2.vue'

// * Templates
import Calendar from '../../../parts/Calendar.vue'

// * Composables
import { useResponsiveClass } from '../../../../../../../assets/js/common/responsive'
import { useColorTransparency } from '../../../../../../../assets/js/common/colorManipulation'
import { useReactiveCustomize } from '../../../../../../../assets/js/admin/useReactiveCustomize.js'

// * Components props
let props = defineProps({
  visibility: {
    type: Boolean,
    default: false,
  },
})

// * Popup contnte width
let popupInnerRef = ref(null)
let popupInnerWidth = ref(0)

// * Sidebar collapsed var
let sidebarCollapsed = inject('sidebarCollapsed')

// * window resize listener
window.addEventListener('resize', resize)

// * resize function
function resize() {
  if (popupInnerRef.value) {
    popupInnerWidth.value = popupInnerRef.value.offsetWidth
  }
}

watch(sidebarCollapsed, (current) => {
  if (current) {
    setTimeout(() => {
      collapseTriggered()
    }, 1500)
  } else {
    setTimeout(() => {
      collapseTriggered()
    }, 500)
  }
})

function collapseTriggered() {
  popupInnerWidth.value = popupInnerRef.value.offsetWidth
}

onMounted(() => {
  nextTick(() => {
    popupInnerWidth.value = popupInnerRef.value.offsetWidth
  })
})

let popupReady = computed(() => props.visibility)
watch(popupReady, (newVal) => {
  if (newVal) {
    setTimeout(() => {
      popupInnerWidth.value = popupInnerRef.value.offsetWidth
    }, 300)
  }
})

let responsiveClass = computed(() => {
  return useResponsiveClass(popupInnerWidth.value)
})

/*************
 * Customize *
 *************/
let langKey = inject('langKey')
let amLabels = inject('labels')

let stepName = inject('stepName')
let subStepName = inject('subStepName')
let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

let stepRecognition = computed(() => {
  if (subStepName.value) return subStepName.value
  return stepName.value
})

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value][stepRecognition.value]
      .translations &&
      amCustomize.value[pageRenderKey.value][stepRecognition.value]
        .translations[label] &&
      amCustomize.value[pageRenderKey.value][stepRecognition.value]
        .translations[label][langKey.value]
      ? amCustomize.value[pageRenderKey.value][stepRecognition.value]
          .translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

/**********
 * Filter *
 *********/
let packageFormData = ref({
  employee: null,
  location: null,
})

let packageFormRef = ref(null)

// * Form validation rules
let rules = ref({
  employee: [
    {
      required: computed(
        () =>
          amCustomize.value[pageRenderKey.value][stepRecognition.value].options
            .employee.required
      ),
      message: labelsDisplay('please_select_employee'),
      trigger: 'submit',
    },
  ],
  location: [
    {
      required: computed(
        () =>
          amCustomize.value[pageRenderKey.value][stepRecognition.value].options
            .location.required
      ),
      message: labelsDisplay('please_select_location'),
      trigger: 'submit',
    },
  ],
})

let employees = [
  {
    id: 1,
    firstName: 'John',
    lastName: 'Doe',
    pictureThumbPath: null,
    description: '',
    badge: null,
  },
]

let locations = [
  {
    id: 1,
    name: 'Location 1',
  },
]

// * Fonts
let amFonts = inject('amFonts')

// * Colors block
let amColors = inject('amColors')

let cssDialogVars = computed(() => {
  return {
    '--am-c-csd-text': amColors.value.colorMainText,
    '--am-c-csd-bgr': amColors.value.colorMainBgr,
    '--am-c-csd-text-op10': useColorTransparency(
      amColors.value.colorMainText,
      0.1
    ),
    '--am-c-scroll-op30': useColorTransparency(
      amColors.value.colorPrimary,
      0.3
    ),
    '--am-c-scroll-op10': useColorTransparency(
      amColors.value.colorPrimary,
      0.1
    ),
    '--am-font-family': amFonts.value.fontFamily,
  }
})
</script>

<script>
export default {
  name: 'AppointmentBooking',
}
</script>

<style lang="scss">
@mixin am-cabinet-slide-dialog {
  // csd - cabinet slide dialog
  .am-csd {
    border-radius: 8px;
    padding: 0;

    $mainClass: '.am-csd';

    * {
      font-family: var(--am-font-family);
      box-sizing: border-box;
    }

    &__cancel {
      &.am-slide-popup__block-inner.am-position-center {
        max-width: 480px;
        width: 100%;
        padding: 0;
        border-radius: 8px;
        background-color: var(--am-c-csd-bgr);
      }

      #{$mainClass} {
        &__content {
          p {
            font-size: 15px;
            font-weight: 400;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: var(--am-c-csd-text);
          }
        }
      }
    }

    &__reschedule {
      &.am-slide-popup__block-inner.am-position-center {
        max-width: 520px;
        width: 100%;
        padding: 0;
        border-radius: 8px;
        background-color: var(--am-c-csd-bgr);
      }

      &#{$mainClass} {
        display: flex;
        flex-direction: column;
      }

      #{$mainClass} {
        &__inner {
          display: flex;
          flex-direction: column;
          height: 100%;
          overflow: hidden;
        }

        &__header {
          border-bottom: 1px solid var(--am-c-csd-text-op10);
        }

        &__content {
          padding: 24px;
          overflow-x: hidden;

          table {
            margin: 0;
            border: none;
          }

          // Main Scroll styles
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
        }

        &__footer {
          border-top: 1px solid var(--am-c-csd-text-op10);
        }
      }
    }

    &__inner {
    }

    &__header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 20px 24px 24px;

      &-text {
        font-size: 18px;
        font-weight: 500;
        color: var(--am-c-csd-text);
      }

      &-btn {
        color: var(--am-c-csd-text);
        cursor: pointer;
      }
    }

    &__content {
      padding: 0 24px;
    }

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

    &__filter {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 0 16px;

      &.am-rw-480 {
        flex-wrap: wrap;
      }

      &-wrapper {
        .el-form {
          &-item {
            display: block;
            font-family: var(--am-font-family);
            font-size: var(--am-fs-label);
            margin-bottom: 24px;

            &__label {
              flex: 0 0 auto;
              text-align: left;
              font-size: var(--am-fs-label);
              line-height: 1.3;
              color: var(--am-c-main-text);
              box-sizing: border-box;
              margin: 0;

              &:before {
                color: var(--am-c-error);
              }
            }

            &__content {
              display: flex;
              flex-wrap: wrap;
              align-items: center;
              flex: 1;
              position: relative;
              font-size: var(--am-fs-inp);
              min-width: 0;
            }

            &__error {
              font-size: 12px;
              color: var(--am-c-error);
              padding-top: 4px;
            }
          }
        }
      }

      .el-form {
        &-item {
          width: calc(50% - 12px);

          &.am-csd__filter-item-full {
            width: 100%;
          }

          &.am-rw-480 {
            width: 100%;
          }

          &__error {
            line-height: 1;
          }

          .el-input {
            &__inner {
              height: 40px !important;
            }
          }
        }
      }
    }
  }
}

#amelia-app-backend-new #amelia-container {
  @include am-cabinet-slide-dialog;
}
</style>
