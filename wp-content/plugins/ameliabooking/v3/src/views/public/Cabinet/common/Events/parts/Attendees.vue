<template>
  <div
    ref="attendeesContainer"
    class="am-capei-att"
    :style="cssVars"
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

    <div v-if="!eventAttendeeVisibility" class="am-capei-att__top">
      <div class="am-capei-att__top-heading">
        <div class="am-capei-att__top-text">
          {{ props.event.name }}
        </div>
        <div v-if="maxCount" class="am-capei-att__top-places">
          {{ places }} {{ placesLabels }}
        </div>
      </div>

      <AmButton
        category="secondary"
        type="plain"
        size="small"
        class="am-capei-att__back"
        @click="emits('close')"
      >
        {{ amLabels.back }}
      </AmButton>
    </div>

    <div
      v-if="!eventAttendeeVisibility"
      class="am-capei-att__header"
      :class="props.responsiveClass"
    >
      <AmSelect
        v-model="customerId"
        filterable
        clearable
        collapse-tags
        collapse-tags-tooltip
        remote
        prefix-icon="search"
        prefix-icon-color="var(--am-c-capei-att-text)"
        popper-class="am-cap-cust__popper"
        :placeholder="amLabels.event_attendees_search"
        :remote-method="
          (val) => {
            useSearchCustomers(store, val)
          }
        "
        :loading="searchingCustomers"
        @focus="useFocusCustomers(store)"
        @change="
          getBookings(
            activeTab === 'approved' ? ['approved', 'canceled'] : ['waiting'],
            1
          )
        "
      >
        <AmOption
          v-for="item in customers"
          :key="item.id"
          :value="item.id"
          :label="`${item.firstName} ${item.lastName}`"
        >
          <div class="am-cap__cust-option">
            <div class="am-cap__cust-option__heading">
              {{ item.firstName + ' ' + item.lastName }}
            </div>
            <div
              v-if="customerEmailVisibility"
              class="am-cap__cust-option__inner"
            >
              {{ item.email }}
            </div>
            <div
              v-if="customerPhoneVisibility"
              class="am-cap__cust-option__inner"
            >
              {{ item.phone }}
            </div>
          </div>
        </AmOption>
      </AmSelect>

      <AmButton
        v-if="amSettings.roles.allowWriteEvents"
        prefix="plus"
        icon="plus"
        :icon-only="props.pageWidth < 540"
        @click="addEventAttendee"
      >
        {{ amLabels.event_add_attendee }}
      </AmButton>
    </div>

    <el-tabs
      v-if="!eventAttendeeVisibility"
      v-model="activeTab"
      @tab-click="tabClick"
    >
      <!-- Attendees -->
      <el-tab-pane :label="amLabels.attendees" name="approved">
        <div v-if="!approvedBookingsLoading" class="am-capei-att__card-wrapper">
          <template v-if="approvedBookingsList.length">
            <div
              v-for="(item, index) in approvedBookingsList"
              :key="index"
              class="am-capei-att__card"
              :class="props.responsiveClass"
            >
              <div
                class="am-capei-att__card-inner"
                :class="props.responsiveClass"
              >
                <div
                  class="am-capei-att__info"
                  :class="props.responsiveClass"
                >
                  <!-- Customer Name -->
                  <div
                    class="am-capei-customer__name"
                    :class="noShowData(0).class"
                  >
                    <span
                      v-if="noShowData(0).icon"
                      :class="`am-icon-${noShowData(0).icon}`"
                    />
                    {{ item.customer.firstName + ' ' + item.customer.lastName }}
                  </div>
                  <!-- /Customer Name -->

                  <div
                    v-if="((item.customer.phone && customerPhoneVisibility) || (item.customer.email && customerEmailVisibility))"
                    class="am-capei-customer__data-wrapper"
                  >
                    <!-- Customer Phone -->
                    <div
                      v-if="item.customer.phone && customerPhoneVisibility"
                      class="am-capei-customer__data"
                    >
                      <span class="am-icon-phone"></span>
                      <a :href="`tel:${item.customer.phone}`">
                        {{ item.customer.phone }}
                      </a>
                    </div>
                    <!-- /Customer Phone -->

                    <!-- Customer Email -->
                    <div
                      v-if="item.customer.email && customerEmailVisibility"
                      class="am-capei-customer__data"
                    >
                      <span class="am-icon-email"></span>
                      <a :href="`mailto:${item.customer.email}`">
                        {{ item.customer.email }}
                      </a>
                    </div>
                    <!-- /Customer Email -->
                  </div>
                </div>

                <AmSelect
                  v-model="item.status"
                  :placeholder="amLabels.select"
                  :disabled="!amSettings.roles.allowWriteEvents"
                  :prefix-icon="
                    attendeeStatuses.find((i) => i.value === item.status).icon
                  "
                  :prefix-icon-color="
                    attendeeStatuses.find((i) => i.value === item.status).color
                  "
                  popper-class="am-capei-att__popper"
                  :parent-class="props.responsiveClass"
                  @change="
                    (changedStatus) => {
                      updateBookingStatus(item.id, changedStatus)
                    }
                  "
                >
                  <AmOption
                    v-for="status in attendeeStatuses"
                    :key="status.value"
                    :value="status.value"
                    :label="status.label"
                  >
                    <span
                      :class="`am-icon-${status.icon}`"
                      :style="{ color: status.color }"
                    />
                    {{ status.label }}
                  </AmOption>
                </AmSelect>
              </div>

              <div
                v-if="item.ticketsData.length"
                class="am-capei-att__card-tickets"
              >
                <div class="am-capei-att__card-label">
                  {{ amLabels.event_book_tickets }}:
                </div>
                <div class="am-capei-att__card-tickets__inner">
                  <div
                    v-for="(ticket, i) in item.ticketsData"
                    :key="i"
                    class="am-capei-att__card-ticket"
                  >
                    {{ ticket.persons }} x
                    {{
                      props.event.customTickets.find(
                        (a) => a.id === ticket.eventTicketId
                      ).name
                    }}
                  </div>
                </div>
              </div>
              <div v-else class="am-capei-att__card-persons">
                <div class="am-capei-att__card-label">
                  {{ amLabels.event_book_persons }}:
                </div>
                <div class="am-capei-att__card-person">
                  {{ item.persons }}
                </div>
              </div>

              <div v-if="amSettings.featuresIntegrations.eTickets.enabled && item.qrCodes" class="am-capei-att__card-etickets">
                <div class="am-capei-att__card-etickets__label">
                  {{ amLabels.checked_in }}:
                </div>
                <div v-for="(val, date) in (getQrCodesBooking(item.qrCodes))" :key="date" class="am-capei-att__card-etickets__item">
                  <span class="am-capei-att__card-etickets__date">{{date}}:</span>
                  <span class="am-capei-att__card-etickets__capacity">{{val}}</span>
                </div>
              </div>

              <DotsPopup
                v-if="amSettings.roles.allowWriteEvents"
                :index="index"
                :reference-class="props.responsiveClass"
                @edit="editEventAttendee(item)"
                @remove="promptRemoveAttendee(item, false)"
              />
            </div>

            <AmPagination
              v-if="
                approvedBookingsCount > amSettings.general.itemsPerPage
              "
              :page-size="amSettings.general.itemsPerPage"
              :pager-count="5"
              layout="prev, pager, next"
              :total="approvedBookingsCount"
              :current-page="approvedBookingsPage"
              @current-change="bookingsPageChange"
            />
          </template>
          <EmptyState v-else :heading="amLabels.no_attendees_yet" />
        </div>
        <Skeleton v-else />
      </el-tab-pane>
      <!-- /Attendees -->

      <!-- Waiting List -->
      <el-tab-pane
        v-if="
          amSettings.featuresIntegrations.waitingList.enabled &&
          eventSettings !== null &&
          'waitingList' in eventSettings &&
          eventSettings.waitingList.enabled
        "
        :label="amLabels.waiting_list"
        name="waiting"
      >
        <div v-if="!waitingBookingsLoading" class="am-capei-att__card-wrapper">
          <template v-if="waitingBookingsList.length">
            <div
              v-for="(item, index) in waitingBookingsList"
              :key="index"
              class="am-capei-att__card"
            >
              <div
                class="am-capei-att__card-inner"
                :class="props.responsiveClass"
              >
                <div
                  class="am-capei-att__info"
                  :class="props.responsiveClass"
                >
                  <!-- Customer Name -->
                  <div
                    class="am-capei-customer__name"
                    :class="noShowData(0).class"
                  >
                    <span
                      v-if="noShowData(0).icon"
                      :class="`am-icon-${noShowData(0).icon}`"
                    />
                    {{ item.customer.firstName + ' ' + item.customer.lastName }}
                  </div>
                  <!-- /Customer Name -->

                  <div
                    v-if="((item.customer.phone && customerPhoneVisibility) || (item.customer.email && customerEmailVisibility))"
                    class="am-capei-customer__data-wrapper"
                  >
                    <!-- Customer Phone -->
                    <div
                      v-if="item.customer.phone && customerPhoneVisibility"
                      class="am-capei-customer__data"
                    >
                      <span class="am-icon-phone"></span>
                      <a :href="`tel:${item.customer.phone}`">
                        {{ item.customer.phone }}
                      </a>
                    </div>
                    <!-- /Customer Phone -->

                    <!-- Customer Email -->
                    <div
                      v-if="item.customer.email && customerEmailVisibility"
                      class="am-capei-customer__data"
                    >
                      <span class="am-icon-email"></span>
                      <a :href="`mailto:${item.customer.email}`">
                        {{ item.customer.email }}
                      </a>
                    </div>
                    <!-- /Customer Email -->
                  </div>
                </div>

                <AmSelect
                  v-model="item.status"
                  :placeholder="amLabels.select"
                  :disabled="!amSettings.roles.allowWriteEvents"
                  :prefix-icon="
                    attendeeStatuses.find((i) => i.value === item.status).icon
                  "
                  :prefix-icon-color="
                    attendeeStatuses.find((i) => i.value === item.status).color
                  "
                  popper-class="am-capei-att__popper"
                  @change="
                    (changedStatus) => {
                      updateBookingStatus(item.id, changedStatus)
                    }
                  "
                >
                  <AmOption
                    v-for="status in attendeeStatuses"
                    :key="status.value"
                    :value="status.value"
                    :label="status.label"
                  >
                    <span
                      :class="`am-icon-${status.icon}`"
                      :style="{ color: status.color }"
                    />
                    {{ status.label }}
                  </AmOption>
                </AmSelect>
              </div>

              <div
                v-if="Object.keys(item.ticketsData).length"
                class="am-capei-att__card-tickets"
              >
                <div class="am-capei-att__card-label">
                  {{ amLabels.event_book_tickets }}:
                </div>
                <div class="am-capei-att__card-tickets__inner">
                  <div
                    v-for="(ticket, i) in item.ticketsData"
                    :key="i"
                    class="am-capei-att__card-ticket"
                  >
                    {{ ticket.persons }} x
                    {{
                      props.event.customTickets.find(
                        (a) => a.id === ticket.eventTicketId
                      ).name
                    }}
                  </div>
                </div>
              </div>
              <div v-else class="am-capei-att__card-persons">
                <div class="am-capei-att__card-label">
                  {{ amLabels.event_book_persons }}:
                </div>
                <div class="am-capei-att__card-person">
                  {{ item.persons }}
                </div>
              </div>

              <DotsPopup
                v-if="amSettings.roles.allowWriteEvents"
                :index="index"
                :reference-class="props.responsiveClass"
                @edit="editEventAttendee(item)"
                @remove="promptRemoveAttendee(item, true)"
              />
            </div>

            <AmPagination
              v-if="
                waitingBookingsCount > amSettings.general.itemsPerPage
              "
              :page-size="amSettings.general.itemsPerPage"
              :pager-count="5"
              layout="prev, pager, next"
              :total="waitingBookingsCount"
              :current-page="waitingBookingsPage"
              @current-change="bookingsPageChange"
            />
          </template>
          <EmptyState v-else :heading="amLabels.no_attendees_yet" />
        </div>
        <Skeleton v-else />
      </el-tab-pane>
      <!-- /Waiting List -->
    </el-tabs>

    <CancelPopup
      :visibility="removeDialogVisible"
      :title="amLabels.confirm"
      :description="amLabels.event_attendee_remove"
      :close-btn-text="amLabels.no"
      :confirm-btn-text="amLabels.yes"
      :customized-options="{
        cancelBtn: { buttonType: 'plain' },
        confirmBtn: { buttonType: 'filled' },
      }"
      @decline="declineRemoveAttendee"
      @confirm="removeEventAttendee"
      @close="declineRemoveAttendee"
    >
    </CancelPopup>

    <Attendee
      v-if="eventAttendeeVisibility"
      :visibility="eventAttendeeVisibility"
      :title="
        editAttendeeRecognition
          ? amLabels.event_edit_attendee
          : amLabels.event_add_attendee
      "
      :event="props.event"
      :page-width="props.pageWidth"
      :is-new="!editAttendeeRecognition"
      @save="savedAttendee"
      @close="closedAttendee"
    ></Attendee>
  </div>
