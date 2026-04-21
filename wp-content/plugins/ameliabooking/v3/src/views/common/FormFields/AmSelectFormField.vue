<template>
  <el-form-item
    ref="formFieldRef"
    class="am-ff__item"
    :prop="props.itemName"
    :label-position="props.labelPosition"
  >
    <template #label>
      <span class="am-ff__item-label" v-html="props.label" />
    </template>
    <AmSelect
      v-model="model"
      :fit-input-width="true"
      :clearable="props.clearable"
      :prefix-icon="props.prefixIcon"
      :placeholder="props.placeholder"
      :disabled="props.disabled"
      :loading="props.loading"
      :loading-text="props.loadingText"
    >
      <AmOption
        v-for="(option, i) in props.options"
        :key="i"
        :label="option.label"
        :value="option.value ? option.value : option.label"
      />
    </AmSelect>
  </el-form-item>
</template>

<script setup>
// * _components
import AmSelect from '../../_components/select/AmSelect.vue'
import AmOption from '../../_components/select/AmOption.vue'

// * Import from Vue
import {
  computed,
  ref,
  toRefs
} from "vue";

// * Form Item Props
let props = defineProps({
  modelValue: {
    type: [String, Array, Object, Number, null],
    required: true
  },
  itemName: {
    type: String,
    required: true
  },
  label: {
    type: String
  },
  labelPosition: {
    type: String,
    default: 'top'
  },
  options: {
    type: Array,
  },
  prefixIcon: {
    type: [String, Object],
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  clearable: {
    type: Boolean,
    default: true
  },
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  loadingText: {
    type: String,
    default: 'Loading...'
  },
})

// * Define Emits
const emits = defineEmits(['update:modelValue'])

// * Component model
let { modelValue } = toRefs(props)
let model = computed({
  get: () => modelValue.value,
  set: (val) => {
    emits('update:modelValue', val)
  }
})

// * Form Item Reference
let formFieldRef = ref(null)

defineExpose({
  formFieldRef
})
</script>

<script>
export default {
  name: "SelectFormField"
}
</script>
