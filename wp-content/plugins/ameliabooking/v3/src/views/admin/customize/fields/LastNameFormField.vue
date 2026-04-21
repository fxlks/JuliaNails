<template>
  <!--Last Name -->
  <el-form-item
    v-if="amCustomize[pageRenderKey].infoStep.options.lastName.visibility"
    class="am-fs__info-form__item"
    prop="lastName"
    label-position="top"
  >
    <template #label>
      <span class="am-fs__info-form__label">
        {{ labelsDisplay('last_name_colon') }}
      </span>
    </template>
    <AmInput
      v-model="infoFormData.lastName"
      name="lastName"
      :placeholder="labelsDisplay('enter_last_name')"
    />
  </el-form-item>
  <!-- /Last Name -->
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
  name: 'LastNameFormField',
}
</script>

<style scoped></style>