</template>

<script setup>
// * Import from Vue
import { ref, inject, onMounted, computed } from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Import from Library
import httpClient from '../../../../../../plugins/axios'


// * Composables
import { useAuthorizationHeaderObject } from '../../../../../../assets/js/public/panel'
import { useUrlParams } from '../../../../../../assets/js/common/helper'
import { useColorTransparency } from '../../../../../../assets/js/common/colorManipulation'
import {
  useFocusCustomers,
  useSearchCustomers,
} from '../../../../../../assets/js/admin/customers'
import { useScrollTo } from '../../../../../../assets/js/common/scrollElements'
import {
  useAttendeeStatuses,
  useBackAttendee,
  useFrontAttendee,
  useInitAttendee,
} from '../../../../../../assets/js/common/attendees'

// * Components
import AmButton from '../../../../../_components/button/AmButton.vue'
import AmOption from '../../../../../_components/select/AmOption.vue'
import AmSelect from '../../../../../_components/select/AmSelect.vue'
import DotsPopup from '../../../../Parts/DotsPopup.vue'
import Skeleton from '../../Authentication/parts/Skeleton.vue'
import AmPagination from '../../../../../_components/pagination/AmPagination.vue'
import EmptyState from '../../parts/EmptyState.vue'
import Attendee from './Attendee.vue'
import CancelPopup from '../../parts/CancelPopup.vue'
import AmAlert from "../../../../../_components/alert/AmAlert.vue";

