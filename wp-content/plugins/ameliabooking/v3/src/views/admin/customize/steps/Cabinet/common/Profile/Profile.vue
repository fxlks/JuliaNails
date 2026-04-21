<template>
  <div ref="pageContainer" class="am-capi" :style="cssVars">
    <div class="am-capi__inner">
      <AmAlert
        v-if="alertVisibility"
        type="success"
        :show-border="true"
        :close-after="5000"
        custom-class="am-capi__alert"
        @trigger-close="closeAlert"
      >
        <template #title>
          <span class="am-icon-checkmark-circle-full"></span>
          {{ successMessage }}
        </template>
      </AmAlert>

      <el-tabs v-model="activeTab" class="am-capi__tabs" @tab-click="tabClick">
        <el-tab-pane
          class="am-capi__tabs-item"
          :label="labelsDisplay('personal_info')"
          name="first"
        >
          <el-form
            ref="infoFormRef"
            :model="infoFormData"
            :rules="infoFormRules"
            label-position="top"
            class="am-capi__form"
            :class="responsiveClass"
          >
            <template
              v-for="item in amCustomize.capc.profile.order"
              :key="item.id"
            >
              <component
                :is="infoFormConstruction[item.id].template"
                v-if="
                  amCustomize[pageRenderKey].profile.options[item.id]
                    ? amCustomize[pageRenderKey].profile.options[item.id]
                        .visibility
                    : true
                "
                ref="customerCollectorRef"
                v-model="infoFormData[item.id]"
                v-model:countryPhoneIso="
                  infoFormConstruction[item.id].countryPhoneIso
                "
                v-bind="infoFormConstruction[item.id].props"
              ></component>
            </template>
          </el-form>
        </el-tab-pane>
        <el-tab-pane
          class="am-capi__tabs-item"
          :label="labelsDisplay('password_tab')"
          name="second"
        >
          <el-form
            ref="passFormRef"
            :model="passFormData"
            :rules="passFormRules"
            label-position="top"
            class="am-capi__form"
            :class="responsiveClass"
          >
            <template
              v-for="(item, key) in passFormConstruction"
              :key="item.props.itemName"
            >
              <component
                :is="item.template"
                ref="customerPassCollectorRef"
                v-model="passFormData[key]"
                v-bind="item.props"
              ></component>
            </template>
          </el-form>
        </el-tab-pane>
      </el-tabs>

      <DeleteProfile
        :visibility="deleteProfileVisibility"
        :customized-labels="globalStepLabels('deleteProfile')"
        :customized-options="amCustomize.capc.deleteProfile.options"
      />

      <MainProfileFooter
        :display="activeTab"
        :customized-labels="globalStepLabels('profile')"
        :delete-footer-type="
          amCustomize.capc.profile.options.deleteFooterButton.buttonType
        "
        :save-footer-button="
          amCustomize.capc.profile.options.saveFooterButton.buttonType
        "
        :pass-footer-button="
          amCustomize.capc.profile.options.passFooterButton.buttonType
        "
      />
    </div>
  </div>
</template>

<script setup>
// * _components
import AmAlert from '../../../../../../_components/alert/AmAlert.vue'

// * Dedicated Components
import DeleteProfile from '../parts/DeleteProfile.vue'
import MainProfileFooter from '../../../../../../common/SbsFormConstruction/MainContent/parts/MainProfileFooter.vue'

// * Import from Vue
import { ref, computed, inject, provide, onMounted, nextTick, watch } from 'vue'

// * Composables
import { useResponsiveClass } from '../../../../../../../assets/js/common/responsive'
import { useReactiveCustomize } from '../../../../../../../assets/js/admin/useReactiveCustomize.js'

// * Form Fields Templates
import { formFieldsTemplates } from '../../../../../../../assets/js/common/formFieldsTemplates'
import { useColorTransparency } from '../../../../../../../assets/js/common/colorManipulation'

// * Customize
const { amCustomize } = useReactiveCustomize()

// * Page Content width
let pageContainer = ref(null)
let pageWidth = ref(0)

// * Sidebar collapsed var
let sidebarCollapsed = inject('sidebarCollapsed')

