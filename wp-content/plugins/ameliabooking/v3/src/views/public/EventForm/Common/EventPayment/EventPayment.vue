<template>
  <div
    :class="props.globalClass"
    :style="cssVars"
  >
    <Payment
      v-if="selectedEvent"
      :selected-item="selectedEvent"
      :tax-visibility="taxVisible"
      :in-dialog="true"
    ></Payment>
  </div>
</template>

<script setup>
// * Components
import Payment from "../../../Parts/Payment/Page/Payment.vue";

// * Import from Vue
import {
  inject,
  provide,
  reactive,
  computed
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Composables
import { useColorTransparency } from "../../../../../assets/js/common/colorManipulation";
import { useTaxVisibility } from "../../../../../assets/js/common/pricing";

let props = defineProps({
  globalClass: {
    type: String,
    default: ''
  },
  inDialog: {
    type: Boolean,
    default: false
  }
})

// * Store
const store = useStore()

const {
  footerButtonReset,
} = inject('changingStepsFunctions', {
  footerButtonReset: () => {},
})

footerButtonReset()

let stepsArray = inject('stepsArray')
let stepIndex = inject('stepIndex')

// * Form Key
let formKey = computed(() => store.getters['getFormKey'])

// * Amelia Settings
let amSettings = computed(() => store.getters['getSettings'])

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})
  let componentKey = stepsArray.value[stepIndex.value].key

  if (
    amSettings.value.customizedData
    && formKey.value in amSettings.value.customizedData
    && amSettings.value.customizedData[formKey.value][componentKey].translations
  ) {
    let customizedLabels = amSettings.value.customizedData[formKey.value][componentKey].translations
    Object.keys(customizedLabels).forEach(labelKey => {
      if (customizedLabels[labelKey][localLanguage.value]) {
        computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
})

provide('amLabels', amLabels.value)

// * Event obj
let selectedEvent = computed(() => store.getters['eventEntities/getEvent'](store.getters['eventBooking/getSelectedEventId']))

// * Tax
let taxVisible = computed(() => {
  return useTaxVisibility(store, selectedEvent.value.id, 'event')
})

// * Colors
let amColors = inject('amColors')

const cssVars = computed(() => {
  return  {
    '--am-c-ps-price-bgr': useColorTransparency(amColors.value.colorBtnPrim, 0.05),
    '--am-c-ps-total-price': amColors.value.colorBtnPrim,
    '--am-c-ps-text-op50': useColorTransparency(amColors.value.colorMainText, 0.5),
    '--am-c-ps-text-op25': useColorTransparency(amColors.value.colorMainText, 0.25),
    '--am-c-ps-text-op20': useColorTransparency(amColors.value.colorMainText, 0.2),
    '--am-c-ps-text-op06': useColorTransparency(amColors.value.colorMainText, 0.06),
    '--am-c-ps-primary': amColors.value.colorPrimary,
    '--am-c-ps-primary-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),
    '--am-c-ps-primary-op06': useColorTransparency(amColors.value.colorPrimary, 0.06)
  }
})
</script>

<script>
export default {
  name: "EventPayment",
  key: 'payment',
  label: 'event_payment'
}
</script>

<style lang="scss">

</style>