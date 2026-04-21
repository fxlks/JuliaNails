<template>
  <div class="am-pei" :style="cssVars">
    <div class="am-pei__inner">
      <AmCollapse v-if="features.tickets" class="am-pei__segment">
        <AmCollapseItem
          :side="true"
          @collapse-open="segmentCollapseState = false"
          @collapse-close="segmentCollapseState = true"
        >
          <template #heading>
            <div class="am-pei__segment-info">
              <span>
                {{ props.customizedLabels.summary_event }}
              </span>
            </div>
          </template>
          <template #icon-below>
            <Transition
              :duration="{ enter: 500, leave: 500 }"
              @enter="onCollapseClose"
              @leave="onCollapseOpen"
            >
              <div v-show="segmentCollapseState" class="am-pei__segment-sub">
                <p>
                  {{
                    `${props.selectedItem.name} x ${eventPersonsNumber} ${
                      eventPersonsNumber > 1
                        ? props.customizedLabels.summary_persons
                        : props.customizedLabels.summary_person
                    }`
                  }}
                </p>
                <p class="am-amount">
                  {{ useFormattedPrice(eventSubtotalPrice) }}
                </p>
              </div>
            </Transition>
          </template>
          <template #default>
            <div class="am-pei__segment-open">
              <template v-for="ticket in tickets" :key="ticket.id">
                <div v-if="ticket.persons" class="am-pei__segment-open__text">
                  <span>
                    {{ ticket.name }} ({{ useFormattedPrice(ticket.price) }})
                    {{
                      `x ${ticket.persons} ${
                        ticket.persons > 1
                          ? props.customizedLabels.summary_persons
                          : props.customizedLabels.summary_person
                      }`
                    }}
                  </span>
                  <span class="am-amount">
                    {{ useFormattedPrice(ticket.price * ticket.persons) }}
                  </span>
                </div>
              </template>

              <div class="am-pei__segment-sub">
                <p>
                  {{ props.selectedItem.name }}
                </p>
                <p class="am-amount">
                  {{ useFormattedPrice(eventSubtotalPrice) }}
                </p>
              </div>
            </div>
          </template>
        </AmCollapseItem>
      </AmCollapse>
      <div
        v-else
        class="am-pei__segment-wrapper"
      >
        <div class="am-pei__segment">
          <div class="am-pei__segment-info">
          <span>
            {{ labelsDisplay('summary_event') }}
          </span>
          </div>
          <div class="am-pei__segment-open">
            <div class="am-pei__segment-sub">
              <p>
                {{ `${props.selectedItem.name} (${useFormattedPrice(10)})`}}
                <span>
                  {{ `x 1 ${labelsDisplay('summary_person')}` }}
                </span>
              </p>
              <p class="am-amount"> {{ useFormattedPrice(10) }} </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="am-pei__info">
      <div v-if="features.coupons" class="am-pei__info-subtotal">
        <span>{{ props.customizedLabels.subtotal }}:</span>
        <span class="am-amount">
          {{ useFormattedPrice(eventSubtotalPrice) }}
        </span>
      </div>

      <el-form v-if="features.coupons" ref="couponFormRef" :rules="rules" :model="couponFormData">
        <div class="am-fs__coupon">
          <el-form-item :prop="'coupon'" class="am-fs__coupon-form-item">
            <template #label>
              <span>{{ `${labelsDisplay('coupon')}:` }}</span>
            </template>
            <AmInput
              v-model="couponFormData.coupon"
              size="small"
              :prefix-icon="IconCoupon"
            />
          </el-form-item>
          <AmButton size="small">
            {{ labelsDisplay('add_coupon_btn') }}
          </AmButton>
        </div>
      </el-form>

      <div
        v-if="features.coupons"
        class="am-pei__info-discount am-pei__info-discount-green"
      >
        <span>
          {{ `${props.customizedLabels.discount_amount_colon}:` }}
        </span>
        <span class="am-amount">
          {{ useFormattedPrice(discount) }}
        </span>
      </div>

      <div
        v-if="taxVisibility && amSettings.payments.taxes.excluded"
        class="am-pei__info-tax"
      >
        <span>
          {{ `${props.customizedLabels.total_tax_colon}:` }}
        </span>
        <span class="am-amount">
          {{ useFormattedPrice(10) }}
        </span>
      </div>

      <div class="am-pei__info-total" :class="{ 'am-single-row': !discount }">
        <span>{{ props.customizedLabels.total_amount_colon }}</span>
        <span class="am-amount">
          {{ useFormattedPrice(totalAmount) }}
          <template v-if="taxVisibility && !amSettings.payments.taxes.excluded">
            {{ labelsDisplay('incl_tax') }}
          </template>
        </span>
      </div>

      <template v-if="paymentGateway !== 'onSite' && features.depositPayment">
        <div class="am-pei__info-total">
          <span> {{ props.customizedLabels.paying_now }}: </span>
          <span class="am-amount">
            {{
              paymentDeposit
                ? useFormattedPrice(totalAmount)
                : useFormattedPrice(depositAmount)
            }}
          </span>
        </div>

        <div class="am-pei__info-total">
          <span> {{ props.customizedLabels.paying_later }}: </span>
          <span class="am-amount">
            {{
              !paymentDeposit
                ? useFormattedPrice(totalAmount - depositAmount)
                : useFormattedPrice(0)
            }}
          </span>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
