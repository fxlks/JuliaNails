<template>
  <el-collapse
    v-model="activeBookingIndex"
    class="am-capai-collapse am-capai-pay"
    accordion
    :style="cssVars"
  >
    <el-collapse-item
      v-for="(item, index) in appointmentBookings"
      :key="index"
      :name="index"
      class="am-capai-collapse__item"
    >
      <template #title>
        <div
          class="am-capai-pay__heading"
          :class="{'am-pb-16': !((item.customer.email && customerEmailVisibility) || (item.customer.phone && customerPhoneVisibility))}"
        >
          <!-- Customer Name -->
          <div
            class="am-capai-customer__name"
            :class="noShowData(item.customer.noShowCount).class"
          >
          <span
            v-if="noShowData(item.customer.noShowCount).icon"
            :class="`am-icon-${noShowData(item.customer.noShowCount).icon}`"
          />
            {{ item.customer.firstName + ' ' + item.customer.lastName }}
          </div>
          <!-- /Customer Name -->

          <!-- Payment Status -->
          <div class="am-capai-pay__heading-status">
            <span
              :class="usePaymentStatusIcon(paymentData[index].status, amColors).icon"
              :style="{color: usePaymentStatusIcon(paymentData[index].status, amColors).color}"
            />
            <div class="am-capai-pay__heading-status__text">
              {{ usePaymentStatusName(paymentData[index].status) }}
            </div>
          </div>
          <!-- /Payment Status -->
        </div>
      </template>

      <!-- Customer Info -->
      <div
        v-if="(item.customer.email && customerEmailVisibility) || (item.customer.phone && customerPhoneVisibility)"
        class="am-capai-customer__data-wrapper"
        :class="props.responsiveClass"
      >
        <!-- Customer Email -->
        <div
          v-if="item.customer.email && customerEmailVisibility"
          class="am-capai-customer__data"
          :class="props.responsiveClass"
        >
          <span class="am-icon-email"></span>
          <a :href="`mailto:${item.customer.email}`">
            {{ item.customer.email }}
          </a>
        </div>
        <!-- /Customer Email -->

        <!-- Customer Phone -->
        <div
          v-if="item.customer.phone && customerPhoneVisibility"
          class="am-capai-customer__data"
          :class="props.responsiveClass"
        >
          <span class="am-icon-phone"></span>
          <a :href="`tel:${item.customer.phone}`">
            {{ item.customer.phone }}
          </a>
        </div>
        <!-- /Customer Phone -->
      </div>
      <!-- /Customer Info -->

      <div
        class="am-capai-pay__content"
        :class="props.responsiveClass"
      >
        <template
          v-for="(payment, paymentIndex) in item.payments"
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

        <div v-if="pageWidth > 480" class="am-divider" />

        <div
          class="am-capai-pay__row"
          :class="props.responsiveClass"
        >
          <div class="am-capai-pay__row-label">
            {{ amLabels.price }}
          </div>
          <div
            class="am-capai-pay__row-value"
            :class="{'am-pl-34': pageWidth > 480}"
          >
            {{ useFormattedPrice(paymentData[index].bookable) }}
          </div>
        </div>

        <div
          class="am-capai-pay__row"
          :class="props.responsiveClass"
        >
          <div class="am-capai-pay__row-label">
            {{ amLabels.extras }}
          </div>
          <div
            class="am-capai-pay__row-value"
            :class="{'am-pl-34': pageWidth > 480}"
          >
            {{ useFormattedPrice(paymentData[index].total - paymentData[index].bookable) }}
          </div>
        </div>

        <div
          class="am-capai-pay__row"
          :class="props.responsiveClass"
        >
          <div class="am-capai-pay__row-label">
            {{ amLabels.subtotal }}
          </div>
          <div
            class="am-capai-pay__row-value"
            :class="{'am-pl-34': pageWidth > 480}"
          >
            {{ useFormattedPrice(paymentData[index].total) }}
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
            :class="{'am-pl-34': pageWidth > 480}"
          >
            {{ useFormattedPrice(paymentData[index].discount) }}
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
            :class="{'am-pl-34': pageWidth > 480}"
          >
            {{ useFormattedPrice(paymentData[index].tax) }}
          </div>
        </div>

        <div
          v-if="paymentData[index].wcTax"
          class="am-capai-pay__row"
          :class="props.responsiveClass"
        >
          <div class="am-capai-pay__row-label">
            {{ amLabels.tax }} (Woo)
          </div>
          <div
            class="am-capai-pay__row-value"
            :class="{'am-pl-34': pageWidth > 480}"
          >
            {{ useFormattedPrice(paymentData[index].wcTax) }}
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
            :class="{'am-pl-34': pageWidth > 480}"
          >
            {{ useFormattedPrice(paymentData[index].deposit) }}
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
            :class="{'am-pl-34': pageWidth > 480}"
          >
            {{ useFormattedPrice(paymentData[index].paid ? paymentData[index].paid - paymentData[index].deposit : 0) }}
          </div>
        </div>

        <div
          v-if="paymentData[index].refunded"
          class="am-capai-pay__row"
          :class="props.responsiveClass"
        >
          <div class="am-capai-pay__row-label">
            {{ amLabels.refunded }}
          </div>
          <div
            class="am-capai-pay__row-value"
            :class="{'am-pl-34': pageWidth > 480}"
          >
            {{ useFormattedPrice(paymentData[index].refunded) }}
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
            :class="{'am-pl-34': pageWidth > 480}"
          >
            {{ useFormattedPrice(paymentData[index].paid - (paymentData[index].total - paymentData[index].discount + paymentData[index].tax + paymentData[index].wcTax)) }}
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
            :class="{'am-pl-34': pageWidth > 480}"
          >
            {{ useFormattedPrice(paymentData[index].total - paymentData[index].discount + paymentData[index].tax + paymentData[index].wcTax) }}
          </div>
        </div>
      </div>
    </el-collapse-item>
  </el-collapse>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  onMounted,
  computed,
  inject,
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Import Composables
import {
  usePaymentGatewayName,
  usePaymentStatusIcon,
  usePaymentStatusName,
} from "../../../../../../assets/js/admin/payment";
import { getFrontedFormattedDate } from "../../../../../../assets/js/common/date";
import { useFormattedPrice } from "../../../../../../assets/js/common/formatting";
import {
  useAppointmentBookingAmountData,
  useAppointmentServicePrice,
  useChangedBookingPrice,
} from "../../../../../../assets/js/common/appointments";
import { useColorTransparency } from "../../../../../../assets/js/common/colorManipulation";

