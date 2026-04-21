<template>
  <div class="am-elt" :class="props.globalClass" :style="cssVars">
    <div class="am-elt__header">
      <div class="am-elt__header-left">
        <p class="am-elt__heading">
          {{ labelsDisplay('event_tickets') }}
        </p>
        <p>
          {{ labelsDisplay('event_tickets_context') }}
        </p>
      </div>
    </div>
    <template v-for="ticket in tickets" :key="ticket.id">
      <EventTicket
        :ticket="ticket"
        :capacity="null"
        :extra-people="null"
        :customized-labels="customLabels"
        :in-dialog="props.inDialog"
      />
    </template>
  </div>
</template>

<script setup>
// * Parts
import EventTicket from './parts/EventTicket.vue'

// * Import from Vue
import { ref, reactive, computed, inject } from 'vue'

// * Composables
import { useColorTransparency } from '../../../../../../../assets/js/common/colorManipulation'
import { useReactiveCustomize } from '../../../../../../../assets/js/admin/useReactiveCustomize.js'

let props = defineProps({
  globalClass: {
    type: String,
    default: '',
  },
  inDialog: {
    type: Boolean,
    default: false,
  },
})

// * Event Tickets
let tickets = ref([
  {
    id: 1,
    eventTicketId: 1,
    name: 'Ticket one',
    persons: 0,
    price: 10,
    sold: 5,
    spots: 10,
  },
  {
    id: 2,
    eventTicketId: 2,
    name: 'Ticket two',
    persons: 0,
    price: 10,
    sold: 1,
    spots: 1,
  },
  {
    id: 3,
    eventTicketId: 3,
    name: 'Ticket three',
    persons: 0,
    price: 10,
    sold: 0,
    spots: 10,
  },
])

// * Customize
const { amCustomize } = useReactiveCustomize()

// * Form string recognition
let pageRenderKey = inject('pageRenderKey')

// * Labels
let langKey = inject('langKey')
let amLabels = inject('labels')

function labelsDisplay(label) {
  let computedLabel = computed(() => {
    let translations =
      amCustomize.value[pageRenderKey.value].tickets.translations
    return translations &&
      translations[label] &&
      translations[label][langKey.value]
      ? translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Computed labels
let customLabels = computed(() => {
  let computedLabels = reactive({ ...amLabels })

  if (amCustomize.value[pageRenderKey.value].tickets.translations) {
    let customizedLabels =
      amCustomize.value[pageRenderKey.value].tickets.translations
    Object.keys(customizedLabels).forEach((labelKey) => {
      if (customizedLabels[labelKey][langKey.value]) {
        computedLabels[labelKey] = customizedLabels[labelKey][langKey.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
})

// * Fonts
let amFonts = inject('amFonts')

// * Colors
let amColors = inject('amColors')

// * Css Vars
let cssVars = computed(() => {
  return {
    '--am-c-font-family': amFonts.value.fontFamily,
    '--am-c-elt-text': amColors.value.colorMainText,
    '--am-c-elt-text-op90': useColorTransparency(
      amColors.value.colorMainText,
      0.9
    ),
    '--am-c-elt-text-op80': useColorTransparency(
      amColors.value.colorMainText,
      0.8
    ),
  }
})
</script>

<script>
export default {
  name: 'EventTickets',
  label: 'event_select_tickets',
  key: 'tickets',
}
</script>

<style lang="scss">
#amelia-app-backend-new #amelia-container {
  .am-elt {
    & > * {
      font-family: var(--am-font-family), sans-serif;
      box-sizing: border-box;
      word-break: break-word;

      $count: 100;
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

    &__header {
      display: flex;
      flex-direction: column;
      margin: 0 0 16px;

      &-left {
        p {
          font-size: 13px;
          font-weight: 400;
          line-height: 18px;
          word-break: break-word;
          color: var(--am-c-elt-text-op80); /* $shade-700 */
          margin: 0;
        }

        p.am-elt__heading {
          font-size: 15px;
          font-weight: 500;
          line-height: 24px;
          //text-transform: uppercase;
          color: var(--am-c-elt-text); /* $shade-900 */
          margin: 0;
        }
      }

      &-right {
        margin-top: 16px;
        display: flex;
        justify-content: space-between;

        p {
          font-weight: 400;
          font-size: 15px;
          line-height: 1.6;
          color: var(--am-c-elt-text-op90); /* $shade-800 */

          span {
            font-weight: 700;
            margin-right: 4px;
          }
        }
      }
    }
  }
}
</style>
