<template>
  <div class="am-congrats__info-title">
    {{ `${props.labels.event_about_list}:` }}
  </div>
  <div
    class="am-congrats__info-item am-fs__congrats-info-event-date-start"
    :class="responsiveClass"
  >
    <span class="am-congrats__info-item__label">
      {{props.labels.event_start}}:
    </span>
    <span class="am-congrats__info-item__value">
      {{ `${getFrontedFormattedDate(dateStart)} ${props.labels.event_at} ${getEventFrontedFormattedTime(timeStart)}` }}
    </span>
  </div>
  <div
    class="am-congrats__info-item am-fs__congrats-info-event-date-end"
    :class="responsiveClass"
  >
    <span class="am-congrats__info-item__label">
      {{props.labels.event_end}}:
    </span>
    <span class="am-congrats__info-item__value">
      {{ `${getFrontedFormattedDate(dateEnd)} ${props.labels.event_at} ${getEventFrontedFormattedTime(timeEnd)}` }}
    </span>
  </div>
  <div
    v-if="props.booked.address"
    class="am-congrats__info-item am-fs__congrats-info-event-location"
    :class="responsiveClass"
  >
    <span class="am-congrats__info-item__label">
      {{ props.labels.event_location }}:
    </span>
    <span class="am-congrats__info-item__value">
      {{ props.booked.address }}
    </span>
  </div>
  <div
    v-if="props.booked.customPricing"
    class="am-congrats__info-item am-fs__congrats-info-event-tickets"
    :class="responsiveClass"
  >
    <span class="am-congrats__info-item__label">
      {{props.labels.event_tickets}}:
    </span>
    <span
      class="am-congrats__info-item__value"
      @click="showTickets"
    >
      <span>{{ `x${ticketNumber} ` }}</span>
      <span class="am-clickable">{{ showLabel }}</span>
    </span>
  </div>

  <div
    v-if="ticketVisible"
    class="am-congrats__info-item am-fs__congrats-info-event-tickets"
  >
    <span class="am-congrats__info-item__value collapsable">
      <span
        v-for="(ticket, index) in props.booked.ticketsData"
        :key="index"
      >
        {{ `${ticket.persons} x ${ticket.name}(${useFormattedPrice(ticket.price)})` }}
      </span>
    </span>
  </div>
  <slot name="payment"></slot>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  computed,
  inject
} from 'vue'

// * Composables
import {
  getFrontedFormattedDate,
  getEventFrontedFormattedTime
} from '../../../../../assets/js/common/date'
import { useFormattedPrice } from "../../../../../assets/js/common/formatting";
import { useResponsiveClass } from "../../../../../assets/js/common/responsive";

let props = defineProps({
  booked: {
    type: Object,
    required: true
  },
  labels: {
    type: Object,
    required: true
  },
  inDialog: {
    type: Boolean,
    default: false
  }
})

let dateStart = computed(() => props.booked ? props.booked.data[0].start.split(' ')[0] : '')
let timeStart = computed(() => props.booked ? props.booked.data[0].start.split(' ')[1] : '')

let dateEnd = computed(() => props.booked ? props.booked.data[props.booked.data.length - 1].end.split(' ')[0] : '')
let timeEnd = computed(() => props.booked ? props.booked.data[props.booked.data.length - 1].end.split(' ')[1] : '')

let ticketNumber = computed(() => {
  let number = 0
  props.booked.ticketsData.forEach(t => {
    number += t.persons
  })

  return number
})

let showLabel = ref(props.labels.event_show_more)
let ticketVisible = ref(false)

function showTickets () {
  ticketVisible.value = !ticketVisible.value
  showLabel.value = ticketVisible.value ? props.labels.event_show_less : props.labels.event_show_more
}

// * Responsive - Container Width
let cWidth = inject('containerWidth')
let dWidth = inject('dialogWidth')

let componentWidth = computed(() => {
  return props.inDialog ? dWidth.value : cWidth.value
})

let responsiveClass = computed(() => useResponsiveClass(componentWidth.value))
</script>

<script>
export default {
  name: "EventCongratsInfo"
}
</script>

<style lang="scss">
@mixin congrats-info-item-block {
  .am-congrats__info {
    &-title {
      font-size: 18px;
      font-weight: 500;
      line-height: 1.55555;
      color: var(--am-c-atc-text);
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
        color: var(--am-c-atc-text-op40);
      }

      &__value {
        font-weight: 500;
        color: var(--am-c-atc-text);

        &.collapsable {
          width: 100%;
          display: flex;
          flex-direction: column;
          align-items: flex-end;
          margin-top: -9px;

          span {
            padding: 2px 0px;
          }
        }

        .am-clickable {
          color: var(--am-c-primary);
          cursor: pointer;
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include congrats-info-item-block;
}
</style>