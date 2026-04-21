<template>
  <div
    ref="amSkeletonContainer"
    class="am-skeleton-els-filters"
  >
    <el-skeleton
      animated
      class="am-skeleton-els-filters-search"
    >
      <template #template>
        <div
          class="am-skeleton-els__item"
        >
          <el-skeleton-item
            class="am-skeleton-els__item-inner"
            variant="text"
          />
        </div>
      </template>
    </el-skeleton>

    <el-skeleton
      animated
      class="am-skeleton-els-filters-button"
    >
      <template #template>
        <div
          class="am-skeleton-els__item"
        >
          <el-skeleton-item
            class="am-skeleton-els__item-inner"
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
</script>

<script>
export default {
  name: "EventListFiltersSkeleton"
}
</script>

<style lang="scss">

.amelia-v2-booking #amelia-container {
  // am - amelia
  // els - event list
  .am-els__filters {
    .am-skeleton-els-filters {
      margin-bottom: 10px;

      .el-skeleton {
        padding: 0 0 0 0;
      }

      .am-skeleton-els-filters-search {
        display: inline-block;
        width: 84%;
        margin-right: 10px;

        div {
          div {
            height: 40px;
          }
        }
      }

      .am-skeleton-els-filters-button {
        display: inline-block;
        width: 14%;

        div {
          div {
            height: 40px;
          }
        }
      }
    }
  }
}

</style>
