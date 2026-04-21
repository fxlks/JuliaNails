<template>
  <div class="am-asi" :style="cssVars">
    <div class="am-asi__top">
      <AmAlert
        v-if="profileDeleted"
        class="am-asi__top-message"
        type="success"
        :title="labelsDisplay('profile_deleted')"
        :description="''"
        :show-icon="true"
        :closable="true"
        @close="profileDeleted = false"
      ></AmAlert>

      <div class="am-asi__header">
        {{ labelsDisplay('welcome_back') }}
      </div>
      <div class="am-asi__text">
        {{ labelsDisplay('enter_credentials') }}
      </div>
    </div>

    <!-- Social Buttons -->
    <div v-if="features.googleSocialLogin || features.facebookSocialLogin">
      <div class="am-asi__social-wrapper">
        <img
          v-if="features.googleSocialLogin"
          :src="
            baseUrls.wpAmeliaPluginURL + '/v3/src/assets/img/icons/google.svg'
          "
          height="36"
        />
        <img
          v-if="features.facebookSocialLogin"
          :src="
            baseUrls.wpAmeliaPluginURL + '/v3/src/assets/img/icons/facebook.svg'
          "
          height="36"
        />
      </div>
      <!-- /Social Buttons -->

      <!-- Social Divider -->
      <div class="am-asi__social-divider">
        <span class="par-sm">{{
          labelsDisplay('or_enter_details_below')
        }}</span>
      </div>
    </div>
    <!-- /Social Divider -->

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
      :type="amCustomize[pageRenderKey][stepName].options.signInBtn.buttonType"
      @click="submitForm"
    >
      {{ labelsDisplay('sign_in') }}
    </AmButton>

    <div class="am-asi__footer">
      <span class="am-asi__footer-text">
        {{ labelsDisplay('forgot_your_password') }}
      </span>
      <span class="am-asi__footer-link">
        {{ labelsDisplay('reset_password') }}
      </span>
    </div>
  </div>
</template>

<script setup>
// * import from Vue
import { ref, computed, inject, defineComponent, markRaw } from 'vue'

// * Composables
import { useResponsiveClass } from '../../../../../../../assets/js/common/responsive'
import { useColorTransparency } from '../../../../../../../assets/js/common/colorManipulation'

// * Components
import { formFieldsTemplates } from '../../../../../../../assets/js/common/formFieldsTemplates'
import AmButton from '../../../../../../_components/button/AmButton.vue'
import AmAlert from '../../../../../../_components/alert/AmAlert.vue'
import IconComponent from '../../../../../../_components/icons/IconComponent.vue'

// * Customize
const amCustomize = inject('customize')

// * Labels
const amLabels = inject('labels')
let langKey = inject('langKey')

let pageRenderKey = inject('pageRenderKey')
let stepName = inject('stepName')

// * Plugin Licence
let licence = inject('licence')

// * Features
let features = inject('features')

let baseUrls = inject('baseUrls')

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value][stepName.value]
      .translations &&
      amCustomize.value[pageRenderKey.value][stepName.value].translations[
        label
      ] &&
      amCustomize.value[pageRenderKey.value][stepName.value].translations[
        label
      ][langKey.value]
      ? amCustomize.value[pageRenderKey.value][stepName.value].translations[
          label
        ][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Icon components
let emailIcon = defineComponent({
  components: { IconComponent },
  template: `<IconComponent icon="email"></IconComponent>`,
})

let passwordIcon = defineComponent({
  components: { IconComponent },
  template: `<IconComponent icon="password"></IconComponent>`,
})

/********
 * Form *
 ********/
// * Form reference
let authFormRef = ref(null)

// * Form data
let infoFormData = ref({
  email: '',
  password: '',
})

// * Form validation rules
let infoFormRules = computed(() => {
  return {
    email: [
      {
        required: true,
        message: labelsDisplay('enter_email_or_username_warning'),
        trigger: 'submit',
      },
    ],
    password: [
      {
        required: true,
        message: labelsDisplay('enter_password_warning'),
        trigger: 'submit',
      },
    ],
  }
})

// * Form construction
let signInFormConstruction = ref({
  email: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'email',
      label: computed(() => labelsDisplay('email_or_username')),
      prefixIcon: markRaw(emailIcon),
      placeholder: '',
      class: 'am-asi__item',
    },
  },
  password: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'password',
      itemType: 'password',
      showPassword: true,
      label: computed(() => labelsDisplay('password')),
      prefixIcon: markRaw(passwordIcon),
      placeholder: '',
      class: 'am-asi__item',
    },
  },
})

// * Submit Form
let profileDeleted = ref(false)
function submitForm() {
  authFormRef.value.validate((valid) => {
    if (!valid) profileDeleted.value = true
    return !!valid
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
    '--am-c-main-text-op70': useColorTransparency(
      amColors.value.colorMainText,
      0.7
    ),
    '--am-c-main-text-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
    '--am-c-main-text-op40': useColorTransparency(
      amColors.value.colorMainText,
      0.4
    ),
    '--am-c-main-text-op25': useColorTransparency(
      amColors.value.colorMainText,
      0.25
    ),
    '--am-c-inp-bgr': amColors.value.colorInpBgr,
    '--am-c-inp-border': amColors.value.colorInpBorder,
    '--am-c-inp-text': amColors.value.colorInpText,
    '--am-c-inp-placeholder': amColors.value.colorInpPlaceHolder,
    '--am-c-btn-prim': amColors.value.colorBtnPrim,
    '--am-c-btn-prim-text': amColors.value.colorBtnPrimText,
    '--am-c-skeleton-op20': useColorTransparency(
      amColors.value.colorMainText,
      0.2
    ),
    '--am-c-skeleton-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
    '--am-font-family': amFonts.value.fontFamily,

    '--am-c-scroll-op30': useColorTransparency(
      amColors.value.colorPrimary,
      0.3
    ),
    '--am-c-scroll-op10': useColorTransparency(
      amColors.value.colorPrimary,
      0.1
    ),
  }
})
</script>

<script>
export default {
  name: 'AuthSignIn',
  key: 'signIn',
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
    box-shadow: 0 0 9px -4px var(--am-c-main-text-op40),
      0px 17px 35px -12px var(--am-c-main-text-op25);
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

      &-message {
        width: 100%;
        margin-bottom: 22px;
        font-weight: 500;
        font-size: 16px;
        line-height: 1.5;
        border: 1px solid var(--am-c-success);
        border-bottom-width: 4px;
        box-shadow: 0 2px 3px rgba(26, 44, 55, 0.1);
        border-radius: 5px;

        .el-alert {
          &--success {
            border: none;
          }

          .el-icon {
            color: var(--am-c-success);
          }

          &__closebtn {
            font-size: 16px;
            color: var(--am-c-main-text);
          }
        }
      }
    }

    &__social-wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      margin: 8px 0 24px;
      gap: 24px;

      img {
        border: 1px solid #d1d5d7;
        padding: 4px;
        border-radius: 4px;
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
      flex-wrap: wrap;

      &-text {
        font-size: 15px;
        font-weight: 400;
        line-height: 1.6;
        color: var(--am-c-main-text-op70);
        cursor: pointer;
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

// Admin
#amelia-app-backend-new {
  @include auth;
}
</style>
