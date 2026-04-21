<template>
  <div
    v-if="!props.loading"
    class="am-fs__main-footer"
    :class="[
      { 'am-fs__main-footer-cp': props.secondButtonShow },
      { 'am-fs__main-footer-cp-mobile-s': mobileS && props.secondButtonShow },
      {
        'am-fs__main-footer__cart':
          props.addToCartButtonShow || props.backToCartButtonShow,
      },
      {
        'am-fs__main-footer__cart-mobile-s':
          (props.addToCartButtonShow || props.backToCartButtonShow) && mobileS,
      },
    ]"
    :style="cssVars"
  >
    <template v-if="ready">
      <AmButton
        v-if="props.secondButtonShow"
        category="secondary"
        :type="props.secondaryFooterButtonType"
        @click="secondButtonClick"
      >
        {{ displayLabels('congrats_panel') }}
      </AmButton>
      <AmButton
        v-if="props.addToCartButtonShow"
        class="am-button-cart"
        category="secondary"
        :type="props.addToCartButtonType"
        @click="addToCartButtonClick"
      >
        <span class="am-icon-plus"></span>{{ displayLabels('cart_add_button') }}
      </AmButton>
      <AmButton
        v-if="props.backToCartButtonShow"
        class="am-button-cart"
        category="secondary"
        :type="props.backToCartButtonType"
        @click="backToCartButtonClick"
      >
        {{ props.backToCartLabel }}
      </AmButton>
      <div
        v-if="props.paymentGateway === 'payPal' && !isCongratzStep"
        :id="'am-paypal-element-' + shortcodeData.counter"
      ></div>
      <AmButton
        v-if="props.paymentGateway !== 'payPal' || isCongratzStep"
        class="am-button-continue"
        :class="{ 'square-continue': props.paymentGateway === 'square' }"
        :disabled="isCongratzStep ? false : footerBtnDisabled"
        :type="props.primaryFooterButtonType"
        @click="continueClick"
      >
        {{ displayLabels(labelValue) }}
      </AmButton>
    </template>
    <template v-else>
      <!-- Skeleton -->
      <div class="am-fs__main-footer-skeleton">
        <el-skeleton animated>
          <template #template>
            <el-skeleton-item />
          </template>
        </el-skeleton>
      </div>
      <!-- /Skeleton -->
    </template>
  </div>
</template>

<script setup>
import AmButton from '../../../../_components/button/AmButton.vue'
import { computed, inject, ref, watch } from 'vue'
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation'

let props = defineProps({
  loading: {
    type: Boolean,
    default: false,
  },
  ready: {
    type: Boolean,
    default: true,
  },
  customizedLabels: {
    type: Object,
    default: () => {
      return {}
    },
  },
  primaryFooterButtonType: {
    type: String,
    default: 'filled',
  },
  secondaryFooterButtonType: {
    type: String,
    default: 'filled',
  },
  paymentGateway: {
    type: String,
    default: '',
  },
  secondButtonShow: {
    type: Boolean,
    required: false,
  },
  addToCartButtonShow: {
    type: Boolean,
    required: false,
  },
  addToCartButtonType: {
    type: String,
    required: 'text',
  },
  backToCartButtonShow: {
    type: Boolean,
    required: false,
  },
  backToCartButtonType: {
    type: String,
    required: 'text',
  },
  backToCartLabel: {
    type: String,
    default: '',
  },
})

const emits = defineEmits(['addToCart', 'backToCart'])

// * Step Functions
const { secondButtonClick } = inject('secondButton', {
  secondButtonClick: () => {},
})

const { footerButtonClick, footerBtnDisabled } = inject(
  'changingStepsFunctions',
  {
    footerButtonClick: () => {},
  }
)

function removeFocusFromButton(e) {
  if (e.target.classList.value.split(' ').indexOf('am-button') === -1) {
    e.target.parentNode.blur()
  } else {
    e.target.blur()
  }
}

function continueClick(e) {
  removeFocusFromButton(e)
  footerButtonClick()
}

// * Labels
const amLabels = inject('labels')

// * Check current step for button text (continue/finish)
const sidebarSteps = inject('sidebarSteps')
const stepIndex = inject('stepIndex')

let labelValue = ref('continue')

// flag for payPal button on congratz step
let isCongratzStep = ref(false)

watch(stepIndex, (value) => {
  if (value === sidebarSteps.value.length) {
    labelValue.value = 'finish_appointment'
    isCongratzStep.value = true
  } else {
    labelValue.value = 'continue'
  }
})

const shortcodeData = inject(
  'shortcodeData',
  ref({
    counter: 1000,
  })
)

function displayLabels(label) {
  return Object.keys(props.customizedLabels).length &&
    props.customizedLabels[label]
    ? props.customizedLabels[label]
    : amLabels[label]
}

function addToCartButtonClick() {
  emits('addToCart')
}

function backToCartButtonClick() {
  emits('backToCart')
}

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
let cssVars = computed(() => {
  return {
    '--am-c-main-text-op15': useColorTransparency(
      amColors.value.colorMainText,
      0.15
    ),
    '--am-c-success-op20': useColorTransparency(
      amColors.value.colorSuccess,
      0.2
    ),
  }
})

let cWidth = inject('containerWidth', 0)
let mobileS = computed(() => cWidth.value < 420)
</script>

<script>
export default {
  name: 'MainContentFooter',
}
</script>

<style lang="scss">
@mixin main-content-footer {
  // am -- amelia
  // fs -- form steps
  // Amelia Form Steps
  .am-fs {
    // Main Container
    &__main {
      &-footer {
        position: absolute;
        left: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        width: 100%;
        padding: 8px 32px;
        background-color: transparent;
        box-shadow: 0 -2px 3px var(--am-c-main-text-op15);

        // cp - customer panel - button on congratulations page
        &-cp,
        &-ba {
          display: flex;
          justify-content: space-between;

          &-mobile-s {
            padding: 8px;

            .am-button--secondary {
              padding: 8px;
            }
          }
        }

        &__cart {
          justify-content: space-between;

          &-mobile-s {
            padding: 8px;

            .am-button--secondary {
              padding: 8px;
            }
          }
        }

        .am-button-cart {
          .am-icon-plus {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--am-c-success);
            background-color: var(--am-c-success-op20);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin: 0 4px 0 0;
            &:before {
              width: 20px;
              height: 20px;
              line-height: 20px;
            }
          }
        }

        &-skeleton {
          .el-skeleton {
            padding: 0;
            &__item {
              width: 109px;
              height: 40px;
            }
          }
        }
      }
    }
  }
}

// Public
.amelia-v2-booking #amelia-container {
  @include main-content-footer;
}

// Admin
#amelia-app-backend-new {
  @include main-content-footer;
}
</style>
