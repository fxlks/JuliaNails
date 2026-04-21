<template>
  <div class="am-fs__payments-wrapper" :style="cssVars">
    <div class="am-fs__payments">

      <!-- Recurring Service -->
      <AmCollapse
        v-if="isRecurring"
        class="am-fs__payments-services"
      >
        <AmCollapseItem
          :side="true"
          @collapse-open="recurringCollapseState = false"
          @collapse-close="recurringCollapseState = true"
        >
          <template #heading>
            <div class="am-fs__payments-services-info">
              <span>
                {{ amLabels.summary_services }}
              </span>
            </div>
          </template>
          <template #icon-below>
            <Transition
              :duration="{enter: 500, leave: 500}"
              @enter="onCollapseClose"
              @leave="onCollapseOpen"
            >
              <div
                v-show="recurringCollapseState"
                class="am-fs__payments-services-sub"
              >
                <p>
                  {{ amLabels.summary_services_subtotal }}
                </p>
                <p class="am-amount">
                  {{ useFormattedPrice(appointmentsAmount.prepaid.totalServiceAmount + appointmentsAmount.postpaid.totalServiceAmount) }}
                </p>
              </div>
            </Transition>
          </template>
          <template #default>
            <div class="am-fs__payments-services-open">
              <div class="am-fs__payments-services-open-bordered">
                <div
                  v-for="(sNum, sPrice) in appointmentsAmount.servicesPrices"
                  :key="sPrice"
                  class="am-fs__payments-services-open-text"
                >
                <span>
                  {{`${service.name} (${useFormattedPrice(sPrice)}) x ${persons} ${personsLabel}`}}
                </span>
                  <span class="am-amount">
                  {{ useFormattedPrice(sPrice*(service.aggregatedPrice ? persons : 1)) }}
                </span>
                </div>
              </div>

              <div class="am-fs__payments-services-open-bordered">
                <div
                  v-for="(sNum, sPrice) in appointmentsAmount.servicesPrices"
                  v-show="isRecurring"
                  :key="sPrice"
                  class="am-fs__payments-services-open-total am-fs__payments-services-open-text"
                >
                <span>
                  {{ `${sNum} ${recurrenceLabel(sNum)} x ${service.name} (${useFormattedPrice(sPrice)}) x ${persons} ${personsLabel}` }}
                </span>
                </div>
              </div>

              <div class="am-fs__payments-services-sub">
                <p>
                  {{ amLabels.summary_services_subtotal }}
                </p>
                <p class="am-amount">
                  {{ useFormattedPrice(appointmentsAmount.prepaid.totalServiceAmount + appointmentsAmount.postpaid.totalServiceAmount) }}
                </p>
              </div>
            </div>
          </template>
        </AmCollapseItem>
      </AmCollapse>
      <!-- /Recurring Service -->

      <div v-else class="am-fs__payments-service">
        <div class="am-fs__payments-services-info">
          <span>
            {{ amLabels.summary_services }}
          </span>
        </div>
        <div class="am-fs__payments-services-open">
          <div v-for="(sNum, sPrice) in appointmentsAmount.servicesPrices" :key="sPrice" class="am-fs__payments-services-sub">
            <p>
              {{ `${service.name} (${useFormattedPrice(sPrice)})` }}
              <span v-if="service.aggregatedPrice">
                {{ `x ${persons} ${personsLabel}`}}
              </span>
            </p>
            <p class="am-amount">
              {{ useFormattedPrice(appointmentsAmount.prepaid.totalServiceAmount + appointmentsAmount.postpaid.totalServiceAmount) }}
            </p>
          </div>
        </div>
      </div>

      <AmCollapse
        v-if="extras.length > 0"
        class="am-fs__payments-services"
      >
        <!--  -->
        <AmCollapseItem
          :side="true"
          @collapse-open="extrasCollapseState = false"
          @collapse-close="extrasCollapseState = true"
        >
          <template #heading>
            <div class="am-fs__payments-services-info">
              <span>
                {{ amLabels.summary_extras }}
              </span>
            </div>
          </template>
          <template #icon-below>
            <Transition
              :duration="{enter: 500, leave: 500}"
              @enter="onCollapseClose"
              @leave="onCollapseOpen"
            >
              <div
                v-show="extrasCollapseState"
                class="am-fs__payments-services-sub"
              >
                <p>
                  {{ amLabels.summary_extras_subtotal }}
                </p>
                <p class="am-amount">
                  {{ useFormattedPrice(appointmentsAmount.prepaid.totalExtrasAmount + appointmentsAmount.postpaid.totalExtrasAmount) }}
                </p>
              </div>
            </Transition>
          </template>
          <template #default>
            <div class="am-fs__payments-services-open">
              <div :class="{'am-fs__payments-services-open-bordered': isRecurring}">
                <div
                  v-for="extra in extras"
                  :key="extra.extraId"
                  class="am-fs__payments-services-open-text"
                >
                <span>
                  {{ `${extra.quantity} x ${extra.name} (${useFormattedPrice(extra.price)})` }}
                  <span
                    v-if="extra.aggregatedPrice === null ? service.aggregatedPrice : extra.aggregatedPrice">
                    {{ `x ${persons} ${personsLabel}` }}
                  </span>
                </span>
                  <span class="am-amount">
                  {{ useFormattedPrice(useAppointmentExtraAmount(service, extra, persons)) }}
                </span>
                </div>
              </div>

              <div class="am-fs__payments-services-open-bordered">
                <div
                  v-for="(extra, index) in extras"
                  v-show="isRecurring"
                  :key="index" class="am-fs__payments-services-open-total am-fs__payments-services-open-text"
                >
                <span>
                  {{ `${bookingsCount} ${amLabels.summary_recurrences} x ${extra.name} ( ${useFormattedPrice(useAppointmentExtraAmount(service, extra, persons))} )` }}
                </span>
                </div>
              </div>

              <div class="am-fs__payments-services-sub">
                <p>
                  {{ amLabels.summary_extras_subtotal }}
                </p>
                <p class="am-amount">
                  {{ useFormattedPrice(appointmentsAmount.prepaid.totalExtrasAmount + appointmentsAmount.postpaid.totalExtrasAmount) }}
                </p>
              </div>
            </div>
          </template>
        </AmCollapseItem>
      </AmCollapse>
      <div v-else-if="extras.length" class="am-fs__payments-extra">
        <div class="am-fs__payments-services-info">
          <span>
            {{ amLabels.summary_extras }}
          </span>
        </div>
        <div class="am-fs__payments-services-open">
          <div class="am-fs__payments-services-sub">
            <p>
              {{ `${extras[0].quantity} ${extras[0].name} (${useFormattedPrice(extras[0].price)})` }}
              <span
                v-if="extras[0].aggregatedPrice === null ? service.aggregatedPrice : extras[0].aggregatedPrice">
                {{ `x ${persons} ${personsLabel}` }}
              </span>
            </p>
            <p class="am-amount">
              {{ useFormattedPrice(appointmentsAmount.prepaid.totalExtrasAmount + appointmentsAmount.postpaid.totalExtrasAmount) }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="am-fs__payments-app-info">
      <Coupon
        v-if="settings.payments.coupons"
        type="appointment"
        :count="bookingsCount"
        :ids="[service.id]"
        @coupon-applied="couponApplied"
      />

      <div
        v-if="!onlyTotal"
        class="am-fs__payments-app-info-subtotal"
      >
        <span>
          {{`${amLabels.subtotal}:`}}
        </span>
        <span class="am-amount">
          {{ useFormattedPrice(appointmentsAmount.prepaid.totalAmount + appointmentsAmount.postpaid.totalAmount) }}
        </span>
      </div>

      <Transition name="am-fade">
        <div
          v-if="settings.payments.coupons"
          v-show="appointmentsAmount.prepaid.discountAmount > 0"
          class="am-fs__payments-app-info-discount"
          :class="{'am-fs__payments-app-info-discount-green': appointmentsAmount.prepaid.discountAmount > 0 }"
        >
          <span>
            {{`${amLabels.discount_amount_colon}:`}}
          </span>
          <span class="am-amount">
            {{ appointmentsAmount.prepaid.discountAmount + appointmentsAmount.postpaid.discountAmount > appointmentsAmount.prepaid.totalAmount + appointmentsAmount.postpaid.totalAmount
            ? useFormattedPrice(appointmentsAmount.prepaid.totalAmount + appointmentsAmount.postpaid.totalAmount)
            : useFormattedPrice(appointmentsAmount.prepaid.discountAmount + appointmentsAmount.postpaid.discountAmount) }}
          </span>
        </div>
      </Transition>

      <div
        v-if="appointmentsAmount.prepaid.taxAmount + appointmentsAmount.postpaid.taxAmount > 0"
        class="am-fs__payments-app-info-tax"
      >
        <span>{{ amLabels.total_tax_colon}}:</span>
        <span class="am-amount">
          {{ useFormattedPrice(appointmentsAmount.prepaid.taxAmount + appointmentsAmount.postpaid.taxAmount) }}
        </span>
      </div>

      <div
        class="am-fs__payments-app-info-total"
        :class="{'am-single-row': onlyTotal, 'am-fs__payments-bordered': !onlyTotal}"
      >
        <span>{{amLabels.total_amount_colon}}</span>
        <span class="am-amount">
          {{ useFormattedPrice(getTotalAmount()) }}
          <template v-if="taxVisibility && !amSettings.payments.taxes.excluded">
            {{amLabels.incl_tax}}
          </template>
        </span>
      </div>

      <div v-if="(hasDeposit || (isRecurring && appointmentsAmount.postpaid.totalAmount)) && paymentGateway !== 'onSite'">
        <div class="am-fs__payments-app-info-deposit">
          <span>
            {{ `${amLabels.paying_now}:` }}
          </span>
          <span class="am-amount">
            {{ useFormattedPrice(getPrepaidAmount()) }}
          </span>
        </div>

        <div class="am-fs__payments-app-info-remaining">
          <span>
            {{ `${amLabels.paying_later}:` }}
          </span>
          <span class="am-amount">
            {{ useFormattedPrice(getPostpaidAmount()) }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Coupon from '../../../Parts/Payment/Coupon.vue'
import AmCollapse from '../../../../_components/collapse/AmCollapse'
import AmCollapseItem from '../../../../_components/collapse/AmCollapseItem'
import { inject, provide, reactive, ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import { useFormattedPrice } from '../../../../../assets/js/common/formatting.js'
import {
  useAppointmentExtraAmount,
  useAppointmentsAmountInfo
} from '../../../../../assets/js/common/appointments.js'
import { useCartItem } from "../../../../../assets/js/public/cart";
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation.js'
import {useTaxVisibility} from "../../../../../assets/js/common/pricing";

const store = useStore()

// * Root Settings
const amSettings = inject('settings')

const amLabels = inject('amLabels')

// * Root Settings
const settings = inject('settings')

let extras = computed(() => store.getters['booking/getSelectedExtras'])

let persons = computed(() => store.getters['booking/getBookingPersons'])

let personsLabel = computed(() => {
  return persons.value > 1 ? amLabels.value.summary_persons : amLabels.value.summary_person
})

function recurrenceLabel (num) {
  return num > 1 ? amLabels.value.summary_recurrences : amLabels.value.summary_recurrence
}

let isRecurring = ref(false)

let hasDeposit = inject('hasDeposit')

let appointmentsAmount = computed(() => {
  let amountInfo = useAppointmentsAmountInfo(store)

  let amount = {
    prepaid: {
      totalAmount: 0,
      totalServiceAmount: 0,
      totalExtrasAmount: 0,
      discountAmount: 0,
      taxAmount: 0,
      depositAmount: 0,
    },
    postpaid: {
      totalAmount: 0,
      totalServiceAmount: 0,
      totalExtrasAmount: 0,
      discountAmount: 0,
      taxAmount: 0,
      depositAmount: 0,
    }
  }

  amountInfo.forEach((item) => {
    amount.prepaid.totalAmount += item.prepaid.totalAmount
    amount.postpaid.totalAmount += item.postpaid.totalAmount

    amount.prepaid.totalServiceAmount += item.prepaid.totalServiceAmount
    amount.postpaid.totalServiceAmount += item.postpaid.totalServiceAmount

    amount.prepaid.totalExtrasAmount += item.prepaid.totalExtrasAmount
    amount.postpaid.totalExtrasAmount += item.postpaid.totalExtrasAmount

    amount.prepaid.discountAmount += item.prepaid.discountAmount
    amount.postpaid.discountAmount += item.postpaid.discountAmount

    amount.prepaid.taxAmount += item.prepaid.taxAmount
    amount.postpaid.taxAmount += item.postpaid.taxAmount

    amount.prepaid.depositAmount += item.prepaid.depositAmount
    amount.postpaid.depositAmount += item.postpaid.depositAmount

    amount.servicesPrices = item.servicesPrices

    amount.prepaid.count = item.prepaid.count
    amount.postpaid.count = item.postpaid.count
  })

  return amount
})

function getPrepaidAmount () {
  const { totalAmount, discountAmount, taxAmount, depositAmount } = appointmentsAmount.value.prepaid;
  const total = totalAmount - discountAmount + taxAmount;

  return depositAmount ? Math.min(total, depositAmount) : total;
}

function getPostpaidAmount () {
  return getTotalAmount() - getPrepaidAmount()
}

function getTotalAmount () {
  return appointmentsAmount.value.prepaid.totalAmount +
    appointmentsAmount.value.postpaid.totalAmount +
    appointmentsAmount.value.prepaid.taxAmount +
    appointmentsAmount.value.postpaid.taxAmount -
    appointmentsAmount.value.prepaid.discountAmount -
    appointmentsAmount.value.postpaid.discountAmount
}

let onlyTotal = computed(() => {
  return (!settings.payments.coupons || appointmentsAmount.value.prepaid.discountAmount === 0) &&
    (!settings.payments.taxes.enabled || (appointmentsAmount.value.prepaid.taxAmount + appointmentsAmount.value.postpaid.taxAmount === 0))
})

let paymentGateway = computed(() => store.getters['booking/getPaymentGateway'])

let bookingsCount = ref(0)

provide('bookingsCount', bookingsCount)

let service = reactive(store.getters['entities/getService'](
  useCartItem(store).serviceId
))

const emits = defineEmits(['setOnSitePayment'])

function couponApplied () {
  emits(
    'setOnSitePayment',
    appointmentsAmount.value.prepaid.totalAmount - appointmentsAmount.value.prepaid.discountAmount + appointmentsAmount.value.prepaid.taxAmount <= 0
  )
}
onMounted(() => {
  isRecurring.value = appointmentsAmount.value.prepaid.count > 1 || appointmentsAmount.value.postpaid.count > 0

  bookingsCount.value = appointmentsAmount.value.prepaid.count + appointmentsAmount.value.postpaid.count

  store.commit('booking/setBookingsCount', bookingsCount.value)
})

// * Collapse visualisation
let recurringCollapseState = ref(true)
let extrasCollapseState = ref(true)

function onCollapseClose (el) {
  el.style.opacity = 0
  setTimeout(() => {
    el.style.opacity = 1
    el.style.height = 'var(--am-h-services-sub)'
  }, 200)
}

function onCollapseOpen (el) {
  el.style.opacity = 0
  el.style.setProperty('--am-h-services-sub', `${el.offsetHeight}px`)
  setTimeout(() => {
    el.style.height = '0px'
  }, 100)
}

let taxVisibility = computed(() => {
  return useTaxVisibility(store, service.id, 'service')
})

// * Colors
let amColors = inject('amColors')
let cssVars = computed(() => {
  return {
    '--am-c-pay-text': amColors.value.colorMainText,
    '--am-c-pay-text-op70': useColorTransparency(amColors.value.colorMainText, 0.7),
    '--am-c-pay-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-c-pay-text-op30': useColorTransparency(amColors.value.colorMainText, 0.3),
    '--am-c-pay-success': amColors.value.colorSuccess,
    '--am-c-pay-primary': amColors.value.colorPrimary,
  }
})
</script>


<script>
export default {
  name: 'AppointmentInfo'
}
</script>

<style lang="scss">
.amelia-v2-booking {
  #amelia-container {
    .am-fs__payments {
      &-bordered {
        border-top: 1px dashed var(--am-c-pay-text-op30);
      }

      &-app-info {
        margin: 0;

        &-subtotal {
          display: flex;
          justify-content: space-between;
          margin-top: 16px;

          span {
            font-size: 14px;
            font-weight: 500;
            line-height: 1.42857;
            color: var(--am-c-pay-text);
          }
        }

        &-discount, &-tax {
          display: flex;
          justify-content: space-between;
          font-size: 13px;
          font-weight: 400;
          line-height: 1.3846;
          color: var(--am-c-pay-text);
          padding: 8px 0 0;

          &-green {
            color: var(--am-c-pay-success);
          }
        }

        &-discount {
          &-green {
            color: var(--am-c-pay-success);
          }
        }

        &-total {
          margin-top: 8px;
        }

        &-total, &-deposit, &-remaining {
          display: flex;
          justify-content: space-between;
          font-size: 15px;
          font-weight: 500;
          line-height: 1.33333;
          color: var(--am-c-pay-text);
          padding: 8px 0 0;

          &.am-single-row {
            padding-top: 16px;
          }

          & > span:nth-child(2) {
            color: var(--am-c-pay-primary);
          }
        }
      }

      &-service, &-extra {
        border: 1px solid var(--am-c-pay-text-op30);
        border-radius: 8px;
        padding: 12px;
        box-sizing: border-box;
      }

      &-extra {
        margin: 8px 0;
      }

      &-services {
        .am-collapse-item__heading {
          flex-wrap: wrap;
          width: 100%;
          padding: 12px;
          transition-delay: .5s;

          &-side {
            transition-delay: 0s;
          }

          .am-collapse-item__trigger {
            padding: 0
          }
        }
        .am-collapse-item__content {
          & > * {
            padding: 12px;
          }
        }

        &-info {
          span {
            font-size: 12px;
            line-height: 1.33333;
            font-weight: 500;
            color: var(--am-c-pay-text-op60);
          }
        }

        &-sub {
          width: 100%;
          display: flex;
          justify-content: space-between;
          transition: all ease-in-out .3s;

          p {
            font-size: 13px;
            font-weight: 500;
            line-height: 1.384615;
            color: var(--am-c-pay-text);
            margin: 0;
            padding: 0;
          }
        }

        &-open {
          width: 100%;

          &-bordered {
            border-bottom: 1px dashed var(--am-c-pay-text-op30);
            margin-bottom: 10px;
            padding-bottom: 10px;
          }

          &-total {
            & > span {
              color: var(--am-c-pay-text-op70)
            }

            &:last-child {
              font-weight: 500;
            }
          }

          &-text {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            line-height: 1.384615;
            color: var(--am-c-pay-text);
          }
        }

      }

      .am-amount {
        white-space: nowrap;
      }

      .am-fade-enter-active,
      .am-fade-leave-active {
        transition: opacity 0.5s ease;
      }

      .am-fade-enter-from,
      .am-fade-leave-to {
        opacity: 0;
      }
    }
  }
}
</style>
