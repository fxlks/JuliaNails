<template>
  <!-- Option -->
  <el-option
    ref="amSelectOption"
    class="am-select-option"
    :value="value"
    :label="label"
    :disabled="disabled"
  >
    <slot/>
  </el-option>
  <!-- /Option -->
</template>

<script setup>
import { ref } from 'vue'

/**
 * Component Props
 */
defineProps ({
  value: {
    type: [String, Number, Object],
    required: true
  },
  label: {
    type: [String, Number],
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

/**
 * Component reference
 */
const amSelectOption = ref(null)
</script>

<style lang="scss">
.am-select-popper {
  --am-hmin-select-option: 32px;
  --am-h-select-option: auto;
  --am-flh-select-option: 1.4;
  --am-c-select-option-text: var(--am-c-option-text);
  --am-c-select-option-bgr: transparent;
  --am-pad-select-option: 8px;
  --am-mar-select-option: 0px;
  --am-fs-select-option: 14px;
  --am-fw-select-option: 400;
  --am-ff-select-option: var(--am-font-family);
  background-color: var(--am-c-option-bgr) !important;

  &.el-select__popper.el-popper[role=tooltip] {
    background-color: transparent;
    border-color: var(--am-c-option-border);
    overflow: hidden;
  }

  &.el-select-dropdown {
    margin: 0;
    position: static;
    border: none;
  }

  * {
    font-family: var(--am-font-family), sans-serif;
    border-radius: unset;
  }

  .el-select-dropdown {
    &__list {
      padding: 0;
    }

    &__item {
      min-height: var(--am-hmin-select-option) !important;
      height: var(--am-h-select-option) !important;
      font-family: var(--am-ff-select-option), sans-serif !important;
      font-size: var(--am-fs-select-option) !important;
      font-weight: var(--am-fw-select-option) !important;
      line-height: var(--am-flh-select-option) !important;
      color: var(--am-c-select-option-text) !important;
      background-color: var(--am-c-select-option-bgr) !important;
      padding: var(--am-pad-select-option) !important;
      margin: var(--am-mar-select-option) !important;
      white-space: normal;

      &:hover, &.is-hovering {
        --am-c-select-option-bgr: var(--am-c-option-hover);
      }

      &.is-selected {
        --am-c-select-option-text: var(--am-c-option-selected);
      }

      &.is-disabled {
        --am-c-select-option-text: var(--am-c-option-text-op50) !important;
      }

      &:last-child {
        border-bottom: none;
      }
    }

    p.el-select-dropdown__empty {
      padding: 16px;
      font-size: 14px;
      color: var(--am-c-option-text-op65);
    }
  }
}
</style>
