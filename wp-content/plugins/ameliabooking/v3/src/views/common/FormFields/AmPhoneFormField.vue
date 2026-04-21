<template>
  <el-form-item
    v-if="props.visible"
    ref="formFieldRef"
    class="am-ff__item"
    :prop="props.itemName"
    :label-position="props.labelPosition"
    :style="cssVars"
    style="z-index: 10"
  >
    <template #label>
      <span class="am-ff__item-label" v-html="props.label" />
    </template>
    <AmInputPhone
      :key="`phone-${localDefaultCode}-${props.refreshTrigger}`"
      v-model="model"
      :name="props.itemName"
      :placeholder="props.placeholder"
      :default-code="localDefaultCode"
      :disabled="props.disabled"
      style="position: relative"
      @country-phone-iso-updated="countryPhoneIsoUpdated"
    />
    <div
      v-if="props.isWhatsApp"
      class="am-whatsapp-opt-in-text"
    >
      {{ props.whatsAppLabel }}
    </div>
  </el-form-item>
</template>

<script setup>
// * _components
import AmInputPhone from '../../_components/input-phone/AmInputPhone.vue'

// * Import from Vue
import {
  computed,
  inject,
  ref,
  toRefs,
  onMounted,
  watch,
  nextTick
} from "vue";

// * Composables
import { useColorTransparency } from "../../../assets/js/common/colorManipulation";

// * Form Item Props
let props = defineProps({
  modelValue: {
    type: [String, Number, Object, Array],
    required: true
  },
  itemName: {
    type: String,
    required: true
  },
  label: {
    type: String
  },
  whatsAppLabel: {
    type: String
  },
  labelPosition: {
    type: String,
    default: 'top'
  },
  placeholder: {
    type: String
  },
  phoneError: {
    type: Boolean,
    default: false
  },
  visible: {
    type: Boolean,
    default: true
  },
  defaultCode: {
    type: String,
    default: ''
  },
  isWhatsApp: {
    type: [Boolean, String, Number],
    default: false
  },
  countryPhoneIso: {
    type: [String, Object, Array, Number],
    required: true
  },
  disabled: {
    type: Boolean,
    default: false
  },
  refreshTrigger: {
    type: Number,
    default: 0
  }
})

// * Define Emits
const emits = defineEmits(['update:modelValue', 'update:countryPhoneIso'])

// * Component model
let { modelValue } = toRefs(props)
let model = computed({
  get: () => modelValue.value,
  set: (val) => {
    emits('update:modelValue', val ? val : '')
  }
})

// * Local ref for default code to handle reactivity
let localDefaultCode = ref(props.defaultCode)

function countryPhoneIsoUpdated (val) {
  emits('update:countryPhoneIso', val)
}

// * Watch for refresh trigger to update country code
watch(() => props.refreshTrigger, () => {
  nextTick(() => {
    localDefaultCode.value = props.defaultCode
    if (props.defaultCode) {
      emits('update:countryPhoneIso', props.defaultCode.toLowerCase())
    }
  })
})

// * Watch for defaultCode changes
watch(() => props.defaultCode, (newVal) => {
  localDefaultCode.value = newVal
})

onMounted(() => {
  localDefaultCode.value = props.defaultCode
  if (props.defaultCode) {
    emits('update:countryPhoneIso', props.defaultCode.toLowerCase())
  }
})

// * Colors
let amColors = inject('amColors')
let cssVars = computed(() => {
  return {
    // is - Info Step, wa - WhatsApp
    '--am-c-is-wa-text': useColorTransparency(amColors.value.colorMainText, 0.5),
    // ! Inline styles need to be removed
    'margin-bottom': props.isWhatsApp && !props.phoneError ? '10px' : '24px'
  }
})

let formFieldRef = ref(null)

defineExpose({
  formFieldRef
})
</script>

<script>
export default {
  name: "PhoneFormField"
}
</script>

<style lang="scss">
.amelia-v2-booking {
  #amelia-container {
    .am-fs__info-form__item, .am-ff__item {
      .am-whatsapp-opt-in-text {
        color: var(--am-c-is-wa-text);
        font-weight: 400;
        font-size: 10px;
        line-height: 16px;
        word-break: break-word;
      }
    }
  }
}
</style>
