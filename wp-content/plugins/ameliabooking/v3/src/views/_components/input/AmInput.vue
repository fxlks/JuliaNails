<template>
  <div class="am-input-wrapper" :style="cssVars">
    <el-input
      v-bind="filteredProps"
      ref="amInput"
      v-model="model"
      :class="[
        props.type === 'text' ||
        props.type === 'email' ||
        props.type === 'password' ? 'am-input' : 'am-textarea',
        props.size && (props.type !== 'text-area' && props.type !== 'textarea') ? `am-input--${props.size}` : ''
      ]"
      :type="props.type === 'text-area' ? 'textarea' : props.type"
      :aria-label="props.ariaLabel"
      @blur="(e) => $emit('blur', e)"
      @focus="(e) => $emit('focus', e)"
      @change="
        (currentValue, oldValue) => $emit('change', currentValue, oldValue)
      "
      @input="
        (currentValue, oldValue) => $emit('input', currentValue, oldValue)
      "
      @keyup.enter="(e) => $emit('enter', e)"
      @clear="() => $emit('clear')"
    >
      <!-- * Prepend Slot * -->
      <template v-if="props.prepend" #prepend>
        <template v-if="typeof props.prepend === 'string'">
          {{ props.prepend }}
        </template>
        <component
          :is="props.prepend"
          v-if="typeof props.prepend === 'object'"
        />
      </template>
      <!-- */ Prepend Slot * -->

      <!-- * Icon Start/Prefix * -->
      <template v-if="props.prefixIcon" #prefix>
        <span
          v-if="typeof props.prefixIcon === 'string'"
          :class="`am-icon-${props.prefixIcon}`"
        />
        <component
          :is="props.prefixIcon"
          v-if="typeof props.prefixIcon === 'object'"
        />
      </template>
      <!-- */ Icon Start/Prefix * -->

      <!-- * Icon End/Suffix * -->
      <template v-if="props.suffixIcon" #suffix>
        <span
          v-if="typeof props.suffixIcon === 'string'"
          :class="`am-icon-${props.suffixIcon}`"
        />
        <component
          :is="props.suffixIcon"
          v-if="typeof props.suffixIcon === 'object'"
        />
      </template>
      <!-- */ Icon End/Suffix * -->

      <!-- * Append Slot * -->
      <template v-if="props.append" #append>
        <template v-if="typeof props.append === 'string'">
          {{ props.append }}
        </template>
        <component :is="props.append" v-if="typeof props.append === 'object'" />
      </template>
      <!-- */ Append Slot * -->
    </el-input>
  </div>
</template>

<script setup>
// * Import from Vue
import { computed, ref, toRefs, inject } from 'vue'

// * Import from Libraries
import { format, unformat } from 'v-money3'

// * Composables
import { useColorTransparency } from '../../../assets/js/common/colorManipulation'
import { useCurrencyOptions } from '../../../assets/js/common/formatting'

/**
 * Component Props
 */
