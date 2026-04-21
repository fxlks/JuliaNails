<template>
  <div
    ref="advSelectWrapperRef"
    class="am-adv-select__wrapper"
  >
    <el-cascader
      ref="advSelect"
      v-model="model"
      class="am-adv-select"
      :class="[
        `am-adv-select--${size}`,
        {'am-adv-select--disabled': disabled},
        {'am-rtl': isRtl}
      ]"
      :options="props.options"
      :props="props.propsData"
      :placeholder="props.placeholder"
      :disabled="props.disabled"
      :clearable="props.clearable"
      :show-all-levels="props.showAllLevels"
      :collapse-tags="props.collapseTags"
      :collapse-tags-tooltip="props.collapseTagsTooltip"
      :separator="props.separator"
      :filterable="props.filterable"
      :filter-method="props.filterMethod"
      :debounce="props.debounce"
      :before-filter="props.beforeFilter"
      :popper-class="popperClasses"
      :teleported="props.teleported"
      :tag-type="props.tagType"
      :tag-effect="props.tagEffect"
      :validate-event="props.validateEvent"
      :max-collapse-tags="props.maxCollapseTags"
      :persistent="props.persistent"
      :fallback-placements="props.fallbackPlacements"
      :placement="props.placement"
      :style="cssVars"
      :aria-label="props.ariaLabel"
      @change="handleChange"
      @expand-change="handleExpandChange"
      @blur="(e) => emits('blur', e)"
      @focus="(e) => emits('focus', e)"
      @visible-change="handleVisibleChange"
      @remove-tag="(val) => emits('remove-tag', val)"
    >
      <template v-if="props.prefixIcon" #prefix>
        <component :is="props.prefixIcon" v-if="typeof props.prefixIcon === 'object'" />
        <span
          v-if="typeof props.prefixIcon === 'string'"
          :class="`am-icon-${props.prefixIcon}`"
        />
      </template>
      <template #empty>
        <span>
          {{ emptyStateString }}
        </span>
      </template>
      <template #default="{node, data}">
        <div
          v-if="!node.isLeaf"
          class="am-adv-select__item"
          :style="cssDropdownVars"
        >
          <span class="am-adv-select__item-label">
            {{ data[props.propsData.label] }}
          </span>
          <span class="am-adv-select__item-quantity">
            {{ `(${data[props.propsData.children].length})` }}
          </span>
        </div>
        <div
          v-if="node.isLeaf"
          class="am-adv-select__item"
          :class="{'am-adv-select__item-checked': node.checked}"
          :style="cssDropdownVars"
        >
          <span class="am-adv-select__item-label">
            {{ data[props.propsData.label] }}
          </span>
          <span v-if="data.price" class="am-adv-select__item-price">
            {{` ${useFormattedPrice(data.price)}` }}
            <span
              v-if="taxVisibility(data.id)"
              class="am-adv-select__item-tax"
            >
              <template v-if="props.taxOptions.find(a => a.id === data.id).excluded">
                {{ props.taxLabel }}
              </template>
              <template v-else>
                {{props.taxLabelIncl}}
              </template>
            </span>
          </span>
        </div>
      </template>
    </el-cascader>

    <teleport
      v-if="teleportCategoryDisable && categoryName"
      :to="`${popperId} .am-adv-select__popper-${uniqueClassSuffix} .el-cascader-panel .el-cascader-menu:nth-child(1)`"
      :disabled="!teleportCategoryDisable  && categoryName"
    >
      <span v-if="options.length" class="am-adv-select__popper-heading" :style="cssTeleport">
        {{ categoryName }}
      </span>
    </teleport>

    <teleport
      v-if="teleportSubCategoryDisable && subCategoryName"
      :to="`${popperId} .am-adv-select__popper-${uniqueClassSuffix} .el-cascader-panel .el-cascader-menu:nth-child(2)`"
      :disabled="!teleportSubCategoryDisable && subCategoryName"
    >
      <span v-if="options.length" class="am-adv-select__popper-heading" :style="cssTeleport">
        {{ subCategoryName }}
      </span>
    </teleport>
  </div>
</template>

<script setup>
import {
  computed,
  inject,
  nextTick,
  ref,
  toRefs
} from 'vue'
import { useColorTransparency } from "../../../assets/js/common/colorManipulation";
import { useFormattedPrice } from '../../../assets/js/common/formatting'
import { useElementSize } from '@vueuse/core'

/**
 * Component Props
 */
