<template>
  <div
    v-show="ready && !loading"
    ref="paymentRef"
    class="am-payments"
    :class="[props.componentClass, props.globalClass]"
    :style="cssVars"
  >
    <div class="am-payments__heading">
      <div
        v-if="paymentError"
        class="am-payments__error"
      >
        <AmAlert
          type="error"
          :title="paymentError"
          :show-icon="true"
          :closable="false"
        >
        </AmAlert>
      </div>
      <span class="am-payments__heading-main">
        {{ amLabels.summary }}
      </span>
    </div>

    <component
      :is="componentTypes[bookableType]"
      :tax-visibility="props.taxVisibility"
      @set-on-site-payment="setOnSitePayment"
    ></component>

    <AmCheckbox
      v-if="hasDeposit && fullAmount && paymentGateway !== 'onSite'"
      v-model="paymentDeposit"
      class="am-payments__full"
      :label="amLabels.full_amount_consent"
      :class="{'am-payments__full-checked': paymentDeposit}"
    >
    </AmCheckbox>

    <div class="am-payments__method">
      <p
        v-show="Object.keys(availablePayments).filter(item => availablePayments[item]).length > 1"
        class="am-payments__method-heading"
      >
        {{ amLabels.payment_method }}
      </p>
      <div class="am-payments__method-cards">
        <template v-for="(available, gateway) in availablePayments">
          <div
            v-if="available && Object.keys(availablePayments).filter(item => availablePayments[item]).length > 1"
            :key="gateway"
            class="am-payments__method-button"
            :class="[{'am-payments__method-button__selected' : paymentGateway === gateway }, responsiveClass, `am-payments__method-button-${paymentList.length}`]"
            @click="setPaymentGateway(gateway)"
          >
            <img
              v-if="available"
              :src="`${baseUrls.wpAmeliaPluginURL}/v3/src/assets/img/icons/${gateway === 'wc' ? 'online' : gateway}.svg`"
              :alt="gateway"
              :class="gateway"
            >
            <div>
              <p>{{ getPaymentBtnString(gateway) }}</p>
            </div>
          </div>
        </template>
      </div>
    </div>
    <div class="am-payments__sentence">
      <p>
        {{ paymentSentence }}
      </p>
    </div>
    <PaymentOnSite
        ref="refOnSitePayment"
        :instant-booking="true"
        :show-recaptcha="false"
        @payment-error="callPaymentError"
    />
    <div v-if="paymentGateway" class="am-payments__pm">
      <component
        :is="paymentTypes[paymentGateway]"
        @payment-error="callPaymentError"
        @payment-abandoned="callPaymentAbandoned"
      />
    </div>
  </div>

  <BookingSkeleton />
</template>

<script setup>
// * _components
import AmCheckbox from '../../../../_components/checkbox/AmCheckbox.vue'
import AmAlert from '../../../../_components/alert/AmAlert.vue'

// * Info
// import AppointmentInfo from '../Info/AppointmentPaymentInfo.vue'
// import PackageInfo from '../Info/PackagePaymentInfo.vue'
import EventPaymentInfo from '../Info/EventPaymentInfo.vue'

// * Methods
import PaymentStripe from '../Methods/PaymentStripe.vue'
import PaymentPayPal from '../Methods/PaymentPayPal.vue'
import PaymentOnSite from '../Methods/PaymentOnSite.vue'
import PaymentWc from '../Methods/PaymentWc.vue'
import PaymentCommon from '../Methods/PaymentCommon.vue'
import PaymentSquare from "../Methods/PaymentSquare.vue";

// * Loader component
import BookingSkeleton from '../../../Parts/BookingSkeleton.vue'

