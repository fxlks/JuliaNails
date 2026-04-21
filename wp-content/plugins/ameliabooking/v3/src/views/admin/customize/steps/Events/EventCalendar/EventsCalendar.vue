<template>
  <div
    ref="evtCalPageRef"
    class="am-ecs__page"
    :class="{ 'am-tablet': pageWidth < 992 }"
    :style="cssVars"
  >
    <div
      ref="mainBlockRef"
      class="am-ecs__main"
      :class="{
        'am-full-width':
          pageWidth < 992 || !customizeOptions.upcomingBlock.visibility,
      }"
    >
      <!-- Filters -->
      <div
        v-if="
          customizeOptions.search.visibility ||
          customizeOptions.filters.visibility
        "
        ref="filtersRef"
        class="am-ecs__filters"
      >
        <div class="am-ecs__filters-top">
          <div
            v-if="customizeOptions.search.visibility"
            class="am-ecs__filters-search"
            :class="filterResponsiveClass"
          >
            <AmInput
              v-model="eventSearch"
              :placeholder="`${labelsDisplay('event_search')}...`"
              :prefix-icon="iconSearch"
              size="small"
            />
          </div>
          <div
            v-if="customizeOptions.filters.visibility"
            class="am-ecs__filters-btn"
            :class="[
              { 'am-single': !customizeOptions.search.visibility },
              filterResponsiveClass,
            ]"
          >
            <AmButton
              :icon="filterIcon"
              :icon-only="filtersWidth <= 500"
              custom-class="am-ecs__filters-btn__inner"
              category="secondary"
              type="plain"
              size="small"
              @click="filtersMenuVisibility = !filtersMenuVisibility"
            >
              <span class="am-icon-filter"></span>
              <span v-if="filtersWidth > 500">{{
                labelsDisplay('event_filters')
              }}</span>
            </AmButton>
          </div>
        </div>

        <Transition name="am-slide-fade">
          <div
            v-if="filtersMenuVisibility"
            class="am-ecs__filters-menu"
            :class="filterResponsiveClass"
          >
            <div
              class="am-ecs__filters-menu__items"
              :class="[filterResponsiveClass, filterClassWidth.tag]"
            >
              <AmSelect
                v-model="tagFilter"
                clearable
                size="small"
                :filterable="false"
                :multiple="true"
                :collapse-tags="true"
                :placeholder="labelsDisplay('event_type')"
                :prefix-icon="tagFilter.length ? '' : iconType"
              >
                <AmOption
                  v-for="(tag, index) in tags"
                  :key="index"
                  :value="tag.name"
                  :label="tag.name"
                >
                </AmOption>
              </AmSelect>
            </div>
            <div
              v-if="locations.length > 0"
              class="am-ecs__filters-menu__items"
              :class="[filterResponsiveClass, filterClassWidth.location]"
            >
              <AmSelect
                v-model="locationFilter"
                clearable
                size="small"
                :filterable="false"
                :multiple="true"
                :collapse-tags="true"
                :placeholder="labelsDisplay('event_location')"
                :prefix-icon="locationFilter.length ? '' : iconLocation"
              >
                <AmOption
                  v-for="location in locations"
                  :key="location.id"
                  :value="location.id"
                  :label="location.name"
                >
                </AmOption>
              </AmSelect>
            </div>
            <div
              v-if="employees.length > 0"
              class="am-ecs__filters-menu__items"
              :class="[filterResponsiveClass, filterClassWidth.employee]"
            >
              <AmSelect
                v-model="employeeFilter"
                clearable
                size="small"
                :filterable="false"
                :multiple="true"
                :collapse-tags="true"
                :placeholder="labelsDisplay('event_employee')"
                :prefix-icon="employeeFilter.length ? '' : iconEmployee"
              >
                <AmOption
                  v-for="emp in employees"
                  :key="emp.id"
                  :value="emp.id"
                  :label="`${emp.firstName} ${emp.lastName}`"
                >
                </AmOption>
              </AmSelect>
            </div>
            <div
              class="am-ecs__filters-menu__items"
              :class="[filterResponsiveClass, filterClassWidth.status]"
            >
              <AmSelect
                v-model="statusFilter"
                clearable
                size="small"
                :filterable="false"
                :multiple="true"
                :collapse-tags="true"
                :placeholder="labelsDisplay('event_status')"
                :prefix-icon="statusFilter.length ? '' : iconStatus"
                @change="selectEventStatus"
              >
                <AmOption
                  v-for="status in evtStatus"
                  :key="status.toLowerCase()"
                  :value="status.toLowerCase()"
                  :label="labelsDisplay(status.toLowerCase())"
                >
                </AmOption>
              </AmSelect>
            </div>
          </div>
        </Transition>
      </div>
      <!-- /Filters -->

      <!-- Header -->
      <div ref="headerRef" class="am-ecs__header">
        <div class="am-ecs__header-inner" :class="responsiveCalClass">
          <div class="am-ecs__header-actions">
            <AmButton
              custom-class="am-ecs__header-today"
              category="secondary"
              type="plain"
              size="small"
              @click="calendarChangeMonth('today')"
            >
              {{ labelsDisplay('event_today') }}
            </AmButton>
            <AmButton
              custom-class="am-ecs__header-prev"
              :icon-only="true"
              :icon="iconArrLeft"
              category="secondary"
              type="plain"
              size="small"
              @click="calendarChangeMonth('prev')"
            />
            <AmButton
              custom-class="am-ecs__header-next"
              :icon-only="true"
              :icon="iconArrRight"
              category="secondary"
              type="plain"
              size="small"
              @click="calendarChangeMonth('next')"
            />
          </div>
          <div class="am-ecs__header-text">
            {{ headerTitle }}
          </div>
        </div>
        <div class="am-ecs__header-selection" :class="responsiveCalClass">
          <AmSelect
            v-model="selectedMonth"
            size="small"
            @change="dateSelection"
          >
            <AmOption
              v-for="month in dropdownMonths"
              :key="month.value"
              :value="month.value"
              :label="month.label"
            ></AmOption>
          </AmSelect>
          <AmSelect v-model="selectedYear" size="small" @change="dateSelection">
            <AmOption
              v-for="year in dropdownYears"
              :key="year.value"
              :value="year.value"
            >
            </AmOption>
          </AmSelect>
        </div>
      </div>
      <!-- /Header -->

      <!-- Calendar -->
      <div class="am-ecs">
        <FullCalendar ref="eventCalendarRef" :options="calendarOptions">
        </FullCalendar>
      </div>
      <!-- /Calendar -->
    </div>
    <div
      v-if="customizeOptions.upcomingBlock.visibility"
      ref="sideRef"
      class="am-ecs__side"
      :class="{ 'am-full-width': pageWidth < 992 }"
    >
      <div ref="sideHeaderRef" class="am-ecs__side-header">
        <div class="am-ecs__side-header__text">
          {{ labelsDisplay('event_upcoming_events') }}
        </div>
        <div class="am-ecs__side-header__range">
          <div>
            {{
              `${
                translationObject[
                  `month${moment(upcomingRange[0]).format('M')}`
                ]
              }
            ${moment(upcomingRange[0]).format('YYYY')}`
            }}
          </div>
        </div>
        <div class="am-ecs__side-header__actions">
          <AmButton
            custom-class="am-ecs__side-header__btn"
            :icon-only="true"
            :icon="iconArrLeft"
            category="secondary"
            type="plain"
            size="small"
            :disabled="upcomingNumber === 1"
          />
          <AmButton
            custom-class="am-ecs__side-header__btn"
            :icon-only="true"
            :icon="iconArrRight"
            category="secondary"
            type="plain"
            size="small"
          />
        </div>
      </div>

      <div
        v-if="features.waitingList"
        ref="sideTabRef"
        class="am-ecs__side-tab"
      >
        <div
          v-for="(item, index) in sideTabs"
          :key="index"
          class="am-ecs__side-tab__item"
          :class="[{ 'am-active': activeTabIndex === index }, item.class]"
          @click="() => (activeTabIndex = index)"
        >
          {{ item.title }}
        </div>
      </div>

      <div
        class="am-ecs__side-main"
        :class="sideResponsiveClass"
        :style="pageWidth > 991 ? { height: `${sideBlockHeight}px` } : {}"
      >
        <template v-if="activeTabIndex === 0">
          <div
            v-for="evt in upcomingEvents"
            :key="evt.id"
            class="am-ecs__side-card"
            :class="sideResponsiveClass"
            :style="{
              '--am-c-evt': evt.color,
              '--am-c-evt-op05': useColorTransparency(evt.color, 0.05),
            }"
          >
            <div class="am-ecs__side-card__header">
              <span
                v-if="multipleDaysEvent(evt.periods)"
                class="am-ecs__side-card__begins"
              >
                {{ labelsDisplay('event_begins') }}
              </span>
              <span
                v-if="customizeOptions.price.visibility"
                class="am-ecs__side-card__price"
              >
                {{ calculatedEventPrice(evt) }}
                <template v-if="taxVisibility">
                  {{
                    amSettings.payments?.taxes?.excluded
                      ? ` +${labelsDisplay('total_tax_colon')}`
                      : ` ${labelsDisplay('incl_tax')}`
                  }}
                </template>
              </span>
            </div>
            <div class="am-ecs__side-card__date-time">
              <span class="am-ecs__side-card__day">
                {{
                  moment(evt.periods[0].periodStart.split(' ')[0]).format('DD')
                }}
              </span>
              <span class="am-ecs__side-card__month">
                {{
                  translationObject.months[
                    moment(evt.periods[0].periodStart.split(' ')[0])
                      .format('MMM')
                      .toLowerCase()
                  ].toUpperCase()
                }}
              </span>
              <span class="am-ecs__side-card__at">{{
                labelsDisplay('event_at')
              }}</span>
              <span class="am-ecs__side-card__time">
                {{
                  getEventFrontedFormattedTime(
                    evt.periods[0].periodStart.split(' ')[1]
                  )
                }}
              </span>
            </div>
            <div class="am-ecs__side-card__name">
              {{ evt.name }}
            </div>
            <div
              v-if="customizeOptions.location.visibility"
              class="am-ecs__side-card__location"
            >
              {{ useEventLocation(evt, locations) }}
            </div>
            <div
              v-if="
                customizeOptions.status.visibility ||
                customizeOptions.capacity.visibility
              "
              class="am-ecs__side-card__footer"
            >
              <div
                v-if="customizeOptions.status.visibility"
                class="am-ecs__side-card__status"
                :class="`am-${useEventStatus(evt)}`"
              >
                {{ labelsDisplay(useEventStatus(evt)) }}
              </div>
              <div
                v-if="customizeOptions.capacity.visibility"
                class="am-ecs__side-card__spots"
              >
                <span
                  v-if="placesCalculation(evt)"
                  class="am-ecs__side-card__spots-numb"
                >
                  {{ placesCalculation(evt) }}
                </span>
                <span class="am-ecs__side-card__spots-text">
                  {{ eventPlacesBlock(evt) }}
                </span>
              </div>
            </div>
          </div>
        </template>

        <template v-if="activeTabIndex === 1">
          <div
            v-for="evt in waitingEvents"
            :key="evt.id"
            class="am-ecs__side-card"
            :class="sideResponsiveClass"
            :style="{
              '--am-c-evt': evt.color,
              '--am-c-evt-op05': useColorTransparency(evt.color, 0.05),
            }"
          >
            <div class="am-ecs__side-card__header">
              <span
                v-if="multipleDaysEvent(evt.periods)"
                class="am-ecs__side-card__begins"
              >
                {{ labelsDisplay('event_begins') }}
              </span>
              <span
                v-if="customizeOptions.price.visibility"
                class="am-ecs__side-card__price"
              >
                {{ calculatedEventPrice(evt) }}
                <template v-if="taxVisibility">
                  {{
                    amSettings.payments?.taxes?.excluded
                      ? ` +${labelsDisplay('total_tax_colon')}`
                      : ` ${labelsDisplay('incl_tax')}`
                  }}
                </template>
              </span>
            </div>
            <div class="am-ecs__side-card__date-time">
              <span class="am-ecs__side-card__day">
                {{
                  moment(evt.periods[0].periodStart.split(' ')[0]).format('DD')
                }}
              </span>
              <span class="am-ecs__side-card__month">
                {{
                  translationObject.months[
                    moment(evt.periods[0].periodStart.split(' ')[0])
                      .format('MMM')
                      .toLowerCase()
                  ].toUpperCase()
                }}
              </span>
              <span class="am-ecs__side-card__at">{{
                labelsDisplay('event_at')
              }}</span>
              <span class="am-ecs__side-card__time">
                {{
                  getEventFrontedFormattedTime(
                    evt.periods[0].periodStart.split(' ')[1]
                  )
                }}
              </span>
            </div>
            <div class="am-ecs__side-card__name">
              {{ evt.name }}
            </div>
            <div
              v-if="customizeOptions.location.visibility"
              class="am-ecs__side-card__location"
            >
              {{ useEventLocation(evt, locations) }}
            </div>
            <div
              v-if="
                customizeOptions.status.visibility ||
                customizeOptions.capacity.visibility
              "
              class="am-ecs__side-card__footer"
            >
              <div
                v-if="customizeOptions.status.visibility"
                class="am-ecs__side-card__status am-waiting"
              >
                {{ labelsDisplay('waiting_list') }}
              </div>
              <div
                v-if="customizeOptions.capacity.visibility"
                class="am-ecs__side-card__spots"
              >
                <span
                  v-if="placesCalculation(evt)"
                  class="am-ecs__side-card__spots-numb"
                >
                  {{ placesCalculation(evt) }}
                </span>
                <span class="am-ecs__side-card__spots-text">
                  {{
                    placesCalculation(evt) === 1
                      ? labelsDisplay('person_waiting')
                      : labelsDisplay('people_waiting')
                  }}
                </span>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