// * window resize listener
window.addEventListener('resize', resize)
// * resize function
function resize() {
  if (pageContainer.value) {
    pageWidth.value = pageContainer.value.offsetWidth
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
  pageWidth.value = pageContainer.value.offsetWidth
}

onMounted(() => {
  nextTick(() => {
    pageWidth.value = pageContainer.value.offsetWidth
  })
})

let responsiveClass = computed(() => {
  return useResponsiveClass(pageWidth.value)
})

// * Root Settings
const amSettings = inject('settings')

// * Labels
let langKey = inject('langKey')
let amLabels = inject('labels')

let stepName = inject('stepName')
let subStepName = inject('subStepName')
let pageRenderKey = inject('pageRenderKey')

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

function globalStepLabels(step) {
  let stepLabels = {}
  let customizedLabels = step
    ? amCustomize.value[pageRenderKey.value][step].translations
    : null
  if (customizedLabels && Object.keys(customizedLabels)) {
    Object.keys(
      amCustomize.value[pageRenderKey.value][step].translations
    ).forEach((label) => {
      stepLabels[label] =
        amCustomize.value[pageRenderKey.value][step].translations[label][
          langKey.value
        ]
    })
  } else {
    stepLabels = {}
  }

  return stepLabels
}

let activeTab = ref('first')

// * Success message
let successMessage = ref('')

// * Change Password Alert visibility
let alertVisibility = ref(false)

function closeAlert() {
  alertVisibility.value = false
  successMessage.value = ''
}

/**
 * * Customer information form
 */

// * Form reference
let infoFormRef = ref(null)

// * Form data
let infoFormData = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  birthday: '',
})

// * Form construction
let infoFormConstruction = computed(() => {
  return {
    firstName: {
      template: formFieldsTemplates.text,
      props: {
        itemName: 'firstName',
        label: labelsDisplay('first_name_colon'),
        placeholder: labelsDisplay('enter_first_name'),
        class: `am-capi__item ${responsiveClass.value}`,
      },
    },
    lastName: {
      template: formFieldsTemplates.text,
      props: {
        itemName: 'lastName',
        label: labelsDisplay('last_name_colon'),
        placeholder: labelsDisplay('enter_last_name'),
        class: `am-capi__item ${responsiveClass.value}`,
      },
    },
    email: {
      template: formFieldsTemplates.text,
      props: {
        itemName: 'email',
        label: labelsDisplay('email_colon'),
        placeholder: labelsDisplay('enter_email'),
        class: `am-capi__item ${responsiveClass.value}`,
      },
    },
    phone: {
      countryPhoneIso: 'US',
      template: formFieldsTemplates.phone,
      props: {
        itemName: 'phone',
        label: labelsDisplay('phone_colon'),
        placeholder: labelsDisplay('enter_phone'),
        defaultCode:
          amSettings.general.phoneDefaultCountryCode === 'auto'
            ? 'us'
            : amSettings.general.phoneDefaultCountryCode.toLowerCase(),
        phoneError: false,
        whatsAppLabel: labelsDisplay('whatsapp_opt_in_text'),
        isWhatsApp: amSettings.notifications.whatsAppEnabled,
        class: `am-capi__item ${responsiveClass.value}`,
      },
    },
    birthday: {
      template: formFieldsTemplates.datepicker,
      props: {
        itemName: 'birthday',
        label: labelsDisplay('date_of_birth'),
        placeholder: labelsDisplay('enter_date_of_birth'),
        clearable: true,
        readonly: false,
        class: `am-capi__item ${responsiveClass.value}`,
      },
    },
  }
})

// * Form validation rules
let infoFormRules = computed(() => {
  return {
    firstName: [
      {
        required: true,
        message: labelsDisplay('enter_first_name_warning'),
        trigger: 'submit',
      },
    ],
    lastName: [
      {
        required:
          amCustomize.value[pageRenderKey.value][stepName.value].options
            .lastName.required,
        message: labelsDisplay('enter_last_name_warning'),
        trigger: 'submit',
      },
    ],
    email: [
      {
        required:
          amCustomize.value[pageRenderKey.value][stepName.value].options.email
            .required,
        type: 'email',
        message: labelsDisplay('enter_valid_email_warning'),
        trigger: 'submit',
      },
    ],
    phone: [
      {
        required:
          amCustomize.value[pageRenderKey.value][stepName.value].options.phone
            .required,
        message: labelsDisplay('enter_phone_warning'),
        trigger: 'submit',
      },
    ],
    birthday: [
      {
        required:
          amCustomize.value[pageRenderKey.value][stepName.value].options
            .birthday.required,
        message: labelsDisplay('enter_date_of_birth_warning'),
        trigger: 'submit',
      },
    ],
  }
})

