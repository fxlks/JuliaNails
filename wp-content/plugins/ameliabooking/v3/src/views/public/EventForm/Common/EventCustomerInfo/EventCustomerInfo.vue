<template>
  <div
    ref="infoFormWrapperRef"
    class="am-elfci"
    :class="props.globalClass"
  >
    <div v-show="!loading">
      <div
        v-if="(paymentError && instantBooking) || (paymentError && isWaitingAvailable)"
        class="am-elfci__error"
      >
        <AmAlert
          type="error"
          :title="paymentError"
          :show-icon="true"
          :closable="false"
        >
        </AmAlert>
      </div>

      <div v-if="authError" class="am-elfci__error">
        <AmAlert
            type="error"
            :title="authErrorMessage"
            :show-icon="true"
            :closable="true"
        >
        </AmAlert>
      </div>

      <!-- Social Buttons -->
      <div v-if="(settings.socialLogin.googleLoginEnabled && settings.general.googleClientId && !store.getters['customerInfo/getLoggedUser']) || (settings.socialLogin.facebookLoginEnabled && settings.socialLogin.facebookCredentialsEnabled && !store.getters['customerInfo/getLoggedUser'])">
        <div class="am-elfci__social-wrapper">
          <div class="am-elfci__social-wrapper__label">
            {{amLabels.auto_fill_your_details}}
          </div>
          <am-social-button
            :provider="socialProvider"
            @social-action="onSignupSocial"
          />
        </div>

        <!-- Social Divider -->
        <div class="am-elfci__social-divider">
          <span class="par-sm">{{ amLabels.or_enter_details_below }}</span>
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
        <template v-for="item in customizedOrder" :key="item.id">
          <component
            :is="infoFormConstruction[item.id].template"
            v-if="item.id in customizedOptions && 'visibility' in customizedOptions[item.id] ? customizedOptions[item.id].visibility : true"
            ref="customerCollectorRef"
            v-model="infoFormData[item.id]"
            v-model:countryPhoneIso="infoFormConstruction[item.id].countryPhoneIso"
            v-bind="infoFormConstruction[item.id].props"
          ></component>
        </template>

        <el-form-item
          v-if="
            amSettings.featuresIntegrations.mailchimp.enabled &&
            amSettings.mailchimp.subscribeFieldVisible &&
            customizedOptions.email.visibility
          "
          class="am-elfci__item am-subscribe"
        >
          <AmCheckbox
            v-model="subscribeToMailchimp"
            :label="amLabels.subscribe_to_mailing_list"
          >
          </AmCheckbox>
        </el-form-item>

        <!-- Custom Fields -->
        <template v-for="(item, index) in eventCustomFieldsArray" :key="index">
          <component
            :is="infoFormConstruction[`cf${item.id}`].template"
            v-if="checkCustomerCustomFieldVisibility(item)"
            ref="customFieldsCollectorRefs"
            v-model="infoFormData[`cf${item.id}`]"
            v-bind="infoFormConstruction[`cf${item.id}`].props"
            @address-selected="(address) => addressSelected(address, item.id)"
          ></component>
        </template>
      </el-form>
    </div>

    <div v-show="!loading">
      <PaymentOnSite
        v-if="isWaitingAvailable || (instantBooking && (amSettings.payments.wc.enabled ? amSettings.payments.wc.onSiteIfFree || !wcEventEnabled : true))"
        ref="refOnSiteBooking"
        :instant-booking="instantBooking"
        @payment-error="callPaymentError"
      />

      <PaymentWc
        v-if="instantBooking && amSettings.payments.wc.enabled && !amSettings.payments.wc.onSiteIfFree && wcEventEnabled"
        ref="refWcBooking"
        :instant-booking="instantBooking"
        @payment-error="callPaymentError"
      />
    </div>

    <CustomerInfoSkeleton />
  </div>
</template>

