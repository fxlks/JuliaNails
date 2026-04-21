<template>
  <!-- Phone Number -->
  <el-form-item
    v-if="amCustomize[pageRenderKey].infoStep.options.phone.visibility"
    class="am-fs__info-form__item"
    prop="phone"
    label-position="top"
  >
    <template #label>
      <span class="am-fs__info-form__label">
        {{ labelsDisplay('phone_colon') }}
      </span>
    </template>
    <AmInputPhone
      v-model="infoFormData.phone"
      :placeholder="labelsDisplay('enter_phone')"
      name="phone"
    />
  </el-form-item>
  <!-- /Phone Number -->
</template>

<script setup>
import AmInputPhone from '../../../_components/input-phone/AmInputPhone.vue'
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
  name: 'PhoneFormField',
}
</script>