let deleteProfileDialog = ref(false)
provide('deleteProfileDialog', deleteProfileDialog)

function saveProfileChanges() {
  infoFormRef.value.validate((valid) => {
    if (valid) {
      successMessage.value = labelsDisplay('profile_data_success')
      alertVisibility.value = true
    } else {
      return false
    }
  })
}
provide('saveProfileChanges', saveProfileChanges)

// * Delete Profile
let deleteProfileVisibility = computed(() => {
  return subStepName.value === 'deleteProfile'
})

/**
 * * Customer change password form
 */

// * Form reference
let passFormRef = ref(null)

// * Form data
let passFormData = ref({
  newPass: '',
  confirmPass: '',
})

// * Form construction
let passFormConstruction = computed(() => {
  return {
    newPass: {
      template: formFieldsTemplates.text,
      props: {
        itemName: 'newPass',
        itemType: 'password',
        showPassword: true,
        label: labelsDisplay('new_password_colon'),
        placeholder: '',
        minLength: 3,
        class: `am-capp__item ${responsiveClass.value}`,
      },
    },
    confirmPass: {
      template: formFieldsTemplates.text,
      props: {
        itemName: 'confirmPass',
        itemType: 'password',
        showPassword: true,
        label: labelsDisplay('new_password_colon_retype'),
        placeholder: '',
        minLength: 3,
        class: `am-capp__item ${responsiveClass.value}`,
      },
    },
  }
})

// * Form validation rules
let passFormRules = computed(() => {
  return {
    newPass: [
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
    confirmPass: [
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
      {
        validator: () =>
          passFormData.value.newPass === passFormData.value.confirmPass,
        message: labelsDisplay('passwords_not_match'),
        trigger: 'submit',
      },
    ],
  }
})

function tabClick() {
  alertVisibility.value = false
}

// * Submit Form
function changeProfilePassword() {
  passFormRef.value.validate((valid) => {
    if (valid) {
      successMessage.value = labelsDisplay('password_success')
      alertVisibility.value = true
    } else {
      return false
    }
  })
}
provide('changeProfilePassword', changeProfilePassword)

// * Colors
let amColors = inject('amColors')
let cssVars = computed(() => {
  return {
    '--am-c-capi-primary': amColors.value.colorPrimary,
    '--am-c-capi-text': amColors.value.colorMainText,
    '--am-c-capi-text-op10': useColorTransparency(
      amColors.value.colorMainText,
      0.1
    ),
  }
})
</script>

<script>
export default {
  name: 'CabinetProfile',
  key: 'profile',
}
</script>

<style lang="scss">
@mixin am-cabinet-profile {
  // am - amelia
  // capi - cabinet personal information
  // capp - cabinet personal password
  .am-capi {
    &__inner {
      display: block;
      padding: 16px 32px;
    }

    // Tabs
    .el-tabs {
      &__nav {
        &-wrap {
          &:after {
            background-color: var(--am-c-capi-text-op10);
          }
        }
      }

      &__active-bar {
        background-color: var(--am-c-capi-primary);
      }

      &__item {
        &.is-focus {
          color: var(--am-c-capi-text);

          &.is-active {
            color: var(--am-c-capi-primary);

            &:focus {
              &:not(:active) {
                box-shadow: none;
              }
            }
          }
        }
      }

      &__content {
        overflow: unset;
      }
    }

    &__form {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;

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

      // * traba da postanu globalni stilovi za formu
      .el-form {
        &-item {
          display: block;
          font-family: var(--am-font-family), sans-serif;
          font-size: var(--am-fs-label);
          margin-bottom: 24px;

          &.am-capi__item {
            width: calc(50% - 12px);

            &.am-rw-480 {
              width: 100%;
            }
          }

          &.am-capp__item {
            width: 100%;

            .el-form-item__content {
              max-width: 360px;
            }
          }

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
            font-size: var(--am-fs-input);
            min-width: 0;
            color: var(--am-c-main-text);
          }

          &__error {
            font-size: 12px;
            color: var(--am-c-error);
            padding-top: 4px;
          }
        }
      }
    }

    &__alert {
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

          .am-icon-checkmark-circle-full {
            font-size: 28px;
            line-height: 1;
            color: var(--am-c-alerts-bgr);
          }
        }
      }
    }
  }
}
#amelia-app-backend-new #amelia-container {
  @include am-cabinet-profile;
}
</style>
