<template>
  <div class="am-radio-group-wrapper">
    <el-radio-group
      ref="amRadioGroup"
      v-model="model"
      class="am-radio-group"
      v-bind="$props"
      :text-color="textColor"
      :fill="fill"
      @change="(eventLabel) => $emit('change', eventLabel)"
    >
      <slot />
    </el-radio-group>
  </div>
</template>

<script setup>
import { computed, ref, toRefs } from "vue";

const props = defineProps({
  modelValue: {
    type: [String, Number, Boolean]
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
  textColor: {
    type: String
  },
  fill: {
    type: String
  },
  validateEvent: {
    type: Boolean,
    default: true
  },
  ariaLabel: {
    type: String
  },
  id: {
    type: String
  },
})


/**
 * Component Emits
 **/
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
const amRadioGroup = ref()


</script>

<style lang="scss">
.am-radio-group-wrapper {
  .el-radio-group {
    display: flex;
    flex-direction: column;
  }
}
</style>