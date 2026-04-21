<template>
  <div
    class="am-ec"
    :class="[{ 'am-no-border': !props.borderVisibility }, responsiveClass]"
    :style="cssVars"
  >
    <!-- Period -->
    <div class="am-ec__period" :class="responsiveClass">
      <p
        v-if="eventStatus === 'open' || eventStatus === 'upcoming'"
        class="am-ec__period-text"
        :class="responsiveClass"
      >
        {{ labelsDisplay('event_begins') }}
      </p>
      <p class="am-ec__period-date" :class="responsiveClass">
        <span
          class="am-ec__period-date__day"
          :class="[
            {
              'am-ghost':
                eventStatus === 'canceled' || eventStatus === 'closed',
            },
            responsiveClass,
          ]"
        >
          {{
            getEventFrontedFormattedDateDay(
              props.event.periods[0].periodStart.split(' ')[0]
            )
          }}
        </span>
        <span
          class="am-ec__period-date__month"
          :class="[
            {
              'am-ghost':
                eventStatus === 'canceled' || eventStatus === 'closed',
            },
            responsiveClass,
          ]"
        >
          {{
            getEventFrontedFormattedDateMonth(
              props.event.periods[0].periodStart.split(' ')[0]
            )
          }}
        </span>
      </p>
      <p
        class="am-ec__period-time"
        :class="[
          {
            'am-ghost': eventStatus === 'canceled' || eventStatus === 'closed',
          },
          responsiveClass,
        ]"
      >
        {{
          getEventFrontedFormattedTime(
            props.event.periods[0].periodStart.split(' ')[1]
          )
        }}
      </p>
    </div>

    <!-- Image -->
    <div
      v-if="
        props.event.gallery.length &&
        props.imageVisibility &&
        customizeOptions.imgTab.visibility
      "
      class="am-ec__image"
      :class="responsiveClass"
      :style="{
        backgroundImage: `url(${props.event.gallery[0].pictureFullPath})`,
      }"
    ></div>

    <!-- Info -->
    <div class="am-ec__info">
      <p
        class="am-ec__info-name"
        :class="{
          'am-ghost': eventStatus === 'canceled' || eventStatus === 'closed',
        }"
      >
        {{ props.event.name }}
      </p>
      <p
        v-if="
          inHeader && componentWidth <= 500 && customizeOptions.price.visibility
        "
        class="am-ec__info-price"
      >
        {{
          props.event.customPricing
            ? `${labelsDisplay('from')} ${useFormattedPrice(
                useMinTicketPrice(props.event)
              )}`
            : useFormattedPrice(props.event.price)
        }}
        <template v-if="taxVisibility">
          {{
            amSettings.payments.taxes.excluded
              ? `+${labelsDisplay('total_tax_colon')}`
              : labelsDisplay('incl_tax')
          }}
        </template>
      </p>
      <p
        v-if="customizeOptions.location.visibility"
        class="am-ec__info-location"
        :class="{
          'am-ghost': eventStatus === 'canceled' || eventStatus === 'closed',
        }"
      >
        {{ useEventLocation(props.event, locations) }}
      </p>
      <div
        v-if="
          customizeOptions.status.visibility ||
          customizeOptions.slots.visibility
        "
        class="am-ec__info-other"
      >
        <p
          v-if="customizeOptions.status.visibility"
          :class="`am-ec__info-availability ${eventStatus}`"
        >
          {{ labelsDisplay(eventStatus) }}
        </p>
        <p
          v-if="
            eventStatus === 'full' &&
            customizeOptions.slots.visibility &&
            features.waitingList
          "
          class="am-ec__info-waiting-list"
        >
          {{ eventPlaces }}
          {{
            eventPlaces === 1
              ? labelsDisplay('person_waiting')
              : labelsDisplay('people_waiting')
          }}
        </p>
        <p
          v-if="
            showEventCapacity(eventStatus) && customizeOptions.slots.visibility
          "
          class="am-ec__info-capacity"
        >
          <span class="am-ec__info-capacity__number">
            {{ eventPlaces }}
          </span>
          <span class="am-ec__info-capacity__text">
            {{
              eventPlaces > 1
                ? ` ${labelsDisplay('event_slots_left')}`
                : ` ${labelsDisplay('event_slot_left')}`
            }}
          </span>
        </p>
      </div>
    </div>

    <!-- Price and Action Button -->
    <div
      v-if="!inHeader || componentWidth > 500"
      class="am-ec__actions"
      :class="[
        { 'am-vertical-center': !customizeOptions.price.visibility },
        responsiveClass,
      ]"
    >
      <div
        v-if="customizeOptions.price.visibility"
        class="am-ec__actions-price"
        :class="responsiveClass"
      >
        <p>
          {{
            props.event.customPricing
              ? `${labelsDisplay('from')} ${useFormattedPrice(
                  useMinTicketPrice(props.event)
                )}`
              : useFormattedPrice(props.event.price)
          }}
          <template v-if="taxVisibility">
            {{
              amSettings.payments.taxes.excluded
                ? `+${labelsDisplay('total_tax_colon')}`
                : labelsDisplay('incl_tax')
            }}
          </template>
        </p>
      </div>
      <p v-if="props.btnVisibility" class="am-ec__actions-btn">
        <AmButton
          :size="componentWidth > 500 ? 'small' : 'medium'"
          :type="bookingBtnType"
          :category="isWaiting ? 'waiting' : 'primary'"
          @click="selectEvent(props.event.id)"
        >
          {{
            isWaiting
              ? labelsDisplay('join_waiting_list')
              : eventStatus !== 'open'
              ? labelsDisplay('event_learn_more')
              : labelsDisplay('event_read_more')
          }}
        </AmButton>
      </p>
    </div>
  </div>
