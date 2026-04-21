<template>
  <div
    ref="pageContainer"
    class="am-cap am-cape-main"
    :class="{'am-capei-main': eventVisibility || (eventAttendeeVisibility || attendeeOpenFromAttendees)}"
    :style="cssVars"
  >
    <div
      class="am-cape-main__inner"
      :class="responsiveClass"
    >
      <AmAlert
        v-if="alertVisibility"
        ref="alertContainer"
        :type= "alertType"
        :show-border="true"
        :close-after="5000"
        custom-class="am-cap__alert"
        @close="closeAlert"
        @trigger-close="closeAlert"
      >
        <template #title>
          <span class="am-icon-checkmark-circle-full"></span> {{ alertMessage }}
        </template>
      </AmAlert>

      <CabinetFilters
        v-if="!eventAttendeeVisibility && !eventVisibility && !eventAttendeesVisibility && ready"
        :step-key="'events'"
        :empty="empty"
        :responsive-class="responsiveClass"
        @change-filters="getEvents"
      />

      <div
        v-if="(shortcodeData.cabinetType === 'employee' && ready && !eventAttendeeVisibility && !eventVisibility && !eventAttendeesVisibility) &&
          (((licence.isPro || licence.isDeveloper) && amSettings.featuresIntegrations.eTickets.enabled) || amSettings.roles.allowWriteEvents)"
        class="am-cap__actions"
        :class="responsiveClass"
      >
        <AmButton
          v-if="(licence.isPro || licence.isDeveloper) && amSettings.featuresIntegrations.eTickets.enabled && (amCustomize.events.options?.scanQrCodeBtn?.visibility ?? true)"
          prefix="scan-qr-code"
          size="small"
          category="secondary"
          type="plain"
          @click="qrScannerVisibility = true"
        >
          {{amLabels.scan_e_ticket}}
        </AmButton>
        <AmButton
          v-if="amSettings.roles.allowWriteEvents"
          :icon="plusIcon"
          prefix="plus"
          size="small"
          category="primary"
          :type="amCustomize.events.options.newEvtBtn.buttonType"
          @click="addEvent"
        >
          <span>{{amLabels.new_event}}</span>
        </AmButton>
      </div>

      <template v-if="!loading && ready">
        <div
          v-if="!eventAttendeeVisibility && !eventVisibility && !eventAttendeesVisibility && dateGroupedEvents && Object.keys(dateGroupedEvents).length > 0"
          class="am-cape__wrapper"
          :class="[{'am-no-border': dateGroupedEvents && Object.keys(dateGroupedEvents).length === 1}, responsiveClass]"
        >
          <div
            v-for="(item, dateKey) in dateGroupedEvents"
            :key="dateKey"
            class="am-cape"
          >
            <div
              class="am-cape__date"
              :class="[
                {'am-today': getFrontedFormattedDate(dateKey) === getFrontedFormattedDate(moment().format('YYYY-MM-DD'))},
                {'am-no-flag': dateGroupedEvents && Object.keys(dateGroupedEvents).length === 1},
                responsiveClass
              ]"
            >
              {{getFrontedFormattedDate(dateKey)}}
            </div>
            <CollapseCard
              v-for="(event, index) in item.events"
              :key="index"
              :start="getFrontedFormattedTime(event.periods[0].periodStart.split(' ')[1].slice(0, 5))"
              :name="event.name"
              :employee="eventEmployees(event)"
              :customers="event.bookings.filter(i => i.status !== 'rejected' && i.status !== 'canceled').map(i => i.customer)"
              :price="useEventBookingsPrice(event)"
              :duration="null"
              :periods="(periods = usePeriodsData(event.periods)) ? periods : []"
              :extras="[]"
              :tickets="useTicketsData(event)"
              :custom-fields="useCustomFieldsData(event.bookings, shortcodeData.cabinetType)"
              :location="useEventLocation(store, event)"
              :qr-codes="qrCodeTicketsVisibility ? useEventQrCodes(event) : []"
              :google-meet-link="event.periods.length === 1 && event.periods[0].googleMeetUrl ? event.periods[0].googleMeetUrl : ''"
              :microsoft-teams-link="event.periods.length === 1 && event.periods[0].microsoftTeamsUrl ? event.periods[0].microsoftTeamsUrl : ''"
              :zoom-link="event.periods.length === 1 && event.periods[0].zoomMeeting ? event.periods[0].zoomMeeting.joinUrl : ''"
              :lesson-space-link="event.periods.length === 1 && event.periods[0].lessonSpace ? event.periods[0].lessonSpace : ''"
              :bookable="event"
              :reservation="event"
              :booking="event.bookings[0]"
              :responsive-class="responsiveClass"
              :parent-width="pageWidth"
              :customized-options="customizedOptions('events')"
              @edit-event="editEvent"
              @add-event-attendee="addEventAttendee"
              @list-event-attendees="listEventAttendees"
              @cancel-booking="(data) => {
                targetBooking = data
              }"
            ></CollapseCard>
          </div>
        </div>

        <EmptyState
          v-if="!eventAttendeeVisibility && !eventVisibility && !eventAttendeesVisibility && (dateGroupedEvents === null || Object.keys(dateGroupedEvents).length === 0)"
          :heading="amLabels.no_evt_found"
          :text="amLabels.have_no_evt"
        ></EmptyState>

        <AmPagination
          v-if="!eventAttendeeVisibility && !eventVisibility && !eventAttendeesVisibility && dateGroupedEvents && Object.keys(dateGroupedEvents).length > 0 && eventsCount > amSettings.general.itemsPerPage"
          :page-size="amSettings.general.itemsPerPage"
          :pager-count="5"
          layout="prev, pager, next"
          :total="eventsCount"
          :current-page="eventsPage"
          @current-change="eventsPageChange"
        />

        <Event
          v-if="eventVisibility"
          :page-width="pageWidth"
          :responsive-class="responsiveClass"
          @close="closeEvent"
          @save="saveEventCallback"
          @duplicate="duplicateEvent"
        ></Event>

        <EventAttendees
          v-if="!eventAttendeeVisibility && eventAttendeesVisibility"
          :event="targetEvent"
          :page-width="pageWidth"
          :responsive-class="responsiveClass"
          @close="closeEventAttendees"
          @open-attendee="openAttendee"
        ></EventAttendees>

        <Attendee
          v-if="eventAttendeeVisibility"
          :visibility="eventAttendeeVisibility"
          :title="editAttendeeRecognition ? amLabels.event_edit_attendee : amLabels.event_add_attendee"
          :event="targetEvent"
          :page-width="pageWidth"
          :is-new="!editAttendeeRecognition"
          @save="savedEventAttendee"
          @close="closedEventAttendee"
        ></Attendee>

        <CancelPopup
          v-if="originKey === 'capc'"
          :visibility="targetBooking !== null"
          :title="customizedLabels('cancelEvent').cancel_event"
          :description="customizedLabels('cancelEvent').confirm_cancel_event"
          :close-btn-text="customizedLabels('cancelEvent').close"
          :confirm-btn-text="customizedLabels('cancelEvent').confirm"
          :customized-options="customizedOptions('cancelEvent')"
          :loading="waitingForCancelation"
          @close="targetBooking = null"
          @decline="targetBooking = null"
          @confirm="cancelBooking"
        >
        </CancelPopup>

        <QrCodeScanner
          v-if="qrScannerVisibility"
          v-model:visibility="qrScannerVisibility"
          @update:visibility="(value) => qrScannerVisibility = value"
        />
      </template>
      <Skeleton v-else></Skeleton>
    </div>
  </div>
