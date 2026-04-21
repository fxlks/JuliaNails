<template>

  <!-- Date Picker -->
  <div
    class="am-date-picker__wrapper"
    :class="{'is-disabled': props.disabled}"
    :style="cssVars"
  >
    <div
      class="am-date-picker__input"
      :class="[`am-${props.type}`, {'is-disabled': props.disabled}]"
      @click="triggerCalendar"
    >
      <div
        v-if="props.type === 'date' && model"
        class="am-date-picker__input-date"
      >
        {{ getFrontedFormattedDate(moment(model).format('YYYY-MM-DD')) }}
      </div>

      <div
        v-if="props.type === 'daterange' && model[0]"
        class="am-date-picker__input-start"
      >
        {{ getFrontedFormattedDate(moment(model[0]).format('YYYY-MM-DD')) }}
      </div>
      <div
        v-if="props.type === 'daterange' && model[1]"
        class="am-date-picker__input-end"
      >
        {{ getFrontedFormattedDate(moment(model[1]).format('YYYY-MM-DD')) }}
      </div>
    </div>

    <el-config-provider :locale="elementPlusTranslations(props.lang)">
      <el-date-picker
        :id="id"
        ref="amDatePicker"
        v-model="model"
        :readonly="props.readonly"
        :disabled="props.disabled"
        :editable="props.editable"
        :clearable="props.clearable"
        :placeholder="props.placeholder"
        :start-placeholder="props.startPlaceholder"
        :end-placeholder="props.endPlaceholder"
        :type="props.type"
        :format="props.format"
        :popper-class="props.popperClass"
        :popper-options="props.popperOptions"
        :popper-style="cssVars"
        :range-separator="props.rangeSeparator"
        :default-value="props.defaultValue"
        :default-time="props.defaultTime"
        :value-format="props.valueFormat"
        :unlink-panels="props.unlinkPanels"
        :prefix-icon="props.prefixIcon"
        :clear-icon="props.clearIcon"
        :validate-event="props.validateEvent"
        :disabled-date="disabledDate"
        :shortcuts="props.shortcuts"
        :cell-class-name="props.cellClassName"
        :teleported="props.teleported"
        :empty-values="props.emptyValues"
        :value-on-clear="props.valueOnClear"
        :fallback-placements="props.fallbackPlacements"
        :placement="props.placement"
        class="am-date-picker"
        :class="[
          `am-date-picker--${size}`,
          {'am-date-picker--disabled': props.disabled}
        ]"
        @change="(eventValue) => $emit('change', eventValue)"
        @blur="(e) => $emit('blur', e)"
        @focus="(e) => $emit('focus', e)"
        @clear="(e) => $emit('clear', e)"
        @calendar-change="(e) => $emit('calendar-change', e)"
        @panel-change="(e) => $emit('panel-change', e)"
        @visible-change="(e) => $emit('visible-change', e)"
      >
      </el-date-picker>
    </el-config-provider>
  </div>
  <!-- /Date Picker -->

</template>

<script setup>
// * Import from Vue
import {
  computed,
  ref,
  toRefs,
  inject,
  markRaw,
} from "vue";

// * Libraries
import moment from "moment";

// * Composables
import { useColorTransparency } from "../../../assets/js/common/colorManipulation";
import { elementPlusTranslations } from "../../../assets/js/common/translationsElementPlus";
import { getFrontedFormattedDate } from "../../../assets/js/common/date";
import IconComponent from "../icons/IconComponent.vue";

/**
 * Component Props
 */
