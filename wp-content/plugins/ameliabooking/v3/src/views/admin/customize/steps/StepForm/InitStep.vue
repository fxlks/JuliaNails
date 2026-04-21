<template>
  <div v-if="!loading" class="am-fs__init">
    <el-form
      ref="initFormRef"
      :model="initFormData"
      :rules="rules"
      label-position="top"
      class="am-fs__init-form"
    >
      <template
        v-for="item in amCustomize[pageRenderKey][stepName].order"
        :key="item.id"
      >
        <component :is="formFields[item.id]"></component>
      </template>
    </el-form>

    <!-- Bringing Anyone with you -->
    <AmSlidePopup :visibility="bringingAnyoneVisibility">
      <p class="am-fs__popup-x">
        <span class="am-icon-close"></span>
      </p>
      <BringingAnyone></BringingAnyone>
      <template #footer>
        <AmButton
          category="secondary"
          :type="
            amCustomize[pageRenderKey].bringingAnyone.options.secondaryButton
              .buttonType
          "
        >
          {{ labelsDisplay('bringing_no', 'bringingAnyone') }}
        </AmButton>
        <AmButton
          :type="
            amCustomize[pageRenderKey].bringingAnyone.options.primaryButton
              .buttonType
          "
        >
          {{ labelsDisplay('bringing_yes', 'bringingAnyone') }}
        </AmButton>
      </template>
    </AmSlidePopup>
    <!--/ Bringing Anyone with you -->

    <!-- Packages Popup -->
    <AmSlidePopup :visibility="packagesVisibility" :style="cssPackage">
      <p class="am-fs__popup-x">
        <span class="am-icon-close"></span>
      </p>
      <div class="am-fs__ps-popup">
        <div
          v-if="
            amCustomize[pageRenderKey].packageStep.options.heading.visibility
          "
          class="am-fs__ps-popup__heading"
        >
          {{ labelsDisplay('package_heading', 'packageStep') }}
        </div>
        <PackageStep></PackageStep>
        <div class="am-fs__ps-popup__or">
          {{ labelsDisplay('separator_or', 'packageStep') }}
        </div>
      </div>

      <template #footer>
        <AmButton
          class="am-fs__ps-popup__btn"
          :class="`am-fs__ps-popup__btn${checkScreen ? '-mobile' : ''}`"
          category="primary"
          size="medium"
          :type="
            amCustomize[pageRenderKey].packageStep.options.primaryButton
              .buttonType
          "
          :suffix="pill"
        >
          {{ labelsDisplay('continue_without_package', 'packageStep') }}
        </AmButton>
      </template>
    </AmSlidePopup>
    <!--/ Packages Popup -->
  </div>
</template>

<script setup>
import AmSlidePopup from '../../../../_components/slide-popup/AmSlidePopup.vue'
import AmButton from '../../../../_components/button/AmButton.vue'

// * Import SubSteps
import BringingAnyone from './BringingAnyone.vue'
import PackageStep from './PackageStep.vue'

// * Import Fields
import ServiceFormField from '../../fields/ServiceFormField.vue'
import LocationFormField from '../../fields/LocationFormField.vue'
import EmployeeFormField from '../../fields/EmployeeFormField.vue'
import { ref, provide, markRaw, inject, defineComponent, computed } from 'vue'

// * Import composables
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation'

const amSettings = inject('settings')

// * Plugin Licence
let licence = inject('licence')

let loading = inject('loading')

let langKey = inject('langKey')
let amLabels = inject('labels')

let stepName = inject('stepName')
let subStepName = inject('subStepName')
let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

// * Label computed function
function labelsDisplay(label, stepKey) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value][stepKey].translations &&
      amCustomize.value[pageRenderKey.value][stepKey].translations[label] &&
      amCustomize.value[pageRenderKey.value][stepKey].translations[label][
        langKey.value
      ]
      ? amCustomize.value[pageRenderKey.value][stepKey].translations[label][
          langKey.value
        ]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Form data
let initFormData = ref({
  service: null,
  location: null,
  employee: null,
})
provide('formData', initFormData)

