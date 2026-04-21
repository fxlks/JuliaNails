<template>
  <div
    class="am-ec"
    :class="[{'am-no-border': !props.borderVisibility}, responsiveClass]"
    :style="cssVars"
  >
    <!-- Period -->
    <div
      class="am-ec__period"
      :class="responsiveClass"
    >
      <p
        v-if="multipleDaysEvent(props.event.periods) && (eventStatus === 'open' || eventStatus === 'upcoming')"
        class="am-ec__period-text"
        :class="responsiveClass"
      >
        {{ props.labels.event_begins }}
      </p>
      <p
        class="am-ec__period-date"
        :class="responsiveClass"
      >
        <span
          class="am-ec__period-date__day"
          :class="[{'am-ghost': (eventStatus === 'canceled' || eventStatus === 'closed')}, responsiveClass]"
        >
          {{ getEventFrontedFormattedDateDay(props.event.periods[0].periodStart.split(' ')[0]) }}
        </span>
        <span
          class="am-ec__period-date__month"
          :class="[{'am-ghost': (eventStatus === 'canceled' || eventStatus === 'closed')}, responsiveClass]"
        >
          {{ monthsTranslationsShort(localLanguage)[moment(props.event.periods[0].periodStart.split(' ')[0]).format('M')] }}
        </span>
      </p>
      <p
        class="am-ec__period-time"
        :class="[{'am-ghost': (eventStatus === 'canceled' || eventStatus === 'closed')}, responsiveClass]"
      >
        {{ getEventFrontedFormattedTime(props.event.periods[0].periodStart.split(' ')[1]) }}
      </p>
    </div>

    <!-- Image -->
    <div
      v-if="(props.event.gallery.length || props.event.pictureFullPath) && props.imageVisibility && props.customizedOptions.imgTab.visibility"
      class="am-ec__image"
      :class="responsiveClass"
      :style="{backgroundImage: `url(${props.event.pictureFullPath ? props.event.pictureFullPath : props.event.gallery[0].pictureFullPath})`}"
    ></div>

    <!-- Info -->
    <div class="am-ec__info">
      <p
        class="am-ec__info-name"
        :class="{'am-ghost': (eventStatus === 'canceled' || eventStatus === 'closed')}"
      >
        {{props.event.name}}
      </p>
      <p
        v-if="inHeader && componentWidth <= 500 && props.customizedOptions.price.visibility"
        class="am-ec__info-price"
      >
        {{ eventCardPrice }}
        <template v-if="useCheckIfEventNotFree(props.event) && props.taxVisible">
          <template v-if="amSettings.payments.taxes.excluded">
            {{ `+${props.labels.total_tax_colon}` }}
          </template>
          <template v-else>
            {{props.labels.incl_tax}}
          </template>
        </template>
      </p>
      <p
        v-if="props.customizedOptions.location.visibility && useEventLocation(props.event, locations)"
        class="am-ec__info-location"
        :class="{'am-ghost': (eventStatus === 'canceled' || eventStatus === 'closed')}"
      >
        {{ useEventLocation(props.event, locations) }}
      </p>
      <div class="am-ec__info-other">
        <p
          v-if="props.customizedOptions.status.visibility"
          :class="`am-ec__info-availability ${eventStatus}`"
        >
          {{ props.labels[eventStatus] }}
        </p>
        <p
          v-if="props.customizedOptions.slots.visibility && useWaitingListAvailability(props.event) && useWaitingListOccupancy(props.event) > 0"
          class="am-ec__info-waiting-list"
        >
          {{ useWaitingListOccupancy(props.event) }}
          {{ useWaitingListOccupancy(props.event) === 1 ? props.labels.person_waiting : props.labels.people_waiting }}
        </p>
        <p
          v-if="props.customizedOptions.slots.visibility && showEventCapacity(eventStatus)"
          class="am-ec__info-capacity"
        >
          <span class="am-ec__info-capacity__number">
            {{ eventPlaces }}
          </span>
          <span class="am-ec__info-capacity__text">
            {{ eventPlaces === 1 ? ` ${props.labels.event_slot_left}` : ` ${props.labels.event_slots_left}` }}
          </span>
        </p>
      </div>
    </div>

    <!-- Price and Action Button -->
    <div
      v-if="!inHeader || componentWidth > 500"
      class="am-ec__actions"
      :class="[{'am-vertical-center': !props.customizedOptions.price.visibility}, responsiveClass]"
    >
      <div
        v-if="props.customizedOptions.price.visibility"
        class="am-ec__actions-price"
        :class="responsiveClass"
      >
        <p>
          {{ eventCardPrice }}
        </p>
        <p
          v-if="useCheckIfEventNotFree(props.event) && props.taxVisible"
          class="am-tax"
        >
          <template v-if="amSettings.payments.taxes.excluded">
            {{ `+${props.labels.total_tax_colon}` }}
          </template>
          <template v-else>
            {{props.labels.incl_tax}}
          </template>
        </p>
      </div>
      <p
       v-if="props.btnVisibility"
        class="am-ec__actions-btn"
      >
        <AmButton
          :size="componentWidth > 500 ? 'small' : 'medium'"
          :type="bookingBtnType"
          :category="useWaitingListAvailability(props.event) ? 'waiting' : 'primary'"
          @click="selectEvent(props.event.id)"
        >
          {{ useWaitingListAvailability(props.event) ? props.labels.join_waiting_list : eventStatus !== 'open' ? props.labels.event_learn_more : props.labels.event_read_more }}
        </AmButton>
      </p>
    </div>
  </div>
