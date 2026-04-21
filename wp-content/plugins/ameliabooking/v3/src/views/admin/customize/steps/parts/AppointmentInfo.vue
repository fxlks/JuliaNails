<template>
  <div class="am-fs__payments-wrapper" :style="cssVars">
    <div class="am-fs__payments">
      <AmCollapse class="am-fs__payments-services">
        <AmCollapseItem
          :side="true"
          @collapse-open="recurringCollapseState = false"
          @collapse-close="recurringCollapseState = true"
        >
          <template #heading>
            <div class="am-fs__payments-services-info">
              <span>{{ labelsDisplay('summary_services') }}</span>
            </div>
          </template>
          <template #icon-below>
            <Transition
              :duration="{ enter: 500, leave: 500 }"
              @enter="onCollapseClose"
              @leave="onCollapseOpen"
            >
              <div
                v-show="recurringCollapseState"
                class="am-fs__payments-services-sub"
              >
                <p>{{ labelsDisplay('summary_services_subtotal') }}</p>
                <p>1000.00$</p>
              </div>
            </Transition>
          </template>
          <template #default>
            <div class="am-fs__payments-services-open">
              <div>
                <span>
                  Service 1 (125.00$) x 4
                  {{ labelsDisplay('summary_persons') }}</span
                >
                <span> 500.00$ </span>
              </div>

              <div
                v-if="features.recurringAppointments"
                style="color: #4d5b63"
                class="am-fs__payments-services-open-total"
              >
                <span
                  >2 {{ labelsDisplay('summary_recurrences') }} x Service 1
                  (125.00$) x 4 {{ labelsDisplay('summary_persons') }}</span
                >
              </div>

              <div style="font-weight: 500">
                <span>{{ labelsDisplay('summary_services_subtotal') }}</span>
                <span>1000.00$</span>
              </div>
            </div>
          </template>
        </AmCollapseItem>
      </AmCollapse>

      <AmCollapse v-if="features.extras" class="am-fs__payments-services">
        <AmCollapseItem
          :side="true"
          @collapse-open="extrasCollapseState = false"
          @collapse-close="extrasCollapseState = true"
        >
          <template #heading>
            <div class="am-fs__payments-services-info">
              <span>{{ labelsDisplay('summary_extras') }}</span>
            </div>
          </template>
          <template #icon-below>
            <Transition
              :duration="{ enter: 500, leave: 500 }"
              @enter="onCollapseClose"
              @leave="onCollapseOpen"
            >
              <div
                v-show="extrasCollapseState"
                class="am-fs__payments-services-sub"
              >
                <p>{{ labelsDisplay('summary_extras_subtotal') }}</p>
                <p>224.00$</p>
              </div>
            </Transition>
          </template>
          <template #default>
            <div class="am-fs__payments-services-open">
              <div v-for="extra in extras" :key="extra.extraId">
                <span
                  >x {{ extra.times }} {{ extra.name }} ({{
                    useFormattedPrice(extra.price)
                  }}) <span>x 4 {{ labelsDisplay('summary_persons') }}</span>
                </span>
                <span>{{ extra.sum }}</span>
              </div>

              <div class="am-fs__payments-services-open-total">
                <span
                  >2 {{ labelsDisplay('summary_recurrences') }} x
                  {{ labelsDisplay('summary_extras') }} ( 112.00$ )</span
                >
              </div>

              <div class="am-fs__payments-services-open-total">
                <span>{{ labelsDisplay('summary_extras_subtotal') }}</span>
                <span>224.00$</span>
              </div>
            </div>
          </template>
        </AmCollapseItem>
      </AmCollapse>
    </div>

    <div class="am-fs__payments-app-info">
      <div v-if="features.coupons" class="am-fs__payments-app-info-subtotal">
        <span>{{ labelsDisplay('subtotal') }}:</span>
        <span>1124.00$</span>
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
        class="am-fs__payments-app-info-discount am-fs__payments-app-info-discount-green"
      >
        <span>{{ labelsDisplay('discount_amount_colon') }}:</span>
        <span>142.40$</span>
      </div>

      <div class="am-fs__payments-app-info-total">
        <span>{{ labelsDisplay('total_amount_colon') }}:</span>
        <span>1081.60$</span>
      </div>

      <div v-if="props.paymentGateway !== 'onSite' && features.depositPayment">
        <div class="am-fs__payments-app-info-total">
          <span>{{ labelsDisplay('paying_now') }}:</span>
          <span>200.00$</span>
        </div>

        <div class="am-fs__payments-app-info-total">
          <span>{{ labelsDisplay('paying_later') }}:</span>
          <span>881.00$</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import AmInput from '../../../../_components/input/AmInput.vue'
