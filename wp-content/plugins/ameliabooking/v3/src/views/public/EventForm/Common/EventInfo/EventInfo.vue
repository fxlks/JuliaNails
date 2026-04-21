<template>
  <!-- Event List (elf) -->
  <div
    v-if="!loading"
    class="am-eli"
    :class="props.globalClass"
    :style="cssVars"
  >
    <GalleryCarousel
      v-if="selectedEvent.gallery.length && customizedOptions.gallery.visibility"
      :gallery="selectedEvent.gallery"
      class="am-eli__image"
    ></GalleryCarousel>

    <div class="am-eli__header">
     <EventCard
       :event="selectedEvent"
       :labels="amLabels"
       :locations="locations"
       :tickets="tickets"
       :customized-options="customizedOptions"
       :date-string-visibility="true"
       :border-visibility="false"
       :btn-visibility="false"
       :auto-info-height="true"
       :image-visibility="false"
       vertical-orientation="start"
       :in-header="true"
       :in-dialog="true"
       :tax-visible="useTaxVisibility(store, selectedEvent.id, 'event') && (customizedOptions.tax?.visibility ?? true)"
     ></EventCard>
    </div>

    <div class="am-eli__main">
      <el-tabs v-model="activeName">
        <el-tab-pane
          :label="amLabels.event_info"
          name="first"
        >
          <div
            v-if="selectedEvent.periods.length"
            class="am-eli__timetable am-eli__main-item"
          >
            <p class="am-eli__timetable-title am-eli__main-title">
              {{ amLabels.event_timetable }}
              <span v-if="eventRange.start !== eventRange.end">
                {{ `: ${eventRange.start} - ${eventRange.end}` }}
              </span>
            </p>
            <p
              v-for="period in selectedEventsPeriods"
              :key="period.id"
              class="am-eli__timetable-main"
            >
              <span class="am-eli__timetable-main__date am-eli__main-text">
                {{ period.dateOfMonth }}
              </span>
              <span class="am-eli__timetable-main__time am-eli__main-text">
                {{ ` - ${period.timeStart} - ${period.timeEnd}` }}
              </span>
            </p>
          </div>

          <DescriptionItem
            v-if="customizedOptions.eventDescription.visibility"
            class="am-eli__main-item"
            :title="amLabels.description"
            :description="selectedEvent.description"
            :limit="250"
            :customized-labels="amLabels"
          ></DescriptionItem>

          <EventEmployee
            v-if="customizedOptions.eventOrganizer.visibility"
            :divider="true"
            :employee="eventOrganizer"
            :rank="amLabels.event_organizer"
            :customized-labels="amLabels"
            class="am-eli__main-item"
          ></EventEmployee>

          <template v-if="customizedOptions.eventEmployees.visibility">
            <EventEmployee
              v-for="employee in eventStaff"
              :key="employee.id"
              :divider="true"
              :employee="employee"
              :customized-labels="amLabels"
              class="am-eli__main-item"
            ></EventEmployee>
          </template>
        </el-tab-pane>
        <el-tab-pane
          v-if="tickets.length"
          :label="amLabels.event_tickets"
          name="second"
        >
          <template
            v-for="ticket in tickets"
            :key="ticket.id"
          >
            <EventTicket
              :ticket="ticket"
              :capacity="!isWaitingList ? selectedEvent.maxCustomCapacity : selectedEvent.maxCustomCapacity ? waitingListOptions.maxCapacity : null"
              :extra-people="!isWaitingList ? selectedEvent.maxExtraPeople : waitingListOptions.maxExtraPeopleEnabled ? waitingListOptions.maxExtraPeople : null"
              :customized-labels="amLabels"
              :readonly="true"
            />
          </template>
        </el-tab-pane>
      </el-tabs>
    </div>
  </div>

  <EventInfoSkeleton v-else></EventInfoSkeleton>

  <!-- Bringing Anyone with you -->
  <AmSlidePopup
    v-if="bringingAnyoneAvailability"
    :visibility="bringingAnyoneVisibility"
    custom-class="am-eli__bringing-wrapper"
  >
    <BringingAnyone></BringingAnyone>

    <template #footer>
      <div
        class="am-eli__bringing-footer"
        :class="responsiveClass"
      >
        <AmButton
          :type="amDialogCustomizedOptions.secBtn.buttonType"
          category="secondary"
          :disabled="false"
          @click="noOneBringWith"
        >
          {{ amDialogLabels.back_btn }}
        </AmButton>
        <AmButton
          :type="amDialogCustomizedOptions.primBtn.buttonType"
          :category="isWaitingList ? 'waiting' : 'primary'"
          :disabled="false"
          @click="bringPeopleWithYou"
        >
          {{ amDialogLabels.continue }}
        </AmButton>
      </div>
    </template>
  </AmSlidePopup>
  <!--/ Bringing Anyone with you -->
