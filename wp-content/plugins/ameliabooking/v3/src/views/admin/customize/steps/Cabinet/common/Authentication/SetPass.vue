<template>
  <div class="am-asi" :style="cssVars">
    <div class="am-asi__top">
      <div class="am-asi__header">
        {{ labelsDisplay('new_password_set') }}
      </div>
      <div class="am-asi__text">
        {{ labelsDisplay('new_password_set_description') }}
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
      :type="amCustomize[pageRenderKey][stepName].options.newPassBtn.buttonType"
      @click="submitForm"
    >
      {{ labelsDisplay('new_password_set_action') }}
    </AmButton>
  </div>
</template>

<script setup>
// * import from Vue
import { ref, computed, inject } from 'vue'

// * Components
import { formFieldsTemplates } from '../../../../../../../assets/js/common/formFieldsTemplates'
import AmButton from '../../../../../../_components/button/AmButton.vue'

// * Composables
import { useResponsiveClass } from '../../../../../../../assets/js/common/responsive.js'
import { useColorTransparency } from '../../../../../../../assets/js/common/colorManipulation.js'
import { useReactiveCustomize } from '../../../../../../../assets/js/admin/useReactiveCustomize.js'

// * Customize
const { amCustomize } = useReactiveCustomize()

// * Labels
let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
let stepName = inject('stepName')

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

/********
 * Form *
 ********/
// * Form reference
let authFormRef = ref(null)

// * Form data
let infoFormData = ref({
  newPassword: '',
  confirmPassword: '',
})

// * Form validation rules
let infoFormRules = computed(() => {
  return {
    newPassword: [
      {
        required: true,
        message: labelsDisplay('new_password_required'),
        trigger: 'submit',
      },
      {
        min: 4,
        message: labelsDisplay('new_password_length'),
        trigger: 'submit',
      },
    ],
    confirmPassword: [
      {
        required: true,
        message: labelsDisplay('new_password_required'),
        trigger: 'submit',
      },
      {
        min: 4,
        message: labelsDisplay('new_password_length'),
        trigger: 'submit',
      },
    ],
  }
})

// * Form construction
let signInFormConstruction = ref({
  newPassword: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'newPassword',
      itemType: 'password',
      showPassword: true,
      label: computed(() => labelsDisplay('new_password_colon')),
      placeholder: '',
      minLength: 3,
      class: 'am-asi__item',
    },
  },
  confirmPassword: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'confirmPassword',
      itemType: 'password',
      showPassword: true,
      label: computed(() => labelsDisplay('new_password_colon_retype')),
      placeholder: '',
      minLength: 3,
      class: 'am-asi__item',
    },
  },
})

// * Submit Form
function submitForm() {
  authFormRef.value.validate((valid) => {
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
  name: 'AuthNewPass',
  key: 'setPass',
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
