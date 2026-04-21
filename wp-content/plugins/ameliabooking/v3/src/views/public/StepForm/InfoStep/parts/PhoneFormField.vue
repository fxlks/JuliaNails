<template>
  <!-- Phone Number -->
  <el-form-item
    v-if="amCustomize.infoStep.options.phone.visibility"
    ref="primeFieldRef"
    class="am-fs__info-form__item"
    prop="phone"
    label-position="top"
    style="z-index: 10"
    :style="cssVars"
  >
    <template #label>
      <span class="am-fs__info-form__label">
        {{ amLabels.phone_colon }}
      </span>
    </template>
    <AmInputPhone
      :key="`phone-${defaultCountryCode}-${props.refreshTrigger}`"
      v-model="infoFormData.phone"
      :placeholder="amLabels.enter_phone"
      :default-code="defaultCountryCode"
      name="phone"
      style="position: relative"
      @country-phone-iso-updated="(val) => {emits('countryPhoneIsoUpdated', val)}"
    />
    <div v-if="whatsAppSetUp() && !props.phoneError" class="am-whatsapp-opt-in-text">
      {{ amLabels.whatsapp_opt_in_text }}
    </div>
  </el-form-item>
  <!-- /Phone Number -->
</template>

<script setup>
import AmInputPhone from '../../../../_components/input-phone/AmInputPhone.vue'
import { settings } from "../../../../../plugins/settings";

// * Vue
import {
  computed,
  inject,
  ref,
  onMounted,
  watch,
  nextTick
} from "vue";

// * Vuex
import { useStore } from 'vuex'

// * Composables
import {
  useColorTransparency
} from "../../../../../assets/js/common/colorManipulation";

// * Emits
const emits = defineEmits([
  'countryPhoneIsoUpdated',
])

// * Props
let props = defineProps({
  phoneError: {
    type: Boolean,
    default: false
  },
  refreshTrigger: {
    type: Number,
    default: 0
  }
})

// * Store
const store = useStore()

// * Computed default country code - prioritize saved country ISO
let defaultCountryCode = computed(() => {
  const savedCountry = store.getters['booking/getCustomerCountryPhoneIso']
  return savedCountry || (settings.general.phoneDefaultCountryCode === 'auto' ? '' : settings.general.phoneDefaultCountryCode.toLowerCase())
})

// * Colors
let amColors = inject('amColors')
let cssVars = computed(() => {
  return {
    // is - Info Step, wa - WhatsApp
    '--am-c-is-wa-text': useColorTransparency(amColors.value.colorMainText, 0.5),
    'margin-bottom': whatsAppSetUp() && !props.phoneError ? '10px' : '24px'
  }
})

let primeFieldRef = ref(null)

// * Labels
let amLabels = inject('amLabels')

// * Customize
let amCustomize = inject('amCustomize')

// * Form field data
let infoFormData = inject('infoFormData')

function whatsAppSetUp () {
  return settings.notifications.whatsAppEnabled
}

onMounted(() => {
  if (defaultCountryCode.value) {
    emits('countryPhoneIsoUpdated', defaultCountryCode.value)
  }
})

// * Watch for country code changes to emit updates
watch(defaultCountryCode, (newVal) => {
  if (newVal) {
    emits('countryPhoneIsoUpdated', newVal)
  }
})

defineExpose({
  primeFieldRef
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
    .am-fs__info-form__item, .am-elfci__item {
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
