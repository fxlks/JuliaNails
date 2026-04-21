<template>
  <div :style="cssVars">
    <div class="am-congrats__main">
      <img
        v-if="props.type !== 'event'"
        :src="
          baseUrls.wpAmeliaPluginURL +
          '/v3/src/assets/img/congratulations/congratulations.svg'
        "
      />

      <p v-if="type !== 'event'" class="am-congrats__main-heading">
        {{ labelsDisplay('congratulations') }}
      </p>

      <p v-if="props.type === 'event'" class="am-congrats__main-heading">
        {{ selectedItem.title }}
      </p>

      <span v-if="itemIdLabel">
        {{ itemIdLabel }}
      </span>

      <slot name="positionBelowHeading"></slot>
    </div>

    <CongratsInfo>
      <template #info>
        <component :is="componentTypes[props.type]">
          <template #payment>
            <PaymentCongratsInfo />
          </template>
        </component>
      </template>
      <template #customer>
        <CustomerCongratsInfo />
      </template>
    </CongratsInfo>

    <slot name="positionBottom"></slot>
  </div>
</template>

<script setup>
// * Parts
import CongratsInfo from '../../../../public/Parts/Congratulations/Parts/CongratsInfo.vue'
import PaymentCongratsInfo from '../parts/PaymentCongratsInfo.vue'
import CustomerCongratsInfo from '../parts/CustomerCongratsInfo.vue'
import EventCongratsInfo from '../Events/common/EventCustomerInfo/parts/EventCongratsInfo.vue'

// * Import from Vue
import { ref, computed, inject, markRaw } from 'vue'

// * Composables
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation'
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

// * Component Properties
let props = defineProps({
  type: {
    type: String,
    required: true,
  },
  selectedItem: {
    type: Object,
    required: true,
  },
})

// * Base Urls
const baseUrls = inject('baseUrls')

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

// * Template heading string
let itemIdLabel = computed(() => {
  if (props.type === 'appointment') {
    return `${labelsDisplay('appointment_id')} #${props.selectedItem.id}`
  }

  if (props.type === 'event') {
    return `${labelsDisplay('event_id')} #${props.selectedItem.id}`
  }

  return ''
})

let componentTypes = ref({
  event: markRaw(EventCongratsInfo),
})

// * Fonts
let amFonts = inject('amFonts')

// * Colors
let amColors = inject('amColors')

// * Css Vars
let cssVars = computed(() => {
  return {
    '--am-font-family': amFonts.value.fontFamily,
    '--am-c-congrats-heading-text': amColors.value.colorMainHeadingText,
    '--am-c-congrats-heading-text-op40': useColorTransparency(
      amColors.value.colorMainHeadingText,
      0.4
    ),
    '--am-c-congrats-text': amColors.value.colorMainText,
    '--am-c-congrats-text-op30': useColorTransparency(
      amColors.value.colorMainText,
      0.3
    ),
    '--am-c-congrats-text-op40': useColorTransparency(
      amColors.value.colorMainText,
      0.4
    ),
    '--am-c-congrats-primary': amColors.value.colorPrimary,
  }
})
</script>

<style lang="scss">
#amelia-app-backend-new #amelia-container {
  .am-congrats {
    &__main {
      display: flex;
      margin-top: 22px;
      flex-direction: column;
      align-items: center;
      font-family: var(--am-font-family);
      margin-bottom: 16px;

      & > * {
        $count: 5;
        @for $i from 0 through $count {
          &:nth-child(#{$i + 1}) {
            animation: 600ms
              cubic-bezier(0.45, 1, 0.4, 1.2)
              #{$i *
              100}ms
              am-animation-slide-up;
            animation-fill-mode: both;
          }
        }
      }

      & img {
        width: 54px;
        margin-bottom: 8px;
      }

      &-heading {
        margin-bottom: 2px;
        font-weight: 500;
        font-size: 18px;
        line-height: 28px;
        color: var(--am-c-congrats-heading-text);
      }

      &-atc {
        margin-top: 16px;
      }

      & span {
        color: var(--am-c-congrats-heading-text-op40);
        font-weight: 400;
        font-size: 14px;
        line-height: 1.42857;
      }
    }
  }
}
</style>
