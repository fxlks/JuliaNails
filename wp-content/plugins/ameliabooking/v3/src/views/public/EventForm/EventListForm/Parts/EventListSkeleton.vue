<template>
  <div
    ref="amSkeletonContainer"
    class="am-skeleton-evl__wrapper"
  >
    <el-skeleton
      animated
      class="am-skeleton-evl"
    >
      <template #template>
        <div
          v-for="i in new Array(props.displayNumber)"
          :key="i"
          class="am-skeleton-evl__item"
          :class="itemWidth"
        >
          <el-skeleton-item
            class="am-skeleton-evl__item-inner"
            variant="text"
          />
        </div>
      </template>
    </el-skeleton>
  </div>
</template>

<script setup>
import {
  ref,
  computed,
  onMounted
} from "vue";

let props = defineProps({
  displayNumber: {
    type: Number,
    required: true
  }
})

// * Component reference
let amSkeletonContainer = ref(null)

// * Plugin wrapper width
let containerWidth = ref(0)

// * window resize listener
window.addEventListener('resize', resize);

// * resize function
function resize() {
  if (amSkeletonContainer.value) {
    containerWidth.value = amSkeletonContainer.value.offsetWidth
  }
}

onMounted(() => {
  if (amSkeletonContainer.value) {
    containerWidth.value = amSkeletonContainer.value.offsetWidth
  }
})

let itemWidth = computed(() => {
  if (containerWidth.value <= 500) {
    return 'am-w100'
  }

  if (containerWidth.value <= 600) {
    return 'am-w50'
  }

  if (containerWidth.value <= 768) {
    return 'am-w33'
  }

  return ''
})
</script>

<script>
export default {
  name: "EventListSkeleton"
}
</script>

<style lang="scss">

.amelia-v2-booking #amelia-container {
  // am - amelia
  // evl - event list
  .am-els {
    .am-skeleton-evl {
      padding: 0 0 0 0;

      &__wrapper {
        max-width: 792px;
        margin: 0 auto;
      }

      &__item {
        &-inner {
          height: 89px;
        }
      }
    }
  }
}

</style>
