<template>
  <div class="am-fs__payment_payPal" :style="cssVars">
  </div>
</template>

<script setup>
import {
  settings,
  ajaxUrl
} from '../../../../../plugins/settings.js'

// * Import from Vue
import {
  inject,
  computed,
  onMounted,
} from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Composables
import {
  usePaymentError,
  useBookingData,
  useCreateBooking,
  useCreateBookingSuccess,
  useCreateBookingError,
  getErrorMessage
} from '../../../../../assets/js/public/booking.js'

const store = useStore()

// * Labels
const amLabels = inject('amLabels')


// * Step Functions
const { nextStep } = inject('changingStepsFunctions', {
  nextStep: () => {}
})

// * Colors
// let amColors = inject('amColors')
//
let cssVars = computed(() => {
  return {
  }
})

// * Payment Part
let transactionReference = null

const shortcodeData = inject('shortcodeData')

function payPalPaymentInit () {
  const selector = '#am-paypal-element-' + shortcodeData.value.counter
  const el = document.getElementById('am-paypal-element-' + shortcodeData.value.counter)

  if (
    typeof window.paypal === 'undefined' ||
    typeof window.paypal.Buttons !== 'function' ||
    !window.paypal.FUNDING ||
    !el
  ) {
    setTimeout(payPalPaymentInit, 100)
    return
  }

  let btn
  try {
    btn = window.paypal.Buttons({
      fundingSource: window.paypal.FUNDING.PAYPAL,
      style: {
        color: 'gold',
        shape: 'rect',
        tagline: false,
        height: 40
      },

      onClick: function (data, actions) {
        if (store.getters['coupon/getCouponValidated']) {
          actions.resolve()
        } else {
          emits('payment-error', amLabels.coupon_mandatory)
          actions.reject()
        }
      },

      createOrder: function () {
        return fetch(ajaxUrl + '/payment/payPal', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          body: JSON.stringify(useBookingData(store, null, true, {}, null)['data'])
        })
          .then(r => {
            if (!r.ok) return Promise.reject(new Error('HTTP ' + r.status))
            return r.json()
          })
          .then(response => {
            transactionReference = response.data.transactionReference
            return response.data.paymentID
          })
          .catch(error => {
            payPalError(error)
          })
      },

      onApprove: function (data) {
        store.commit('setLoading', true)

        useCreateBooking(
          store,
          useBookingData(
            store,
            null,
            false,
            {
              transactionReference: transactionReference,
              PaymentId: data.orderID,
              PayerId: data.payerID
            },
            null
          ),
          successBooking,
          error => {
            useCreateBookingError(
              store,
              error.response.data,
              () => {
                emits('payment-error', getErrorMessage())
              }
            )
          }
        )
      },

      onCancel: function () {
        store.commit('setLoading', false)
      },

      onError: function (error) {
        payPalError(error)
      }
    })
  } catch (e) {
    setTimeout(payPalPaymentInit, 100)
    return
  }

  if (btn && btn.render) {
    btn.render(selector).catch(function (e) {})
  } else {
    setTimeout(payPalPaymentInit, 100)
  }
}

function payPalError(error) {
  let errorString = error.toString()

  let response = JSON.parse(
    JSON.stringify(
      JSON.parse(errorString.substring(errorString.indexOf('{'),
      errorString.lastIndexOf('}') + 1))
    )
  )

  if (typeof response === 'object' && response.hasOwnProperty('data')) {
    errorBooking(response)
  } else if (typeof response === 'object' && response.hasOwnProperty('message')) {
    usePaymentError(
      store,
      function () {
        emits('payment-error', response.message)
      }
    )
  } else {
    usePaymentError(
      store,
      function () {
        emits('payment-error', errorString)
      }
    )
  }
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

// * Components Emits
const emits = defineEmits(['payment-error'])

function errorBooking (error) {
  useCreateBookingError(
    store,
    error,
    () => {
      emits('payment-error', getErrorMessage())
    }
  )
}

onMounted(() => {
  payPalPaymentInit()
})
</script>

<script>
export default {
  name: 'PaymentPayPal'
}
</script>

<style lang="scss">
.amelia-v2-booking {
  #amelia-container {
    .am-fs__payment_payPal {
      height: 56px;
      .paypal-button {
        width: 160px;
        border-radius: 9px;
      }
    }
  }
}
</style>
