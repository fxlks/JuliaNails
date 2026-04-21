<template>
  <div ref="infoFormWrapperRef" class="am-elfci" :class="props.globalClass">
    <!-- Social Buttons -->
    <div v-if="features.googleSocialLogin || features.facebookSocialLogin">
      <div class="am-elfci__social-wrapper">
        <div class="am-elfci__social-wrapper__label">
          {{ labelsDisplay('auto_fill_your_details') }}
        </div>
        <div class="am-elfci__social-wrapper__social-buttons">
          <img
            v-if="features.googleSocialLogin"
            :src="
              baseUrls.wpAmeliaPluginURL + '/v3/src/assets/img/icons/google.svg'
            "
            height="32"
          />
          <img
            v-if="features.facebookSocialLogin"
            :src="
              baseUrls.wpAmeliaPluginURL +
              '/v3/src/assets/img/icons/facebook.svg'
            "
            height="32"
          />
        </div>
      </div>

      <!-- Social Divider -->
      <div class="am-elfci__social-divider">
        <span class="par-sm">{{
          labelsDisplay('or_enter_details_below')
        }}</span>
      </div>
      <!-- /Social Divider -->
    </div>
    <!-- /Social Buttons -->

    <el-form
      ref="infoFormRef"
      :model="infoFormData"
      :rules="infoFormRules"
      label-position="top"
      class="am-elfci__form"
      :class="responsiveClass"
    >
      <template v-for="item in customizeOrder" :key="item.id">
        <component
          :is="infoFormConstruction[item.id].template"
          v-if="infoFormConstruction[item.id].visibility"
          ref="customerCollectorRef"
          v-model="infoFormData[item.id]"
          v-model:countryPhoneIso="
            infoFormConstruction[item.id].countryPhoneIso
          "
          v-bind="infoFormConstruction[item.id].props"
        ></component>
      </template>

      <el-form-item
          v-if="
            amSettings.featuresIntegrations.mailchimp.enabled &&
            amSettings.mailchimp.subscribeFieldVisible &&
            amCustomize[pageRenderKey].customerInfo.options.email.visibility
          "
          class="am-elfci__item am-subscribe"
      >
        <AmCheckbox
            :value="amSettings.mailchimp.checkedByDefault"
            :label="labelsDisplay('subscribe_to_mailing_list')"
        >
        </AmCheckbox>
      </el-form-item>
    </el-form>
  </div>
</template>

<script setup>
import AmCheckbox from '../../../../../../_components/checkbox/AmCheckbox.vue'

// * Import from Vue
import { ref, computed, inject } from 'vue'

// * Form Fields Templates
import { formFieldsTemplates } from '../../../../../../../assets/js/common/formFieldsTemplates'

// * Composables
import { useResponsiveClass } from '../../../../../../../assets/js/common/responsive'
import { useReactiveCustomize } from '../../../../../../../assets/js/admin/useReactiveCustomize.js'

// * Components
let props = defineProps({
  globalClass: {
    type: String,
    default: '',
  },
  inDialog: {
    type: Boolean,
    default: false,
  },
})

// * Features
let features = inject('features')

// * Settings
// * Root Settings
const amSettings = inject('settings')

// * Form string recognition
let pageRenderKey = inject('pageRenderKey')

// * Customize
const { amCustomize } = useReactiveCustomize()

// * Plugin Licence
let licence = inject('licence')

let baseUrls = inject('baseUrls')

// * Order
let customizeOrder = computed(() => {
  return amCustomize.value[pageRenderKey.value].customerInfo.order
})

// * Options
let customizeOptions = computed(() => {
  return amCustomize.value[pageRenderKey.value].customerInfo.options
})

// * Labels
let langKey = inject('langKey')
let amLabels = inject('labels')