const props = defineProps({
  id: {
    type: String
  },
  modelValue: {
    type: [String, Array, Object, Number],
  },
  options: {
    // * data of the options, the key of value and label can be customize by CascaderProps
    type: Array,
    default: () => []
  },
  propsData: {
    // * configuration options, see the following CascaderProps table.
    type: Object,
  },
  size: {
    // default / medium / small / mini / micro
    type: String,
    default: 'default',
    validator(value) {
      return ['default', 'medium', 'small', 'mini', 'micro'].includes(value)
    }
  },
  placeholder: {
    type: String,
    default: ''
  },
  disabled: {
    // * whether Cascader is disabled
    type: Boolean,
    default: false
  },
  clearable: {
    // * whether selected value can be cleared
    type: Boolean,
    default: true,
  },
  showAllLevels: {
    // * whether to display all levels of the selected value in the input
    type: Boolean,
    default: true
  },
  collapseTags: {
    // * whether to collapse tags in multiple selection mode
    type: Boolean,
    default: true
  },
  collapseTagsTooltip: {
    // * whether show all selected tags when mouse hover text of collapse-tags. To use this, collapse-tags must be true
    type: Boolean,
    default: false
  },
  separator: {
    // * option label separator
    type: String,
    default: ' / '
  },
  filterable: {
    // * whether to filter options
    type: Boolean,
    default: true
  },
  filterMethod: {
    // * customize search logic, the first parameter is node, the second is keyword, and need return a boolean value indicating whether it hits.
    type: Function,
    default: (node, keyword) => {
      let search = keyword.toLowerCase()
      let label = node.text.toLowerCase()

      return label.includes(search)
    }
  },
  debounce: {
    // * debounce delay when typing filter keyword, in milliseconds
    type: Number,
    default: 300
  },
  beforeFilter: {
    // * hook function before filtering with the value to be filtered as its parameter. If false is returned or a Promise is returned and then is rejected, filtering will be aborted
    type: Function
  },
  popperClass: {
    // * custom class name for Cascader's dropdown
    type: String,
    default: ''
  },
  teleported: {
    // * whether cascader popup is teleported
    type: Boolean,
    default: true
  },
  tagType: {
    // * tag type
    type: String,
    default: 'info',
    validator(value) {
      return ['info', 'success', 'warning', 'danger'].includes(value)
    }
  },
  tagEffect: {
    // * tag effect
    type: String,
    default: 'light',
    validator(value) {
      return ['dark', 'light', 'plain'].includes(value)
    }
  },
  validateEvent: {
    // * event name that triggers validation
    type: Boolean,
    default: true
  },
  maxCollapseTags: {
    // * The max tags number to be shown. To use this, collpase-tags must be true
    type: Number,
    default: 1
  },
  persistent: {
    // * when dropdown is inactive and persistent is false, dropdown will be destroyed
    type: Boolean,
    default: true
  },
  fallbackPlacements: {
    // * ist of possible positions for Tooltip popper.js
    type: Array,
    default: () => ['bottom-start', 'top-start', 'right', 'left']
  },
  placement: {
    // * placement of the dropdown, default is bottom-start
    type: String,
    default: 'bottom-start',
    validator(value) {
      return [
        'top',
        'top-start',
        'top-end',
        'bottom',
        'bottom-start',
        'bottom-end',
        'left',
        'left-start',
        'left-end',
        'right',
        'right-start',
        'right-end'
      ].includes(value)
    },
  },
  prefixIcon: {
    type: [String, Object],
    default: ''
  },
  currencySymbol: {
    type: String,
    default: '$'
  },
  categoryName: {
    type: String,
    default: ''
  },
  subCategoryName: {
    type: String,
    default: ''
  },
  emptyStateString: {
    type: String,
    default: 'No matching data'
  },
  taxOptions: {
    type: [Object, Array],
    default: () => {
      return []
    }
  },
  taxLabel: {
    type: String,
    default: ''
  },
  taxLabelIncl: {
    type: String,
    default: ''
  },
  taxVisible: {
    type: Boolean,
    default: true
  },
  ariaLabel: {
    type: String,
    default: 'dropdown'
  },
})

/**
 * Component Emits
 * @type {EmitFn<(string)[]>}
 */
const emits = defineEmits(['change', 'expand-change', 'blur', 'focus', 'visible-change', 'remove-tag', 'update:modelValue'])

// * Component model
let { modelValue } = toRefs(props)
let model = computed({
  get: () => modelValue.value,
  set: (val) => {
    emits('update:modelValue', val)
  }
})

// * Tat Visibility
function taxVisibility (id) {
  return props.taxVisible && !!props.taxOptions.filter(a => a.id === id).length
}

