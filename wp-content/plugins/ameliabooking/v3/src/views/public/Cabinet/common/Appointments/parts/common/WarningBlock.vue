<template>
  <div class="am-capai-warn" :style="cssVars">
    <span class="am-icon-triangle-info"></span>
    <div
      v-if="props.heading || props.text"
      class="am-capai-warn__content"
    >
      <div v-if="props.heading" class="am-capai-warn__heading">
        {{ props.heading }}
      </div>
      <div v-if="props.text" class="am-capai-warn__text">
        {{ props.text }}
      </div>
    </div>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  computed,
  inject
} from 'vue'

// * Props
const props = defineProps({
  heading: {
    type: String,
    default: ''
  },
  text: {
    type: String,
    default: ''
  }
})

// * Functions
import { useColorTransparency } from "../../../../../../../assets/js/common/colorManipulation";

// * Colors
let amColors = inject('amColors')
// * CSS Variables
let cssVars = computed(() => {
  return {
    '--am-c-warning': amColors.value.colorWarning,
    '--am-c-warning-op10': useColorTransparency(amColors.value.colorWarning, 0.1),
    '--am-c-main-text': amColors.value.colorMainText,
    '--am-c-main-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
  }
})
</script>

<style lang="scss">
@mixin am-cabinet-appointment-warning {
  // am    - amelia
  // capai - cabinet-panel-appointment-item
  // warn  - warning
  .am-capai-warn {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    border: 1px solid var(--am-c-warning);
    background-color: var(--am-c-warning-op10);
    box-shadow: 0 2px 3px 0 var(--am-c-main-text-op10);
    border-radius: 6px;
    padding: 8px 12px 8px 8px;

    .am-icon-triangle-info {
      font-size: 32px;
      color: var(--am-c-warning);
      line-height: 24px;

      &::before {
        vertical-align: top;
      }
    }

    &__content {
      display: flex;
      flex-direction: column;
    }

    &__heading {
      color: var(--am-c-main-text);
      font-size: 14px;
      font-style: normal;
      font-weight: 500;
      line-height: 1.71429;
    }

    &__text {
      color: var(--am-c-main-text);
      font-size: 13px;
      font-style: normal;
      font-weight: 400;
      line-height: 1.53846;
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-appointment-warning;
}
</style>