// * Import from Vue
import {
  ref,
  inject,
  provide,
  markRaw,
  watch,
  computed,
  onMounted
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Composables
import useAction from "../../../../../assets/js/public/actions";
import { useColorTransparency } from "../../../../../assets/js/common/colorManipulation";
import { usePrepaidPrice } from "../../../../../assets/js/common/appointments";
import { useResponsiveClass } from "../../../../../assets/js/common/responsive";
import {settings} from "../../../../../plugins/settings";

// * Properties
let props = defineProps({
  globalClass: {
    type: String,
    default: ''
  },
  componentClass: {
    type: String,
    default: ''
  },
  selectedItem: {
    type: Object,
    required: true
  },
  inDialog: {
    type: Boolean,
    default: false
  },
  taxVisibility: {
    type: Boolean,
    default: false
  }
})

// * Store
const store = useStore()

// * Amelia Settings
let amSettings = computed(() => store.getters['getSettings'])

// * Amelia URLs
let baseUrls = inject('baseUrls')

// * labels
const amLabels = inject('amLabels')

// * Payment Part
const componentTypes = {
  // appointment: markRaw(AppointmentInfo),
  // package: markRaw(PackageInfo),
  event: markRaw(EventPaymentInfo)
}

// * Payment Error
let paymentError = computed(() => {
  if (store.getters['payment/getError']) return store.getters['payment/getError']
  if (store.getters['coupon/getError']) return store.getters['coupon/getError']

  return ''
})

// * Step Functions
const {
  headerButtonPreviousClicked,
  footerBtnDisabledUpdater,
  footerButtonClick
} = inject('changingStepsFunctions', {
  headerButtonPreviousClicked: ref(false),
  footerBtnDisabledUpdater: () => {},
  footerButtonClick: () => {}
})

watch(headerButtonPreviousClicked, () => {
  store.commit('payment/setPaymentGateway', '')
})

const paymentTypes = {
  onSite: markRaw(PaymentOnSite),
  stripe: markRaw(PaymentStripe),
  payPal: markRaw(PaymentPayPal),
  razorpay: markRaw(PaymentCommon),
  mollie: markRaw(PaymentCommon),
  wc: markRaw(PaymentWc),
  square: markRaw(PaymentSquare),
  barion: markRaw(PaymentCommon),
}

let ready = computed(() => store.getters['getReady'])

let loading = computed(() => store.getters['getLoading'])

let bookableType = computed(() => store.getters['bookableType/getType'])

let paymentGateway = computed(() => {
  if (!store.getters['payment/getPaymentGateway']) {
    store.commit('payment/setPaymentGateway', amSettings.value.payments.defaultPaymentMethod)
  }
  return store.getters['payment/getPaymentGateway']
})

let hasDeposit = computed(() => {
  return ready.value ? props.selectedItem.depositPayment !== 'disabled' : false
})

provide('hasDeposit', hasDeposit)

let fullAmount = computed(() => {
  return ready.value ? props.selectedItem.fullPayment : false
})

// true = pay full amount
let paymentDeposit = computed({
  get: () => store.getters['payment/getPaymentDeposit'],
  set: (value) => store.commit('payment/setPaymentDeposit', value)
})

let mandatoryOnSitePayment = ref(false)

let paymentSentence = computed(() => {
  let sentence = ''
  if (paymentGateway.value === 'onSite' && !mandatoryOnSitePayment.value) {
    sentence = amLabels.payment_onsite_sentence
  } else if (paymentGateway.value === 'mollie' || paymentGateway.value === 'wc' || paymentGateway.value === 'barion') {
    sentence = amLabels.payment_wc_mollie_sentence
  }

  return sentence
})

let refOnSitePayment = ref(null)

function callPaymentError (msg) {
  if (store.getters['payment/getOnSitePayment']) {
    store.commit('payment/setOnSitePayment', false)
    store.commit('payment/setPaymentGateway', 'onSite')
    mandatoryOnSitePayment.value = true
    refOnSitePayment.value.continueWithBooking()
  }
  store.commit('payment/setError', msg)
}

function callPaymentAbandoned () {
  store.commit('setLoading', false)
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
        razorpay: false,
        square: false,
        barion: false,
      }
    }

    let entity = props.selectedItem

    let entityPayments = entity.settings ? JSON.parse(entity.settings)['payments'] : null

    let payments = !entityPayments ? {
      onSite: amSettings.value.payments.onSite,
      stripe: amSettings.value.payments.stripe.enabled,
      payPal: amSettings.value.payments.payPal.enabled,
      wc: amSettings.value.payments.wc.enabled,
      mollie: amSettings.value.payments.mollie.enabled,
      razorpay: amSettings.value.payments.razorpay.enabled,
      square: settings.payments.square.enabled,
      barion: settings.payments.barion.enabled,
    } : {
      onSite: 'onSite' in entityPayments ? entityPayments.onSite && amSettings.value.payments.onSite : amSettings.value.payments.onSite,
      stripe: 'stripe' in entityPayments ? entityPayments.stripe.enabled && amSettings.value.payments.stripe.enabled : amSettings.value.payments.stripe.enabled,
      payPal: 'payPal' in entityPayments ? entityPayments.payPal.enabled && amSettings.value.payments.payPal.enabled : amSettings.value.payments.payPal.enabled,
      wc: 'wc' in entityPayments ? (!('enabled' in entityPayments.wc) || entityPayments.wc.enabled) && amSettings.value.payments.wc.enabled : amSettings.value.payments.wc.enabled,
      mollie: 'mollie' in entityPayments ? entityPayments.mollie.enabled && amSettings.value.payments.mollie.enabled : amSettings.value.payments.mollie.enabled,
      razorpay: 'razorpay' in entityPayments ? entityPayments.razorpay.enabled && amSettings.value.payments.razorpay.enabled : amSettings.value.payments.razorpay.enabled,
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
        onSite: amSettings.value.payments.onSite,
        stripe: amSettings.value.payments.stripe.enabled,
        payPal: amSettings.value.payments.payPal.enabled,
        wc: amSettings.value.payments.wc.enabled,
        mollie: amSettings.value.payments.mollie.enabled,
        square: settings.payments.square.enabled,
        razorpay: amSettings.value.payments.razorpay.enabled,
        barion: settings.payments.barion.enabled,
      }
    }

    if (amSettings.value.payments.defaultPaymentMethod && payments[amSettings.value.payments.defaultPaymentMethod]) {
      store.commit('payment/setPaymentGateway', amSettings.value.payments.defaultPaymentMethod)

      footerBtnDisabledUpdater(false)
    } else {
      if (Object.keys(payments).filter(item => payments[item]).length === 1) {
        for (let key in payments) {
          if (payments[key]) {
            store.commit('payment/setPaymentGateway', key)

            footerBtnDisabledUpdater(false)
          }
        }
      }
    }

    return payments
  }

  return {}
})

