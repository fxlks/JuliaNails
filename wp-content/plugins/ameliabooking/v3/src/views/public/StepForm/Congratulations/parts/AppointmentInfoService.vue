<template>
  <div class="am-fs__congrats-info-app-date">
    <span>{{amLabels.congrats_date}}:</span>
    <span>{{ getFrontedFormattedDate(date) }}</span>
  </div>
  <div  class="am-fs__congrats-info-app-time">
    <span>{{amLabels.congrats_time}}:</span>
    <span>{{ getFrontedFormattedTime(useFormatTime(time)) }}</span>
  </div>
  <div class="am-fs__congrats-info-app-service">
    <span>{{amLabels.congrats_service}}:</span>
    <span>{{ bookable ? bookable.name : '' }}</span>
  </div>
  <div class="am-fs__congrats-info-app-employee">
    <span>{{amLabels.congrats_employee}}:</span>
    <span>{{ employee ? employee.firstName + ' ' + employee.lastName : '' }}</span>
  </div>
  <div v-if="location" class="am-fs__congrats-info-app-location">
    <span>{{amLabels.congrats_location}}:</span>
    <span>{{ location.address ? location.address : location.name }}</span>
  </div>
  <div v-if="booked.data.length > 1" class="am-fs__congrats-info-app-recurring">
    <span>{{amLabels.recurring}}:</span>
    <span>{{ amLabels.appointment_repeats }} x{{booked.data.length}}</span>
  </div>
</template>

<script setup>
import { computed, inject } from 'vue'
import { useStore } from 'vuex'
import { getFrontedFormattedTime, getFrontedFormattedDate, useFormatTime } from '../../../../../assets/js/common/date'

const store = useStore()

const amLabels = inject('amLabels')

let booked = inject('booked')

let date = computed(() => booked.value ? booked.value.data[0].start.split(' ')[0] : '')

let time = computed(() => booked.value ? booked.value.data[0].start.split(' ')[1] : '')

let bookable = computed(() => {
  return booked.value ?
    store.getters['entities/getServices'].find(i => i.id === booked.value.data[0].serviceId) : null
})

let employee = computed(() => {
  return booked.value ?
    store.getters['entities/getEmployees'].find(i => i.id === booked.value.data[0].providerId) : null
})

let location =  computed(() => {
  return booked.value && booked.value.data[0].locationId ?
    store.getters['entities/getLocations'].find(i => i.id === booked.value.data[0].locationId) : null
})
</script>

<script>
export default {
  name: 'AppointmentInfoService'
}
</script>

<style lang="scss">

</style>