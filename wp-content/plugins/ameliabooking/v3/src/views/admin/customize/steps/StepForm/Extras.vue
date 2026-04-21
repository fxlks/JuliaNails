<template>
  <div class="am-fs__extras" :style="cssVars">
    <div class="am-fs__extras-heading">
      <span
        v-if="extrasOptions.heading.visibility"
        class="am-fs__extras-heading-main"
      >
        {{ labelsDisplay('extras_available') }}
      </span>
      <span class="am-fs__extras-heading-required am-error-text">
        {{ `${labelsDisplay('min_req_extras_colon')} 2` }}
      </span>
    </div>
    <div class="am-fs__extras-main">
      <AmCollapse>
        <AmCollapseItem
          v-for="extra in extrasArray"
          :key="extra.id"
          :button-closed="labelsDisplay('extras_card_open')"
          :button-opened="labelsDisplay('extras_card_close')"
        >
          <template #heading>
            <div class="am-fs__extras-card__header">
              <span class="am-fs__extras-card__header-left">
                {{ extra.name }}
              </span>
              <span class="am-fs__extras-card__header-right">
                <span class="card-text">
                  {{ useFormattedPrice(extra.price) }}
                </span>
                <AmInputNumber
                  v-model="extra.quantity"
                  :max="extra.maxQuantity"
                  :min="0"
                  size="small"
                />
              </span>
            </div>
          </template>
          <template #default>
            <div
              v-if="
                extrasOptions.description.visibility ||
                extrasOptions.duration.visibility
              "
              class="am-fs__extras-card__content"
              :style="cssVars"
            >
              <div
                v-if="extra.description && extrasOptions.description.visibility"
                class="am-fs__extras-card__content-main"
              >
                {{ extra.description }}
              </div>
              <div
                v-if="extrasOptions.duration.visibility"
                class="am-fs__extras-card__content-sub"
              >
                {{ `${labelsDisplay('duration_colon')} 60 min` }}
              </div>
            </div>
          </template>
        </AmCollapseItem>
      </AmCollapse>
    </div>
  </div>
</template>

<script setup>
// * Components
import AmCollapse from '../../../../_components/collapse/AmCollapse.vue'
import AmCollapseItem from '../../../../_components/collapse/AmCollapseItem.vue'
import AmInputNumber from '../../../../_components/input-number/AmInputNumber.vue'
// * Import from Vue
import { ref, inject, computed } from 'vue'
// * Composables
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation.js'
import { useFormattedPrice } from '../../../../../assets/js/common/formatting'
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

function labelsDisplay(label) {
  let computedLabel = computed(() => {
    let translations =
      amCustomize.value[pageRenderKey.value].extrasStep.translations
    return translations &&
      translations[label] &&
      translations[label][langKey.value]
      ? translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

let extrasOptions = computed(() => {
  return amCustomize.value[pageRenderKey.value].extrasStep.options
})

let extrasArray = ref([
  {
    name: 'Extra 1',
    price: '5',
    quantity: 0,
    maxQuantity: 5,
    description:
      "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.",
  },
  {
    name: 'Extra 2',
    price: '3',
    quantity: 0,
    maxQuantity: 5,
    description:
      "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.",
  },
  {
    name: 'Extra 3',
    price: '7',
    quantity: 0,
    maxQuantity: 5,
    description:
      "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.",
  },
])

// * Colors
let amColors = inject('amColors')
let cssVars = computed(() => {
  return {
    '--am-c-extras-text': amColors.value.colorMainText,
    '--am-c-extras-text-op80': useColorTransparency(
      amColors.value.colorMainText,
      0.8
    ),
  }
})
</script>

<script>
export default {
  name: 'ExtrasStep',
  key: 'extrasStep',
  sidebarData: {
    label: 'extras',
    icon: 'border-plus',
    stepSelectedData: [],
    finished: false,
    selected: false,
  },
}
</script>

<style lang="scss">
#amelia-app-backend-new {
  #amelia-container {
    .am-fs__extras {
      --am-c-extras-text: var(--am-c-main-text);

      * {
        font-family: var(--am-font-family);
        box-sizing: border-box;
      }

      &-heading {
        display: flex;
        flex-direction: column;
        margin-bottom: 4px;

        & > * {
          $count: 3;
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

        &-main {
          display: block;
          font-size: var(--am-fs-label);
          font-weight: 500;
          line-height: 1.6;
          color: var(--am-c-extras-text);
          margin-bottom: 4px;
        }

        &-sub {
          display: block;
          font-size: 13px;
          font-weight: 400;
          line-height: 1.3846;
          color: var(--am-c-extras-text-op80);
          margin-bottom: 4px;
        }

        &-required {
          display: block;
          font-size: 13px;
          font-weight: 400;
          line-height: 1.3846;
          color: var(--am-c-extras-text);
          margin-bottom: 4px;

          &.am-error-text {
            color: var(--am-c-error);
          }
        }
      }

      &-main {
        .am-collapse-item {
          $count: 3;
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
      }

      &-card {
        &__header {
          width: 100%;
          display: flex;
          align-items: center;
          justify-content: space-between;
          font-size: var(--am-fs-label);
          line-height: 1.6;

          &-left {
            display: flex;
            align-items: center;
            justify-content: left;
            font-size: var(--am-fs-label);
            line-height: 1.6;
            color: var(--am-c-extras-text);
          }

          &-right {
            display: flex;
            align-items: center;
            justify-content: right;
            font-size: var(--am-fs-label);
            line-height: 1.6;

            .card-text {
              font-size: var(--am-fs-label);
              font-weight: 500;
              line-height: 1.6;
              color: var(--am-c-primary);
              margin-right: 16px;
            }
          }

          .am-input-number {
            max-width: 100px;
          }
        }

        &__content {
          font-size: 14px;
          font-weight: 400;
          line-height: 1.4285;

          &-main {
            font-size: 14px;
            font-weight: 400;
            line-height: 1.4285;
            margin-bottom: 16px;
            color: var(--am-c-extras-text-op80);
          }

          &-sub {
            font-size: 14px;
            font-weight: 400;
            line-height: 1.4285;
            color: var(--am-c-extras-text-op80);
          }
        }
      }
    }
  }
}
</style>
