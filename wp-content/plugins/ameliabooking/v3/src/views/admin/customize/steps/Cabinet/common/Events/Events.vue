<template>
  <div ref="pageContainer" class="am-cap" :style="cssVars">
    <CabinetFilters
      :param-list="paramList"
      :responsive-class="responsiveClass"
    />

    <div
      v-if="pageRenderKey === 'cape'"
      class="am-cap__actions"
      :class="responsiveClass"
    >
      <AmButton
        v-if="features.eTickets && amCustomize.cape.events.options.scanQrCodeBtn.visibility"
        prefix="scan-qr-code"
        size="small"
        category="secondary"
        type="plain"
      >
        {{amLabels.scan_e_ticket}}
      </AmButton>
      <AmButton
        prefix="plus"
        size="small"
        category="primary"
        :type="amCustomize.cape.events.options.newEvtBtn.buttonType"
      >
        <span>{{ amLabels.new_event }}</span>
      </AmButton>
    </div>

    <div
      class="am-cape__wrapper"
      :class="[
        {
          'am-no-border':
            dateGroupedEvents && Object.keys(dateGroupedEvents).length === 1,
        },
        responsiveClass,
      ]"
    >
      <div
        v-for="(item, dateKey) in dateGroupedEvents"
        :key="dateKey"
        class="am-cape"
      >
        <div
          class="am-cape__date"
          :class="[
            {
              'am-today':
                getFrontedFormattedDate(dateKey) ===
                getFrontedFormattedDate(moment().format('YYYY-MM-DD')),
            },
            {
              'am-no-flag':
                dateGroupedEvents &&
                Object.keys(dateGroupedEvents).length === 1,
            },
            responsiveClass,
          ]"
        >
          {{ getFrontedFormattedDate(dateKey) }}
        </div>
        <CollapseCard
          v-for="(event, index) in item.events"
          :key="index"
          :start="
            getFrontedFormattedTime(
              event.periods[0].periodStart.split(' ')[1].slice(0, 5)
            )
          "
          :name="event.name"
          :employee="[
            { firstName: 'John', lastName: 'Doe', rank: 'organizer' },
          ]"
          :price="useEventBookingsPrice(event)"
          :duration="null"
          :periods="(periods = usePeriodsData(event.periods)) ? periods : []"
          :extras="[]"
          :tickets="event.customTickets"
          :custom-fields="event.bookings[0].customFields"
          :location="event.location"
          :google-meet-link="event.periods[0].googleMeetUrl"
          :microsoft-teams-link="event.periods[0].microsoftTeamsUrl"
          :zoom-link="event.periods[0].zoomMeeting.joinUrl"
          :lesson-space-link="event.periods[0].lessonSpace"
          :bookable="event"
          :reservation="event"
          :booking="event.bookings[0]"
          :responsive-class="responsiveClass"
          :qr-codes="qrCodesData"
          :parent-width="pageWidth"
          :customized-options="amCustomize[pageRenderKey][stepName].options"
        ></CollapseCard>
      </div>
    </div>

    <CancelPopup
      v-if="cancelVisibility"
      :visibility="cancelVisibility"
      title-key="cancel_event"
      content-key="confirm_cancel_event"
    >
    </CancelPopup>
  </div>
</template>

<script setup>
// * Import from libraries
import moment from 'moment'

// * import from Vue
import { ref, computed, inject, onMounted, watch, nextTick } from 'vue'

// * Templates
import CancelPopup from '../parts/CancelPopup.vue'
import CollapseCard from '../parts/CollapseCard/CollapseCard.vue'
import CabinetFilters from '../parts/Filters.vue'

// * Composables
import {
  getFrontedFormattedDate,
  getFrontedFormattedTime,
} from '../../../../../../../assets/js/common/date'
import {
  useEventBookingsPrice,
  usePeriodsData,
} from '../../../../../../../assets/js/admin/event'
import { useResponsiveClass } from '../../../../../../../assets/js/common/responsive'
import { useColorTransparency } from '../../../../../../../assets/js/common/colorManipulation'
import { useReactiveCustomize } from '../../../../../../../assets/js/admin/useReactiveCustomize.js'

// * Components
import AmButton from '../../../../../../_components/button/AmButton.vue'

let pageRenderKey = inject('pageRenderKey')

// * Global Settings
let amSettings = inject('settings')

