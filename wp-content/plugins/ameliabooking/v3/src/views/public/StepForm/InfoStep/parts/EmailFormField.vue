<template>
  <!-- Email -->
  <el-form-item
    v-if="amCustomize.infoStep.options.email.visibility"
    ref="primeFieldRef"
    class="am-fs__info-form__item am-info-email"
    prop="email"
    label-position="top"
  >
    <template #label>
      <span class="am-fs__info-form__label">
        {{ amLabels.email_colon }}
      </span>
    </template>
    <AmInput
      v-model="infoFormData.email"
      name="email"
      type="email"
      :placeholder="amLabels.enter_email"
      :disabled="props.loggedInUser"
    />
  </el-form-item>
  <!-- /Email -->
</template>

<script setup>
import AmInput from'../../../../_components/input/AmInput.vue'

import {inject, ref} from "vue";

let primeFieldRef = ref(null)

// * Labels
let amLabels = inject('amLabels')

// * Customize
let amCustomize = inject('amCustomize')

// * Form field data
let infoFormData = inject('infoFormData')

let props = defineProps({
  loggedInUser: {
    type: Boolean,
    default: false
  }
})

defineExpose({
  primeFieldRef
})
</script>

<script>
export default {
  name: "EmailFormField"
}
</script>
