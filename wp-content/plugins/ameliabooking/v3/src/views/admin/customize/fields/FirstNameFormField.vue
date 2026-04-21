<template>
  <!-- First Name -->
  <el-form-item
    class="am-fs__info-form__item"
    prop="firstName"
    label-position="top"
  >
    <template #label>
      <span class="am-fs__info-form__label">
        {{ labelsDisplay('first_name_colon') }}
      </span>
    </template>
    <AmInput
      v-model="infoFormData.firstName"
      name="firstName"
      :placeholder="labelsDisplay('enter_first_name')"
    />
  </el-form-item>
  <!-- /First Name -->
</template>

<script setup>
import AmInput from '../../../_components/input/AmInput.vue'
import { useReactiveCustomize } from '../../../../assets/js/admin/useReactiveCustomize.js'

import { computed, inject } from 'vue'

let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

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
  name: 'FirstNameFormField',
}
</script>

<style scoped></style>