// * Component properties
let props = defineProps({
  responsiveClass: {
    type: String,
    default: '',
  },
  pageWidth: {
    type: Number,
  },
  event: {
    type: Object,
    default: () => {},
  },
})

let store = useStore()

// * Components emits
let emits = defineEmits(['close', 'openAttendee'])

// * Labels
let amLabels = inject('amLabels')

// * Root Settings
const amSettings = inject('settings')

// * Data in shortcode
const shortcodeData = inject('shortcodeData')

// * Customize data
let amCustomize = inject('amCustomize')

// * Customized options
let customerPhoneVisibility = computed(() => shortcodeData.value.cabinetType === 'employee' ? amCustomize.value.events.options.customerPhone.visibility : true)
let customerEmailVisibility = computed(() => shortcodeData.value.cabinetType === 'employee' ? amCustomize.value.events.options.customerEmail.visibility : true)

// * Alert block
let alertContainer = ref(null)
let alertVisibility = ref(false)
let alertType = ref('success')

let alertMessage = ref('')

function closeAlert () {
  alertVisibility.value = false
  store.commit('cabinet/setPaymentLinkError', {value: false, type: 'event'})
}

let eventAttendeeVisibility = ref(false)

let editAttendeeRecognition = ref(false)

let approvedBookingsLoading = ref(false)

