<template>

  <!-- Option Group -->
  <el-option-group
    class="adm-select-option-group"
    :label="label"
    :disabled="disabled"
  >
    <slot />
  </el-option-group>
  <!-- /Option Group -->

</template>

<script>
export default {
  name: 'AdmOptionGroup',

  props: {
    label: {
      type: String,
      default: ''
    },
    disabled: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style lang="scss">
// Option Group
.el-select-group {
  // Title
  &__title {
    padding-left: 8px;
    font-weight: 600;
    font-size: 12px;
    line-height: 24px;
    color: $shade-700;
  }

  // Item
  .el-select-dropdown__item {
    padding-left: 8px;
  }

  // Divider
  &__wrap:not(:last-of-type):after {
    background: $shade-300;
    left: 8px;
    right: 8px;
  }
}
</style>