// * Libraries
import moment from 'moment'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import allLocales from '@fullcalendar/core/locales-all'

// * Import from Vue
import {
  computed,
  inject,
  nextTick,
  onMounted,
  ref,
  watch,
  watchEffect,
  onBeforeUnmount
} from 'vue'

// * Components Width and Height
import { useElementSize } from '@vueuse/core'

// * Composables
import {
  getEventFrontedFormattedTime,
  getFirstDayOfWeek,
} from '../../../../../../assets/js/common/date'
import { useColorTransparency } from '../../../../../../assets/js/common/colorManipulation'
import { useResponsiveClass } from '../../../../../../assets/js/common/responsive'
import { elementPlusTranslations } from '../../../../../../assets/js/common/translationsElementPlus'
import {
  useCheckIfEventNotFree,
  useEventLocation,
  useEventStatus,
  useMinTicketPrice,
} from '../../../../../../assets/js/public/events'
import { useFormattedPrice } from '../../../../../../assets/js/common/formatting'
import { useReactiveCustomize } from '../../../../../../assets/js/admin/useReactiveCustomize.js'

// * Structure Components
import AmButton from '../../../../../_components/button/AmButton.vue'
import IconComponent from '../../../../../_components/icons/IconComponent.vue'
import AmSelect from '../../../../../_components/select/AmSelect.vue'
import AmOption from '../../../../../_components/select/AmOption.vue'
import AmInput from '../../../../../_components/input/AmInput.vue'

