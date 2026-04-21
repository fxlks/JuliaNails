<template>
  <AmSelect
    v-model="timeZoneSelection"
    :filterable="true"
    :placeholder="useCurrentTimeZone()"
    prefix-icon="globe-watch"
  >
    <AmOption
      v-for="(timeZone, index) in timeZones"
      :key="index"
      :value="timeZone"
      :label="timeZone"
    >
      {{ timeZone }}
    </AmOption>
  </AmSelect>
</template>

<script setup>
// * Import from Vue
import {
  inject,
  onBeforeMount,
  ref
} from 'vue'

// * Composables
import { useCurrentTimeZone } from "../../../../../../../assets/js/common/helper";

// * _components
import AmOption from "../../../../../../_components/select/AmOption.vue";
import AmSelect from "../../../../../../_components/select/AmSelect.vue";

const amSettings = inject('settings')
const adminTimeZone = inject('timeZone')

let timeZoneSelection = ref('')

const timeZones = ref([])

onBeforeMount(() => {
  if (!amSettings.general.showClientTimeZone) {
    timeZoneSelection.value = adminTimeZone.value
  } else {
    timeZoneSelection.value = useCurrentTimeZone()
  }
})
</script>

<style lang="scss">

</style>