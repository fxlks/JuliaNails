<template>
  <div
    class="am-alert-wrapper"
    :class="props.customClass"
    :style="cssVars"
  >
    <el-alert
      ref="amAlert"
      :title="props.title"
      :type="props.type"
      :description="props.description"
      :closable="props.closable"
      :center="props.center"
      :close-text="props.closeText"
      :show-icon="props.showIcon"
      :effect="props.effect"
      class="am-alert"
      :class="{'am-border': showBorder}"
      :style="cssVars"
      @close="() => $emit('close')"
    >
      <template v-if="slots.title" #title>
        <slot name="title"></slot>
      </template>
      <template v-if="slots.default" #default>
        <slot name="default"></slot>
      </template>
    </el-alert>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  computed,
  inject,
  ref,
  useSlots
} from 'vue';
import { useColorTransparency } from "../../../assets/js/common/colorManipulation";

const slots = useSlots()

/**
 * Component Props
 */
const props = defineProps({
  title: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'info',
    validator(value) {
      return ['success', 'warning', 'info', 'error'].includes(value)
    }
  },
  description: {
    type: String,
    default: ''
  },
  closable: {
    type: Boolean,
    default: true
  },
  center: {
    type: Boolean,
    default: false
  },
  closeText: {
    type: String,
    default: ''
  },
  showIcon: {
    type: Boolean,
    default: false
  },
  effect: {
    type: String,
    default: 'light'
  },
  showBorder: {
    type: Boolean,
    default: false
  },
  customClass: {
    type: String,
    default: ''
  },
  closeAfter: {
    type: Number,
    default: 0
  }
})

/**
 * Component Emits
 * */
const emits = defineEmits(['close', 'trigger-close'])

/**
 * Component reference
 */
const amAlert = ref()

if (props.closeAfter) {
  setTimeout(() => {
    emits('trigger-close')
  }, props.closeAfter)
}

// * Color Vars
let amColors = inject('amColors', {
  amColors: {
    value: {
      colorInpText: '#1A2C37',
    }
  }
})

// * Css Variables
let cssVars = computed(() => {
  // alerts - alert success
  // alerti - alert info
  // alertw - alert warning
  // alerte - alert error
  return {
    '--am-c-alert-text': amColors.value.colorMainText,
    // success
    '--am-c-alerts-bgr': amColors.value.colorSuccess,
    '--am-c-alerts-bgr-op10': useColorTransparency(amColors.value.colorSuccess, 0.1),
    '--am-c-alerts-bgr-op60': useColorTransparency(amColors.value.colorSuccess, 0.6),
    // info
    '--am-c-alerti-bgr': amColors.value.colorPrimary,
    '--am-c-alerti-bgr-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),
    '--am-c-alerti-bgr-op60': useColorTransparency(amColors.value.colorPrimary, 0.6),
    // warning
    '--am-c-alertw-bgr': amColors.value.colorWarning,
    '--am-c-alertw-bgr-op10': useColorTransparency(amColors.value.colorWarning, 0.1),
    '--am-c-alertw-bgr-op60': useColorTransparency(amColors.value.colorWarning, 0.6),
    // error
    '--am-c-alerte-bgr': amColors.value.colorError,
    '--am-c-alerte-bgr-op10': useColorTransparency(amColors.value.colorError, 0.1),
    '--am-c-alerte-bgr-op60': useColorTransparency(amColors.value.colorError, 0.6)
  }
})

</script>

<style lang="scss">
@mixin am-alert-block {
  .am-alert-wrapper {
    // -c-    color
    // -rad-  border radius
    // -h-    height
    // -fs-   font size
    // -padd- padding
    // -bgr-   background

    .el-alert {
      padding: 8px 12px;
      border-radius: 6px;
      box-sizing: border-box;

      &.am-border {
        background-color: transparent;
        border-width: 1px 1px 4px 1px;
      }

      &--success {
        background-color: var(--am-c-alerts-bgr-op10);
        border: 1px solid var(--am-c-alerts-bgr-op60);
        box-shadow: 0 2px 3px var(--am-c-alerts-bgr-op10);

        &.am-border {
          border-color: var(--am-c-alerts-bgr);
        }
      }

      &--info {
        background-color: var(--am-c-alerti-bgr-op10);
        border: 1px solid var(--am-c-alerti-bgr-op60);
        box-shadow: 0 2px 3px var(--am-c-alerti-bgr-op10);

        &.am-border {
          border-color: var(--am-c-alerti-bgr);
        }
      }

      &--error {
        background-color: var(--am-c-alerte-bgr-op10);
        border: 1px solid var(--am-c-alerte-bgr-op60);
        box-shadow: 0 2px 3px var(--am-c-alerte-bgr-op10);

        &.am-border {
          border-color: var(--am-c-alerte-bgr);
        }
      }

      &--warning {
        background-color: var(--am-c-alertw-bgr-op10);
        border: 1px solid var(--am-c-alertw-bgr-op60);
        box-shadow: 0 2px 3px var(--am-c-alertw-bgr-op10);

        &.am-border {
          border-color: var(--am-c-alertw-bgr);
        }
      }

      &__title {
        font-weight: 500;
        font-size: 14px;
        line-height: 24px;
        /* $shade-900 */
        color: var(--am-c-alert-text);
      }

      &__description {
        font-weight: 400;
        font-size: 13px;
        line-height: 20px;
        /* $shade-900 */
        color: var(--am-c-alert-text);
      }

      &__icon{
        margin-right: 11px;
      }

      &__closebtn {
        color: var(--am-c-alert-text);
      }
    }
  }
}

// public
.amelia-v2-booking #amelia-container {
  @include am-alert-block;
}

// admin
#amelia-app-backend-new {
  @include am-alert-block;
}
</style>