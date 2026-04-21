<template>
  <!-- Event List (elf) -->
  <div class="am-eli" :class="props.globalClass" :style="cssVars">
    <GalleryCarousel
      v-if="selectedEvent.gallery.length && customizeOptions.gallery.visibility"
      :gallery="selectedEvent.gallery"
      class="am-eli__image"
    ></GalleryCarousel>

    <div class="am-eli__header">
      <EventCard
        :parent-key="stepName"
        :event="selectedEvent"
        :labels="amLabels"
        :locations="locations"
        :tickets="tickets"
        :date-string-visibility="true"
        :border-visibility="false"
        :btn-visibility="false"
        :auto-info-height="true"
        :image-visibility="false"
        vertical-orientation="start"
        :in-header="true"
        :in-dialog="true"
      ></EventCard>
    </div>

    <div class="am-eli__main">
      <el-tabs v-model="activeName">
        <el-tab-pane :label="labelsDisplay('event_info')" name="first">
          <div
            v-if="selectedEvent.periods.length"
            class="am-eli__timetable am-eli__main-item"
          >
            <p class="am-eli__timetable-title am-eli__main-title">
              {{ labelsDisplay('event_timetable') }}
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
            v-if="customizeOptions.eventDescription.visibility"
            class="am-eli__main-item"
            :title="labelsDisplay('description')"
            :description="selectedEvent.description"
            :customized-labels="customLabels"
            :limit="250"
          ></DescriptionItem>

          <EventEmployee
            v-if="customizeOptions.eventOrganizer.visibility && !licence.isLite"
            :divider="true"
            :employee="eventOrganizer"
            :rank="labelsDisplay('event_organizer')"
            :customized-labels="customLabels"
            class="am-eli__main-item"
          ></EventEmployee>

          <template v-if="customizeOptions.eventEmployees.visibility">
            <EventEmployee
              v-for="employee in eventProviders"
              :key="employee.id"
              :divider="true"
              :employee="employee"
              :customized-labels="customLabels"
              class="am-eli__main-item"
            ></EventEmployee>
          </template>
        </el-tab-pane>
        <el-tab-pane
          v-if="features.tickets"
          :label="labelsDisplay('event_tickets')"
          name="second"
        >
          <EventTicket
            v-for="ticket in tickets"
            :key="ticket.id"
            :ticket="ticket"
            :capacity="selectedEvent.maxCustomCapacity"
            :extra-people="selectedEvent.maxExtraPeople"
            :customized-labels="customLabels"
            :readonly="true"
          />
        </el-tab-pane>
      </el-tabs>
    </div>
  </div>

  <!-- Bringing Anyone with you -->
  <AmSlidePopup
    :visibility="bringingAnyoneVisibility"
    custom-class="am-eli__bringing-wrapper"
  >
    <EventBringingAnyone />

    <template #footer>
      <div class="am-eli__bringing-footer" :class="responsiveClass">
        <AmButton
          :type="
            amCustomize[pageRenderKey].bringingAnyone.options.secBtn.buttonType
          "
          category="secondary"
          :disabled="false"
          @click="noOneBringWith"
        >
          {{ labelsDisplay('back_btn', 'bringingAnyone') }}
        </AmButton>
        <AmButton
          :category="isWaiting ? 'waiting' : 'primary'"
          :type="
            amCustomize[pageRenderKey].bringingAnyone.options.primBtn.buttonType
          "
          :disabled="false"
          @click="bringPeopleWithYou"
        >
          {{ labelsDisplay('continue', 'bringingAnyone') }}
        </AmButton>
      </div>
    </template>
  </AmSlidePopup>
  <!--/ Bringing Anyone with you -->
</template>

<script setup>
// * _components
import AmButton from '../../../../../../_components/button/AmButton.vue'
import AmSlidePopup from '../../../../../../_components/slide-popup/AmSlidePopup.vue'

// * Dedicated Components
import EventCard from '../../EventList/parts/EventCard.vue'
import DescriptionItem from '../../../../../../public/EventForm/Common/Parts/DescriptionItem.vue'
import EventEmployee from '../../../../../../public/EventForm/Common/Parts/EventEmployee.vue'

// * Parts
import EventTicket from '../EventTickets/parts/EventTicket.vue'
import GalleryCarousel from '../../../../../../public/EventForm/Common/Parts/GalleryCarousel.vue'

