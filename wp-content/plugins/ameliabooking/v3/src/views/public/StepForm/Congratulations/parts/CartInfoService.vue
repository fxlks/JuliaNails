<template>
  <div class="am-fs__congrats-info-cart">
    <span>{{amLabels.congrats_cart}}:</span>
    <span>{{ cart.length }}</span>
  </div>
</template>

<script setup>
import { computed, inject } from 'vue'
import { useStore } from 'vuex'
import { useCart } from '../../../../../assets/js/public/cart'

const store = useStore()

const amLabels = inject('amLabels')

let cart = computed(() => useCart(store).filter(i => i.serviceId && i.serviceId in i.services))
</script>

<script>
export default {
  name: 'CartInfoService'
}
</script>

<style lang="scss">

</style>
