<template>
  <div
    ref="initItemRef"
    class="am-fs__init-item"
    :class="[
      { 'am--selected': props.selected },
      { 'am--disabled': props.disabled }
    ]"
    :tabindex="props.disabled ? -1 : 0"
    @click="selectItem(props.item)"
    @keydown.enter="selectItem(props.item)"
  >
    <div v-if="componentWidth > 370" class="am-fs__init-item__img">
      <img
        :src="usePictureLoad(baseUrls, item, props.isPerson)"
        :alt="props.itemName"
      />
    </div>
    <div
      class="am-fs__init-item__content"
      :class="[responsiveClass, {'am-justify-center': !props.infoItems.length && !props.infoBtnVisibility && !props.packagesBtnVisibility}]"
    >
      <!-- Heading -->
      <div class="am-fs__init-item__heading" :class="responsiveClass">
        <div
          v-if="componentWidth <= 370"
          class="am-fs__init-item__img"
          :class="responsiveClass"
        >
          <img
            :src="usePictureLoad(baseUrls, item, props.isPerson)"
            :alt="props.itemName"
          />
        </div>
        <div class="am-fs__init-item__name" :class="responsiveClass">
          {{ props.itemName }}
        </div>
        <div
          v-if="props.priceVisibility"
          class="am-fs__init-item__cost"
          :class="responsiveClass"
        >
          <div class="am-fs__init-item__price">
            {{ props.price.length ? props.price : !props.isPerson ? labelsDisplay('free') : ''}}
          </div>
          <div
            v-if="props.taxVisibility && props.price.length"
            class="am-fs__init-item__price am-fs__init-item__tax"
          >
            {{ props.taxExcluded ? `+${labelsDisplay('total_tax_colon')}` : labelsDisplay('incl_tax') }}
          </div>
        </div>
      </div>
      <!-- Heading -->

      <!-- Info items -->
      <div
        v-if="props.infoItems.length"
        class="am-fs__init-item__info"
      >
        <div
          v-for="(infoItem, index) in props.infoItems"
          :key="index"
          class="am-fs__init-item__info-inner"
        >
          <IconComponent :icon="infoItem.icon"/>
          <a
            v-if="'isLink' in infoItem && infoItem.isLink"
            class="am-fs__init-item__info-name"
            :href="`https://maps.google.com/?q=${infoItem.name}`"
            target="_blank"
            rel="noopener noreferrer"
            tabindex="-1"
          >
            {{ infoItem.name }}
          </a>
          <span v-else class="am-fs__init-item__info-name">
            {{ infoItem.name }}
          </span>
        </div>
      </div>
      <!-- Info items -->

      <!-- Footer -->
      <div
        v-if="props.infoBtnVisibility || props.packagesBtnVisibility"
        class="am-fs__init-item__footer"
      >
        <span
          v-if="props.infoBtnVisibility"
          class="am-fs__init-item__footer-actions"
          tabindex="0"
          @click.stop="emits('triggerInfoPopup', props.item)"
          @keydown.enter="emits('triggerInfoPopup', props.item)"
        >
          {{ labelsDisplay('learn_more') }}
        </span>
        <span
          v-if="props.packagesBtnVisibility"
          class="am-fs__init-item__footer-actions"
          tabindex="0"
        >
          {{ labelsDisplay('view_in_package') }}
        </span>
      </div>
      <!-- Footer -->
    </div>
  </div>
</template>

<script setup>
// * Import from Vue
import { computed, inject, ref } from 'vue'

import { useElementSize } from '@vueuse/core'

// * Composables
import { usePictureLoad } from '../../../../../../assets/js/common/image'
import { useResponsiveClass} from '../../../../../../assets/js/common/responsive'

// * Components
import IconComponent from '../../../../../_components/icons/IconComponent.vue'

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  itemName: {
    type: String,
    required: true
  },
  selected: {
    type: Boolean,
    required: true
  },
  disabled: {
    type: Boolean,
    default: false
  },
  parentWidth: {
    type: Number,
    default: 0
  },
  isPerson: {
    type: Boolean,
    default: false
  },
  priceVisibility: {
    type: Boolean,
    default: false
  },
  price: {
    type: String,
    default: ''
  },
  taxVisibility: {
    type: Boolean,
    default: false
  },
  taxExcluded: {
    type: Boolean,
    default: false
  },
  infoItems: {
    type: Array,
    default: () => []
  },
  infoBtnVisibility: {
    type: Boolean,
    default: false
  },
  packagesBtnVisibility: {
    type: Boolean,
    default: false
  },
})

// * Component emits
const emits = defineEmits(['selectItem', 'triggerInfoPopup', 'triggerPackagesPopup'])