const props = defineProps({
  modelValue: {
    type: [String, Array, Object, Number],
  },
  readonly: {
    // * whether DatePicker is read only
    type: Boolean,
    default: false
  },
  disabled: {
    // * whether DatePicker is disabled
    type: Boolean,
    default: false
  },
  size: {
    // default / medium / small / mini / micro
    type: String,
    default: 'default',
    validator(value) {
      return ['default', 'medium', 'small', 'mini', 'micro'].includes(value)
    }
  },
  editable: {
    // * whether the input is editable
    type: Boolean,
    default: true
  },
  clearable: {
    // * whether the input is clearable and whether to show clear button
    type: Boolean,
    default: false
  },
  placeholder: {
    // * placeholder in non-range mode
    type: String,
    default: ''
  },
  startPlaceholder: {
    // * placeholder in range mode
    type: String,
  },
  endPlaceholder: {
    // * placeholder in range mode
    type: String,
  },
  type: {
    // * type of the picker
    type: String,
    default: 'date',
    validator(value) {
      return ['year', 'years','month', 'months', 'date', 'dates', 'datetime', 'week', 'datetimerange', 'daterange', 'monthrange', 'yearrange'].includes(value)
    }
  },
  format: {
    // * format of the displayed value in the input box
    type: String
  },
  popperClass: {
    // * custom class name for DatePicker's dropdown
    type: String,
    default: ''
  },
  popperOptions: {
    // * options for the popper.js instance
    type: Object,
    default: () => {}
  },
  rangeSeparator: {
    // * separator between two date inputs in range mode
    type: String,
    default: '-'
  },
  defaultValue: {
    // * optional, default date of the calendar
    type: [String, Array, Object, Number],
  },
  defaultTime: {
    // * optional, default time of the calendar
    type: [String, Array, Object, Number],
  },
  valueFormat: {
    // * optional, format of binding value. If not specified, the binding value will be a Date object
    type: String
  },
  id: {
    // * same as id in native input
    type: [String, Array],
  },
  name: {
    // * same as name in native input
    type: [String, Array],
  },
  unlinkPanels: {
    // * whether to unlink the two panels in range mode
    type: Boolean,
    default: false
  },
  prefixIcon: {
    type: [String, Object, Function],
    default: markRaw({
      components: { IconComponent },
      template: `<IconComponent icon="calendar"/>`,
    }),
  },
  clearIcon: {
    type: [String, Object, Function],
    default: markRaw({
      components: { IconComponent },
      template: `<IconComponent icon="close"/>`,
    }),
  },
  validateEvent: {
    // * whether to trigger validation when the value changes
    type: Boolean,
    default: true
  },
  disabledDate: {
    // * a function determining if a date is disabled with that date as its parameter. Should return a Boolean
    type: Function,
    default: () => {}
  },
  shortcuts: {
    // * an array of objects to set shortcut options
    type: Array,
    default: () => []
  },
  cellClassName: {
    // * a function determining the class name of a date cell with that date as its parameter. Should return a String
    type: Function,
  },
  teleported: {
    // * empty values of component
    type: Boolean,
    default: true
  },
  emptyValues: {
    // * whether to show empty values
    type: [String, Array],
  },
  valueOnClear: {
    // * clear return value
    type: [String, Number, Boolean, Function],
    default: ''
  },
  fallbackPlacements: {
    // * list of possible positions for Tooltip, fallback placements for the popper.js instance
    type: Array,
  },
  placement: {
    // * position of the DatePicker's dropdown
    type: String,
    default: 'bottom',
    validator(value) {
      return ['top', 'top-start', 'top-end', 'bottom', 'bottom-start', 'bottom-end', 'left', 'left-start', 'left-end', 'right', 'right-start', 'right-end'].includes(value)
    }
  },
  lang: {
    type: String,
    default: ''
  }
})

/**
 * Component Emits
 * */
const emits = defineEmits(['update:modelValue', 'change', 'blur', 'focus', 'clear', 'calendar-change', 'panel-change', 'visible-change'])

/**
 * Component model
 */
let { modelValue } = toRefs(props)
let model = computed({
  get: () => modelValue.value,
  set: (val) => {
    emits('update:modelValue', val)
  }
})

/**
 * Component reference
 */
const amDatePicker = ref(null)

function triggerCalendar () {
  if (amDatePicker.value) {
    amDatePicker.value.focus()
  }
}

// * Colors block
let amColors = inject('amColors');

let cssVars = computed(() => {
  return {
    '--am-c-primary': amColors.value.colorPrimary,
    '--am-c-inp-bgr': amColors.value.colorInpBgr,
    '--am-c-inp-border': amColors.value.colorInpBorder,
    '--am-c-inp-text': amColors.value.colorInpText,
    '--am-c-inp-text-op03': useColorTransparency(amColors.value.colorInpText, 0.03),
    '--am-c-inp-text-op05': useColorTransparency(amColors.value.colorInpText, 0.05),
    '--am-c-inp-text-op40': useColorTransparency(amColors.value.colorInpText, 0.4),
    '--am-c-inp-text-op60': useColorTransparency(amColors.value.colorInpText, 0.6),
    '--am-c-inp-placeholder': amColors.value.colorInpPlaceHolder,
    '--am-c-drop-bgr': amColors.value.colorDropBgr,
    '--am-c-drop-text': amColors.value.colorDropText,
    '--am-c-drop-text-op03': useColorTransparency(amColors.value.colorDropText, 0.03),
    '--am-c-drop-text-op10': useColorTransparency(amColors.value.colorDropText, 0.1),
    '--am-c-drop-text-op30': useColorTransparency(amColors.value.colorDropText, 0.3),
    '--am-c-drop-text-op50': useColorTransparency(amColors.value.colorDropText, 0.5),
    '--am-c-drop-text-op70': useColorTransparency(amColors.value.colorDropText, 0.7),
    '--am-c-drop-text-op80': useColorTransparency(amColors.value.colorDropText, 0.8),
    '--am-c-drop-border': amColors.value.colorDropBorder,
    '--am-c-skeleton-op20': useColorTransparency(amColors.value.colorMainText, 0.2),
    '--am-c-skeleton-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-c-skeleton-sb-op20': useColorTransparency(amColors.value.colorSbText, 0.2),
    '--am-c-skeleton-sb-op60': useColorTransparency(amColors.value.colorSbText, 0.6),
  }
})
</script>