</template>

<script setup>
// * import from Vue
import {
  ref,
  reactive,
  computed,
  inject,
  provide,
  onMounted,
  watch,
} from "vue";

import { useElementSize } from "@vueuse/core";

// * Import from Vuex
import { useStore } from "vuex";

// * Import from Libraries
import httpClient from "../../../../../plugins/axios";

// * Templates
import CancelPopup from "../../common/parts/CancelPopup.vue";
import CollapseCard from "../parts/CollapseCard/CollapseCard.vue";
import CabinetFilters from "../parts/Filters.vue";
import Skeleton from "../../common/parts/Skeleton.vue";
import QrCodeScanner from "../parts/QrCodeScanner.vue";

// * Composables
import {
  useAuthorizationHeaderObject
} from "../../../../../assets/js/public/panel";
import {
  getDateRange,
  getFrontedFormattedDate,
  getFrontedFormattedTime
} from "../../../../../assets/js/common/date";
import {
  useParsedEvents,
  useEventBookingsPrice,
  usePeriodsData,
  useTicketsData,
  useEventLocation,
  useEventQrCodes,
} from "../../../../../assets/js/admin/event";
import {
  useFrontEvent,
} from "../../../../../assets/js/common/events";
import {
  useInitAttendee,
} from "../../../../../assets/js/common/attendees";
import {
  useCustomFieldsData,
} from "../../../../../assets/js/admin/booking";
import {
  useResponsiveClass
} from "../../../../../assets/js/common/responsive";
import {
  useColorTransparency
} from "../../../../../assets/js/common/colorManipulation";
import {
  useUrlParams
} from "../../../../../assets/js/common/helper";
import moment from "moment";
import EmptyState from "../parts/EmptyState.vue";
import AmAlert from "../../../../_components/alert/AmAlert.vue";
import AmButton from "../../../../_components/button/AmButton.vue";
import Event from "../Events/parts/Event.vue";
import EventAttendees from "../Events/parts/Attendees.vue";
import Attendee from "../Events/parts/Attendee.vue";
import {useScrollTo} from "../../../../../assets/js/common/scrollElements";
import IconComponent from "../../../../_components/icons/IconComponent.vue";
import {usePopulateSettings} from "../../../../../assets/js/common/settings";
import AmPagination from "../../../../_components/pagination/AmPagination.vue";

