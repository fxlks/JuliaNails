<template>
  <div class="am-input-phone-wrapper">
    <MazPhoneNumberInput
      ref="amPhoneInput"
      v-model="model"
      class="am-input-phone"
      :class="{'am-rtl': isRtl}"
      show-code-on-list
      :default-country-code="props.defaultCode ? props.defaultCode.toUpperCase() : null"
      :countries-height="40"
      :no-example="true"
      size="md"
      :name="name"
      :success="results?.isValid"
      :placeholder="placeholder"
      :style="cssVars"
      @country-code="countryPhoneIsoUpdated"
      @update="results = $event"
      @click="triggerClick"
      @keypress="triggerClick"
    />
  </div>
</template>

<script setup>
import MazPhoneNumberInput from 'maz-ui/components/MazPhoneNumberInput'
import { computed, ref, toRefs, inject, nextTick } from 'vue'
import { useColorTransparency } from '../../../assets/js/common/colorManipulation.js'

/**
 * Component Props
 */
const props = defineProps({
  modelValue: {
    type: String
  },
  name: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  placeholder: {
    type: String,
    default: 'Enter phone'
  },
  defaultCode: {
    type: String,
    default: ''
  }
})
/**
 * Component Emits
 * */
const emits = defineEmits(['update:modelValue', 'countryPhoneIsoUpdated'])

let isRtl = computed(() => {
  if (document) {
    return document.documentElement.dir === 'rtl'
  }
  return false
})

/**
 * Component model
 */
let { modelValue } = toRefs(props)

let model = computed({
  get: () => modelValue.value,
  set: (val) => {
    emits('update:modelValue', val)
    triggerClick()
  }
})

/**
 * Component reference
 */

const amPhoneInput = ref(null)
const results = ref()

function triggerClick () {
  if (amPhoneInput.value.$refs.CountrySelector.$refs.optionsList) {
    nextTick(() => {
      amPhoneInput.value.$refs.CountrySelector.$refs.optionsList.style.maxWidth = `${amPhoneInput.value.$el.offsetWidth}px`
    })
  }
}

function countryPhoneIsoUpdated (val) {
  emits('countryPhoneIsoUpdated', val)
}

let amColors = inject('amColors')
let cssVars = computed(() => {
  return {
    '--am-c-ph-drop-text-op10': useColorTransparency(amColors.value.colorDropText, 0.1)
  }
})
</script>

<style lang="scss">
@mixin am-phone-input-block {
  .am-input-phone {

    &-wrapper {
      width: 100%;
    }

    &.am-rtl {
      .m-phone-number-input {
        &__select {
          &.m-select {
            .m-input {
              &-wrapper {
                &-right {
                  border-radius: 0 6px 6px 0;
                  border-right: none;
                  border-left: 1px solid var(--am-c-ph-inp-border);
                  padding-right: 0;
                  padding-left: 7px;
                }
              }
            }
          }
        }

        &__input {
          direction: rtl;
          border-radius: 6px 0 0 6px;

          .m-input-wrapper {
            &-input {
              label {
                left: auto;
                right: 8px;
              }
            }
          }
        }

        &__country-flag {
          left: auto;
          right: 9px;
        }
      }
    }
  }

  .m-phone-number-input {
    // am  - amelia
    // c   - color
    // ph  - phone
    // inp - input
    // bgr - background
    --am-c-ph-inp-bgr: var(--am-c-inp-bgr);
    --am-c-ph-inp-border: var(--am-c-inp-border);
    --am-c-ph-inp-text: var(--am-c-inp-text);
    --am-c-ph-inp-placeholder: var(--am-c-inp-placeholder);
    --am-phone-input-font-size: var(--am-input-font-size);
    --am-phone-input-color-border: var(--am-c-inp-border);
    height: 40px;
    display: flex;
    flex-direction: row;
    border: none;
    box-shadow: 0 0 0 1px var(--am-c-ph-inp-border);
    border-radius: 6px;

    &__select {
      &.m-select {
        position: static;
        width: 25%;
        max-width: 60px;
        min-width: 60px;

        .m-input {
          position: static;
          height: 100%;

          &-wrapper {
            border: none;

            &-input {
              max-width: 40px;
              label, input {
                display: none;
              }
            }

            &-right {
              background: var(--am-c-ph-inp-bgr);
              border-radius: 6px 0 0 6px;
              width: 100%;
              height: 100%;
              border-right: 1px solid var(--am-c-ph-inp-border);
              display: flex;
              padding-right: 7px;
              justify-content: flex-end;

              button {
                padding: 0;
                background: transparent;

                svg {
                  font-size: 14px !important;
                  path {
                    color: var(--am-c-ph-inp-text);
                  }
                }
              }
            }
          }
        }

        .m-select-list {
          padding: 6px 0;
          //min-width: 260px;
          box-shadow: 0 1px 2px rgba(0, 20, 60, 0.2), 0 4px 8px -1px rgba(0, 20, 60, 0.17);
          border-radius: 6px;
          margin-top: 4px;
          background: var(--am-c-drop-bgr);
          max-width: 100% !important;

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

          &-item {
            padding: 4px 12px;
            font-size: 14px;
            line-height: 24px;
            font-weight: 400;
            border-radius: 0;
            outline: none;
            border: none;

            &:hover:not(:active) {
              background-color: var(--am-c-ph-drop-text-op10);
            }

            &:active {
              background-color: var(--am-c-ph-drop-text-op10);
            }

            &.--is-selected {
              border: 1px solid var(--am-c-ph-drop-text-op10);
              background: var(--am-c-ph-drop-text-op10);
              color: var(--am-c-ph-inp-text);
            }

            .m-phone-number-input__select__item {
              display: flex;
              align-items: center;
              overflow: hidden;
              text-overflow: ellipsis;
              white-space: nowrap;
              min-width: 28px;

              > span {
                color: var(--am-c-drop-text);
              }

              .maz-flag {
                display: inline-flex;
                margin-right: 8px;
                border-radius: 3px;
                min-width: 20px;
              }

              .maz-text-muted {
                width: 32px;
                margin-right: 8px;
              }

              .maz-truncate {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                max-width: calc(100% - 28px);
              }
            }
          }
        }
      }
    }

    &__input {
      background: var(--am-c-ph-inp-bgr);
      border-radius: 0 6px 6px 0;
      width: 100%;
      height: 100%;

      &.--focused {
        --am-c-ph-inp-border: var(--am-c-btn-prim);
      }

      .m-input-wrapper {
        width: 100%;
        border: none;

        &-input {
          font-size: 14px;
          line-height: 24px;

          input {
            font-size: 14px;
            color: var(--am-c-ph-inp-text) !important;
            border: none;
            background-color: transparent;
            padding: 14px 8px 8px;
            margin: 0;
          }

          label {
            left: 8px;
            color: var(--am-c-inp-placeholder);
            font-size: 12px;
          }
        }
      }
    }

    &__country-flag {
      bottom: 14px;
      left: 9px;
    }

  }
}

// Public
.amelia-v2-booking #amelia-container {
  @include am-phone-input-block;
}

// Admin
#amelia-app-backend-new {
  @include am-phone-input-block;
}
</style>
