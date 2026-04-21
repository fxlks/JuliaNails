<template>
  <div ref="ameliaContainer" class="am-fcl" :style="cssVars">
    <template v-for="category in categoriesList" :key="category.id">
      <div class="am-fcl__item" :class="itemWidth">
        <div class="am-fcl__item-inner">
          <div
            class="am-fcl__item-content"
            :style="
              customizeOptions.cardSideColor.visibility
                ? {
                    borderLeft: '7px solid',
                    borderLeftColor: category.color,
                  }
                : {}
            "
          >
            <div
              class="am-fcl__item-heading"
              :style="
                customizeOptions.cardColor.visibility
                  ? {
                      backgroundColor: useColorTransparency(
                        category.color,
                        0.1
                      ),
                    }
                  : {}
              "
            >
              <div class="am-fcl__item-name">
                {{ category.name }}
              </div>
              <div class="am-fcl__item-segments">
                <div
                  v-if="customizeOptions.services.visibility"
                  class="am-fcl__item-segments__item"
                >
                  <span
                    class="am-fcl__item-segments__item-icon am-icon-service"
                  ></span>
                  <span class="am-fcl__item-segments__item-count">
                    {{ category.serviceList.length }}
                  </span>
                </div>
                <div
                  v-if="features.packages && customizeOptions.packages.visibility"
                  class="am-fcl__item-segments__item"
                >
                  <span
                    class="am-fcl__item-segments__item-icon am-icon-shipment"
                  ></span>
                  <span class="am-fcl__item-segments__item-count">
                    {{ category.packageList.length }}
                  </span>
                </div>
              </div>
            </div>
            <div class="am-fcl__item-footer">
              <AmButton
                class="am-fcl__item-btn"
                size="mini"
                category="secondary"
                :type="customizeOptions.cardButton.buttonType"
                :suffix="IconArrowRight"
              >
                {{ labelsDisplay('view_all') }}
              </AmButton>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
// * Import from Vue
import {inject, ref, computed, onMounted, provide} from 'vue'

// * Composables
import { useColorTransparency } from '../../../../assets/js/common/colorManipulation.js'
import { useReactiveCustomize } from '../../../../assets/js/admin/useReactiveCustomize.js'

// * Components
import AmButton from '../../../_components/button/AmButton.vue'
import IconArrowRight from '../../../_components/icons/IconArrowRight.vue'

// * Plugin Licence
let licence = inject('licence')

// * Features
let features = inject('features')

// * Customize
const { amCustomize } = useReactiveCustomize()

// * Options
let customizeOptions = computed(() => {
  return amCustomize.value.cbf.categoriesList.options
})

// * Component reference
let ameliaContainer = ref(null)

// * Plugin wrapper width
let containerWidth = ref(0)

// * window resize listener
window.addEventListener('resize', resize)

// * resize function
function resize() {
  if (ameliaContainer.value) {
    containerWidth.value = ameliaContainer.value.offsetWidth
  }
}

onMounted(() => {
  if (ameliaContainer.value) {
    containerWidth.value = ameliaContainer.value.offsetWidth
  }
})

let itemWidth = computed(() => {
  if (containerWidth.value <= 500) {
    return 'am-w100'
  }

  if (containerWidth.value <= 600) {
    return 'am-w50'
  }

  if (containerWidth.value <= 768) {
    return 'am-w33'
  }

  return ''
})

let categoriesList = [
  {
    id: 1,
    name: 'Category 1',
    packageList: [1, 2],
    serviceList: [1, 2, 3],
    position: 1,
    color: '#1788FB',
  },
  {
    id: 2,
    name: 'Category 2',
    packageList: [1],
    serviceList: [1, 2, 3],
    position: 1,
    color: '#4BBEC6',
  },
  {
    id: 3,
    name: 'Category 3',
    packageList: [1, 2],
    serviceList: [1],
    position: 1,
    color: '#FBC22D',
  },
  {
    id: 4,
    name: 'Category 4',
    packageList: [1, 2],
    serviceList: [1, 2, 3],
    position: 1,
    color: '#26CC2B',
  },
  {
    id: 5,
    name: 'Category 5',
    packageList: [1, 2],
    serviceList: [1, 2, 3],
    position: 1,
    color: '#774DFB',
  },
]

let langKey = inject('langKey')
let amLabels = inject('labels')

function labelsDisplay(label) {
  let computedLabel = computed(() => {
    let translations = amCustomize.value.cbf.categoriesList.translations
    return translations &&
      translations[label] &&
      translations[label][langKey.value]
      ? translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-fcl-card-text-op80': useColorTransparency(
      amColors.value.colorCardText,
      0.8
    ),
  }
})
</script>

<script>
export default {
  name: 'CategoriesList',
  key: 'categoriesList',
}
</script>

<style lang="scss">
#amelia-app-backend-new #amelia-container.am-fc__wrapper {
  .am-fcl {
    // -fcl- form category list
    // -ff- font family
    --am-c-fcl-primary: var(--am-c-primary);
    --am-c-fcl-bgr: var(--am-c-main-bgr);
    --am-c-fcl-card-bgr: var(--am-c-card-bgr);
    --am-c-fcl-card-border: var(--am-c-card-border);
    --am-c-fcl-card-text: var(--am-c-card-text);
    --am-ff-fcl: var(--am-font-family);
    display: flex;
    align-items: unset;
    justify-content: center;
    flex-wrap: wrap;
    width: 100%;
    background-color: var(--am-c-main-bgr);
    border-radius: 12px;

    * {
      font-family: var(--am-ff-fcl);
      box-sizing: border-box;
    }

    &__item {
      max-width: 25%;
      width: 100%;
      display: flex;
      background-color: transparent;
      padding: 8px;

      &.am-w33 {
        max-width: 33.33333%;
      }

      &.am-w50 {
        max-width: 50%;
      }

      &.am-w100 {
        max-width: 100%;
      }

      &-inner {
        display: flex;
        width: 100%;
        background-color: transparent;
        border: 1px solid var(--am-c-fcl-card-border);
        border-radius: 8px;
        overflow: hidden;
      }

      &-content {
        position: relative;
        display: flex;
        width: 100%;
        background-color: var(--am-c-fcl-card-bgr);
        padding: 0 0 52px;
      }

      &-heading {
        width: 100%;
        padding: 12px;
      }

      &-name {
        width: 100%;
        font-family: var(--am-ff-fcl);
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        color: var(--am-c-fcl-card-text);
        margin: 0 0 12px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      &-segments {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        flex-wrap: wrap;

        &__item {
          display: flex;
          align-items: center;
          height: 18px;

          &:first-child {
            margin-right: 4px;
          }

          &-icon {
            font-size: 24px;
            color: var(--am-c-fcl-primary);
          }

          &-count {
            font-family: var(--am-ff-fcl);
            font-size: 13px;
            font-weight: 400;
            line-height: 1.3846;
            color: var(--am-c-fcl-card-text-op80);
          }
        }
      }

      &-footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        padding: 12px;
      }

      &-btn {
        width: 100%;
      }
    }
  }
}
</style>
