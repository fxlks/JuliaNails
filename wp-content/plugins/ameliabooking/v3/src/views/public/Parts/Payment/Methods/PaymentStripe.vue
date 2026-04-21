<template>
  <div class="am-fs__payment-stripe" :style="cssVars">
    <div v-if="supportsExpressCheckout" class="am-fs__payment-stripe__express-checkout">
      <div :id="'am-stripe-prb-' + shortcodeData.counter" class="am-stripe-prb"></div>
    </div>

    <div v-if="paymentRequestAvailable" class="am-fs__payment-divider">
      <span class="am-divider-text">{{ amLabels.payment_or_pay_with_card }}</span>
    </div>

    <div v-if="amSettings.payments.stripe.address" class="am-fs__payment-stripe__card">
      <div :id="'am-stripe-address-' + shortcodeData.counter" class="am-stripe-address"></div>
    </div>
    <div class="am-fs__payment-stripe__card">
      <div>
        <p>
          {{ amLabels.card_number_colon }}:
        </p>
        <div :id="'am-stripe-cn-' + shortcodeData.counter" class="am-stripe-cn"></div>
      </div>
      <div>
        <div>
          <p>
            {{ amLabels.expires_date_colon }}:
          </p>
          <div :id="'am-stripe-ed-' + shortcodeData.counter" class="am-stripe-ed"></div>
        </div>
        <div>
          <p>
            CVC:
          </p>
          <div :id="'am-stripe-cvc-' + shortcodeData.counter" class="am-stripe-cvc"></div>
        </div>
      </div>
    </div>
    <div class="am-fs__payment-stripe__policy">
      <p>
        {{ amLabels.payment_protected_policy }}
      </p>
      <img :src="baseUrls.wpAmeliaPluginURL+'/v3/src/assets/img/icons/stripe.svg'" alt="Stripe policy">
      <span>
        {{ amLabels.stripe }}
      </span>
    </div>

    <div
        v-if="amSettings.general.googleRecaptcha.enabled"
        :id="'recaptcha-' + shortcodeData.counter"
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
</template>

<script setup>
import {computed, onMounted, inject, watchEffect, ref, watch, nextTick} from 'vue'
import {
  usePaymentError,
  useBookingData,
  useCreateBooking,
  useCreateBookingSuccess,
  useCreateBookingError,
  getErrorMessage
} from '../../../../../assets/js/public/booking.js'
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation.js'

// * Import from Vuex
import { useStore } from 'vuex'
import {useScrollTo} from "../../../../../assets/js/common/scrollElements";
import { VueRecaptcha } from "vue-recaptcha";
import httpClient from "../../../../../plugins/axios"

const store = useStore()

// * Global settings
const amSettings = inject('settings')

// * local language short code
const localLanguage = inject('localLanguage')

// * Labels
const amLabels = inject('amLabels')

// * Base Urls
const baseUrls = inject('baseUrls')

const shortcodeData = inject('shortcodeData')

// * Step Functions
const {
  nextStep,
  footerButtonReset,
  footerButtonClicked
} = inject('changingStepsFunctions', {
  nextStep: () => {},
  footerButtonReset: () => {},
  footerButtonClicked: {
    value: false
  }
})

// * Components Emits
const emits = defineEmits(['payment-error'])

/*************
 * Recaptcha *
 ************/

let recaptchaRef = ref(null)

let recaptchaValid = ref(false)

let recaptchaResponse = ref(null)

// * Express Checkout
let paymentRequest = null
let paymentRequestButton = null
let supportsExpressCheckout = ref(false)
let paymentRequestAvailable = ref(false)

