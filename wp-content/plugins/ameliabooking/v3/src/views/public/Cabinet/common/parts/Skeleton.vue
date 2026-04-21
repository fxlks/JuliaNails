<template>
  <el-skeleton animated class="am-asi-el-skeleton" :class="[{'am-asi': props.mainClass}]">
    <template #template>
      <div v-if="props.centerFirst" class="el-skeleton-item-wrapper-title">
        <el-skeleton-item variant="rect" />
      </div>

      <div v-for="item in new Array(props.count)" :key="item" class="el-skeleton-item-wrapper">
        <el-skeleton-item variant="rect" />
      </div>
    </template>
  </el-skeleton>
</template>

<script setup>
let props = defineProps({
  centerFirst: {
    type: Boolean,
    default: false
  },
  count: {
    type: Number,
    default: 3
  },
  mainClass: {
    type: String,
    default: ''
  },
})
</script>

<script>
export default {
  name: "AuthSkeleton"
}
</script>

<style lang="scss">
.amelia-v2-booking {
  #amelia-container {
    .am-asi {
      &-el-skeleton {
        padding-right: 2px;
        max-height: 440px;
        overflow-y: hidden;
        --el-skeleton-to-color: var(--am-c-skeleton-op60);
        --el-skeleton-color: var(--am-c-skeleton-op20);

        .el-skeleton-item-wrapper-title {
          width: 50%;
          margin: 0 auto;
        }

        .el-skeleton-item-wrapper, .el-skeleton-item-wrapper-title {
          padding: 6px 0;

          .el-skeleton__item {
            height: 56px;
          }
        }

        // Main Scroll styles
        &::-webkit-scrollbar {
          width: 8px;
        }

        &::-webkit-scrollbar-thumb {
          border-radius: 6px;
          background: var(--am-c-scroll-op30);
        }

        &::-webkit-scrollbar-track {
          border-radius: 6px;
          background: var(--am-c-scroll-op10);
        }
      }
    }
  }
}
</style>