// * Plugin Licence
let licence = inject('licence')

// * Features
let features = inject('features')

// * Page Content width
let pageContainer = ref(null)
let pageWidth = ref(0)

// * Global labels
let amLabels = inject('labels')

// * Sidebar collapsed var
let sidebarCollapsed = inject('sidebarCollapsed')

// * window resize listener
window.addEventListener('resize', resize)
// * resize function
function resize() {
  if (pageContainer.value) {
    pageWidth.value = pageContainer.value.offsetWidth
  }
}

watch(sidebarCollapsed, (current) => {
  if (current) {
    setTimeout(() => {
      collapseTriggered()
    }, 1500)
  } else {
    setTimeout(() => {
      collapseTriggered()
    }, 500)
  }
})

function collapseTriggered() {
  pageWidth.value = pageContainer.value.offsetWidth
}

onMounted(() => {
  nextTick(() => {
    pageWidth.value = pageContainer.value.offsetWidth
  })
})

let responsiveClass = computed(() => {
  return useResponsiveClass(pageWidth.value)
})

/********
 * Form *
 ********/
let paramList = ref({
  dates: {
    name: 'dates',
  },
  events: {
    name: 'events',
    icon: 'event',
    ids: [],
  },
  providers: {
    name: 'providers',
    icon: 'employee',
    ids: [],
  },
  locations: {
    name: 'locations',
    icon: 'locations',
    ids: [],
  },
})

if (licence.isStarter) {
  delete paramList.value.locations
}

if (pageRenderKey.value === 'cape') {
  delete paramList.value.providers
  paramList.value.customers = {
    name: 'customers',
    icon: 'user',
    ids: [],
  }
}

let arrApp = [
  {
    when: 0,
    name: 'Healthy Plate Symposium',
    status: 'approved',
  },
  {
    when: 0,
    name: 'Nutrition Nirvana Workshop',
    status: 'canceled',
  },
  {
    when: 2,
    name: 'Healthy Plate Symposium',
    status: 'approved',
  },
  {
    when: 2,
    name: 'Culinary Wellness Cooking Class',
    status: 'canceled',
  },
  {
    when: 5,
    name: 'Nutrition Nirvana Workshop',
    status: 'rejected',
  },
  {
    when: 5,
    name: 'Healthy Plate Symposium',
    status: 'approved',
  },
  {
    when: 5,
    name: 'Nutrition Nirvana Workshop',
    status: 'approved',
  },
]

let dateGroupedEvents = ref({})

