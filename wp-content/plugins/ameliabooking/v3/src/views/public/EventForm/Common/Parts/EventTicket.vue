<template>
  <div
    class="am-ct"
    :class="[{ 'am-readonly': props.readonly }, responsiveClass]"
    :style="cssVars"
  >
    <div class="am-ct__info">
      <p class="am-ct__info-name">
        {{ props.ticket.name }}
      </p>
      <p class="am-ct__info-spots">
        <span v-if="componentWidth <= 500" class="am-ct__info-spots__price">
          {{ useFormattedPrice(props.ticket.price) }}
        </span>
        <span v-if="!props.capacity" class="am-ct__info-spots__number">
          {{ isWaitingList ? ticketCalculation() > 0 ? ticketCalculation() : '' : ticketCalculation() }}
        </span>
        <span v-if="!props.capacity" class="am-ct__info-spots__text">
          {{ ticketText() }}
        </span>
      </p>
    </div>
    <div
      class="am-ct__action"
      :class="[{ 'am-readonly': props.readonly }, responsiveClass]"
    >
      <p v-if="componentWidth > 500" class="am-ct__action-price">
        {{ useFormattedPrice(props.ticket.price) }}
      </p>
      <AmInputNumber
        v-if="!props.readonly"
        v-model="spots"
        size="small"
        :min="0"
        :max="selectedEvent.bringingAnyone ? spotsLimitation : 1"
        :disabled="disableSelection"
        @change="updateSpots"
      />
    </div>
  </div>
</template>

<script setup>
import AmInputNumber from '../../../../_components/input-number/AmInputNumber.vue'

// * Import from Vue
import { computed, inject } from 'vue'

// * Import form Vuex
import { useStore } from 'vuex'

// * Composables
import { useFormattedPrice } from '../../../../../assets/js/common/formatting'
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation'
import { useResponsiveClass } from '../../../../../assets/js/common/responsive'

// * Define store
const store = useStore()
/**
 * Component Props
 */
