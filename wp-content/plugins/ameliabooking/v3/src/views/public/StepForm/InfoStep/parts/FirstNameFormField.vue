<template>
  <!-- First Name -->
  <el-form-item
    ref="primeFieldRef"
    class="am-fs__info-form__item am-info-first-name"
    prop="firstName"
    label-position="top"
  >
    <template #label>
      <span class="am-fs__info-form__label">
        {{ amLabels.first_name_colon }}
      </span>
    </template>
    <AmInput
      v-model="infoFormData.firstName"
      name="firstName"
      :placeholder="amLabels.enter_first_name"
    />
  </el-form-item>
  <!-- /First Name -->
</template>

<script setup>
import AmInput from'../../../../_components/input/AmInput.vue'
import {inject, ref} from "vue";

let primeFieldRef = ref(null)

let amLabels = inject('amLabels')

// * Form field data
let infoFormData = inject('infoFormData')

defineExpose({
  primeFieldRef
})
</script>

<script>
export default {
  name: "FirstNameFormField"
}
</script>