<script setup>
// * Dedicated parts
import PaymentOnSite from '../../../Parts/Payment/Methods/PaymentOnSite.vue'
import PaymentWc from '../../../Parts/Payment/Methods/PaymentWc.vue'
import CustomerInfoSkeleton from "../../../Parts/Skeletons/CustomerInfoSkeleton.vue";

// * Import from Vue
import {
  ref,
  reactive,
  computed,
  inject,
  onMounted,
  nextTick,
  watchEffect,
  watch
} from 'vue'
import VueAuthenticate from 'vue-authenticate'

// * Form Fields Templates
import { formFieldsTemplates } from '../../../../../assets/js/common/formFieldsTemplates.js'

// * Import from Vuex
import { useStore } from 'vuex'

// * Composables
import { useScrollTo } from '../../../../../assets/js/common/scrollElements.js'
import { useResponsiveClass } from "../../../../../assets/js/common/responsive.js";
import { usePrepaidPrice } from "../../../../../assets/js/common/appointments";

// * _components
import AmAlert from "../../../../_components/alert/AmAlert.vue";
import useAction from "../../../../../assets/js/public/actions";
import {settings} from "../../../../../plugins/settings";
import httpClient from "../../../../../plugins/axios";
import AmSocialButton from "../../../../common/FormFields/AmSocialButton.vue";
import {SocialAuthOptions} from "../../../../../assets/js/admin/socialAuthOptions";
import AmCheckbox from "../../../../_components/checkbox/AmCheckbox.vue";
import {mapAddressComponentsForXML} from "../../../../../assets/js/common/helper";

let props = defineProps({
  globalClass: {
    type: String,
    default: ''
  },
  inDialog: {
    type: Boolean,
    default: false
  }
})

// * Store
const store = useStore()

// get customer data
store.dispatch('customerInfo/requestCurrentUserData')
// filter custom fields
store.dispatch('customFields/filterEventCustomFields')

watch(
    () => store.getters['customerInfo/getLoggedUser'],
    (newValue, oldValue) => {
      if (newValue) {
        let customer = store.getters['customerInfo/getCustomer']

        store.commit('customFields/populateCustomerCustomFields', customer)

        if (customer.customFields.includes('datepicker')) {
          refreshDatePickerValue.value = true
        }
        
        // Force phone component to re-render with new value
        refreshPhoneComponent.value++
      }
    }
)

// * Loading State
let loading = computed(() => store.getters['getLoading'])

// * Root Settings
const amSettings = inject('settings')

// * Event form customization
let customizedDataForm = inject('customizedDataForm')

// * Customization options
let customizedOptions = computed(() => {
  return customizedDataForm.value.customerInfo.options
})

// * Customized fields order
let customizedOrder = computed(() => {
  return customizedDataForm.value.customerInfo.order
})

