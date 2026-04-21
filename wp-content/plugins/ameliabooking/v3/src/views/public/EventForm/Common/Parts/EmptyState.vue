<template>
<div
  class="am-ev-empty"
  :style="cssVars"
>
  <span class="am-icon-search-close"></span>
  <p>
    {{ labels.no_results }}
  </p>
</div>
</template>

<script setup>
// * Import from Vue
import {
  inject,
  computed
} from "vue";

// * Composables
import { useColorTransparency } from "../../../../../assets/js/common/colorManipulation";

// * Global labels
let labels = inject('labels')

// * Colors
let amColors = inject('amColors')

// * Css Vars
let cssVars = computed(() => {
  return {
    '--am-c-ev-empty-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-c-ev-empty-text': amColors.value.colorMainText
  }
})
</script>

<script>
export default {
  name: "EmptyState",
}
</script>

<style lang="scss">
.amelia-v2-booking #amelia-container {
  .am-ev-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    span {
      display: flex;
      font-size: 122px;
      line-height: 1;
      color: var(--am-c-ev-empty-text-op60);
    }

    p {
      font-size: 24px;
      line-height: 1;
      color: var(--am-c-ev-empty-text);
      padding: 0;
      margin: 0 0 24px;
    }
  }
}
</style>