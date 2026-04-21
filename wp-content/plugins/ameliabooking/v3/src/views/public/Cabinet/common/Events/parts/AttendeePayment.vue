<template>
  <div
    class="am-capai-pay__content"
    :class="props.responsiveClass"
    :style="cssVars"
  >
    <template
      v-for="(payment, paymentIndex) in payments"
      :key="paymentIndex"
    >
      <div
        class="am-capai-pay__row"
        :class="props.responsiveClass"
      >
        <div class="am-capai-pay__row-label">
          {{ amLabels.date }}
        </div>
        <div class="am-capai-pay__row-value">
          {{getFrontedFormattedDate(payment.dateTime.split(' ')[0]) }}
        </div>
      </div>

      <div
        class="am-capai-pay__row"
        :class="props.responsiveClass"
      >
        <div class="am-capai-pay__row-label">
          {{ amLabels.payment_method }}
        </div>
        <div class="am-capai-pay__row-value">
          {{ usePaymentGatewayName(payment) }}
        </div>
      </div>

      <div
        class="am-capai-pay__row"
        :class="props.responsiveClass"
      >
        <div class="am-capai-pay__row-label">
          {{ amLabels.status }}
        </div>
        <div class="am-capai-pay__row-value">
          <span
            :class="usePaymentStatusIcon(payment.status, amColors).icon"
            :style="{color: usePaymentStatusIcon(payment.status, amColors).color}"
          />
          {{ usePaymentStatusName(payment.status) }}
        </div>
      </div>
    </template>

    <div v-if="props.pageWidth > 480" class="am-divider" />

    <div
      class="am-capai-pay__row"
      :class="props.responsiveClass"
    >
      <div class="am-capai-pay__row-label">
        {{ amLabels.price }}
      </div>
      <div
        class="am-capai-pay__row-value"
        :class="{'am-pl-34': props.pageWidth > 480}"
      >
        {{ useFormattedPrice(paymentData.price) }}
      </div>
    </div>

    <div
      class="am-capai-pay__row"
      :class="props.responsiveClass"
    >
      <div class="am-capai-pay__row-label">
        {{ amLabels.discount_amount }}
      </div>
      <div
        class="am-capai-pay__row-value"
        :class="{'am-pl-34': props.pageWidth > 480}"
      >
        {{ useFormattedPrice(paymentData.discount) }}
      </div>
    </div>

    <div
      class="am-capai-pay__row"
      :class="props.responsiveClass"
    >
      <div class="am-capai-pay__row-label">
        {{ amLabels.tax }}
      </div>
      <div
        class="am-capai-pay__row-value"
        :class="{'am-pl-34': props.pageWidth > 480}"
      >
        {{ useFormattedPrice(paymentData.tax) }}
      </div>
    </div>

    <div
      class="am-capai-pay__row"
      :class="props.responsiveClass"
    >
      <div class="am-capai-pay__row-label">
        {{ amLabels.paid_deposit }}
      </div>
      <div
        class="am-capai-pay__row-value"
        :class="{'am-pl-34': props.pageWidth > 480}"
      >
        {{ useFormattedPrice(paymentData.deposit) }}
      </div>
    </div>

    <div
      class="am-capai-pay__row"
      :class="props.responsiveClass"
    >
      <div class="am-capai-pay__row-label">
        {{ amLabels.paid_remaining_amount }}
      </div>
      <div
        class="am-capai-pay__row-value"
        :class="{'am-pl-34': props.pageWidth > 480}"
      >
        {{ useFormattedPrice(paymentData.paid - paymentData.deposit) }}
      </div>
    </div>

    <div
      class="am-capai-pay__row"
      :class="props.responsiveClass"
    >
      <div class="am-capai-pay__row-label">
        {{ amLabels.due }}
      </div>
      <div
        class="am-capai-pay__row-value"
        :class="{'am-pl-34': props.pageWidth > 480}"
      >
        {{ useFormattedPrice(paymentData.paid - (paymentData.price - paymentData.discount + paymentData.tax)) }}
      </div>
    </div>

    <div
      class="am-capai-pay__row"
      :class="props.responsiveClass"
    >
      <div class="am-capai-pay__row-label">
        {{ amLabels.total }}
      </div>
      <div
        class="am-capai-pay__row-value"
        :class="{'am-pl-34': props.pageWidth > 480}"
      >
        {{ useFormattedPrice(paymentData.price - paymentData.discount + paymentData.tax) }}
      </div>
    </div>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  computed,
  inject,
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Import Composables
import {
  usePaymentGatewayName,
  usePaymentStatusName,
  usePaymentStatusIcon,
} from "../../../../../../assets/js/admin/payment";
import { getFrontedFormattedDate } from "../../../../../../assets/js/common/date";
import { useFormattedPrice } from "../../../../../../assets/js/common/formatting";
import { useAttendeeAmount } from "../../../../../../assets/js/common/attendees";
import { useColorTransparency } from "../../../../../../assets/js/common/colorManipulation";

// * Component Props
const props = defineProps({
  event: {
    type: Object,
    default: () => {},
  },
  pageWidth: {
    type: Number,
    default: 0,
  },
})

// * Store
let store = useStore()

// * Labels
let amLabels = inject('amLabels')

let payments = computed(() => store.getters['attendee/getPayments'])

let paymentData = computed(() => Object.assign(
  useAttendeeAmount(store),
  {
    deposit: payments.value[0].amount,
    paid: payments.value.reduce(
      (sum, payment) => sum + payment.amount,
      0
    ),
  }
))

// * Colors
let amColors = inject('amColors')

// * CSS Variables
let cssVars = computed(() => {
  return {
    '--am-c-pay-text-op03': useColorTransparency(amColors.value.colorMainText, 0.03)
  }
})
</script>

<style lang="scss">
@mixin am-cabinet-attendee-payment {
  // am    - amelia
  // capai - cabinet-panel-attendee-item
  // pay   - payment
  .am-capai-pay {
    // Content
    &__content {
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      align-items: flex-start;
      gap: 8px;
      padding: 16px;
      border-radius: 9px;
      background-color: var(--am-c-pay-text-op03);
    }

    // Row
    &__row {
      width: 100%;
      display: flex;
      flex-direction: row;
      align-items: center;
      gap: 24px;

      &.am-rw {
        &-480 {
          flex-direction: column;
          align-items: flex-start;
          gap: 0;
          border-bottom: 1px solid var(--am-c-inp-border);

          .am-capai-pay__row {
            &-label {
              width: 100%;
              font-weight: 500;
            }

            &-value {
              width: 100%;
            }
          }
        }
      }

      &-label, &-value {
        width: calc(50% - 12px);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
        font-style: normal;
        font-weight: 400;
        line-height: 1.6;
        color: var(--am-c-main-text);

        [class^='am-icon-'] {
          font-size: 24px;
          line-height: 24px;
        }
      }

      &-value {
        &.am-pl-34 {
          padding-left: 34px;
        }
      }
    }

  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-attendee-payment;
}
</style>