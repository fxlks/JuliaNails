<template>
  <!-- Select -->
  <div
    class="am-select-wrapper"
    :class="props.parentClass"
  >
    <el-select
      :id="id"
      ref="amSelect"
      v-model="model"
      class="am-select"
      :class="[
        `am-select--${props.size}`,
        {'am-select--disabled': props.disabled},
        {'am-select--suffix': props.suffixIcon},
        {'am-select--prefix': props.prefixIcon},
        {'am-select--multiple': props.multiple && !props.collapseTags},
        { 'am-rtl': isRtl },
        props.customClass
      ]"
      :popper-class="`am-select-popper${popperClass ? ' ' + popperClass : popperClass}`"
      :popper-options="props.popperOptions"
      :multiple="props.multiple"
      :disabled="props.disabled"
      :value-key="props.valueKey"
      :clearable="props.clearable"
      :collapse-tags="props.collapseTags"
      :multiple-limit="props.multipleLimit"
      :name="props.name"
      :autocomplete="props.autocomplete"
      :placeholder="props.placeholder ? props.placeholder : amLabels.select"
      :filterable="props.filterable"
      :allow-create="props.allowCreate"
      :filter-method="props.filterMethod"
      :remote="props.remote"
      :remote-method="props.remoteMethod"
      :loading="props.loading"
      :loading-text="props.loadingText"
      :no-match-text="props.noMatchText"
      :no-data-text="props.noDataText"
      :collapse-tags-tooltip="props.collapseTagsTooltip"
      :reserve-keyword="props.reserveKeyword"
      :default-first-option="props.defaultFirstOption"
      :teleported="props.teleported"
      :automatic-dropdown="props.automaticDropdown"
      :clear-icon="props.clearIcon"
      :fit-input-width="props.fitInputWidth"
      :suffix-icon="props.suffixIcon"
      :tag-type="props.tagType"
      :prefix-icon="props.prefixIcon"
      :aria-label="props.ariaLabel"
      :offset="props.offset"
      :show-arrow="props.showArrow"
      :placement="props.placement"
      :fallback-placements="props.fallbackPlacements"
      :validate-event="props.validateEvent"
      :append-to="props.appendTo"
      :persistent="props.persistent"
      :tabindex="props.tabindex"
      :empty-values="props.emptyValues"
      :value-on-clear="props.valueOnClear"
      :effect="props.tagEffect"
      :max-collapse-tags="props.maxCollapseTags"
      :remote-show-suffix="props.remoteShowSuffix"
      :tag-effect="props.tagEffect"
      :style="{ ...cssVars }"
      @change="(val) => $emit('change', val)"
      @visible-change="visibleChange"
      @remove-tag="(eventValue) => $emit('remove-tag', eventValue)"
      @clear="$emit('clear')"
      @blur="(e) => $emit('blur', e)"
      @focus="(e) => $emit('focus', e)"
      @click="(e) => $emit('click', e)"
    >
      <template v-if="prefixIcon" #prefix>
        <component :is="prefixIcon" v-if="typeof prefixIcon === 'object'" />
        <span
          v-if="typeof prefixIcon === 'string'"
          :class="`am-icon-${prefixIcon}`"
          :style="`color: ${prefixIconColor}`"
        />
      </template>
      <slot />
    </el-select>
  </div>
  <!-- /Select -->
</template>

<script setup>
// * Import form Vue
import {
  ref,
  toRefs,
  computed,
  inject
} from 'vue'

// * Components
import IconComponent from "../icons/IconComponent.vue";

// * Composable
import { useColorTransparency } from '../../../assets/js/common/colorManipulation';

// * Labels
let amLabels = inject('labels')

/**
 * Component Props
 */