const props = defineProps({
  id: {
    type: String,
  },
  type: {
    type: String,
    default: 'text',
  },
  modelValue: {
    type: [String, Number, null, undefined],
  },
  maxlength: {
    type: [String, Number],
  },
  minlength: {
    type: [String, Number],
  },
  showWordLimit: {
    // * whether show word count, only works when type is 'text' or 'textarea'
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: '',
  },
  clearable: {
    // * whether to show clear button, only works when type is not 'textarea'
    type: Boolean,
    default: false,
  },
  formatter: {
    // * specifies the format of the value presented input.(only works when type is 'text')
    type: Function,
  },
  parser: {
    // * specifies the value extracted from formatter input.(only works when type is 'text')
    type: Function,
  },
  showPassword: {
    // * whether to show toggleable password input, only works when type is 'password'
    type: Boolean,
    default: false,
  },
  disabled: {
    // * whether to disable input
    type: Boolean,
    default: false,
  },
  size: {
    type: String,
    default: 'default',
    validator(value) {
      return ['default', 'medium', 'small', 'mini', 'micro'].includes(value)
    },
  },
  prefixIcon: {
    type: [String, Object],
    default: '',
  },
  prepend: {
    type: [String, Object],
    default: '',
  },
  suffixIcon: {
    type: [String, Object],
    default: '',
  },
  append: {
    type: [String, Object],
    default: '',
  },
  rows: {
    // * number of rows of textarea, only works when type is 'textarea'
    type: Number,
    default: 2,
  },
  autosize: {
    // * whether textarea has an adaptive height, only works when type is 'textarea'. Can accept an object, e.g. { minRows: 2, maxRows: 6 }
    type: [Boolean, Object],
    default: false,
  },
  autocomplete: {
    // * whether to enable native autocomplete
    type: String,
    default: 'off',
  },
  name: {
    // * native name attribute
    type: String,
    default: '',
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  max: {
    type: [String, Number],
  },
  min: {
    type: [String, Number],
  },
  step: {
    type: [String, Number],
  },
  resize: {
    // * whether to enable native resize, only works when type is 'textarea'
    type: String,
    default: 'vertical',
    validator(value) {
      return ['none', 'both', 'horizontal', 'vertical'].includes(value)
    },
  },
  autofocus: {
    // * whether to focus input on mounted
    type: Boolean,
    default: false,
  },
  form: {
    // * native form attribute
    type: String,
  },
  ariaLabel: {
    type: String,
    default: '',
  },
  tabindex: {
    // * native tabindex attribute
    type: [String, Number],
  },
  validateEvent: {
    type: Boolean,
    default: true,
  },
  inputStyle: {
    // * custom input style
    type: [String, Object],
    default: () => ({}),
  },
  isMoney: {
    type: Boolean,
    default: false,
  },
})

// Create filtered properties
const filteredProps = computed(() => {
  // Create a copy of props
  const filterObj = { ...props }

  // List of props to exclude
  const excludeProps = [
    'id',
    'type',
    'modelValue',
    'size',
    'label',
    'prefixIcon',
    'prepend',
    'suffixIcon',
    'append',
    'isMoney',
  ]

  // Remove excluded props
  excludeProps.forEach((prop) => {
    delete filterObj[prop]
  })

  return filterObj
})

/**
 * Component Emits
 * */
const emits = defineEmits([
  'change',
  'input',
  'visible-change',
  'clear',
  'blur',
  'focus',
  'update:modelValue',
  'enter',
])

/**
 * Component model
 */
let { modelValue } = toRefs(props)

let model = computed({
  get: () => {
    return props.isMoney
      ? format(modelValue.value, useCurrencyOptions())
      : modelValue.value
  },
  set: (val) => {
    emits(
      'update:modelValue',
      props.isMoney
        ? unformat(val, {
            ...useCurrencyOptions(),
            modelModifiers: { number: true },
          })
        : val
    )
  },
})

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
    '--am-c-inp-bgr': amColors.value.colorInpBgr,
    '--am-c-inp-border': amColors.value.colorInpBorder,
    '--am-c-inp-text': amColors.value.colorInpText,
    '--am-c-inp-text-op03': useColorTransparency(
      amColors.value.colorInpText,
      0.03
    ),
    '--am-c-inp-text-op05': useColorTransparency(
      amColors.value.colorInpText,
      0.05
    ),
    '--am-c-inp-text-op40': useColorTransparency(
      amColors.value.colorInpText,
      0.4
    ),
    '--am-c-inp-text-op60': useColorTransparency(
      amColors.value.colorInpText,
      0.6
    ),
    '--am-c-inp-placeholder': amColors.value.colorInpPlaceHolder,
  }
})

/**
 * Component reference
 */
const amInput = ref(null)
</script>

