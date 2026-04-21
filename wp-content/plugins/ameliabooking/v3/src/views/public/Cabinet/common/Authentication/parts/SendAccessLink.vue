<template>
  <div
    v-if="!loading"
    class="am-asi"
    :style="cssVars"
  >
    <div class="am-asi__top">
      <AmAlert
        v-if="authError"
        class="am-asi__top-message am-asi__top-message-error"
        type="error"
        :title="authErrorMessage"
        :description="''"
        :show-icon="true"
        :closable="true"
      ></AmAlert>

      <div class="am-asi__header">
        {{ amLabels.access_link_send }}
      </div>
      <div class="am-asi__text">
        {{ amLabels.access_link_send_description }}
      </div>
    </div>

    <el-form
      ref="authFormRef"
      :model="infoFormData"
      :rules="infoFormRules"
      label-position="top"
      class="am-asi__form"
      :class="responsiveClass"
    >
      <template v-for="(item, index) in signInFormConstruction" :key="index">
        <component
          :is="item.template"
          ref="customerCollectorRef"
          v-model="infoFormData[index]"
          v-bind="item.props"
          @enter="submitForm"
        ></component>
      </template>
    </el-form>

    <AmButton
      class="am-asi__btn"
      :type="amCustomize.accessLink.options.sendBtn.buttonType"
      @click="submitForm"
    >
      {{ amLabels.send }}
    </AmButton>

    <div v-if="amSettings.roles[cabinetType + 'Cabinet']['loginEnabled']" class="am-asi__footer">
      <span class="am-asi__footer-link" @click="pageKey = 'signIn'">
        {{ amLabels.sign_in }}
      </span>
    </div>

    <div
      v-if="amSettings.general.googleRecaptcha.enabled && amSettings.roles[cabinetType + 'Cabinet'].googleRecaptcha"
      id="am-recaptcha"
      class="am-recaptcha-holder"
    >
      <vue-recaptcha
        ref="recaptchaRef"
        :size="amSettings.general.googleRecaptcha.invisible ? 'invisible' : null"
        :load-recaptcha-script="true"
        :sitekey="amSettings.general.googleRecaptcha.siteKey"
        @verify="onRecaptchaVerify"
        @expired="onRecaptchaExpired"
      >
      </vue-recaptcha>
    </div>
  </div>
  <Skeleton v-else :count="4" :center-first="true" :main-class="'am-asi'"></Skeleton>
</template>

<script setup>
// * import from Vue
import {
  ref,
  reactive,
  computed,
  inject,
} from 'vue'
import {VueRecaptcha} from 'vue-recaptcha'

// * Import from Vuex
import { useStore } from 'vuex'

// * Import from Libraries
import httpClient from '../../../../../../plugins/axios'

// * Composables
import { useResponsiveClass } from '../../../../../../assets/js/common/responsive.js'
import { useColorTransparency } from '../../../../../../assets/js/common/colorManipulation'

// * Components
import { formFieldsTemplates } from '../../../../../../assets/js/common/formFieldsTemplates'
import AmButton from '../../../../../_components/button/AmButton.vue'
import Skeleton from './Skeleton'
import AmAlert from "../../../../../_components/alert/AmAlert.vue";

// * Vars
let store = useStore()

// * Root Settings
const amSettings = inject('settings')

// * Customized form data
let amCustomize = inject('amCustomize')

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  let customizedLabels = amCustomize.value.accessLink.translations
  if (customizedLabels) {
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

// * Sign in error alert
let authError = ref(false)
let authErrorMessage = ref('')

/*************
 * Recaptcha *
 ************/

let recaptchaRef = ref(null)

let recaptchaValid = ref(false)

let recaptchaResponse = ref(null)

function onRecaptchaExpired () {
  recaptchaValid.value = false

  authError.value = true
  authErrorMessage.value = amLabels.value.recaptcha_invalid_error
}

function onRecaptchaVerify (response) {
  recaptchaValid.value = true

  recaptchaResponse.value = response

  if (amSettings.general.googleRecaptcha.invisible) {
    reauthorize()
  }
}

/********
 * Form *
 ********/
// * Form reference
let authFormRef = ref(null)

// * Form data
let infoFormData = ref({
  email: computed({
    get: () => store.getters['auth/getEmail'],
    set: (val) => {
      store.commit('auth/setEmail', val ? val : '')
    }
  }),
})

// * Form validation rules
let infoFormRules = ref({
  email: [
    {
      required: true,
      message: amLabels.value.enter_email_warning,
      trigger: 'submit',
    }
  ]
})

// * Form construction
let signInFormConstruction = ref({
  email: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'email',
      itemType: 'email',
      label: amLabels.value.email,
      placeholder: '',
      class: 'am-asi__item'
    }
  },
})

// * Page key
let pageKey = inject('pageKey')

// * Cabinet type
let cabinetType = inject('cabinetType')