const props = defineProps({
  id: {
    type: String,
  },
  modelValue: {
    type: [String, Array, Object, Number, null],
  },
  multiple: {
    // * whether multiple-select is activated
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  valueKey: {
    // * unique identity key name for value, required when value is an object
    type: String,
    default: 'value',
  },
  size: {
    // default / medium / small / mini / micro
    type: String,
    default: 'default',
    validator(value) {
      return ['default', 'medium', 'small', 'mini', 'micro'].includes(value)
    },
  },
  clearable: {
    // * whether select can be cleared
    type: Boolean,
    default: false,
  },
  collapseTags: {
    // * whether to collapse tags to a text when multiple selecting
    type: Boolean,
    default: false,
  },
  collapseTagsTooltip: {
    // * whether show all selected tags when mouse hover text of collapse-tags. To use this, collapse-tags must be true
    type: Boolean,
    default: false
  },
  multipleLimit: {
    // * maximum number of options user can select when multiple is true. No limit when set to 0
    type: Number,
    default: 0,
  },
  name: {
    // * the name attribute of select input
    type: String,
  },
  autocomplete: {
    // * the autocomplete attribute of select input
    type: String,
    default: 'off',
  },
  placeholder: {
    // * placeholder, default is ''
    type: String,
    default: '',
  },
  filterable: {
    // * whether Select is filterable
    type: Boolean,
    default: false,
  },
  allowCreate: {
    // * whether creating new items is allowed. To use this, filterable must be true
    type: Boolean,
    default: false,
  },
  filterMethod: {
    // * custom filter method
    type: Function,
  },
  remote: {
    // * whether options are loaded from server
    type: Boolean,
    default: false,
  },
  remoteMethod: {
    // * custom remote search method
    type: Function,
  },
  remoteShowSuffix: {
    // * in remote search method show suffix icon
    type: Boolean,
    default: false,
  },
  loading: {
    // * whether Select is loading data from server
    type: Boolean,
    default: false,
  },
  loadingText: {
    // * displayed text while loading data from server, default is 'Loading'
    type: String,
    default: 'Loading...',
  },
  noMatchText: {
    // * displayed text when no data matches the filtering query, you can also use slot empty, default is 'No matching data'
    type: String,
    default: 'No matching data',
  },
  noDataText: {
    // * displayed text when there is no options, you can also use slot empty, default is 'No data'
    type: String,
    default: 'No data',
  },
  popperClass: {
    // * custom class name for Select's dropdown
    type: String,
    default: '',
  },
  reserveKeyword: {
    // * when multiple and filterable is true, whether to reserve current keyword after selecting an option
    type: Boolean,
    default: true,
  },
  defaultFirstOption: {
    // * select first matching option on enter key. Use with filterable or remote
    type: Boolean,
    default: false,
  },
  teleported: {
    // * whether select dropdown is teleported, if true it will be teleported to where append-to sets
    type: Boolean,
    default: true,
  },
  appendTo: {
    // * which element the select dropdown appends to, default is body
    type: String,
    default: 'body',
  },
  persistent: {
    // * when select dropdown is inactive and persistent is false, select dropdown will be destroyed
    type: Boolean,
    default: true,
  },
  automaticDropdown: {
    // * for non-filterable Select, this prop decides if the option menu pops up when the input is focused
    type: Boolean,
    default: false,
  },
  clearIcon: {
    type: [String, Object],
    default: () => {
      return {
        components: {IconComponent},
        template: `<IconComponent icon="close"></IconComponent>`
      }
    }
  },
  fitInputWidth: {
    // * whether the width of the dropdown is the same as the input
    type: Boolean,
    default: false,
  },
  suffixIcon: {
    type: [String, Object],
    default: () => {
      return {
        components: {IconComponent},
        template: `<IconComponent icon="arrow-down"></IconComponent>`
      }
    }
  },
  tagType: {
    // * tag type when multiple is true, default is 'info'
    type: String,
    default: 'info',
    validator(value) {
      return ['info', 'success', 'warning', 'danger', ''].includes(value)
    },
  },
  tagEffect: {
    type: String,
    default: 'light',
    validator(value) {
      return ['light', 'dark', 'plain'].includes(value)
    },
  },
  validateEvent: {
    // * whether to trigger form validation
    type: Boolean,
    default: true,
  },
  offset: {
    // * offset of the dropdown
    type: Number,
    default: 12,
  },
  showArrow: {
    // * whether the dropdown has an arrow
    type: Boolean,
    default: false,
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
  fallbackPlacements: {
    // * list of possible positions for dropdown popper.js
    type: Array,
    default: () => {
      return [
        'bottom-start',
        'top-start',
        'right',
        'left'
      ]
    },
  },
  maxCollapseTags: {
    // * the max tags number to be shown. To use this, collapse-tags must be true
    type: Number,
    default: 1,
  },
  popperOptions: {
    // * popper.js parameters
    type: Object,
    default: () => {
      return {}
      // * eg.
      // return {
      //   gpuAcceleration: false,
      //   boundariesElement: 'body',
      //   modifiers: {
      //     computeStyle: {
      //       gpuAcceleration: false,
      //     },
      //     flip: {
      //       behavior: ['top', 'bottom', 'left', 'right'],
      //     },
      //     preventOverflow: {
      //       boundariesElement: 'viewport',
      //     },
      //   },
      // }
    },
  },
  ariaLabel: {
    // * aria-label for select input
    type: String,
    default: 'dropdown',
  },
  emptyValues: {
    // * empty values for select input
    type: Array,
  },
  valueOnClear: {
    // * value when select input is cleared
    type: [String, Number, Object, Function],
  },
  tabindex: {
    // * tabindex for select input
    type: [String, Number],
  },
  customClass: {
    type: String,
    default: '',
  },
  parentClass: {
    type: String,
    default: '',
  },
  prefixIcon: {
    type: [String, Object, Function],
  },
  prefixIconColor: {
    type: [String, Object, Function],
    default: '',
  },
  dropdownArrowVisibility: {
    type: Boolean,
    default: false,
  },
})

/**
 * Component Emits
 * */
const emits = defineEmits([
  'change',
  'visible-change',
  'remove-tag',
  'clear',
  'blur',
  'focus',
  'update:modelValue',
  'click'
])

/**
 * Component model
 */
let { modelValue } = toRefs(props)
let model = computed({
  get: () => modelValue.value,
  set: (val) => {
    emits('update:modelValue', val)
  },
})

/**
 * Component reference
 */
const amSelect = ref(null)

// * Component text orientation
let isRtl = computed(() => {
  if (document) {
    return document.documentElement.dir === 'rtl'
  }

  return false
})

// * Font Vars
let amFonts = inject(
  'amFonts',
  ref({
    fontFamily: 'Amelia Roboto, sans-serif',
    fontUrl: '',
    customFontFamily: '',
    fontFormat: '',
    customFontSelected: false,
  })
)

// * Color Vars
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
    colorInpPlaceHolder: '#808A90',
    colorDropBgr: '#FFFFFF',
    colorDropBorder: '#D1D5D7',
    colorDropText: '#0E1920',
    colorBtnPrim: '#265CF2',
    colorBtnPrimText: '#FFFFFF',
    colorBtnSec: '#1A2C37',
    colorBtnSecText: '#FFFFFF',
  })
)

