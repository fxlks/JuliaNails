<template>
  <div class="am-elf__main-content am-elf__congrats" :class="props.globalClass">
    <Congratulations
      type="event"
      :selected-item="selectedItem"
      :customer="customer"
    >
      <template #positionBottom>
        <AddToCalendar
          class="am-congrats__main-atc"
          :calendar-string="labelsDisplay('add_to_calendar')"
        ></AddToCalendar>
      </template>
    </Congratulations>
  </div>
</template>

<script setup>
// * Common Component
import Congratulations from '../../../common/Congratulations.vue'

// * Parts
import AddToCalendar from '../../../parts/AddToCalendar.vue'

// * Import from Vue
import { ref, computed, inject } from 'vue'

// * Composables
import { useReactiveCustomize } from '../../../../../../../assets/js/admin/useReactiveCustomize.js'

// * Component Poroperties
let props = defineProps({
  globalClass: {
    type: String,
    default: '',
  },
})

let selectedItem = ref({
  id: 348,
  title: 'Amelia Event',
})

let customer = ref({})

// * Customize Object
const { amCustomize } = useReactiveCustomize()

// * Form string recognition
let pageRenderKey = inject('pageRenderKey')

// * Form step string recognition
let stepName = inject('stepName')

// * Language string recognition
let langKey = inject('langKey')

// * Labels
let amLabels = inject('labels')

function labelsDisplay(label) {
  let computedLabel = computed(() => {
    let translations =
      amCustomize.value[pageRenderKey.value][stepName.value].translations
    return translations &&
      translations[label] &&
      translations[label][langKey.value]
      ? translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}
</script>

<script>
export default {
  name: 'CongratulationsStep',
  label: 'event_congrats',
  key: 'congrats',
}
</script>

<style lang="scss"></style>