async function initializeExpressCheckout(update = false) {
  let checkoutPaymentData = null

  await httpClient.post(
    '/payments/amount',
    useBookingData(
      store,
      null,
      true,
      {},
      null
    )['data']
  ).then((response) => {
    checkoutPaymentData = response.data.data
  }).catch(e => {
    const message = e?.response?.data?.message || e.message || 'Unknown error'
    emits('payment-error', message)
  })

  if (!update) {
    stripePaymentInit(
      checkoutPaymentData?.transfers?.accounts &&
      Object.keys(checkoutPaymentData.transfers.accounts).length === 1 &&
      checkoutPaymentData?.transfers?.method === 'direct'
        ? Object.keys(checkoutPaymentData.transfers.accounts)[0]
        : null
    )
  }

  if (!stripeObject) return

  supportsExpressCheckout.value = true

  const totalPriceParts = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: checkoutPaymentData.currency,
  }).formatToParts(checkoutPaymentData.amount)

  const IntegerPart = totalPriceParts.filter((part) => part.type === 'integer').map(part => part.value).join('')
  const fractionPart = totalPriceParts.find((part) => part.type === 'fraction')?.value || ''
  const totalPriceInMinorUnits = Number.parseInt(`${IntegerPart}${fractionPart}`)

  paymentRequest = stripeObject.paymentRequest({
    country: 'US',
    currency: checkoutPaymentData.currency.toLowerCase(),
    total: {
      label: 'total',
      amount: totalPriceInMinorUnits
    },
    requestPayerName: true,
    requestPayerEmail: true
  })

  paymentRequest.canMakePayment().then(function (result) {
    if (result) {
      paymentRequestAvailable.value = true

      paymentRequestButton = stripeObject.elements().create('paymentRequestButton', {
        paymentRequest: paymentRequest,
        style: {
          paymentRequestButton: {
            type: 'default',
            theme: 'dark',
            height: '40px',
          },
        },
      })

      paymentRequestButton.mount('#am-stripe-prb-' + shortcodeData.value.counter)

      paymentRequest.on('paymentmethod', handleExpressCheckout)
    } else {
      paymentRequestAvailable.value = false
    }
  }).catch(error => {
    console.error('Error checking payment capabilities:', error)
    paymentRequestAvailable.value = false
  })
}

async function handleExpressCheckout(event) {
  try {
    const { paymentMethod } = event
    const addressResult = amSettings.payments.stripe.address
        ? await address.getValue()
        : null

    useCreateBooking(
      store,
      useBookingData(
        store,
        null,
        false,
        {
          paymentMethodId: paymentMethod.id,
          address: addressResult ? addressResult.value : null,
        },
        recaptchaResponse.value
      ),
      function (response) {
        if (response.data.data.requiresAction) {
          stripePaymentActionRequired({
            ...response.data.data,
            expressCheckoutEvent: event
          })
          return
        }
        event.complete('success')
        successBooking(response)
      },
      (response) => {
        event.complete('fail')
        errorBooking(response)
      }
    )
  } catch (error) {
    event.complete('fail')
    emits('payment-error', error.message)
  }
}

function onRecaptchaExpired () {
  recaptchaValid.value = false

  emits('payment-error', amLabels.recaptcha_error)
}

function onRecaptchaVerify (response) {
  recaptchaValid.value = true

  recaptchaResponse.value = response

  if (amSettings.general.googleRecaptcha.invisible) {
    stripePaymentCreate(
      useBookingData(
        store,
        null,
        false,
        {},
        recaptchaResponse.value
      )
    )

    return false
  }
}


// * Payment Part
let stripeObject = null
// let stripeCard = null
let cardNum = null
let cardEd = null
let cardCvc = null

let address = null

// * Colors
let amColors = inject('amColors')
let amFonts = inject('amFonts')

function stripePaymentInit (accountId) {
  const options = {
    locale: localLanguage.value.replace('_', '-')
  }

  if (accountId) {
    options.stripeAccount = accountId
  }

  stripeObject = Stripe(
    amSettings.payments.stripe.testMode === false
      ? amSettings.payments.stripe.livePublishableKey
      : amSettings.payments.stripe.testPublishableKey, options
  )

  let elements = stripeObject.elements()

  // stripeCard = elements.create('card')
  // TODO - set unique ID with counter
  // stripeCard.mount('#am-stripe-element')

  let style = {
    base: {
      color: amColors.value.colorInpText,
      fontSize: '15px',
      fontFamily: amFonts.value.fontFamily
    }
  }

  cardNum = elements.create('cardNumber', {style})
  cardNum.mount('#am-stripe-cn-' + shortcodeData.value.counter)
  cardEd = elements.create('cardExpiry', {style})
  cardEd.mount('#am-stripe-ed-' + shortcodeData.value.counter)
  cardCvc = elements.create('cardCvc', {style})
  cardCvc.mount('#am-stripe-cvc-' + shortcodeData.value.counter)

  if (amSettings.payments.stripe.address) {
    address = elements.create('address', { mode: 'billing',  });
    address.mount('#am-stripe-address-' + shortcodeData.value.counter)
  }
}

