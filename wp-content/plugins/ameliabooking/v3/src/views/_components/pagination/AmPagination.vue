<template>
  <el-pagination
    v-model:current-page="currentPage"
    class="am-pagination"
    :small="props.small"
    :background="props.background"
    :page-size="props.pageSize"
    :default-page-size="props.defaultPageSize"
    :total="props.total"
    :page-count="props.pageCount"
    :pager-count="props.pagerCount"
    :default-current-page="props.defaultCurrentPage"
    :layout="props.layout"
    :page-sizes="props.pageSizes"
    :prev-text="props.prevText"
    :prev-icon="props.prevIcon"
    :next-text="props.nextText"
    :next-icon="props.nextIcon"
    :disabled="props.disabled"
    :hide-on-single-page="props.hideOnSinglePage"
    :popper-class="props.popperClass"
    :style="{ ...cssVars }"
    @size-change="(val) => emits('size-change', val)"
    @current-change="(val) => emits('current-change', val)"
    @prev-click="(val) => emits('prev-click', val)"
    @next-click="(val) => emits('next-click', val)"
  />
</template>

<script setup>
import {
  ref,
  inject,
  computed
} from 'vue'
import {useColorTransparency} from "../../../assets/js/common/colorManipulation";
import IconArrowLeft from '../icons/IconArrowLeft.vue'
import IconArrowRight from '../icons/IconArrowRight.vue'

// * Component Props
const props = defineProps({
  small: {
    type: Boolean,
    default: false
  },
  background: {
    type: Boolean,
    default: false
  },
  currentPage: {
    type: Number,
  },
  pageSize: {
    type: Number,
    default: 10
  },
  defaultPageSize: {
    type: Number,
  },
  total: {
    type: Number,
  },
  pageCount: {
    type: Number,
  },
  pagerCount: {
    type: Number,
    default: 7
  },
  defaultCurrentPage: {
    type: Number,
  },
  layout: {
    type: String,
    default: 'prev, pager, next, jumper, ->, total, slot'
  },
  pageSizes: {
    type: Array,
    default: () => [10, 20, 30, 40, 50, 100]
  },
  popperClass: {
    type: String,
  },
  prevText: {
    type: String,
  },
  prevIcon: {
    type: [String, Object, Function],
    default: () => IconArrowLeft,
  },
  nextText: {
    type: String,
    default: ''
  },
  nextIcon: {
    type: [String, Object, Function],
    default: () => IconArrowRight,
  },
  disabled: {
    type: Boolean,
    default: false
  },
  hideOnSinglePage: {
    type: Boolean
  }
})

// * Component Emits
const emits = defineEmits([
  'size-change',
  'current-change',
  'prev-click',
  'next-click',
  'update:current-page',
])

let currentPage = computed({
  get: () => {
    return props.currentPage
  },
  set: (val) => {
    emits('update:current-page', val)
  }
})

// * Font Vars
let amFonts = inject('amFonts', ref({
  fontFamily: 'Amelia Roboto, sans-serif',
  fontUrl: '',
  customFontFamily: '',
  fontFormat: '',
  customFontSelected: false
}))

// * Color Vars
let amColors = inject('amColors', ref({
  colorPrimary: '#1246D6',
  colorMainBgr: '#FFFFFF',
  colorMainText: '#1A2C37',
}))

// * CSS Variables
let cssVars = computed(() => {
  return {
    '--am-c-pagination-bgr': amColors.value.colorMainBgr,
    '--am-c-pagination-text': amColors.value.colorMainText,
    '--am-c-pagination-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-c-pagination-border': amColors.value.colorMainBgr,
    '--am-c-pagination-active': amColors.value.colorPrimary,
    '--am-rad-pagination': '6px',
    '--am-font-family': amFonts.value.fontFamily,
  }
})
</script>

<style lang="scss">
@mixin am-pagination-block {
  .am-pagination {
    // -c-    color
    // -rad-  border radius
    // -fs-   font size
    // -bgr   background

    display: flex;
    align-items: center;
    justify-content: center;

    * {
      box-sizing: border-box;
      font-family: var(--am-font-family), sans-serif;
    }

    .el-pager {
      display: flex;
      align-items: center;
      justify-content: center;

      .number {
        width: 28px;
        min-width: 28px;
        height: 28px;
        font-size: 14px;
        font-weight: 500;
        line-height: 26px;
        color: var(--am-c-pagination-text);
        background: var(--am-c-pagination-bgr);
        border: 2px solid var(--am-c-pagination-border);
        border-radius: var(--am-rad-pagination);
        margin: 2px;

        &.is-active {
          --am-c-pagination-bgr: var(--am-c-primary);
          --am-c-pagination-border: var(--am-c-primary);
          --am-c-pagination-text: var(--am-c-card-bgr, var(--am-c-main-bgr));
        }

        &:hover {
          --am-c-pagination-border: var(--am-c-primary);
        }
      }

      .btn-quickprev,
      .btn-quicknext {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        &.el-icon {
          color: var(--am-c-pagination-text);
          background-color: transparent;

          &:hover {
            --am-c-pagination-text: var(--am-c-primary);
          }
        }
      }
    }

    .btn-next,
    .btn-prev {
      color: var(--am-c-pagination-text);
      background-color: var(--am-c-pagination-bgr);

      .el-icon {
        display: flex;
      }

      &:not(:disabled):hover {
        --am-c-pagination-text: var(--am-c-primary);
      }

      &:disabled {
        --am-c-pagination-text: var(--am-c-pagination-text-op60);
      }
    }
  }
}

// public
.amelia-v2-booking #amelia-container {
  @include am-pagination-block;
}

// admin
#amelia-app-backend-new {
  @include am-pagination-block;
}
</style>