</template>

<script setup>
// * _components
import AmButton from '../../../../../../_components/button/AmButton.vue'

// * Import from Vue
import { computed, inject } from 'vue'

// * Composables
import {
  useEventLocation,
  useMinTicketPrice,
  showEventCapacity,
  useEventStatus,
} from '../../../../../../../assets/js/public/events.js'
import {
  getEventFrontedFormattedTime,
  getEventFrontedFormattedDateDay,
  getEventFrontedFormattedDateMonth,
} from '../../../../../../../assets/js/common/date.js'
import { useFormattedPrice } from '../../../../../../../assets/js/common/formatting'
import { useColorTransparency } from '../../../../../../../assets/js/common/colorManipulation'
import { useResponsiveClass } from '../../../../../../../assets/js/common/responsive'

let props = defineProps({
  event: {
    type: [Object, Function, Array],
    required: true,
  },
  parentKey: {
    type: String,
    required: true,
  },
  tickets: {
    type: Array,
    default: () => [],
  },
  locations: {
    type: Array,
    required: true,
  },
  borderVisibility: {
    type: Boolean,
    default: true,
  },
  btnVisibility: {
    type: Boolean,
    default: true,
  },
  autoInfoHeight: {
    type: Boolean,
    default: false,
  },
  imageVisibility: {
    type: Boolean,
    default: true,
  },
  verticalOrientation: {
    type: String,
    default: 'center',
  },
  inHeader: {
    type: Boolean,
    default: false,
  },
  inDialog: {
    type: Boolean,
    default: false,
  },
})

const emits = defineEmits(['click'])

// * Root Settings
let amSettings = inject('settings')

// * Licence
let licence = inject('licence')

// * Features
let features = inject('features')

// * Container width
let cWidth = inject('containerWidth')

let componentWidth = computed(() => {
  return cWidth.value
})

let responsiveClass = computed(() => useResponsiveClass(componentWidth.value))

let eventStatus = computed(() => {
  return useEventStatus(props.event)
})

// * Waiting enabled
let isWaiting = computed(() => {
  return (
    eventStatus.value === 'full' &&
    features.value.waitingList
  )
})

