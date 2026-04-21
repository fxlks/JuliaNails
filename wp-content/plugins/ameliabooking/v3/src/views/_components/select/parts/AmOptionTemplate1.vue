<template>
  <span class="am-oitf__wrapper">
    <span class="am-oitf">
      <span v-if="!props.iconString" class="am-oitf__img">
        <span
          class="am-oitf__img-placeholder"
          :style="{ backgroundImage: `url(${props.imageThumb})`, backgroundColor: (props.imageThumb ? 'var(--am-c-option-bgr)': 'var(--am-c-option-img-bgr)')}"
        >
          <span v-if="!props.imageThumb">
            {{ imagePlaceholder() }}
          </span>
        </span>
      </span>
      <span v-if="props.iconString" class="am-oitf__icon" :class="`am-icon-${props.iconString}`"></span>
      <span class="am-oitf__data">
        <span class="am-oitf__data-label">
          {{ props.label }}
        </span>
        <span v-if="props.price" class="am-oitf__data-price">
        {{ `${(props.price > 0 ? '+' : '-') + useFormattedPrice(props.price)}` }}
        </span>
        <span v-if="!props.price && props.priceString" class="am-oitf__data-price-string">
        {{ `${props.priceString}` }}
        </span>
      </span>
    </span>
  </span>
</template>

<script setup>
import { useFormattedPrice } from '../../../../assets/js/common/formatting'


/**
 * Component Props
 */
let props = defineProps({
  identifier: {
    type: [String, Number],
    required: true
  },
  imageThumb: {
    type: String,
    default: ''
  },
  label: {
    type: [String, Number],
    required: true
  },
  description: {
    type: String,
    default: ''
  },
  price: {
    type: [String, Number],
    default: ''
  },
  iconString: {
    type: String,
    default: ''
  },
  priceString: {
    type: String,
    default: ''
  },
})

function imagePlaceholder () {
  if (props.label) {
    let shortLabel = ''
    props.label.split(' ').forEach(item => {
      shortLabel += item.charAt(0).toUpperCase()
    })

    return shortLabel
  }
}
</script>

<script>
export default {
  inheritAttrs: false,
}
</script>

<style lang="scss">
// am - Amelia
// oitf - option inner template first
.am-oitf {
  width: 100%;
  display: flex;
  align-items: center;

  * {
    font-family: var(--am-font-family);
  }

  &__wrapper {
    display: flex;
  }

  &__icon {
    font-size: 24px;
    color: var(--am-c-select-option-text);
  }

  &__img {
    display: flex;
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    margin-right: 8px;

    &-placeholder {
      position: relative;
      display: block;
      width: 100%;
      height: 100%;
      background-color: var(--am-c-option-img-bgr);
      border-radius: 50%;
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;

      span {
        display: block;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 15px;
        font-weight: 500;
        line-height: 1;
        color: var(--am-c-option-img-text);
      }
    }
  }

  &__data {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;

    &-label {
      font-size: 15px;
      font-weight: 500;
      line-height: 1.333333;
      color: var(--am-c-select-option-text);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    &-price {
      display: flex;
      flex-shrink: 0;
      align-self: center;
      justify-content: center;
      min-width: 70px;
      font-size: 16px;
      font-weight: 500;
      line-height: 1.25;
      color: var(--am-c-option-selected);
    }
  }
}
</style>
