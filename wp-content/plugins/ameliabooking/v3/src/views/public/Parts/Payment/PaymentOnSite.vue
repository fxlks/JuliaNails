<template>
  <div class="am-fs__payment_default">
    <div
      v-if="settings.general.googleRecaptcha.enabled && props.showRecaptcha"
      :id="'recaptcha-' + shortcodeData.counter"
      class="am-recaptcha-holder"
    >
      <vue-recaptcha
        ref="recaptchaRef"
        :size="settings.general.googleRecaptcha.invisible ? 'invisible' : null"
        :load-recaptcha-script="true"
        :sitekey="settings.general.googleRecaptcha.siteKey"
        @verify="onRecaptchaVerify"
        @expired="onRecaptchaExpired"
      >
      </vue-recaptcha>
    </div>
  </div>
</template>

<script setup>
import {useStore} from "vuex";
import {inject, watchEffect, ref} from "vue";
import {VueRecaptcha} from "vue-recaptcha";
import {settings} from "../../../../plugins/settings";
import {
  useBookingData,
  useCreateBooking,
  useCreateBookingSuccess,
  useCreateBookingError,
  getErrorMessage,
} from "../../../../assets/js/public/booking.js";

let props = defineProps({
  instantBooking: {
    type: Boolean,
    default: false
  },
  showRecaptcha: {
    type: Boolean,
    default: true
  }
})

const store = useStore()

const emits = defineEmits(['payment-error'])

const amLabels = inject('labels')

const shortcodeData = inject('shortcodeData')

/**************
 * Navigation *
 *************/

const { nextStep, footerButtonReset, footerButtonClicked } = inject('changingStepsFunctions', {
  nextStep: () => {},
  footerButtonReset: () => {},
  footerButtonClicked: {
    value: false
  }
})

defineExpose({
  continueWithBooking
})

watchEffect(() => {
  if (footerButtonClicked.value && !props.instantBooking) {
    if (!store.getters['booking/getCouponValidated']) {
      footerButtonReset()
      emits('payment-error', amLabels.coupon_mandatory)
    } else {
      continueWithBooking()
    }
  }
})


/*************
 * Recaptcha *
 ************/

let recaptchaRef = ref(null)

let recaptchaValid = ref(false)

let recaptchaResponse = ref(null)

function onRecaptchaExpired () {
  recaptchaValid.value = false

  emits('payment-error', amLabels.recaptcha_error)
}

function onRecaptchaVerify (response) {
  recaptchaValid.value = true

  recaptchaResponse.value = response

  if (settings.general.googleRecaptcha.invisible) {
    onSiteBooking(
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


/***********
 * Payment *
 **********/

function onSiteBooking (bookingData) {
  if (settings.general.googleRecaptcha.enabled && props.showRecaptcha && !settings.general.googleRecaptcha.invisible && !recaptchaValid.value) {
    emits('payment-error', amLabels.recaptcha_error)

    return false
  }

  useCreateBooking(
    store,
    bookingData,
    response => {
      useCreateBookingSuccess(store, response, () => {
        nextStep()
      })
    },
    error => {
      console.log(error)

      useCreateBookingError(
        store,
        error.response.data,
        () => {
          if (settings.general.googleRecaptcha.enabled && props.showRecaptcha && settings.general.googleRecaptcha.invisible) {
            recaptchaRef.value.reset()
          }

          emits('payment-error', getErrorMessage())
        }
      )
    }
  )
}

function continueWithBooking () {
  footerButtonReset()

  let bookingData = useBookingData(
    store,
    null,
    false,
    {},
    recaptchaResponse.value
  )

  store.commit('booking/setLoading', true)

  if (settings.general.googleRecaptcha.enabled && props.showRecaptcha) {
    if (settings.general.googleRecaptcha.invisible) {
      recaptchaRef.value.execute()
    } else if (!recaptchaValid.value) {
      emits('payment-error', amLabels.recaptcha_error)

      store.commit('booking/setLoading', false)
    } else {
      onSiteBooking(bookingData)
    }
  } else {
    onSiteBooking(bookingData)
  }
}
</script>

<script>
export default {
  name: 'PaymentOnSite'
}
</script>

<style lang="scss">
.amelia-v2-booking {
  #amelia-container {
    .am-fs__payment_default {
      .am-recaptcha-holder {
        height: 84px;
        position: relative;

        & > div > div {
          position: absolute !important;
        }
      }
      &-btn {
      }
    }
  }
}
</style>