let approvedBookingsList = ref([])

let approvedBookingsCount = ref(0)

let approvedBookingsPage = ref(1)

let waitingBookingsLoading = ref(false)

let waitingBookingsList = ref([])

let waitingBookingsCount = ref(0)

let waitingBookingsPage = ref(1)

let eventSettings = computed(() => {
  return props.event.settings ? JSON.parse(props.event.settings) : null
})

// * No show
const { noShowData } = inject('noShowData')

let removeDialogVisible = ref(false)

let targetAttendee = ref(null)

let targetAttendeeWaiting = ref(false)

function declineRemoveAttendee() {
  targetAttendee.value = null

  removeDialogVisible.value = false
}

function promptRemoveAttendee(item, isWaiting) {
  targetAttendee.value = item

  targetAttendeeWaiting.value = isWaiting

  removeDialogVisible.value = true
}

// * Page Content width
let attendeesContainer = ref(null)

function savedAttendee () {
  store.commit('attendee/setActive', false)

  alertVisibility.value = true
  alertMessage.value = amLabels.value.event_attendee_saved

  if (attendeesContainer.value && alertContainer.value) {
    setTimeout(function () {
      useScrollTo(attendeesContainer.value, alertContainer.value.$el, 0, 300)
    }, 500)
  }

  getBookings(activeTab.value === 'approved' ? ['approved', 'canceled'] : ['waiting'])

  closedAttendee()
}