// * _components
import AmCollapse from '../../../../../../../_components/collapse/AmCollapse.vue'
import AmCollapseItem from '../../../../../../../_components/collapse/AmCollapseItem.vue'
import IconCoupon from '../../../../../../../_components/icons/IconCoupon.vue'
import AmInput from '../../../../../../../_components/input/AmInput.vue'
import AmButton from '../../../../../../../_components/button/AmButton.vue'

// * Import from Vue
import { ref, computed, inject } from 'vue'

// * Composables
import { useFormattedPrice } from '../../../../../../../../assets/js/common/formatting'
import { useColorTransparency } from '../../../../../../../../assets/js/common/colorManipulation'
import { useReactiveCustomize } from '../../../../../../../../assets/js/admin/useReactiveCustomize.js'

let props = defineProps({
  bookableType: {
    type: String,
    default: 'appointment',
  },
  paymentGateway: {
    type: String,
    default: 'onSite',
  },
  selectedItem: {
    type: Object,
    required: true,
  },
  customizedLabels: {
    type: Object,
    required: true,
  },
})

// * Root Settings
let amSettings = inject('settings')

// * Features
let features = inject('features')

// * Plugin Licence
let licence = inject('licence')

// * Event Tickets
let tickets = ref([
  {
    id: 1,
    eventTicketId: 1,
    name: 'Ticket one',
    persons: 3,
    price: 10,
    sold: 0,
    spots: 10,
  },
  {
    id: 2,
    eventTicketId: 2,
    name: 'Ticket two',
    persons: 1,
    price: 20,
    sold: 0,
    spots: 10,
  },
  {
    id: 3,
    eventTicketId: 3,
    name: 'Ticket three',
    persons: 7,
    price: 30,
    sold: 0,
    spots: 10,
  },
])

// * Total event persons
let eventPersonsNumber = ref(0)

// * Subtotal price
let eventSubtotalPrice = computed(() => {
  let price = 0
  tickets.value.forEach((t) => {
    price += t.price * t.persons
    eventPersonsNumber.value += t.persons
  })
  return price
})

let couponFormRef = ref(null)

let couponFormData = ref({
  coupon: '',
})

// * Form validation rules
let rules = computed(() => {
  return {
    coupon: [
      {
        required:
          amCustomize.value[pageRenderKey.value].payment.options.coupon
            .required,
        trigger: ['blur', 'change'],
      },
    ],
  }
})

// * Discount
let discount = ref(48)

// * Total Amount
let totalAmount = computed(() => {
  return eventSubtotalPrice.value - discount.value
})

// * Collapse visualisation
let segmentCollapseState = ref(true)

// * Payment Deposit
let paymentDeposit = ref(false)

// * Payment Deposit Amount
let depositAmount = ref(56)

function onCollapseClose(el) {
  el.style.opacity = 0
  setTimeout(() => {
    el.style.opacity = 1
    el.style.height = 'var(--am-h__part-sub)'
  }, 200)
}

function onCollapseOpen(el) {
  el.style.opacity = 0
  el.style.setProperty('--am-h__part-sub', `${el.offsetHeight}px`)
  setTimeout(() => {
    el.style.height = '0px'
  }, 100)
}

let taxVisibility = computed(() => {
  return (
    amSettings.payments?.taxes?.enabled && !licence.isStarter && !licence.isLite
  )
})

