<template>
  <!-- :class="{'am-elf__main-heading-mobile': mobile}" -->
  <div
    v-if="props.ready && !props.loading && !props.loadingUpcoming"
    class="am-el__header-inner"
    :style="cssVars"
  >
    <AmButton
      v-if="stepIndex < stepsArray.length - 1"
      class="am-heading-prev"
      :icon="IconArrowLeft"
      :icon-only="true"
      size="micro"
      type="plain"
      category="secondary"
      aria-label="Previous step"
      :disabled="props.loading"
      @click="previousStep"
    ></AmButton>
    <span class="am-el__header-inner__title">
      {{ displayLabels(stepsArray[stepIndex].label) }}
    </span>
  </div>
  <span v-else class="am-el__skeleton" :style="cssVars">
    <el-skeleton animated>
      <template #template>
        <el-skeleton-item />
      </template>
    </el-skeleton>
  </span>
</template>

<script setup>
import AmButton from '../../../_components/button/AmButton.vue'
import IconArrowLeft from '../../../_components/icons/IconArrowLeft'

// * Import from Vue
import { ref, computed, inject } from 'vue'

// * Composables
import { useColorTransparency } from '../../../../assets/js/common/colorManipulation'

let props = defineProps({
  customizedLabels: {
    type: Object,
    default: () => {
      return {}
    },
  },
  ready: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  loadingUpcoming: {
    type: Boolean,
    default: false,
  },
})

// * Popup steps array
let stepsArray = inject('stepsArray')

// * Step Index
const stepIndex = inject('stepIndex')

// * Step Functions
const { previousStep } = inject('changingStepsFunctions', {
  previousStep: () => {},
})

// * Non modified labels
let amLabels = inject('labels')

function displayLabels(label) {
  return Object.keys(props.customizedLabels).length &&
    props.customizedLabels[label]
    ? props.customizedLabels[label]
    : amLabels[label]
}

// * Container Width
// let cWidth = inject('containerWidth', 0)
// let checkScreen = computed(() => cWidth.value <= 560)
// let sidebarVisibility = amCustomize
// let mobile = computed(() => cWidth.value < 410)

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

// * Css Vars
let cssVars = computed(() => {
  return {
    '--am-c-el-text-op15': useColorTransparency(
      amColors.value.colorMainText,
      0.15
    ),
  }
})
</script>

<script>
export default {
  name: 'EventListHeader',
}
</script>

<style lang="scss">
@mixin am-dialog-event-list-header {
  // el - event list
  .am-el {
    &__header-inner {
      height: 60px;
      padding: 18px 24px;
      box-shadow: 0 2px 3px var(--am-c-el-text-op15);

      &__title {
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
        &-prev {
          margin-right: 12px;
          box-shadow: none;
        }

        &-next {
          margin-left: 12px;
          box-shadow: none;
        }
      }
    }

    &__skeleton {
      display: flex;
      flex-direction: row;
      align-items: center;
      padding: 18px 24px;
      box-shadow: 0 2px 3px var(--am-c-el-text-op15);

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
}

// Public
.amelia-v2-booking #amelia-container {
  @include am-dialog-event-list-header;
}

// Public
#amelia-app-backend-new #amelia-container {
  @include am-dialog-event-list-header;
}
</style>