// * Licence
let licence = inject('licence')

// * Features
let features = inject('features')

// * Root Settings
let amSettings = inject('settings')

// * Calendar page Reference
const evtCalPageRef = ref(null)
const { width: pageWidth } = useElementSize(evtCalPageRef)

// * Main Wrapper Reference
const mainBlockRef = ref(null)
const { width: mainWidth } = useElementSize(mainBlockRef)

let responsiveCalClass = computed(() => {
  return useResponsiveClass(mainWidth.value)
})

// * Calendar Reference
const eventCalendarRef = ref(null)
const { width: calWidth, height: calHeight } = useElementSize(eventCalendarRef)

// * Filters Reference
let filtersRef = ref(null)
const { width: filtersWidth, height: filtersHeight } =
  useElementSize(filtersRef)
let filterResponsiveClass = computed(() =>
  useResponsiveClass(filtersWidth.value)
)

// * Header Reference
let headerRef = ref(null)
const { height: headerHeight } = useElementSize(headerRef)

// * Sidebar Reference
let sideRef = ref(null)
const { width: sideWidth } = useElementSize(sideRef)
let sideResponsiveClass = computed(() => useResponsiveClass(sideWidth.value))

// * Sidebar Header Reference
let sideHeaderRef = ref(null)
const { height: sideHeaderHeight } = useElementSize(sideHeaderRef)

// * Sidebar Tabs Reference
let sideTabRef = ref(null)
const { height: sideTabHeight } = useElementSize(sideTabRef)

// * Calendar Loader Height
let calLoaderHeight = ref(0)

// * Side Block Height
let sideBlockHeight = ref(0)

/// * Customize
const { amCustomize } = useReactiveCustomize()

// * Options
let customizeOptions = computed(() => {
  return amCustomize.value.ecf.calendar.options
})

let langKey = inject('langKey')
let amLabels = inject('labels')

// * This object contains translations for months days etc
let translationObject = computed(
  () => elementPlusTranslations(langKey.value).el.datepicker
)