import AmButton from '../../../../_components/button/AmButton.vue'
import AmCollapse from '../../../../_components/collapse/AmCollapse'
import AmCollapseItem from '../../../../_components/collapse/AmCollapseItem'
import IconCoupon from '../../../../_components/icons/IconCoupon.vue'
import { computed, inject, ref } from 'vue'
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation'
import { useFormattedPrice } from '../../../../../assets/js/common/formatting'
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

// * Features
let features = inject('features')

let props = defineProps({
  bookableType: {
    type: String,
    default: 'appointment',
  },
  paymentGateway: {
    type: String,
    default: 'onSite',
  },
})

let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

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
          amCustomize.value[pageRenderKey.value].paymentStep.options.coupon
            .required,
        trigger: ['blur', 'change'],
      },
    ],
  }
})

let extras = [
  {
    name: 'Extra 1',
    price: '3.00$',
    times: '1',
    sum: '12.00$',
  },
  {
    name: 'Extra 2',
    price: '5.00$',
    times: '5',
    sum: '100.00$',
  },
]

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

// * Collapse visualisation
let recurringCollapseState = ref(true)
let extrasCollapseState = ref(true)

function onCollapseClose(el) {
  el.style.opacity = 0
  setTimeout(() => {
    el.style.opacity = 1
    el.style.height = 'var(--am-h-services-sub)'
  }, 200)
}

function onCollapseOpen(el) {
  el.style.opacity = 0
  el.style.setProperty('--am-h-services-sub', `${el.offsetHeight}px`)
  setTimeout(() => {
    el.style.height = '0px'
  }, 100)
}

// * Colors
let amColors = inject('amColors')
let cssVars = computed(() => {
  return {
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
    '--am-c-pay-success': amColors.value.colorSuccess,
    '--am-c-pay-primary': amColors.value.colorPrimary,
  }
})
</script>

<script>
export default {
  name: 'AppointmentInfo',
}
</script>

<style lang="scss">
#amelia-app-backend-new {
  #amelia-container {
    .am-fs__payments {
      &-app-info {
        margin: 0;

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

        &-discount {
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

      &-services {
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

        &-open {
          width: 100%;

          &-total {
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

          & > div {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 400;
            line-height: 1.384615;
            color: var(--am-c-pay-text);
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

        & > div:first-child {
          margin-top: 0;
        }
      }
    }

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

      .am-icon-info-reverse,
      .am-icon-check {
        font-size: 18px;
      }

      .am-icon-info-reverse {
        /* $red-900 */
        color: var(--am-c-error);
      }

      .am-icon-check {
        /* $green-900 */
        position: absolute;
        width: 20px;
        height: 20px;
        background-color: var(--am-c-success);
        border-radius: 50%;
        color: var(--am-c-inp-bgr);

        &:before {
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
        }
      }

      & > span {
        margin-right: 3px;
      }

      &-invalid {
        input {
          border: 1.5px solid var(--am-c-error);
        }
      }

      .el-icon {
        height: 100%;
      }
    }

    &.am-fs__wrapper {
      .am-fs__coupon {
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
    }

    .am-fs__coupon-discount {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      font-weight: 400;
      line-height: 1.42857;
      color: var(--am-c-ps-text);
      margin-bottom: 12px;

      & span:nth-child(2) {
        font-weight: 500;
        /* $green-900 */
        color: var(--am-c-success);
      }
    }
  }
}
</style>
