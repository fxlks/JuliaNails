<template>
  <div
    ref="amSkeletonContainer"
    class="am-skeleton-catalog__wrapper"
  >
    <el-skeleton
      animated
      class="am-skeleton-catalog"
    >
      <template #template>
        <div
          v-for="i in new Array(8)"
          :key="i"
          class="am-skeleton-catalog__item"
          :class="itemWidth"
        >
          <el-skeleton-item
            class="am-skeleton-catalog__item-inner"
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
  name: "CatalogSkeleton"
}
</script>

<style lang="scss">
.amelia-v2-booking #amelia-container.am-fc__wrapper {
  .am-skeleton-catalog {
    display: flex;
    align-items: unset;
    justify-content: center;
    flex-wrap: wrap;
    width: 100%;
    background-color: var(--am-c-main-bgr);
    border-radius: 12px;
    padding: 0;

    &__wrapper {
      width: 100%;
    }

    &__item {
      max-width: 25%;
      width: 100%;
      height: 148px;
      display: flex;
      background-color: transparent;
      padding: 8px;

      &.am-w33 {
        max-width: 33.33333%;
      }

      &.am-w50 {
        max-width: 50%;
      }

      &.am-w100 {
        max-width: 100%;
      }

      &-inner {
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg,var(--el-skeleton-color) 25%,var(--el-skeleton-to-color) 37%,var(--el-skeleton-color) 63%);
        background-size: 400% 100%;
        -webkit-animation: el-skeleton-loading 1.4s ease infinite;
        animation: el-skeleton-loading 1.4s ease infinite;
      }
    }
  }
}

@-webkit-keyframes el-skeleton-loading {
  0% {background-position: 100% 50%}
  100% {background-position: 0 50%}
}

@keyframes el-skeleton-loading {
  0% {background-position: 100% 50%}
  100% {background-position: 0 50%}
}
</style>