arrApp.forEach((item, index) => {
  if (
    Object.keys(dateGroupedEvents.value).indexOf(
      moment().add(item.when, 'days').format('YYYY-MM-DD')
    ) < 0
  ) {
    dateGroupedEvents.value[
      moment().add(item.when, 'days').format('YYYY-MM-DD')
    ] = {}
    dateGroupedEvents.value[
      moment().add(item.when, 'days').format('YYYY-MM-DD')
    ].date = moment().add(item.when, 'days').format('YYYY-MM-DD')
    dateGroupedEvents.value[
      moment().add(item.when, 'days').format('YYYY-MM-DD')
    ].events = []
  }

  let app = {
    aggregatedPrice: true,
    bookMultipleTimes: true,
    bookable: true,
    bookingCloses: null,
    bookingClosesRec: 'same',
    bookingOpens: null,
    bookingOpensRec: 'same',
    bookings: [
      {
        actionsCompleted: null,
        aggregatedPrice: true,
        appointmentId: null,
        coupon: null,
        couponId: null,
        created: null,
        customFields: [
          { label: 'Custom field 1', value: '' },
          { label: 'Custom field 2', value: '' },
          { label: 'Custom field 3', value: '' },
        ],
        customer: {
          birthday: null,
          countryPhoneIso: null,
          email: 'testjanedoe@test.com',
          externalId: null,
          firstName: 'Jane',
          gender: null,
          id: 1,
          lastName: 'Doe',
          note: null,
          phone: '',
          pictureFullPath: null,
          pictureThumbPath: null,
          status: 'visible',
          translations: null,
          type: 'customer',
          zoomUserId: null,
        },
        customerId: 1,
        duration: null,
        extras: [],
        id: 1,
        info: '{"firstName":"Jane","lastName":"Doe","phone":null,"locale":"en_US","timeZone":"Europe\\/Belgrade","urlParams":null}',
        isChangedStatus: null,
        isLastBooking: null,
        isUpdated: null,
        packageCustomerService: null,
        payments: [],
        persons: 1,
        price: 0,
        status: item.status,
        ticketsData: [
          {
            customerBookingId: index,
            eventTicketId: index,
            id: index,
            persons: 2,
            price: 10,
          },
          {
            customerBookingId: index,
            eventTicketId: index,
            id: index + 1,
            persons: 1,
            price: 15,
          },
          {
            customerBookingId: index,
            eventTicketId: index,
            id: index + 2,
            persons: 3,
            price: 20,
          },
        ],
        token: null,
        utcOffset: null,
      },
    ],
    bringingAnyone: true,
    cancelable: true,
    closeAfterMin: null,
    closeAfterMinBookings: false,
    closed: false,
    color: '#FA3C52',
    coupons: [],
    created: '2023-02-23 11:08:33',
    customLocation: null,
    customPricing: true,
    customTickets: [
      {
        dateRangePrice: null,
        dateRanges: '[]',
        enabled: true,
        eventId: index,
        id: index,
        name: 'Ticket 1',
        price: 10,
        sold: 2,
        spots: 60,
        persons: 2,
        translations: null,
      },
      {
        dateRangePrice: null,
        dateRanges: '[]',
        enabled: true,
        eventId: index,
        id: index + 1,
        name: 'Ticket 2',
        price: 15,
        sold: 1,
        spots: 60,
        persons: 1,
        translations: null,
      },
      {
        dateRangePrice: null,
        dateRanges: '[]',
        enabled: true,
        eventId: index,
        id: index + 2,
        name: 'Ticket 3',
        price: 20,
        sold: 3,
        spots: 60,
        persons: 3,
        translations: null,
      },
    ],
    deposit: 10,
    depositPayment: 'fixed',
    depositPerPerson: true,
    description:
      '<!-- Content --><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis eget ac aliquam orci sed ac aliquet metus a. Magna dui consectetur tellus, suspendisse consequat, sem pulvinar. Ut tincidunt donec neque condimentum sed proin.</p>',
    extras: [],
    full: false,
    fullPayment: true,
    id: index,
    initialEventEnd: null,
    initialEventStart: null,
    location: 'Location 1',
    locationId: 6,
    maxCapacity: 240,
    maxCustomCapacity: null,
    maxExtraPeople: 10,
    name: item.name,
    notifyParticipants: 0,
    opened: true,
    organizerId: 24,
    parentId: null,
    periods: [
      {
        bookings: [],
        eventId: index,
        googleCalendarEventId: null,
        googleMeetUrl: '/',
        id: index,
        lessonSpace: '/',
        outlookCalendarEventId: '/',
        microsoftTeamsUrl: '/',
        appleCalendarEventId: null,
        periodEnd: `${moment()
          .add(item.when + 1, 'days')
          .format('YYYY-MM-DD')} 23:00:00`,
        periodStart: `${moment()
          .add(item.when, 'days')
          .format('YYYY-MM-DD')} 03:00:00`,
        zoomMeeting: {
          joinUrl: '/',
        },
      },
      {
        bookings: [],
        eventId: index,
        googleCalendarEventId: null,
        googleMeetUrl: '/',
        id: index + 1,
        lessonSpace: '/',
        outlookCalendarEventId: '/',
        microsoftTeamsUrl: '/',
        appleCalendarEventId: null,
        periodEnd: `${moment()
          .add(item.when + 3, 'days')
          .format('YYYY-MM-DD')} 23:00:00`,
        periodStart: `${moment()
          .add(item.when + 2, 'days')
          .format('YYYY-MM-DD')} 03:00:00`,
        zoomMeeting: {
          joinUrl: '/',
        },
      },
      {
        bookings: [],
        eventId: index,
        googleCalendarEventId: null,
        googleMeetUrl: '/',
        id: index + 2,
        lessonSpace: '/',
        outlookCalendarEventId: '/',
        microsoftTeamsUrl: '/',
        appleCalendarEventId: null,
        periodEnd: `${moment()
          .add(item.when + 5, 'days')
          .format('YYYY-MM-DD')} 23:00:00`,
        periodStart: `${moment()
          .add(item.when + 4, 'days')
          .format('YYYY-MM-DD')} 03:00:00`,
        zoomMeeting: {
          joinUrl: '/',
        },
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 236,
    position: null,
    price: 10,
    recurring: null,
    show: true,
    status: item.status,
    tags: [
      {
        id: index,
        eventId: index,
        name: 'Tag 1',
      },
      {
        id: index + 1,
        eventId: index,
        name: 'Tag 2',
      },
    ],
    ticketRangeRec: 'calculate',
    translations: null,
    type: 'event',
    upcoming: false,
    zoomUserId: null,
  }

  dateGroupedEvents.value[
    moment().add(item.when, 'days').format('YYYY-MM-DD')
  ].events.push(app)
})

// * Qr Codes
const qrCodesData = [
  {
    eventName: 'Event name',
    type: 'groupTicket',
  },
  {
    eventName: 'Event name',
    type: 'ticket',
  },
  {
    eventName: 'Event name',
    type: 'ticket',
  },
]

// * Customize
const { amCustomize } = useReactiveCustomize()
let stepName = inject('stepName')

// * Cancel Event
let subStepName = inject('subStepName')

let cancelVisibility = computed(() => {
  return subStepName.value === 'cancelEvent'
})

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-cape-bgr': amColors.value.colorMainBgr,
    '--am-c-cape-text': amColors.value.colorMainText,
    '--am-c-cape-text-op70': useColorTransparency(
      amColors.value.colorMainText,
      0.7
    ),
    '--am-c-cape-text-op25': useColorTransparency(
      amColors.value.colorMainText,
      0.25
    ),
    '--am-c-cape-primary': amColors.value.colorPrimary,
  }
})
</script>