// * Labels
let labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  if (customizedDataForm.value.customerInfo.translations) {
    let customizedLabels = customizedDataForm.value.customerInfo.translations
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

// * Step functionality
let {
  nextStep,
  footerButtonClicked,
  footerButtonReset
} = inject('changingStepsFunctions', {
  nextStep: () => {},
  footerButtonReset: () => {},
  footerButtonClicked: {
    value: false
  }
})

let selectedEvent = computed(() => store.getters['eventEntities/getEvent'](store.getters['eventBooking/getSelectedEventId']))

// * Custom Fields Array
let eventCustomFieldsArray = computed(() => store.getters['customFields/getFilteredCustomFieldsArray'])

// * Event Custom Fields
let customFields = computed(() => store.getters['customFields/getCustomFields'])

// * Event fluid step keys
let eventFluidStepKey = inject('eventFluidStepKey')

// * Payment recognition
let refOnSiteBooking = ref(null)

let refWcBooking = ref(null)

let instantBooking = ref(usePrepaidPrice(store) === 0)

let isWaitingAvailable = computed(() => store.getters['eventWaitingListOptions/getAvailability'])

/**
 * Form Block start
 */
// * Step reference
let infoFormWrapperRef = ref(null)

// * Form reference
let infoFormRef = ref(null)

// * Customer refs
let customerCollectorRef = ref([])

// * Custom fields refs
let customFieldsCollectorRefs = ref([])

// * All form fields refs
let allFieldsRefs = ref([])

// * InitInfoStep hook - custom fields placeholder
let refCFPlaceholders = ref({})

// * InitInfoStep hook - adding coupon
let couponCode = ref('')

// * Form field date picker needs refresh
let refreshDatePickerValue = ref(false)

// * Form field phone needs refresh
let refreshPhoneComponent = ref(0)

// * Form data
let infoFormData = ref({
  firstName: computed({
    get: () => store.getters['customerInfo/getCustomerFirstName'],
    set: (val) => {
      store.commit('customerInfo/setCustomerFirstName', val ? val : "")
    }
  }),
  lastName: computed({
    get: () => store.getters['customerInfo/getCustomerLastName'],
    set: (val) => {
      store.commit('customerInfo/setCustomerLastName', val ? val : "")
    }
  }),
  email: computed({
    get: () => store.getters['customerInfo/getCustomerEmail'],
    set: (val) => {
      store.commit('customerInfo/setCustomerEmail', val ? val : "")
    }
  }),
  phone: computed({
    get: () => store.getters['customerInfo/getCustomerPhone'],
    set: (val) => {
      store.commit('customerInfo/setCustomerPhone', val ? val : "")
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
      class: 'am-elfci__item',
      disabled: computed(() => {
        return !!(store.getters['customerInfo/getCustomerFirstName'] && store.getters['customerInfo/getLoggedUser'])
      })
    }
  },
  lastName: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'lastName',
      label: amLabels.value.last_name_colon,
      placeholder: amLabels.value.enter_last_name,
      class: 'am-elfci__item',
      disabled: computed(() => {
        return !!(store.getters['customerInfo/getCustomerLastName'] && store.getters['customerInfo/getLoggedUser'])
      })
    }
  },
  email: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'email',
      label: amLabels.value.email_colon,
      placeholder: amLabels.value.enter_email,
      class: 'am-elfci__item',
      disabled: computed(() => {
        return !!(store.getters['customerInfo/getCustomerEmail'] && store.getters['customerInfo/getLoggedUser'])
      })
    }
  },
  phone: {
    countryPhoneIso: computed({
      get: () => store.getters['customerInfo/getCustomerCountryPhoneIso'],
      set: (val) => {
        store.commit('customerInfo/setCustomerCountryPhoneIso', val ? val.toLowerCase() : '')
      }
    }),
    template: formFieldsTemplates.phone,
    props: {
      itemName: 'phone',
      label: amLabels.value.phone_colon,
      placeholder: amLabels.value.enter_phone,
      defaultCode: computed(() => {
        const savedCountry = store.getters['customerInfo/getCustomerCountryPhoneIso']
        return savedCountry || (amSettings.general.phoneDefaultCountryCode === 'auto' ? '' : amSettings.general.phoneDefaultCountryCode.toLowerCase())
      }),
      phoneError: false,
      whatsAppLabel: amLabels.value.whatsapp_opt_in_text,
      isWhatsApp: amSettings.notifications.whatsAppEnabled,
      class: 'am-elfci__item',
      disabled: computed(() => {
        return !!(store.getters['customerInfo/getCustomerPhone'] && store.getters['customerInfo/getLoggedUser'])
      }),
      refreshTrigger: computed(() => refreshPhoneComponent.value)
    }
  },
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
})

let subscribeToMailchimp = computed({
  get: () => store.getters['customerInfo/getCustomerSubscribe'],
  set: (val) => {
    store.commit('customerInfo/setCustomerSubscribe', val)
  }
})