async function stripePaymentCreate () {
  if (amSettings.general.googleRecaptcha.enabled && !amSettings.general.googleRecaptcha.invisible && !recaptchaValid.value) {
    emits('payment-error', amLabels.recaptcha_error)

    return false
  }

  let addressResult = null
  if (amSettings.payments.stripe.address && address) {
    addressResult = await address.getValue()
  }

  stripeObject.createPaymentMethod(
    'card',
    cardNum,
    {
      billing_details: {
        ...(addressResult ? addressResult.value : {})
      }
    }
  ).then(
    function (result) {
      if (stripeError(result, addressResult)) {
        store.commit('setLoading', false)
        return
      }

      useCreateBooking(
        store,
        useBookingData(
          store,
          null,
          false,
          {
            paymentMethodId: result.paymentMethod.id,
            address: addressResult ? addressResult.value : null
          },
          recaptchaResponse.value
        ),
        function (response) {
          if (response.data.data.requiresAction) {
            stripePaymentActionRequired(response.data.data)

            return
          }

          store.commit('setLoading', false)
          successBooking(response)
        },
        (response) => {
          store.commit('setLoading', false)
          errorBooking(response)
        }
      )


    }
  )
}

function stripePaymentActionRequired (response) {
  const { expressCheckoutEvent } = response || {}

  stripeObject.handleNextAction({
    clientSecret: response.paymentIntentClientSecret
  }).then(
    async function (result) {
      let addressResult = null
      if (amSettings.payments.stripe.address && address && !expressCheckoutEvent) {
        addressResult = await address.getValue()
      }

      if (stripeError(result, addressResult)) {
        if (expressCheckoutEvent) {
          expressCheckoutEvent.complete('fail')
        }
        return
      }

      useCreateBooking(
        store,
        useBookingData(
          store,
          null,
          false,
          {
            paymentIntentId: result.paymentIntent.id,
            address: addressResult ? addressResult.value : null
          },
          null
        ),
        function (response) {
          if (expressCheckoutEvent) {
            expressCheckoutEvent.complete('success')
          }
          successBooking(response)
        },
        function (error) {
          if (expressCheckoutEvent) {
            expressCheckoutEvent.complete('fail')
          }
          errorBooking(error)
        }
      )
    }
  ).catch((error) => {
    if (expressCheckoutEvent) {
      expressCheckoutEvent.complete('fail')
    }
    emits('payment-error', error.message || 'Authentication failed')
  })
}

function stripeError (result, addressResult) {
  let addressError = addressResult && !addressResult.complete
  if (result.error || addressError) {
    usePaymentError(
      store,
      function () {
        emits('payment-error', addressError ? amLabels.payment_address_error : result.error.message)
      }
    )
    return true
  }

  return false
}

function successBooking (response) {
  useCreateBookingSuccess(
    store,
    response,
    function () {
      nextStep()
    }
  )
}

function errorBooking (error) {
  useCreateBookingError(
    store,
    error.response.data,
    () => {
      emits('payment-error', getErrorMessage())
    }
  )
}

let paymentStepRef = inject('paymentRef')

function continueWithBooking () {
  footerButtonReset()

  store.commit('setLoading', true)

  if (amSettings.general.googleRecaptcha.enabled) {
    if (amSettings.general.googleRecaptcha.invisible) {
      recaptchaRef.value.execute()
    } else if (!recaptchaValid.value) {
      emits('payment-error', amLabels.value.recaptcha_error)
      store.commit('setLoading', false)
    } else {
      stripePaymentCreate()
    }
  } else {
    stripePaymentCreate()
  }
}