// * Dialogs
import EventBringingAnyone from './parts/EventBringingAnyone.vue'

// * Import from Vue
import { computed, inject, ref, reactive } from 'vue'
import moment from 'moment/moment'

// * Composables
import { useColorTransparency } from '../../../../../../../assets/js/common/colorManipulation'
import {
  getEventFrontedFormattedTime,
  getFrontedFormattedDate,
} from '../../../../../../../assets/js/common/date'
import { useResponsiveClass } from '../../../../../../../assets/js/common/responsive'
import { useReactiveCustomize } from '../../../../../../../assets/js/admin/useReactiveCustomize.js'

// * Components Properties
let props = defineProps({
  globalClass: {
    type: String,
    default: '',
  },
  inDialog: {
    type: Boolean,
    default: false,
  },
})

// * Plugin Licence
let licence = inject('licence')

// * Features
let features = inject('features')

let isWaiting = computed(() => {
  return features.value.waitingList
})

// * Base Urls
const baseUrls = inject('baseUrls')

let selectedEvent = ref({
  id: 3,
  bookable: true,
  color: '#1788FB',
  opened: true,
  full: !!isWaiting.value,
  closed: false,
  description:
    "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
  fullPayment: false,
  gallery: [
    {
      pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
    },
    {
      pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
    },
    {
      pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
    },
    {
      pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
    },
    {
      pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
    },
  ],
  locationId: 1,
  customPricing: true,
  customTickets: [
    {
      enabled: true,
      id: 1,
      eventId: 1,
      name: 'Ticket one',
      price: 10,
      sold: 0,
      spots: 20,
    },
    {
      enabled: true,
      id: 2,
      eventId: 1,
      name: 'Ticket two',
      price: 50,
      sold: 0,
      spots: 20,
    },
    {
      enabled: true,
      id: 2,
      eventId: 1,
      name: 'Ticket three',
      price: 100,
      sold: 0,
      spots: 20,
    },
  ],
  depositPerPerson: true,
  depositPayment: 'percentage',
  deposit: 10,
  maxCapacity: 9,
  maxCustomCapacity: null,
  maxExtraPeople: 3,
  name: 'Event 3',
  organizerId: 1,
  periods: [
    {
      id: 1,
      eventId: 1,
      periodStart: moment()
        .add(2, 'days')
        .add(5, 'hours')
        .format('YYYY-MM-DD hh:mm:ss'),
      periodEnd: moment()
        .add(4, 'days')
        .add(10, 'hours')
        .format('YYYY-MM-DD hh:mm:ss'),
    },
  ],
  pictureFullPath: null,
  pictureThumbPath: null,
  places: 30,
  price: 10,
  providers: [
    {
      id: 1,
      firstName: 'Jane',
      lastName: 'Doe',
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
    },
    {
      id: 2,
      firstName: 'Sahar',
      lastName: 'Potter',
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
    },
    {
      id: 3,
      firstName: 'Miles',
      lastName: 'Shannon',
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
    },
  ],
  show: true,
  status: 'approved',
  tags: [{}],
})

// * Locations array
let locations = computed(() => {
  if (licence.isLite) return []

  return [
    {
      id: 1,
      name: 'Location 1',
      address: 'Location 1',
    },
    {
      id: 2,
      name: 'Location 2',
      address: 'Location 2',
    },
    {
      id: 3,
      name: 'Location 3',
      address: 'Location 3',
    },
  ]
})

// * Employees - limit to 1 for lite licence
let eventProviders = computed(() => {
  if (licence.isLite) return selectedEvent.value.providers.slice(0, 1)
  return selectedEvent.value.providers
})

// * Event Tickets
let tickets = ref([
  {
    id: 1,
    eventTicketId: 1,
    name: 'Ticket one',
    persons: 0,
    price: 10,
    sold: 5,
    spots: 10,
  },
  {
    id: 2,
    eventTicketId: 2,
    name: 'Ticket two',
    persons: 0,
    price: 10,
    sold: 1,
    spots: 10,
  },
  {
    id: 3,
    eventTicketId: 3,
    name: 'Ticket three',
    persons: 0,
    price: 10,
    sold: 0,
    spots: 10,
  },
])

const activeName = ref('first')