// * Container Width
let cWidth = inject('containerWidth', 0)
let checkScreen = computed(() => cWidth.value < 560 || (cWidth.value > 560 && cWidth.value < 640))

// * Element Wrapper Size
const advSelectWrapperRef = ref(null)

const {width: wrapperWidth} = useElementSize(advSelectWrapperRef)

// * Component reference
const advSelect = ref(null)

// * Component text orientation
let isRtl = computed(() => {
  if (document) {
    return document.documentElement.dir === 'rtl'
  }

  return false
})

/**
 * Dropdown (Popper) ID
 */
const popperId = ref('')

/**
 * Dropdown (Popper) identifier class
 */
let uniqueClassSuffix = ref(Math.floor(Math.random() * 1000) + 1)

/**
 * Dropdown (Popper) Classes
 * @type {ComputedRef<string>}
 */
let popperClasses = computed(() => {
  return `am-adv-select__popper-${uniqueClassSuffix.value} am-adv-select__popper${props.popperClass ? ' ' + props.popperClass : ''}` + (checkScreen.value ? ' am-adv-select__popper-mobile':'')
})

/**
 * Teleport Variables - category and subcategory name in drop down
 */
let teleportSubCategoryDisable = ref(false)
let teleportCategoryDisable = ref(false)

// * Fonts
let amFonts = inject('amFonts', ref({
  fontFamily: 'Amelia Roboto, sans-serif',
  fontUrl: '',
  customFontFamily: '',
  fontFormat: '',
  customFontSelected: false
}))

// * Colors
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

// * Adv css vars
let cssVars = computed(() => {
  return {
    '--am-c-advs-bgr': amColors.value.colorInpBgr,
    '--am-c-advs-border': amColors.value.colorInpBorder,
    '--am-c-advs-text': amColors.value.colorInpText,
    '--am-c-advs-placeholder': amColors.value.colorInpPlaceHolder,
    '--am-c-advs-shadow': useColorTransparency(amColors.value.colorInpText, 0.05),
    '--am-c-advs-text-op60': useColorTransparency(amColors.value.colorInpText, 0.6),
    '--am-c-advs-text-op40': useColorTransparency(amColors.value.colorInpText, 0.4),
    '--am-c-advs-text-op10': useColorTransparency(amColors.value.colorInpText, 0.03),
    '--am-font-family': amFonts.value.fontFamily
  }
})

// * Adv dropdown css vars
let cssDropdownVars = computed(() => {
  return {
    '--am-c-advs-item-price': amColors.value.colorPrimary,
    '--am-c-advs-item-price-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),
    '--am-c-advs-item-selected': amColors.value.colorPrimary,
    '--am-c-advs-item-label-op40': useColorTransparency(amColors.value.colorDropText, 0.4),
    '--am-font-family': amFonts.value.fontFamily
  }
})

/**
 * Change Function
 * @param val
 */
function handleChange(val) {
  emits('change', val)
  emits('update:modelValue', val)
}

/**
 * Dropdown expand detection and width of dropdown control
 * @param val
 */
function handleExpandChange(val) {
  nextTick(() => {
    if (val && val.length > 0) {
      teleportSubCategoryDisable.value = true
    }
  })
  emits('expand-change', val)
}

/**
 * Dropdown visibility control
 * @param e
 */
function handleVisibleChange(e) {
  nextTick(() => {
    if (!e && !model.value) {
      teleportSubCategoryDisable.value = false
    }

    teleportCategoryDisable.value = true
    if (advSelect.value) {
      advSelect.value.contentRef.style.setProperty('--am-c-advs-item-bgr', amColors.value.colorDropBgr)
      advSelect.value.contentRef.style.setProperty('--am-c-advs-item-border', amColors.value.colorDropBorder)
      advSelect.value.contentRef.style.setProperty('--am-c-advs-item-bgr-op10', useColorTransparency(amColors.value.colorDropText, 0.1))
      advSelect.value.contentRef.style.setProperty('--am-c-advs-item-label', amColors.value.colorDropText)
      advSelect.value.contentRef.style.setProperty('--am-c-advs-item-label-op65', useColorTransparency(amColors.value.colorDropText, 0.65))
      advSelect.value.contentRef.style.setProperty('--am-c-advs-item-border-op10', useColorTransparency(amColors.value.colorDropText, 0.1))
      advSelect.value.contentRef.style.setProperty('--am-font-family', amFonts.value.fontFamily)
      advSelect.value.contentRef.style.setProperty('width', `${wrapperWidth.value}px`)
    }
  })
  emits('visible-change', e)
}

