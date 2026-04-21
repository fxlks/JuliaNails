<template>
  <div class="am-elf__bringing" :style="cssVars">
    <div
      class="am-elf__bringing-main"
      :class="responsiveClass"
    >
      <div
        v-if="true"
        class="am-elf__bringing-heading"
      >
        {{ amLabels.event_bringing }}
      </div>
      <div
        class="am-elf__bringing-content"
        :class="responsiveClass"
      >
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
// * Import from Vue
import {
  reactive,
  computed,
  inject
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * _components
import AmInputNumber from '../../../../_components/input-number/AmInputNumber.vue'

// * Composables
import { useColorTransparency } from "../../../../../assets/js/common/colorManipulation";
import { useResponsiveClass } from "../../../../../assets/js/common/responsive";

let store = useStore()

// * Options
let options = computed(() => {
  return {
    min: store.getters['persons/getMinPersons'],
    max:  store.getters['persons/getMaxPersons']
  }
})

let persons = computed({
  get: () => store.getters['persons/getPersons'],
  set: (val) => {
    store.commit('persons/setPersons', val)
  }
})

// * Responsive - Container Width
let cWidth = inject('containerWidth')

let responsiveClass = computed(() => {
  return useResponsiveClass(cWidth.value)
})

// * Root Settings
const amSettings = inject('settings')

// * Event form customization
let customizedDataForm = inject('customizedDataForm')

// * Labels
let labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  if (customizedDataForm.value.bringingAnyone.translations) {
    let customizedLabels = customizedDataForm.value.bringingAnyone.translations
    Object.keys(customizedLabels).forEach(labelKey => {
      if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
        computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
})

// * Fonts
let amFonts = inject('amFonts')

// * Global colors
let amColors = inject('amColors');

// * Css Vars
let cssVars = computed(() => {
  return {
    '--am-font-family': amFonts.value.fontFamily,
    '--am-bringing-color-border': useColorTransparency(amColors.value.colorMainText, 0.25),
    '--am-bringing-color-text-opacity60': useColorTransparency(amColors.value.colorMainText, 0.6),
  }
})
</script>

<script>
export default {
  name: 'BringingAnyone'
}
</script>

<style lang="scss">
@mixin bringing-anyone-block {
  .am-elf {
    &__bringing {
      display: flex;
      flex-direction: row;
      width: 100%;

      * {
        box-sizing: border-box;
        font-family: var(--am-font-family), sans-serif;
      }

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

// Front
.amelia-v2-booking #amelia-container {
  @include bringing-anyone-block;
}
</style>
