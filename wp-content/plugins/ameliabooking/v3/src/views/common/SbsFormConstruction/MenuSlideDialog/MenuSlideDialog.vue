<template>
  <AmSlidePopup
    :visibility="visibility"
    :position="position"
    custom-class="am-msd"
    :close-outside="true"
    :custom-css="{...customCss, ...cssVars}"
    @update:visibility="emits('update:visibility', false)"
  >
    <div class="am-msd__item-wrapper">
      <div
        class="am-msd__item"
        @click="closeMenu"
      >
        <div class="am-msd__item-inner">
          <transition name="fade">
            <p
              class="am-msd__item-heading"
              :class="{'am-rtl': isRtl}"
            >
              {{displayLabels('menu_title')}}
            </p>
          </transition>
          <transition name="fade">
            <div class="am-msd__item-icon">
              <span class="am-icon-close"></span>
            </div>
          </transition>
        </div>
      </div>
      <template
        v-for="(step, index) in menuItems"
        :key="step.key"
      >
        <div
          v-if="step.key !== 'packages' || store.getters['entities/getPackages'].length"
          class="am-msd__item"
          :class="{'selected': monitor === step.key}"
          @click="menuSelection(step, index)"
        >
          <div class="am-msd__item-inner">
            <div class="am-msd__item-icon">
              <span :class="`am-icon-${step.icon}`"></span>
            </div>
            <transition name="fade">
              <p
                class="am-msd__item-heading"
                :class="{'am-rtl': isRtl}"
              >
                {{ step.label }}
              </p>
            </transition>
            <transition name="fade">
              <div
                class="am-msd__item-indicator"
                :class="{'am-rtl': isRtl}"
              >
                <span :class="isRtl ? 'am-icon-arrow-big-left' : 'am-icon-arrow-big-right'"></span>
              </div>
            </transition>
          </div>
        </div>
        <div
          v-if="index === 0"
          class="am-msd__item-divider"
        ></div>
      </template>
    </div>

    <template #footer>
      <div
        class="am-msd__item"
        @click="() => emits('logout')"
      >
        <div class="am-msd__item-inner">
          <div class="am-msd__item-icon">
            <span class="am-icon-logout"></span>
          </div>
          <p class="am-msd__item-heading">
            {{displayLabels('log_out')}}
          </p>
        </div>
      </div>
    </template>
  </AmSlidePopup>
</template>

<script setup>
// * _components
import AmSlidePopup from "../../../_components/slide-popup/AmSlidePopup.vue";

// * Import from Vue
import {
  computed,
  inject
} from "vue";

import {useStore} from "vuex";

// * Components Props
let props = defineProps({
  visibility: {
    type: Boolean,
    required: true
  },
  customizedLabels: {
    type: Object,
    default: () => {
      return {}
    }
  },
  monitor: {
    type: String,
    required: true
  },
  menuItems: {
    type: Array,
    required: true
  },
  customCss: {
    type: Object,
    default: () => {}
  },
  width: {
    type: Number,
    default: 300
  },
  position: {
    type: String,
    default: 'right',
    validator(value) {
      return ['right', 'left', 'top', 'bottom'].includes(value)
    }
  }
})

// * Components Emits
let emits = defineEmits(['click', 'update:visibility', 'logout'])

let amLabels = inject('labels')

const store = useStore()

// * Component text orientation
let isRtl = computed(() => {
  if (document) {
    return document.documentElement.dir === 'rtl'
  }

  return false
})

function menuSelection (step, index) {
  let obj = {step, index}
  emits('click', obj)
  emits('update:visibility', false)
}

function closeMenu () {
  emits('update:visibility', false)
}

function displayLabels (label) {
  return Object.keys(props.customizedLabels).length && props.customizedLabels[label] ? props.customizedLabels[label] : amLabels[label]
}

let cssVars = computed(() => {
  return {
    '--am-mw-msd': `${props.width}px`
  }
})
</script>

<style lang="scss">
@mixin menu-slide-dialog {
  // am -- amelia
  // msd -- menu slide dialog
  .am-msd {
    flex-direction: column;
    justify-content: space-between;
    font-size: 16px;
    color: var(--am-c-msd-text);
    transition: width .3s ease-in-out;

    &.am-slide-popup__block-inner {
      display: flex;
      border-radius: 0.5rem 0 0 0.5rem;
      background-color: var(--am-c-msd-bgr);
      padding: 16px 8px 16px 16px;

      &.am-position-right, &.am-position-left {
        max-width: var(--am-mw-msd);
        width: 100%;
      }

      &.am-position-top {
        border-radius: 8px 8px 0 0;
      }

      &.am-position-right {
        border-radius: 0 8px 8px 0;
      }

      &.am-position-left {
        border-radius: 8px 0 0 8px;
      }

      &.am-position-bottom {
        border-radius: 0 0 8px 8px;
      }
    }

    * {
      box-sizing: border-box;
      font-family: var(--am-font-family);
    }

    // Sidebar Step
    &__item {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-radius: 4px;
      padding: 8px;
      margin-bottom: 8px;

      $count: 100;
      @for $i from 0 through $count {
        &:nth-child(#{$i + 1}) {
          animation: 400ms cubic-bezier(.45,1,.4,1.2) #{$i*70}ms am-animation-slide-right;
          animation-fill-mode: both;
        }
      }

      &.selected {
        background-color: var(--am-c-msd-text-op10);

        .am-msd__item-indicator {
          display: flex;
        }
      }

      // Sidebar Step Wrapper
      &-wrapper {
        padding: 0 8px 0 0;
        overflow-x: hidden;

        // Main Scroll styles
        &::-webkit-scrollbar {
          width: 6px;
        }

        &::-webkit-scrollbar-thumb {
          border-radius: 6px;
          background: var(--am-c-scroll-op30);
        }

        &::-webkit-scrollbar-track {
          border-radius: 6px;
          background: var(--am-c-scroll-op10);
        }

        .el-skeleton {
          .el-skeleton__text {
            --el-skeleton-color: var(--am-c-skeleton-sb-op20);
            --el-skeleton-to-color: var(--am-c-skeleton-sb-op60);
          }
          padding: 0;
        }
      }

      // Sidebar Step Inner
      &-inner {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }

      // Sidebar Step Icon
      &-icon {
        position: relative;
        width: 24px;
        height: 20px;
        font-size: 24px;

        span {
          display: block;
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
        }
      }

      // Sidebar Step Heading
      &-heading {
        font-family: var(--am-font-family);
        font-size: 14px;
        font-weight: 500;
        line-height: 1.43;
        margin: 0 auto 0 6px;

        &.am-rtl {
          margin: 0 6px 0 auto;
        }

        &.fade-enter-active {
          animation: sidebar-step-selection 1s;
        }

        &.fade-leave-active {
          animation: sidebar-step-selection 1s reverse;
        }
      }

      // Sidebar Item Indicator
      &-indicator {
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-left: auto;

        &.am-rtl {
          margin-left: 0;
          margin-right: auto;
        }
      }

      // Sidebar Item Divider
      &-divider {
        border-top: 1px solid var(--am-c-msd-text-op60);
        margin: 16px 0;
      }
    }
  }
}

@keyframes sidebar-step-selection {
  0% {
    opacity: 0;
    transform: translateX(-100%);
  }
  50% {
    transform: translateX(10%);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}

// Public
.amelia-v2-booking #amelia-container {
  @include menu-slide-dialog;
}

// Admin
#amelia-app-backend-new {
  @include menu-slide-dialog;
}
</style>
