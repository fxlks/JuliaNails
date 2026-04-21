<template>
  <div
    v-if="props.coupon.code && (props.coupon.discount || props.coupon.deduction)"
    class="am-congrats__info-item"
    :class="responsiveClass"
  >
    <span class="am-congrats__info-item__label">
      Coupon code:
    </span>
    <span class="am-congrats__info-item__value">
      {{ props.coupon.code }}
    </span>
  </div>
  <div
    v-if="props.booked.price > 0 || (props.booked.price <= 0 && props.coupon.code)"
    class="am-congrats__info-item am-fs__congrats-info-payment"
    :class="responsiveClass"
  >
    <span class="am-congrats__info-item__label">
      {{ paymentLabel }}
    </span>
    <span class="am-congrats__info-item__value">
      {{ priceDisplay }}
    </span>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  computed,
  inject
} from "vue";

// * Composable
import { useFormattedPrice } from "../../../../../assets/js/common/formatting";
import { useResponsiveClass } from "../../../../../assets/js/common/responsive";

let props = defineProps({
  booked: {
    type: Object,
    required: true
  },
  labels: {
    type: Object,
    required: true
  },
  coupon: {
    type: Object,
    required: true
  },
  inDialog: {
    type: Boolean,
    default: false
  }
})

// * Responsive - Container Width
let cWidth = inject('containerWidth')
let dWidth = inject('dialogWidth')

let componentWidth = computed(() => {
  return props.inDialog ? dWidth.value : cWidth.value
})

let responsiveClass = computed(() => useResponsiveClass(componentWidth.value))

function getPaymentGatewayNiceName (payment) {
  if (payment.gateway === 'onSite') {
    return props.labels['on_site']
  }

  if (payment.gateway === 'wc') {
    return payment.gatewayTitle
  }

  if (payment.gateway) {
    return payment.gateway.charAt(0).toUpperCase() + payment.gateway.slice(1)
  }
}

let paymentLabel = computed(() => {
  if(props.booked && props.booked.paymentAmount && props.booked.payments[0].gateway === 'onSite') {
    return `${props.labels.congrats_total_amount}:`
  }

  return `${props.labels.congrats_payment}:`
})

let priceDisplay = computed(() => {
  if (props.booked && props.booked.paymentAmount && props.booked.payments[0].gateway) {
    let gatewayTitle = ''

    if (props.booked.payments[0].gatewayTitle) {
      gatewayTitle = props.booked.payments[0].gatewayTitle
    } else {
      gatewayTitle = getPaymentGatewayNiceName(props.booked.payments[0])
    }

    return `${useFormattedPrice(props.booked.paymentAmount)} - ${gatewayTitle}`
  }

  if (props.booked) {
    let pricePart = ''

    pricePart = props.booked.payments[0].gateway !== 'onSite'
      ? useFormattedPrice(props.booked.paymentAmount)
      : useFormattedPrice(props.booked.price)

    let label = ''
    if (props.booked.payments[0].status !== 'paid') {
      label = ` - ${props.labels.on_site}`
    }

    return `${pricePart} ${label}`
  }

  return ''
})
</script>

<script>
export default {
  name: "PaymentCongratsInfo"
}
</script>

<style lang="scss">
@mixin congrats-info-item-block {
  .am-congrats__info-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;

    * {
      font-size: 14px;
      line-height: 1.42857;
    }

    &.am-rw-480 {
      flex-direction: column;
      align-items: flex-start;
    }

    &__label {
      color: var(--am-c-atc-text-op40);
    }

    &__value {
      color: var(--am-c-atc-text);
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include congrats-info-item-block;
}
</style>
