<template>
  <span :class="`am-icon-${icon}`"></span>
</template>

<script setup>
defineProps({
  icon: {
    type: String,
    required: true
  }
})
</script>

<script>
export default {
  name: "IconComponent"
}
</script>