Object.keys(customFields.value).forEach((fieldKey) => {
  // * Form Model
  infoFormData.value[fieldKey] = computed({
    get: () => store.getters['customFields/getCustomFieldValue'](fieldKey),
    set: (val) => {
      let obj = {
        key: fieldKey,
        value: val
      }
      store.commit('customFields/setCustomFieldValue', obj)
    }
  })

  // * Form Rules
  infoFormRules.value[fieldKey] = [{
    message: amLabels.value.required_field,
    required: customFields.value[fieldKey].required,
    trigger: 'submit'
  }]

  // * Form Construction
  infoFormConstruction.value[fieldKey] = {
    template: formFieldsTemplates[customFields.value[fieldKey].type],
    props: {
      id: 'am-cf-' + customFields.value[fieldKey].id,
      itemName: fieldKey,
      label: customFields.value[fieldKey].label,
      options: customFields.value[fieldKey].options,
      class: `am-elfci__item am-cf-width-${customFields.value[fieldKey].width}`
    }
  }

  if (customFields.value[fieldKey].type === 'checkbox' || customFields.value[fieldKey].type === 'radio') {
    infoFormConstruction.value[fieldKey].props.options = infoFormConstruction.value[fieldKey].props.options.map((option) => {
      return {
        ...option,
        value: option.label
      }
    })
  }

  if (customFields.value[fieldKey].type === 'text-area') {
    infoFormConstruction.value[fieldKey].props = {
      ...infoFormConstruction.value[fieldKey].props,
      ...{itemType: 'textarea'}
    }
  }

  if (customFields.value[fieldKey].type === 'file') {
    infoFormConstruction.value[fieldKey].props = {
      ...infoFormConstruction.value[fieldKey].props,
      ...{btnLabel: amLabels.value.upload_file_here}
    }
  }

  if (customFields.value[fieldKey].type === 'datepicker') {
    infoFormConstruction.value[fieldKey].props = {
      ...infoFormConstruction.value[fieldKey].props,
      ...{weekStartsFromDay: amSettings.wordpress.startOfWeek},
      refreshValue: refreshDatePickerValue
    }
  }
})

let wcEventEnabled = ref(false)

onMounted(() => {
  let eventPayments = selectedEvent && selectedEvent.value.settings ? JSON.parse(selectedEvent.value.settings)['payments'] : null

  wcEventEnabled.value = eventPayments && 'wc' in eventPayments
    ? !('enabled' in eventPayments.wc) || eventPayments.wc.enabled
    : settings.payments.wc.enabled

  if (isWaitingAvailable.value) {
    if (eventFluidStepKey.value.indexOf('eventPayment') !== -1) {
      let index = eventFluidStepKey.value.indexOf('eventPayment')
      if (index > 0) {
        eventFluidStepKey.value.splice(index, 1)
      }
    }
  } else {
    // * remove payment step
    if (instantBooking.value && eventFluidStepKey.value.indexOf('eventPayment') !== -1) {
      let index = eventFluidStepKey.value.indexOf('eventPayment')
      if (index > 0) {
        eventFluidStepKey.value.splice(index, 1)
      }
    }

    // * add payment step
    if (!instantBooking.value && eventFluidStepKey.value.indexOf('eventPayment') < 0) {
      eventFluidStepKey.value.push('eventPayment')
    }
  }

  nextTick(() => {
    setTimeout(() => {
      customerCollectorRef.value.forEach(el => {
        if (el.formFieldRef) {
          allFieldsRefs.value.push(el.formFieldRef)
        }
      })

      customFieldsCollectorRefs.value.forEach(el => {
        if (el.formFieldRef) {
          allFieldsRefs.value.push(el.formFieldRef)
        }
      })
    }, 500)
  })

  Object.keys(customFields.value).forEach((fieldKey) => {
    // * Placeholder implementation for custom input and textarea
    if (customFields.value[fieldKey].type === 'text' || customFields.value[fieldKey].type === 'text-area') {
      refCFPlaceholders.value[fieldKey] = {placeholder: ''}
    }
  })

  useAction(
      store,
      { customFields, customFieldsPlaceholders: refCFPlaceholders, couponCode },
      'InitInfoStep',
      'event',
      null,
      null
  )

  if (couponCode.value) {
    store.commit('coupon/setCode', couponCode.value)
  }

  if (Object.values(refCFPlaceholders.value).filter(cf => cf.placeholder !== '').length) {
    Object.keys(refCFPlaceholders.value).forEach((fieldKey) => {
      infoFormConstruction.value[fieldKey].props = {
        ...infoFormConstruction.value[fieldKey].props,
        placeholder: refCFPlaceholders.value[fieldKey].placeholder
      }
    })
  }
})

