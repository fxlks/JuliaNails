<template>
  <div
    class="am-payments"
    :class="[props.componentClass, props.globalClass]"
    :style="cssVars"
  >
    <div class="am-payments__heading">
      <span class="am-payments__heading-main">
        {{ props.customizedLabels.summary }}
      </span>
    </div>

    <component
      :is="componentTypes[bookableType]"
      :payment-gateway="paymentGateway"
      :selected-item="props.selectedItem"
      :customized-labels="props.customizedLabels"
    ></component>

    <AmCheckbox
      v-if="paymentGateway !== 'onSite' && features.depositPayment"
      v-model="paymentDeposit"
      class="am-payments__full"
      :label="props.customizedLabels.full_amount_consent"
      :class="{'am-payments__full-checked': paymentDeposit}"
    >
    </AmCheckbox>

    <div v-if="!licence.isLite && !licence.isStarter" class="am-payments__method">
      <p class="am-payments__method-heading">
        {{ props.customizedLabels.payment_method }}
      </p>
      <div
        class="am-payments__method-cards"
        :class="{'am-payments__method-cards-wrap': wrapCards}"
      >
        <div
          v-for="(available, gateway) in availablePayments"
          :key="gateway"
          class="am-payments__method-button"
          :class="[{'am-payments__method-button__selected' : paymentGateway === gateway }, responsiveClass, `am-payments__method-button-${paymentList.length}`]"
          @click="setPaymentGateway(gateway)"
        >
          <img
            :src="`${baseUrls.wpAmeliaPluginURL}/v3/src/assets/img/icons/${gateway === 'mollie' ? 'stripe' : gateway}.svg`"
            :class="gateway"
          >
          <div>
            <p>
              {{ gateway === 'onSite' ? props.customizedLabels.on_site : paymentsBtnText.filter(item => item.key === gateway)[0].text }}
            </p>
          </div>
        </div>
      </div>
    </div>
    <div class="am-payments__sentence">
      <p>
        {{ paymentSentence }}
      </p>
    </div>
  </div>
</template>

<script setup>
// * _components
import AmCheckbox from "../../../../_components/checkbox/AmCheckbox.vue";

// * Parts
import EventPaymentInfo from "../Events/common/EventPayment/parts/EventPaymentInfo.vue";

// * Import from Vue
import {
  computed,
  inject,
  markRaw, ref
} from "vue";

// * Composables
import { useColorTransparency } from "../../../../../assets/js/common/colorManipulation";
import { useResponsiveClass } from "../../../../../assets/js/common/responsive";

// * Components Properties
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
  customizedLabels: {
    type: Object,
    required: true
  }
})

// * Base Urls
const baseUrls = inject('baseUrls')

// * Plugin Licence
let licence = inject('licence')

// * Features
let features = inject('features')

// * Bookable type
let bookableType = inject('bookableType')

// * Payment Part
const componentTypes = {
  event: markRaw(EventPaymentInfo),
}

let paymentDeposit = ref(false)

const availablePayments = {
  onSite: true,
  stripe: true,
  payPal: true,
  razorpay: true
}

let paymentList = computed(() => {
  let arr = []
  Object.keys(availablePayments).forEach(a => {
    if (availablePayments[a]) {
      arr.push(a)
    }
  })

  return arr
})

let paymentGateway = ref('onSite')

function setPaymentGateway (gateway) {
  paymentGateway.value = gateway
}

let paymentsBtnText = []

Object.keys(availablePayments).forEach(key => {
  paymentsBtnText.push({key, text:getPaymentBtnString(key)})
})

function getPaymentBtnString (key) {
  switch(key) {
    case 'onSite':
      return props.customizedLabels['on_site']
    case 'payPal':
      return props.customizedLabels['payment_paypal']
    case 'stripe':
      return props.customizedLabels['stripe']
    case 'razorpay':
      return props.customizedLabels['razorpay']
    case 'mollie':
      return props.customizedLabels['on_line']
    default:
      return ''
  }
}

let paymentSentence = computed(() => {
  if (paymentGateway.value === 'onSite') {
    return props.customizedLabels.payment_onsite_sentence
  }

  if (paymentGateway.value === 'mollie' || paymentGateway.value === 'wc') {
    return props.customizedLabels.payment_wc_mollie_sentence
  }

  return ''
})

// * Responsive
let cWidth = inject('containerWidth')
let responsiveClass = computed(() => {
  return useResponsiveClass(cWidth.value)
})

let wrapCards = computed(() => cWidth.value < 450 || (cWidth.value > 560 && (cWidth.value - 240 < 450)))

// * Fonts
let amFonts = inject('amFonts')

// * Colors
let amColors = inject('amColors')

// * Css Variables
const cssVars = computed(() => {
  return  {
    '--am-font-family': amFonts.value.fontFamily,
    '--am-c-ps-price-bgr': useColorTransparency(amColors.value.colorBtnPrim, 0.05),
    '--am-c-ps-total-price': amColors.value.colorBtnPrim,
    '--am-c-ps-text': amColors.value.colorMainText,
    '--am-c-ps-border': amColors.value.colorInpBorder,
    '--am-c-ps-text-op06': useColorTransparency(amColors.value.colorMainText, 0.06),
    '--am-c-ps-text-op20': useColorTransparency(amColors.value.colorMainText, 0.2),
    '--am-c-ps-text-op25': useColorTransparency(amColors.value.colorMainText, 0.25),
    '--am-c-ps-text-op50': useColorTransparency(amColors.value.colorMainText, 0.5),
    '--am-c-ps-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-c-ps-primary': amColors.value.colorPrimary,
    '--am-c-ps-primary-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),
    '--am-c-ps-primary-op06': useColorTransparency(amColors.value.colorPrimary, 0.06)
  }
})
</script>

<style lang="scss">
#amelia-app-backend-new #amelia-container {

  .am-payments {
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

    &__price {}

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
        justify-items: center;

        & > div {
          margin: 0 6px 6px 0;
        }

        &-wrap {
          flex-wrap: wrap;
        }
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
            word-break: break-all;
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
