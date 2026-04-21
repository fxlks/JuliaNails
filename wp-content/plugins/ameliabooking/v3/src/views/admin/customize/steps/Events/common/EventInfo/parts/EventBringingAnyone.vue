<template>
  <div class="am-elf__bringing" :style="cssVars">
    <div class="am-elf__bringing-main" :class="responsiveClass">
      <div class="am-elf__bringing-heading">
        {{ labelsDisplay('event_bringing') }}
      </div>
      <div class="am-elf__bringing-content" :class="responsiveClass">
        <AmInputNumber
          v-model="persons"
          :min="options.min"
          :max="options.max"
          :class="responsiveClass"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
// * Componets
import AmInputNumber from '../../../../../../../_components/input-number/AmInputNumber.vue'

// * Import from Vue
import { ref, computed, inject } from 'vue'

// * Composables
import { useColorTransparency } from '../../../../../../../../assets/js/common/colorManipulation'
import { useResponsiveClass } from '../../../../../../../../assets/js/common/responsive'
import { useReactiveCustomize } from '../../../../../../../../assets/js/admin/useReactiveCustomize.js'

// * Responsive
let cWidth = inject('containerWidth')
let responsiveClass = computed(() => {
  return useResponsiveClass(cWidth.value)
})

let langKey =inject('langKey')
let amLabels = inject('labels')

// * Customize
const { amCustomize } = useReactiveCustomize()

// * Form string recognition
let pageRenderKey = inject('pageRenderKey')

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value].bringingAnyone.translations &&
      amCustomize.value[pageRenderKey.value].bringingAnyone.translations[
        label
      ] &&
      amCustomize.value[pageRenderKey.value].bringingAnyone.translations[label][
        langKey.value
      ]
      ? amCustomize.value[pageRenderKey.value].bringingAnyone.translations[
          label
        ][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

/**
 * Computed properties for "Bringing anyone with you" functionality
 * @type {ComputedRef<*|boolean>}
 */
let options = ref({
  min: 1,
  max: 5,
})
let persons = ref(1)

// * Global colors
let amColors = inject('amColors')

// * Css Variables
let cssVars = computed(() => {
  return {
    '--am-bringing-color-border': useColorTransparency(
      amColors.value.colorMainText,
      0.25
    ),
    '--am-bringing-color-text-opacity60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
  }
})
</script>

<script>
export default {
  name: 'BringingAnyone',
  key: 'bringingAnyone',
}
</script>

<style lang="scss">
@mixin bringing-anyone-block {
  .am-elf {
    &__bringing {
      display: flex;
      flex-direction: row;
      width: 100%;

      &-wrapper {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 188px;
        padding: 24px;
      }

      &-main {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0 0 32px 0;

        &.am-rw-500 {
          flex-wrap: wrap;
        }
      }

      &-heading {
        display: block;
        width: 100%;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.33333;
        color: var(--am-c-main-text);
        margin: 0 8px 4px 0;
        word-break: break-word;
      }

      &-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0;
        margin: 0 0 4px 0;
        border-radius: 8px;

        &.am-rw-500 {
          width: 100%;
        }

        .am-input-number {
          width: 185px;

          &.am-rw-500 {
            width: 100%;
          }
        }
      }
    }
  }
}

// Admin
#amelia-app-backend-new {
  @include bringing-anyone-block;
}
</style>