<style lang="scss">
@mixin am-datepicker-block {

  // Date Picker
  .am-date-picker {
    &__wrapper {
      --am-c-dpicker-bgr: var(--am-c-inp-bgr);
      --am-c-dpicker-text: var(--am-c-inp-text);
      --am-c-dpicker-border: var(--am-c-inp-border);
      --am-c-dpicker-placeholder: var(--am-c-inp-placeholder);
      --am-c-dpicker-shadow: var(--am-c-inp-text-op05);
      --am-c-dpicker-rad: var(--am-c-inp-rad);
      --am-fs-dpicker: var(--am-fs-inp);
      --am-h-dpicker: 40px;
      --am-padd-dpicker: 0 8px;

      display: flex;
      align-items: center;
      position: relative;
      width: 100%;
      background-color: var(--am-c-dpicker-bgr);
      border-radius: 4px;
      border: none;
      box-shadow: 0 2px 2px var(--am-c-dpicker-shadow);

      &:hover:not(.is-disabled) {
        --am-c-dpicker-border: var(--am-c-inp-text-op40);
      }

      &.is-disabled {
        --am-c-dpicker-bgr: var(--am-c-inp-text-op03);
      }
    }

    // size
    &--default {
      --am-h-dpicker: 40px;
    }

    &--medium {
      --am-h-dpicker: 36px;
    }

    &--small {
      --am-h-dpicker: 32px;
    }

    &--mini {
      --am-h-dpicker: 28px;
    }

    &--micro {
      --am-h-dpicker: 24px;
    }

    //  Custom input display
    &__input {
      position: absolute;
      top: 0;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: space-around;
      cursor: text;
      z-index: 1;

      &.am-date {
        left: 36px;
        width: calc(100% - 36px);
      }

      &.am-daterange {
        left: 32px;
        width: calc(100% - 54px);
      }

      &.is-disabled {
        --am-c-dpicker-text: var(--am-c-inp-text-op60);
        cursor: not-allowed;
      }

      [class^='am-date-picker__input'] {
        height: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: var(--am-c-dpicker-text);
        font-size: var(--am-fs-dpicker);
        line-height: 24px;
        background-color: transparent;
        border-radius: 0;
      }

      .am-date-picker__input-date {
        width: 100%;
        text-align: left;
        justify-content: flex-start;
      }
    }

    // Date Editors
    &.el-date-editor {
      // Date Range Picker
      &--daterange {
        // Visual simulation of input display
        &.el-input__wrapper {
          width: 100%;
          height: var(--am-h-dpicker);
          background-color: transparent;
          border: none;
          box-shadow: 0 0 0 1px var(--am-c-dpicker-border);
          padding: var(--am-padd-dpicker);
          box-sizing: border-box;
          transition: box-shadow 0.3s ease-in-out;
          z-index: 2;

          &:hover {
            --am-c-dpicker-border: var(--am-c-inp-text-op40);
          }

          &.is-active:not(.is-disabled), &.is-focus:not(.is-disabled) {
            --am-c-dpicker-border: var(--am-c-primary);
          }

          &.is-disabled {
            --am-c-dpicker-text: var(--am-c-inp-text-op60);
          }
        }

        .el-range {
          &-input {
            color: transparent;
            font-size: var(--am-fs-dpicker);
            line-height: 24px;
            border: none;
            background: none;
            border-radius: 0;
            padding: 0;
            margin: 0;
            box-shadow: none;
          }

          &__icon {
            color: var(--am-c-dpicker-text);
            font-size: 24px;
            line-height: 24px;
          }

          &-separator {
            color: var(--am-c-dpicker-text);
            font-size: var(--am-fs-dpicker);
            line-height: 24px;
          }
        }
      }

      // Date Picker
      &--date {
        &.el-input {
          width: 100%;
          height: var(--am-h-dpicker);
          background: none;
          z-index: 2;

          &--prefix {
            --am-padd-dpicker: 0 12px 0 8px;
          }

          &--suffix {
            --am-padd-dpicker: 0 8px 0 12px;
          }

          &--prefix, &--suffix {
            --am-padd-dpicker: 0 8px;
          }

          &.is-disabled {
            .el-input__inner {
              -webkit-text-fill-color: transparent;
            }
          }
        }

        .el-input {
          &__wrapper {
            width: 100%;
            height: var(--am-h-dpicker);
            gap: 0 4px;
            background-color: transparent;
            border: none;
            box-shadow: 0 0 0 1px var(--am-c-dpicker-border);
            padding: var(--am-padd-dpicker);
            box-sizing: border-box;
            transition: box-shadow 0.3s ease-in-out;

            &.is-focus:not(.is-disabled), &.is-active:not(.is-disabled) {
              --am-c-dpicker-border: var(--am-c-primary);
            }
          }

          &__inner {
            color: transparent;
            font-size: var(--am-fs-dpicker);
            line-height: 24px;
            border: none;
            background: none;
            border-radius: 0;
            padding: 0;
            margin: 0;
            box-shadow: none;

            // Placeholder
            &::placeholder {
              color: var(--am-c-dpicker-placeholder);
              opacity: 1; /* Ensures it’s not transparent */
            }
            &::-webkit-input-placeholder { /* Chrome, Safari */
              color: var(--am-c-dpicker-placeholder);
            }
            &:-moz-placeholder { /* Firefox 4-18 */
              color: var(--am-c-dpicker-placeholder);
              opacity: 1;
            }
            &::-moz-placeholder { /* Firefox 19+ */
              color: var(--am-c-dpicker-placeholder);
              opacity: 1;
            }
            &:-ms-input-placeholder { /* IE 10-11 */
              color: var(--am-c-dpicker-placeholder);
            }
          }

          &__prefix, &__suffix {
            font-size: 24px;
            line-height: 1;
            color: var(--am-c-dpicker-text);

            * {
              font-size: 24px;
              line-height: 1;
              color: var(--am-c-dpicker-text);
            }

            .am-icon-close {
              font-size: 18px;
            }
          }
        }
      }
    }
  }
}

