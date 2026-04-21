<template>
  <div class="am-fs__bringing" :style="cssVars">
    <div class="am-fs__bringing-main">
      <div
        v-if="
          inPopup &&
          amCustomize[pageRenderKey].bringingAnyone.options.heading.visibility
        "
        class="am-fs__bringing-heading"
      >
        {{ labelsDisplay('bringing_anyone_title') }}
      </div>
      <div class="am-fs__bringing-content">
        <span class="am-fs__bringing-content-left">
          <span class="am-icon-users"></span>
          <span class="am-fs__bringing-content-text">{{
            labelsDisplay(
              amSettings.appointments.bringingAnyoneLogic === 'additional'
                ? 'bringing_people'
                : 'bringing_people_total'
            )
          }}</span>
        </span>
        <AmInputNumber
          v-model="persons"
          :min="options.min"
          :max="options.max"
          size="small"
        />
      </div>
      <div
        v-if="amCustomize[pageRenderKey].bringingAnyone.options.info.visibility"
        class="am-fs__bringing-message"
      >
        {{
          labelsDisplay(
            amSettings.appointments.bringingAnyoneLogic === 'additional'
              ? 'add_people'
              : 'add_people_total'
          )
        }}
      </div>
    </div>

    <div
      v-if="
        features.customPricing &&
        amCustomize[pageRenderKey].bringingAnyone.options.bringingPrice
          .visibility
      "
      class="am-fs__bringing-main"
    >
      <div class="am-fs__bringing-content-price">
        <span class="am-fs__bringing-content-price-left">
          <span class="am-icon-service"></span>
          <span class="am-fs__bringing-content-text">
            {{ labelsDisplay('bringing_price') }}
          </span>
        </span>
        <p
          v-for="(item, index) in [
            { from: 1, to: 3, prices: [30, 30] },
            { from: 4, to: 6, prices: [25, 25] },
            { from: 7, to: 9, prices: [20, 20] },
          ]"
          :key="index"
          class="am-fs__bringing-content-text am-fs__bringing-content-price-text"
          :class="{
            'am-fs__bringing-content-price-text-selected': index === 1,
          }"
        >
          <span class="am-icon-users"></span>
          <span>{{ item.from }} - {{ item.to }}</span>
          <span>{{
            item.prices[0] === item.prices[1]
              ? useFormattedPrice(item.prices[0])
              : useFormattedPrice(item.prices[0]) +
                ' - ' +
                useFormattedPrice(item.prices[1])
          }}</span>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import AmInputNumber from '../../../../_components/input-number/AmInputNumber.vue'

import { ref, computed, inject } from 'vue'
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation'
import { useFormattedPrice } from '../../../../../assets/js/common/formatting'
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

const amSettings = inject('settings')

let { inPopup } = inject('inPopup', {
  inPopup: {
    value: true,
  },
})

// * Features
let features = inject('features')

let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

// * Plugin Licence
let licence = inject('licence')

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
  min: amSettings.appointments.bringingAnyoneLogic === 'additional' ? 0 : 1,
  max: 5,
})
let persons = ref(1)

// * Global colors
let amColors = inject('amColors')
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
    '--am-c-ps-primary': amColors.value.colorPrimary,
    '--am-c-ps-primary-op10': useColorTransparency(
      amColors.value.colorPrimary,
      0.1
    ),
  }
})
</script>

<script>
export default {
  name: 'BringingAnyone',
  key: 'bringingAnyone',
  sidebarData: {
    label: 'bringing_anyone',
    icon: 'users-plus',
    stepSelectedData: [],
    finished: false,
    selected: false,
  },
}
</script>

<style lang="scss">
@mixin bringing-anyone-block {
  .am-fs__bringing {
    &-main {
      margin: 0 0 32px 0;
    }

    &-heading {
      display: block;
      width: 100%;
      font-size: 16px;
      font-weight: 500;
      line-height: 1.5;
      color: var(--am-c-main-text);
      margin: 0 0 4px 0;
    }

    &-content-persons {
      display: flex;

      &-text {
        margin: 0 0 0 8px;
      }
    }

    &-content-price {
      display: block;
      background-color: var(--am-c-ps-primary-op10);

      &-left {
        margin-bottom: 5px;

        .am-icon-service {
          margin-right: 5px;
        }
      }

      &-text {
        padding: 3px;
        border: 1px solid var(--am-bringing-color-border);
        border-radius: 8px;
        display: inline-block;
        margin: 3px 0 0 3px;

        span {
          margin: 3px;
        }

        &-selected {
          border-color: var(--am-c-ps-primary);
          background-color: #ffffff;
        }

        span:nth-child(3) {
          color: var(--am-c-ps-primary);
          font-weight: bold;
        }
      }
    }

    &-content {
      display: flex;
    }

    &-content,
    &-content-price {
      align-items: center;
      justify-content: space-between;
      padding: 12px 16px;
      margin: 0 0 4px 0;
      border: 1px solid var(--am-bringing-color-border);
      border-radius: 8px;

      &-left {
        display: flex;
        align-items: center;

        .am-icon-users {
          font-size: 24px;
          line-height: 1;
          color: var(--am-c-main-text);
        }
      }

      &-text {
        font-size: 14px;
        font-weight: 400;
        line-height: 1.43;
        color: var(--am-c-main-text);
      }

      .am-input-number {
        max-width: 100px;
      }
    }

    &-message {
      font-size: 14px;
      font-weight: 400;
      line-height: 1.43;
      color: var(--am-bringing-color-text-opacity60);
    }
  }
}

// Admin
#amelia-app-backend-new {
  @include bringing-anyone-block;
}
</style>
