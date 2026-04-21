<template>
  <!-- Email -->
  <el-form-item
    v-if="amCustomize[pageRenderKey].infoStep.options.email.visibility"
    class="am-fs__info-form__item"
    prop="email"
    label-position="top"
  >
    <template #label>
      <span class="am-fs__info-form__label">
        {{ labelsDisplay('email_colon') }}
      </span>
    </template>
    <AmInput
      v-model="infoFormData.email"
      name="email"
      type="text"
      :placeholder="labelsDisplay('enter_email')"
    />
  </el-form-item>
  <!-- /Email -->
</template>

<script setup>
import AmInput from '../../../_components/input/AmInput.vue'

import { computed, inject } from 'vue'

let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
let amCustomize = inject('customize')

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value].infoStep.translations &&
      amCustomize.value[pageRenderKey.value].infoStep.translations[label] &&
      amCustomize.value[pageRenderKey.value].infoStep.translations[label][
        langKey.value
      ]
      ? amCustomize.value[pageRenderKey.value].infoStep.translations[label][
          langKey.value
        ]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Form field data
let infoFormData = inject('infoFormData')
</script>

<script>
export default {
  name: 'EmailFormField',
}
</script>

<style scoped></style>