</template>

<script setup>
// * _components
import AmButton from "../../../../_components/button/AmButton.vue";

// * Import from Vue
import {
  computed,
  inject
} from "vue";

// * Libraries
import moment from "moment";

// * Composables
import {
  useEventLocation,
  useMinTicketPrice,
  showEventCapacity,
  useEventStatus,
  useCheckIfEventNotFree,
  useWaitingListAvailability,
  useWaitingListOccupancy
} from "../../../../../assets/js/public/events.js";
import {
  getEventFrontedFormattedTime,
  getEventFrontedFormattedDateDay
} from "../../../../../assets/js/common/date.js";
import {
  useFormattedPrice
} from "../../../../../assets/js/common/formatting";
import { useColorTransparency } from "../../../../../assets/js/common/colorManipulation";
import { useResponsiveClass } from "../../../../../assets/js/common/responsive";
import { monthsTranslationsShort } from "../../../../../assets/js/common/translationsElementPlus";

// * Component Properties
let props = defineProps({
  event: {
    type: [Object, Function, Array],
    required: true
  },
  labels: {
    type: Object,
    required: true
  },
  customizedOptions: {
    type: Object,
    default: () => {
      return {
        bookingBtn: {
          buttonType: 'filled'
        },
        infoBtn: {
          buttonType: 'plain'
        },
        imgTab: {
          visibility: true
        },
        location: {
          visibility: true
        },
        price: {
          visibility: true
        },
        slots: {
          visibility: true
        },
        status: {
          visibility: true
        }
      }
    }
  },
  tickets: {
    type: Array,
    default: () => []
  },
  locations: {
    type: Array,
    required: true
  },
  borderVisibility: {
    type: Boolean,
    default: true
  },
  btnVisibility: {
    type: Boolean,
    default: true
  },
  autoInfoHeight: {
    type: Boolean,
    default: false
  },
  imageVisibility: {
    type: Boolean,
    default: true
  },
  verticalOrientation: {
    type: String,
    default: 'center'
  },
  inHeader: {
    type: Boolean,
    default: false
  },
  inDialog: {
    type: Boolean,
    default: false
  },
  taxVisible: {
    type: Boolean,
    default: false
  }
})

// * Component Emits
const emits = defineEmits(['click'])

// * Root Settings
const amSettings = inject('settings')

// * Locale language long
const localLanguage = inject('localLanguage')

// * Container width
let cWidth = inject('containerWidth')
let dWidth = inject('dialogWidth')

let componentWidth = computed(() => {
  return props.inDialog ? dWidth.value : cWidth.value
})

// * Responsive
let responsiveClass = computed(() => useResponsiveClass(componentWidth.value))

// * Event Status
let eventStatus = computed(() => {
  return useEventStatus(props.event)
})



let bookingBtnType = computed(() => {
  if (useWaitingListAvailability(props.event)) {
    if ('waitingBtn' in props.customizedOptions) {
      return props.customizedOptions.waitingBtn.buttonType
    }

    return 'filled'
  }

  if (eventStatus.value !== 'open') {
    return props.customizedOptions.infoBtn.buttonType
  }

  return props.customizedOptions.bookingBtn.buttonType
})

