<template>
  <div
    v-if="loaded"
    class="am-fs__init"
    :class="[props.globalClass, {'am-oxvisible': (bringingAnyoneVisibility || packagesVisibility)}]"
    tabindex="0"
  >
    <el-form
      ref="initFormRef"
      :model="initFormData"
      :rules="rules"
      label-position="top"
      class="am-fs__init-form"
    >
      <template v-for="field in amCustomize.initStep.order" :key="field.id">
        <component
          :is="amFields[field.id].template"
          v-bind="amFields[field.id].props"
        ></component>
      </template>
    </el-form>

    <!-- Bringing Anyone with you -->
    <AmSlidePopup
      v-if="bringingAnyoneOptions.availability"
      :visibility="bringingAnyoneVisibility"
      class="am-fs__init__bringing"
    >
      <p
        class="am-fs__popup-x"
        :class="{'am-rtl': isRtl}"
        @click="closeBringingPopup"
      >
        <AmeliaIconClose></AmeliaIconClose>
      </p>
      <BringingAnyone :in-popup="true" />
      <template #footer>
        <AmButton
          v-if="(bringingAnyoneOptions.min !== bringingAnyoneOptions.max) &&  bringingAnyoneOptions.min <= 0"
          category="secondary"
          :type="amCustomize.bringingAnyone.options.secondaryButton.buttonType"
          :disabled="(bringingAnyoneOptions.min === bringingAnyoneOptions.max) || (!amSettings.appointments.allowBookingIfNotMin && bringingAnyoneOptions.min > 0)"
          @click="noOneBringWith"
        >
          {{bringingLabels.bringing_no}}
        </AmButton>
        <AmButton
          :type="(bringingAnyoneOptions.min !== bringingAnyoneOptions.max) &&  bringingAnyoneOptions.min <= 0 ?
          amCustomize.bringingAnyone.options.primaryButton.buttonType : amCustomize.bringingAnyone.options.primaryFooterButton.buttonType"
          :disabled="bookingPersons === (amSettings.appointments.bringingAnyoneLogic === 'additional' ? 1 : 0)"
          @click="bringPeopleWithYou"
        >
          <template v-if="(bringingAnyoneOptions.min !== bringingAnyoneOptions.max) &&  bringingAnyoneOptions.min <= 0">
            {{bringingLabels.bringing_yes}}
          </template>
          <template v-else>
            {{bringingLabels.continue}}
          </template>
        </AmButton>
      </template>
    </AmSlidePopup>
    <!--/ Bringing Anyone with you -->

    <!-- Packages Popup -->
    <PackagesPopup
      class="am-fs__init__package"
      @continue-with-service="continueWithService()"
    />
    <!--/ Packages Popup -->
  </div>

  <!-- Skeleton -->
  <el-skeleton v-else animated :style="cssPackage">
    <template #template>
      <div v-for="item in new Array(3)" :key="item">
        <div class="el-skeleton-item-wrapper">
          <el-skeleton-item variant="h3" />
          <el-skeleton-item variant="text" />
        </div>
      </div>
    </template>
  </el-skeleton>
  <!-- /Skeleton -->
</template>

<script setup>
import { useStore } from "vuex";
import {ref, reactive, computed, watchEffect, inject, provide, markRaw} from 'vue'

import AmSlidePopup from "../../../_components/slide-popup/AmSlidePopup.vue";
import AmButton from "../../../_components/button/AmButton.vue";
import BringingAnyone from "../BringingAnyone/BringingAnyone.vue";
import AmeliaIconClose from "../../../_components/icons/IconClose";
import PackagesPopup from "../PakagesStep/parts/PackagesPopup";

import ServiceFormField from "../_fields/ServiceFormField.vue";
import LocationFormField from "../_fields/LocationFormField.vue";
import EmployeeFormField from "../_fields/EmployeeFormField.vue";

import { useColorTransparency } from "../../../../assets/js/common/colorManipulation";
import { useCapacity } from "../../../../assets/js/common/appointments";
import { useCart } from '../../../../assets/js/public/cart'

let props = defineProps({
  globalClass: {
    type: String,
    default: ''
  }
})

let amCustomize = inject('amCustomize')

// * Amelia Settings
const amSettings = inject('settings')