let cssTeleport = computed(() => {
  return {
    '--am-h-advs-item-heading': checkScreen.value ? '32px' : '26px',
    '--am-fs-advs-item-heading': checkScreen.value ? '16px' : '12px',
    '--am-c-advs-item-heading-op65': useColorTransparency(amColors.value.colorDropText, 0.65),
    '--am-c-advs-shadow': useColorTransparency(amColors.value.colorInpText, 0.05),
  }
})
</script>

<style lang="scss">
@mixin am-adv-select-block {
  // Adv Select
  .am-adv-select {
    // -c-    color
    // -rad-  border radius
    // -h-    height
    // -fs-   font size
    // -padd- padding
    // -bgr   background
    // -advs- advanced select
    --am-c-advs-bgr: var(--am-c-inp-bgr);
    --am-c-advs-border: var(--am-c-inp-border);
    --am-c-advs-text: var(--am-c-inp-text);
    --am-c-advs-placeholder: var(--am-color-input-placeholder);
    --am-rad-advs: var(--am-rad-inp);
    --am-fs-advs: var(--am-fs-inp);
    --am-h-advs: var(--am-h-inp);
    --am-padd-advs: 0 12px;
    width: 100%;

    &__wrapper {
      width: 100%;
    }

    // Size - default / medium / small / mini / micro
    &--default {
      --am-h-advs: 40px;
    }
    &--medium {
      --am-h-advs: 36px;
    }
    &--small {
      --am-h-advs: 32px;
    }
    &--mini {
      --am-h-advs: 28px;
    }
    &--micro {
      --am-h-advs: 24px;
    }

    // Disabled
    &--disabled {
      --am-c-advs-bgr: var(--am-c-advs-text-op10);
      --am-c-advs-text: var(--am-c-advs-text-op60);
    }

    // Element visual presentation
    .el-input {
      border: none;
      border-radius: 4px;
      box-shadow: 0 2px 2px var(--am-c-advs-shadow);

      &.is-focus {
        // prevent hover color to be applied
        .el-input__wrapper {
          --am-c-advs-border: var(--am-c-primary);
        }
      }

      &.el-input--prefix {
        --am-padd-advs: 0 8px;
      }

      // Visual presentation of input
      &__wrapper {
        display: inline-flex;
        align-items: center;
        gap: 0 6px;
        background-image: none;
        background-color: var(--am-c-advs-bgr);
        border: none;
        border-radius: var(--am-rad-advs);
        box-shadow: 0 0 0 1px var(--am-c-advs-border);
        padding: var(--am-padd-advs);
        height: var(--am-h-advs);
        transition: 0.3s;

        &:hover {
          --am-c-advs-border: var(--am-c-advs-text-op40);
        }

        &:focus, &:active &.is-focus {
          --am-c-advs-border: var(--am-c-primary);
        }
      }

      &__inner {
        height: 24px;
        font-size: var(--am-fs-advs);
        font-weight: 400;
        line-height: 1.6;
        color: var(--am-c-advs-text);
        background-color: transparent;
        border: none;
        border-radius: var(--am-rad-advs);
        padding: 0;
        width: 100%;

        &::-webkit-input-placeholder { /* Chrome/Opera/Safari */
          color: var(--am-c-advs-placeholder);
        }
        &::-moz-placeholder { /* Firefox 19+ */
          color: var(--am-c-advs-placeholder);
        }
        &:-ms-input-placeholder { /* IE 10+ */
          color: var(--am-c-advs-placeholder);
        }
        &:-moz-placeholder { /* Firefox 18- */
          color: var(--am-c-advs-placeholder);
        }

        &:focus {
          border: none;
          box-shadow: none;
        }
      }

      &__suffix {
        .el-input__validateIcon {
          border: none;
        }

        .el-icon {
          font-family: 'amelia-icons';
          font-size: 18px;
          color: var(--am-c-advs-text);

          &.icon-arrow-down {
            &:before {
              font-family: 'amelia-icons' !important;
              font-size: 18px;
              content: $am-icon-arrow-down;
            }

            svg {
              display: none;
            }
          }

          &.icon-circle-close {
            &:before {
              font-family: 'amelia-icons' !important;
              font-size: 18px;
              content: $am-icon-close;
            }

            svg {
              display: none;
            }
          }
        }
      }

      &__prefix {
        [class^='am-icon'] {
          font-size: 24px;
          line-height: 1;
          color: var(--am-c-advs-text);
        }
      }
    }

    &.am-rtl {
      .el-input {
        &__suffix {
          right: unset;
          left: 12px;
        }
      }
    }
  }
}

// public
.amelia-v2-booking #amelia-container {
  @include am-adv-select-block;
}