let eventPlaces = computed(() => {
  if (props.event.customPricing) {
    let places = 0
    let eventTickets = props.tickets.length ? props.tickets : props.event.customTickets

    if (props.event.maxCustomCapacity) {
      let sold = 0
      eventTickets.forEach(t => {
        if ('enabled' in t ? t.enabled : true) {
          sold += t.sold
        }
      })

      places = props.event.maxCustomCapacity - sold
    }
    else {
      eventTickets.forEach(t => {
        if ('enabled' in t ? t.enabled : true) {
          places += (t.spots - t.sold) - ('persons' in t ? t.persons : 0)
        }
      })
    }

    return places
  }

  return props.event.places
})

let eventCardPrice = computed(() => {
  if (props.event.customPricing) {
    if (useCheckIfEventNotFree(props.event)) {
      return `${props.labels.from} ${useFormattedPrice(useMinTicketPrice(props.event))}`
    } else {
      return props.labels.event_free
    }
  }

  return useCheckIfEventNotFree(props.event) ? useFormattedPrice(props.event.price) : props.labels.event_free
})

function selectEvent (id) {
  emits('click', id)
}

function multipleDaysEvent (arr) {
  let testArr = []
  arr.forEach(period => {
    testArr.push(period.periodStart.split(' ')[0])
    testArr.push(period.periodEnd.split(' ')[0])
  })

  return !testArr.every((val) => val === testArr[0])
}

// * Colors
let amColors = inject('amColors')

let cardBorderOrientation = computed(() => {
  if (componentWidth.value <= 500) {
    return `0px 2px 2px -1px ${amColors.value.colorCardBorder}, 0px 0px 11px ${useColorTransparency(amColors.value.colorCardBorder, 0.3)}, inset 0px -8px 0px ${props.event.color}`
  }

  return `0px 2px 2px -1px ${amColors.value.colorCardBorder}, 0px 0px 11px ${useColorTransparency(amColors.value.colorCardBorder, 0.3)}, inset 8px 0px 0px ${props.event.color}`
})

// * Css Vars
let cssVars = computed(() => {
  let obj = {
    boxShadow: cardBorderOrientation.value,
    '--am-c-ec-success': amColors.value.colorSuccess,
    '--am-c-ec-primary': amColors.value.colorPrimary,
    '--am-c-ec-warning': amColors.value.colorWarning,
    '--am-c-ec-bgr': props.inHeader ? amColors.value.colorMainBgr : amColors.value.colorCardBgr,
    '--am-c-ec-text': props.inHeader ? amColors.value.colorMainText : amColors.value.colorCardText,
    '--am-c-ec-text-op90': useColorTransparency(props.inHeader ? amColors.value.colorMainText : amColors.value.colorCardText, 0.9),
    '--am-c-ec-text-op80': useColorTransparency(props.inHeader ? amColors.value.colorMainText : amColors.value.colorCardText, 0.8),
    '--am-c-ec-text-op50': useColorTransparency(props.inHeader ? amColors.value.colorMainText : amColors.value.colorCardText, 0.5),
    '--am-c-ec-error-op70': useColorTransparency(amColors.value.colorError, 0.7),
    // ? Height of card content
    '--am-h-ec-info': props.autoInfoHeight ? 'unset' : '84px',
    '--am-vo-ec': props.verticalOrientation,
    '--am-fs-ec-title': props.inHeader ? '18px' : '16px'
  }

  if (!props.borderVisibility) delete obj.boxShadow

  return obj
})
</script>

<script>
export default {
  name: "EventCard"
}
</script>