// * Store
let store = useStore();

// * Document text orientation
let isRtl = computed(() => store.getters['getIsRtl'])

function isFieldFilterable(key) {
  return  (amCustomize.initStep.options[key] &&
    'filterable' in amCustomize.initStep.options[key]) ?
    amCustomize.initStep.options[key].filterable : true
}

let amFields = reactive({
  service: {
    template: markRaw(ServiceFormField),
    props: {
      class: isRtl.value ? 'am-rtl' : '',
      filterable: isFieldFilterable('service'),
      taxVisible: amCustomize.initStep.options.tax?.visibility ?? true
    }
  },
  location: {
    template: markRaw(LocationFormField),
    props: {
      class: isRtl.value ? 'am-rtl' : '',
      visibility: amCustomize.initStep.options.location.visibility,
      filterable: isFieldFilterable('location')
    }
  },
  employee: {
    template: markRaw(EmployeeFormField),
    props: {
      class: isRtl.value ? 'am-rtl' : '',
      visibility: amCustomize.initStep.options.employee.visibility,
      filterable: isFieldFilterable('employee')
    }
  }
})

let loaded = computed(() => {
  return store.getters['entities/getReady']
})

let bookingPersons = computed(() => {
  return store.getters['booking/getBookingPersons']
})

// * Short Code
const shortcodeData = inject('shortcodeData')

