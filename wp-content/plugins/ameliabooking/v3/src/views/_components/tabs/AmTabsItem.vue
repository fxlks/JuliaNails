<template>
  <el-tab-pane
    :label="props.label"
    :disabled="props.disabled"
    :name="props.name"
    :closable="props.closable"
    :lazy="props.lazy"
  >
    <slot></slot>
  </el-tab-pane>
</template>

<script setup>

/**
 * Component Props
 */
const props = defineProps({
  label: {
    type: String,
  },
  disabled: {
    type: Boolean,
    default: false
  },
  name: {
    type: [String, Number]
  },
  closable: {
    type: Boolean,
    default: false
  },
  lazy: {
    type: Boolean,
    default: false
  }
})
</script>

<script>
export default {
  name: "ElTabPane",
  componentName: '"ElTabPane"'
}
</script>

<style lang="scss">

</style>
