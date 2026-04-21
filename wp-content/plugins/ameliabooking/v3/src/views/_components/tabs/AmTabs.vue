<template>
  <el-tabs v-model="model">
    <slot></slot>
  </el-tabs>
</template>

<script setup>
import {
  computed,
  toRefs
} from "vue";

/**
 * Component Props
 */
const props = defineProps({
  id: {
    type: String
  },
  modelValue: {
    type: [String, Number],
  },
  tabsArray: {
    type: Array,
  }
})

/**
 * Component Emits
 * */
const emits = defineEmits(['tab-click', 'tab-change', 'tab-remove', 'tab-add', 'edit', 'update:activeName'])

/**
 * Component model
 */
let { modelValue } = toRefs(props)
let model = computed({
  get: () => modelValue.value,
  set: (val) => {
    emits('update:activeName', val)
  }
})
</script>

<script>
export default {
  name: "AmTabs"
}
</script>

<style lang="scss">

</style>