function closedAttendee() {
  eventAttendeeVisibility.value = false
  editAttendeeRecognition.value = false
  emits('openAttendee', false)
}

function addEventAttendee() {
  store.commit(
    'attendee/setAttendee',
    useInitAttendee(
      store,
      store.getters['attendee/getDefaultAttendee'],
      props.event
    )
  )

  eventAttendeeVisibility.value = true
  emits('openAttendee', true)
}

function editEventAttendee(attendee) {
  let customers = store.getters['customerInfo/getCustomers']

  let customersIds = store.getters['customerInfo/getCustomersIds']

  if (customers.filter((i) => i.id === attendee.customer.id).length === 0) {
    customers.push(attendee.customer)

    store.commit('customerInfo/setCustomers', customers)
  }

  if (customersIds.indexOf(attendee.customer.id) === -1) {
    customersIds.push(attendee.customer.id)

    store.commit('customerInfo/setCustomersIds', customersIds)
  }

  store.commit(
    'attendee/setAttendee',
    useInitAttendee(store, attendee, props.event)
  )

  editAttendeeRecognition.value = true
  eventAttendeeVisibility.value = true
  emits('openAttendee', true)
}

/********
 * Tabs *
 ********/
const activeTab = ref('approved')

function tabClick() {
  switch (activeTab.value) {
    case 'approved':
      approvedBookingsPage.value = 1

      break
    case 'waiting':
      waitingBookingsPage.value = 1

      break
    default:
  }

  getBookings(
    activeTab.value === 'approved' ? ['approved', 'canceled'] : ['waiting']
  )
}

let customerId = ref(null)

let searchingCustomers = computed(
  () => store.getters['customerInfo/getLoading']
)

let customers = computed(() => store.getters['customerInfo/getCustomers'])

function getBookings(statuses, page = 1) {
  if (statuses[0] === 'approved') {
    approvedBookingsLoading.value = true
  } else {
    waitingBookingsLoading.value = true
  }

  httpClient
    .get(
      '/bookings/events',
      Object.assign(
        useAuthorizationHeaderObject(store),
        {
          params: useUrlParams(
            {
              source: 'cabinet-provider',
              statuses: statuses,
              page: page,
              limit: parseInt(amSettings.general.itemsPerPage),
              events: [props.event.id],
              customers: customerId.value ? [customerId.value] : [],
            }
          ),
        }
      )
    )
    .then((response) => {
      if (statuses[0] === 'approved') {
        bookedCount.value = response.data.data.attendeeCount
        maxCount.value = response.data.data.maxCapacity

        approvedBookingsList.value = response.data.data.bookings
          .filter((i) => i.status === 'approved' || i.status === 'canceled')
          .reduce((actual, current) => {
            actual.push(
              useInitAttendee(
                store,
                useFrontAttendee(
                  current,
                  store.getters['attendee/getAttendee']
                ),
                props.event
              )
            )

            return actual
          }, [])

        approvedBookingsCount.value = response.data.data.filteredCount
      } else {
        bookedCount.value = response.data.data.waitingCount
        maxCount.value = response.data.data.waitingCapacity

        waitingBookingsList.value = response.data.data.bookings
          .filter((i) => i.status === 'waiting')
          .reduce((actual, current) => {
            actual.push(useFrontAttendee(current))

            return actual
          }, [])

        waitingBookingsCount.value = response.data.data.filteredCount
      }
    })
    .catch((e) => {
      console.log(e)
    })
    .finally(() => {
      approvedBookingsLoading.value = false

      waitingBookingsLoading.value = false
    })
}

function updateBookingStatus(id, status) {
  httpClient
    .post(
      '/events/bookings/' + id,
      {
        status: status,
        bookings: [{ status: status }],
      },
      Object.assign(useAuthorizationHeaderObject(store), {
        params: { source: 'cabinet-provider' },
      })
    )
    .then(() => {})
    .catch((e) => {
      console.log(e)
    })
    .finally(() => {})
}

