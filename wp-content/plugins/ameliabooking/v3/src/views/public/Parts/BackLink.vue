<template>
  <div v-if="licence.isLite && amSettings.general.backLink.enabled" class="am-lite-footer">
    <a
      rel="nofollow"
      class="am-lite-footer-link"
      :href="amSettings.general.backLink.url"
      target="_blank"
    >
      {{ amSettings.general.backLink.label }}
    </a>
  </div>
</template>

<script setup>
import { inject } from "vue";

let amSettings = inject('settings')

let licence = inject('licence')
</script>

<script>
export default {
  name: 'BackLinkBlock',
}
</script>

<style lang="scss">
// am -- amelia
// fs -- form steps

.amelia-v2-booking {
  .am-lite-footer {
    text-align: center;
  }
}
</style>
