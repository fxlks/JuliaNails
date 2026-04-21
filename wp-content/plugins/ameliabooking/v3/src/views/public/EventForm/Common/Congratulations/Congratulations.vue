<template>
  <div
    v-if="ready && booked && !loading && !loadingUpcoming"
    :style="cssVars"
    class="am-elf__main-content am-elf__congrats"
    :class="props.globalClass"
  >
    <Congratulations
      :labels="amLabels"
      :base-urls="baseUrls"
      :booked="booked"
      :coupon="coupon"
      :customer="customer"
      :in-dialog="props.inDialog"
    >
      <template #positionBottom>
        <AddToCalendar
          class="am-congrats__main-atc"
          :booked="booked"
          :labels="amLabels"
          :ready="ready"
        ></AddToCalendar>
      </template>
    </Congratulations>
  </div>

  <!-- Skeleton -->
  <Skeleton v-else></Skeleton>
  <!-- /Skeleton -->
</template>

<script setup>
// * Components
import Congratulations from "../../../Parts/Congratulations/Page/Congratulations.vue";

// * Parts
import AddToCalendar from "../../../Parts/Congratulations/Parts/AddToCalendar.vue";
import Skeleton from "../../../Parts/Congratulations/Skeleton/Skeleton.vue";

// * Import from Vue
import {
  computed,
  inject,
  watchEffect,
  onMounted, reactive, provide
} from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Composables
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation.js';

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

const store = useStore()

// * Root Settings
const amSettings = computed(() => store.getters['getSettings'])

// * URLs
const baseUrls = computed(() => store.getters['getBaseUrls'])

/**************
 * Computed *
 *************/

// * Ready state
let ready = computed(() => store.getters['getReady'])

// * Loading state
let loading = computed(() => store.getters['getLoading'])

let loadingUpcoming = computed(
  () => store.getters['getFormKey'] === 'ecf' ? store.getters['eventEntities/getUpcomingLoading'] : false
)

let booked = computed(() => store.getters['eventBooking/getBooked'])

// * Coupon
let coupon = computed(() => store.getters['coupon/getCoupon'])

let customer = computed(() => {
  return {
    firstName: store.getters['customerInfo/getCustomerFirstName'],
    lastName: store.getters['customerInfo/getCustomerLastName'],
    email: store.getters['customerInfo/getCustomerEmail'],
    phone: store.getters['customerInfo/getCustomerPhone'],
  }
})

/**************
 * Navigation *
 *************/

const { footerButtonClicked } = inject('changingStepsFunctions', {
  footerButtonClicked: {
    value: false
  }
})

function finish () {
  if (booked.value.redirectAfterBookingUrl) {
    window.location.href = booked.value.redirectAfterBookingUrl
  } else {
    window.location.reload()
  }
}

// * Watching when footer button was clicked
watchEffect(() => {
  if (footerButtonClicked.value) {
    finish()
  }
})

onMounted(() => {
  if (!store.getters['getRestoring']) {
    store.commit('setLoading', false)
  }
})

let stepsArray = inject('stepsArray')
let stepIndex = inject('stepIndex')

// * Form Key
let formKey = computed(() => store.getters['getFormKey'])

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

// * Colors
let amColors = inject('amColors')

const cssVars = computed(() => {
  if ( booked.value.type !== 'event') {
    return  {
      '--am-c-atc-text-op40': useColorTransparency(amColors.value.colorMainText, 0.4),
      '--am-c-atc-heading-text-op40': useColorTransparency(amColors.value.colorSbText, 0.4),
      '--am-c-atc-text-op30': useColorTransparency(amColors.value.colorMainText, 0.3),
      '--am-c-atc-text': amColors.value.colorMainText,
      '--am-c-atc-heading-text': amColors.value.colorSbText,
      '--am-c-atc-bgr-coverage': booked.value.type === 'package' && !(amSettings.value.general.addToCalendar && booked.value && booked.value.data.length) ? '50%' : '80%'
    }
  } else {
    return  {
      '--am-c-atc-text-op40': useColorTransparency(amColors.value.colorMainText, 0.4),
      '--am-c-atc-heading-text-op40': useColorTransparency(amColors.value.colorMainHeadingText, 0.4),
      '--am-c-atc-text-op30': useColorTransparency(amColors.value.colorMainText, 0.3),
      '--am-c-atc-text': amColors.value.colorMainText,
      '--am-c-atc-heading-text': amColors.value.colorMainHeadingText
    }
  }
})
</script>

<script>
export default {
  name: 'CongratulationsStep',
  label: 'event_congrats',
  key: 'congrats'
}
</script>

<style lang="scss">

</style>
