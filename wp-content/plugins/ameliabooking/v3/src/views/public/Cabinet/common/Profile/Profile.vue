<template>
  <div
    ref="pageContainer"
    class="am-capi"
    :style="cssVars"
  >
    <div class="am-capi__inner">
      <AmAlert
        v-if="alertVisibility"
        type="success"
        :show-border="true"
        :close-after="5000"
        custom-class="am-capi__alert"
        @close="closeAlert"
        @trigger-close="closeAlert"
      >
        <template #title>
          <span class="am-icon-checkmark-circle-full"></span> {{ successMessage }}
        </template>
      </AmAlert>

      <el-tabs
        v-model="activeTab"
        class="am-capi__tabs"
        @tab-click="tabClick"
      >
        <el-tab-pane
          class="am-capi__tabs-item"
          :label="amLabels.personal_info"
          name="first"
        >
          <template v-if="!loading">
            <el-form
              v-if="store.getters['auth/getProfile']"
              ref="infoFormRef"
              :model="infoFormData"
              :rules="infoFormRules"
              label-position="top"
              class="am-capi__form"
              :class="responsiveClass"
            >
              <template v-for="item in amCustomize.profile.order" :key="item.id">
                <component
                  :is="infoFormConstruction[item.id].template"
                  v-if="customizedOptions[item.id] ? customizedOptions[item.id].visibility : true"
                  ref="customerCollectorRef"
                  v-model="infoFormData[item.id]"
                  v-model:countryPhoneIso="infoFormConstruction[item.id].countryPhoneIso"
                  v-bind="infoFormConstruction[item.id].props"
                ></component>
              </template>
            </el-form>
          </template>
          <ProfileSkeleton
            v-else
            :count="5"
            :page-width="pageWidth"
          ></ProfileSkeleton>
        </el-tab-pane>

        <!-- Customer Custom Fields -->
        <el-tab-pane
          v-if="Object.keys(customFields).length"
          :label="amLabels.custom_fields"
          name="third"
        >
          <template v-if="!loading">
            <el-form
              ref="customFieldsFormRef"
              :model="customFieldsForm"
              :rules="customFieldsFormRules"
              label-position="top"
              class="am-capei-att-cf__form"
              :class="responsiveClass"
            >
              <template v-for="cf in Object.values(customFields)" :key="cf.id">
                <component
                  :is="cfFormConstruction[cf.id]?.template"
                  v-if="cf.id in customFieldsForm"
                  v-model="customFieldsForm[cf.id]"
                  v-bind="cfFormConstruction[cf.id]?.props"
                  @address-selected="(address) => addressSelected(address, cf.id)"
                />
              </template>
            </el-form>
          </template>
          <ProfileSkeleton
            v-else
            :count="Object.keys(customFields).length"
            :page-width="pageWidth"
          ></ProfileSkeleton>
        </el-tab-pane>
        <!-- /Customer Custom Fields -->

        <el-tab-pane
          class="am-capi__tabs-item"
          :label="amLabels.password_tab"
          name="second"
        >
          <template v-if="!loading">
            <el-form
              ref="passFormRef"
              :model="passFormData"
              :rules="passFormRules"
              label-position="top"
              class="am-capi__form"
              :class="responsiveClass"
            >
              <template v-for="(item, key) in passFormConstruction" :key="item.props.itemName">
                <component
                  :is="item.template"
                  ref="customerPassCollectorRef"
                  v-model="passFormData[key]"
                  v-bind="item.props"
                ></component>
              </template>
            </el-form>
          </template>

          <ProfileSkeleton
            v-else
            :item-direction="'column'"
            :count="2"
            :page-width="pageWidth"
          ></ProfileSkeleton>
        </el-tab-pane>
      </el-tabs>

      <DeleteProfile
        :visibility="deleteProfileDialog"
        :customized-labels="customizedStepLabels('deleteProfile')"
        :customized-options="amCustomize.deleteProfile.options"
        @close="deleteProfileDialog = false"
        @delete-profile="deleteProfile"
      />

      <MainProfileFooter
        :loading="loading"
        :display="activeTab"
        :parent-width="pageWidth"
        :customized-labels="amLabels"
        :save-footer-button="customizedOptions.saveFooterButton.buttonType"
        :delete-footer-type="customizedOptions.deleteFooterButton.buttonType"
        :pass-footer-button="customizedOptions.passFooterButton.buttonType"
      />
    </div>
  </div>