let paymentList = computed(() => {
  let arr = []
  Object.keys(availablePayments.value).forEach(a => {
    if (availablePayments.value[a]) {
      arr.push(a)
    }
  })

  return arr
})
function setPaymentGateway (gateway) {
  store.commit('payment/setPaymentGateway', gateway)

  footerBtnDisabledUpdater(false)
}

function setOnSitePayment (isMandatory) {
  if (amSettings.value.payments.wc.enabled ? amSettings.value.payments.wc.onSiteIfFree : true) {
    if (isMandatory) {
      store.commit('payment/setPaymentGateway', 'onSite')
    }

    mandatoryOnSitePayment.value = isMandatory
  }
}

let paymentRef = ref(null)
provide('paymentRef', paymentRef)


onMounted(() => {
  store.commit('payment/setError', '')
  store.commit('setLoading', false)

  let price = usePrepaidPrice(store)

  if (price === 0) {
    if (amSettings.value.payments.wc.enabled === true && !amSettings.value.payments.wc.onSiteIfFree) {
      store.commit('payment/setPaymentGateway', 'wc')
    } else {
      store.commit('payment/setPaymentGateway', 'onSite')
    }

    footerButtonClick()
  }

  useAction(store, {}, 'InitiateCheckout', bookableType.value, null, null)
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
    '--am-c-ps-primary-op06': useColorTransparency(amColors.value.colorPrimary, 0.06),
    '--am-w-ps-card': `${100 / paymentList.value.length}%`
  }
})

