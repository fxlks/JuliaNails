<template>
  <div
    class="am-ct"
    :class="[{'am-readonly' : props.readonly}, responsiveClass]"
    :style="cssVars"
  >
    <div class="am-ct__info">
      <p class="am-ct__info-name">
        {{ props.ticket.name }}
      </p>
      <p
        v-if="!props.capacity && (props.ticket.spots - props.ticket.sold - spots > 0 || !props.readonly)"
        class="am-ct__info-spots"
      >
        <span
          v-if="componentWidth <= 500"
          class="am-ct__info-spots__price"
        >
          {{ useFormattedPrice(props.ticket.price) }}
        </span>
        <span class="am-ct__info-spots__number">
          {{ isWaiting ? ticketCalculation() > 0 ? ticketCalculation() : '' : ticketCalculation() }}
        </span>
        <span class="am-ct__info-spots__text">
          {{ ticketText() }}
        </span>
      </p>
    </div>
    <div
      class="am-ct__action"
      :class="[{'am-readonly' : props.readonly}, responsiveClass]"
    >
      <p
        v-if="componentWidth > 500"
        class="am-ct__action-price"
      >
        {{ useFormattedPrice(props.ticket.price) }}
      </p>
      <AmInputNumber
        v-if="!props.readonly"
        v-model="spots"
        size="small"
        :min="0"
        :max="10"
        @change="updateSpots"
      />
    </div>
  </div>
</template>

<script setup>
// * _components
import AmInputNumber from "../../../../../../../_components/input-number/AmInputNumber.vue";

// * Import from Vue
import {
  ref,
  computed,
  inject
} from "vue";

// * Composables
import { useFormattedPrice } from "../../../../../../../../assets/js/common/formatting";
import { useResponsiveClass } from "../../../../../../../../assets/js/common/responsive";
import { useColorTransparency } from "../../../../../../../../assets/js/common/colorManipulation";

/**
 * Component Props
 */
const props = defineProps({
  customizedLabels: {
    type: Object,
    required: true
  },
  ticket: {
    type: Object
  },
  capacity: {
    type: Number
  },
  extraPeople: {
    type: Number
  },
  readonly: {
    type: Boolean,
    default: false
  },
  inDialog: {
    type: Boolean,
    default: false
  }
})

// * Root Settings
let amSettings = inject('settings')

// * Licence
let licence = inject('licence')

// * Features
let features = inject('features')

let isWaiting = computed(() => {
  return features.value.waitingList
})

let spots = ref(0)

function updateSpots () {}

function ticketCalculation() {
  if (isWaiting.value) {
    return props.ticket.sold + spots.value
  }

  return props.ticket.spots - props.ticket.sold - spots.value
}

function ticketText() {
  if (isWaiting.value) {
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

// * Responsive - Container Width
let cWidth = inject('containerWidth')

let componentWidth = computed(() => {
  return cWidth.value
})

let responsiveClass = computed(() => useResponsiveClass(componentWidth.value))

// * Fonts
let amFonts = inject('amFonts')

// * Colors
let amColors = inject('amColors')

// * Css Vars
let cssVars = computed(() => {
  return {
    '--am-font-family': amFonts.value.fontFamily,
    '--am-c-ct-primary': amColors.value.colorPrimary,
    '--am-c-ct-bgr': amColors.value.colorMainBgr,
    '--am-c-ct-text': amColors.value.colorMainText,
    '--am-c-ct-text-op90': useColorTransparency(amColors.value.colorMainText, 0.9),
    '--am-c-ct-text-op80': useColorTransparency(amColors.value.colorMainText, 0.8),
    '--am-c-ct-text-op70': useColorTransparency(amColors.value.colorMainText, 0.7),
    '--am-c-ct-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-c-ct-text-op20': useColorTransparency(amColors.value.colorMainText, 0.2),
  }
})
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
        margin: 0 0 8px;
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

// Admin
#amelia-app-backend-new {
  @include card-ticket;
}
</style>