</template>

<script setup>
// * _components
import AmAlert from "../../../../_components/alert/AmAlert.vue";

// * Dedicated Components
import DeleteProfile from "../parts/DeleteProfile.vue"
import MainProfileFooter from "../../../../common/SbsFormConstruction/MainContent/parts/MainProfileFooter.vue"
import ProfileSkeleton from "./parts/ProfileSkeleton.vue";

// * Import Libraries
import moment from "moment";
import { useCookies } from "vue3-cookies";
import httpClient from "../../../../../plugins/axios";

// * Import from Vue
import {
  ref,
  reactive,
  computed,
  inject,
  provide,
  onMounted,
  nextTick,
  watch
} from "vue";

// * Composables
import {
  useResponsiveClass
} from "../../../../../assets/js/common/responsive";
import {
  useAuthorizationHeaderObject
} from "../../../../../assets/js/public/panel";
import {
  useColorTransparency
} from "../../../../../assets/js/common/colorManipulation";

// * Form Fields Templates
import { formFieldsTemplates } from "../../../../../assets/js/common/formFieldsTemplates";

// * Import from Vuex
import { useStore } from "vuex";
import {mapAddressComponentsForXML} from "../../../../../assets/js/common/helper";
let store = useStore()

// * Loading state
let loading = computed(() => store.getters['getLoading'])

// * Page Content width
let pageContainer = ref(null)
let pageWidth = ref(0)

// * Sidebar collapsed var
let sidebarCollapsed = inject('sidebarCollapsed')

// * window resize listener
window.addEventListener('resize', resize);
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

function collapseTriggered () {
  pageWidth.value = pageContainer.value.offsetWidth
}

onMounted(() => {
  nextTick(() => {
    pageWidth.value = pageContainer.value.offsetWidth
  })
  setInitCustomerCustomFields()
})

let responsiveClass = computed(() => {
  return useResponsiveClass(pageWidth.value)
})

// * Root Settings
const amSettings = inject('settings')

// * Cabinet type
let cabinetType = inject('cabinetType')

// * Customized form data
let amCustomize = inject('amCustomize')

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

function customizedLabelsObject (obj, custObj) {
  if (custObj) {
    Object.keys(custObj).forEach(labelKey => {
      if (custObj[labelKey][localLanguage.value] && langDetection.value) {
        obj[labelKey] = custObj[labelKey][localLanguage.value]
      } else if (custObj[labelKey].default) {
        obj[labelKey] = custObj[labelKey].default
      }
    })
  }
  return obj
}

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})
  let customizedLabels = amCustomize.value.profile.translations

  return customizedLabelsObject(computedLabels, customizedLabels)
})

// * Dedicated step labels
function customizedStepLabels (step) {
  let stepLabels = {}
  let customizedLabels = amCustomize.value[step].translations

  return customizedLabelsObject(stepLabels, customizedLabels)
}

// * Customized options
let customizedOptions = computed(() => {
  return amCustomize.value.profile.options
})

let activeTab = ref('first')

// * Success message
let successMessage = ref('')

function closeAlert () {
  alertVisibility.value = false
  successMessage.value = ''
}

/**
 * * Customer information form
 */

// * Form reference
let infoFormRef = ref(null)

let phoneError = ref(false)

// * Form data
let infoFormData = ref({
  firstName: computed({
    get: () => store.getters['auth/getProfile'].firstName,
    set: (val) => {
      store.commit('auth/setProfileFirstName', val ? val : "")
    }
  }),
  lastName: computed({
    get: () => store.getters['auth/getProfile'].lastName,
    set: (val) => {
      store.commit('auth/setProfileLastName', val ? val : "")
    }
  }),
  email: computed({
    get: () => store.getters['auth/getProfile'].email,
    set: (val) => {
      store.commit('auth/setProfileEmail', val ? val : "")
    }
  }),
  phone: computed({
    get: () => store.getters['auth/getProfile'].phone,
    set: (val) => {
      store.commit('auth/setProfilePhone', val ? val : "")
    }
  }),
  birthday: computed({
    get: () => store.getters['auth/getProfile'].birthday ? store.getters['auth/getProfile'].birthday : '',
    set: (val) => {
      store.commit('auth/setProfileBirthday', val ? moment(val).format('YYYY-MM-DD') : "")
    }
  }),
})

