<template>
  <el-skeleton v-show="loading" animated class="am-skeleton-ci">
    <template #template>
      <div
        v-for="(item) in new Array(10)"
        :key="item"
        class="am-skeleton-ci__inner"
      >
        <el-skeleton-item
          class="am-skeleton-ci__label"
          variant="text"
        />
        <el-skeleton-item
          class="am-skeleton-ci__input"
          variant="text"
        />
      </div>
    </template>
  </el-skeleton>
</template>

<script setup>
// * Import from Vue
import { computed } from "vue";

// * Import from Store
import { useStore } from "vuex";

const store = useStore()

let bookableType = computed(() => store.getters['bookableType/getType'])

let loading = computed(() => {
  if (bookableType.value === 'event') {
    return store.getters['getLoading']
  }

  return store.getters['booking/getLoading']
})
</script>

<style lang="scss">
.amelia-v2-booking #amelia-container {
  .am-skeleton-ci {
    &__inner {
      display: inline-flex;
      flex-direction: column;
      width: calc(50% - 8px);
      margin-bottom: 24px;

      &:nth-child(odd) {
        margin-right: 8px;
      }

      &:nth-child(even) {
        margin-left: 8px;
      }
    }

    &__label {
      height: 20px;
      width: 65%;
      margin-bottom: 6px;
    }

    &__input {
      height: 40px;
      width: 100%;
    }
  }
}
</style>