<template>
  <!-- Skeleton -->
  <el-skeleton
    animated
    class="am-skeleton-congratz"
  >
    <template #template>
      <div class="am-skeleton-congratz-heading">
        <el-skeleton-item variant="circle" />
        <el-skeleton-item variant="h3" />
        <el-skeleton-item variant="text" />
      </div>
      <div class="am-skeleton-congratz-booking-info">
        <div
          v-for="(item, i) in new Array(10)"
          :key="item"
          :class="{'am-customer-info': (i === 6)}">
            <el-skeleton-item variant="text" :style="{width: `${useRandomIntFromInterval(14, 36)}%`}"/>
            <el-skeleton-item variant="text" :style="{width: `${useRandomIntFromInterval(14, 36)}%`}"/>
        </div>
      </div>
    </template>
  </el-skeleton>
  <!-- /Skeleton -->
</template>

<script setup>
// * Composables
import { useRandomIntFromInterval } from "../../../../../assets/js/common/formatting.js";
</script>

<script>
export default {
  name: "LoadingSkeleton"
}
</script>

<style lang="scss">
@mixin congrats-skeleton-block {
  .am-skeleton-congratz {

    &-heading {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 13px;

      :first-child {
        width: 52px;
        height: 52px;
        margin-bottom: 9px;
      }

      :nth-child(2) {
        height: 28px;
        max-width: 132px;
        margin-bottom: 4px;
      }

      :last-child {
        max-width: 148px;
        height: 18px;
      }
    }

    &-booking-info {
      & > div {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;

        .el-skeleton__item {
          height: 20px;
        }

      }

      .am-customer-info {
        border-bottom: 1px solid var(--am-c-skeleton-op20);
        padding-bottom: 12px;
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include congrats-skeleton-block;
}
</style>