// * Labels
const globalLabels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...globalLabels})

  if (amSettings.customizedData && amSettings.customizedData.sbsNew && amSettings.customizedData.sbsNew.initStep.translations) {
    let customizedLabels = amSettings.customizedData.sbsNew.initStep.translations
    Object.keys(customizedLabels).forEach(labelKey => {
      if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
        computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
})

provide('amLabels', amLabels)

let bringingLabels = computed(() => {
  let computedLabels = reactive({...globalLabels})

  if (amSettings.customizedData && amSettings.customizedData.sbsNew && amSettings.customizedData.sbsNew.bringingAnyone.translations) {
    let customizedLabels = amSettings.customizedData.sbsNew.bringingAnyone.translations
    Object.keys(customizedLabels).forEach(labelKey => {
      if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
        computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
})

// * Step Functions
const { nextStep, footerButtonReset, footerButtonClicked } = inject('changingStepsFunctions', {
  nextStep: () => {},
  footerButtonReset: () => {},
  footerButtonClicked: {
    value: false
  }
})

/**
 * * Bringing anyone with you - block
 */

let bringingAnyoneOptions = computed(() => {
  return useCapacity(store.getters['entities/getEmployeeServices'](
    store.getters['booking/getServiceProviderSelection']
  ))
})

provide('bringingOptions', {
  bringingAnyoneOptions
})

// * Bringing anyone with you popup visibility
let bringingAnyoneVisibility = ref(false)

function noOneBringWith() {
  closeBringingPopup()
  store.commit('booking/setBookingPersons', 0)
  nextStep()
}

function bringPeopleWithYou() {
  nextStep()
}

function continueWithService() {
  packagesVisibility.value = false
  if (bringingAnyoneOptions.value.availability) {
    bringingAnyoneVisibility.value = true
  } else {
    nextStep()
  }
}

function closeBringingPopup () {
  bringingAnyoneVisibility.value = false
}

// * Package popup
let packagesOptions = computed(() => store.getters['entities/filteredPackages'](
    store.getters['booking/getSelection']
))

let packagesVisibility = ref(false)

provide('packagesVisibility', packagesVisibility)


/**
 * Form Block start
 */
// * Form reference
let initFormRef = ref(null)

// * Form data
let initFormData = ref({
  service: computed({
    get: () => {
      if (store.getters['booking/getCategoryId'] === null && store.getters['booking/getServiceId'] === null) {
        return null
      }
      return [
        store.getters['booking/getCategoryId'],
        store.getters['booking/getServiceId']
      ]
    },
    set: (val) => {
      store.commit('booking/setCategoryId', val ? val[0] : null)
      store.commit('booking/setServiceId', val ? val[1] : null)
      store.commit('booking/setBookableType', 'appointment')
    }
  }),
  onlyService: computed({
    get: () => store.getters['booking/getServiceId'],
    set: (val) => {
      store.commit('booking/setServiceId', val ? val : null)
      store.commit('booking/setBookableType', 'appointment')
    }
  }),
  location: computed({
    get: () => store.getters['booking/getLocationId'],
    set: (val) => {
      store.commit('booking/setLocationId', val ? val : null)
    }
  }),
  employee: computed({
    get: () => store.getters['booking/getEmployeeId'],
    set: (val) => {
      // let employee = employeeOptions.value.find(item => item.id === val)
      // store.commit('booking/setEmployeeId', employee ? employee.id : null)
      store.commit('booking/setEmployeeId', val ? val : null)
    }
  })
})

provide('initFormData', initFormData)

// * Form validation rules
let rules = ref({
  service: [
    {
      required: true,
      message: amLabels.value.please_select_service,
      trigger: ['blur', 'change'],
    }
  ],
  onlyService: [
    {
      required: true,
      message: amLabels.value.please_select_service,
      trigger: ['blur', 'change'],
    }
  ],
  location: [
    {
      required: amCustomize.initStep.options.location.required,
      message: amLabels.value.please_select_location,
      trigger: ['blur', 'change'],
    }
  ],
  employee: [
    {
      required: amCustomize.initStep.options.employee.required,
      message: amLabels.value.please_select_employee,
      trigger: ['blur', 'change'],
    }
  ],
})

/**
 * Submit Form Function
 */
function submitForm() {
  initFormRef.value.validate((valid) => {
    footerButtonReset()
    if (valid) {
      if (packagesOptions.value.length && shortcodeData.value.show !== 'services' && useCart(store).length <= 1) {
        packagesVisibility.value = true
      } else {
        continueWithService()
      }
    } else {
      return false
    }
  })
}

// * Watching when footer button was clicked
watchEffect(() => {
  if (footerButtonClicked.value) {
    submitForm()
  }
})

// * Colors
let amColors = inject('amColors')
let cssPackage = computed(() => {
  return {
    '--am-c-ps-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-c-ps-text-op20': useColorTransparency(amColors.value.colorMainText, 0.2),
  }
})
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
  }
}
</script>

<style lang="scss">
.amelia-v2-booking #amelia-container {
  .am-fs {
    &__main {
      &-content.am-fs__init {
        padding-top: 40px;

        &.am-oxvisible {
          overflow-x: visible;
        }
      }

      &-inner {
        overflow: hidden;
      }
    }

    &__init {
      &-form {
        &__item {
          $count: 100;
          @for $i from 0 through $count {
            &:nth-child(#{$i + 1}) {
              animation: 600ms cubic-bezier(.45,1,.4,1.2) #{$i*100}ms am-animation-slide-up;
              animation-fill-mode: both;
            }
          }

          &.el-form-item {
            margin-bottom: 24px;
          }
        }

        &__label {
          display: inline-block;
          font-family: var(--am-font-family);
          font-weight: 500;
          color: var(--am-c-main-text);
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
      top:16px;
      right:16px;
      cursor: pointer;
      color: var(--am-c-main-text);
      margin: 0;

      &.am-rtl {
        right: auto;
        left: 16px;
      }
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

          &:before, &:after{
            content: "";
            flex: 1 1;
            border-bottom: 1px solid var(--am-c-ps-text-op20);
            margin: auto;
          }

          &:before {
            margin-right: 10px
          }

          &:after {
            margin-left: 10px
          }
        }

        &__btn {
          &.am-button.am-button--medium {
            --am-h-btn: 56px;
            --am-fs-btn: 14px;
            line-height: 16px;
            width: 100%;
            justify-content: space-between;
          }

          &-mobile {
            &.am-button.am-button--medium {
              --am-fs-btn: 12px
            }
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

  .el-skeleton {
    width: 100%;
    padding: 16px 32px;

    &.skeleton-mobile {
      .el-skeleton {
        padding: 16px;
      }
    }

    &-item-wrapper {
      padding: 8px 0;
      display: flex;
      align-items: flex-start;
      flex-direction: column;

      .el-skeleton__h3 {
        margin-bottom: 4px;
        width: 100px;
        height: 20px;
      }

      .el-skeleton__text {
        width: 100%;
        height: 40px;
      }
    }
  }
}
</style>
