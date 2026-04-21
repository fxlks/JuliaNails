<template>
  <div>
    <div v-show="squareLoading" class="am-fs__square-loading" :style="cssVars">
      <!-- Skeleton -->
      <el-skeleton animated>
        <el-skeleton-item />
      </el-skeleton>
      <!-- /Skeleton -->
    </div>
    <!-- Credit Card via Square-->
    <div v-show="!squareLoading" class="am-fs__payment-square" :style="cssVars">
      <div class="am-fs__payment-square__google-pay">
        <div id="google-pay-button"/>
      </div>

      <div v-if="applePayReady" class="am-fs__payment-square__apple-pay">
        <div id="apple-pay-button"/>
      </div>

      <div class="am-fs__payment-divider">
        <span class="am-divider-text">{{ amLabels.payment_or_pay_with_card }}</span>
      </div>

      <div id="payment-status-container"></div>
      <div id="card-container"></div>
    </div>
    <!-- /Credit Card via Square-->
  </div>

</template>

<script setup>
import {computed, inject, onMounted, ref, watchEffect, nextTick, watch} from 'vue'
import {
  getErrorMessage,
  useBookingData,
  useCreateBooking,
  useCreateBookingError,
  useCreateBookingSuccess,
} from '../../../../../assets/js/public/booking.js'
import {useColorTransparency} from '../../../../../assets/js/common/colorManipulation.js'
import {useStore} from "vuex"
import httpClient from "../../../../../plugins/axios"
import {useScrollTo} from "../../../../../assets/js/common/scrollElements";

// * Global settings
const amSettings = inject('settings')
const store = useStore()

// * Labels
const amLabels = inject('amLabels')

// * Colors
let amColors = inject('amColors')

// * Css variables
let cssVars = computed(() => {
  return {
    '--am-c-pay-text': amColors.value.colorMainText,
    '--am-c-pay-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6)
  }
})

// * Components Emits
const emits = defineEmits(['payment-error'])

const { nextStep, footerButtonReset, footerButtonClicked } = inject('changingStepsFunctions', {
  nextStep: () => {},
  footerButtonReset: () => {},
  footerButtonClicked: {
    value: false
  }
})

const cardInstance = ref(null)

async function continueWithBooking () {
  footerButtonReset()
  store.commit('setLoading', true)

  if (!cardInstance.value) {
    store.commit('setLoading', true)
  }

  const totalAmount = await payingNow()
  const token = await squareTokenize(cardInstance.value, totalAmount.formattedAmount)
  if (!token) {
    store.commit('setLoading', false)
    return
  }
  await createSquarePayment(token)
}

// * Watching when footer button was clicked
watchEffect(() => {
  if (footerButtonClicked.value) {
    if (!store.getters['booking/getCouponValidated']) {
      footerButtonReset()
      emits('payment-error', amLabels.value.coupon_mandatory)
    } else {
      continueWithBooking()
    }
  }
})

const cardReady = ref(false)
const googlePayReady = ref(false)
const squareLoading = computed(() => !(cardReady.value && googlePayReady.value))
const applePayReady = ref(true)
const paymentTotalAmount = ref(null)
let paymentRequest = null
const walletInstance = ref({googlePay: null, applePay: null})

// * Watch coupon changes and payment deposit changes to update paymentRequest amount
// Apple Pay - No async operations can be called between the user gesture (click) and tokenize().
watch(
    [() => store.getters['coupon/getCoupon'], () => store.getters['payment/getPaymentDeposit']],
    async (newValues, oldValues) => {
      const [newCoupon, newDeposit] = newValues || []
      const [oldCoupon, oldDeposit] = oldValues || []

      if (paymentRequest && ((newCoupon.deduction || newCoupon.discount) || newDeposit !== oldDeposit)) {
        // Wait for next tick to ensure DOM is updated
        await nextTick()
        paymentRequest = await buildPaymentRequest()
        const initWallet = async (method, target, onError) => {
          try {
            walletInstance.value[target] = await payments[method](paymentRequest)
          } catch (err) {
            console.log(err)
            if (onError) onError()
          }
        }

        await initWallet('googlePay', 'googlePay')
        await initWallet('applePay', 'applePay', () => {
          applePayReady.value = false
        })
      }
    }
)

async function getAmountToPay() {
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

  const totalPriceParts = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: checkoutPaymentData.currency,
  }).formatToParts(checkoutPaymentData.amount)

  const IntegerPart = totalPriceParts.find((part) => part.type === 'integer')?.value || ''
  const fractionPart = totalPriceParts.find((part) => part.type === 'fraction')?.value || ''
  const decimalPart = totalPriceParts.find((part) => part.type === 'decimal')?.value || ''
  const formattedAmount = `${IntegerPart}${decimalPart}${fractionPart}`
  paymentTotalAmount.value = formattedAmount

  return {
    formattedAmount,
    rawAmount: checkoutPaymentData.amount,
    countryCode: checkoutPaymentData.countryCode,
  }
}

async function payingNow() {
  return await getAmountToPay()
}

onMounted(async () => {
// Defer mounting logic until DOM is visible
  cardReady.value = false
  googlePayReady.value = false

  await nextTick()
  await initSquarePayment()
})