// * Form construction
let infoFormConstruction = ref({
  firstName: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'firstName',
      label: amLabels.value.first_name_colon,
      placeholder: amLabels.value.enter_first_name,
      class: computed(() => `am-capi__item ${responsiveClass.value}`),
    }
  },
  lastName: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'lastName',
      label: amLabels.value.last_name_colon,
      placeholder: amLabels.value.enter_last_name,
      class: computed(() => `am-capi__item ${responsiveClass.value}`),
    }
  },
  email: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'email',
      label: amLabels.value.email_colon,
      placeholder: amLabels.value.enter_email,
      class: computed(() => `am-capi__item ${responsiveClass.value}`),
    }
  },
  phone: {
    countryPhoneIso: computed({
      get: () => store.getters['auth/getProfile'].countryPhoneIso ? store.getters['auth/getProfile'].countryPhoneIso : '',
      set: (val) => {
        store.commit('auth/setProfileCountryPhoneIso', val ? val.toLowerCase() : '')
      }
    }),
    template: formFieldsTemplates.phone,
    props: {
      itemName: 'phone',
      label: amLabels.value.phone_colon,
      placeholder: amLabels.value.enter_phone,
      defaultCode: computed(() => store.getters['auth/getProfile'].countryPhoneIso ? store.getters['auth/getProfile'].countryPhoneIso : ''),
      phoneError: computed(() => phoneError.value),
      whatsAppLabel: amLabels.value.whatsapp_opt_in_text,
      isWhatsApp: amSettings.notifications.whatsAppEnabled,
      class: computed(() => `am-capi__item ${responsiveClass.value}`),
    }
  },
  birthday: {
    template: formFieldsTemplates.datepicker,
    props: {
      itemName: 'birthday',
      label: amLabels.value.date_of_birth,
      placeholder: amLabels.value.enter_date_of_birth,
      clearable: true,
      readonly: false,
      class: computed(() => `am-capi__item am-capi__item-birthday ${responsiveClass.value}`),
    }
  }
})

// * Form validation rules
let infoFormRules = ref({
  firstName: [
    {
      required: true,
      message: amLabels.value.enter_first_name_warning,
      trigger: 'submit',
    }
  ],
  lastName: [
    {
      required: customizedOptions.value.lastName.required,
      message: amLabels.value.enter_last_name_warning,
      trigger: 'submit',
    }
  ],
  email: [
    {
      required: customizedOptions.value.email.required,
      type: 'email',
      message: amLabels.value.enter_valid_email_warning,
      trigger: 'submit',
    }
  ],
  phone: [
    {
      required: customizedOptions.value.phone.required,
      message: amLabels.value.enter_phone_warning,
      trigger: 'submit',
    }
  ],
  birthday: [
    {
      required: customizedOptions.value.birthday.required,
      message: amLabels.value.enter_date_of_birth_warning,
      trigger: 'submit',
    }
  ],
})

let deleteProfileDialog = ref(false)
provide('deleteProfileDialog', deleteProfileDialog)

function saveProfileChanges () {
  // Trim inputs
  store.commit('auth/setProfileFirstName', infoFormData.value.firstName ? infoFormData.value.firstName.trim() : "")
  store.commit('auth/setProfileLastName', infoFormData.value.lastName ? infoFormData.value.lastName.trim() : "")
  store.commit('auth/setProfileEmail', infoFormData.value.email ? infoFormData.value.email.trim() : "")

  infoFormRef.value.validate((valid) => {
    if (valid) {
      successMessage.value = amLabels.value.profile_data_success
      let user = store.getters['auth/getProfile']

      store.commit('setLoading', true)

      httpClient.post(
        '/users/' + cabinetType.value + 's/' + user.id,
        user,
        useAuthorizationHeaderObject(store)
      ).finally(() => {
        store.commit('setLoading', false)
        alertVisibility.value = true
        if (customizedOptions.value.phone.required && infoFormData.value.phone) {
          phoneError.value = false
        }
      })
    } else {
      if (customizedOptions.value.phone.required && !infoFormData.value.phone) {
        phoneError.value = true
      }
      return false
    }
  })
}
provide('saveProfileChanges', saveProfileChanges)

