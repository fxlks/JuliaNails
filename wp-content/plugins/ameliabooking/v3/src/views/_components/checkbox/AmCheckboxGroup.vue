<template>
  <!-- Radio Group -->
  <div class="am-checkbox-group-wrapper">
    <el-checkbox-group
        ref="AmCheckboxGroup"
        v-model="model"
        class="am-checkbox-group"
        v-bind="$props"
        :min="min"
        :max="max"
        :text-color="textColor"
        :fill="fill"
        @change="(eventLabel) => $emit('change', eventLabel)"
    >
      <slot />
    </el-checkbox-group>
  </div>
</template>

<script setup>
import { computed, ref, toRefs } from "vue";

/**
 * Component Props
 */
const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  size: {
    type: String,
    default: 'default',
    validator(value) {
      return ['default', 'medium', 'small'].includes(value)
    }
  },
  disabled: {
    type: Boolean,
    default: false
  },
  min: {
    type: Number
  },
  max: {
    type: Number
  },
  textColor: {
    type: String
  },
  fill: {
    type: String
  },
})

/**
 * Component Emits
 * */
const emits = defineEmits(['change', 'update:modelValue'])

/**
 * Component model
 */
let { modelValue } = toRefs(props)
let model = computed({
  get: () => modelValue.value,
  set: (val) => {
    emits('update:modelValue', val)
  }
})

/**
 * Component reference
 */
const AmCheckboxGroup = ref()

</script>

<style lang="scss">
.am-checkbox-group-wrapper {
  .am-checkbox-group {}
}
</style>
