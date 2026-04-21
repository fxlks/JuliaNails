<template>
  <div v-if="props.data" class="am-fs__ci" :style="cssVars">
    <div class="am-fs__ci-main">
      <!-- Service -->
      <div class="am-fs__ci-block">
        <div class="am-fs__ci-title">
          {{ labelsDisplay('service_colon', 'cartStep') }}
        </div>
        <div class="am-fs__ci-prod">
          <div class="am-fs__ci-prod__title">
            <span class="am-fs__ci-prod__title-name">
              {{ data.service.name }}
            </span>
            <span class="am-fs__ci-prod__title-price">
              {{ `(${useFormattedPrice(data.service.price)})` }}
            </span>
            <span class="am-fs__ci-prod__title-number">
              {{
                `x ${data.service.persons} ${
                  data.service.persons === 1
                    ? labelsDisplay('summary_person', 'cartStep')
                    : labelsDisplay('summary_persons', 'cartStep')
                }`
              }}
            </span>
          </div>
          <div class="am-fs__ci-prod__price">
            {{ useFormattedPrice(serviceAggregatedPrice) }}
          </div>
        </div>
        <div class="am-fs__ci-cost">
          <div class="am-fs__ci-cost__title">
            {{ labelsDisplay('summary_services_subtotal', 'cartStep') }}
          </div>
          <div class="am-fs__ci-cost__price">
            {{ useFormattedPrice(serviceAggregatedPrice) }}
          </div>
        </div>
      </div>
      <!-- /Service -->

      <!-- Extras -->
      <div v-if="data.extras" class="am-fs__ci-block">
        <div class="am-fs__ci-title">
          {{ labelsDisplay('extras', 'cartStep') }}
        </div>
        <div
          v-for="(extraData, extraIndex) in data.extras"
          :key="extraIndex"
          class="am-fs__ci-prod"
        >
          <div class="am-fs__ci-prod__title">
            <span class="am-fs__ci-prod__title-name">
              {{ extraData.name }}
            </span>
            <span v-if="extraData.price" class="am-fs__ci-prod__title-price">
              {{ `(${useFormattedPrice(extraData.price)})` }}
            </span>
            <span class="am-fs__ci-prod__title-number">
              {{ ` x ${extraData.quantity}` }}
            </span>
            <span class="am-fs__ci-prod__title-number">
              {{
                ` x ${data.service.persons} ${
                  data.service.persons === 1
                    ? labelsDisplay('summary_person', 'cartStep')
                    : labelsDisplay('summary_persons', 'cartStep')
                }`
              }}
            </span>
          </div>
          <div class="am-fs__ci-prod__price">
            {{ useFormattedPrice(extraAggregatedPrice(extraData)) }}
          </div>
        </div>
        <div class="am-fs__ci-cost">
          <div class="am-fs__ci-cost__title">
            {{ labelsDisplay('summary_extras_subtotal', 'cartStep') }}
          </div>
          <div class="am-fs__ci-cost__price">
            {{ useFormattedPrice(extrasTotalPrice()) }}
          </div>
        </div>
      </div>
      <!-- /Extras -->

      <!-- Total -->
      <div class="am-fs__ci-block">
        <div class="am-fs__ci-cost">
          <div class="am-fs__ci-cost__title">
            {{ labelsDisplay('total_price', 'cartStep') }}
          </div>
          <div class="am-fs__ci-cost__price">
            {{ useFormattedPrice(serviceAggregatedPrice + extrasTotalPrice()) }}
          </div>
        </div>
      </div>
      <!-- /Total -->
    </div>

    <div class="am-fs__ci-info">
      <div class="am-fs__ci-title">
        {{ labelsDisplay('info', 'cartStep') }}
      </div>
      <div class="am-fs__ci-info__el">
        <span class="am-icon-calendar"></span>
        <span>
          {{ getFrontedFormattedDate(props.data.service.date) }}
        </span>
      </div>
      <div class="am-fs__ci-info__el">
        <span class="am-icon-clock"></span>
        <span>
          {{
            `${getFrontedFormattedTime(
              props.data.service.time
            )} - ${getFrontedFormattedTime(
              addSeconds(props.data.service.time, duration)
            )}`
          }}
        </span>
      </div>
      <div class="am-fs__ci-info__el">
        <span class="am-icon-user"></span>
        <span>
          {{
            `${props.data.service.employee.firstName} ${props.data.service.employee.lastName}`
          }}
        </span>
      </div>
      <div class="am-fs__ci-info__el">
        <span class="am-icon-date-time"></span>
        <span>
          {{ useSecondsToDuration(duration, amLabels.h, amLabels.min) }}
        </span>
      </div>
      <div class="am-fs__ci-info__el">
        <span class="am-icon-locations"></span>
        <span>
          {{ props.data.service.location.name }}
        </span>
      </div>
      <div class="am-fs__ci-info__el">
        <span class="am-icon-users-plus"></span>
        <span>
          {{
            `${props.data.service.persons} / ${props.data.service.maxCapacity}`
          }}
        </span>
      </div>
    </div>

    <div class="am-fs__ci-manage">
      <div class="am-fs__ci-manage__discard">
        <span class="am-icon-bucket"></span>
        <span>{{ labelsDisplay('delete', 'cartStep') }}</span>
      </div>
      <div class="am-fs__ci-manage__edit">
        <span>{{ labelsDisplay('edit', 'cartStep') }}</span>
        <span class="am-icon-edit"></span>
      </div>
    </div>
  </div>