const squareLocationId = amSettings.payments.square.locationId
const squareClientId = amSettings.payments.square.testMode ? amSettings.payments.square.clientTestId : amSettings.payments.square.clientLiveId

const bookingData =  useBookingData(
    store,
    null,
    true,
    {},
    null
)

let paymentStepRef = inject('paymentRef')


const payments = window.Square.payments(squareClientId, squareLocationId)

async function buildPaymentRequest () {
  try {
    const paymentInfo = await payingNow()
    if (!paymentInfo) return null
    return payments.paymentRequest({
      countryCode: paymentInfo.countryCode,
      currencyCode: bookingData.data.payment.currency,
      total: {
        amount: paymentInfo.formattedAmount.toString(),
        label: 'Total'
      }
    })
  } catch (e) {
    console.log(e)
    return null
  }
}
async function setupDigitalWallet ({ buttonId, type, readyRef }) {
  try {
    const btnEl = document.getElementById(buttonId)
    if (!btnEl) return
    if (type === 'googlePay') {
      btnEl.innerHTML = ''
    }

    if (!paymentRequest) return
    walletInstance.value[type] = await payments[type](paymentRequest)

    // Google Pay needs to render its own button via attach
    if (type === 'googlePay') {
      await walletInstance.value[type].attach(`#${buttonId}`)
    }
    readyRef.value = true

    btnEl.addEventListener('click', async () => {
      store.commit('setLoading', true)

      const token = await squareTokenize(walletInstance.value[type], paymentTotalAmount.value)
      if (!token) {
        store.commit('setLoading', false)
        return
      }
      await createSquarePayment(token)
    })
  } catch (e) {
    console.log(e)
    if (type === 'applePay') {
      readyRef.value = false
    }
  }
}

async function initSquarePayment() {
  const squareCardStyle = {
    '.input-container': {
      borderColor: '#d9d9d9',
      borderRadius: '6px',
    }
  }

  try {
    const cardContainer = document.getElementById('card-container')
    if (cardContainer) {
      cardContainer.innerHTML = ''
    }
    const card = await payments.card({
      style: squareCardStyle
    })
    await card.attach('#card-container')
    cardInstance.value = card
    cardReady.value = true
  } catch (e) {
    const statusContainer = document.getElementById('payment-status-container')
    console.error(e)
    store.commit('setLoading', false)
    if (statusContainer) {
      statusContainer.className = 'missing-credentials'
      statusContainer.style.visibility = 'visible'
    }
  }

  paymentRequest = await buildPaymentRequest()
  await setupDigitalWallet({ buttonId: 'google-pay-button', type: 'googlePay', readyRef: googlePayReady })
  await setupDigitalWallet({ buttonId: 'apple-pay-button', type: 'applePay', readyRef: applePayReady })
}

const squareTokenize = async (payments, totalAmount) => {
  try {
    const {token, status, errors} = await payments.tokenize({
          amount: totalAmount.toString(),
          billingContact: {
            familyName: bookingData.data.bookings[0].customer.lastName,
            givenName: bookingData.data.bookings[0].customer.firstName,
            email: bookingData.data.bookings[0].customer.email,
            phone: bookingData.data.bookings[0].customer.phone,
          },
          customerInitiated: true,
          sellerKeyedIn: false,
          currencyCode: bookingData.data.payment.currency,
          intent: 'CHARGE',
        }
    )

    if (status === 'OK') {
      return token;
    } else if (status === 'Invalid' && errors.length > 0) {
      const messages = errors.map((err) => err.message)
      emits('payment-error',  messages.join(', '))
      useScrollTo(paymentStepRef.value, paymentStepRef.value, 20, 300)
      return ''
    }
  } catch (e) {
    console.log(e)
  }
}

const createSquarePayment = async (token) => {
  if (!token) {
    return
  }
  useCreateBooking(
      store,
      useBookingData(
          store,
          null,
          false,
          {
            locationId: squareLocationId,
            sourceId: token,
            idempotencyKey: window.crypto.randomUUID(),
          },
          null
      ),
      function (response) {
        successBooking(response)
      },
      (response) => {
        errorBooking(response)
      }
  )
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
</script>

<script>
export default {
  name: 'PaymentSquare'
}
</script>

<style lang="scss">
.amelia-v2-booking {
  #amelia-container {
    .am-fs__square-loading {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      width: 100%;

      .el-skeleton {
        display: flex;
        align-items: center;
        flex-direction: column;
        gap: 10px;
        width: 100%;

        &__item {
          width: 100%;
          height: 40px;
        }
      }
    }

    .am-fs__payment-square {
      display: flex;
      flex-direction: column;
      gap: 6px;

      span {
        display: inline-flex;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.42857;
        color: #6772E5;
      }

      &__google-pay {
        #google-pay-button {
          width: 100%;
          div {
            button {
              width: 100%;
              display: flex;
              justify-content: center;
              align-items: center;
            }
          }
        }
      }

      // Apple Pay button styling
      &__apple-pay {
        #apple-pay-button {
          height: 40px;
          width: 100%;
          font-size: 15px;
          display: inline-block;
          -webkit-appearance: -apple-pay-button;
          -apple-pay-button-type: plain;
          -apple-pay-button-style: black;
        }
      }

      #card-container {
        .sq-card-iframe-container {
          border: 1px solid #d9d9d9;
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