<style lang="scss">
@mixin am-input-block {
  // Input Wrapper
  .am-input-wrapper {
    --am-c-input-bgr: var(--am-c-inp-bgr);
    --am-c-input-border: var(--am-c-inp-border);
    --am-c-input-text: var(--am-c-inp-text);
    --am-c-input-placeholder: var(--am-c-inp-placeholder);
    --am-c-input-shadow: var(--am-c-inp-text-op05);
    --am-rad-input: var(--am-rad-inp);
    --am-fs-input: var(--am-fs-inp);
    --am-h-input: var(--am-h-inp);
    --am-padd-input: 0 12px;
    display: flex;
    width: 100%;
    position: relative;

    // Input
    .am-input {
      width: 100%;
      box-sizing: border-box;
      min-width: 100%;
      max-width: 100%;
      background-color: transparent;
      box-shadow: 0 2px 2px var(--am-c-input-shadow);
      border-radius: var(--am-rad-input);

      &.el-input {
        // Disabled state
        &.is-disabled {
          --am-c-input-bgr: var(--am-c-inp-text-op03);
          --am-c-input-text: var(--am-c-inp-text-op60);
          box-shadow: none;
          cursor: not-allowed;

          .el-input {
            &__wrapper {
              &:hover {
                --am-c-input-border: var(--am-c-inp-border);
              }
            }
          }
        }

        // Icons - Prefix and Suffix
        &--prefix {
          --am-padd-input: 0 12px 0 8px;
        }

        &--suffix {
          --am-padd-input: 0 8px 0 12px;
        }

        &--prefix.el-input--suffix {
          --am-padd-input: 0 8px;
        }

        // Icons - Prepend and Append
        &.el-input-group {
          // Prepend
          &--prepend {
            --am-rad-input: 0 6px 6px 0;

            .el-input-group__prepend {
              border: none;
              padding: 0 12px;
              background-color: var(--am-c-inp-text-op03);
              box-shadow: inset 1px 0 0 0 var(--am-c-input-border),
                0 1px 0 0 var(--am-c-input-border),
                0 -1px 0 0 var(--am-c-input-border);
            }
          }

          // Append
          &--append {
            --am-rad-input: 6px 0 0 6px;

            .el-input-group__append {
              border: none;
              padding: 0 12px;
              background-color: var(--am-c-inp-text-op03);
              box-shadow: 0 0 0 0 var(--am-c-input-border),
                0 1px 0 0 var(--am-c-input-border),
                0 -1px 0 0 var(--am-c-input-border),
                inset -1px 0 0 0 var(--am-c-input-border);
            }
          }

          // Both Prepend and Append
          &--prepend.el-input-group--append {
            --am-rad-input: 0;
          }
        }
      }

      .el-input {
        // Input wrapper - visual display
        &__wrapper {
          display: inline-flex;
          gap: 0 6px;
          height: var(--am-input-height);
          background-color: var(--am-c-input-bgr);
          border: none;
          border-radius: var(--am-rad-input);
          box-shadow: 0 0 0 1px var(--am-c-input-border);
          padding: var(--am-padd-input);
          box-sizing: border-box;
          transition: box-shadow 0.3s ease-in-out;

          &:hover {
            --am-c-input-border: var(--am-c-inp-text-op40);
          }

          &.is-focus {
            --am-c-input-border: var(--am-c-primary);
          }
        }

        // Input
        &__inner {
          width: 100%;
          height: 100%;
          min-height: 24px;
          max-height: unset;
          font-size: var(--am-fs-input);
          line-height: 24px;
          color: var(--am-c-input-text);
          border: none;
          background: none;
          border-radius: 0;
          padding: 0;
          margin: 0;
          box-shadow: none;

          // Placeholder
          &::placeholder {
            color: var(--am-c-input-placeholder);
            opacity: 1; /* Ensures it’s not transparent */
          }
          &::-webkit-input-placeholder {
            /* Chrome, Safari */
            color: var(--am-c-input-placeholder);
          }
          &:-moz-placeholder {
            /* Firefox 4-18 */
            color: var(--am-c-input-placeholder);
            opacity: 1;
          }
          &::-moz-placeholder {
            /* Firefox 19+ */
            color: var(--am-c-input-placeholder);
            opacity: 1;
          }
          &:-ms-input-placeholder {
            /* IE 10-11 */
            color: var(--am-c-input-placeholder);
          }
        }

        // Prefix and Suffix Icons
        &__prefix,
        &__suffix {
          &-inner {
            align-items: center;
            font-size: 24px;
            color: var(--am-c-inp-text);

            * {
              font-size: 24px;
              color: var(--am-c-inp-text);
            }

            // Element plus icons
            i.el-input__icon {
              font-size: 18px;

              // Clear icon - override
              &.el-input__clear {
                &:before {
                  font-family: 'amelia-icons' !important;
                  content: $am-icon-close;
                }

                svg {
                  display: none;
                }
              }
            }
          }
        }
      }

      // Input Sizes - Default, Medium, Small, Mini, Micro
      &--default {
        --am-input-height: 40px;
      }
      &--medium {
        --am-input-height: 36px;
      }
      &--small {
        --am-input-height: 32px;
      }
      &--mini {
        --am-input-height: 28px;
      }
      &--micro {
        --am-input-height: 24px;
      }
    }

    // Textarea
    .am-textarea {
      width: 100%;
      box-sizing: border-box;
      min-width: 100%;
      max-width: 100%;
      box-shadow: 0 2px 2px var(--am-c-input-shadow);

      &.is-disabled {
        --am-c-input-bgr: var(--am-c-inp-text-op03);
        --am-c-input-text: var(--am-c-inp-text-op60);
        box-shadow: none;
        cursor: not-allowed;

        .el-textarea {
          &__inner {
            &:hover {
              --am-c-input-border: var(--am-c-inp-border);
            }
          }
        }
      }

      .el-textarea {
        &__inner {
          width: 100%;
          color: var(--am-c-input-text);
          background-color: var(--am-c-input-bgr);
          border: none;
          border-radius: var(--am-rad-input);
          box-shadow: 0 0 0 1px var(--am-c-input-border);
          padding: 8px 12px;
          box-sizing: border-box;
          transition: box-shadow 0.3s ease-in-out;

          &:hover {
            --am-c-input-border: var(--am-c-inp-text-op40);
          }

          &.is-focus {
            --am-c-input-border: var(--am-c-primary);
          }

          // Placeholder
          &::placeholder {
            color: var(--am-c-input-placeholder);
            opacity: 1; /* Ensures it’s not transparent */
          }
          &::-webkit-input-placeholder {
            /* Chrome, Safari */
            color: var(--am-c-input-placeholder);
          }
          &:-moz-placeholder {
            /* Firefox 4-18 */
            color: var(--am-c-input-placeholder);
            opacity: 1;
          }
          &::-moz-placeholder {
            /* Firefox 19+ */
            color: var(--am-c-input-placeholder);
            opacity: 1;
          }
          &:-ms-input-placeholder {
            /* IE 10-11 */
            color: var(--am-c-input-placeholder);
          }
        }
      }
    }
  }
}

// public
.amelia-v2-booking #amelia-container {
  @include am-input-block;
}

// admin
#amelia-app-backend-new {
  @include am-input-block;
}
</style>
