<template>
  <el-form-item
    v-if="props.visible"
    ref="formFieldRef"
    class="am-ff__item"
    :prop="props.itemName"
    :label-position="props.labelPosition"
  >
    <template #label>
      <span class="am-ff__item-label" v-html="props.label" />
    </template>
    <AmInput
      v-model="model"
      :name="props.itemName"
      :type="props.itemType"
      :clearable="props.clearable"
      :readonly="props.readonly"
      :show-password="props.showPassword"
      :placeholder="props.placeholder"
      :disabled="props.disabled"
      :prefix-icon="props.prefixIcon"
      :prepend="props.prepend"
      :append="props.append"
      :suffix-icon="props.suffixIcon"
      @enter="emits('enter')"
    />
  </el-form-item>
</template>

<script setup>
// * _components
import AmInput from'../../_components/input/AmInput.vue'

// * Import from Vue
import {
  computed,
  ref,
  toRefs
} from "vue";

// * Form Item Props
let props = defineProps({
  modelValue: {
    type: [String, Array, Object, Number],
    required: true
  },
  itemName: {
    type: String,
    required: true
  },
  itemType: {
    type: String,
    default: 'text'
  },
  label: {
    type: String
  },
  labelPosition: {
    type: String,
    default: 'top'
  },
  placeholder: {
    type: String
  },
  visible: {
    type: Boolean,
    default: true
  },
  disabled: {
    type: Boolean,
    default: false
  },
  showPassword: {
    type: Boolean,
    default: false
  },
  clearable: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  },
  prefixIcon: {
    type: [String, Object],
    default: ''
  },
  prepend: {
    type: [String, Object],
    default: ''
  },
  suffixIcon: {
    type: [String, Object],
    default: ''
  },
  append: {
    type: [String, Object],
    default: ''
  }
})

// * Define Emits
const emits = defineEmits(['update:modelValue', 'enter'])

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
  name: "FirstNameFormField"
}
</script>