function getPaymentBtnString (key) {
  switch(key) {
    case 'square':
      return amLabels['square']
    case 'onSite':
      return amLabels['on_site']
    case 'payPal':
      return amLabels['pay_pal']
    case 'stripe':
      return amLabels['stripe']
    case 'razorpay':
      return amLabels['razorpay']
    case 'mollie':
      return amLabels['mollie']
    case 'wc':
      return amLabels['on_line']
    case 'barion':
      return amLabels['barion']
    default:
      return ''
  }
}

// * Responsive
let cWidth = inject('containerWidth', ref(0))
let dWidth = inject('dialogWidth', ref(0))
let responsiveClass = computed(() => {
  return useResponsiveClass(props.inDialog ? dWidth.value : cWidth.value)
})
</script>

<script>
export default {
  name: "PaymentPage"
}
</script>

<style lang="scss">
.amelia-v2-booking #amelia-container {

  .am-payments {
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

    &__heading {
      font-size: 15px;
      font-style: normal;
      font-weight: 500;
      line-height: 1.6;
      color: var(--am-c-ps-text);
      margin-bottom: 4px;
      animation: 600ms cubic-bezier(.45,1,.4,1.2) #{100}ms am-animation-slide-up;
      animation-fill-mode: both;
    }

    &__price {
      //border: 1px solid var(--am-c-ps-text-op20);
      //border-radius: 8px;
      //padding: 16px;
      //margin-bottom: 16px;
    }

    &__sentence {
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

    &__full {
      color: var(--am-c-ps-text);
      border: 1px solid var(--am-c-ps-text-op25);
      border-radius: 8px;
      box-shadow: 0 1px 1px var(--am-c-ps-text-op06);
      box-sizing: border-box;
      padding: 12px;
      margin: 16px 0 0;

      &-checked {
        border: 1px solid var(--am-c-ps-primary);
        background: var(--am-c-ps-primary-op10);

        &:after {
          transform: rotate(45deg) scaleY(1);
        }
      }

      .el-checkbox {
        display: flex;
        align-items: center;

        &__input {
          height: 32px !important;
          line-height: 32px;
          align-items: center;
        }

        &__label {
          line-height: 32px;
          align-items: center;
        }
      }
    }

    &__error {
      animation: 600ms cubic-bezier(.45,1,.4,1.2) #{100}ms am-animation-slide-up;
      animation-fill-mode: both;
      margin-bottom: 10px;
    }

    &__method {
      margin-top: 16px;

      &-heading {
        font-size: 18px;
        font-weight: 500;
        line-height: 1.55555;
        color: var(--am-c-ps-text);
        margin-bottom: 8px;
      }

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
        width: 108px;
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

        .barion {
          width: 50px;
        }

        div {
          p {
            font-weight: 500;
            font-size: 15px;
            line-height: 24px;
            color: var(--am-c-ps-text);
            margin: 0;
            text-align: center;
            word-break: break-word;
          }
          span {
            font-size: 12px;
            font-weight: 400;
            line-height: 1.66666;
            color: var(--am-c-ps-text-op50);
          }
        }

        &__selected {
          background: var(--am-c-ps-primary-op10);
          border: 1px solid var(--am-c-ps-primary);
          box-sizing: border-box;
          box-shadow: 0 1px 1px var(--am-c-ps-primary-op06);
        }

        &-4 {
          width: calc(25% - 6px);

          &.am-rw {
            &-480 {
              width: calc(50% - 6px);
            }
          }
        }

        &-3 {
          width: calc(33.333333% - 6px);

          &.am-rw {
            &-420 {
              width: calc(50% - 6px);
            }
          }
        }

        &-2 {
          width: calc(50% - 6px);

          &.am-rw {
            &-420 {
              width: calc(50% - 6px);
            }
          }
        }
      }
    }
    // pm - payment method
    &__pm {
      margin-top: 24px;
    }

    .el-checkbox__label {
      font-size: 15px;
    }
  }
}
</style>
