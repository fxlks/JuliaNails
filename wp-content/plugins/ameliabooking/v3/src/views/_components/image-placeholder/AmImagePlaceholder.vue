<template>
  <span :class="props.itemClass" :style="{...cardImage(props.itemData)}">
    {{ cardSign(props.itemData) }}
  </span>
</template>

<script setup>
import { amCardColors } from "../../../assets/js/common/colorManipulation";

let props = defineProps({
  itemClass: {
    type: String,
    default: ''
  },
  itemData: {
    type: Object,
    default: () => {}
  },
  trimString: {
    type: Number,
    default: 2
  }
})

function cardImage(itemData) {
  if (itemData.pictureFullPath) return {backgroundImage: `url(${itemData.pictureFullPath})`}

  return {backgroundColor: `${amCardColors.value[Math.floor(Math.random() * amCardColors.value.length)]}`}
}

function cardSign(itemData) {
  if (!itemData.pictureFullPath) {
    let name = 'firstName' in itemData ? `${itemData.firstName} ${itemData.lastName}` : itemData.name
    return name.split(' ').map((s) => s.charAt(0)).join('').toUpperCase().substring(0, props.trimString).replace(/[^\w\s]/g, '')
  }
  return ''
}
</script>

<script>
export default {
  name: "AmImagePlaceholder"
}
</script>