// * Watching when footer button was clicked
watchEffect(() => {
  if(footerButtonClicked.value) {
    if (!store.getters['coupon/getCouponValidated']) {
      footerButtonReset()
      useScrollTo(paymentStepRef.value, paymentStepRef.value, 20, 300)
      emits('payment-error', amLabels.value.coupon_mandatory)
    } else {
      continueWithBooking()
    }
  }
}, {flush: 'post'})

// * Watch coupon and payment deposit changes and update paymentRequest amount
watch(
  [() => store.getters['coupon/getCoupon'], () => store.getters['payment/getPaymentDeposit']],
  async (newValues, oldValues) => {
    const [newCoupon, newDeposit] = newValues || []
    const [oldCoupon, oldDeposit] = oldValues || []

    if (paymentRequest && ((newCoupon.deduction || newCoupon.discount) || newDeposit !== oldDeposit)) {
      if (paymentRequestButton) {
        paymentRequestButton.unmount();
        paymentRequestButton = null;
      }
      // Wait for next tick to ensure DOM is updated
      await nextTick()
      initializeExpressCheckout(true)
    }
  }
)

onMounted(() => {
  initializeExpressCheckout(false)
})

// * Css variables
let cssVars = computed(() => {
  return {
    '--am-c-pay-text': amColors.value.colorMainText,
    '--am-c-pay-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6)
  }
})
</script>

<script>
export default {
  name: 'PaymentStripe'
}
</script>

<style lang="scss">
.amelia-v2-booking {
  #amelia-container {
    $pay: am-fs__payment-stripe;
    .#{$pay} {
      .#{$pay}__policy {
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--am-c-pay-text-op60);
        margin: 20px 0 10px;

        p {
          display: inline-flex;
          font-size: 14px;
          font-weight: 500;
          line-height: 1.42857;
          color: var(--am-c-ps-text);
        }

        img {
          margin: 0 8px;
        }

        span {
          display: inline-flex;
          font-size: 14px;
          font-weight: 500;
          line-height: 1.42857;
          color: #6772E5;
        }
      }

      &__card, &__address {
        p {
          font-size: 15px;
          font-weight: 500;
          line-height: 1.33333;
          color: var(--am-c-pay-text);
          margin-bottom: 4px;
        }

        & > div:nth-child(2) {
          display: flex;
          gap: 16px;

          & > div:first-child {
            width: 60%
          }

          & > div:nth-child(2) {
            width: 40%
          }
        }

        .am-stripe-cn, .am-stripe-ed, .am-stripe-cvc {
          background-color: var(--am-c-inp-bgr);
          border: 1px solid var(--am-c-inp-border);
          border-radius: 6px;
          box-shadow: 0 2px 2px rgb(14 25 32 / 3%);
          box-sizing: border-box;
          padding: 12px;
        }
        .am-stripe-cn {
          margin-bottom: 16px;
        }

        .am-stripe-address {
          & > div {
            width: 100% !important;
          }
          margin-bottom: 16px;
        }
      }

      .am-recaptcha-holder {
        height: 84px;
        position: relative;

        & > div > div {
          position: absolute !important;
        }
      }

      .am-fs__payment-stripe__express-checkout {
        margin-bottom: 20px;

        .am-stripe-prb {
          margin-bottom: 10px;
        }

        p {
          color: var(--am-c-pay-text-op60);
          font-size: 14px;
        }
      }

      .am-fs__payment-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin: 16px 0;
        text-align: center;
      }

      .am-fs__payment-divider::before,
      .am-fs__payment-divider::after {
        content: "";
        flex-grow: 1;
        height: 1px;
        background-color: var(--am-c-pay-text-op60);
        margin: 0 8px;
      }

      .am-divider-text {
        font-size: 14px;
        color: var(--am-c-pay-text-op60);
        text-transform: uppercase;
        line-height: 1.33333;
        font-weight: 500;
      }
    }
  }
}
</style>
