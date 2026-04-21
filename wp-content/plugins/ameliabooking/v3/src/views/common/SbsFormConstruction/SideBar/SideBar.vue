<template>
  <div :style="cssVars" role="complementary">
    <slot name="step-list"></slot>
    <slot name="support-info"></slot>
  </div>
</template>

<script setup>

// * Color Vars
import {
  ref,
  computed,
  inject
} from "vue";
import { useColorTransparency } from "../../../../assets/js/common/colorManipulation";

let amColors = inject('amColors', ref({
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
}))

// * Css Variables
let cssVars = computed(() => {
  return {
    '--am-c-sb-text-op5': useColorTransparency(amColors.value.colorSbText, 0.05),
    '--am-c-sb-text-op10': useColorTransparency(amColors.value.colorSbText, 0.1),
    '--am-c-sb-text-op60': useColorTransparency(amColors.value.colorSbText, 0.6),
    '--am-c-scroll-op30': useColorTransparency(amColors.value.colorSbText, 0.30),
    '--am-c-scroll-op10': useColorTransparency(amColors.value.colorSbText, 0.10),
  }
})
</script>

<script>
export default {
  name: "SideBar"
}
</script>

<style lang="scss">
//@import "../../../../../src/assets/scss/common/variables/variables-colors";
@mixin step-sidebar-container {
  // am -- amelia
  // fs -- form steps
  // sb -- sidebar
  // Amelia Form Steps
  .am-fs{
    // Sidebar
    &-sb {
      flex: 0 0 auto;
      position: relative;
      max-width: 240px;
      width: 240px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      font-size: 16px;
      color: var(--am-c-sb-text);
      border-radius: 0.5rem 0 0 0.5rem;
      background-color: var(--am-c-sb-bgr);
      padding: 16px 8px 16px;
      transition: width .3s ease-in-out;

      &.am-rtl {
        border-radius: 0 0.5rem 0.5rem 0;
      }

      &.am-collapsed {
        transition-delay: 1s;
      }

      * {
        font-family: var(--am-font-family);
      }

      // Sidebar Step
      &__step {
        background-color: var(--am-c-sb-text-op5);
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

        // Sidebar Step Wrapper
        &-wrapper {
          padding: 0 8px 0;
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

          .am-fs-sb__step-icon__number {
            top: -14px;
            left: -16px;
            transform: translate(0, 0);
            font-size: 12px;
            width: 18px;
            height: 18px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: var(--am-c-error);
            color: var(--am-c-sb-text);

            &.am-collapsed {
              width: 16px;
              height: 16px;
              font-size: 10px;
            }

            &.am-rtl {
              left: auto;
              right: -16px;
            }
          }
        }

        // Sidebar Step Heading
        &-heading {
          font-family: var(--am-font-family);
          font-size: 14px;
          font-weight: 500;
          line-height: 1.43;
          margin: 0 0 0 6px;

          &.fade-enter-active {
            animation: sidebar-step-selection 1s;
          }

          &.fade-leave-active {
            animation: sidebar-step-selection 1s reverse;
          }
        }

        // Sidebar Step Check State
        // Default
        &-checker {
          --am-c-sb-checker-border: var(--am-c-sb-text-op10, #{fade-out($am-white, 0.9)});
          display: flex;
          align-items: center;
          justify-content: center;
          width: 24px;
          height: 24px;
          background-color: var(--am-c-sb-bgr);
          border-radius: 50%;
          border: 1px solid var(--am-c-sb-checker-border);
          transition: border 0.3s ease-in-out;
          margin-left: auto;

          &.am-rtl {
            margin-right: auto;
            margin-left: 0;
          }

          // Selected
          &-selected {
            --am-c-sb-checker-border: var(--am-c-primary);
            border-width: 4px;

            // Sidebar Collapsed
            &.am-collapsed {
              border-width: 2px;
            }
          }

          .am-icon-check {
            position: absolute;
            width: 18px;
            height: 18px;
            font-size: 18px;
            line-height: 0;
            padding: 4px;
            border-radius: 50%;
            background-color: var(--am-c-success);
            color: var(--am-c-sb-bgr);

            &:before {
              position: absolute;
              top: 50%;
              left: 50%;
              transform: translate(-50%, -50%);
            }

            &.fade-enter-active {
              animation: sidebar-step-check-state 1s;
            }

            &.fade-leave-active {
              animation: sidebar-step-check-state 1s reverse;
            }
          }

          // Sidebar Collapsed
          &.am-collapsed {
            width: 16px;
            height: 16px;
            position: absolute;
            bottom: -4px;
            right: -4px;

            .am-icon-check {
              position: absolute;
              width: 14px;
              height: 14px;
              font-size: 16px;
            }
          }
        }

        &-selection {
          font-family: var(--am-font-family);
          font-size: 12px;
          font-weight: 400;
          line-height: 1.5;
          padding: 4px 0;
          margin: 0;
          border-bottom: 1px dashed var(--am-c-sb-text-op10);
          white-space: pre;

          &.fade-enter-active {
            animation: sidebar-step-selection 1s;
          }

          &.fade-leave-active {
            animation: sidebar-step-selection 1s reverse;
          }

          &:last-child {
            border-bottom: none;
            padding-bottom: 0;
          }

          &__wrapper {
            display: block;
            overflow: hidden;
          }

          &-packages {
            max-height: 100px;
          }

        }
      }

      // Sidebar Step
      &__page {
        display: flex;
        min-height: 40px;
        background-color: transparent;
        border-radius: 4px;
        padding: 8px;
        margin-bottom: 8px;
        cursor: pointer;

        &:hover {
          background-color: var(--am-c-sb-text-op10);

          .am-fs-sb__page-indicator {
            display: flex;
          }
        }

        &.selected {
          background-color: var(--am-c-sb-text-op10);

          .am-fs-sb__page-indicator {
            display: flex;
          }
        }

        $count: 100;
        @for $i from 0 through $count {
          &:nth-child(#{$i + 1}) {
            animation: 400ms cubic-bezier(.45,1,.4,1.2) #{$i*70}ms am-animation-slide-right;
            animation-fill-mode: both;
          }
        }

        // Sidebar Step Wrapper
        &-wrapper {
          padding: 0 8px;
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
          gap: 0 4px;

          &.am-collapsed {
            justify-content: center;
          }
        }

        // Sidebar Step Icon
        &-icon {
          position: relative;
          width: 24px;
          height: 20px;
          font-size: 24px;
          flex: 0 0 auto;

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
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
          margin: 0 auto 0 0;

          &.am-rtl {
            margin: 0 0 0 auto;
          }

          &.fade-enter-active {
            animation: sidebar-step-selection 1s;
          }

          &.fade-leave-active {
            animation: sidebar-step-selection 1s reverse;
          }
        }

        &-indicator {
          display: none;
          align-items: center;
          justify-content: center;
          font-size: 24px;
          margin-left: auto;

          &.am-rtl {
            margin-right: auto;
            margin-left: 0;
          }

          &.fade-enter-active {
            animation: sidebar-step-selection 1s;
          }

          &.fade-leave-active {
            animation: sidebar-step-selection 1s reverse;
          }
        }

        &-divider {
          border-top: 1px solid var(--am-c-sb-text-op60);
          margin: 16px 0;
        }
      }

      &__footer {
        position: absolute;
        bottom: 16px;
        left: 16px;
        width: calc(100% - 32px);

        .am-fs-sb__page {
          &-heading {
            margin-right: auto;
          }
        }
      }

      &__support {
        border-top: 1px solid var(--am-c-sb-text-op10);
        padding: 12px 0;
        display: flex;
        flex-direction: column;

        &-heading {
          font-family: var(--am-font-family);
          font-size: 14px;
          font-weight: 500;
          font-style: normal;
          line-height: 1.43;
          text-align: center;
          color: var(--am-c-sb-text-op60);

          &.fade-enter-active {
            animation: sidebar-step-selection 1s;
          }

          &.fade-leave-active {
            animation: sidebar-step-selection 1s reverse;
          }
        }

        &-email {
          font-family: var(--am-font-family);
          font-size: 14px;
          font-weight: 400;
          font-style: normal;
          line-height: 1.43;
          text-align: center;
          color: var(--am-c-sb-text);
          text-decoration: none;
          margin: 4px 0 0;

          [class^="am-icon-"] {
            font-size: 24px;
            color: var(--am-c-sb-text);
            justify-self: flex-end;
          }
        }
      }

      &__menu {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        border-top: 1px solid var(--am-c-sb-text-op10);
        padding: 12px 0 0;
        cursor: pointer;

        &-text {
          font-size: 15px;
          font-weight: 500;
          line-height: 1.4;
          color: var(--am-c-sb-text);

          &.fade-enter-active {
            animation: sidebar-step-selection 1s;
          }

          &.fade-leave-active {
            animation: sidebar-step-selection 1s reverse;
          }
        }

        & [class^="am-icon-"], & [class*=" am-icon-"] {
          font-size: 24px;
          color: var(--am-c-sb-text);
          justify-self: flex-end;
        }

        &.am-collapsed {
          & [class^="am-icon-"], & [class*=" am-icon-"] {
            margin: auto;
          }
        }
      }
    }
  }
}

@keyframes sidebar-step-check-state {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.2);
  }
  100% {
    transform: scale(1);
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
  @include step-sidebar-container;
}

// Admin
#amelia-app-backend-new {
  @include step-sidebar-container;
}
</style>
