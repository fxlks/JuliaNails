<template>
  <el-form-item
    :id="props.id"
    ref="formFieldRef"
    class="am-ff__item"
    :prop="props.itemName"
    :label-position="props.labelPosition"
  >
    <template #label>
      <span class="am-ff__item-label" v-html="props.label" />
    </template>
    <!-- Address Field -->
    <AmAddressInput
      :id="props.itemName"
      v-model="model"
      @address-selected="(address) => emits('address-selected', address)"
    />
    <!-- /Address Field -->
  </el-form-item>
</template>

<script setup>
// * Components
import AmAddressInput  from "../../_components/address-input/AmAddressInput.vue";

// * Import from Vue
import {
  computed,
  ref,
  toRefs,
} from "vue";

// * Form Item Props
let props = defineProps({
  modelValue: {
    type: [String, Array, Object, Number],
    required: true
  },
  id: {
    type: [String, Number],
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
  }
})

// * Define Emits
const emits = defineEmits(['update:modelValue', 'address-selected'])

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
  name: "AddressFormField"
}
</script>

<style lang="scss">
.pac-container {
  z-index: 9999999999 !important;
}
</style>