// * Store
let store = useStore()

// * Page Content width
let pageContainer = ref(null)

let { width: pageWidth } = useElementSize(pageContainer)

let responsiveClass = computed(() => {
  return useResponsiveClass(pageWidth.value)
})

// * Plus icon
let plusIcon = {
  components: {IconComponent},
  template: `<IconComponent icon="plus"/>`
}

// * Plugin Licence
let licence = inject('licence')

// * Root Settings
const amSettings = inject('settings')

let originKey = inject('originKey')

// * Customized form data
let amCustomize = inject('amCustomize')

// * Data in shortcode
const shortcodeData = inject('shortcodeData')

// * Qr Code Tickets visibility
let qrCodeTicketsVisibility = computed(() => {
  return shortcodeData.value.cabinetType === 'customer' && (licence.isPro || licence.isDeveloper) && amSettings.featuresIntegrations.eTickets.enabled
})

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  let customizedLabels = amCustomize.value.events.translations
  if (customizedLabels) {
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

provide('amLabels', amLabels)

// * Alert block
let alertContainer = ref(null)
let alertVisibility = ref(false)
let alertType = ref('success')

let alertMessage = ref('')

function closeAlert () {
  alertVisibility.value = false
  store.commit('cabinet/setPaymentLinkError', {value: false, type: 'event'})
}

function customizedLabels (step) {
  let computedLabels = reactive({...labels})

  let customizedLabels = amCustomize.value[step].translations
  if (customizedLabels) {
    Object.keys(customizedLabels).forEach(labelKey => {
      if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
        computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
}

function customizedOptions (step) {
  return amCustomize.value[step].options
}

let ready = computed(() => store.getters['entities/getReady'])

/********
 * Form *
 ********/
let props = defineProps({
  loadBookingsCounter: {
    type: Number,
    default: 0
  },
})

// * Cabinet type
let cabinetType = inject('cabinetType')
store.commit('cabinetFilters/setDates', getDateRange(cabinetType.value))

// * Loading
let loading = computed(() => store.getters['cabinet/getEventsLoading'])

let empty = ref(false)

let targetBooking = ref(null)

let dateGroupedEvents = ref(null)

let eventsCount = ref(0)

let eventsPage = ref(1)

function eventsPageChange(page) {
  eventsPage.value = page

  getEvents(page)
}

function getEvents (page = 1) {
  targetBooking.value = null

  store.commit('cabinet/setEventsLoading', true)

  let timeZone = store.getters['cabinet/getTimeZone']
  let params = JSON.parse(JSON.stringify(store.getters['cabinetFilters/getEventsFilters']))
  params.dates = params.dates.map(d => moment(d).format('YYYY-MM-DD'))
  params.timeZone = timeZone
  params.source = 'cabinet-' + cabinetType.value
  params.id = params.events
  params.group = true
  params.page = page
  params.limit = amSettings.general.itemsPerPage

  if (params.customers) {
    params.customerId = params.customers
  }

  store.commit('auth/setLoadingEventsCounter', store.getters['auth/getLoadingEventsCounter'] + 1)

  let loadingCounter = store.getters['auth/getLoadingEventsCounter']

  httpClient.get(
    '/events',
    Object.assign(
      useAuthorizationHeaderObject(store),
      {params: useUrlParams(params)}
    )
  ).then((response) => {
    if (loadingCounter !== store.getters['auth/getLoadingEventsCounter']) {
      return
    }

    store.dispatch(
      'cabinetFilters/injectEventsOptions',
      response.data.data.events
    )

    store.commit('auth/setPreloadedEvents', response.data.data.events)

    eventsCount.value = response.data.data.count

    store.commit('eventEntities/setEvents', response.data.data.events)
    dateGroupedEvents.value = useParsedEvents(
      response.data.data.events,
      store.getters['cabinet/getTimeZone'],
      store
    )
  }).catch((error) => {
    if(error?.response?.data?.data?.reauthorize !== undefined && error.response.data.data.reauthorize) {
      store.dispatch('auth/logout')
    }

    console.log(error)
  }).finally(() => {
    if (loadingCounter !== store.getters['auth/getLoadingEventsCounter']) {
      return
    }

    store.commit('cabinet/setEventsLoading', false)
  })
}

/*********
 * Event *
 *********/
function editEvent (event) {
  store.commit('cabinet/setEventsLoading', true)

  httpClient.get(
    '/events/' + event.id,
    Object.assign(
      {
        params: {
          source: 'cabinet-' + cabinetType.value,
          timeZone: store.getters['cabinet/getTimeZone'],
        },
      },
      useAuthorizationHeaderObject(store)
    )
  ).then((response) => {
    store.commit(
      'event/setEvent',
      useFrontEvent(store, response.data.data.event)
    )

    eventVisibility.value = true
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    store.commit('cabinet/setEventsLoading', false)
  })
}

let eventVisibility = ref(false)

let eventAttendeesVisibility = ref(false)

let eventAttendeeVisibility = ref(false)

let editAttendeeRecognition = ref(false)

let attendeeOpenFromAttendees = ref(false)

let targetEvent = ref(null)

function duplicateEvent (event) {
  store.commit('cabinet/setEventsLoading', true)

  eventVisibility.value = false

  setTimeout(() => {
    store.commit('event/setEvent', event)

    eventVisibility.value = true

    store.commit('cabinet/setEventsLoading', false)
  }, 500)
}

function saveEventCallback () {
  alertVisibility.value = true
  alertMessage.value = amLabels.value.event_saved

  if (pageContainer.value && alertContainer.value) {
    setTimeout(function () {
      useScrollTo(pageContainer.value, alertContainer.value.$el, 0, 300)
    }, 500)
  }

  getEvents()

  closeEvent()
}

function addEvent () {
  resetEvent()

  eventVisibility.value = true
}

function addEventAttendee (event) {
  targetEvent.value = event

  resetEventAttendee(useInitAttendee(store, store.getters['attendee/getDefaultAttendee'], event))

  eventAttendeeVisibility.value = true
}

function savedEventAttendee () {
  getEvents(eventsPage.value)

  closedEventAttendee()
}

function closedEventAttendee () {
  eventAttendeeVisibility.value = false
  editAttendeeRecognition.value = false
}

function listEventAttendees (event) {
  targetEvent.value = event

  eventAttendeesVisibility.value = true
}

function closeEventAttendees () {
  targetEvent.value = null

  getEvents(eventsPage.value)

  resetEventAttendee(useInitAttendee(store, store.getters['attendee/getDefaultAttendee']))

  eventAttendeesVisibility.value = false
  attendeeOpenFromAttendees.value = false
}

function openAttendee (open) {
  attendeeOpenFromAttendees.value = open
}

function resetEventAttendee (payload = {}) {
  store.commit('attendee/setAttendee', payload)
}

function closeEvent () {
  eventVisibility.value = false

  resetEvent()
}

function resetEvent () {
  store.commit(
    'event/setEvent',
    {bookings: [], settings: usePopulateSettings(amSettings, store.getters['event/getSettings'], {}, {})}
  )
}

let waitingForCancelation = ref(false)
function cancelBooking () {
  waitingForCancelation.value = true
  httpClient.post(
    '/bookings/cancel/' + targetBooking.value.id,
    {
      type: 'event',
      source: 'cabinet-' + cabinetType.value,
    },
    Object.assign(useAuthorizationHeaderObject(store), {params: {source: 'cabinet-' + cabinetType.value}})
  ).then(() => {
    targetBooking.value = null
    getEvents()

    alertVisibility.value = true
    alertMessage.value = amLabels.value.event_canceled

    if (pageContainer.value && alertContainer.value) {
      setTimeout(function () {
        useScrollTo(pageContainer.value, alertContainer.value.$el, 0, 300)
      }, 500)
    }
  }).catch((error) => {
    if (!('data' in error.response.data) && 'message' in error.response.data) {
      alertVisibility.value = true
      alertMessage.value = error.response.data.message

      if (pageContainer.value && alertContainer.value) {
        setTimeout(function () {
          useScrollTo(pageContainer.value, alertContainer.value.$el, 0, 300)
        }, 500)
      }
    }
  }).finally(() => {
    waitingForCancelation.value = false
  })
}

watch(() => props.loadBookingsCounter, () => {
  getEvents()
})

onMounted(() => {
  getEvents()
})

// * Event oraganizer
function eventEmployees (evt) {
  let arr = []
  let employees = store.getters['entities/getEmployees']

  if (evt.organizerId) {
    if(employees.find(item => item.id === evt.organizerId)) {
      let organizer = {...employees.find(item => item.id === evt.organizerId), ...{rank: 'organizer'}}
      if (!evt.providers.length) return organizer
      arr.push(organizer)
    }
  }

  if (evt.providers.length) {
    evt.providers.forEach((pro) => {
      if(pro.id !== evt.organizerId && employees.find(item => item.id === pro.id)) {
        arr.push(employees.find(item => item.id === pro.id))
      }
    })
  }

  return arr
}

// * No Show Function
function noShowData(noShowCount) {
  if (noShowCount) {
    if (noShowCount === 1) {
      return {
        class: 'am-no-show-1',
        icon: 'no-show-1',
      }
    }
    if (noShowCount === 2) {
      return {
        class: 'am-no-show-2',
        icon: 'no-show-2',
      }
    }

    if (noShowCount > 2) {
      return {
        class: 'am-no-show-3',
        icon: 'no-show-3',
      }
    }
  }

  return {
    class: '',
    icon: '',
  }
}
provide('noShowData', { noShowData })

// * Qr Scanner
let qrScannerVisibility = ref(false)

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-cape-bgr': amColors.value.colorMainBgr,
    '--am-c-cape-text': amColors.value.colorMainText,
    '--am-c-cape-text-op70': useColorTransparency(amColors.value.colorMainText, 0.7),
    '--am-c-cape-text-op25': useColorTransparency(amColors.value.colorMainText, 0.25),
    '--am-c-cape-primary': amColors.value.colorPrimary,

    '--am-c-cust-no1': amColors.value.colorMainText,
    '--am-c-cust-no1-bgr': useColorTransparency(amColors.value.colorMainText, 0.1),
    '--am-c-cust-no2': amColors.value.colorWarning,
    '--am-c-cust-no2-bgr': useColorTransparency(amColors.value.colorWarning, 0.1),
    '--am-c-cust-no3': amColors.value.colorError,
    '--am-c-cust-no3-bgr': useColorTransparency(amColors.value.colorError, 0.1),
    '--am-c-cust-text': amColors.value.colorMainText,
    '--am-c-cust-link': useColorTransparency(amColors.value.colorMainText, 0.5),
  }
})
</script>

<script>
export default {
  name: 'CabinetEvents',
  key: 'events'
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
        background-image: linear-gradient(180deg, transparent, transparent 50%, var(--am-c-cape-bgr) 50%, var(--am-c-cape-bgr) 100%), linear-gradient(180deg, var(--am-c-cape-text-op25), var(--am-c-cape-text-op25), var(--am-c-cape-text-op25), var(--am-c-cape-text-op25));
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
        &:before, &:after {
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

    &__alert {
      margin: 0 0 16px;
      .el-alert {
        padding: 4px 12px 4px 0;
        box-sizing: border-box;

        &__content {
          .el-alert__closebtn {
            top: 50%;
            transform: translateY(-50%);
          }
        }

        &__title {
          display: flex;
          align-items: center;
          font-size: 16px;
          line-height: 1.5;

          .am-icon-checkmark-circle-full {
            font-size: 28px;
            line-height: 1;
            color: var(--am-c-alerts-bgr);
          }
        }
      }
    }
  }

  .am-fs__main-content.am-cap {
    //  am    - amelia
    //  capei - cabinet panel event item
    &.am-capei-main {
      height: calc(100% - 148px);
    }

    &.am-cape-main {
      padding: 0;

      .am-cape-main__inner {
        display: block;
        padding: 16px 32px;

        &.am-rw- {
          &480 {
            padding: 16px;
          }
        }
      }
    }
  }

  // Customer
  .am-capei-customer {
    &__name {
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: 18px;
      font-weight: 500;
      line-height: 1.555556;
      color: var(--am-c-cust-text);

      &[class*='am-no-show-'] {
        border: 1px solid var(--am-c-no-show);
        padding: 0 8px 0 4px;
        border-radius: 6px;
        background-color: var(--am-c-no-show-bgr);
      }

      [class^='am-icon-'] {
        font-size: 28px;
        line-height: 1;
        color: var(--am-c-no-show);
      }

      &.am-no-show-1 {
        --am-c-no-show: var(--am-c-cust-no1);
        --am-c-no-show-bgr: var(--am-c-cust-no1-bgr);
      }

      &.am-no-show-2 {
        --am-c-no-show: var(--am-c-cust-no2);
        --am-c-no-show-bgr: var(--am-c-cust-no2-bgr);
      }

      &.am-no-show-3 {
        --am-c-no-show: var(--am-c-cust-no3);
        --am-c-no-show-bgr: var(--am-c-cust-no3-bgr);
      }
    }

    // Customer Email and Phone
    &__data {
      display: flex;
      align-items: center;
      gap: 0 4px;

      &.am-rw {
        &-420 {
          width: 100%;
        }
      }

      &-wrapper {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 8px;

        &.am-rw {
          &-420 {
            flex-wrap: wrap;
            gap: 4px;
          }
        }
      }

      a {
        font-size: 14px;
        font-weight: 400;
        line-height: 1;
        color: var(--am-c-cust-link);
        text-decoration: none;
      }

      [class^='am-icon-'] {
        font-size: 28px;
        line-height: 1;
        color: var(--am-c-cust-text);
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-events;
}
</style>