function labelsDisplay(label) {
  let computedLabel = computed(() => {
    let translations = amCustomize.value.ecf.calendar.translations
    return translations &&
      translations[label] &&
      translations[label][langKey.value]
      ? translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Base Urls
const baseUrls = inject('baseUrls')

// * Events array
let events = computed(() => [
  {
    id: 1,
    bookable: false,
    color: '#1788FB',
    opened: true,
    full: false,
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
    name: 'Event 1',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .subtract(2, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(2, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 30,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 2,
    bookable: true,
    color: '#4bbec6',
    opened: true,
    full: true,
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
    name: 'Event 2',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(6, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(8, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 30,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 3,
    bookable: true,
    color: '#fbc22d',
    opened: true,
    full: false,
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
          .startOf('month')
          .add(8, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(10, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 30,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 4,
    bookable: true,
    color: '#FA3C52',
    opened: true,
    full: false,
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
    name: 'Event 4',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(10, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(11, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 30,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 5,
    bookable: false,
    color: '#D696B8',
    opened: true,
    full: false,
    closed: false,
    cancelable: true,
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
    name: 'Event 5',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(12, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(14, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 30,
    price: 10,
    providers: [],
    show: true,
    status: 'rejected',
    tags: [{}],
  },
  {
    id: 6,
    bookable: false,
    color: '#689BCA',
    opened: false,
    full: false,
    closed: false,
    upcoming: true,
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
    name: 'Event 6',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(15, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(15, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 30,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 7,
    bookable: true,
    color: '#26CC2B',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 7',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(16, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(17, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 8,
    bookable: true,
    color: '#FD7E35',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 8',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(17, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(20, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 9,
    bookable: true,
    color: '#E38587',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 9',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(18, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(21, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 10,
    bookable: true,
    color: '#774DFB',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 10',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(21, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(22, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'waiting',
    tags: [{}],
  },
  {
    id: 11,
    bookable: true,
    color: '#bcfb00',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 11',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(23, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(26, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 12,
    bookable: true,
    color: '#082770',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 12',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(26, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(30, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 13,
    bookable: true,
    color: '#409e40',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 13',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(29, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(31, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 14,
    bookable: true,
    color: '#fbba17',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 14',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(30, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(32, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
])

// * Date Reference
let dateReference = ref(moment().format('YYYY-MM-DD'))

// * Event Calendar Options
let calendarOptions = ref({
  locale: computed(() => langKey.value.split('_')[0]),
  locales: allLocales,
  plugins: [interactionPlugin, dayGridPlugin],
  initialView: 'dayGridMonth',
  headerToolbar: false,
  slotLabelFormat: {
    hour: 'numeric',
    minute: '2-digit',
    hour12: false,
  },
  eventTimeFormat: {
    hour: 'numeric',
    minute: '2-digit',
    hour12: false,
  },
  aspectRatio: 0.9,
  firstDay: getFirstDayOfWeek(),
  dayMaxEvents: true,
  selectable: true,
  unselectAuto: false,
  selectLongPressDelay: 0,
  dayCellClassNames: 'am-evt-day-wrapper',
  unselectCancel: '.am-evt-more-link-inner',
  moreLinkClassNames: 'am-evt-more-link',
  eventClassNames: 'am-evt-block',
  eventContent: calendarEventBlock,
  eventClick: calendarEventClick,
  dateClick: calendarDateClick,
  moreLinkContent: calendarMoreLinkBlock,
  moreLinkClick: calendarMoreLinkClick,
  events: [],
})

function calendarEventBlock(eventInfo) {
  let props = eventInfo.event.extendedProps
  let eventColor = props.evtColor
  // * event card text
  let spotsText =
    props.places === 1
      ? `${props.places} ${labelsDisplay('event_slot_left')}`
      : `${props.places} ${labelsDisplay('event_slots_left')}`
  let noSpotsText = labelsDisplay('event_no_spots')
  let closedText = labelsDisplay('closed')
  let canceledText = labelsDisplay('canceled')
  let spots = props.places <= 0 ? noSpotsText : spotsText
  if (props.evtStatus === 'closed') spots = closedText
  if (props.evtStatus === 'canceled') spots = canceledText
  if (props.evtStatus === 'waiting') spots = labelsDisplay('join_waiting_list')
  let spotsBlock = '<span class="am-evt-spots-left">' + spots + '</span>'
  let spotsWrapper = '<div class="am-evt-spots">' + spotsBlock + '</div>'

  if (
    props.evtStatus === 'closed' ||
    props.evtStatus === 'canceled' ||
    props.evtStatus === 'waiting'
  ) {
    spotsWrapper = customizeOptions.value.status.visibility ? spotsWrapper : ''
  } else {
    spotsWrapper = customizeOptions.value.capacity.visibility
      ? spotsWrapper
      : ''
  }

  // * event card classes
  let hoverClass = props.hovered ? ' am-evt-hovered' : ''
  let statusClass = ` am-evt-${props.evtStatus}`

  let eventBlock =
    '<div class="am-evt-wrapper' +
    hoverClass +
    statusClass +
    '" style="--am-c-evt: ' +
    eventColor +
    '">' +
    '<span class="am-evt-background"></span>' +
    '<div class="am-evt-time">' +
    getEventFrontedFormattedTime(props.evtTimeStart) +
    '</div>' +
    '<div class="am-evt-inner">' +
    '<p class="am-evt-text">' +
    eventInfo.event.title +
    '</p>' +
    '</div>' +
    spotsWrapper +
    '</div>'
  return { html: eventBlock }
}

function calendarEventClick(eventInfo) {
  calendarOptions.value.events.map((event) => {
    event.hovered =
      parseInt(event.evtId) === parseInt(eventInfo.event.extendedProps.evtId)
  })
}

function calendarDateClick() {}

function calendarMoreLinkBlock(moreLinkInfo) {
  let text = amLabels.value.event
  if (moreLinkInfo.num > 1) {
    text = labelsDisplay('events')
  }

  let calendarApi = eventCalendarRef.value.getApi()

  if (calendarApi.el.offsetWidth < 448) {
    return { html: '<div class="am-evt-more-link-inner mobile"></div>' }
  }

  return {
    html:
      '<div class="am-evt-more-link-inner">' +
      '+' +
      moreLinkInfo.num +
      ' ' +
      text +
      '</div>',
  }
}

function calendarMoreLinkClick(info) {
  info.jsEvent.stopPropagation()
}

function mergePeriods(periods) {
  if (!periods.length) return []

  // Convert to moment objects and sort by start date
  periods = periods
    .map((period) => ({
      periodStart: moment(period.periodStart),
      periodEnd: moment(period.periodEnd),
    }))
    .sort((a, b) => a.periodStart - b.periodStart)

  const mergedPeriods = [periods[0]]

  for (let i = 1; i < periods.length; i++) {
    const lastMerged = mergedPeriods[mergedPeriods.length - 1]
    const current = periods[i]

    if (current.periodStart <= lastMerged.periodEnd) {
      // Overlapping periods, merge them
      lastMerged.periodEnd = moment.max(lastMerged.periodEnd, current.periodEnd)
    } else {
      // No overlap, add to the merged periods
      mergedPeriods.push(current)
    }
  }

  return mergedPeriods.map((period) => ({
    periodStart: period.periodStart.format('YYYY-MM-DD HH:mm:ss'),
    periodEnd: period.periodEnd.format('YYYY-MM-DD HH:mm:ss'),
  }))
}

function eventArrayBuilder(evtArr) {
  let buildArr = []
  evtArr.forEach(function (event) {
    mergePeriods(event.periods).forEach((period) => {
      let periodStartDate = moment(
        period.periodStart.split(' ')[0],
        'YYYY-MM-DD'
      )
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

      let isWaiting =
        event.name === 'Event 10' &&
        features.value.waitingList

      buildArr.push({
        evtId: event.id,
        evtStatus: isWaiting ? 'waiting' : useEventStatus(event),
        periodId: period.id,
        locationId: event.locationId,
        customLocation: event.customLocation,
        title: event.name,
        places: placesCalculation(event),
        evtColor: event.color,
        hovered: false,
        evtTimeStart: periodStartTime,
        start: periodStartDate.format('YYYY-MM-DD') + ' ' + periodStartTime,
        end: periodEndDate.format('YYYY-MM-DD') + ' ' + periodEndTime,
      })
    })
  })

  let eventsToDisplay = []
  buildArr.forEach((a) => {
    if (
      !eventsToDisplay.find(
        (b) =>
          b.evtId === a.evtId && b.start.split(' ')[0] === a.start.split(' ')[0]
      )
    ) {
      eventsToDisplay.push(a)
    }
  })

  calendarOptions.value.events = eventsToDisplay

  nextTick(() => {
    setTimeout(() => {
      eventCalendarRef.value.getApi().render()
    }, 100)
  })
}

onMounted(() => {
  let evtArr = Array.from(events.value)

  eventArrayBuilder(evtArr)
})

function placesCalculation(evt) {
  if (evt.customPricing) {
    let places = 0
    let eventTickets = evt.customTickets

    if (evt.maxCustomCapacity) {
      let sold = 0
      eventTickets.forEach((t) => {
        if ('enabled' in t ? t.enabled : true) {
          sold += t.sold
        }
      })

      places = evt.maxCustomCapacity - sold
    } else {
      eventTickets.forEach((t) => {
        if ('enabled' in t ? t.enabled : true) {
          places += t.spots - t.sold - ('persons' in t ? t.persons : 0)
        }
      })
    }

    return places
  }

  return evt.places
}

// * Header Functionality

function getEventBasedOnDateChange() {
  return [
    moment(dateReference.value)
      .startOf('month')
      .subtract(15, 'days')
      .format('YYYY-MM-DD'),
    moment(dateReference.value)
      .endOf('month')
      .add(15, 'days')
      .format('YYYY-MM-DD'),
  ]
}
function calendarChangeMonth(monthType) {
  if (monthType === 'today') {
    eventCalendarRef.value.getApi().today()
  }
  if (monthType === 'prev') {
    eventCalendarRef.value.getApi().prev()
  }
  if (monthType === 'next') {
    eventCalendarRef.value.getApi().next()
  }

  dateReference.value = moment(
    eventCalendarRef.value.getApi().getDate().toUTCString()
  ).format('YYYY-MM-DD')

  selectedMonth.value = moment(dateReference.value).format('MM')
  selectedYear.value = moment(dateReference.value).format('YYYY')

  getEventBasedOnDateChange()
}

let selectedMonth = ref(moment(dateReference.value).format('MM'))
let dropdownMonths = computed(() => {
  return [
    {
      value: '01',
      label: translationObject.value.month1,
    },
    {
      value: '02',
      label: translationObject.value.month2,
    },
    {
      value: '03',
      label: translationObject.value.month3,
    },
    {
      value: '04',
      label: translationObject.value.month4,
    },
    {
      value: '05',
      label: translationObject.value.month5,
    },
    {
      value: '06',
      label: translationObject.value.month6,
    },
    {
      value: '07',
      label: translationObject.value.month7,
    },
    {
      value: '08',
      label: translationObject.value.month8,
    },
    {
      value: '09',
      label: translationObject.value.month9,
    },
    {
      value: '10',
      label: translationObject.value.month10,
    },
    {
      value: '11',
      label: translationObject.value.month11,
    },
    {
      value: '12',
      label: translationObject.value.month12,
    },
  ]
})

let selectedYear = ref(moment(dateReference.value).format('YYYY'))
let dropdownYears = ref([
  {
    value: moment().format('YYYY'),
  },
  {
    value: moment().add(1, 'year').format('YYYY'),
  },
  {
    value: moment().add(2, 'year').format('YYYY'),
  },
])

function dateSelection() {
  dateReference.value = moment(
    `${selectedYear.value}-${selectedMonth.value}`,
    'YYYY-MM'
  ).format('YYYY-MM-DD')
  getEventBasedOnDateChange()
}

// * Event Header
let headerTitle = computed(() => {
  return `${
    dropdownMonths.value.find((m) => m.value === selectedMonth.value).label
  }, ${selectedYear.value}`
})

let iconArrLeft = {
  components: { IconComponent },
  template: `<IconComponent icon="arrow-left"/>`,
}

let iconArrRight = {
  components: { IconComponent },
  template: `<IconComponent icon="arrow-right"/>`,
}

let iconType = {
  components: { IconComponent },
  template: `<IconComponent icon="folder"/>`,
}

let iconLocation = {
  components: { IconComponent },
  template: `<IconComponent icon="locations"/>`,
}

let iconEmployee = {
  components: { IconComponent },
  template: `<IconComponent icon="employee"/>`,
}

let iconStatus = {
  components: { IconComponent },
  template: `<IconComponent icon="circle-empty"/>`,
}

// ** Filters **
let filtersMenuVisibility = ref(false)

// * Locations array
let locations = computed(() => {

  if (licence.isLite || licence.isStarter) return []

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

// * Tags array
let tags = ref([{ name: 'Tag 1' }, { name: 'Tag 2' }, { name: 'Tag 3' }])

// * Employees array
let employees = computed(() => {
  if (licence.isLite) return []

  return [
    {
      id: 1,
      firstName: 'John',
      lastName: 'Doe',
    },
    {
      id: 2,
      firstName: 'Jane',
      lastName: 'Doe',
    },
    {
      id: 3,
      firstName: 'John',
      lastName: 'Wick',
    },
  ]
})

// * Status Array
let evtStatus = ref(['Open', 'Full', 'Upcoming', 'Closed', 'Canceled'])

// * Filter class generated according to filter elements visibility
let filterClassWidth = computed(() => {
  let tagVisibility = tags.value.length > 0
  let locationVisibility = locations.value.length > 0
  let employeeVisibility = employees.value.length > 0

  let classFilter = {
    tag: 'am-mw25',
    location: 'am-mw25',
    employee: 'am-mw25',
    status: 'am-mw25',
  }

  if (!tagVisibility && !locationVisibility && !employeeVisibility) {
    classFilter.status = 'am-mw100'
  } else {
    let visibleCount = tagVisibility + locationVisibility + employeeVisibility

    if (visibleCount === 2) {
      let wideClass = 'am-mw33'
      let narrowClass = 'am-mw33'
      if (tagVisibility) classFilter.tag = wideClass
      if (locationVisibility) classFilter.location = wideClass
      if (employeeVisibility) classFilter.employee = wideClass
      classFilter.status = narrowClass
    } else if (visibleCount === 1) {
      let singleClass = 'am-mw50'
      if (tagVisibility) classFilter.tag = singleClass
      if (locationVisibility) classFilter.location = singleClass
      if (employeeVisibility) classFilter.employee = singleClass
      classFilter.status = singleClass
    }
  }

  return classFilter
})

let filterIcon = {
  components: { IconComponent },
  template: `<IconComponent icon="filter"/>`,
}

let eventSearch = ref('')

let iconSearch = {
  components: { IconComponent },
  template: `<IconComponent icon="search"/>`,
}

let tagFilter = ref('')

let locationFilter = ref('')

let employeeFilter = ref('')

let statusFilter = ref('')

function selectEventStatus(status) {
  let evtArr = Array.from(events.value)

  if (status.length) {
    let filteredArray = []
    evtArr.forEach((evt) => {
      status.forEach((s) => {
        if (s === evt.status) filteredArray.push(evt)
      })
    })
    eventArrayBuilder(filteredArray)
  } else {
    eventArrayBuilder(evtArr)
  }
}

watchEffect(() => {
  if (calHeight.value) {
    calLoaderHeight.value = calHeight.value
    sideBlockHeight.value =
      calHeight.value +
      filtersHeight.value +
      headerHeight.value +
      48 -
      (sideHeaderHeight.value + 36)

    if (features.value.waitingList) {
      sideBlockHeight.value += sideTabHeight.value
    }
  }

  if (calWidth.value < 448) {
    calendarOptions.value.moreLinkClassNames +=
      !calendarOptions.value.moreLinkClassNames.includes('mobile')
        ? ' mobile'
        : ''
    calendarOptions.value.dayCellClassNames +=
      !calendarOptions.value.dayCellClassNames.includes('mobile')
        ? ' mobile'
        : ''
  } else {
    calendarOptions.value.moreLinkClassNames = 'am-evt-more-link'
    calendarOptions.value.dayCellClassNames = 'am-evt-day-wrapper'
  }
})

let typingInLabelsInputs = ref(null)
watch([amCustomize.value, customizeOptions.value], () => {
  clearTimeout(typingInLabelsInputs.value)
  typingInLabelsInputs.value = setTimeout(() => {
    eventCalendarRef.value.getApi().render()
  }, 500)
})

// ** Upcoming events
let upcomingArray = ref([
  {
    id: 1,
    bookable: false,
    color: '#1788FB',
    opened: true,
    full: false,
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
    name: 'Event 1',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .subtract(2, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(2, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 30,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 2,
    bookable: true,
    color: '#4bbec6',
    opened: true,
    full: true,
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
    name: 'Event 2',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(6, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(8, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 30,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 3,
    bookable: true,
    color: '#fbc22d',
    opened: true,
    full: false,
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
          .startOf('month')
          .add(1, 'month')
          .add(8, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(10, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 30,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 4,
    bookable: true,
    color: '#FA3C52',
    opened: true,
    full: false,
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
    name: 'Event 4',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(10, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(11, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 30,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 5,
    bookable: false,
    color: '#D696B8',
    opened: true,
    full: false,
    closed: false,
    cancelable: true,
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
    name: 'Event 5',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(12, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(14, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 30,
    price: 10,
    providers: [],
    show: true,
    status: 'rejected',
    tags: [{}],
  },
  {
    id: 6,
    bookable: false,
    color: '#689BCA',
    opened: false,
    full: false,
    closed: false,
    upcoming: true,
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
    name: 'Event 6',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(15, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(15, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 30,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 7,
    bookable: true,
    color: '#26CC2B',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 7',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(16, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(17, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 8,
    bookable: true,
    color: '#FD7E35',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 8',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(17, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(20, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 9,
    bookable: true,
    color: '#E38587',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 9',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(18, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(21, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 10,
    bookable: true,
    color: '#774DFB',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 10',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(21, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(22, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 11,
    bookable: true,
    color: '#bcfb00',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 11',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(23, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(26, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 12,
    bookable: true,
    color: '#082770',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 12',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(26, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(30, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 13,
    bookable: true,
    color: '#409e40',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 13',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(29, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(31, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 14,
    bookable: true,
    color: '#fbba17',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 14',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(30, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(32, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
])
let upcomingEvents = ref(
  upcomingArray.value.filter(
    (e) => useEventStatus(e) === 'open' || useEventStatus(e) === 'upcoming'
  )
)

// * Upcoming Number
let upcomingNumber = ref(1)

let upcomingRange = ref([
  moment().add(1, 'month').startOf('month').format('YYYY-MM-DD'),
  moment().add(1, 'month').startOf('month').format('YYYY-MM-DD'),
])

// * Sidebar tabs
let sideTabs = ref([
  {
    title: computed(() => labelsDisplay('upcoming')),
    class: 'am-upcoming',
  },
  {
    title: computed(() => labelsDisplay('waiting_list')),
    class: 'am-waiting',
  },
])
let activeTabIndex = ref(0)

// * Waiting array
let waitingEvents = ref([
  {
    id: 3,
    bookable: true,
    color: '#fbc22d',
    opened: true,
    full: false,
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
          .startOf('month')
          .add(1, 'month')
          .add(8, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(10, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 30,
    price: 10,
    providers: [],
    show: true,
    status: 'waiting',
    tags: [{}],
  },
  {
    id: 4,
    bookable: true,
    color: '#FA3C52',
    opened: true,
    full: false,
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
    name: 'Event 4',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(10, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(11, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 30,
    price: 10,
    providers: [],
    show: true,
    status: 'waiting',
    tags: [{}],
  },
  {
    id: 6,
    bookable: false,
    color: '#689BCA',
    opened: false,
    full: false,
    closed: false,
    upcoming: true,
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
    name: 'Event 6',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(15, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(15, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 30,
    price: 10,
    providers: [],
    show: true,
    status: 'waiting',
    tags: [{}],
  },
  {
    id: 7,
    bookable: true,
    color: '#26CC2B',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 7',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(16, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(17, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'waiting',
    tags: [{}],
  },
  {
    id: 8,
    bookable: true,
    color: '#FD7E35',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 8',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(17, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(20, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'waiting',
    tags: [{}],
  },
  {
    id: 9,
    bookable: true,
    color: '#E38587',
    opened: true,
    full: false,
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
    customPricing: false,
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
    name: 'Event 9',
    organizerId: 1,
    periods: [
      {
        id: 1,
        eventId: 1,
        periodStart: moment()
          .startOf('month')
          .add(1, 'month')
          .add(18, 'days')
          .add(5, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
        periodEnd: moment()
          .startOf('month')
          .add(1, 'month')
          .add(21, 'days')
          .add(10, 'hours')
          .format('YYYY-MM-DD hh:mm:ss'),
      },
    ],
    pictureFullPath: null,
    pictureThumbPath: null,
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'waiting',
    tags: [{}],
  },
])

function calculatedEventPrice(evt) {
  if (evt.customPricing) {
    if (useCheckIfEventNotFree(evt)) {
      return `${labelsDisplay('from')} ${useFormattedPrice(
        useMinTicketPrice(evt)
      )}`
    } else {
      return labelsDisplay('event_free')
    }
  }

  return useCheckIfEventNotFree(evt)
    ? useFormattedPrice(evt.price)
    : labelsDisplay('event_free')
}

function multipleDaysEvent(arr) {
  let testArr = []
  arr.forEach((period) => {
    testArr.push(period.periodStart.split(' ')[0])
    testArr.push(period.periodEnd.split(' ')[0])
  })

  return !testArr.every((val) => val === testArr[0])
}

function eventPlacesBlock(evt) {
  let spots = placesCalculation(evt)
  let spotsText =
    spots === 1
      ? labelsDisplay('event_slot_left')
      : labelsDisplay('event_slots_left')
  let noSpotsText = labelsDisplay('event_no_spots')

  return spots <= 0 ? noSpotsText : spotsText
}

let taxVisibility = computed(() => {
  return (
    amSettings.payments?.taxes?.enabled &&
    customizeOptions.value.tax.visibility &&
    !licence.isStarter &&
    !licence.isLite
  )
})

// * Window resize
window.addEventListener('resize', handleResize)
function handleResize() {
  eventCalendarRef.value.getApi().render()
}

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize)
})

// * Colors
let amColors = inject('amColors')

// * CSS Vars
let cssVars = computed(() => {
  return {
    '--am-c-ecal-bgr': amColors.value.colorMainBgr,
    '--am-c-ecal-heading-text': amColors.value.colorMainHeadingText,
    '--am-c-ecal-heading-text-op70': useColorTransparency(
      amColors.value.colorMainHeadingText,
      0.7
    ),
    '--am-c-ecal-text': amColors.value.colorMainText,
    '--am-c-ecal-text-op03': useColorTransparency(
      amColors.value.colorMainText,
      0.03
    ),
    '--am-c-ecal-text-op05': useColorTransparency(
      amColors.value.colorMainText,
      0.05
    ),
    '--am-c-ecal-text-op10': useColorTransparency(
      amColors.value.colorMainText,
      0.1
    ),
    '--am-c-ecal-text-op20': useColorTransparency(
      amColors.value.colorMainText,
      0.2
    ),
    '--am-c-ecal-text-op30': useColorTransparency(
      amColors.value.colorMainText,
      0.3
    ),
    '--am-c-ecal-text-op50': useColorTransparency(
      amColors.value.colorMainText,
      0.5
    ),
    '--am-c-ecal-text-op70': useColorTransparency(
      amColors.value.colorMainText,
      0.7
    ),
    '--am-c-ecal-card-bgr': amColors.value.colorCardBgr,
    '--am-c-ecal-card-border': amColors.value.colorCardBorder,
    '--am-c-ecal-card-border-op10': useColorTransparency(
      amColors.value.colorCardBorder,
      0.1
    ),
    '--am-c-ecal-card-text': amColors.value.colorCardText,
    '--am-c-ecal-card-text-op80': useColorTransparency(
      amColors.value.colorCardText,
      0.8
    ),
    '--am-c-ecal-card-text-op50': useColorTransparency(
      amColors.value.colorCardText,
      0.5
    ),
    '--am-c-ecal-error': amColors.value.colorError,
    '--am-c-ecal-primary': amColors.value.colorPrimary,
    '--am-c-ecal-primary-op30': useColorTransparency(
      amColors.value.colorPrimary,
      0.3
    ),
    '--am-c-ecal-success': amColors.value.colorSuccess,
    '--am-c-ecal-warning': amColors.value.colorWarning,
  }
})
</script>

<script>
export default {
  name: 'EventsCalendar',
  key: 'calendar',
}
</script>

<style lang="scss">
@import '../../../../../../assets/scss/common/transitions/_transitions-mixin.scss';

//ecs - event calendar step
@mixin am-ecs-block {
  .am-ecs {
    &__page {
      display: flex;
      background-color: var(--am-c-ecal-bgr);
      border-radius: 8px;
      border: 1px solid var(--am-c-ecal-text-op10);
      box-shadow: 0 5px 5px 0 var(--am-c-ecal-text-op03);

      &.am-tablet {
        flex-wrap: wrap;
      }

      * {
        font-family: var(--am-font-family, 'Amelia Roboto'), sans-serif;
        font-style: initial;
        box-sizing: border-box;
        text-decoration: unset;
      }
    }

    &__filters {
      @include slide-fade;
      margin: 16px;

      &-top {
        display: flex;
        align-items: center;
        justify-content: center;
      }

      &-search {
        flex: 0 1 auto;
        width: 100%;
      }

      &-btn {
        margin-left: 8px;

        &.am-rw-500 {
          width: auto;
        }

        &.am-single {
          margin: 0 0 0 auto;
        }

        .am-button {
          width: 100%;

          &__inner {
            width: 100%;
            justify-content: space-evenly;

            span {
              line-height: 1;
            }
          }

          .am-icon-filter {
            font-size: 20px;
          }
        }
      }

      &-menu {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 16px 0 0;
        background-color: var(--am-c-ecal-text-op03);
        border-radius: 6px;
        padding: 12px 20px;

        &.am-rw-650 {
          flex-wrap: wrap;
        }

        &__items {
          width: 100%;

          &.am-mw {
            &25 {
              max-width: calc(25% - 16px);

              &.am-rw-650 {
                max-width: calc(50% - 4px);
                padding: 4px 0;
              }

              &.am-rw-420 {
                max-width: 100%;
              }
            }

            &33 {
              max-width: calc(33.333% - 16px);
              &.am-rw-600 {
                max-width: calc(50% - 4px);
                padding: 4px 0;

                &:last-child {
                  max-width: 100%;
                }
              }

              &.am-rw-420 {
                max-width: 100%;
              }
            }

            &50 {
              max-width: calc(50% - 16px);

              &.am-rw-600 {
                max-width: calc(50% - 4px);
              }

              &.am-rw-420 {
                max-width: 100%;
                padding: 4px 0;
              }
            }

            &100 {
              max-width: 100%;
            }
          }

          & .am-select {
            .el-select__tags {
              & > span {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: nowrap;

                .el-tag {
                  background-color: transparent;
                  border: none;
                  font-size: 14px;
                  font-weight: 500;

                  &.is-hit {
                    width: calc(100% - 24px);
                    padding: 0 0 0 8px;

                    .el-tag__content {
                      width: 100%;
                    }
                  }

                  &:not(.is-hit) {
                    width: 24px;
                  }

                  &__close {
                    display: none;
                  }

                  .el-select__tags-text {
                    display: block;
                    max-width: unset !important;
                    width: 100%;
                    text-overflow: ellipsis;
                    overflow: hidden;
                    white-space: nowrap;
                    color: var(--am-c-inp-text);
                    font-size: 14px;
                    font-weight: 500;
                  }
                }
              }
            }

            .el-input__prefix {
              left: 4px;
              font-size: 25px;

              span {
                color: var(--am-c-select-placeholder);
              }
            }
          }
        }
      }
    }

    &__header {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      margin: 8px 16px;

      &-inner {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin: 8px 0;
      }

      &-actions {
        display: flex;
        align-items: center;
        justify-content: flex-start;
      }

      &-today {
        &.am-button.am-button--small {
          --am-padd-btn: 6px 30px;
          margin: 0 16px 0 0;
        }
      }

      &-prev {
        &.am-button.am-button--small {
          --am-fs-btn: 16px;
          margin: 0 8px 0 0;
        }
      }

      &-next {
        &.am-button.am-button--small {
          --am-fs-btn: 16px;
          margin: 0 16px 0 0;
        }
      }

      &-text {
        font-size: 18px;
        font-weight: 500;
        line-height: 1.555556;
        color: var(--am-c-ecal-heading-text);
      }

      &-selection {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 40%;
        margin: 8px 0;

        &.am-rw-650 {
          width: 100%;
        }

        .am-select-wrapper {
          width: calc(50% - 3px);
        }
      }
    }

    &__main {
      width: calc(100% - 290px);

      &.am-full-width {
        width: 100%;
      }
    }

    &__side {
      max-width: 290px;
      width: 100%;
      border-radius: 8px;
      background-color: var(--am-c-ecal-text-op03);

      &-header {
        padding: 16px 24px 0;

        &__text {
          font-size: 24px;
          font-style: normal;
          font-weight: 500;
          line-height: 1.33333;
          color: var(--am-c-ecal-heading-text);
          margin: 0 0 12px;
        }

        &__range {
          display: flex;
          align-items: center;
          justify-content: flex-start;
          flex-wrap: wrap;
          font-size: 15px;
          font-style: normal;
          font-weight: 500;
          line-height: 1.6;
          color: var(--am-c-ecal-heading-text-op70);
          margin: 0 0 12px;
        }

        &__actions {
          display: flex;
          align-items: center;
          justify-content: space-between;
          margin: 0 0 12px;
        }

        &__btn.am-button.am-button--small.is-icon-only {
          width: calc(50% - 4px);
          font-size: 16px;
        }
      }

      &-tab {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 24px 12px;

        &__item {
          position: relative;
          display: flex;
          align-items: center;
          justify-content: center;
          width: calc(50% - 4px);
          padding: 12px 16px 10px 16px;
          color: var(--am-c-ecal-text);
          background-color: var(--am-c-ecal-text-op05);
          border-radius: 9px 9px 0 0;
          font-size: 14px;
          font-style: normal;
          font-weight: 500;
          line-height: 1.42857;
          cursor: pointer;
          transition: all 0.3s ease-in-out;

          &:hover:not(.am-active) {
            background-color: var(--am-c-ecal-text-op10);
          }

          &:after {
            content: '';
            display: block;
            width: 100%;
            height: 1px;
            position: absolute;
            bottom: -3px;
            left: 0;
            background-color: var(--am-c-ecal-text-op20);
          }

          &.am-upcoming {
            &.am-active {
              color: var(--am-c-ecal-bgr);
              background-color: var(--am-c-ecal-primary);

              &:after {
                background-color: var(--am-c-ecal-primary);
              }
            }
          }

          &.am-waiting {
            &.am-active {
              color: var(--am-c-ecal-bgr);
              background-color: var(--am-c-ecal-warning);

              &:after {
                background-color: var(--am-c-ecal-warning);
              }
            }
          }
        }
      }

      &-main {
        padding: 0 24px;
        overflow-x: hidden;

        // Main Scroll styles
        &::-webkit-scrollbar {
          width: 6px;
        }

        &::-webkit-scrollbar-thumb {
          border-radius: 6px;
          background: var(--am-c-scroll-op30);
        }

        &::-webkit-scrollbar-track {
          border-radius: 6px;
          background: var(--am-c-scroll-op10);
        }
      }

      &-card {
        display: flex;
        flex-wrap: wrap;
        width: 100%;
        padding: 12px 16px 12px 24px;
        border-radius: 8px;
        background-color: var(--am-c-ecal-card-bgr);
        box-shadow: 4px 0 0 0 var(--am-c-evt) inset,
          0 2px 2px -1px var(--am-c-ecal-card-border),
          0 0 11px 0px var(--am-c-ecal-card-border-op10);
        margin: 0 0 12px;
        cursor: pointer;
        transition: background-color 0.2s ease-in-out;

        &:hover {
          background-color: var(--am-c-evt-op05);
        }

        &__header {
          width: 100%;
          display: flex;
          align-items: center;
          justify-content: space-between;
          margin: 0 0 4px;
        }

        &__begins {
          font-size: 12px;
          font-style: normal;
          font-weight: 400;
          line-height: 1.5;
          color: var(--am-c-ecal-card-text-op50);
        }

        &__price {
          text-align: end;
          font-size: 13px;
          font-style: normal;
          font-weight: 700;
          line-height: 1.38462;
          color: var(--am-c-ecal-primary);
          margin-left: auto;
        }

        &__date-time {
          display: flex;
          align-items: center;
          justify-content: flex-start;
          flex: 0 0 auto;
        }

        &__day {
          font-size: 18px;
          font-style: normal;
          font-weight: 500;
          line-height: 1.111111;
          color: var(--am-c-ecal-card-text);
          margin: 0 4px 0 0;
        }

        &__month,
        &__at,
        &__time {
          font-size: 14px;
          font-style: normal;
          font-weight: 400;
          line-height: 1;
          color: var(--am-c-ecal-card-text);
          margin: 0 4px 0 0;
        }

        &__name {
          width: 100%;
          font-size: 14px;
          font-style: normal;
          font-weight: 700;
          line-height: 1.42857;
          color: var(--am-c-ecal-card-text);
          margin: 8px 0 0;
        }

        &__location {
          width: 100%;
          font-size: 13px;
          font-style: normal;
          font-weight: 400;
          line-height: 1.38462;
          color: var(--am-c-ecal-card-text-op80);
          margin: 4px 0 0;
        }

        &__footer {
          width: 100%;
          display: flex;
          align-items: center;
          justify-content: space-between;
        }

        &__status {
          font-size: 13px;
          font-style: normal;
          font-weight: 700;
          line-height: 1.38462; /* 138.462% */

          &.am-open {
            color: var(--am-c-ecal-success);
          }
          &.am-full {
            color: var(--am-c-ecal-primary);
          }
          &.am-upcoming {
            color: var(--am-c-ecal-warning);
          }
          &.am-waiting {
            color: var(--am-c-ecal-warning);
          }
        }

        &__spots {
          font-size: 13px;
          font-style: normal;
          font-weight: 400;
          line-height: 1.38462; /* 138.462% */
          color: var(--am-c-ecal-card-text-op80);
          margin-left: auto;

          &-numb {
            font-weight: 700;
            margin: 0 4px 0 0;
          }
        }
      }

      &-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: var(--am-c-ecal-card-text);
        font-size: 16px;
        margin: 16px 0;
      }

      &.am-full-width {
        max-width: 100%;

        .am-ecs__side {
          &-header {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;

            &__actions {
              max-width: 100px;
              width: 100%;
            }
          }

          &-main {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
          }

          &-card {
            width: calc(33.33333% - 6px);

            &.am-rw-768 {
              width: calc(50% - 6px);
            }

            &.am-rw-600 {
              width: 100%;
            }
          }
        }
      }
    }

    .fc {
      &.fc-theme-standard {
        .fc-scrollgrid-liquid {
          border: none;
        }

        .fc-scrollgrid-liquid {
          // calendar heading
          .fc-scrollgrid-section-header {
            & > th {
              background-color: var(--am-c-ecal-text-op03);
              border-right: none;
              border-left: none;
            }

            .fc-col-header-cell {
              border: none;
            }

            .fc-scrollgrid-sync-inner {
              display: flex;
              align-items: center;
              justify-content: center;
            }

            .fc-col-header-cell-cushion {
              font-size: 12px;
              font-weight: 400;
              line-height: 1;
              text-transform: uppercase;
              color: var(--am-c-ecal-text-op50);
              padding: 7px 0;
            }
          }

          // calendar body
          .fc-scrollgrid-section-body {
            & > td {
              border: none;
            }
          }

          .fc-scrollgrid-sync-table {
            tr {
              &:last-of-type {
                td {
                  border-bottom: none;
                }
              }
            }

            td {
              border: 1px solid var(--am-c-ecal-text-op05);
              padding: 4px 0 0;
              background-color: transparent;

              &:first-of-type {
                border-left: none;
              }

              &:last-of-type {
                border-right: none;
              }

              .fc-highlight {
                background-color: transparent;
              }
            }

            .fc-daygrid-day-number {
              font-size: 14px;
              font-weight: 400;
              line-height: 1.42857;
              color: var(--am-c-ecal-text);
              padding: 0 4px;
            }

            .am-evt-day-wrapper.mobile {
              &.fc-day-today {
                .fc-daygrid-day-number {
                  color: var(--am-c-ecal-bgr);
                }
              }

              .fc-daygrid-day {
                &-top {
                  padding: 6px 0;

                  &:before {
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 20px;
                    height: 20px;
                  }
                }

                &-number {
                  width: 100%;
                  text-align: center;
                  line-height: 1;
                }

                &-events {
                  height: calc(100% - 26px);
                }
              }
            }
          }
        }
      }
    }

    .am-evt {
      &-day {
        &-wrapper {
          &.fc-popover {
            max-width: 430px;
            background-color: var(--am-c-ecal-bgr);
            border-radius: 8px;
            overflow: hidden;

            .fc-popover {
              &-header {
                padding: 16px 16px 8px;
                background-color: transparent;
              }

              &-title {
                font-size: 15px;
                font-style: normal;
                font-weight: 500;
                line-height: 1.333333; /* 133.333% */
                color: var(--am-c-ecal-text);
              }

              &-close {
                font-size: 18px;
                line-height: 1;
                color: var(--am-c-ecal-text);

                &:hover {
                  color: var(--am-c-ecal-text-op70);
                }
              }

              &-body {
                background-color: var(--am-c-ecal-bgr);
                padding: 0 16px 16px;

                .fc-daygrid-event-harness {
                  background-color: transparent;
                }

                .am-evt-block {
                  &:focus {
                    background-color: transparent;

                    &:after {
                      background-color: transparent;
                    }
                  }
                }
              }
            }
          }

          &.fc-day-today {
            .fc-daygrid-day-top {
              position: relative;

              &::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 8px;
                display: block;
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background-color: var(--am-c-ecal-error);
                transform: translateY(-50%);
              }
            }
          }
        }
      }

      &-block {
        background-color: var(--am-c-main-bgr);
        border-radius: 4px;
        text-decoration: none;
        margin: 0 4px 4px;
        border: none;

        &:hover {
          text-decoration: none;
          cursor: pointer;
        }

        &:focus {
          &:after {
            background-color: transparent;
          }
        }
      }

      &-wrapper {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
        height: auto;
        border-radius: 4px;
        padding: 2px 2px 2px 6px;
        overflow: hidden;
        box-shadow: 2px 0 0 0 var(--am-c-evt) inset,
          0px 1px 1px 0px var(--am-c-ecal-text-op30);
        box-sizing: border-box;
        transition: 0.5s all ease-in-out;

        &.am-evt-hovered {
          .am-evt-background {
            opacity: 0.25;
          }
        }

        // status styles for event cards
        &.am-evt {
          &-closed {
            .am-evt {
              &-background {
                background-color: var(--am-c-ecal-text-op03);
                opacity: 1;
              }

              &-time {
                color: var(--am-c-ecal-text-op30);
              }

              &-text {
                color: var(--am-c-ecal-text-op50);
              }

              &-spots-left {
                color: var(--am-c-ecal-text-op30);
              }
            }
          }

          &-canceled {
            box-shadow: 0 1px 1px 0 var(--am-c-ecal-text-op30);

            .am-evt {
              &-background {
                background-color: var(--am-c-ecal-text-op03);
                opacity: 1;
              }

              &-time {
                color: var(--am-c-ecal-text-op30);
              }

              &-text {
                color: var(--am-c-ecal-text-op50);
              }

              &-spots-left {
                color: var(--am-c-ecal-text-op30);
              }
            }
          }

          &-full {
            .am-evt {
              &-time {
                color: var(--am-c-ecal-text-op30);
              }

              &-text {
                color: var(--am-c-ecal-text-op50);
              }

              &-spots-left {
                color: var(--am-c-ecal-text-op30);
              }
            }
          }
        }
      }

      &-time {
        display: flex;
        width: 100%;
        font-size: 11px;
        font-weight: 500;
        line-height: 1.454545;
        color: var(--am-c-ecal-text-op70);
        margin: 0;
        padding: 0;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
      }

      &-inner {
        display: flex;
        overflow: hidden;
      }

      &-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0.08;
        z-index: -1;
        background-color: var(--am-c-evt);
      }

      &-color {
        position: absolute;
        top: 4px;
        left: 4px;
        width: 4px;
        height: calc(100% - 8px);
        border-radius: 2px;
      }

      &-text {
        display: inline-block;
        font-size: 12px;
        font-weight: 500;
        line-height: 1.333333;
        color: var(--am-c-ecal-text);
        margin: 0;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
      }

      &-spots-left {
        flex: 1;
        font-size: 11px;
        font-weight: 500;
        line-height: 1.27273;
        color: var(--am-c-ecal-text-op70);
        padding: 0;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
      }

      &-spots {
        width: 100%;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
      }

      &-more-link {
        width: calc(100% - 8px);
        background-color: var(--am-c-ecal-text-op05);
        box-shadow: 0 1px 1px 0 var(--am-c-ecal-text-op30);
        border-radius: 4px;
        padding: 4px 6px;
        margin: 0 4px 2px;

        &:hover {
          background-color: var(--am-c-ecal-text-op10);
        }

        &.mobile {
          position: absolute;
          width: 100%;
          height: 100%;
          display: flex;
          align-items: center;
          justify-content: center;
          background-color: transparent;
          box-shadow: none;
          padding: 0;
          margin: 0;
          overflow: visible;
        }

        &-inner {
          position: relative;
          width: 100%;
          font-size: 11px;
          font-weight: 500;
          line-height: 1.45455;
          color: var(--am-c-ecal-text-op70);
          z-index: 2;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;

          &.mobile {
            display: flex;
            width: 8px;
            height: 8px;
            align-items: center;
            justify-content: center;
            background-color: var(--am-c-ecal-primary);
            box-shadow: 0 1px 1px 0 var(--am-c-ecal-primary-op30);
            color: var(--am-c-ecal-bgr);
            font-size: 16px;
            line-height: 1;
            padding: 0;
            border-radius: 50%;
            overflow: visible;
            text-overflow: unset;
            white-space: normal;
          }
        }
      }
    }
  }
}

#amelia-app-backend-new #amelia-container {
  @include am-ecs-block;
}
</style>
