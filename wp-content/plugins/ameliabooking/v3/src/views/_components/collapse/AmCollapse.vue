<template>
  <div class="am-collapse">
    <slot></slot>
  </div>
</template>

<script setup>
import { provide } from 'vue';

const props = defineProps({
  multiple: {
    type: Boolean,
    default: false
  }
})
provide('multiple', props.multiple)
</script>

<style lang="scss">
// am  -- amelia
@mixin am-collapse-block {
  .am-collapse {}
}

// public
.amelia-v2-booking #amelia-container {
  @include am-collapse-block;
}

// admin
#amelia-app-backend-new {
  @include am-collapse-block;
}

// modal popup
.am-dialog-popup {
  @include am-collapse-block;
}
</style>