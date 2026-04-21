<template>
  <div>
    <span>{{props.labels.congrats_date}}:</span>
    <span>{{ getFrontedFormattedDate(date) }}</span>
  </div>
  <div>
    <span>{{props.labels.congrats_time}}:</span>
    <span>{{ getFrontedFormattedTime(useFormatTime(time)) }}</span>
  </div>
  <div>
    <span>{{props.labels.congrats_service}}:</span>
    <span>{{ bookable ? bookable.name : '' }}</span>
  </div>
  <div>
    <span>{{props.labels.congrats_employee}}:</span>
    <span>{{ employee ? employee.firstName + ' ' + employee.lastName : '' }}</span>
  </div>
  <div v-if="location">
    <span>{{props.labels.congrats_location}}:</span>
    <span>{{ location.address ? location.address : location.name }}</span>
  </div>
  <div v-if="booked.data.length > 1">
    <span>{{props.labels.recurring}}:</span>
    <span>{{ props.labels.appointment_repeats }} x{{booked.data.length}}</span>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  computed
} from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Composables
import {
  getFrontedFormattedTime,
  getFrontedFormattedDate,
  useFormatTime
} from '../../../../../assets/js/common/date'

const store = useStore()

let props = defineProps({
  booked: {
    type: Object,
    required: true
  },
  labels: {
    type: Object,
    required: true
  }
})

let date = computed(() => props.booked ? props.booked.data[0].start.split(' ')[0] : '')

let time = computed(() => props.booked ? props.booked.data[0].start.split(' ')[1] : '')

let bookable = computed(() => {
  return props.booked ?
    store.getters['entities/getServices'].find(i => i.id === props.booked.data[0].serviceId) : null
})

let employee = computed(() => {
  return props.booked ?
    store.getters['entities/getEmployees'].find(i => i.id === props.booked.data[0].providerId) : null
})

let location =  computed(() => {
  return props.booked && props.booked.data[0].locationId ?
    store.getters['entities/getLocations'].find(i => i.id === props.booked.data[0].locationId) : null
})
</script>

<script>
export default {
  name: 'AppointmentCongratsInfo'
}
</script>