/**
 * * Customer change password form
 */

// * Form reference
let passFormRef = ref(null)

// * Form data
let passFormData = ref({
  newPass: computed({
    get: () => store.getters['auth/getNewPassword'],
    set: (val) => {
      store.commit('auth/setNewPassword', val ? val : "")
    }
  }),
  confirmPass: computed({
    get: () => store.getters['auth/getConfirmPassword'],
    set: (val) => {
      store.commit('auth/setConfirmPassword', val ? val : "")
    }
  })
})

// * Form construction
let passFormConstruction = ref({
  newPass: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'newPass',
      itemType: 'password',
      showPassword: true,
      label: amLabels.value.new_password_colon,
      placeholder: '',
      minLength: 3,
      class: computed(() => `am-capp__item ${responsiveClass.value}`),
    }
  },
  confirmPass: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'confirmPass',
      itemType: 'password',
      showPassword: true,
      label: amLabels.value.new_password_colon_retype,
      placeholder: '',
      minLength: 3,
      class: computed(() => `am-capp__item ${responsiveClass.value}`),
    }
  }
})

// * Form validation rules
let passFormRules = ref({
  newPass: [
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
  confirmPass: [
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
    }
  ],
})

// * Change Password Alert visibility
let alertVisibility = ref(false)

function tabClick () {
  alertVisibility.value = false
}

// * Submit Form
function changeProfilePassword() {
  passFormRef.value.validate((valid) => {
    if (valid) {
      let user = store.getters['auth/getProfile']

      store.commit('setLoading', true)

      httpClient.post(
        '/users/customers/' + user.id,
        {password: store.getters['auth/getNewPassword']},
        useAuthorizationHeaderObject(store)
      ).then(() => {
        successMessage.value = amLabels.value.password_success

        store.commit('auth/setNewPassword', '')
        store.commit('auth/setConfirmPassword', '')
      }).catch(() => {
      }).finally(() => {
        store.commit('setLoading', false)
        alertVisibility.value = true
      })
    } else {
      return false
    }
  })
}
provide('changeProfilePassword', changeProfilePassword)

// * Logout
function logout () {
  deleteProfileDialog.value = false
  store.commit('auth/setProfileDeleted', true)
  store.dispatch('auth/logout')
}

function addressSelected (addressComponents, cfId) {
  if (addressComponents) {
    for (const key in customFields) {
      if (customFields[key].id === cfId) {
        customFields[key].components = mapAddressComponentsForXML(addressComponents)
        break;
      }
    }
  }
}

function deleteProfile () {
  store.commit('setLoading', true)

  let profileId =  store.getters['auth/getProfile'] ? store.getters['auth/getProfile'].id : null
  if (!profileId) {
    return
  }

  let data =   {
    email: '',
    firstName: amLabels.value.customer,
    lastName: profileId,
    phone: '',
    birthday: '',
    gender: '',
    externalId: '',
    password: ''
  };

  const vueCookies = useCookies()['cookies']

  httpClient.post(
    '/users/customers/' + profileId,
    Object.assign(
      useAuthorizationHeaderObject(store),
      data
    )
  ).then(() => {
    store.commit('auth/setProfile', {})
    vueCookies.remove('ameliaUserEmail')

    store.commit('setLoading', false)

    logout()
  }).catch((error) => {
    store.commit('setLoading', false)
    console.log(error)
  })
}

/**
 * * Customer custom fields form
 */

// Refs and reactive objects
const customFieldsFormRef = ref(null)
const customFieldsForm = reactive({})
const customFieldsFormRules = reactive({})
const cfFormConstruction = reactive({})
const customFields = reactive({})

function setCustomerCustomFieldsFormData(field, id, savedValue) {
  customFieldsForm[id] = savedValue ?? ''
}