// admin
#amelia-app-backend-new {
  @include am-adv-select-block;
}

// Adv Select Dropdown - Popper
.am-adv-select__popper {
  --am-c-advs-item-bgr: var(--am-c-drop-bgr);
  --am-c-advs-item-label: var(--am-c-main-text);
  --am-c-advs-item-price: var(--am-c-primary);
  --am-c-advs-item-selected: var(--am-c-primary);
  --am-h-advs-item-heading: 26px;
  --am-fs-advs-item-heading: 12px;

  max-width: 454px;
  width: 100%;
  z-index: 9999999999 !important;

  &.el-cascader__dropdown.el-popper[role=tooltip] {
    background-color: transparent;
    border-color: var(--am-c-advs-item-border);
    overflow: hidden;
  }

  * {
    font-family: var(--am-font-family);
    border-radius: unset;
    box-sizing: border-box;
  }

  // Adv Select Dropdown Category Name
  &-heading {
    position: absolute;
    top: 0;
    left: 0;
    display: flex;
    align-items: center;
    width: 100%;
    height: var(--am-h-advs-item-heading);
    font-size: var(--am-fs-advs-item-heading);
    font-weight: 700;
    font-style: normal;
    line-height: 1.5;
    color: var(--am-c-advs-item-heading-op65);
    background-color: var(--am-c-advs-item-bgr);
    padding-left: 8px;
  }

  .el-cascader {
    // Panel - dropdown
    &-panel {
      display: flex;
      background-color: var(--am-c-advs-item-bgr);
      max-width: 454px;
      width: 100%;
    }

    // Panel sides (columns) - Menu
    &-menu {
      width: 100%;
      min-width: 170px;

      // First column
      &:nth-child(1) {
        padding-top: 26px;
        flex-shrink: 2;
        border-color: var(--am-c-advs-item-border-op10);
      }

      // Second column
      &:nth-child(2) {
        padding-top: 26px;
        flex-shrink: 1;
      }

      &:last-child {
        .el-cascader-node {
          padding: 0;
        }
      }

      .el-scrollbar__bar.is-vertical {
        display: block !important;
        top: var(--am-h-advs-item-heading);
        padding-top: 6px;

        .el-scrollbar__thumb {
          background: var(--am-c-advs-item-label);
          opacity: 0.3;
        }
      }
    }

    &-node {
      height: unset;
      padding: 0;

      &:not(.is-disabled):hover, &:not(.is-disabled):focus {
        background-color: var(--am-c-advs-item-bgr-op10);
      }

      &.in-active-path {
        .am-adv-select__item-label {
          color: var(--am-c-advs-item-selected);
        }
      }

      &__label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 4px 16px 4px 8px;
      }

      i {
        display: none;
      }
    }

    &__suggestion-list {
      background-color: var(--am-c-advs-item-bgr);
      color: var(--am-c-advs-item-label-op65);
    }
  }

  .am-adv-select {
    &__item {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;

      &-checked {
        .am-adv-select__item-label {
          color: var(--am-c-advs-item-selected);
        }
      }

      &-label {
        display: block;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.43;
        color: var(--am-c-advs-item-label);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      &-quantity {
        font-size: 14px;
        font-weight: 400;
        line-height: 1.714;
        color: var(--am-c-advs-item-label-op40);
      }

      &-price {
        font-size: 14px;
        font-weight: 400;
        line-height: 1.714;
        color: var(--am-c-advs-item-price);
      }

      &-tax {
        display: inline-flex;
        font-size: 14px;
        font-weight: 400;
        line-height: 1;
        color: var(--am-c-advs-item-price);
        background-color: var(--am-c-advs-item-price-op10);
        border-radius: 10px;
        padding: 3px 8px;
      }
    }
  }

  .el-popper__arrow {
    display: none;
  }

  // Mobile
  &-mobile {
    width: calc(100% - 20px);

    .el-cascader {
      // Panel - dropdown
      &-panel {
        height: min-content;
        max-width: 100%;
        width: 100%;
        display: flex;
        flex-direction: column;

        .am-adv-select__popper-heading {
          height: var(--am-h-advs-item-heading);
          font-size: var(--am-fs-advs-item-heading);
          box-shadow: 0 2px 2px var(--am-c-advs-shadow);
        }
      }

      &-menu {
        min-height: 160px;

        &:first-child:not(:only-child) {
          border-bottom: 1px solid var(--am-c-inp-border);
        }

        &__wrap {
          height: min-content;
          max-height: 160px;
          overflow-y: scroll;
        }
      }

    }
  }
}
</style>