// * Loading
let loading = computed(() => {
  return store.getters['getLoading']
})

// * Submit Form
function submitForm() {
  authFormRef.value.validate((valid) => {
    if (valid) {
      if (amSettings.general.googleRecaptcha.enabled && amSettings.roles[cabinetType.value + 'Cabinet'].googleRecaptcha) {
        if (amSettings.general.googleRecaptcha.invisible) {
          recaptchaRef.value.execute()
        } else if (!recaptchaValid.value) {
          authError.value = true
          authErrorMessage.value = amLabels.value.recaptcha_error

          return false
        } else {
          reauthorize()
        }
      } else {
        reauthorize()
      }
    } else {
      return false
    }
  })
}

function reauthorize() {
  store.commit('setLoading', true)

  let params = {
    email: store.getters['auth/getEmail'],
    locale: window.localeLanguage[0],
    cabinetType: cabinetType.value
  }

  if (recaptchaResponse.value !== null) {
    params.recaptcha = recaptchaResponse.value
  }

  httpClient.post(
    '/users/customers/reauthorize',
    params
  ).then(() => {
    pageKey.value = 'sendAccessLinkProcess'
  }).catch(() => {
  }).finally(() => {
    store.commit('setLoading', false)
  })
}

/*************
 * Customize *
 *************/

// * Responsive - Container Width
let cWidth = inject('containerWidth')

let responsiveClass = computed(() => useResponsiveClass(cWidth.value))

// * Fonts
let amFonts = inject('amFonts')

// * Colors block
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-primary': amColors.value.colorPrimary,
    '--am-c-success': amColors.value.colorSuccess,
    '--am-c-error': amColors.value.colorError,
    '--am-c-warning': amColors.value.colorWarning,
    '--am-c-main-bgr': amColors.value.colorMainBgr,
    '--am-c-main-heading-text': amColors.value.colorMainHeadingText,
    '--am-c-main-text': amColors.value.colorMainText,
    '--am-c-main-text-op70': useColorTransparency(amColors.value.colorMainText, 0.7),
    '--am-c-main-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-c-main-text-op40': useColorTransparency(amColors.value.colorMainText, 0.4),
    '--am-c-main-text-op25': useColorTransparency(amColors.value.colorMainText, 0.25),
    '--am-c-inp-bgr': amColors.value.colorInpBgr,
    '--am-c-inp-border': amColors.value.colorInpBorder,
    '--am-c-inp-text': amColors.value.colorInpText,
    '--am-c-inp-placeholder': amColors.value.colorInpPlaceHolder,
    '--am-c-btn-prim': amColors.value.colorBtnPrim,
    '--am-c-btn-prim-text': amColors.value.colorBtnPrimText,
    '--am-c-skeleton-op20': useColorTransparency(amColors.value.colorMainText, 0.2),
    '--am-c-skeleton-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-font-family': amFonts.value.fontFamily,

    '--am-c-scroll-op30': useColorTransparency(amColors.value.colorPrimary, 0.3),
    '--am-c-scroll-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),
  }
})
</script>

<script>

export default {
  name: 'AuthReset',
}
</script>

<style lang="scss">
// am - amelia
// asi - authentication sign in
@mixin auth {
  .am-asi {
    max-width: 400px;
    width: 100%;
    background-color: var(--am-c-main-bgr);
    box-shadow: 0 0 9px -4px var(--am-c-main-text-op40), 0px 17px 35px -12px var(--am-c-main-text-op25);
    border-radius: 12px;
    padding: 32px 24px 24px;
    margin: 0 auto;
    font-family: var(--am-font-family);

    * {
      font-family: var(--am-font-family);
      box-sizing: border-box;
    }

    &__top {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin: 0 0 32px;
    }

    &__header {
      font-size: 24px;
      font-weight: 500;
      line-height: 1.33333;
      color: var(--am-c-main-heading-text);
      margin: 0 0 4px;
    }

    &__text {
      font-size: 15px;
      font-weight: 400;
      line-height: 1.6;
      text-align: center;
      color: var(--am-c-main-text-op70);
    }

    &__form {
      .am-ff {
        &__item {
          &-label {
            font-size: 15px;
            font-weight: 500;
            color: var(--am-c-main-text);
          }
        }
      }

      .el-form {
        &-item {
          margin-bottom: 30px;

          &__label {
            margin: 0 0 4px;
          }
        }
      }
    }

    &__btn {
      width: 100%;
      margin: 16px 0;
    }

    &__footer {
      display: flex;
      align-items: center;
      justify-content: center;

      &-link {
        font-size: 15px;
        font-weight: 400;
        line-height: 1.6;
        color: var(--am-c-primary);
        cursor: pointer;
      }
    }
  }
}

// Public
.amelia-v2-booking #amelia-container {
  @include auth;
}

// Admin
#amelia-app-backend-new {
  @include auth;
}
</style>