function bookingsPageChange(page) {
  let statuses =
    activeTab.value === 'approved' ? ['approved', 'canceled'] : ['waiting']

  if (statuses[0] === 'approved') {
    approvedBookingsPage.value = page
  } else {
    waitingBookingsPage.value = page
  }

  getBookings(statuses, page)
}

function removeEventAttendee() {
  removeDialogVisible.value = false

  if (targetAttendeeWaiting.value) {
    waitingBookingsLoading.value = true
  } else {
    approvedBookingsLoading.value = true
  }

  httpClient
    .post(
      '/events/bookings/delete/' + targetAttendee.value.id,
      useBackAttendee(store),
      Object.assign(
        useAuthorizationHeaderObject(store),
        {
          params: {
            source: 'cabinet-provider',
          },
        }
      )
    )
    .then(() => {
      bookingsPageChange(approvedBookingsPage.value)
    })
    .catch(() => {
      waitingBookingsLoading.value = false
      approvedBookingsLoading.value = false
    })
}

let attendeeStatuses = ref(useAttendeeStatuses().filter(
    i => (
      !amSettings.featuresIntegrations.waitingList.enabled ||
      (
        eventSettings.value
          ? 'waitingList' in eventSettings.value && !eventSettings.value.waitingList.enabled
          : true
      ) ? i.value !== 'waiting' : true
    ) && (
      !amSettings.featuresIntegrations.noShowTag.enabled ? i.value !== 'no-show' : true
    )
  )
)

let bookedCount = ref(0)

let maxCount = ref(0)

// * Capacity of attendees
let places = computed(() => {
  return `${bookedCount.value}/${maxCount.value}`
})

let placesLabels = computed(() => {
  return bookedCount.value === 1
    ? amLabels.value.attendee
    : amLabels.value.attendees
})

function getQrCodesBooking (qrCodes) {
  if (!qrCodes) {
    return {}
  }

  let tickets = []

  try {
    tickets = JSON.parse(qrCodes)
  } catch (error) {
    return {}
  }

  if (!Array.isArray(tickets)) {
    return {}
  }

  const periodDates = new Set()

  if (!Array.isArray(props.event?.periods)) {
    return {}
  }

  props.event.periods.forEach(period => {
    if (!period?.periodStart || !period?.periodEnd) {
      return
    }

    const startDate = new Date(period.periodStart.split(' ')[0])
    const endDate = new Date(period.periodEnd.split(' ')[0])

    if (Number.isNaN(startDate.getTime()) || Number.isNaN(endDate.getTime())) {
      return
    }

    const currentDate = new Date(startDate)

    while (currentDate <= endDate) {
      periodDates.add(currentDate.toISOString().split('T')[0])
      currentDate.setDate(currentDate.getDate() + 1)
    }
  })

  const eDates = {}

  periodDates.forEach(date => {
    eDates[date] = { scanned: 0, total: 0 }

    tickets.forEach(ticket => {
      if (ticket.type !== 'booking') {
        eDates[date].total += 1

        if (ticket.dates && ticket.dates[date]) {
          eDates[date].scanned += 1
        }
      }
    })
  })

  return Object.fromEntries(
    Object.entries(eDates)
      .sort(([dateA], [dateB]) => dateA.localeCompare(dateB))
      .map(([date, counts]) => [date, `${counts.scanned}/${counts.total}`])
  )
}

onMounted(() => {
  getBookings(['approved', 'canceled'])

  store.commit('customerInfo/setCustomers', store.getters['auth/getPreloadedCustomers'])
})

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-capei-att-primary': amColors.value.colorPrimary,
    '--am-c-capei-att-text': amColors.value.colorMainText,
    '--am-c-capei-att-text-op90': useColorTransparency(
      amColors.value.colorMainText,
      0.9
    ),
    '--am-c-capei-att-text-op10': useColorTransparency(
      amColors.value.colorMainText,
      0.1
    ),
  }
})
</script>

<script>
export default {
  name: 'CabinetEventAttendees',
}
</script>

