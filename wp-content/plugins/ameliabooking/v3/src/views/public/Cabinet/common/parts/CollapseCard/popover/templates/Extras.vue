<template>
  <div
    class="am-cc__extras"
    :class="responsiveClass"
  >
    <div
      v-for="(item, index) in props.data"
      :key="index"
      class="am-cc__extras-item"
    >
      <span class="am-cc__extras-name">
          {{ item.name }}
        </span>
      <div
        v-if="item.duration || item.price || item.quantity"
        class="am-cc__extras-item__inner"
        :class="[
          {'am-flex-space': (!item.price && item.duration)},
          {'am-flex-end': (!item.duration)},
        ]"
      >
        <span
          v-if="item.duration"
          class="am-cc__extras-duration"
        >
          {{ useSecondsToDuration(item.duration, amLabels.h, amLabels.min) }}
        </span>
        <span
          v-if="shortcodeData.cabinetType === 'customer' && item.price"
          class="am-cc__extras-price"
        >
          {{ useFormattedPrice(item.price) }}
        </span>
        <span
          v-if="item.quantity"
          class="am-cc__extras-quantity"
        >
        x{{ item.quantity }}
      </span>
      </div>
    </div>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  computed,
  inject
} from "vue";

// * Composables
import { useResponsiveClass } from "../../../../../../../../assets/js/common/responsive.js";
import { useFormattedPrice } from "../../../../../../../../assets/js/common/formatting";
import { useSecondsToDuration } from "../../../../../../../../assets/js/common/date";

// * Vars
const amLabels = inject('amLabels')

// * Data in shortcode
const shortcodeData = inject('shortcodeData')

let props = defineProps({
  data: {
    type: [Array, Object, String]
  }
})

/*************
 * Customize *
 *************/

let cWidth = inject('containerWidth')

let responsiveClass = computed(() => {
  return useResponsiveClass(cWidth.value)
})
</script>

<script>
export default {
  name: "TemplateExtras"
}
</script>

<style lang="scss">
@mixin popover-template-extras {
  .am-cc__extras {
    width: 340px;

    &.am-w-420 {
      width: 100%;
    }

    &-item {
      width: 100%;
      display: flex;
      align-items: center;
      margin: 0 0 4px;

      &:last-child {
        margin: 0;
      }

      &__inner {
        width: 100%;
        display: flex;
        align-items: center;

        &.am-flex {
          &-space {
            justify-content: space-between;
          }

          &-end {
            justify-content: flex-end;
          }
        }

        & > span {
          width: 33.333%;
          display: inline-flex;
          font-size: 14px;
          line-height: 1.428571429;
        }
      }
    }

    &-name {
      display: inline-flex;
      flex: 0 0 auto;
      font-size: 14px;
      font-weight: 400;
      line-height: 1.428571429;
      color: var(--am-c-cc-text);
      margin: 0 8px 0 0;
    }

    &-duration, &-price {
      font-weight: 400;
      color: var(--am-c-cc-text-op70);
      padding: 0 4px 0 0;
    }

    &-price {
      justify-content: center;
    }

    &-quantity {
      font-weight: 500;
      color: var(--am-c-cc-text);
      justify-content: flex-end;
    }
  }
}

.el-popover {
  @include popover-template-extras;
}
</style>
