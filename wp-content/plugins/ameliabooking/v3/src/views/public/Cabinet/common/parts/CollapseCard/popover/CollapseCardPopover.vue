<template>
  <!-- Periods -->
  <el-popover
    :show-arrow="false"
    :persistent="false"
    :width="'auto'"
    :popper-class="'am-cc__popover'"
    :popper-style="cssVars"
    trigger="click"
  >
    <template #reference>
      <slot></slot>
    </template>

    <div class="am-cc__popover-inner">
      <div
        v-if="props.headerText"
        class="am-cc__popover-heading"
      >
        {{ props.headerText }}
      </div>
      <component
        :is="templates[props.type]"
        class="am-cc__popover-content"
        :data="props.contentData"
        :customized-options="amCustomize"
      ></component>
    </div>
  </el-popover>
  <!-- /Periods -->
</template>

<script setup>
// * templates
import Periods from "./templates/Periods.vue";
import Extras from "./templates/Extras.vue";
import Tickets from "./templates/Tickets.vue";
import CustomFields from "./templates/CustomFields.vue";
import Employee from "./templates/Employee.vue";
import Customers from "./templates/Customers.vue";
import QrCode from "./templates/QrCode.vue"

// * Import from Vue
import {
  ref,
  computed,
  inject,
  markRaw
} from "vue"

// * Composables
import { useColorTransparency } from "../../../../../../../assets/js/common/colorManipulation.js"

let props = defineProps({
  headerText: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    required: true
  },
  contentData: {
    type: [Array, Object, String],
    default: ''
  }
})

let amCustomize = inject('amCustomize')

let templates = ref({
  period: markRaw(Periods),
  extras: markRaw(Extras),
  ticket: markRaw(Tickets),
  customField: markRaw(CustomFields),
  employee: markRaw(Employee),
  customers: markRaw(Customers),
  qrCode: markRaw(QrCode)
})

// * Fonts
let amFonts = inject('amFonts')

// * Colors block
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-cc-primary': amColors.value.colorPrimary,
    '--am-c-cc-primary-op70': useColorTransparency(amColors.value.colorPrimary, 0.7),
    '--am-c-cc-success': amColors.value.colorSuccess,
    '--am-c-cc-success-op15': useColorTransparency(amColors.value.colorSuccess, 0.15),
    '--am-c-cc-error': amColors.value.colorError,
    '--am-c-cc-error-op15': useColorTransparency(amColors.value.colorError, 0.15),
    '--am-c-cc-bgr': amColors.value.colorMainBgr,
    '--am-c-cc-text': amColors.value.colorMainText,
    '--am-c-cc-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
    '--am-c-cc-text-op15': useColorTransparency(amColors.value.colorMainText, 0.15),
    '--am-c-cc-text-op70': useColorTransparency(amColors.value.colorMainText, 0.7),
    '--am-c-cc-text-op80': useColorTransparency(amColors.value.colorMainText, 0.8),
    '--am-c-cc-text-op90': useColorTransparency(amColors.value.colorMainText, 0.9),
    '--am-c-scroll-op30': useColorTransparency(amColors.value.colorPrimary, 0.30),
    '--am-c-scroll-op10': useColorTransparency(amColors.value.colorPrimary, 0.10),
    '--am-font-family': amFonts.value.fontFamily,

    // css properties
    '--am-rad-inp': '6px',
    '--am-fs-inp': '15px',
  }
})
</script>

<script>
export default {
  name: "CollapseCardPopoverTemplate"
}
</script>

<style lang="scss">
@mixin card-popover {
  &.am-cc__popover {
    padding: 16px;
    background-color: var(--am-c-cc-bgr);
    border-color: var(--am-c-cc-bgr);
    box-shadow: 0 2px 12px 0 var(--am-c-cc-text-op10);

    * {
      font-family: var(--am-font-family);
      box-sizing: border-box;
    }
  }

  .am-cc__popover {
    &-heading {
      font-size: 15px;
      font-weight: 600;
      line-height: 1.6;
      color: var(--am-c-cc-text);
      margin: 0 0 16px;
    }
  }
}

.el-popover {
  @include card-popover;
}
</style>