// * Form validation rules
let rules = computed(() => {
  return {
    service: [
      {
        required: true,
        message: labelsDisplay('please_select_service', 'initStep'),
        trigger: ['blur', 'change'],
      },
    ],
    location: [
      {
        required:
          amCustomize.value[pageRenderKey.value][stepName.value].options
            .location.required,
        message: labelsDisplay('please_select_location', 'initStep'),
        trigger: ['blur', 'change'],
      },
    ],
    employee: [
      {
        required:
          amCustomize.value[pageRenderKey.value][stepName.value].options
            .employee.required,
        message: labelsDisplay('please_select_employee', 'initStep'),
        trigger: ['blur', 'change'],
      },
    ],
  }
})

// * Form Fields Array
let formFields = ref({
  service: markRaw(ServiceFormField),
  location:
    !licence.isLite && !licence.isStarter ? markRaw(LocationFormField) : null,
  employee: !licence.isLite ? markRaw(EmployeeFormField) : null,
})

// * Step contains slide popups
let inPopup = ref(true)
provide('inPopup', { inPopup })

// * Bringing Anyone With You
let bringingAnyoneVisibility = computed(() => {
  return subStepName.value === 'bringingAnyone'
})

// * Packages sugestion
let packagesVisibility = computed(() => {
  return subStepName.value === 'packageStep'
})

let pill = defineComponent({
  template: `<div class="am-fs__ps-pill">$60 USD</div>`,
})

// * Global colors
let amColors = inject('amColors')
let cssPackage = computed(() => {
  return {
    '--am-c-ps-text-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
    '--am-c-ps-text-op20': useColorTransparency(
      amColors.value.colorMainText,
      0.2
    ),
  }
})

// Container Width
let cWidth = inject('containerWidth', 0)
let checkScreen = computed(() => cWidth.value < 560 || cWidth.value - 240 < 520)
</script>

<script>
export default {
  name: 'InitStep',
  key: 'initStep',
  sidebarData: {
    label: 'service_selection',
    icon: 'service',
    stepSelectedData: [],
    finished: false,
    selected: false,
  },
}
</script>

<style lang="scss">
#amelia-app-backend-new {
  #amelia-container {
    .am-fs {
      &__main {
        &-inner {
          overflow: hidden;
        }
      }

      &__init {
        &.am-fs__main-content {
          padding-top: 48px;
        }

        &-form {
          &__item {
            $count: 3;
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

          &__label {
            display: inline-block;
            font-family: var(--am-font-family);
            font-weight: 500;
            margin-bottom: 4px;
          }

          .el-form-item {
            &__label {
              color: var(--am-c-main-text);
              line-height: unset;
              padding: 0;

              &:before {
                color: var(--am-c-error);
              }
            }

            &__error {
              color: var(--am-c-error);
            }
          }
        }
      }

      &__popup-x {
        position: absolute;
        top: 16px;
        right: 16px;
        cursor: pointer;
        color: var(--am-c-main-text);
        margin: 0;
      }

      &__ps {
        &-popup {
          position: relative;

          &__heading {
            font-size: 14px;
            font-weight: 400;
            line-height: 1.42857;
            text-align: center;
            color: var(--am-c-main-text);
            margin: 0 0 16px;
            padding: 20px 6px 0 0;
            max-height: 40px;
            overflow-x: hidden;

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

          &__or {
            display: flex;
            flex-direction: row;
            font-weight: 400;
            font-size: 14px;
            line-height: 20px;
            margin: 20px 0;
            color: var(--am-c-ps-text-op60);

            &:before,
            &:after {
              content: '';
              flex: 1 1;
              border-bottom: 1px solid var(--am-c-ps-text-op20);
              margin: auto;
            }

            &:before {
              margin-right: 10px;
            }

            &:after {
              margin-left: 10px;
            }
          }

          &__btn {
            --am-h-btn: 56px;
            --am-fs-btn: 14px;
            width: 100%;
            justify-content: space-between;

            &-mobile {
              --am-fs-btn: 12px;
              --am-h-btn: 56px;
            }
          }
        }

        &-pill {
          display: inline-block;
          font-size: 14px;
          font-weight: 500;
          line-height: 1;
          color: var(--am-c-btn-prim);
          background-color: var(--am-c-btn-prim-text);
          border-radius: 12px;
          padding: 5px 8px;
        }
      }
    }
  }
}
</style>
