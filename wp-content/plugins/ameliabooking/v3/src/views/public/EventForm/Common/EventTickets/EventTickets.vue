<template>
  <div
    class="am-elt"
    :class="props.globalClass"
    :style="cssVars"
  >
    <div class="am-elt__header">
      <div  class="am-elt__header-left">
        <p class="am-elt__heading">
          {{ amLabels.event_tickets }}
        </p>
        <p>
          {{ amLabels.event_tickets_context }}
        </p>
      </div>
      <div
        v-if="capacity"
        class="am-elt__header-right"
      >
        <p class="am-elt__heading">
          {{ amLabels.event_ticket_types }}
        </p>
        <p>
          <span>
            {{ capacity - totalSold - ticketGlobalSpots }}
          </span>
          {{ `${(capacity - totalSold - ticketGlobalSpots) === 1 ? amLabels.event_ticket_left : amLabels.event_tickets_left}` }}
        </p>
      </div>
    </div>
    <template
      v-for="ticket in tickets"
      :key="ticket.id"
    >
      <EventTicket
        :ticket="ticket"
        :capacity="capacity ? capacity - totalSold : null"
        :extra-people="extraPeople"
        :customized-labels="amLabels"
        :in-dialog="props.inDialog"
      />
    </template>
  </div>
</template>

<script setup>
// * Parts
import EventTicket from '../../Common/Parts/EventTicket.vue'

// * Import from Vue
import {
  ref,
  reactive,
  computed,
  inject,
  watchEffect,
  onMounted
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Composables
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation.js'
import useAction from '../../../../../assets/js/public/actions'

let props = defineProps({
  globalClass: {
    type: String,
    default: ''
  },
  inDialog: {
    type: Boolean,
    default: false
  }
})

// * Step functionality
let {
  nextStep,
  footerButtonClicked,
  footerBtnDisabled,
  footerButtonReset
} = inject('changingStepsFunctions', {
  nextStep: () => {},
  footerButtonReset: () => {},
  footerButtonClicked: ref(false),
  footerBtnDisabled: ref(false)
})

// * Define store
const store = useStore()

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

  if (customizedDataForm.value.tickets.translations) {
    let customizedLabels = customizedDataForm.value.tickets.translations
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

// * isWaitingList available
let isWaitingList = computed(() => store.getters['eventWaitingListOptions/getAvailability'])

// * Event Tickets
let tickets = computed(() => {
  const sorted = [...(store.getters['tickets/getTicketsData'] || [])].sort((a, b) => a.price - b.price)
  if (window?.ameliaActions?.EventTickets) {
    let override
    useAction(
      store,
      { tickets: sorted, setTickets: arr => { if (Array.isArray(arr)) override = arr } },
      'EventTickets',
      'event',
      null,
      null
    )
    return Array.isArray(override) ? override : sorted
  }
  return sorted
})

// * Event Max Capacity
let capacity = computed(() => store.getters['tickets/getMaxCustomCapacity'])

// * Extra People
let extraPeople = computed(() => store.getters['tickets/getMaxExtraPeople'])

let ticketGlobalSpots = computed(() => store.getters['tickets/getEventGlobalSpots'])
let totalSold = computed(() => {
  let spots = 0
  tickets.value.forEach(t => {
    spots += isWaitingList.value ? t.waiting : t.sold
  })

  return spots
})

let sum = computed(() => store.getters['tickets/getTicketsSum'])

// * Watching when footer button was clicked
watchEffect(() => {
  if (footerButtonClicked.value) {
    footerButtonReset()
    nextStep()
  }

  footerBtnDisabled.value = sum.value < 1
})

onMounted(() => {
  store.commit('setLoading', false)
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
    '--am-c-elt-text-op90': useColorTransparency(amColors.value.colorMainText, 0.9),
    '--am-c-elt-text-op80': useColorTransparency(amColors.value.colorMainText, 0.8),
  }
})
</script>

<script>
export default {
  name: "EventTickets",
  key: 'tickets',
  label: 'event_select_tickets'
}
</script>

<style lang="scss">
.amelia-v2-booking #amelia-container {
  .am-elt {

    & > * {
      font-family: var(--am-font-family);
      box-sizing: border-box;
      word-break: break-word;

      $count: 100;
      @for $i from 0 through $count {
        &:nth-child(#{$i + 1}) {
          animation: 600ms cubic-bezier(.45,1,.4,1.2) #{$i*100}ms am-animation-slide-up;
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
          color: var(--am-c-elt-text-op80);/* $shade-700 */
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
          color: var(--am-c-elt-text-op90);/* $shade-800 */

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