</template>

<script setup>
// * _components
import AmButton from "../../../../_components/button/AmButton.vue";
import AmSlidePopup from "../../../../_components/slide-popup/AmSlidePopup.vue";

// * Dedicated Components
import EventCard from "../../Common/Parts/EventCard.vue";
import DescriptionItem from "../../Common/Parts/DescriptionItem.vue";
import EventEmployee from "../../Common/Parts/EventEmployee.vue";
import EventInfoSkeleton from "./EventInfoSkeleton.vue";

// * Parts
import EventTicket from "../../Common/Parts/EventTicket.vue";
import GalleryCarousel from "../../Common/Parts/GalleryCarousel.vue";

// * Dialogs
import BringingAnyone from "../../Common/Parts/BringingAnyone.vue";

// * Import from Vue
import {
  inject,
  ref,
  reactive,
  computed,
  onMounted,
  watchEffect
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Composables
import {
  getFrontedFormattedDate,
  getEventFrontedFormattedTime
} from "../../../../../assets/js/common/date";
import { useColorTransparency } from "../../../../../assets/js/common/colorManipulation";
import { useResponsiveClass } from "../../../../../assets/js/common/responsive";
import { useTaxVisibility } from "../../../../../assets/js/common/pricing";
import useAction from '../../../../../assets/js/public/actions'
import moment from "moment/moment";

// * Components Properties
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
  headerButtonPreviousReset,
  footerButtonReset,
  footerButtonClicked,
  footerBtnDisabled
} = inject('changingStepsFunctions', {
  nextStep: () => {},
  headerButtonPreviousReset: () => {},
  footerButtonReset: () => {},
  footerButtonClicked: ref(false),
  footerBtnDisabled: ref(false)
})

// * Define store
const store = useStore()

// * Loading state
let loading = computed(() => store.getters['getLoading'])

// * Root Settings
const amSettings = inject('settings')

// * Event form customization
let customizedDataForm = inject('customizedDataForm')

// * Customization options
let customizedOptions = computed(() => {
  return customizedDataForm.value.info.options
})

