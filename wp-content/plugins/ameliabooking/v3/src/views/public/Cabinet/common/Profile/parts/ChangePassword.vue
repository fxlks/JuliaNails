<template>
  <el-form
    ref="passFormRef"
    :model="passFormData"
    :rules="passFormRules"
    label-position="top"
    class="am-capi__form"
    :class="responsiveClass"
  >
    <template v-for="(item, key) in passFormConstruction" :key="item.props.itemName">
      <component
        :is="item.template"
        ref="customerPassCollectorRef"
        v-model="passFormData[key]"
        v-bind="item.props"
      ></component>
    </template>
  </el-form>
</template>

<script setup>
// * Import from Vue
import {
  computed,
  ref,
  inject
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Composables

// * _components
import { formFieldsTemplates } from "../../../../../../assets/js/common/formFieldsTemplates";

// * Props
const props = defineProps({
  responsiveClass: {
    type: String,
    default: ''
  }
})

// * Store
const store = useStore();

// * Labels
let amLabels = inject('amLabels')

// * Form reference
let passFormRef = ref(null)

// * Form data
let passFormData = ref({
  newPass: computed({
    get: () => store.getters['auth/getNewPassword'],
    set: (val) => {
      store.commit('auth/setNewPassword', val ? val : "")
    }
  }),
  confirmPass: computed({
    get: () => store.getters['auth/getConfirmPassword'],
    set: (val) => {
      store.commit('auth/setConfirmPassword', val ? val : "")
    }
  })
})

// * Form construction
let passFormConstruction = ref({
  newPass: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'newPass',
      itemType: 'password',
      showPassword: true,
      label: amLabels.new_password_colon,
      placeholder: '',
      minLength: 3,
      class: computed(() => `am-capp__item ${props.responsiveClass}`),
    }
  },
  confirmPass: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'confirmPass',
      itemType: 'password',
      showPassword: true,
      label: amLabels.new_password_colon_retype,
      placeholder: '',
      minLength: 3,
      class: computed(() => `am-capp__item ${props.responsiveClass}`),
    }
  }
})

// * Form validation rules
let passFormRules = ref({
  newPass: [
    {
      required: true,
      message: amLabels.new_password_required,
      trigger: 'submit',
    },
    {
      min: 4,
      message: amLabels.new_password_length,
      trigger: 'submit'
    }
  ],
  confirmPass: [
    {
      required: true,
      message: amLabels.new_password_required,
      trigger: 'submit',
    },
    {
      min: 4,
      message: amLabels.new_password_length,
      trigger: 'submit'
    },
    {
      validator: () => store.getters['auth/getNewPassword'] === store.getters['auth/getConfirmPassword'],
      message: amLabels.passwords_not_match,
      trigger: 'submit'
    }
  ],
})

// * Expose
defineExpose({
  passFormRef,
})
</script>

<style lang="scss">

</style>