const props = defineProps({
  customizedLabels: {
    type: Object,
    required: true,
  },
  ticket: {
    type: Object,
  },
  capacity: {
    type: Number,
  },
  extraPeople: {
    type: Number,
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  inDialog: {
    type: Boolean,
    default: false,
  },
})

// * Responsive - Container Width
let cWidth = inject('containerWidth')
let dWidth = inject('dialogWidth')

let componentWidth = computed(() => {
  return props.inDialog ? dWidth.value : cWidth.value
})

let responsiveClass = computed(() => useResponsiveClass(componentWidth.value))

let selectedEvent = computed(() =>
  store.getters['eventEntities/getEvent'](
    store.getters['eventBooking/getSelectedEventId']
  )
)

let spots = computed({
  get: () => store.getters['tickets/getTicketNumber'](props.ticket.id),
  set: (val) => {
    let obj = {
      id: props.ticket.id,
      numb: val ? val : 0,
    }
    store.commit('tickets/setTicketNumber', obj)
  },
})

let spotsLimitation = computed(() => {
  if (props.extraPeople) {
    if (props.capacity) {
      return (
        props.extraPeople +
        1 -
        (store.getters['tickets/getEventGlobalSpots'] - spots.value)
      )
    }

    let limitCorrection =
      props.extraPeople +
      1 -
      (store.getters['tickets/getEventGlobalSpots'] - spots.value)
    if (isWaitingList.value) {
      return limitCorrection >=
        props.ticket.waitingListSpots - props.ticket.waiting
        ? props.ticket.waitingListSpots - props.ticket.waiting
        : limitCorrection
    }
    return limitCorrection >= props.ticket.spots - props.ticket.sold
      ? props.ticket.spots - props.ticket.sold
      : limitCorrection
  }

  if (props.capacity)
    return (
      props.capacity -
      (store.getters['tickets/getEventGlobalSpots'] - spots.value)
    )

  return isWaitingList.value
    ? props.ticket.waitingListSpots - props.ticket.waiting
    : props.ticket.spots - props.ticket.sold
})

let disableSelection = computed(() => {
  if (selectedEvent.value.bringingAnyone) {
    return spotsLimitation.value <= 0
  }

  if (!selectedEvent.value.maxCustomCapacity) {
    return (
        store.getters['tickets/getEventGlobalSpots'] !== spots.value ||
        (!isWaitingList.value && props.ticket.spots === props.ticket.sold) ||
        (isWaitingList.value && props.ticket.waiting === props.ticket.waitingListSpots)
    )
  }

  return store.getters['tickets/getEventGlobalSpots'] !== spots.value
})

function updateSpots(newValue, oldValue) {
  if (
    props.capacity ||
    props.extraPeople ||
    !selectedEvent.value.bringingAnyone
  ) {
    store.commit('tickets/setEventGlobalSpots', newValue - oldValue)
  }
}

let isWaitingList = computed(
  () => store.getters['eventWaitingListOptions/getAvailability']
)

function ticketCalculation() {
  if (isWaitingList.value) {
    return props.ticket.waiting + spots.value
  }

  // * No attendees, but there are customers on the waiting list, return 0
  return selectedEvent.value.full
    ? 0
    : props.ticket.spots - props.ticket.sold - spots.value
}

function ticketText() {
  if (isWaitingList.value) {
    if (ticketCalculation() === 0) {
      return ` ${props.customizedLabels.join_waiting_list}`
    } else if (ticketCalculation() === 1) {
      return ` ${props.customizedLabels.person_waiting}`
    } else {
      return ` ${props.customizedLabels.people_waiting}`
    }
  }

  return ticketCalculation() === 1
    ? ` ${props.customizedLabels.event_ticket_left}`
    : ` ${props.customizedLabels.event_tickets_left}`
}

// * Fonts
let amFonts = inject('amFonts')

// * Colors
let amColors = inject('amColors')

// * Css Vars
let cssVars = computed(() => {
  return {
    '--am-c-font-family': amFonts.value.fontFamily,
    '--am-c-ct-primary': amColors.value.colorPrimary,
    '--am-c-ct-bgr': amColors.value.colorMainBgr,
    '--am-c-ct-text': amColors.value.colorMainText,
    '--am-c-ct-text-op90': useColorTransparency(
      amColors.value.colorMainText,
      0.9
    ),
    '--am-c-ct-text-op80': useColorTransparency(
      amColors.value.colorMainText,
      0.8
    ),
    '--am-c-ct-text-op70': useColorTransparency(
      amColors.value.colorMainText,
      0.7
    ),
    '--am-c-ct-text-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
    '--am-c-ct-text-op20': useColorTransparency(
      amColors.value.colorMainText,
      0.2
    ),
  }
})
</script>

<script>
export default {
  name: 'EventTicket',
}
</script>

<style lang="scss">
@mixin card-ticket {
  // am - amelia
  // ct - card ticket
  .am-ct {
    width: 100%;
    display: flex;
    justify-content: space-between;
    border: 1px solid var(--am-c-ct-text-op20);
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 12px;

    &.am-rw-420 {
      flex-direction: column;
    }

    &__info {
      display: flex;
      flex-direction: column;
      justify-content: space-between;

      &-name {
        font-weight: 700;
        font-size: 15px;
        line-height: 1.6;
        color: var(--am-c-ct-text); // $shade-900
        margin: 0;
      }

      &-spots {
        font-weight: 400;
        font-size: 15px;
        line-height: 1.6;
        color: var(--am-c-ct-text-op90); // $shade-800
        margin: 0;

        &__price {
          font-weight: 700;
          font-size: 14px;
          line-height: 20px;
          color: var(--am-c-ct-primary); // $blue-900
          margin-right: 8px;
        }

        &__number {
          font-weight: 700;
        }
      }
    }

    &__action {
      display: flex;
      flex-direction: row;
      align-items: center;
      flex: 0 0 auto;

      &-price {
        font-weight: 700;
        font-size: 14px;
        line-height: 20px;
        color: var(--am-c-ct-primary); // $blue-900
      }

      .am-input-number {
        max-width: 130px;
        margin-left: 8px;
      }

      &.am-readonly {
        align-items: flex-start;
      }

      &.am-rw-420 {
        width: 100%;
        margin-top: 8px;

        .am-input-number {
          max-width: unset;
          width: 100%;
          margin-left: 0;
        }
      }
    }

    /* if it's event info step */
    &.am-readonly {
      border: none;
      border-bottom: 1px solid var(--am-c-ct-text-op20);
      border-radius: 0;
    }
  }
}

// Public
.amelia-v2-booking #amelia-container {
  @include card-ticket;
}

// Admin
#amelia-app-backend-new {
  @include card-ticket;
}
</style>