const baseUrls = inject('baseUrls')

const initItemRef = ref(null)

// * Component width
const { width: itemWidth } = useElementSize(initItemRef)

let componentWidth = computed(() => {
  return props.parentWidth ? props.parentWidth : itemWidth.value
})

let responsiveClass = computed(() => {
  return useResponsiveClass(componentWidth.value)
})

// * Lang key
let langKey = inject('langKey')

// * Labels
let amLabels = inject('labels')

let stepName = inject('stepName')
let pageRenderKey = inject('pageRenderKey')
let amCustomize = inject('customize')

function labelsDisplay (label) {

  return computed(() => {
    return amCustomize.value[pageRenderKey.value]?.[stepName.value]?.translations?.[label]?.[langKey.value] || amLabels[label]
  }).value
}

// * Item selection
function selectItem(item) {
  emits('selectItem', item)
}
</script>

<style lang="scss">
#amelia-app-backend-new #amelia-container {
  .am-fs__init {
    &-item {
      width: 100%;
      display: flex;
      flex-wrap: wrap;
      gap: 8px 12px;
      padding: 12px;
      border: 1px solid var(--am-c-inp-border);
      border-radius: 6px;
      cursor: pointer;

      &:focus {
        border-color: var(--am-c-primary);
      }

      &.am--selected {
        border-color: var(--am-c-primary);
        background-color: var(--am-c-primary-op05);
      }

      &.am--disabled {
        cursor: not-allowed;
        opacity: 0.5;

        &:focus {
          border-color: var(--am-c-inp-border);
        }
      }

      // Card image
      &__img {
        width: 76px;
        height: 76px;
        overflow: hidden;
        border-radius: 38px;
        border: 1px solid var(--am-c-inp-border);

        &.am-item-any {
          width: 76px;
          height: 48px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 26px;
          color: var(--am-c-primary);
          background-color: var(--am-c-primary-op05);
        }

        &.am-rw {
          &-370 {
            flex: 0 0 auto;
            width: 42px;
            height: 42px;
            order: 0;
          }
        }

        img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }
      }

      // Card content
      &__content {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        gap: 2px 4px;
        width: calc(100% - 88px);
        justify-content: space-between;

        &.am-justify-center {
          justify-content: center;
        }

        &.am-rw {
          &-370 {
            width: 100%;
          }
        }
      }

      // Card Heading
      &__heading {
        display: flex;
        width: 100%;
        align-items: center;
        justify-content: space-between;
        gap: 2px 4px;

        &.am-rw {
          &-370 {
            flex-wrap: wrap;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
          }
        }
      }

      // Card name
      &__name {
        display: block;
        font-weight: 500;
        font-size: 15px;
        line-height: 1.6;
        color: var(--am-c-main-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;

        &.am-rw {
          &-370 {
            display: flex;
            width: 100%;
            white-space: normal;
            overflow: visible;
            text-overflow: unset;
            order: 2;
          }
        }
      }

      // Card cost
      &__cost {
        display: flex;
        align-self: flex-start;
        flex: 0 0 auto;
        align-items: center;
        gap: 4px;

        &.am-rw {
          &-370 {
            order: 1;
            align-self: center;
            flex: 1;
            flex-wrap: wrap;
            justify-content: flex-end;
          }
        }
      }

      // Card price
      &__price {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 8px;
        font-weight: 500;
        font-size: 14px;
        line-height: 1.42857;
        border-radius: 24px;
        color: var(--am-c-primary);
        background-color: var(--am-c-primary-op05);
      }

      // Card info
      &__info {
        display: flex;
        flex-wrap: wrap;
        gap: 2px 4px;
        width: 100%;

        &-inner {
          display: flex;
          align-items: center;
          justify-content: flex-start;
        }

        [class^='am-icon-'] {
          flex: 0 0 auto;
          align-self: flex-start;
          font-size: 24px;
          line-height: 16px;
          color: var(--am-c-primary);
        }

        &-name {
          font-size: 13px;
          font-weight: 400;
          line-height: 1.38462;
          color: var(--am-c-main-text);
        }
      }

      // Card footer
      &__footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 6px 0 0;
        margin: 6px 0 0;
        border-top: 1px solid var(--am-c-inp-border);

        &-actions {
          display: inline-flex;
          align-items: center;
          font-size: 11px;
          font-weight: 500;
          line-height: 1.81818;
          color: var(--am-c-main-text-op70);
          cursor: pointer;
          transition: 0.3s ease-in-out;

          &:hover,
          &:focus {
            color: var(--am-c-primary);
          }
        }
      }
    }
  }
}
</style>