let selectedEventsPeriods = computed(() => {
  let periodsArr = []
  selectedEvent.value.periods.forEach(function (period) {
    let periodStartDate = moment(period.periodStart.split(' ')[0], 'YYYY-MM-DD')
    let periodEndDate = moment(period.periodEnd.split(' ')[0], 'YYYY-MM-DD')
    let periodStartTime = moment(
      period.periodStart.split(' ')[1],
      'HH:mm:ss'
    ).format('HH:mm:ss')
    let periodEndTime = moment(
      period.periodEnd.split(' ')[1],
      'HH:mm:ss'
    ).format('HH:mm:ss')

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

      periodDates.forEach((dayPeriod) => {
        periodsArr.push({
          id: period.id,
          start: dayPeriod + ' ' + periodStartTime,
          end: dayPeriod + ' ' + periodEndTime,
        })
      })
    } else {
      periodsArr.push({
        id: period.id,
        start: periodStartDate.format('YYYY-MM-DD') + ' ' + periodStartTime,
        end: periodEndDate.format('YYYY-MM-DD') + ' ' + periodEndTime,
      })
    }
  })

  let periods = []
  periodsArr
    .sort(
      (a, b) =>
        moment(a.start, 'YYYY-MM-DD HH:mm:ss') -
        moment(b.start, 'YYYY-MM-DD HH:mm:ss')
    )
    .forEach((item) => {
      periods.push({
        dateOfMonth: getFrontedFormattedDate(item.start.split(' ')[0]),
        timeStart: getEventFrontedFormattedTime(item.start.split(' ')[1]),
        timeEnd: getEventFrontedFormattedTime(item.end.split(' ')[1]),
      })
    })

  return periods
})

let eventRange = computed(() => {
  let rangeArr = []
  let rangeObj = {}

  selectedEvent.value.periods.forEach((item) => {
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

let eventOrganizer = ref({
  firstName: 'John',
  lastName: 'Doe',
  description:
    "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
})

function noOneBringWith() {}

function bringPeopleWithYou() {}

// * Customize
let pageRenderKey = inject('pageRenderKey')
let stepName = inject('stepName')
let subStepName = useReactiveCustomize().subStepName

// * Bringing Anyone With You
let bringingAnyoneVisibility = computed(() => {
  return subStepName.value === 'bringingAnyone'
})

const { amCustomize } = useReactiveCustomize()

// * Options
let customizeOptions = computed(() => {
  return amCustomize.value[pageRenderKey.value].info.options
})

// * Labels
let langKey = inject('langKey')
let amLabels = inject('labels')

function labelsDisplay(label, stepKey = 'info') {
  let computedLabel = computed(() => {
    let translations =
      amCustomize.value[pageRenderKey.value][stepKey].translations
    return translations &&
      translations[label] &&
      translations[label][langKey.value]
      ? translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Computed labels
let customLabels = computed(() => {
  let computedLabels = reactive({ ...amLabels })

  if (amCustomize.value.elf.info.translations) {
    let customizedLabels = amCustomize.value.elf.info.translations
    Object.keys(customizedLabels).forEach((labelKey) => {
      if (customizedLabels[labelKey][langKey.value]) {
        computedLabels[labelKey] = customizedLabels[labelKey][langKey.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
})

// * Responsive
let cWidth = inject('containerWidth')
let responsiveClass = computed(() => {
  return useResponsiveClass(cWidth.value)
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
    '--am-c-eli-text-op90': useColorTransparency(
      amColors.value.colorMainText,
      0.9
    ),
    '--am-c-eli-text-op80': useColorTransparency(
      amColors.value.colorMainText,
      0.8
    ),
    '--am-c-eli-text-op70': useColorTransparency(
      amColors.value.colorMainText,
      0.7
    ),
    '--am-c-eli-text-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.7
    ),
    '--am-c-eli-text-op20': useColorTransparency(
      amColors.value.colorMainText,
      0.1
    ),
  }
})
</script>

<script>
export default {
  name: 'EventInfo',
  key: 'info',
  label: 'event_info',
}
</script>

<style lang="scss">
#amelia-app-backend-new #amelia-container {
  // eli - event list info
  .am-eli {
    * {
      font-family: var(--am-font-family), sans-serif;
      letter-spacing: normal;
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

        .el-tab {
          &s {
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
              padding: 0;
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

            &__header {
              margin: 0;
            }

            &__content {
              padding: 12px 0;
            }
          }

          &-pane {
            padding: 0;
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
