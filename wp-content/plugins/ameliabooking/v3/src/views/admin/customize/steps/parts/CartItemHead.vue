<template>
  <div
    class="am-fs__cserv"
    :class="useResponsiveClass(wrapperWidth)"
    :style="cssVars
">
    <div
      class="am-fs__cserv-heading"
      :class="useResponsiveClass(wrapperWidth)"
    >
      <span
        class="am-fs__cserv-heading-img"
        :style="{backgroundColor: `${props.data.service.color}`}"
      >
        {{ imagePlaceholder(props.data.service.name) }}
      </span>
      <p class="am-fs__cserv-name">
        {{ props.data.service.name }}
      </p>
    </div>
    <div
      class="am-fs__cserv-price"
      :class="useResponsiveClass(wrapperWidth)"
    >
      <span class="am-price">
        {{ useFormattedPrice(servicePrice + extrasPrice) }}
      </span>
    </div>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  computed,
  inject,
} from 'vue'

// * Composable
import { useFormattedPrice } from '../../../../../assets/js/common/formatting'
import { useColorTransparency } from "../../../../../assets/js/common/colorManipulation";
import { useResponsiveClass } from "../../../../../assets/js/common/responsive";

let props = defineProps({
  data: {
    type: Object,
    default: null
  },
  globalClass: {
    type: String,
    default: ''
  }
})

// * Features
let features = inject('features')

function imagePlaceholder (label) {
  let shortLabel = ''

  label.split(' ').forEach(item => {
    shortLabel += item.charAt(0).toUpperCase()
  })

  if (shortLabel.length > 3) shortLabel = shortLabel.slice(0, 3)

  return shortLabel
}

let wrapperWidth = inject('wrapperWidth')
const baseUrls = inject('baseUrls')

let servicePrice = computed(() => {
  return props.data.service.price * props.data.service.persons
})

let extrasPrice = computed(() => {
  let price = 0
  if (features.value.extras) {
    props.data.extras.forEach((extra) => {
      price += extra.price * extra.quantity * props.data.service.persons
    })
  }

  return price
})

let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-cart-text': amColors.value.colorMainText,
    '--am-c-cart-primary': amColors.value.colorPrimary,
    '--am-c-cart-primary-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),
  }
})
</script>

<script>
export default {
  name: 'CartItemHead',
}
</script>

<style lang="scss">
#amelia-app-backend-new {
  #amelia-container {
    // am    - amelia
    // fs    - form steps
    // cserv - cart service
    .am-fs__cserv {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;

      &.am-rw-360 {
        flex-wrap: wrap;
      }

      * {
        word-break: break-word;
      }

      img {
        width: 40px;
        height: 40px;
        border-radius: 4px;
        margin: 0 12px 0 0;
      }

      &-heading {
        display: flex;
        align-items: center;
        justify-content: center;

        &.am-rw-360 {
          width: 100%;
          justify-content: flex-start;
        }

        &-img {
          width: 40px;
          height: 40px;
          border-radius: 4px;
          font-size: 16px;
          display: flex;
          align-items: center;
          justify-content: center;
          flex: 0 0 auto;
          color: var(--am-c-main-bgr);
          margin-right: 12px;
        }
      }

      &-name {
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        /* $shade-900 */
        color: var(--am-c-cart-text);
      }

      &-price {
        display: flex;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        padding: 2px 9px;
        border-radius: 12px;
        background-color: var(--am-c-cart-primary-op10);

        &.am-rw-360 {
          width: 100%;
          margin-top: 8px;
        }

        .am-price {
          display: block;
          font-size: 14px;
          font-weight: 500;
          line-height: 1.42857;
          /* $shade-900 */
          color: var(--am-c-cart-primary);
        }
      }
    }
  }
}
</style>