function setCustomerCustomFieldsFormConstruction(field, id) {
  cfFormConstruction[id] = {
    template: formFieldsTemplates[field.type],
    props: {
      id,
      itemName: id.toString(),
      label: field.label,
      options: field.options,
      class: `am-capei-att-cf__item am-cf-width-${field.width}`,
    },
  }

  if (field.type === 'text-area') {
    cfFormConstruction[id].props.itemType = 'textarea'
  }

  if (field.type === 'datepicker') {
    cfFormConstruction[id].props.weekStartsFromDay = amSettings.wordpress.startOfWeek
  }

  if (field.type === 'file') {
    let profileId =  store.getters['auth/getProfile'] ? store.getters['auth/getProfile'].id : null
    cfFormConstruction[id].props = {
      ...cfFormConstruction[id].props,
      ...{
        btnLabel: amLabels.value.upload_file_here,
        isUpload: true,
        bookingId: profileId,
        source: 'cabinet-customer'
      },
    }
  }
}

function setCustomerCustomFieldsFormRules(field) {
  customFieldsFormRules[field.id] = [
    {
      message: amLabels.value.required_field,
      required: field.required,
      trigger: ['submit', 'change'],
    },
  ]
}

function setInitCustomerCustomFields() {
  const allFields = store.getters['entities/getCustomFields']
  const savedCustomFields = JSON.parse(store.getters['auth/getProfile'].customFields || '{}')

  allFields
    .filter(field => field.saveType === 'customer' && field.type !== 'content')
    .sort((a, b) => a.position - b.position)
    .forEach((field, index) => {
      const { id } = field
      const savedValue = savedCustomFields[id]?.value

      customFields[index] = {
        ...field,
        value: savedValue ?? (field.type === 'checkbox' || field.type === 'file' ? [] : ''),
      }

      if (field.type === 'address' && savedCustomFields[id]?.components) {
        customFields[index].components = savedCustomFields[id]?.components
      }

      setCustomerCustomFieldsFormData(field, id, customFields[index].value)
      setCustomerCustomFieldsFormConstruction(field, id)
      setCustomerCustomFieldsFormRules(field)
    })
}

function saveCustomerCustomFields() {
  customFieldsFormRef.value.validate((valid) => {
    if (valid) {
      const user = store.getters['auth/getProfile']
      const customFieldData = {}

      // Build full custom field data structure
      for (const id in customFields) {
        const field = customFields[id]
        const value = customFieldsForm[field.id]
        customFieldData[field.id] = {
          label: field.label,
          type: field.type,
          value: typeof value === 'string' ? value.trim() : value,
        }
        if (field.type === 'address' && field.components) {
          customFieldData[field.id].components = field.components
        }
      }

      const updatedUser = {
        ...user,
        customFields: JSON.stringify(customFieldData),
      }

      store.commit('setLoading', true)

      httpClient.post(
          `/users/${cabinetType.value}s/${user.id}`,
          updatedUser,
          useAuthorizationHeaderObject(store)
      ).finally(() => {
        store.commit('setLoading', false)
        successMessage.value = amLabels.value.profile_data_success
        alertVisibility.value = true
      })
    } else {
      return false
    }
  })
}
provide('saveCustomerCustomFields', saveCustomerCustomFields)

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-capi-primary': amColors.value.colorPrimary,
    '--am-c-capi-text': amColors.value.colorMainText,
    '--am-c-capi-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1)
  }
})
</script>

<script>
export default {
  name: "CabinetProfile",
  key: 'profile'
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

    &__form {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;

      & > * {
        $count: 100;
        @for $i from 0 through $count {
          &:nth-child(#{$i + 1}) {
            animation: 600ms cubic-bezier(.45,1,.4,1.2) #{$i*100}ms am-animation-slide-up;
            animation-fill-mode: both;
          }
        }
      }

      // * traba da postanu globalni stilovi za formu
      .el-form {
        &-item {
          display: block;
          font-family: var(--am-font-family);
          font-size: var(--am-fs-label);
          margin-bottom: 24px;

          &.am-capi__item {
            width: calc(50% - 12px);

            &-birthday {
              .el-input__inner {
                color: transparent !important;
                text-shadow: 0 0 0 var(--am-c-inp-text);
                cursor: pointer;
              }
            }

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

      .am-whatsapp-opt-in-text {
        font-size: 14px;
        line-height: 1.5;
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

.amelia-v2-booking #amelia-container {
  @include am-cabinet-profile;
}
</style>
