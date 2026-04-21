<template>
  <div
    class="am-fs__ps"
    :class="[props.globalClass, { 'am-fs__ps-popup': inPopup }]"
    :style="cssVars"
  >
    <template v-for="pack in packages" :key="pack.id">
      <div
        class="am-fs__ps-item"
        :class="{
          'am-fs__ps-item__selected': selected === pack.name && !inPopup,
        }"
        @click="clickedCard(pack.name)"
      >
        <div class="am-fs__ps-item__info">
          <p class="am-fs__ps-name">
            {{ pack.name }}
          </p>
          <div class="am-fs__ps-price__wrapper">
            <p class="am-fs__ps-discount">
              {{ `${labelsDisplay('discount_save')} 10%` }}
            </p>
            <p class="am-fs__ps-price">90$</p>
          </div>
        </div>
        <div class="am-fs__ps-item__services">
          <span
            v-for="book in pack.bookable"
            :key="book.id"
            class="am-fs__ps-item__services-inner"
          >
            {{ `${book.serviceName} x ${book.quantity}` }}
          </span>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { inject, computed, ref } from 'vue'
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation.js'
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

let props = defineProps({
  globalClass: {
    type: String,
    default: '',
  },
})

let { inPopup } = inject('inPopup', {
  inPopup: ref(false),
})

let selected = ref(false)
let packages = [
  {
    id: 1,
    name: 'Package 1',
    bookable: [
      {
        serviceName: 'Service 1',
        quantity: 5,
      },
      {
        serviceName: 'Service 2',
        quantity: 3,
      },
      {
        serviceName: 'Service 3',
        quantity: 7,
      },
    ],
  },
  {
    id: 2,
    name: 'Package 2',
    bookable: [
      {
        serviceName: 'Service 1',
        quantity: 5,
      },
      {
        serviceName: 'Service 2',
        quantity: 3,
      },
      {
        serviceName: 'Service 3',
        quantity: 7,
      },
    ],
  },
  {
    id: 3,
    name: 'Package 3',
    bookable: [
      {
        serviceName: 'Service 1',
        quantity: 5,
      },
      {
        serviceName: 'Service 2',
        quantity: 3,
      },
      {
        serviceName: 'Service 3',
        quantity: 7,
      },
    ],
  },
  {
    id: 4,
    name: 'Package 4',
    bookable: [
      {
        serviceName: 'Service 1',
        quantity: 5,
      },
      {
        serviceName: 'Service 2',
        quantity: 3,
      },
      {
        serviceName: 'Service 3',
        quantity: 7,
      },
    ],
  },
  {
    id: 5,
    name: 'Package 5',
    bookable: [
      {
        serviceName: 'Service 1',
        quantity: 5,
      },
      {
        serviceName: 'Service 2',
        quantity: 3,
      },
      {
        serviceName: 'Service 3',
        quantity: 7,
      },
    ],
  },
]

function clickedCard(name) {
  selected.value = name
}

let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value].packageStep.translations &&
      amCustomize.value[pageRenderKey.value].packageStep.translations[label] &&
      amCustomize.value[pageRenderKey.value].packageStep.translations[label][
        langKey.value
      ]
      ? amCustomize.value[pageRenderKey.value].packageStep.translations[label][
          langKey.value
        ]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Global colors
let amColors = inject('amColors')
let cssVars = computed(() => {
  return {
    '--am-c-ps-text': amColors.value.colorMainText,
    '--am-c-ps-text-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
    '--am-c-ps-text-op20': useColorTransparency(
      amColors.value.colorMainText,
      0.2
    ),
    '--am-c-ps-text-op06': useColorTransparency(
      amColors.value.colorMainText,
      0.06
    ),
    '--am-c-primary-op10': useColorTransparency(
      amColors.value.colorPrimary,
      0.1
    ),
    '--am-c-scroll-op30': useColorTransparency(
      amColors.value.colorPrimary,
      0.3
    ),
    '--am-c-scroll-op10': useColorTransparency(
      amColors.value.colorPrimary,
      0.1
    ),
    '--am-c-success-op10': useColorTransparency(
      amColors.value.colorSuccess,
      0.1
    ),
  }
})
</script>

<script>
export default {
  name: 'PackagesStep',
  key: 'packageStep',
  sidebarData: {
    label: 'package_selection',
    icon: 'pack-check',
    stepSelectedData: [],
    finished: false,
    selected: false,
  },
}
</script>

<style lang="scss">
#amelia-app-backend-new {
  #amelia-container {
    .am-fs {
      &__ps {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 100%;

        &.am-fs__ps-popup {
          max-height: 296px;
          overflow-x: hidden;
          padding-right: 6px;
        }

        // Main Scroll styles
        &::-webkit-scrollbar {
          width: 6px;
        }

        &::-webkit-scrollbar-thumb {
          border-radius: 6px;
          background: var(--am-c-scroll-op30);
        }

        &::-webkit-scrollbar-track {
          border-radius: 6px;
          background: var(--am-c-scroll-op10);
        }

        &-item {
          --am-c-ps-bgr: transparent;
          --am-c-ps-text: var(--am-c-ps-text-op60);
          --am-c-ps-border: var(--am-c-ps-text-op20);
          --am-c-ps-shadow: var(--am-c-ps-text-op06);

          width: 100%;
          padding: 12px;
          margin-bottom: 12px;
          background-color: var(--am-c-ps-bgr);
          border: 1px solid var(--am-c-ps-border);
          border-radius: 8px;
          box-shadow: 0 1px 1px var(--am-c-ps-shadow);
          box-sizing: border-box;
          cursor: pointer;

          &.am-fs__ps-item__selected {
            --am-c-ps-text: var(--am-c-main-text);
            --am-c-ps-border: var(--am-c-primary);
          }

          &:last-child {
            margin-bottom: 0;
          }

          &__info {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            margin: 0 0 4px;
          }

          &__services {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;

            &-inner {
              display: inline-flex;
              flex-direction: row;
              flex-wrap: nowrap;
              font-size: 13px;
              font-weight: 400;
              line-height: 1.3846;
              align-items: center;
              justify-content: center;
              color: var(--am-c-ps-text);

              &::before {
                content: '';
                display: inline-flex;
                width: 4px;
                height: 4px;
                border-radius: 50%;
                background-color: var(--am-c-ps-text);
                margin: 0 6px;
              }

              &:first-child {
                &::before {
                  content: unset;
                }
              }
            }
          }
        }

        &-name {
          font-size: 15px;
          font-weight: 500;
          line-height: 1.6;
          color: var(--am-c-main-text);
          margin: 0;
        }

        &-price {
          color: var(--am-c-primary);
          background-color: var(--am-c-primary-op10);

          &__wrapper {
            display: flex;
            flex-direction: row;

            & > p {
              font-size: 14px;
              font-weight: 400;
              line-height: 1;
              text-align: center;
              vertical-align: middle;
              padding: 5px 8px;
              margin: 0 0 0 8px;
              border-radius: 12px;
            }
          }
        }

        &-discount {
          color: var(--am-c-success);
          background-color: var(--am-c-success-op10);
          margin: 0;
        }
      }
    }
  }
}
</style>