// * Submit Form
function submitForm() {
  // store.commit('setLoading', true)
  footerButtonReset()

  // Trim inputs
  infoFormData.value.firstName = infoFormData.value.firstName.trim()
  infoFormData.value.lastName = infoFormData.value.lastName.trim()
  infoFormData.value.email = infoFormData.value.email.trim()

  useAction(
      store,
      {rules: infoFormRules.value},
      'customValidation',
      'event',
      null,
      null
  )

  infoFormRef.value.validate((valid) => {
    if (valid) {
      if (isWaitingAvailable.value) {
        store.commit('payment/setPaymentGateway', 'onSite')

        refOnSiteBooking.value.continueWithBooking()
      } else {
        if (!instantBooking.value) {
          nextStep()
        } else {
          if (amSettings.payments.wc.enabled && !amSettings.payments.wc.onSiteIfFree && wcEventEnabled.value) {
            store.commit('payment/setPaymentGateway', 'wc')

            refWcBooking.value.continueWithBooking()
          } else {
            store.commit('payment/setPaymentGateway', 'onSite')

            refOnSiteBooking.value.continueWithBooking()
          }
        }
      }
    } else {
      // store.commit('setLoading', false)
      let fieldElement

      infoFormRef.value.fields.some(el => {
        if (el.validateState === 'error') {
          fieldElement = el.$el
          return el.validateState === 'error'
        }
      })

      let phoneField = infoFormRef.value.fields.find(el => el.prop === 'phone')
      infoFormConstruction.value.phone.props.phoneError = !!(phoneField && phoneField.validateState === 'error')

      // * Scroll to first error
      useScrollTo(infoFormWrapperRef.value, fieldElement, 0, 300)
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

let paymentError = computed(() => store.getters['payment/getError'])

function callPaymentError (msg) {
  store.commit('payment/setError', msg)
}

let visibilityFlags = ref({})

function checkCustomerCustomFieldVisibility (cf) {
  if (cf.saveType === 'customer') {
    let customerCustomFields = store.getters['customerInfo/getCustomer'].customFields

    if (!customerCustomFields || !(cf.id in JSON.parse(customerCustomFields))) {
      return true
    }

    if (visibilityFlags.value[cf.id]) {
      return visibilityFlags.value[cf.id]
    }

    switch (cf.type) {
      case 'checkbox':
      case 'file':
        visibilityFlags.value[cf.id] = !cf.saveFirstChoice && customFields.value['cf' + cf.id].value !== []
        return visibilityFlags.value[cf.id]
      default:
        visibilityFlags.value[cf.id] = !cf.saveFirstChoice && customFields.value['cf' + cf.id].value !== ''
        return visibilityFlags.value[cf.id]
    }
  }

  return true
}

function addressSelected (addressComponents, cfId) {
  if (addressComponents && store.getters['customFields/getCustomFields']['cf' + cfId]) {
    const cf = store.getters['customFields/getCustomFields']['cf' + cfId]
    cf.components = mapAddressComponentsForXML(addressComponents)
    store.commit('customFields/setCustomField', cf)
  }
}

// * Responsive - Container Width
let cWidth = inject('containerWidth')
let dWidth = inject('dialogWidth')

let componentWidth = computed(() => {
  return props.inDialog ? dWidth.value : cWidth.value
})

let responsiveClass = computed(() => useResponsiveClass(componentWidth.value))

let socialProvider = ref('')
const VueAuthenticateInstance = VueAuthenticate.factory(httpClient, SocialAuthOptions)
// * Facebook Sign in error alert
let authError = ref(false)
let authErrorMessage = ref('')

function onSignupSocial({ provider, credentials }) {
  const socialCheckUrl = `/users/authentication/${provider}`
  const data = {}
  socialProvider = provider

  if (provider === 'google') {
    data.code = credentials
    httpClient.post(`${socialCheckUrl}`, data).then(response => {
      setDataFromSocialLogin(response.data.data.user)
    })
  }
  if (provider === 'facebook') {
    VueAuthenticateInstance.options.providers[provider].url = `${socialCheckUrl}`
    VueAuthenticateInstance.authenticate(provider, data).then((response) => {
      setDataFromSocialLogin(response.data.data.user)
    }).catch((error) => {
      if (!VueAuthenticateInstance.isAuthenticated()) {
        authError.value = true
        authErrorMessage.value = 'User is not authenticated.'
        store.commit('setLoading', false)
      }
    })
  }
}

function setDataFromSocialLogin(data) {
  infoFormData.value.firstName = data.firstName
  infoFormData.value.lastName = data.lastName
  infoFormData.value.email = data.email
}
</script>

<script>
export default {
  name: "EventCustomerInfo",
  key: 'customerInfo',
  label: 'event_customer_info'
}
</script>

<style lang="scss">
.amelia-v2-booking #amelia-container {
  // elfci - event list form customer info
  .am-elfci {

    * {
      box-sizing: border-box;
      word-break: break-word;
    }

    &__social-wrapper {
      display: flex;
      align-items: center;
      flex-direction: column;
      width: 100%;
      margin: 8px 0 24px;
      gap: 24px;

      .am-social-signin {
        &__google {
          #g_id_onload {
            display: none;
          }
          .g_id_signin {
            width: 64px;
          }
        }
      }

      &__label {
        font-weight: 500;
        font-size: 15px;
        color: var(--black, #04080B);
      }

      &-button {
        display: flex;
        gap: 8px;
        padding: 8px;
        justify-content: center;
        align-items: center;
        border-radius: 6px;
        flex: 1 1 0;
        height: 40px;
        box-sizing: border-box;
        border: 1px solid $shade-250;
        background: var(--white, #FFF);
        cursor: pointer;
        width: 100%;
        max-width: 100%;
        box-shadow: 0 2px 2px 0 rgba(14, 25, 32, 0.03);
        color: var(--black, #04080B);
        font-size: 15px;
        font-weight: 500;
      }
    }

    &__social-divider {
      align-items: center;
      display: flex;
      margin-bottom: 24px;

      // Before & After
      &:before,
      &:after {
        background: var(--shade-250, #D1D5D7);
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
        color: var(--shade-500, #808A90);
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

    &__event-customer-info {
      &-error {
        animation: 600ms cubic-bezier(.45,1,.4,1.2) #{100}ms am-animation-slide-up;
        animation-fill-mode: both;
        margin-bottom: 10px;
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
            animation: 600ms cubic-bezier(.45,1,.4,1.2) #{$i*100}ms am-animation-slide-up;
            animation-fill-mode: both;
          }
        }
      }

      .am-elfci__item {
        width: calc(50% - 12px);

        &.am-cf-width-100 {
          width: 100%;
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
              align-items: center;
            }
          }
        }

        .el-form-item {
          &__label {
            display: inline-block;
            color: var(--am-c-main-text);
            font-family: var(--am-font-family), sans-serif;
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

          &__content {
            color: var(--am-c-main-text);
          }
        }
      }

      &-mobile {
        gap: 12px 6px;
        .el-form-item {
          width: 100%;
        }
        &-s {
          gap: 0
        }
      }

      &__label {
        display: inline-block;
        color: var(--am-c-main-text);
        font-family: var(--am-font-family), sans-serif;
        font-weight: 500;
        margin-bottom: 4px;
      }
    }

    &__error {
      animation: 600ms cubic-bezier(.45,1,.4,1.2) #{100}ms am-animation-slide-up;
      animation-fill-mode: both;
      margin-bottom: 10px;
    }
  }
}
</style>