.el-picker-panel {
  background-color: var(--am-c-drop-bgr);
  color: var(--am-c-drop-text);
  border: 1px solid var(--am-c-drop-border);

  &__icon-btn {
    color: var(--am-c-drop-text);

    &:hover {
      color: var(--am-c-btn-prim)
    }
  }

  &__content {
    &.is-left {
      border-right: 1px solid var(--am-c-drop-text-op10);
    }
    table, table td, table tr, table tr:is(.el-date-table__row) {
      border: none !important;
    }
    table tr:not(.el-date-table__row) th {
      color: var(--am-c-drop-text-op70);
      border: none !important;
      border-bottom: 1px solid var(--am-c-drop-text-op10) !important;
    }
    table {
      td.today .el-date-table-cell__text {
        color: var(--am-c-drop-text);
      }
      td.current:not(.disabled) .el-date-table-cell__text {
        background: var(--am-c-btn-prim);
      }
      td.available:hover > div > span {
        background: var(--am-c-drop-text);
        border-radius: 50%;
        color: var(--am-c-drop-bgr);
      }
      td.in-range > .el-date-table-cell {
        background-color: var(--am-c-drop-text-op10);
      }
      td.in-range:hover > .el-date-table-cell {
        background-color: var(--am-c-drop-text-op50);
      }
      td.end-date .el-date-table-cell__text, td.start-date .el-date-table-cell__text {
        color: var(--am-c-drop-bgr);
        background-color: var(--am-c-drop-text-op80);
      }
      td.today.start-date .el-date-table-cell__text {
        color: var(--am-c-drop-bgr);
      }
      td.prev-month, td.next-month {
        color: var(--am-c-drop-text-op30);
      }

      td.disabled .el-date-table-cell {
        color: var(--am-c-drop-text-op30);
        background-color: var(--am-c-drop-text-op03);
      }
    }
  }

  .d-arrow-left, .d-arrow-right {
    display: none;
  }
}

.el-date-picker__header {
  text-align: center;
  margin: 12px;

  .el-picker-panel__icon-btn {
    padding: 0;
  }

  .el-date-picker {
    padding: 0;
    font-size: 12px;
    color: #303133;
    cursor: pointer;
    margin-top: 8px;
    border-width: 0;
    border-style: initial;
    border-color: initial;
    border-image: initial;
    //background: transparent;
    outline: none;

    &__prev-btn {
      float: left;
    }

    &__next-btn {
      float: right;
    }
    &__prev-btn:focus, &__next-btn:focus {
      //background: transparent;
      outline: none;
    }
  }
  &-label:hover {
    color: var(--am-c-btn-prim);
  }
}

.el-popper {
  &.el-picker__popper {
    &[data-popper-placement^="top"],
    &[data-popper-placement^="bottom"],
    &[data-popper-placement^="left"],
    &[data-popper-placement^="right"] {
      .el-popper__arrow {
        display: none;
      }
    }
  }
}

// public
.amelia-v2-booking #amelia-container {
  @include am-datepicker-block;
}

// admin
#amelia-app-backend-new #amelia-container {
  @include am-datepicker-block;
}
</style>