<script>
export default {
  name: 'CabinetEvents',
  key: 'events',
}
</script>

<style lang="scss">
@mixin am-cabinet-events {
  // cape -- cabinet panel events
  .am-cape {
    position: relative;

    &__wrapper {
      position: relative;
      padding: 0 0 0 32px;

      &:before {
        content: '';
        display: block;
        width: 1px;
        height: calc(100% - 4px);
        position: absolute;
        top: 4px;
        left: 8px;
        background-image: linear-gradient(
            180deg,
            transparent,
            transparent 50%,
            var(--am-c-cape-bgr) 50%,
            var(--am-c-cape-bgr) 100%
          ),
          linear-gradient(
            180deg,
            var(--am-c-cape-text-op25),
            var(--am-c-cape-text-op25),
            var(--am-c-cape-text-op25),
            var(--am-c-cape-text-op25)
          );
        background-size: 3px 12px, 100% 20px;
      }

      &.am-no-border {
        padding: 0;

        &:before {
          display: none;
        }
      }

      &.am-rw-500 {
        padding: 0;

        &:before {
          display: none;
        }
      }
    }

    &__date {
      font-size: 15px;
      font-weight: 500;
      line-height: 1.6;
      color: var(--am-c-cape-text-op70);
      margin: 24px 0 8px;

      &:before {
        content: '';
        display: block;
        width: 16px;
        height: 16px;
        position: absolute;
        top: 4px;
        left: -32px;
        background-color: var(--am-c-cape-text-op70);
        border-radius: 50%;
        z-index: 2;
      }

      &:after {
        content: '';
        display: block;
        width: 16px;
        height: 16px;
        position: absolute;
        top: 4px;
        left: -32px;
        background-color: var(--am-c-cape-bgr);
        border-radius: 50%;
        z-index: 1;
      }

      &.am-today {
        &:before {
          background-color: var(--am-c-cape-primary);
        }
      }

      &.am-no-flag {
        &:before,
        &:after {
          display: none;
        }
      }

      &.am-rw-500 {
        display: flex;
        align-items: center;
        &:before {
          position: static;
          margin-right: 8px;
        }

        &:after {
          display: none;
        }
      }
    }
  }

  .am-cap {
    &__actions {
      width: 100%;
      display: flex;
      justify-content: flex-end;
      margin: 0 0 16px;
      gap: 0 10px;

      &.am-rw-420 {
        justify-content: flex-start;
        flex-wrap: wrap;
        gap: 8px;

        .am-button {
          width: 100%;
        }
      }

      .am-button {
        .am-icon-plus, .am-icon-scan-qr-code {
          font-size: 24px;
        }
      }
    }
  }
}

#amelia-app-backend-new #amelia-container {
  @include am-cabinet-events;
}
</style>
