<template>
  <div>
    <span>{{ labelsDisplay('congrats_date') }}:</span>
    <span>{{ moment().format('YYYY-MM-DD') }}</span>
  </div>
  <div>
    <span>{{ labelsDisplay('congrats_time') }}:</span>
    <span>{{ moment().format('hh:mm') }}</span>
  </div>
  <div>
    <span>{{ labelsDisplay('congrats_service') }}:</span>
    <span>Service 1</span>
  </div>
  <div>
    <span>{{ labelsDisplay('congrats_employee') }}:</span>
    <span>Jane Doe</span>
  </div>
  <div v-if="!licence.isLite && !licence.isStarter">
    <span>{{ labelsDisplay('congrats_location') }}:</span>
    <span>Lorem Ipsum, 18</span>
  </div>
</template>

<script setup>
import moment from 'moment'
import { computed, inject } from 'vue'
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

// * Plugin Licence
let licence = inject('licence')

let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value].congratulations
      .translations &&
      amCustomize.value[pageRenderKey.value].congratulations.translations[
        label
      ] &&
      amCustomize.value[pageRenderKey.value].congratulations.translations[
        label
      ][langKey.value]
      ? amCustomize.value[pageRenderKey.value].congratulations.translations[
          label
        ][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}
</script>

<script>
export default {
  name: 'AppointmentInfoService',
}
</script>

<style lang="scss"></style>