// * Css Variables
let cssVars = computed(() => {
  return {
    '--am-c-select-bgr': amColors.value.colorInpBgr,
    '--am-c-select-border': amColors.value.colorInpBorder,
    '--am-c-select-text': amColors.value.colorInpText,
    '--am-c-select-placeholder': amColors.value.colorInpPlaceHolder,
    '--am-c-select-shadow': useColorTransparency(
      amColors.value.colorInpText,
      0.05
    ),
    '--am-c-select-text-op60': useColorTransparency(
      amColors.value.colorInpText,
      0.6
    ),
    '--am-c-select-text-op50': useColorTransparency(
      amColors.value.colorInpText,
      0.5
    ),
    '--am-c-select-text-op40': useColorTransparency(
      amColors.value.colorInpText,
      0.4
    ),
    '--am-c-select-text-op03': useColorTransparency(
      amColors.value.colorInpText,
      0.03
    ),
    '--am-c-select-text-op06': useColorTransparency(
      amColors.value.colorInpText,
      0.06
    ),
    '--am-font-family': amFonts.value.fontFamily,
  }
})

function visibleChange (eventValue) {
  amSelect.value.tooltipRef.popperRef.contentRef.style.setProperty('--am-c-success', amColors.value.colorSuccess)
  amSelect.value.tooltipRef.popperRef.contentRef.style.setProperty('--am-c-error', amColors.value.colorError)
  amSelect.value.tooltipRef.popperRef.contentRef.style.setProperty('--am-c-warning', amColors.value.colorWarning)
  amSelect.value.tooltipRef.popperRef.contentRef.style.setProperty('--am-c-option-bgr', amColors.value.colorDropBgr)
  amSelect.value.tooltipRef.popperRef.contentRef.style.setProperty('--am-c-option-border', amColors.value.colorDropBorder)
  amSelect.value.tooltipRef.popperRef.contentRef.style.setProperty('--am-c-option-text', amColors.value.colorDropText)
  amSelect.value.tooltipRef.popperRef.contentRef.style.setProperty('--am-c-option-text-op65', useColorTransparency(amColors.value.colorDropText, 0.65))
  amSelect.value.tooltipRef.popperRef.contentRef.style.setProperty('--am-c-option-text-op15', useColorTransparency(amColors.value.colorDropText, 0.15))
  amSelect.value.tooltipRef.popperRef.contentRef.style.setProperty('--am-c-option-hover', useColorTransparency(amColors.value.colorDropText, 0.1))
  amSelect.value.tooltipRef.popperRef.contentRef.style.setProperty('--am-c-option-selected', amColors.value.colorPrimary)
  amSelect.value.tooltipRef.popperRef.contentRef.style.setProperty('--am-c-option-selected-op10', useColorTransparency(amColors.value.colorPrimary, 0.1))
  amSelect.value.tooltipRef.popperRef.contentRef.style.setProperty('--am-c-option-img-bgr', amColors.value.colorSuccess)
  amSelect.value.tooltipRef.popperRef.contentRef.style.setProperty('--am-c-option-img-text', amColors.value.colorMainBgr)
  amSelect.value.tooltipRef.popperRef.contentRef.style.setProperty('--am-font-family', amFonts.value.fontFamily)
  emits('visible-change', eventValue)
}
</script>

