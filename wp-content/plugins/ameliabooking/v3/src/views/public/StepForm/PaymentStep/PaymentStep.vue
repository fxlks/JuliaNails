<template>
  <div
    ref="paymentStepRef"
    :class="props.globalClass"
    :style="cssVars"
    tabindex="0"
  >
    <div v-if="paymentError" class="am-fs__payments-error">
      <AmAlert
        type="error"
        :title="paymentError"
        :show-icon="true"
        :closable="false"
      >
      </AmAlert>
    </div>
    <div v-show="ready && !loading" class="am-fs__payments">
      <div class="am-fs__payments-heading">
        <span class="am-fs__payments-heading-main">
          {{ amLabels.summary }}
        </span>
      </div>

      <div class="am-fs__payments-price">
        <component :is="componentTypes[formType]" @set-on-site-payment="setOnSitePayment"></component>
      </div>

      <AmCheckbox
        v-if="hasDeposit && fullAmount && paymentGateway !== 'onSite'"
        v-model="paymentDeposit"
        class="am-fs__payments-full"
        :label="amLabels.full_amount_consent"
        :class="{'am-fs__payments-full-checked': paymentDeposit}"
      >
      </AmCheckbox>

      <p
        v-show="Object.keys(availablePayments).filter(item => availablePayments[item]).length > 1"
        class="am-fs__payments-method"
      >
        {{ amLabels.payment_method }}
      </p>
      <div class="am-fs__payments-main">
        <div class="am-fs__payments-main-cards">
          <template v-for="(available, gateway) in availablePayments">
            <div
              v-if="available && Object.keys(availablePayments).filter(item => availablePayments[item]).length > 1"
              :key="gateway"
              :class="'am-fs__payments-main-button' + (paymentGateway === gateway ? ' am-fs__payments-main-button_selected' : '') + ' am-fs__payments-main-button-' + gateway"
              @click="setPaymentGateway(gateway)"
            >
              <img
                v-if="available"
                :src="baseUrls.wpAmeliaPluginURL + '/v3/src/assets/img/icons/' + paymentsBtn.find(item => item.key === gateway).value.name"
                :alt="gateway"
              >
              <div>
                <p>{{ paymentsBtn.find(item => item.key === gateway).value.text }}</p>
              </div>
            </div>
          </template>
        </div>
      </div>
      <div class="am-fs__payments-sentence">
        <p>{{getPaymentSentence()}}
        </p>
      </div>
      <PaymentOnSite
          ref="refOnSitePayment"
          :instant-booking="true"
          :show-recaptcha="false"
          @payment-error="callPaymentError"
      />
      <div v-if="paymentGateway" class="am-fs__payments-pm">
        <component
          :is="paymentTypes[paymentGateway]"
          @payment-error="callPaymentError"
          @payment-abandoned="callPaymentAbandoned"
        />
      </div>
    </div>

    <BookingSkeleton/>

  </div>
</template>

<script setup>
import { useStore } from "vuex";
import {
  ref,
  inject,
  provide,
  markRaw,
  watch,
  computed,
  onMounted,
  reactive
} from "vue";
import AmCheckbox from '../../../_components/checkbox/AmCheckbox.vue'
import AmAlert from '../../../_components/alert/AmAlert.vue'
import AppointmentInfo from './parts/AppointmentInfo'
import CartInfo from './parts/CartInfo'
import PaymentStripe from '../../Parts/Payment/PaymentStripe.vue'
import PaymentPayPal from '../../Parts/Payment/PaymentPayPal.vue'
import PaymentOnSite from '../../Parts/Payment/PaymentOnSite.vue'
import PaymentWc from '../../Parts/Payment/PaymentWc.vue'
import PaymentCommon from '../../Parts/Payment/PaymentCommon.vue'
import PaymentSquare from '../../Parts/Payment/PaymentSquare.vue'
import BookingSkeleton from "../../Parts/BookingSkeleton.vue";
import PackageInfo from './parts/PackageInfo'
import { settings } from "../../../../plugins/settings";
import { useColorTransparency } from "../../../../assets/js/common/colorManipulation";
import { usePrepaidPrice, usePaymentError } from "../../../../assets/js/common/appointments";
import useAction from "../../../../assets/js/public/actions";
import {
  useCart,
  useCartItem,
  useCartHasItems,
} from '../../../../assets/js/public/cart'

