<template>
  <div :class="props.globalClass" :style="cssVars">
    <div class="am-fs__payments">
      <div class="am-fs__payments-heading">
        <span class="am-fs__payments-heading-main">
          {{ labelsDisplay('summary') }}
        </span>
      </div>

      <div class="am-fs__payments-price">
        <component
          :is="componentTypes[bookableType]"
          :payment-gateway="paymentGateway"
        ></component>
      </div>

      <AmCheckbox
        v-if="paymentGateway !== 'onSite' && features.depositPayment"
        v-model="paymentDeposit"
        class="am-fs__payments-full"
        :label="labelsDisplay('full_amount_consent')"
        :class="{ 'am-fs__payments-full-checked': paymentDeposit }"
      >
      </AmCheckbox>

      <p
        v-if="!licence.isLite && !licence.isStarter"
        class="am-fs__payments-method"
      >
        {{ labelsDisplay('payment_method') }}
      </p>
      <div
        v-if="!licence.isLite && !licence.isStarter"
        class="am-fs__payments-main"
      >
        <div class="am-fs__payments-main-cards">
          <div
            v-for="(available, gateway) in availablePayments"
            :key="gateway"
            class="am-fs__payments-main-button"
            :class="{
              'am-fs__payments-main-button_selected':
                paymentGateway === gateway,
            }"
            @click="setPaymentGateway(gateway)"
          >
            <img :src="baseUrls.wpAmeliaPluginURL + '/v3/src/assets/img/icons/' + gateway + '.svg'" :alt="gateway" />
            <div>
              <p>
                {{
                  gateway === 'onSite'
                    ? labelsDisplay('on_site')
                    : paymentsBtnText.filter((item) => item.key === gateway)[0]
                        .text
                }}
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="am-fs__payments-sentence">
        <p>
          {{
            paymentGateway === 'onSite'
              ? labelsDisplay('payment_onsite_sentence')
              : paymentGateway === 'mollie' || paymentGateway === 'wc'
              ? labelsDisplay('payment_wc_mollie_sentence')
              : ''
          }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import AmCheckbox from '../../../../_components/checkbox/AmCheckbox.vue'
import AppointmentInfo from '../parts/AppointmentInfo.vue'
import PaymentPackageInfo from '../parts/PaymentPackageInfo.vue'
import { inject, ref, markRaw, computed } from 'vue'
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation'
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

let props = defineProps({
  globalClass: {
    type: String,
    default: '',
  },
})

// * Plugin Licence
let licence = inject('licence')

// * Features
let features = inject('features')

let bookableType = inject('bookableType')

let baseUrls = inject('baseUrls')

let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

// * Payment Part
const componentTypes = {
  appointment: markRaw(AppointmentInfo),
  package: markRaw(PaymentPackageInfo),
}

let paymentDeposit = ref(false)

const availablePayments = {
  onSite: true,
  stripe: true,
  payPal: true,
  razorpay: true,
}

let paymentGateway = ref('onSite')

function setPaymentGateway(gateway) {
  paymentGateway.value = gateway
}

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value].paymentStep.translations &&
      amCustomize.value[pageRenderKey.value].paymentStep.translations[label] &&
      amCustomize.value[pageRenderKey.value].paymentStep.translations[label][
        langKey.value
      ]
      ? amCustomize.value[pageRenderKey.value].paymentStep.translations[label][
          langKey.value
        ]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Colors
let amColors = inject('amColors')
const cssVars = computed(() => {
  return {
    '--am-c-ps-price-bgr': useColorTransparency(
      amColors.value.colorBtnPrim,
      0.05
    ),
    '--am-c-ps-total-price': amColors.value.colorBtnPrim,
    '--am-c-ps-text-op50': useColorTransparency(
      amColors.value.colorMainText,
      0.5
    ),
    '--am-c-ps-text-op25': useColorTransparency(
      amColors.value.colorMainText,
      0.25
    ),
    '--am-c-ps-text-op20': useColorTransparency(
      amColors.value.colorMainText,
      0.2
    ),
    '--am-c-ps-text-op06': useColorTransparency(
      amColors.value.colorMainText,
      0.06
    ),
    '--am-c-ps-primary': amColors.value.colorPrimary,
    '--am-c-ps-primary-op10': useColorTransparency(
      amColors.value.colorPrimary,
      0.1
    ),
    '--am-c-ps-primary-op06': useColorTransparency(
      amColors.value.colorPrimary,
      0.06
    ),
  }
})

let paymentsBtnText = []

Object.keys(availablePayments).forEach((key) => {
  paymentsBtnText.push({ key, text: getPaymentBtnString(key) })
})

function getPaymentBtnString(key) {
  switch (key) {
    case 'onSite':
      return amLabels['on_site']
    case 'payPal':
      return amLabels['payment_paypal']
    case 'stripe':
      return amLabels['stripe']
    case 'razorpay':
      return amLabels['razorpay']
    case 'mollie':
      return amLabels['on_line']
    default:
      return ''
  }
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
  },
}
</script>

<style lang="scss">
#amelia-app-backend-new #amelia-container {
  --am-c-ps-text: var(--am-c-main-text);
  --am-c-ps-border: var(--am-c-inp-border);

  .am-fs__main-content.am-fs__payments {
    padding-top: 0;
  }

  .am-fs__payments {
    & > * {
      $count: 6;
      @for $i from 0 through $count {
        &:nth-child(#{$i + 1}) {
          animation: 600ms
            cubic-bezier(0.45, 1, 0.4, 1.2)
            #{$i *
            100}ms
            am-animation-slide-up;
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
        color: var(--am-c-pay-text-op60);
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
      position: absolute;
      top: 5px;
    }

    &-main {
      &-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
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
  }
}
</style>
