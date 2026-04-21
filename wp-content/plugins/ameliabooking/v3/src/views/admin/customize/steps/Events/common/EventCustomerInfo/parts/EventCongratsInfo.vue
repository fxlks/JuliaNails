<template>
  <div class="am-congrats__info-title">
    {{ `${labelsDisplay('event_about_list')}:` }}
  </div>
  <div class="am-congrats__info-item" :class="responsiveClass">
    <span class="am-congrats__info-item__label">
      {{ labelsDisplay('event_start') }}:
    </span>
    <span class="am-congrats__info-item__value">
      {{
        `${getFrontedFormattedDate(dateTimeObj.dateStart)} ${labelsDisplay(
          'event_at'
        )} ${getEventFrontedFormattedTime(dateTimeObj.timeStart)}`
      }}
    </span>
  </div>
  <div class="am-congrats__info-item" :class="responsiveClass">
    <span class="am-congrats__info-item__label">
      {{ labelsDisplay('event_end') }}:
    </span>
    <span class="am-congrats__info-item__value">
      {{
        `${getFrontedFormattedDate(dateTimeObj.dateEnd)} ${labelsDisplay(
          'event_at'
        )} ${getEventFrontedFormattedTime(dateTimeObj.timeEnd)}`
      }}
    </span>
  </div>
  <div v-if="licence.isBasic || licence.isPro || licence.isDeveloper" class="am-congrats__info-item" :class="responsiveClass">
    <span class="am-congrats__info-item__label">
      {{ labelsDisplay('event_location') }}:
    </span>
    <span class="am-congrats__info-item__value">
      {{ address }}
    </span>
  </div>
  <div
    v-if="features.tickets"
    class="am-congrats__info-item"
    :class="responsiveClass"
  >
    <span class="am-congrats__info-item__label">
      {{ labelsDisplay('event_tickets') }}:
    </span>
    <span
      class="am-congrats__info-item__value"
      @click="() => (showTickets = !showTickets)"
    >
      <span>{{ `x${ticketNumber} ` }}</span>
      <span class="am-clickable">{{ showLabel }}</span>
    </span>
  </div>

  <div
    v-if="showTickets && features.tickets"
    class="am-congrats__info-item"
  >
    <span class="am-congrats__info-item__value collapsable">
      <span v-for="(ticket, index) in tickets" :key="index">
        {{
          `${ticket.persons} x ${ticket.name}(${useFormattedPrice(
            ticket.price
          )})`
        }}
      </span>
    </span>
  </div>
  <slot name="payment"></slot>
</template>

<script setup>
// * Import from Vue
import { ref, inject, computed } from 'vue'

// * Composables
import {
  getEventFrontedFormattedTime,
  getFrontedFormattedDate,
} from '../../../../../../../../assets/js/common/date'
import { useFormattedPrice } from '../../../../../../../../assets/js/common/formatting'
import { useResponsiveClass } from '../../../../../../../../assets/js/common/responsive'
import { useReactiveCustomize } from '../../../../../../../../assets/js/admin/useReactiveCustomize.js'

// * Import libraries
import moment from 'moment'

// * Plugin Licence
let licence = inject('licence')

// * Features & Integrations
let features = inject('features')

// * Customize Object
const { amCustomize } = useReactiveCustomize()

// * Form string recognition
let pageRenderKey = inject('pageRenderKey')

// * Form step string recognition
let stepName = inject('stepName')

// * Language string recognition
let langKey = inject('langKey')

// * Responsive - Container Width
let cWidth = inject('containerWidth')

let componentWidth = computed(() => {
  return cWidth.value
})

let responsiveClass = computed(() => useResponsiveClass(componentWidth.value))

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

let dateTimeObj = ref({
  dateStart: moment().format('YYYY-MM-DD'),
  timeStart: moment().add(3, 'hours').format('HH:mm:ss'),
  dateEnd: moment().add(2, 'days').format('YYYY-MM-DD'),
  timeEnd: moment().add(4, 'hours').format('HH:mm:ss'),
})

let address = '9306 Taylorsville Road, Louisville KY 40299'

let showTickets = ref(false)

let showLabel = computed(() => {
  return showTickets.value
    ? labelsDisplay('event_show_less')
    : labelsDisplay('event_show_more')
})

// * Event Tickets
let tickets = ref([
  {
    id: 1,
    eventTicketId: 1,
    name: 'Ticket one',
    persons: 3,
    price: 10,
    sold: 0,
    spots: 10,
  },
  {
    id: 2,
    eventTicketId: 2,
    name: 'Ticket two',
    persons: 5,
    price: 15,
    sold: 0,
    spots: 10,
  },
  {
    id: 3,
    eventTicketId: 3,
    name: 'Ticket three',
    persons: 7,
    price: 20,
    sold: 0,
    spots: 10,
  },
])

let ticketNumber = computed(() => {
  let number = 0
  tickets.value.forEach((t) => {
    number += t.persons
  })

  return number
})
</script>

<style lang="scss">
@mixin congrats-info-item-block {
  .am-congrats__info {
    &-title {
      font-size: 18px;
      font-weight: 500;
      line-height: 1.55555;
      color: var(--am-c-congrats-text);
      margin: 16px 0 12px;
    }

    &-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;

      * {
        font-size: 15px;
        line-height: 1.6;
      }

      &.am-rw-480 {
        flex-direction: column;
        align-items: flex-start;
      }

      &__label {
        color: var(--am-c-congrats-text-op40);
      }

      &__value {
        font-weight: 500;
        color: var(--am-c-congrats-text);

        &.collapsable {
          width: 100%;
          display: flex;
          flex-direction: column;
          align-items: flex-end;
          margin-top: -9px;

          span {
            padding: 2px 0;
          }
        }

        .am-clickable {
          color: var(--am-c-congrats-primary);
          cursor: pointer;
        }
      }
    }
  }
}

// Admin
#amelia-app-backend-new #amelia-container {
  @include congrats-info-item-block;
}
</style>
