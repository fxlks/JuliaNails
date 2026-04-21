<template>
  <div
    v-if="!loading"
    class="am-asi"
    :style="cssVars"
  >
    <div class="am-asi__top">
      <div class="am-asi__header">
        {{ amLabels.new_password_set }}
      </div>
      <div class="am-asi__text">
        {{ amLabels.new_password_set_description }}
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
      :type="amCustomize.setPass.options.newPassBtn.buttonType"
      @click="submitForm"
    >
      {{ amLabels.new_password_set_action }}
    </AmButton>
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

// * Import from Vuex
import { useStore } from 'vuex'

// * Import from Libraries
import httpClient from '../../../../../../plugins/axios'

// * Components
import { formFieldsTemplates } from '../../../../../../assets/js/common/formFieldsTemplates'
import AmButton from '../../../../../_components/button/AmButton.vue'
import Skeleton from './Skeleton'

// * Composables
import { useResponsiveClass } from '../../../../../../assets/js/common/responsive.js'
import { useColorTransparency } from '../../../../../../assets/js/common/colorManipulation.js'
import { useAuthorizationHeaderObject } from '../../../../../../assets/js/public/panel'

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

  let customizedLabels = amCustomize.value.setPass.translations
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

/********
 * Form *
 ********/
// * Form reference
let authFormRef = ref(null)

// * Form data
let infoFormData = ref({
  newPassword: computed({
    get: () => store.getters['auth/getNewPassword'],
    set: (val) => {
      store.commit('auth/setNewPassword', val ? val : '')
    }
  }),
  confirmPassword: computed({
    get: () => store.getters['auth/getConfirmPassword'],
    set: (val) => {
      store.commit('auth/setConfirmPassword', val ? val : '')
    }
  }),
})

// * Form validation rules
let infoFormRules = ref({
  newPassword: [
    {
      required: true,
      message: amLabels.value.new_password_required,
      trigger: 'submit',
    },
    {
      min: 4,
      message: amLabels.value.new_password_length,
      trigger: 'submit'
    }
  ],
  confirmPassword: [
    {
      required: true,
      message: amLabels.value.new_password_required,
      trigger: 'submit',
    },
    {
      min: 4,
      message: amLabels.value.new_password_length,
      trigger: 'submit'
    },
    {
      validator: () => store.getters['auth/getNewPassword'] === store.getters['auth/getConfirmPassword'],
      message: amLabels.value.passwords_not_match,
      trigger: 'submit'
    },
  ],
})

// * Form construction
let signInFormConstruction = ref({
  newPassword: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'newPassword',
      itemType: 'password',
      showPassword: true,
      label: amLabels.value.new_password_colon,
      placeholder: '',
      minLength: 3,
      class: 'am-asi__item'
    }
  },
  confirmPassword: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'confirmPassword',
      itemType: 'password',
      showPassword: true,
      label: amLabels.value.new_password_colon_retype,
      placeholder: '',
      minLength: 3,
      class: 'am-asi__item'
    }
  },
})

// * Cabinet type
let cabinetType = inject('cabinetType')

// * Loading
let loading = computed(() => {
  return store.getters['getLoading']
})

// * Submit Form
function submitForm() {
  authFormRef.value.validate((valid) => {
    if (valid && store.getters['auth/getNewPassword'] === store.getters['auth/getConfirmPassword']) {
      let user = store.getters['auth/getProfile']

      store.commit('setLoading', true)

      httpClient.post(
        '/users/' + cabinetType.value + 's/' + user.id,
        {password: store.getters['auth/getNewPassword']},
        Object.assign(useAuthorizationHeaderObject(store), {params: {source: 'cabinet-' + cabinetType.value}})
      ).then(() => {
        store.commit('auth/setAuthenticated', true)
      }).catch(() => {
      }).finally(() => {
        store.commit('setLoading', false)
      })
    } else {
      return false
    }
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
  name: 'AuthNewPass'
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

      &-text {
        font-size: 15px;
        font-weight: 400;
        line-height: 1.6;
        margin: 0 4px 0 0;
        color: var(--am-c-main-text-op60);
      }

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
