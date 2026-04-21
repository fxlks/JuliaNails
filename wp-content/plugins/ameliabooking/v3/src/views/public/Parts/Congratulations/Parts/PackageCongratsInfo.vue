<template>
  <div>
    <span>{{amLabels.congrats_package}}</span>
    <span>{{ bookable.name }}</span>
  </div>
</template>

<script setup>

import { computed, inject } from 'vue'
import { useStore } from 'vuex'

const store = useStore()

const amLabels = inject('amLabels')

let booked = inject('booked')

let bookable = computed(() => {
  store.commit('booking/setBookableType', 'package')
  return booked.value ? store.getters['entities/getBookableFromBookableEntities'](
    store.getters['booking/getSelection']
  ) : null
})

</script>


<script>
export default {
  name: 'PackageInfoService'
}
</script>

<style lang="scss">

</style>