function labelsDisplay(label) {
  let computedLabel = computed(() => {
    let translations =
      amCustomize.value[pageRenderKey.value].customerInfo.translations
    return translations &&
      translations[label] &&
      translations[label][langKey.value]
      ? translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Form data
let infoFormData = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
})

// * Form construction
let infoFormConstruction = ref({
  firstName: {
    template: formFieldsTemplates.text,
    visibility: true,
    props: {
      itemName: 'firstName',
      label: computed(() => labelsDisplay('first_name_colon')),
      placeholder: computed(() => labelsDisplay('enter_first_name')),
      class: 'am-elfci__item',
    },
  },
  lastName: {
    template: formFieldsTemplates.text,
    visibility: computed(() => customizeOptions.value.lastName.visibility),
    props: {
      itemName: 'lastName',
      label: computed(() => labelsDisplay('last_name_colon')),
      placeholder: computed(() => labelsDisplay('enter_last_name')),
      class: 'am-elfci__item',
    },
  },
  email: {
    template: formFieldsTemplates.text,
    visibility: computed(() => customizeOptions.value.email.visibility),
    props: {
      itemName: 'email',
      label: computed(() => labelsDisplay('email_colon')),
      placeholder: computed(() => labelsDisplay('enter_email')),
      class: 'am-elfci__item',
    },
  },
  phone: {
    countryPhoneIso: '',
    template: formFieldsTemplates.phone,
    visibility: computed(() => customizeOptions.value.phone.visibility),
    props: {
      itemName: 'phone',
      label: computed(() => labelsDisplay('phone_colon')),
      placeholder: computed(() => labelsDisplay('enter_phone')),
      defaultCode:
        amSettings.general.phoneDefaultCountryCode === 'auto'
          ? ''
          : amSettings.general.phoneDefaultCountryCode.toLowerCase(),
      phoneError: false,
      whatsAppLabel: computed(() => labelsDisplay('whatsapp_opt_in_text')),
      isWhatsApp: amSettings.notifications.whatsAppEnabled,
      class: 'am-elfci__item',
    },
  },
})

// * Form validation rules
let infoFormRules = computed(() => {
  return {
    firstName: [
      {
        required: true,
        message: labelsDisplay('enter_first_name_warning'),
        trigger: ['blur', 'submit'],
      },
    ],
    lastName: [
      {
        required: customizeOptions.value.lastName.required,
        message: labelsDisplay('enter_last_name_warning'),
        trigger: ['blur', 'submit'],
      },
    ],
    email: [
      {
        required: customizeOptions.value.email.required,
        type: 'email',
        message: labelsDisplay('enter_valid_email_warning'),
        trigger: ['blur', 'submit'],
      },
    ],
    phone: [
      {
        required: customizeOptions.value.phone.required,
        message: labelsDisplay('enter_phone_warning'),
        trigger: ['blur', 'submit'],
      },
    ],
  }
})

// * Responsive - Container Width
let cWidth = inject('containerWidth')

let componentWidth = computed(() => {
  return cWidth.value
})

let responsiveClass = computed(() => useResponsiveClass(componentWidth.value))
</script>

<script>
export default {
  name: 'EventCustomerInfo',
  key: 'customerInfo',
  label: 'event_customer_info',
}
</script>

<style lang="scss">
#amelia-app-backend-new #amelia-container {
  // elfci - event list form customer info
  .am-elfci {
    &__social-wrapper {
      display: flex;
      align-items: center;
      flex-direction: column;
      width: 100%;
      margin: 8px 0 24px;
      gap: 24px;

      &__label {
        font-weight: 500;
        font-size: 15px;
        color: var(--black, #04080b);
      }

      &__social-buttons {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        gap: 24px;

        img {
          border: 1px solid #d1d5d7;
          padding: 8px;
          border-radius: 4px;
          height: 40px;
          width: 40px;
        }
      }
    }

    &__social-divider {
      align-items: center;
      display: flex;
      margin-bottom: 24px;

      // Before & After
      &:before,
      &:after {
        background: var(--shade-250, #d1d5d7);
        content: '';
        height: 1px;
        width: 100%;
      }

      span {
        flex: none;
        font-size: 15px;
        font-style: normal;
        font-weight: 400;
        line-height: 24px;
        color: var(--shade-500, #808a90);
        margin-left: 8px;
        margin-right: 8px;
      }
    }
    &__main {
      &-content.am-elf__event-customer-info {
        padding-top: 20px;
      }

      &-inner {
        overflow: hidden;
      }
    }

    &__form {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;

      &.am-rw-500 {
        .am-elfci__item {
          width: 100%;
        }
      }

      & > * {
        $count: 100;
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

      .am-elfci__item {
        width: calc(50% - 12px);

        &.am-subscribe {
          width: 100%;
          .el-checkbox {
            &__input {
              height: 32px;
              line-height: 32px;
              align-items: center;
            }

            &__label {
              line-height: 32px;
              padding: 0;
              align-items: center;
            }
          }
        }

        .el-form-item {
          &__label {
            display: inline-block;
            color: var(--am-c-main-text);
            font-family: var(--am-font-family);
            font-weight: 500;
            line-height: unset;
            margin-bottom: 4px;

            &:before {
              color: var(--am-c-error);
            }
          }

          &__error {
            line-height: 1;
            color: var(--am-c-error);
          }
        }
      }

      &-mobile {
        gap: 12px 6px;
        .el-form-item {
          width: 100%;
        }
        &-s {
          gap: 0px;
        }
      }

      &__label {
        display: inline-block;
        color: var(--am-c-main-text);
        font-family: var(--am-font-family);
        font-weight: 500;
        margin-bottom: 4px;
      }
    }
  }
}
</style>
