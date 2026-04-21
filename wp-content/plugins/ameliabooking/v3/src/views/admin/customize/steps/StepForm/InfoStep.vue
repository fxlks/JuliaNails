<template>
  <div class="am-fs__main-content am-fs__info">
    <!-- Social Buttons -->
    <div v-if="features.googleSocialLogin || features.facebookSocialLogin">
      <div class="am-fs__info-social-wrapper">
        <div class="am-fs__info-social-wrapper__label">
          {{ labelsDisplay('auto_fill_your_details') }}
        </div>
        <div class="am-fs__info-social-wrapper__social-buttons">
          <img v-if="features.googleSocialLogin" :src="baseUrls.wpAmeliaPluginURL + '/v3/src/assets/img/icons/google.svg'" alt="Google">
          <img v-if="features.facebookSocialLogin" :src="baseUrls.wpAmeliaPluginURL + '/v3/src/assets/img/icons/facebook.svg'" alt="Facebook">
        </div>
      </div>

      <!-- Social Divider -->
      <div class="am-fs__info-social-divider">
        <span class="par-sm">{{
          labelsDisplay('or_enter_details_below')
        }}</span>
      </div>
      <!-- /Social Divider -->
    </div>
    <!-- Social Buttons -->

    <el-form
      ref="infoFormRef"
      :model="infoFormData"
      :rules="rules"
      label-position="top"
      class="am-fs__info-form"
      :class="{ 'am-fs__info-form-mobile': checkScreen }"
    >
      <template
        v-for="item in amCustomize[pageRenderKey].infoStep.order"
        :key="item.id"
      >
        <component :is="formFields[item.id]"></component>
      </template>


      <el-form-item
          v-if="
            amSettings.featuresIntegrations.mailchimp.enabled &&
            amSettings.mailchimp.subscribeFieldVisible &&
            amCustomize[pageRenderKey].infoStep.options.email.visibility
          "
          class="am-fs__info-form__item am-subscribe"
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
import FirstNameFormField from '../../fields/FirstNameFormField.vue'
import LastNameFormField from '../../fields/LastNameFormField.vue'
import EmailFormField from '../../fields/EmailFormField.vue'
import PhoneFormField from '../../fields/PhoneFormField.vue'
import AmCheckbox from '../../../../_components/checkbox/AmCheckbox.vue'
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

import { ref, inject, provide, computed, markRaw } from 'vue'

let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

// * Features
let features = inject('features')

// * Plugin Licence
let licence = inject('licence')

let baseUrls = inject('baseUrls')

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value].infoStep.translations &&
      amCustomize.value[pageRenderKey.value].infoStep.translations[label] &&
      amCustomize.value[pageRenderKey.value].infoStep.translations[label][
        langKey.value
      ]
      ? amCustomize.value[pageRenderKey.value].infoStep.translations[label][
          langKey.value
        ]
      : amLabels[label]
  })

  return computedLabel.value
}

// Container Width
let cWidth = inject('containerWidth', 0)
let checkScreen = computed(() => cWidth.value < 560 || (cWidth.value - 240 < 520))

// * Amelia Settings
const amSettings = inject('settings')

/**
 * Form Block start
 */
// * Form reference
let infoFormRef = ref(null)

let infoFormData = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
})
provide('infoFormData', infoFormData)

// * Form validation rules
let rules = computed(() => {
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
        required:
          amCustomize.value[pageRenderKey.value].infoStep.options.lastName
            .required,
        message: labelsDisplay('enter_last_name_warning'),
        trigger: ['blur', 'submit'],
      },
    ],
    email: [
      {
        required:
          amCustomize.value[pageRenderKey.value].infoStep.options.email
            .required,
        message: labelsDisplay('enter_valid_email_warning'),
        trigger: ['blur', 'submit'],
      },
    ],
    phone: [
      {
        required:
          amCustomize.value[pageRenderKey.value].infoStep.options.phone
            .required,
        message: labelsDisplay('enter_phone_warning'),
        trigger: ['blur', 'submit'],
      },
    ],
  }
})

// * Form Fields Object
let formFields = ref({
  firstName: markRaw(FirstNameFormField),
  lastName: markRaw(LastNameFormField),
  email: markRaw(EmailFormField),
  phone: markRaw(PhoneFormField),
})
</script>

<script>
export default {
  name: 'InfoStep',
  key: 'infoStep',
  sidebarData: {
    label: 'info_step',
    icon: 'user',
    stepSelectedData: [],
    finished: false,
    selected: false,
  },
}
</script>

<style lang="scss">
#amelia-app-backend-new #amelia-container {
  .am-fs {
    &__main {
      &-content.am-fs__info {
        padding-top: 40px;
      }

      &-inner {
        overflow: hidden;
      }
    }

    &__info {
      &-social-wrapper {
        display: flex;
        align-items: center;
        flex-direction: column;
        width: 100%;
        margin-bottom: 24px;
        gap: 24px;

        &__label {
          font-weight: 500;
          font-size: 15px;
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

      &-social-divider {
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
      &-form {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;

        .el-form-item {
          width: calc(50% - 12px);
          $count: 4;
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
        }

        &-mobile {
          gap: 12px 6px;
          .el-form-item {
            width: 100%;
          }
        }

        &__label {
          display: inline-block;
          color: var(--am-c-main-text);
          font-family: var(--am-font-family);
          font-weight: 500;
          margin-bottom: 4px;
        }

        .el-form-item__label {
          line-height: unset;
          padding: 0;
        }
      }
    }
  }
}
</style>
