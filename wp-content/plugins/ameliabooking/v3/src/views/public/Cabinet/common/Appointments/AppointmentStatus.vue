<template>
  <div>
    <AmSelect
      v-if="status && shortcodeData.cabinetType === 'employee'"
      v-model="status"
      :prefix-icon="
        bookingStatuses.find((i) => i.value === status).icon
      "
      :prefix-icon-color="
        bookingStatuses.find((i) => i.value === status).color
      "
      :disabled="loading"
      size="small"
      @click="(e) => e.stopPropagation()"
      @change="appointmentStatusChange"
    >
      <AmOption
        v-for="item in bookingStatuses"
        :key="item.value"
        :value="item.value"
        :label="item.label"
      >
        <span
          :class="`am-icon-${item.icon}`"
          :style="`color: ${item.color}`"
        ></span>
        {{ item.label }}
      </AmOption>
    </AmSelect>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  computed,
  inject,
  onMounted,
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Composables
import { useAuthorizationHeaderObject } from "../../../../../assets/js/public/panel";
import { useStatuses } from "../../../../../assets/js/admin/status";
import AmOption from "../../../../_components/select/AmOption.vue";
import AmSelect from "../../../../_components/select/AmSelect.vue";
import httpClient from "../../../../../plugins/axios";

// * Component props
let props = defineProps({
  id: {
    type: [Number],
    default: 0
  },
  status: {
    type: String,
    default: ''
  },
})

// * Data in shortcode
const shortcodeData = inject('shortcodeData')

// * Labels
let amLabels = inject('amLabels')

// * Store
let store = useStore()

// * Components emits
let emits = defineEmits(['statusChange'])

// * Booking Statuses
let bookingStatuses = computed(() => {
  return useStatuses().filter((i) =>
    i.value !== 'waiting'
  )
})

function appointmentStatusChange () {
  loading.value = true

  httpClient
    .post(
      '/appointments/status/' + props.id,
      {
        status: status.value,
      },
      Object.assign(
        useAuthorizationHeaderObject(store),
        {
          params: { source: 'cabinet-provider' },
        }
      )
    )
    .then((result) => {
      let message = amLabels.value.appointment_status_changed + (amLabels.value[result.data.data.status]).toLowerCase()
      let type = 'success'

      if ('maximumCapacityReached' in result.data.data && result.data.data.maximumCapacityReached === true) {
        message = amLabels.value.maximum_capacity_reached
        type = 'error'

        status.value = props.status
      }

      emits('statusChange', message, type)

      loading.value = false
    })
    .catch((e) => {
      let message = amLabels.value.error
      let type = 'error'

      if ('response' in e && 'data' in e.response && 'data' in e.response.data) {
        if ('timeSlotUnavailable' in e.response.data.data && e.response.data.data.timeSlotUnavailable === true) {
          message = amLabels.value.time_slot_unavailable
        }
      }

      status.value = props.status

      emits('statusChange', message, type)

      loading.value = false
    })
}

let loading = ref(false)

let status = ref('')

onMounted(() => {
  status.value = props.status
})
</script>

<script>
export default {
  name: 'CollapseCard'
}
</script>

<style lang="scss">
</style>