<style lang="scss">
.amelia-v2-booking #amelia-container {
  // ec - event card
  .am-ec {
    display: flex;
    flex-direction: row;
    align-items: var(--am-vo-ec);
    width: 100%;
    padding: 12px 16px 12px 24px;
    background: var(--am-c-ec-bgr);
    border-radius: 8px;
    margin-bottom: 12px;

    &.am-no-border {
      padding: 0;
    }

    &.am-rw-500 {
      --am-vo-ec: flex-start !important;
      flex-direction: column;
    }

    &__period {
      margin-right: 26px;

      &.am-rw-500 {
        display: flex;
        align-items: baseline;
        margin: 0 0 8px;
      }

      &-text {
        font-size: 12px;
        font-weight: 400;
        line-height: 1.5;
        padding: 0;
        margin: 0;
        color: var(--am-c-ec-text-op50);

        &.am-rw {
          &-500 {
            font-size: 14px;
            margin: 0 4px 0 0;
          }
        }
      }

      &-date {
        padding: 0;
        margin: 0;

        &.am-rw-500 {
          display: flex;
          align-items: baseline;
          margin-right: 8px;
        }

        * {
          color: var(--am-c-ec-text); // $shade-1000
        }

        &__day {
          font-size: 20px;
          font-weight: 500;
          line-height: 1;
          margin-right: 2px;

          &.am-ghost {
            color: var(--am-c-ec-text-op50); // $shade-500
          }

          &.am-rw-500 {
            font-size: 18px;
          }
        }

        &__month {
          font-size: 13px;
          font-weight: 400;
          line-height: 1.23077;

          &.am-ghost {
            color: var(--am-c-ec-text-op50); // $shade-500
          }

          &.am-rw-500 {
            font-size: 14px;
          }
        }
      }

      &-time {
        font-size: 12px;
        font-weight: 400;
        line-height: 1.5;
        color: var(--am-c-ec-text-op80); // $shade-800
        padding: 0;
        margin: 0;

        &.am-ghost {
          color: var(--am-c-ec-text-op50); // $shade-500
        }

        &.am-rw-500 {
          font-size: 14px;
        }
      }
    }

    &__image {
      width: 88px;
      height: 88px;
      border-radius: 4px;
      overflow: hidden;
      margin: 0 16px 0 0;
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;

      &.am-rw-500 {
        width: 100%;
        padding-top: 70%;
        margin: 0 0 8px;
      }
    }

    &__info {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      flex: 1;
      //min-height: var(--am-h-ec-info);

      &-name {
        font-size: var(--am-fs-ec-title);
        font-weight: 500;
        line-height: 1.7777777;
        color: var(--am-c-ec-text-op90); // $shade-900
        margin: 0 0 4px;

        &.am-ghost {
          color: var(--am-c-ec-text-op50); // $shade-500
        }
      }

      &-price {
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        color: var(--am-c-ec-primary);
      }

      &-location {
        font-size: 15px;
        font-weight: 400;
        line-height: 1.6;
        color: var(--am-c-ec-text-op80); // $shade-800
        margin: 0 0 4px;

        &.am-ghost {
          color: var(--am-c-ec-text-op50); // $shade-500
        }
      }

      &-other {
        display: flex;
        align-items: center;
        margin: 4px 0 0;
      }

      &-availability {
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        margin: 0 8px 0 0;

        &.closed {
          color: var(--am-c-ec-text-op50); // $shade-500
        }

        &.open {
          color: var(--am-c-ec-success); // $green-1000
        }

        &.full {
          color: var(--am-c-ec-primary); // $blue-700
        }

        &.upcoming {
          color: var(--am-c-ec-warning);
        }

        &.canceled {
          color: var(--am-c-ec-error-op70);
        }
      }

      &-capacity {
        display: flex;
        align-items: center;
        margin: 0;

        &__number {
          font-weight: 700;
          font-size: 15px;
          line-height: 24px;
          color: var(--am-c-ec-text-op80);
        }

        &__text {
          font-weight: 400;
          font-size: 15px;
          line-height: 1.6;
          margin: 0 0 0 4px;
          color: var(--am-c-ec-text-op80);
        }
      }

      &-waiting-list {
        color: var(--am-c-ec-warning);
        font-size: 15px;
        line-height: 1.6;
        font-weight: 500;
        margin: 0;
      }
    }

    &__actions {
      display: flex;
      flex-direction: column;
      align-self: stretch;
      align-items: flex-end;
      justify-content: space-between;

      &.am-vertical-center {
        justify-content: center;

        .am-ec__actions-btn {
          width: 100%;

          .am-button {
            width: 100%;
          }
        }
      }

      &.am-rw-500 {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        margin: 24px 0 8px;
      }

      &-price {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        font-size: var(--am-fs-ec-title);
        font-weight: 500;
        line-height: 1.6;
        color: var(--am-c-ec-primary);
        text-align: center;

        &.am-rw-500 {
          --am-fs-ec-title: 15px !important;

          p {
            margin: 0;
          }
        }

        &.am-rw-360 {
          flex-wrap: wrap;
        }

        p {
          font-size: inherit;
          font-weight: inherit;
          line-height: inherit;
          text-align: right;
          color: inherit;
          margin: 0 0 8px;

          &.am-tax {
            margin-left: 8px;
          }
        }
      }

      &-btn {
        margin: 0;
      }
    }
  }
}
</style>
