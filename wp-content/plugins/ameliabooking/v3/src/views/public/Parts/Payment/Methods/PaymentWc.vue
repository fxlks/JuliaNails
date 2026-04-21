<template>
  <div class="am-fs__payment_default">
  </div>
</template>

<script setup>
// * Import from Vue
import {
  // ref,
  inject,
  watchEffect
} from "vue";

// * Import form Vuex
import {
  useStore
} from "vuex";

// * Composables
import {
  getErrorMessage,
  useBookingData,
  useCreateBookingError
} from "../../../../../assets/js/public/booking.js";

import httpClient from "../../../../../plugins/axios";

// * Component props
let props = defineProps({
  instantBooking: {
    type: Boolean,
    default: false
  }
})

const store = useStore()

const shortcodeData = inject('shortcodeData')

let stepsArray = inject('stepsArray')

// ! treba da se prilagodi za services i packages
// let sidebarSteps = inject('sidebarSteps', ref([]))

/**************
 * Navigation *
 *************/

const {
  footerButtonReset,
  footerButtonClicked
} = inject('changingStepsFunctions', {
  footerButtonReset: () => {},
  footerButtonClicked: {
    value: false
  }
})

defineExpose({
  continueWithBooking
})

// * Labels
const amLabels = inject('labels')

watchEffect(() => {
  if (footerButtonClicked.value && !props.instantBooking) {
    if (!store.getters['coupon/getCouponValidated']) {
      footerButtonReset()
      emits('payment-error', amLabels.value.coupon_mandatory)
    } else {
      continueWithBooking()
    }
  }
})


/***********
 * Payment *
 **********/

function continueWithBooking () {
  footerButtonReset()

  let bookingData = useBookingData(
    store,
    {
      shortcode: shortcodeData.value,
      steps: stepsArray.value.map(i => i.name),
      // ! it must be refactored
      // sidebar: sidebarSteps.value.map(i => Object.assign({key: i.key, data: i.stepSelectedData})),
    },
    false,
    {},
    null
  )

  store.commit('setLoading', true)

  payment(
    '/payment/wc',
    bookingData,
    function (response) {
      window.location = response.data.data.cartUrl
    }
  )
}

const emits = defineEmits(['payment-error'])


function payment (endpoint, bookingData, success) {
  httpClient.post(
    endpoint,
    bookingData.data,
    bookingData.options
  ).then(success).catch(error => {
    useCreateBookingError(
      store,
      error.response.data,
      () => {
        emits('payment-error', getErrorMessage())
      }
    )}
  )
}
</script>

<script>
export default {
  name: 'PaymentWc'
}
</script>

<style lang="scss">
.amelia-v2-booking {
  #amelia-container {
    .am-fs__payment_default {
      &-btn {
      }
    }
  }
}
</style>
