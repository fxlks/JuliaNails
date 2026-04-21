<template>
  <div
    :class="`am-atc-${generatedClass}`"
    :style="cssVars"
  >
    <p>
      {{ calendarString }}
    </p>
    <div :class="`am-atc-${generatedClass}-cals`">
      <div
        v-for="cal in calendars"
        :key="cal.type"
        :class="`am-atc-${generatedClass}-cals-cards`"
      >
        <a
          :style="{borderColor : 'var(--am-c-atc-text-op10)'}"
          :class="`am-atc-${generatedClass}-cals-card`"
        >
          <div>
            <span :class="`am-icon-${cal.type}`"></span>
          </div>
          <p :style="{color : 'var(--am-c-atc-text)'}">
            {{ cal.label }}
          </p>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  computed,
  ref,
  inject
} from 'vue'

// * Composables
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation.js'

// * Component Properties
defineProps({
  calendarString: {
    type: String,
    required: true
  }
})

let calendars = ref([
  {
    type: 'google',
    label: 'Google'
  },
  {
    type: 'outlook',
    label: 'Outlook'
  },
  {
    type: 'yahoo',
    label: 'Yahoo'
  },
  {
    type: 'apple',
    label: 'Apple'
  }
])

// * Bookable type
let bookableType = inject('bookableType')

let generatedClass = computed(() => {
  if (bookableType.value !== 'event') {
    return 'sbs'
  }

  return 'event'
})

// * Colors
let amColors = inject('amColors')

// * Css Variables
const cssVars = computed(() => {
  if (bookableType.value === 'event') {
    return {
      '--am-c-atc-text': amColors.value.colorMainText,
      '--am-c-atc-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
      '--am-c-atc-text-op5': useColorTransparency(amColors.value.colorMainText, 0.05)
    }
  }

  return {
    '--am-c-atc-text': amColors.value.colorSbText,
    '--am-c-atc-text-op10': useColorTransparency(amColors.value.colorSbText, 0.1),
    '--am-c-atc-text-op5': useColorTransparency(amColors.value.colorSbText, 0.05)
  }
})
</script>

<script>
export default {
  name: 'AddToCalendar'
}
</script>

<style lang="scss">
#amelia-app-backend-new #amelia-container {
  // atc - add to calendar
  .am-atc {
    // sbs - step by step
    &-sbs {
      --am-c-atc-text: var(--am-c-sb-text);

      & > p {
        text-align: center;
        font-size: 18px;
        line-height: 20px;
        color: var(--am-c-atc-text);
        font-weight: 400;
        margin-bottom: 16px;
      }

      &-cals {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;

        &-cards {
          display: flex;
          flex-direction: row;
          align-items: center;
          justify-content: center;
          margin-bottom: 8px;
        }

        &-card {
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
          width: 86px;
          text-decoration: none;
          color: var(--am-c-atc-text);
          border: 1px solid;
          border-radius: 4px;
          background-color: var(--am-c-atc-text-op50);
          padding: 16px 0 8px;
          margin-right: 8px;
          box-sizing: border-box;
          box-shadow: 0 1px 1px rgba(115, 134, 169, 0.06);
          cursor: pointer;

          p {
            font-style: normal;
            font-weight: 500;
            font-size: 15px;
            line-height: 24px;
            margin: 0;
            padding: 0;
          }

          div {
            display: flex;
            height: 24px;
            align-items: center;

            span {
              font-size: 24px;
              color: var(--am-c-atc-text);
            }

            .am-icon-yahoo {
              font-size: 17px
            }
          }

          &:hover {
            background-color: var(--am-c-atc-text-op10);
          }
        }
      }
    }

    // - event
    &-event {
      --am-c-atc-text: var(--am-c-sb-text);

      & > p {
        text-align: center;
        font-size: 18px;
        line-height: 1.55555;
        font-weight: 500;
        color: var(--am-c-atc-text);
        margin-bottom: 16px;
      }

      &-cals {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;

        &-cards {
          display: flex;
          flex-direction: row;
          align-items: center;
          justify-content: center;
          margin-bottom: 8px;
        }

        &-card {
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
          width: 86px;
          text-decoration: none;
          color: var(--am-c-atc-text);
          border: 1px solid;
          border-radius: 4px;
          background-color: var(--am-c-atc-text-op50);
          padding: 16px 0 8px;
          margin-right: 8px;
          box-sizing: border-box;
          box-shadow: 0 1px 1px rgba(115, 134, 169, 0.06);
          cursor: pointer;

          p {
            font-style: normal;
            font-weight: 500;
            font-size: 15px;
            line-height: 24px;
            margin: 0;
            padding: 0;
          }

          div {
            display: flex;
            height: 24px;
            align-items: center;

            span {
              font-size: 24px;
              color: var(--am-c-atc-text);
            }

            .am-icon-yahoo {
              font-size: 17px
            }
          }

          &:hover {
            background-color: var(--am-c-atc-text-op10);
          }
        }
      }
    }
  }
}
</style>