// * Customize Object
const { amCustomize } = useReactiveCustomize()

// * Form string recognition
let pageRenderKey = inject('pageRenderKey')

// * Form step string recognition
let stepName = inject('stepName')

// * Language string recognition
let langKey = inject('langKey')

// * Labels
let amLabels = inject('labels')

function labelsDisplay(label) {
  let computedLabel = computed(() => {
    let translations =
      amCustomize.value[pageRenderKey.value][stepName.value].translations
    return translations &&
      translations[label] &&
      translations[label][langKey.value]
      ? translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Fonts
let amFonts = inject('amFonts')

// * Colors
let amColors = inject('amColors')

// * Css Variables
let cssVars = computed(() => {
  return {
    '--am-font-family': amFonts.value.fontFamily,
    '--am-c-pay-text': amColors.value.colorMainText,
    '--am-c-pay-text-op70': useColorTransparency(
      amColors.value.colorMainText,
      0.7
    ),
    '--am-c-pay-text-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
    '--am-c-pay-text-op30': useColorTransparency(
      amColors.value.colorMainText,
      0.3
    ),
    '--am-c-pay-text-op20': useColorTransparency(
      amColors.value.colorMainText,
      0.2
    ),
    '--am-c-pay-border': useColorTransparency(amColors.value.colorInpBorder),
    '--am-c-pay-success': amColors.value.colorSuccess,
    '--am-c-pay-primary': amColors.value.colorPrimary,
  }
})
</script>

<style lang="scss">
// pei - payment even info
#amelia-app-backend-new #amelia-container {
  .am-pei {
    &__info {
      margin: 0;

      .am-fs__coupon {
        width: 100%;
        display: flex;
        padding: 0;
        margin: 16px 0 0;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.42857;
        gap: 4px;
        color: var(--am-c-ps-text);
        white-space: nowrap;
        align-items: center;

        .el-form-item {
          display: flex;
          gap: 5px;
          align-items: center;
          margin-bottom: 0;
          width: 100%;
          .el-form-item__error {
            display: none;
          }
        }
      }

      &-subtotal {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px dashed var(--am-c-pay-text-op30);
        padding: 0 0 16px;
        margin: 16px 0 0;

        span {
          font-size: 14px;
          font-weight: 500;
          line-height: 1.42857;
          color: var(--am-c-pay-text);
        }
      }

      &-discount,
      &-tax {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 400;
        line-height: 1.3846;
        color: var(--am-c-pay-text);
        padding: 16px 0 0;

        &-green {
          color: var(--am-c-pay-success);
        }
      }

      &-total {
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

    &-service {
      border: 1px solid var(--am-c-pay-text-op30);
      border-radius: 8px;
      padding: 12px;
      box-sizing: border-box;
    }

    &__segment {
      &-wrapper {
        border: 1px solid var(--am-c-pay-border);
        padding: 12px;
        border-radius: 8px;
      }

      .am-collapse-item__heading {
        flex-wrap: wrap;
        width: 100%;
        padding: 12px;
        transition-delay: 0.5s;

        &-side {
          transition-delay: 0s;
        }

        .am-collapse-item__trigger {
          padding: 0;
        }
      }

      .am-collapse-item__content {
        & > * {
          padding: 8px 12px;
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
        transition: all ease-in-out 0.3s;

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

        &__total {
          border-top: 1px dashed var(--am-c-pay-text-op30);
          padding: 12px 0 0;
          margin: 12px 0 4px;

          & > span {
            color: var(--am-c-pay-text-op70);
          }

          &:last-child {
            font-weight: 500;
          }
        }

        &__text {
          display: flex;
          justify-content: space-between;
          font-size: 13px;
          line-height: 1.384615;
          color: var(--am-c-pay-text);
          border-bottom: 1px dashed var(--am-c-pay-text-op20);

          span {
            display: inline-flex;
            font-size: 13px;
            font-weight: 400;
            line-height: 1.53846;
            padding: 4px 0;
            color: var(--am-c-pay-text);
          }

          .am-amount {
            font-weight: 500;
          }

          &:first-child {
            span {
              padding: 0 0 4px;
            }
          }

          &:nth-last-child(2) {
            border-bottom: none;
          }
        }

        .am-pei__segment-sub {
          padding: 4px 0 0;
          margin-top: 4px;
          border-top: 1px solid var(--am-c-pay-text-op20);
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
</style>