<script>
export default {
  inheritAttrs: false,
}
</script>

<style lang="scss">
@mixin am-select-block {
  .am-select {
    // -c-    color
    // -rad-  border radius
    // -h-    height
    // -fs-   font size
    // -padd- padding
    // -bgr   background
    --am-c-select-bgr: var(--am-c-inp-bgr);
    --am-c-select-border: var(--am-c-inp-border);
    --am-c-select-text: var(--am-c-inp-text);
    --am-c-select-placeholder: var(--am-color-input-placeholder);
    --am-rad-select: var(--am-rad-inp);
    --am-fs-select: var(--am-fs-inp);
    --am-h-select: var(--am-h-inp);
    --am-padd-select: 0 12px;
    width: 100%;
    box-shadow: 0 2px 2px var(--am-c-select-shadow);
    border-radius: var(--am-rad-select);

    // Select Wrapper
    &-wrapper {
      width: 100%;
    }

    // Size - default / medium / small / mini / micro
    &--default {
      --am-h-select: 40px;
    }
    &--medium {
      --am-h-select: 36px;
    }
    &--small {
      --am-h-select: 32px;
    }
    &--mini {
      --am-h-select: 28px;
    }
    &--micro {
      --am-h-select: 24px;
    }

    // Padding - depends on icon appearance
    &--prefix {
      --am-padd-select: 0 12px 0 8px;
      &.am-select--suffix {
        --am-padd-select: 0 8px;
      }
    }

    &--suffix {
      --am-padd-select: 0 8px 0 12px;
      &.am-select--prefix {
        --am-padd-select: 0 8px;
      }
    }

    // Disabled
    &--disabled {
      --am-c-select-bgr: var(--am-c-select-text-op03) !important;
      --am-c-select-text: var(--am-c-select-text-op60) !important;
    }

    // Multiple select - collapse tags not set to true
    &--multiple {
      // Select Wrapper - unset height
      &.am-select {
        .el-select {
          &__wrapper {
            min-height: var(--am-h-select);
            height: unset;
            padding-top: 8px;
            padding-bottom: 8px;
          }
        }
      }
    }

    .el-select {
      // Select Wrapper
      &__wrapper {
        display: flex;
        align-items: center;
        gap: 0 6px;
        position: relative;
        height: var(--am-h-select);
        min-height: 24px;
        padding: var(--am-padd-select);
        background-color: var(--am-c-select-bgr);
        border: none;
        border-radius: var(--am-rad-select);
        box-shadow: 0 0 0 1px var(--am-c-select-border);
        box-sizing: border-box;

        &:hover:not(.is-focused), &.is-hovering:not(.is-focused) {
          --am-c-select-border: var(--am-c-select-text-op40);
        }

        &:focus, &.is-focused {
          --am-c-select-border: var(--am-c-primary);
        }

        &.is-disabled {
          cursor: not-allowed;
        }
      }

      // Select Input
      &__input {
        width: 100%;
        height: 24px;
        min-height: auto;
        font-size: var(--am-fs-select);
        font-weight: 400;
        line-height: 1.6;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        color: var(--am-c-select-text);
        border: none;
        border-radius: var(--am-rad-select);
        background-color: transparent;
        padding: 0;
        box-shadow: none;

        &::-webkit-input-placeholder {
          /* Chrome/Opera/Safari */
          color: var(--am-c-select-placeholder);
        }
        &::-moz-placeholder {
          /* Firefox 19+ */
          color: var(--am-c-select-placeholder);
        }
        &:-ms-input-placeholder {
          /* IE 10+ */
          color: var(--am-c-select-placeholder);
        }
        &:-moz-placeholder {
          /* Firefox 18- */
          color: var(--am-c-select-placeholder);
        }
      }

      // Select Items
      &__selected-item {
        &.el-select__placeholder {
          &.is-transparent {
            --am-c-select-text: var(--am-c-select-placeholder);
          }

          span {
            font-size: var(--am-fs-select) !important;
            font-weight: 400;
            line-height: 1.6;
            color: var(--am-c-select-text) !important;
          }
        }
      }

      // Select Suffix icon
      &__suffix {
        .el-icon {
          font-size: 18px;
          color: var(--am-c-select-text);
        }
        [class^='am-icon'] {
          font-size: 18px;
          color: var(--am-c-select-text)
        }
      }

      // Select Prefix icon
      &__prefix {
        [class^='am-icon'] {
          font-size: 24px;
          line-height: 1;
          color: var(--am-c-select-text)
        }
      }
    }

    // Multiple select
    .el-tag {
      display: flex;
      gap: 0 4px;
      padding: 4px 6px;
      border-radius: 4px;
      background-color: var(--am-c-select-text-op06);
      box-sizing: border-box;

      .el-select {
        &__tags {
          &-text {
            font-size: var(--am-fs-select);
            font-weight: 400;
            line-height: 1;
            color: var(--am-c-select-text);
          }
        }
      }

      &__close {
        color: var(--am-c-select-text-op50);
        transition: color 0.3s ease-in-out;

        &:hover {
          color: var(--am-c-select-text);
          background: none;
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
.am-select-popper {
  z-index: 9999999999 !important;
}

// public
.amelia-v2-booking #amelia-container {
  @include am-select-block;
}

// admin
#amelia-app-backend-new {
  @include am-select-block;
}
</style>
