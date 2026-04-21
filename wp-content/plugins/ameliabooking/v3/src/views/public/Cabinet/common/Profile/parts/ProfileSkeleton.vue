<template>
  <el-skeleton animated class="am-cps" :style="cssVars">
    <template #template>
      <div
        v-for="item in new Array(props.count)"
        :key="item"
        class="am-cps__item"
        :class="responsiveClass"
      >
        <el-skeleton-item class="am-cps__item-label" variant="rect" />
        <el-skeleton-item class="am-cps__item-input" variant="rect" />
      </div>
    </template>
  </el-skeleton>
</template>

<script setup>
// * Import from Vue
import {
  computed,
  inject
} from "vue";

// * Composables
import { useColorTransparency } from "../../../../../../assets/js/common/colorManipulation";
import { useResponsiveClass } from "../../../../../../assets/js/common/responsive";

let props = defineProps({
  pageWidth: {
    type: Number,
    default: 768,
  },
  count: {
    type: [Number, String],
    default: 2
  },
  itemDirection: {
    type: String,
    default: 'row',
    validator(value) {
      return ['row', 'row-reverse', 'column', 'column-reverse', 'initial', 'revert', 'unset' ].includes(value)
    }
  }
})

let responsiveClass = computed(() => {
  return useResponsiveClass(props.pageWidth)
})

let amColors = inject('amColors');

let cssVars = computed(() => {
  return {
    '--am-c-skeleton-op20': useColorTransparency(amColors.value.colorMainText, 0.2),
    '--am-c-skeleton-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-c-flex-direction': props.itemDirection
  }
})
</script>

<style lang="scss">
.amelia-v2-booking {
  #amelia-container {
    // cps - cabinet profile skeleton
    .am-cps {
      --el-skeleton-to-color: var(--am-c-skeleton-op60);
      --el-skeleton-color: var(--am-c-skeleton-op20);
      display: flex;
      flex-wrap: wrap;
      align-items: flex-start;
      justify-content: space-between;
      flex-direction: var(--am-c-flex-direction);

      &__item {
        display: flex;
        flex-direction: column;
        width: calc(50% - 12px);

        &.am-rw-480 {
          width: 100%;
        }

        &-label {
          height: 20px;
          margin: 0 0 4px;
          max-width: 50%;
        }

        &-input {
          height: 40px;
          margin: 0 0 24px;
        }
      }
    }
  }
}
</style>