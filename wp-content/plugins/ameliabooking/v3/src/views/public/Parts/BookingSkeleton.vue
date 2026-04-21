<template>
  <el-skeleton v-show="loading" animated class="am-skeleton-payment">
    <template #template>
      <div class="am-skeleton-payment-heading">
        <el-skeleton-item variant="h3" />
      </div>
      <div class="am-skeleton-payment-booking-info">
        <div
            v-for="(item, i) in new Array(9)"
            :key="item"
            :class="{
              'am-skeleton-border-dashed': (i < 5),
              'am-skeleton-border-solid': ([5, 7].includes(i))
            }"
        >
          <el-skeleton-item variant="text" :style="{width: `${useRandomIntFromInterval(25, 28)}%`}"/>
          <el-skeleton-item v-if="i === 5" variant="text" :style="{width: '35%', height: i === 5 ? '32px':'', margin: '8px 0'}"/>
          <el-skeleton-item variant="text" :style="{width: `${useRandomIntFromInterval(25, 28)}%`, height: i === 5 ? '32px':''}"/>
        </div>
      </div>
      <el-skeleton-item variant="h3" :style="{width: '100%', height: '48px', marginTop: '20px'}"/>
      <el-skeleton-item variant="h3" :style="{width: '116px', height: '26px', marginTop: '16px'}"/>
      <div :style="{marginTop: '20px', display: 'flex', justifyContent: 'space-between', flexDirection: 'row'}">
        <el-skeleton-item v-for="i in new Array(3)" :key="i" variant="h3" :style="{width: '30%', height: '48px'}"/>
      </div>
    </template>
  </el-skeleton>
</template>

<script setup>
// * Import from Vue
import { computed } from "vue";
// * Import from Store
import { useStore } from "vuex";
// * Compsables
import { useRandomIntFromInterval } from "../../../assets/js/common/formatting";

const store = useStore()

let bookableType = computed(() => store.getters['bookableType/getType'])

let loading = computed(() => {
  if (bookableType.value === 'event') {
    return store.getters['getLoading']
  }

  return store.getters['booking/getLoading']
})

</script>

<script>
export default {
  name: 'BookingSkeleton',
}
</script>

<style lang="scss">
.amelia-v2-booking #amelia-container {
  .am-skeleton-payment {
    padding: 0;

    &-heading {
      .el-skeleton__item {
        height: 24px;
        width: 100px;
      }
    }

    &-booking-info {
      align-items: center;
      padding: 16px 0;
      & > div {
        padding: 4px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;

        &.am-skeleton-border-dashed {
          border-bottom: 1px solid var(--am-c-skeleton-op20);
          border-bottom-style: dashed;
        }

        &.am-skeleton-border-solid {
          border-bottom: 1px solid var(--am-c-skeleton-op20);
        }

        .el-skeleton__item {
          height: 20px;
        }
      }
    }
  }
}
</style>
