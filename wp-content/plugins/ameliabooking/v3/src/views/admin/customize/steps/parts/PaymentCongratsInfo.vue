<template>
  <div class="am-congrats__info-item" :class="responsiveClass">
    <span class="am-congrats__info-item__label">
      {{ labelsDisplay('congrats_payment') }}
    </span>
    <span class="am-congrats__info-item__value">
      {{ useFormattedPrice(245) }}
    </span>
  </div>
</template>

<script setup>
// * Import from Vue
import { inject, computed } from 'vue'

// * Composable
import { useFormattedPrice } from '../../../../../assets/js/common/formatting'
import { useResponsiveClass } from '../../../../../assets/js/common/responsive'
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

// * Customize Object
const { amCustomize } = useReactiveCustomize()

// * Form string recognition
let pageRenderKey = inject('pageRenderKey')

// * Form step string recognition
let stepName = inject('stepName')

// * Language string recognition
let langKey = inject('langKey')

// * Labels
let amLabels = inject('labels')

function labelsDisplay(label) {
  let computedLabel = computed(() => {
    let translations =
      amCustomize.value[pageRenderKey.value][stepName.value].translations
    return translations &&
      translations[label] &&
      translations[label][langKey.value]
      ? translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Responsive - Container Width
let cWidth = inject('containerWidth')

let componentWidth = computed(() => {
  return cWidth.value
})

let responsiveClass = computed(() => useResponsiveClass(componentWidth.value))
</script>

<style lang="scss">
@mixin congrats-info-item-block {
  .am-congrats__info-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;

    * {
      font-size: 14px;
      line-height: 1.42857;
    }

    &.am-rw-480 {
      flex-direction: column;
      align-items: flex-start;
    }

    &__label {
      color: var(--am-c-atc-text-op40);
    }

    &__value {
      color: var(--am-c-atc-text);
    }
  }
}

#amelia-app-backend-new #amelia-container {
  @include congrats-info-item-block;
}
</style>