<style lang="scss">
@mixin cabinet-event-attendees-block {
  // am      - amelia
  // capei   - cabinet-panel-event-item
  // att     - attendee
  .am-capei-att {
    &__top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      margin-bottom: 20px;

      &-heading {
        display: flex;
        flex-direction: column;
        gap: 4px 8px;
      }

      &-text {
        font-size: 18px;
        line-height: 1.55;
        color: var(--am-c-capei-att-text);
      }

      &-places {
        font-size: 15px;
        font-weight: 400;
        line-height: 1.6;
        color: var(--am-c-capei-att-text-op90);
      }
    }

    &__header {
      display: flex;
      justify-content: space-between;
      width: 100%;
      margin-bottom: 20px;
      gap: 8px 16px;

      &.am-rw-540 {
        gap: 8px;
      }

      .am-icon-plus {
        font-size: 24px;
        line-height: 1;
      }
    }

    // Customer Info
    &__info {
      flex: 0 0 auto;
      display: flex;
      flex-direction: column;
      gap: 8px 4px;
      width: calc(100% - 224px);
      align-items: flex-start;

      &.am-rw {
        &-520 {
          width: calc(100% - 24px);
          order: 0;
        }
      }

      &-inner {
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .am-capei-customer {
        &__data-wrapper {
          flex-wrap: wrap;
        }
      }
    }

    // Card
    &__card {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      padding: 16px;
      border: 1px solid var(--am-c-inp-border);
      border-radius: 6px;
      gap: 16px 0;

      &-wrapper {
        display: flex;
        flex-direction: column;
        gap: 16px;
      }

      &-inner {
        width: calc(100% - 24px);
        display: flex;
        align-items: center;

        &.am-rw {
          &-520 {
            flex-wrap: wrap;
            gap: 16px 0;
          }
        }
      }

      &-tickets {
        order: 2;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        width: calc(100% - 24px);

        &__inner {
          display: flex;
          flex-wrap: wrap;
          gap: 8px;
          width: 100%;
        }
      }

      &-ticket {
        font-size: 14px;
        font-weight: 400;
        line-height: 20px;
        color: var(--am-c-capei-att-text);
        background-color: var(--am-c-capei-att-text-op10);
        padding: 4px 8px;
        border-radius: 4px;
      }

      &-persons {
        order: 2;
        display: flex;
        align-items: center;
        gap: 8px;
        width: calc(100% - 24px);
      }

      &-person {
        font-size: 14px;
        font-weight: 400;
        line-height: 1.6;
        color: var(--am-c-capei-att-text);
      }

      &-label {
        font-size: 14px;
        font-weight: 400;
        line-height: 1.6;
        color: var(--am-c-capei-att-text);
      }

      &-etickets {
        order: 3;
        display: flex;
        flex-direction: column;
        gap: 4px 0;
        width: calc(100% - 24px);

        &__label {
          font-size: 14px;
          font-weight: 400;
          line-height: 1.6;
          color: var(--am-c-capei-att-text);
        }

        &__item {
          display: flex;
          gap: 0 8px;
        }

        &__date {
          font-size: 14px;
          font-weight: 400;
          line-height: 1.6;
          color: var(--am-c-capei-att-text);
        }

        &__capacity {
          font-size: 14px;
          font-weight: 600;
          line-height: 1.6;
          color: var(--am-c-capei-att-text);
        }
      }
    }

    // Status Select
    .am-select {
      &-wrapper {
        &.am-rw {
          &-520 {
            max-width: 100%;
            width: 100%;
            order: 2;
          }
        }
      }

      .el-input__prefix {
        left: 6px;

        [class^='am-icon-'] {
          font-size: 24px;
          line-height: 0;
        }
      }
    }

    .am-cc__edit-btn {
      order: 1;
      color: var(--am-c-capei-att-text);

      &.am-rw {
        &-520 {
          order: 1;
          align-self: flex-start;
        }
      }
    }
  }
}

.am-capei-att__popper {
  [class^='am-icon-'] {
    font-size: 24px;
    line-height: 0;
  }

  .am-icon-info-reverse, .am-icon-close, .am-icon-refresh, .am-icon-check {
    font-size: 18px;
    padding: 3px;
  }

  .el-select-dropdown__item {
    display: flex;
    align-items: center;
    gap: 0 8px;
  }
}

.amelia-v2-booking #amelia-container {
  @include cabinet-event-attendees-block;
}
</style>