// * Labels
let labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  if (customizedDataForm.value.info.translations) {
    let customizedLabels = customizedDataForm.value.info.translations
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

let amDialogLabels = computed(() => {
  let computedLabels = reactive({...labels})

  if (customizedDataForm.value.bringingAnyone.translations) {
    let customizedLabels = customizedDataForm.value.bringingAnyone.translations
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

let amDialogCustomizedOptions = computed(() => {
  return customizedDataForm.value.bringingAnyone.options
})

// * Event obj
let selectedEvent = computed(() => store.getters['eventEntities/getEvent'](store.getters['eventBooking/getSelectedEventId']))

let isWaitingList = computed(() => store.getters['eventWaitingListOptions/getAvailability'])

let waitingListOptions = computed(() => store.getters['eventWaitingListOptions/getOptions'])

let locations = computed(() => store.getters['eventEntities/getLocations'])

let employees = computed(() => store.getters['eventEntities/getEmployees'])

let selectedEventsPeriods = computed(() => {
  let periodsArr = []
  selectedEvent.value.periods.forEach(function (period) {
    let periodStartDate = moment(period.periodStart.split(' ')[0], 'YYYY-MM-DD')
    let periodEndDate = moment(period.periodEnd.split(' ')[0], 'YYYY-MM-DD')
    let periodStartTime = moment(period.periodStart.split(' ')[1], 'HH:mm:ss').format('HH:mm:ss')
    let periodEndTime = moment(period.periodEnd.split(' ')[1], 'HH:mm:ss').format('HH:mm:ss')

    if (periodEndTime === '00:00:00') {
      periodEndTime = '24:00:00'
      periodEndDate.subtract(1, 'days')
    }

    /** if the period in the event lasts for several days */
    if (periodStartDate.diff(periodEndDate, 'days') < 0) {
      let periodDates = []

      while (periodStartDate.isSameOrBefore(periodEndDate)) {
        periodDates.push(periodStartDate.format('YYYY-MM-DD'))
        periodStartDate.add(1, 'days')
      }

      periodDates.forEach(dayPeriod => {
        periodsArr.push({
          id: period.id,
          start: dayPeriod + ' ' + periodStartTime,
          end: dayPeriod + ' ' + periodEndTime
        })
      })
    } else {
      periodsArr.push({
        id: period.id,
        start: periodStartDate.format('YYYY-MM-DD') + ' ' + periodStartTime,
        end: periodEndDate.format('YYYY-MM-DD') + ' ' + periodEndTime
      })
    }
  })

  let periods = []
  periodsArr.sort((a, b) => moment(a.start, 'YYYY-MM-DD HH:mm:ss') - moment(b.start, 'YYYY-MM-DD HH:mm:ss'))
    .forEach(item => {
      periods.push({
        dateOfMonth: getFrontedFormattedDate(item.start.split(' ')[0]),
        timeStart: getEventFrontedFormattedTime(item.start.split(' ')[1]),
        timeEnd: getEventFrontedFormattedTime(item.end.split(' ')[1])
      })
    })

  return periods
})

let eventRange = computed(() => {
  let rangeArr = []
  let rangeObj = {}

  selectedEvent.value.periods.forEach(item => {
    rangeArr.push(
      moment(item.periodStart, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD'),
      moment(item.periodEnd, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD')
    )
  })

  rangeArr = rangeArr.sort()
  rangeObj.start = getFrontedFormattedDate(rangeArr[0])
  rangeObj.end = getFrontedFormattedDate(rangeArr[rangeArr.length - 1])

  return rangeObj
})

const activeName = ref('first')

// * Footer btn
watchEffect(() => {
  footerBtnDisabled.value = !isWaitingList.value && (!selectedEvent.value.bookable || selectedEvent.value.full)
}, {flush: 'post'})

// * Event fluid step keys
let eventFluidStepKey = inject('eventFluidStepKey')

// * add or remove event tickets step
if (selectedEvent.value.customPricing) {
  if (eventFluidStepKey.value.indexOf('eventTickets') < 0) {
    eventFluidStepKey.value.push('eventTickets')
  }
} else {
  let index = eventFluidStepKey.value.indexOf('eventTickets')
  if (index > -1) {
    eventFluidStepKey.value.splice(index, 1)
  }
}

// * Event Organizer and Staff
let eventOrganizer = computed(() => {
  let employeeId = selectedEvent.value.organizerId
  if (employeeId && employees.value.find(e => e.id === employeeId)) {
    return employees.value.find(e => e.id === employeeId)
  }

  return {}
})

let eventStaff = computed(() => {
  let filtered = []
  selectedEvent.value.providers.forEach(emp => {
    if (employees.value.some(e => e.id === emp.id)) {
      emp.badge = employees.value.find(empl => empl.id === emp.id).badge
      filtered.push(emp)
    }
  })

  if (selectedEvent.value.organizerId) {
    return filtered.filter(e => e.id !== selectedEvent.value.organizerId)
  }

  return filtered
})

// * Set max people based on settings from backend
let evCapacity = computed(() => selectedEvent.value.maxCapacity - (selectedEvent.value.maxCapacity - selectedEvent.value.places))

// * Set max waiting people based on settings from backend
let evWaitingCapacity = computed(() => {
  if (!waitingListOptions.value.enabled) {
    return 0
  }
  return waitingListOptions.value.maxCapacity - waitingListOptions.value.peopleWaiting
})

watchEffect(() => {
  if (isWaitingList.value) {
    if (waitingListOptions.value.maxExtraPeopleEnabled
      && waitingListOptions.value.maxExtraPeople
      && evWaitingCapacity.value > waitingListOptions.value.maxExtraPeople) {
      store.commit('persons/setMaxPersons', (waitingListOptions.value.maxExtraPeople + 1))
    } else {
      store.commit('persons/setMaxPersons', evWaitingCapacity.value)
    }
  } else {
    if (selectedEvent.value.maxExtraPeople && evCapacity.value > selectedEvent.value.maxExtraPeople) {
      store.commit('persons/setMaxPersons', (selectedEvent.value.maxExtraPeople + 1))
    } else {
      store.commit('persons/setMaxPersons', evCapacity.value)
    }
  }
}, {flush: 'post'})

// * Bringing anyone with you slide popup
let bringingAnyoneAvailability = computed(() => {
  const commonCondition = !selectedEvent.value.customPricing
    && selectedEvent.value.bringingAnyone
    && (store.getters['persons/getMinPersons'] !== store.getters['persons/getMaxPersons'])

  if (isWaitingList.value) {
    return commonCondition && evWaitingCapacity.value > 1
  } else {
    return commonCondition && evCapacity.value > 1
  }
})

let bringingAnyoneVisibility = ref(false)

function noOneBringWith() {
  bringingAnyoneVisibility.value = false
}

function bringPeopleWithYou() {
  nextStep()
}

// * Event Tickets
watchEffect(() => {
  if (selectedEvent.value.customPricing && !store.getters['tickets/getTicketsData'].length) {
    store.commit('tickets/setTickets', selectedEvent.value.customTickets)

    if (selectedEvent.value.maxCustomCapacity) {
      if (isWaitingList.value) {
        store.commit('tickets/setMaxCustomCapacity', waitingListOptions.value.maxCapacity)
      } else {
        store.commit('tickets/setMaxCustomCapacity', selectedEvent.value.maxCustomCapacity)
      }
    }
  }

  if (selectedEvent.value.maxExtraPeople && !isWaitingList.value) {
    store.commit('tickets/setMaxExtraPeople', selectedEvent.value.maxExtraPeople)
  }

  if (isWaitingList.value && waitingListOptions.value.maxExtraPeopleEnabled) {
    store.commit('tickets/setMaxExtraPeople', waitingListOptions.value.maxExtraPeople)
  }
}, {flush: 'post'})

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

// * Watching when footer button was clicked
watchEffect(() => {
  if (footerButtonClicked.value) {
    footerButtonReset()

    if (bringingAnyoneAvailability.value) {
      bringingAnyoneVisibility.value = true
    } else {
      nextStep()
    }
  }
})

onMounted(() => {
  store.commit('payment/setPaymentDepositType', selectedEvent.value.depositPayment)
  store.commit('payment/setPaymentDepositAmount', selectedEvent.value.deposit)
  store.commit('setLoading', false)
  headerButtonPreviousReset()
})

// * Responsive
let cWidth = inject('containerWidth')
let dWidth = inject('dialogWidth')
let responsiveClass = computed(() => {
  return useResponsiveClass(props.inDialog ? dWidth.value : cWidth.value)
})

// * Fonts
let amFonts = inject('amFonts')

// * Colors
let amColors = inject('amColors')

// * Css Vars
let cssVars = computed(() => {
  return {
    '--am-font-family': amFonts.value.fontFamily,
    '--am-c-eli-primary': amColors.value.colorPrimary,
    '--am-c-eli-bgr': amColors.value.colorMainBgr,
    '--am-c-eli-text': amColors.value.colorMainText,
    '--am-c-eli-text-op90': useColorTransparency(amColors.value.colorMainText, 0.9),
    '--am-c-eli-text-op80': useColorTransparency(amColors.value.colorMainText, 0.8),
    '--am-c-eli-text-op70': useColorTransparency(amColors.value.colorMainText, 0.7),
    '--am-c-eli-text-op60': useColorTransparency(amColors.value.colorMainText, 0.7),
    '--am-c-eli-text-op20': useColorTransparency(amColors.value.colorMainText, 0.1),
  }
})
</script>

<script>
export default {
  name: "EventInfo",
  key: "info",
  label: "event_info"
}
</script>

<style lang="scss">
.amelia-v2-booking #amelia-container {
  // eli - event list info
  .am-eli {
    * {
      font-family: var(--am-font-family);
      letter-spacing: normal;
      word-break: break-word;
    }

    .am-eli {
      &__image {
        position: relative;
        overflow: hidden;
        margin: 8px 0 16px;
      }

      &__header {
        display: flex;
        flex-direction: row;
        margin: 24px 0 0;

        p {
          margin-bottom: 0;
        }
      }

      &__main {
        padding-bottom: 16px;

        &-item {
          margin-bottom: 12px;
        }

        .el-tabs {

          &__nav {
            &-wrap {
              &:after {
                background-color: var(--am-c-eli-text-op20);
              }
            }
          }

          &__item {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.42857;
            color: var(--am-c-eli-text-op90);

             &:focus {
               box-shadow: none;
             }

            &.is-active {
              color: var(--am-c-eli-primary);
            }
          }

          &__active-bar {
            background-color: var(--am-c-eli-primary);
          }

          &__content {
            padding: 12px 0;
          }
        }
      }

      &__timetable {
        &-title {
          font-size: 14px;
          font-weight: 500;
          line-height: 1.42857;
          color: var(--am-c-eli-text-op70);
          margin: 0 0 8px;
          word-break: break-word;
        }

        &-range {
          font-size: 14px;
          font-weight: 500;
          line-height: 1.42857;
          color: var(--am-c-eli-text-op80);
          border-bottom: 1px solid var(--am-c-eli-text-op20);
          margin: 0 0 8px;
        }

        &-main {
          margin: 0 0 2px;

          & > * {
            font-size: 14px;
            line-height: 1.42857;
            color: var(--am-c-eli-text-op80);
          }

          &__date {
            font-weight: 500;
          }

          &__time {
            font-weight: 400;
          }
        }
      }
    }

    &__bringing {
      &-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        width: 100%;
        gap: 8px;

        &.am-rw-500 {
          flex-direction: column;

          .am-button--secondary {
            order: 2;
            width: 100%;
          }

          .am-button--primary {
            order: 1;
            width: 100%;
          }
        }
      }
    }
  }
}
</style>