// * Props
let props = defineProps({
  responsiveClass: {
    type: String,
    default: "",
  },
  savedAppointment: {
    type: Object,
    default: () => {},
  },
});

// * Store
let store = useStore()

// * Data in shortcode
const shortcodeData = inject('shortcodeData')

// * Customize data
let amCustomize = inject('amCustomize')

// * Page Width
let pageWidth = inject('pageWidth')

// * Labels
let amLabels = inject('amLabels')

// * Customized options
let customerPhoneVisibility = computed(() => shortcodeData.value.cabinetType === 'employee' ? amCustomize.value.appointments.options.customerPhone.visibility : true)
let customerEmailVisibility = computed(() => shortcodeData.value.cabinetType === 'employee' ? amCustomize.value.appointments.options.customerEmail.visibility : true)

let activeBookingIndex = ref(null)

let service = computed(() => store.getters['appointment/getEmployeeService'])

let appointmentBookings = computed(() => {
  return store.getters['appointment/getBookings'].filter((b) => 'id' in b && b.id && b.status !== 'canceled' && b.status !== 'rejected')
})

let paymentData = computed(() => {
  let amountData = []

  appointmentBookings.value.forEach((booking, index) => {
    let changedBookingPrice = useChangedBookingPrice(
      store.getters['appointment/getAppointmentData'],
      props.savedAppointment,
      booking,
      service.value
    )

    amountData.push(
      useAppointmentBookingAmountData(
        store,
        {
          price: !changedBookingPrice
            ? booking.price
            : useAppointmentServicePrice(service.value, booking.persons, booking.duration),
          persons: booking.persons,
          aggregatedPrice: booking.aggregatedPrice,
          extras: booking.extras.filter((e) => 'id' in e && e.id),
          serviceId: store.getters['appointment/getServiceId'],
          tax: booking.tax,
          coupon: booking.coupon,
          wcTax: booking.payments.filter(p => p.wcOrderId && p.wcItemTaxValue).reduce(
            (sum, payment) => sum + payment.wcItemTaxValue,
            0
          )
        },
        false
      )
    )

    amountData[index].deposit = booking.payments.length && (booking.payments[0].status === 'paid' || booking.payments[0].status === 'partiallyPaid')
      ? booking.payments[0].amount
      : 0

    amountData[index].paid = booking.payments.filter(p => p.status === 'paid' || p.status === 'partiallyPaid').reduce(
      (sum, payment) => sum + payment.amount,
      0
    )

    amountData[index].refunded = booking.payments.filter(p => p.status === 'refunded').reduce(
      (sum, payment) => sum + payment.amount,
      0
    )

    amountData[index].status = booking.payments.reduce(
      (sum, payment) => sum + payment.amount,
      0
    )

    if (booking.payments.filter(i => i.status !== 'refunded').length === 0) {
      amountData[index].status = 'refunded'
    } else if (
      amountData[index].paid - (amountData[index].total - amountData[index].discount + amountData[index].tax + amountData[index].wcTax) === 0
    ) {
      amountData[index].status = 'paid'
    } else if (amountData[index].paid) {
      amountData[index].status = 'partiallyPaid'
    } else {
      amountData[index].status = 'pending'
    }
  })

  return amountData
})

const { noShowData } = inject('noShowData')

// * Colors
let amColors = inject('amColors')

// * CSS Variables
let cssVars = computed(() => {
  return {
    '--am-c-pay-text-op03': useColorTransparency(amColors.value.colorMainText, 0.03)
  }
})

onMounted(() => {
  if (store.getters['appointment/getBookings'].filter((b) => b.status !== 'canceled' && b.status !== 'rejected').length < 2) {
    activeBookingIndex.value = 0
  }
})
</script>

<style lang="scss">
@mixin am-cabinet-appointment-payment {
  // am    - amelia
  // capai - cabinet-panel-appointment-item
  // pay   - payment
  .am-capai-pay {

    // Heading
    &__heading {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 16px 0 0;

      &.am-pb-16 {
        padding-bottom: 16px;
      }

      &-status {
        display: flex;
        align-items: center;
        gap: 4px;

        [class^='am-icon-'] {
          font-size: 24px;
        }

        &__text {

          font-size: 15px;
          font-style: normal;
          font-weight: 500;
          line-height: 1.6;
          color: var(--am-c-main-text);
        }
      }
    }

    // Content
    &__content {
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      align-items: flex-start;
      gap: 8px;
      padding: 16px;
      border-radius: 9px;
      background-color: var(--am-c-pay-text-op05);
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
  @include am-cabinet-appointment-payment;
}
</style>