let eventPlaces = computed(() => {
  if (props.event.customPricing) {
    let places = 0
    let eventTickets = props.tickets.length
      ? props.tickets
      : props.event.customTickets

    if (props.event.maxCustomCapacity) {
      let sold = 0
      eventTickets.forEach((t) => {
        if ('enabled' in t ? t.enabled : true) {
          sold += t.sold
        }
      })

      places = props.event.maxCustomCapacity - sold
    } else {
      eventTickets.forEach((t) => {
        if ('enabled' in t ? t.enabled : true) {
          places += isWaiting.value
            ? t.sold
            : t.spots - t.sold - ('persons' in t ? t.persons : 0)
        }
      })
    }

    return places
  }

  return props.event.places
})

function selectEvent(id) {
  emits('click', id)
}

// * Customize
let stepName = inject('stepName')
let pageRenderKey = inject('pageRenderKey')
let amCustomize = inject('customize')

// * Options
let customizeOptions = computed(() => {
  return amCustomize.value[pageRenderKey.value][stepName.value].options
})

let langKey = inject('langKey')
let amLabels = inject('labels')

function labelsDisplay(label) {
  let computedLabel = computed(() => {
    let translations =
      amCustomize.value[pageRenderKey.value][props.parentKey].translations
    return translations &&
      translations[label] &&
      translations[label][langKey.value]
      ? translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

let taxVisibility = computed(() => {
  return (
    amSettings.payments?.taxes?.enabled &&
    customizeOptions.value.tax.visibility &&
    !licence.isStarter &&
    !licence.isLite
  )
})

// * Colors
let amColors = inject('amColors')

let cardBorderOrientation = computed(() => {
  if (componentWidth.value <= 500) {
    return `0px 2px 2px -1px ${
      amColors.value.colorCardBorder
    }, 0px 0px 11px ${useColorTransparency(
      amColors.value.colorCardBorder,
      0.3
    )}, inset 0px -8px 0px ${props.event.color}`
  }

  return `0px 2px 2px -1px ${
    amColors.value.colorCardBorder
  }, 0px 0px 11px ${useColorTransparency(
    amColors.value.colorCardBorder,
    0.3
  )}, inset 8px 0px 0px ${props.event.color}`
})

let bookingBtnType = computed(() => {
  if (isWaiting.value) {
    return customizeOptions.value.waitingBtn.buttonType
  }

  if (eventStatus.value !== 'open') {
    return customizeOptions.value.infoBtn.buttonType
  }
  return customizeOptions.value.bookingBtn.buttonType
})

// * Css Vars
let cssVars = computed(() => {
  let obj = {
    boxShadow: cardBorderOrientation.value,
    '--am-c-ec-success': amColors.value.colorSuccess,
    '--am-c-ec-primary': amColors.value.colorPrimary,
    '--am-c-ec-warning': amColors.value.colorWarning,
    '--am-c-ec-bgr': props.inHeader
      ? amColors.value.colorMainBgr
      : amColors.value.colorCardBgr,
    '--am-c-ec-text': props.inHeader
      ? amColors.value.colorMainText
      : amColors.value.colorCardText,
    '--am-c-ec-text-op90': useColorTransparency(
      props.inHeader
        ? amColors.value.colorMainText
        : amColors.value.colorCardText,
      0.9
    ),
    '--am-c-ec-text-op80': useColorTransparency(
      props.inHeader
        ? amColors.value.colorMainText
        : amColors.value.colorCardText,
      0.8
    ),
    '--am-c-ec-text-op50': useColorTransparency(
      props.inHeader
        ? amColors.value.colorMainText
        : amColors.value.colorCardText,
      0.5
    ),
    '--am-c-ec-error-op70': useColorTransparency(
      amColors.value.colorError,
      0.7
    ),
    '--am-h-ec-info': props.autoInfoHeight ? 'unset' : '84px',
    '--am-vo-ec': props.verticalOrientation,
    '--am-fs-ec-title': props.inHeader ? '18px' : '16px',
  }

  if (!props.borderVisibility) delete obj.boxShadow

  return obj
})
</script>

<script>
export default {
  name: 'EventCard',
}
</script>

<style lang="scss">
#amelia-app-backend-new #amelia-container {
  // am - amelia
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

        p {
          font-size: inherit;
          font-weight: inherit;
          line-height: inherit;
          text-align: right;
          color: inherit;
          margin: 0 0 8px;
        }
      }

      &-btn {
        margin: 0;
      }
    }
  }
}
</style>