let props = defineProps({
  globalClass: {
    type: String,
    default: ''
  }
})

// * Store
const store = useStore()
const baseUrls = inject('baseUrls')

// * Labels
const globalLabels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => settings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...globalLabels})

  if (settings.customizedData && settings.customizedData.sbsNew && settings.customizedData.sbsNew.paymentStep.translations) {
    let customizedLabels = settings.customizedData.sbsNew.paymentStep.translations
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

// * Payment Part
const componentTypes = {
  appointment: markRaw(AppointmentInfo),
  package: markRaw(PackageInfo),
  cart: markRaw(CartInfo)
}

let paymentError = computed(() => store.getters['booking/getError'])

// * Step Functions
const {
  footerBtnDisabledUpdater,
  headerButtonPreviousClicked,
  footerButtonClick
} = inject('changingStepsFunctions', {
  footerBtnDisabledUpdater: () => {},
  headerButtonPreviousClicked: {
    value: false
  },
  footerButtonClick: () => {}
})

watch(headerButtonPreviousClicked, () => {
  store.commit('booking/setPaymentGateway', '')
})

let cart = computed(() => useCart(store))

const paymentTypes = {
  onSite: markRaw(PaymentOnSite),
  stripe: markRaw(PaymentStripe),
  payPal: markRaw(PaymentPayPal),
  razorpay: markRaw(PaymentCommon),
  mollie: markRaw(PaymentCommon),
  wc: markRaw(PaymentWc),
  square: markRaw(PaymentSquare),
  barion: markRaw(PaymentCommon)
}

let ready = computed(() => store.getters['entities/getReady'])

let loading = computed(() => store.getters['booking/getLoading'])

let formType = computed(() => cart.value.length > 1 ? 'cart' : store.getters['booking/getBookableType'])

let paymentGateway = computed(() => {
  if (!store.getters['booking/getPaymentGateway']) {
    store.commit('booking/setPaymentGateway', settings.defaultPaymentMethod)
  }
  return store.getters['booking/getPaymentGateway']
})

let hasDeposit = computed(() => {
  switch (store.getters['booking/getBookableType']) {
    case ('appointment'): {
      let depositPayment = false

      useCart(store).filter(i => 'services' in i && Object.keys(i.services).length).forEach((item) => {
        let service = store.getters['entities/getCategories'].find(
          i => i.serviceList.filter(j => j.id === item.serviceId).length
        ).serviceList.find(
          i => i.id === item.serviceId
        )

        if (service.depositPayment!== 'disabled') {
          depositPayment = true
        }
      })

      return ready.value ? depositPayment : false
    }

    case ('package'): {
      let item = useCartItem(store)

      let pack = store.getters['entities/getPackage'](item.packageId)

      return ready.value ? pack.depositPayment !== 'disabled' : false
    }
  }

  return false
})

provide('hasDeposit', hasDeposit)

let fullAmount = computed(() => {
  let fullAmountPayment = false

  useCart(store).filter(i => 'services' in i && Object.keys(i.services).length).forEach((item) => {
    let service = store.getters['entities/getCategories'].find(
      i => i.serviceList.filter(j => j.id === item.serviceId).length
    ).serviceList.find(
      i => i.id === item.serviceId
    )

    if (service.fullPayment) {
      fullAmountPayment = true
    }
  })

  return ready.value && fullAmountPayment
})

// true = pay full amount
let paymentDeposit = computed({
  get: () => store.getters['booking/getPaymentDeposit'],
  set: (value) => store.commit('booking/setPaymentDeposit', value)
})

let paymentStepRef = ref(null)
provide('paymentStepRef', paymentStepRef)

let mandatoryOnSitePayment = ref(false)

let refOnSitePayment = ref(null)

function callPaymentError (msg) {
  if (store.getters['payment/getOnSitePayment']) {
    store.commit('payment/setOnSitePayment', false)
    store.commit('booking/setPaymentGateway', 'onSite')
    mandatoryOnSitePayment.value = true
    refOnSitePayment.value.continueWithBooking()
  }
  usePaymentError(store, msg)
}

function callPaymentAbandoned () {
  store.commit('booking/setLoading', false)
}

let availablePayments = computed(() => {
  if (ready.value) {
    if (mandatoryOnSitePayment.value) {
      return {
        onSite: true,
        stripe: false,
        payPal: false,
        wc: false,
        mollie: false,
        square: false,
        razorpay: false,
        barion: false,
      }
    }

    let entity = null

    switch (store.getters['booking/getBookableType']) {
      case ('appointment'):
        entity = useCartHasItems(store) === 1 ? store.getters['entities/getBookableFromBookableEntities'](
          store.getters['booking/getSelection']
        ) : null

        break

      case ('package'):
        entity = store.getters['entities/getPackage'](
          store.getters['booking/getPackageId']
        )

        break
    }

    let entityPayments = entity && entity.settings ? JSON.parse(entity.settings)['payments'] : null


    let payments = !entityPayments ? {
      onSite: settings.payments.onSite,
      stripe: settings.payments.stripe.enabled,
      payPal: settings.payments.payPal.enabled,
      wc: settings.payments.wc.enabled,
      mollie: settings.payments.mollie.enabled,
      square: settings.payments.square.enabled,
      razorpay: settings.payments.razorpay.enabled,
      barion: settings.payments.barion.enabled,
    } : {
      onSite: 'onSite' in entityPayments ? entityPayments.onSite && settings.payments.onSite : settings.payments.onSite,
      stripe: 'stripe' in entityPayments ? entityPayments.stripe.enabled && settings.payments.stripe.enabled : settings.payments.stripe.enabled,
      payPal: 'payPal' in entityPayments ? entityPayments.payPal.enabled && settings.payments.payPal.enabled : settings.payments.payPal.enabled,
      wc: 'wc' in entityPayments ? (!('enabled' in entityPayments.wc) || entityPayments.wc.enabled) && settings.payments.wc.enabled : settings.payments.wc.enabled,
      mollie: 'mollie' in entityPayments ? entityPayments.mollie.enabled && settings.payments.mollie.enabled : settings.payments.mollie.enabled,
      razorpay: 'razorpay' in entityPayments ? entityPayments.razorpay.enabled && settings.payments.razorpay.enabled : settings.payments.razorpay.enabled,
      square: 'square' in entityPayments ? entityPayments.square.enabled && settings.payments.square.enabled : settings.payments.square.enabled,
      barion: 'barion' in entityPayments ? entityPayments.barion.enabled && settings.payments.barion.enabled && ['USD', 'EUR', 'HUF', 'CZK'].includes(settings.payments.currencyCode) : settings.payments.barion.enabled && ['USD', 'EUR', 'HUF', 'CZK'].includes(settings.payments.currencyCode),
    }

    if (!payments.onSite &&
        !payments.stripe &&
        !payments.payPal &&
        !payments.wc &&
        !payments.mollie &&
        !payments.square &&
        !payments.razorpay &&
        !payments.barion
    ) {
      payments = {
        onSite: settings.payments.onSite,
        stripe: settings.payments.stripe.enabled,
        payPal: settings.payments.payPal.enabled,
        wc: settings.payments.wc.enabled,
        mollie: settings.payments.mollie.enabled,
        square: settings.payments.square.enabled,
        razorpay: settings.payments.razorpay.enabled,
        barion: settings.payments.barion.enabled,
      }
    }

    if (settings.payments.defaultPaymentMethod && payments[settings.payments.defaultPaymentMethod]) {
      store.commit('booking/setPaymentGateway', settings.payments.defaultPaymentMethod)

      footerBtnDisabledUpdater(false)
    } else {
      if (Object.keys(payments).filter(item => payments[item]).length === 1) {
        for (let key in payments) {
          if (payments[key]) {
            store.commit('booking/setPaymentGateway', key)

            footerBtnDisabledUpdater(false)
          }
        }
      }
    }

    return payments
  }

  return {}
})

function setPaymentGateway (gateway) {
  store.commit('booking/setPaymentGateway', gateway)

  footerBtnDisabledUpdater(false)
}

function setOnSitePayment (isMandatory) {
  if (settings.payments.wc.enabled ? settings.payments.wc.onSiteIfFree : true) {
    if (isMandatory) {
      store.commit('booking/setPaymentGateway', 'onSite')
    }

    mandatoryOnSitePayment.value = isMandatory
  }
}

onMounted(() => {
  const isPaymentStatusError = paymentError.value && (
    paymentError.value === globalLabels.payment_canceled ||
    paymentError.value === globalLabels.payment_error ||
    paymentError.value === globalLabels.payment_error_default
  )

  // If it's a payment status error (canceled/failed) delay clear for 3s
  if (!isPaymentStatusError) {
    usePaymentError(store, '')
  } else {
    setTimeout(() => {usePaymentError(store, '')}, 3000)
  }

  if (usePrepaidPrice(store) === 0) {
    if (settings.payments.wc.enabled === true && !settings.payments.wc.onSiteIfFree) {
      store.commit('booking/setPaymentGateway', 'wc')
    } else {
      store.commit('booking/setPaymentGateway', 'onSite')
    }

    footerButtonClick()
  }

  useAction(store, {}, 'InitiateCheckout', formType.value, null, null)
})

let amColors = inject('amColors')

const cssVars = computed(() => {
  return  {
    '--am-c-ps-price-bgr': useColorTransparency(amColors.value.colorBtnPrim, 0.05),
    '--am-c-ps-total-price': amColors.value.colorBtnPrim,
    '--am-c-ps-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-c-ps-text-op50': useColorTransparency(amColors.value.colorMainText, 0.5),
    '--am-c-ps-text-op25': useColorTransparency(amColors.value.colorMainText, 0.25),
    '--am-c-ps-text-op20': useColorTransparency(amColors.value.colorMainText, 0.2),
    '--am-c-ps-text-op06': useColorTransparency(amColors.value.colorMainText, 0.06),
    '--am-c-ps-primary': amColors.value.colorPrimary,
    '--am-c-ps-primary-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),
    '--am-c-ps-primary-op06': useColorTransparency(amColors.value.colorPrimary, 0.06)
  }
})

let paymentsBtn = []

Object.keys(availablePayments.value).forEach(key => {
  paymentsBtn.push({key, value: getPaymentBtn(key) })
})

function getPaymentBtn (key) {
  switch(key) {
    case 'onSite':
      return {text: amLabels.value['on_site'], name: 'onSite.svg'}
    case 'payPal':
      return {text: amLabels.value['pay_pal'], name: 'payPal.svg'}
    case 'stripe':
      return {text: amLabels.value['stripe'], name: 'stripe.svg'}
    case 'razorpay':
      return {text: amLabels.value['razorpay'], name: 'razorpay.svg'}
    case 'mollie':
      return {text: amLabels.value['mollie'], name: 'mollie.svg'}
    case 'wc':
      return {text: amLabels.value['on_line'], name: 'online.svg'}
    case 'square':
      return {text: amLabels.value['square'], name: 'square.svg'}
    case 'barion':
      return {text: amLabels.value['barion'], name: 'barion.svg'}
    default:
      return ''
  }
}

function getPaymentSentence () {
  return paymentGateway.value === 'onSite' && !mandatoryOnSitePayment.value ? amLabels.value.payment_onsite_sentence :
    (paymentGateway.value === 'mollie' || paymentGateway.value === 'wc' || paymentGateway.value === 'barion') ? amLabels.value.payment_wc_mollie_sentence : ''
}

</script>

<script>
export default {
  name: 'PaymentStep',
  key: 'paymentStep',
  sidebarData: {
    label: 'payment_step',
    icon: 'payment',
    stepSelectedData: [],
    finished: false,
    selected: false,
  }
}
</script>

<style lang="scss">
.amelia-v2-booking #amelia-container {
  .am-fs__main-content.am-fs__payments {
    padding-top:0;
  }

  .am-fs__payments {
    --am-c-ps-text: var(--am-c-main-text);
    --am-c-ps-border: var(--am-c-inp-border);
    & > * {
      $count: 6;
      @for $i from 0 through $count {
        &:nth-child(#{$i + 1}) {
          animation: 600ms cubic-bezier(.45,1,.4,1.2) #{$i*100}ms am-animation-slide-up;
          animation-fill-mode: both;
        }
      }
    }

    &-heading {
      font-size: 15px;
      font-style: normal;
      font-weight: 500;
      line-height: 1.6;
      color: var(--am-c-ps-text);
      margin-bottom: 4px;
      animation: 600ms cubic-bezier(.45,1,.4,1.2) #{100}ms am-animation-slide-up;
      animation-fill-mode: both;
    }

    &-price {
      border: 1px solid var(--am-c-ps-text-op20);
      border-radius: 8px;
      padding: 16px;
      margin-bottom: 16px;
    }

    &-sentence {
      display: flex;
      justify-content: center;
      margin-top: 2px;
      p {
        display: inline-flex;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.42857;
        color: var(--am-c-ps-text-op60);
      }
    }

    &-full {
      color: var(--am-c-ps-text);
      border: 1px solid var(--am-c-ps-text-op25);
      border-radius: 8px;
      box-shadow: 0 1px 1px var(--am-c-ps-text-op06);
      box-sizing: border-box;
      padding: 12px;
      margin-bottom: 16px;

      &-checked {
        border: 1px solid var(--am-c-ps-primary);
        background: var(--am-c-ps-primary-op10);

        &:after {
          transform: rotate(45deg) scaleY(1);
        }
      }

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

    &-method {
      font-size: 18px;
      font-weight: 500;
      line-height: 1.55555;
      color: var(--am-c-ps-text);
      margin-bottom: 8px;
    }

    &-error {
      animation: 600ms cubic-bezier(.45,1,.4,1.2) #{100}ms am-animation-slide-up;
      animation-fill-mode: both;
      margin-bottom: 10px;
    }

    &-main {
      &-cards {
        display: flex;
        gap: 6px;
        justify-items: center;
        flex-wrap: wrap;
      }

      &-button {
        display: flex;
        align-items: center;
        gap: 2px;
        min-width: 108px;
        border: 1px solid var(--am-c-ps-text-op25);
        border-radius: 8px;
        box-shadow: 0 1px 1px var(--am-c-ps-text-op06);
        box-sizing: border-box;
        padding: 12px;
        cursor: pointer;
        flex-direction: column;
        justify-items: center;
        height: fit-content;

        img {
          height: 24px;
          width: 24px;
        }

        &-barion {
          img {
            height: 24px;
            width: 50px;
          }
        }

        div {
          p {
            font-weight: 500;
            font-size: 15px;
            line-height: 24px;
            color: var(--am-c-ps-text);
            margin: 0;
            text-align: center;
            word-break: break-all;
          }
          span {
            font-size: 12px;
            font-weight: 400;
            line-height: 1.66666;
            color: var(--am-c-ps-text-op50);
          }
        }

        &_selected {
          background: var(--am-c-ps-primary-op10);
          border: 1px solid var(--am-c-ps-primary);
          box-sizing: border-box;
          box-shadow: 0 1px 1px var(--am-c-ps-primary-op06);
        }
      }
    }
    // pm - payment method
    &-pm {
      margin-top: 24px;
    }

    .el-checkbox__label {
      font-size: 15px;
    }
  }
}
</style>
