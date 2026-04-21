<template>
  <div
    class="am-fs__main-heading"
    :class="{ 'am-fs__main-heading-mobile': mobile }"
    role="region"
    :style="cssVars"
  >
    <span
      v-if="
        stepIndex in sidebarSteps && 'label' in sidebarSteps[stepIndex] && ready
      "
      class="am-fs__main-heading-inner"
    >
      <AmButton
        v-if="stepIndex > 0"
        class="am-heading-prev"
        :icon="{
          components: { IconComponent },
          template: `<IconComponent icon=${
            isRtl ? 'arrow-right' : 'arrow-left'
          } />`,
        }"
        :icon-only="true"
        aria-label="previous step"
        size="micro"
        type="plain"
        category="secondary"
        :disabled="loading"
        @click="previousStep"
      ></AmButton>
      <span class="am-fs__main-heading-inner-title">
        {{
          stepIndex in sidebarSteps && 'label' in sidebarSteps[stepIndex]
            ? sidebarSteps[stepIndex].label
            : ''
        }}
      </span>
    </span>
    <span v-else class="am-fs__main-heading-skeleton">
      <el-skeleton animated>
        <template #template>
          <el-skeleton-item />
        </template>
      </el-skeleton>
    </span>
  </div>
  <!-- Step Slider for Small screen -->
  <template v-if="checkScreen || !sidebarVisible">
    <div class="am-progress-bar" :style="cssVars">
      <span
        v-for="(step, i) in marks"
        :key="i"
        class="am-step"
        :class="
          i === sliderValue
            ? 'am-current-step'
            : i < sliderValue
            ? 'am-passed-step'
            : ''
        "
      >
      </span>
    </div>
  </template>
  <!-- /Step Slider for Small screen -->
</template>

<script setup>
import AmButton from '../../../../_components/button/AmButton.vue'
import IconComponent from '../../../../_components/icons/IconComponent.vue'

import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation'
import { ref, computed, inject } from 'vue'
import { useStore } from 'vuex'

const store = useStore()

defineProps({
  sidebarVisible: {
    type: Boolean,
    default: true,
  },
  ready: {
    type: Boolean,
    default: true,
  },
})

let loading = computed(() => store.getters['booking/getLoading'])

let isRtl = computed(() => store.getters['getIsRtl'])

// * Step Index
const stepIndex = inject('stepIndex', 0)

// * Step Functions
const { previousStep } = inject('changingStepsFunctions', {
  previousStep: () => {},
})

// * Data that are collected form component steps that are used in Sidebar
let sidebarSteps = inject('sidebarSteps', [
  {
    label: '',
    stepSelectedData: [],
    finished: false,
    selected: false,
  },
])

// * Slider Value
const sliderValue = computed(() => stepIndex.value)

// * Create marks from number of steps
const marks = new Array(sidebarSteps.value.length).fill('')

// * Container Width
let cWidth = inject('containerWidth', 0)
let checkScreen = computed(() => cWidth.value <= 560)
// let sidebarVisibility = amCustomize
let mobile = computed(() => cWidth.value < 410)

// * Colors
let amColors = inject(
  'amColors',
  ref({
    colorPrimary: '#1246D6',
    colorSuccess: '#019719',
    colorError: '#B4190F',
    colorWarning: '#CCA20C',
    colorMainBgr: '#FFFFFF',
    colorMainHeadingText: '#33434C',
    colorMainText: '#1A2C37',
    colorSbBgr: '#17295A',
    colorSbText: '#FFFFFF',
    colorInpBgr: '#FFFFFF',
    colorInpBorder: '#D1D5D7',
    colorInpText: '#1A2C37',
    colorInpPlaceHolder: '#1A2C37',
    colorDropBgr: '#FFFFFF',
    colorDropBorder: '#D1D5D7',
    colorDropText: '#0E1920',
    colorBtnPrim: '#265CF2',
    colorBtnPrimText: '#FFFFFF',
    colorBtnSec: '#1A2C37',
    colorBtnSecText: '#FFFFFF',
  })
)

// * Calculate Step Width
let cssVars = computed(() => {
  return {
    '--am-step-length': `${100 / marks.length}%`,
    '--am-c-main-heading-text-op15': useColorTransparency(
      amColors.value.colorMainText,
      0.15
    ),
  }
})
</script>

<style lang="scss">
@mixin main-content-header {
  .am-fs {
    &__main {
      &-heading {
        padding: 16px 32px;
        box-shadow: 0 2px 3px var(--am-c-main-heading-text-op15);

        &-inner {
          display: flex;
          align-items: center;
          gap: 0 12px;

          &-title {
            font-family: var(--am-font-family);
            font-size: 18px;
            font-weight: 500;
            font-style: normal;
            line-height: 1.55;
            text-transform: initial;
            letter-spacing: initial;
            color: var(--am-c-main-heading-text);
            margin: 0;
            white-space: nowrap;

            &:before {
              // theme Twenty Nineteen
              display: none;
            }
          }

          .am-heading {
            &-prev,
            &-next {
              box-shadow: none;
            }
          }
        }

        &-skeleton {
          display: flex;
          flex-direction: row;
          align-items: center;
          .el-skeleton {
            padding: 0;
            &__item {
              width: 170px;
              height: 28px;
            }
          }
        }

        &-mobile {
          padding: 16px;
        }
      }

      .am-progress-bar {
        height: 2px;
        display: flex;
        flex-direction: row;
        gap: 4px;
        width: 100%;

        .am-step {
          height: 2px;
          width: var(--am-step-length);
          background: #d1d5d7;
        }

        .am-passed-step {
          background: var(--am-c-btn-prim);
        }

        .am-current-step {
          background: #808a90;
        }
      }
    }
  }
}
// am -- amelia
// fs -- form steps

// Public
.amelia-v2-booking #amelia-container {
  @include main-content-header;
}

// Admin
#amelia-app-backend-new {
  @include main-content-header;
}
</style>