</template>

<script setup>
// * Import from Vue
import { computed, inject, ref } from 'vue'

// * Composables
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation'
import { useFormattedPrice } from '../../../../../assets/js/common/formatting'
import {
  addSeconds,
  useSecondsToDuration,
  getFrontedFormattedTime,
  getFrontedFormattedDate,
} from '../../../../../assets/js/common/date'
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

let props = defineProps({
  data: {
    type: Object,
    default: null,
  },
  index: {
    type: Number,
    default: 0,
  },
  size: {
    type: Number,
    default: 0,
  },
  globalClass: {
    type: String,
    default: '',
  },
})

// * Features
let features = inject('features')

// * Multi lingual
let langKey = inject('langKey')

// * Component labels
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

// * Label computed function
function labelsDisplay(label, stepKey) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value][stepKey].translations &&
      amCustomize.value[pageRenderKey.value][stepKey].translations[label] &&
      amCustomize.value[pageRenderKey.value][stepKey].translations[label][
        langKey.value
      ]
      ? amCustomize.value[pageRenderKey.value][stepKey].translations[label][
          langKey.value
        ]
      : amLabels[label]
  })

  return computedLabel.value
}

/**************
 * Navigation *
 *************/

let serviceAggregatedPrice = computed(() => {
  return props.data.service.price * props.data.service.persons
})

function extraAggregatedPrice(extra) {
  return extra.price * extra.quantity * props.data.service.persons
}

function extrasTotalPrice() {
  let price = 0
  if (!features.extras) return price

  props.data.extras.forEach((extra) => {
    price += extraAggregatedPrice(extra)
  })

  return price
}

let duration = ref(
  props.data.service.duration +
  (features.extras ? props.data.extras.reduce((accumulator, extra) => {
      return accumulator + extra.duration * extra.quantity
    }, 0) : 0)
)

let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    // am - amelia
    // c  - color
    // ci - cart item
    // op - opacity
    '--am-c-ci-text': amColors.value.colorMainText,
    '--am-c-ci-text-op80': useColorTransparency(
      amColors.value.colorMainText,
      0.8
    ),
    '--am-c-ci-text-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
    '--am-c-ci-text-op30': useColorTransparency(
      amColors.value.colorMainText,
      0.3
    ),
    '--am-c-ci-text-op05': useColorTransparency(
      amColors.value.colorMainText,
      0.05
    ),
    '--am-c-ci-success': amColors.value.colorSuccess,
    '--am-c-ci-primary': amColors.value.colorPrimary,
    '--am-c-ci-error': amColors.value.colorError,
  }
})
</script>

<script>
export default {
  name: 'CartItemBody',
}
</script>

<style lang="scss">
#amelia-app-backend-new #amelia-container {
  // am - amelia
  // fs - form step
  // ci - cart item
  .am-fs__ci {
    width: 100%;
    padding: 12px;

    * {
      word-break: break-word;
    }

    &-block {
      padding: 0 0 12px;
      margin: 0 0 12px;
      border-bottom: 1px dashed var(--am-c-ci-text-op30);

      &:last-of-type {
        border: none;
        margin: 0;
      }
    }

    &-title {
      width: 100%;
      font-size: 12px;
      font-weight: 500;
      line-height: 1.333333;
      margin: 0 0 8px;
      color: var(--am-c-ci-text-op60);
    }

    &-prod {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 0 0 8px;

      &__title {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        color: var(--am-c-ci-text);

        * {
          display: block;
          font-size: 13px;
          line-height: 1.38462;
          font-weight: 400;
          margin-right: 4px;
          color: var(--am-c-ci-text);
        }
      }

      &__price {
        flex: 0 0 auto;
        align-self: flex-start;
        font-size: 13px;
        line-height: 1.38462;
        font-weight: 400;
        color: var(--am-c-ci-text);
      }
    }

    &-cost {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 12px 0 0;

      * {
        font-size: 13px;
        line-height: 1.38462;
        font-weight: 500;
        color: var(--am-c-ci-text);
      }
    }

    &-info {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      padding: 12px;
      background: var(--am-c-ci-text-op05);
      border-radius: 8px;

      &__el {
        display: flex;
        align-items: center;
        margin: 0 10px 0 0;

        span {
          display: block;
          font-size: 13px;
          font-weight: 400;
          line-height: 1.38462;
          color: var(--am-c-ci-text-op80);

          &[class*='am-icon'] {
            font-size: 24px;
            line-height: 1;
            color: var(--am-c-ci-primary);
          }
        }
      }
    }

    &-manage {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      margin: 14px 0 0;

      div {
        display: flex;
        align-items: center;
        padding: 2px 6px;
      }

      span {
        display: block;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.42857;
        cursor: pointer;

        &[class*='am-icon'] {
          font-size: 24px;
          line-height: 1;
        }
      }

      &__discard {
        color: var(--am-c-ci-error);
      }

      &__edit {
        color: var(--am-c-ci-primary);
      }
    }
  }
}
</style>
