<template>
  <div class="am-els" :style="cssVars">
    <div v-if="customizeOptions.header.visibility" class="am-els__available">
      {{
        `${total} ${
          total === 1
            ? labelsDisplay('event_available')
            : labelsDisplay('events_available')
        }`
      }}
    </div>
    <!-- Events Filters -->
    <div v-if="customizeOptions.filters.visibility" class="am-els__filters">
      <div class="am-els__filters-top">
        <div class="am-els__filters-search">
          <AmInput
            v-model="eventSearch"
            :placeholder="`${labelsDisplay('event_search')}...`"
            :prefix-icon="iconSearch"
          />
        </div>
        <div class="am-els__filters-menu__btn">
          <AmButton
            :icon="filterIcon"
            :icon-only="cWidth <= 500"
            custom-class="am-els__filters-menu__btn-inner"
            category="secondary"
            :type="customizeOptions.filterBtn.buttonType"
            @click="filtersMenuVisibility = !filtersMenuVisibility"
          >
            <span class="am-icon-filter"></span>
            <span v-if="cWidth > 500">{{
              labelsDisplay('event_filters')
            }}</span>
          </AmButton>
        </div>
      </div>
      <Transition name="am-slide-fade">
        <div
          v-if="filtersMenuVisibility"
          class="am-els__filters-menu"
          :class="responsiveClass"
        >
          <div
            class="am-els__filters-menu__items"
            :class="[responsiveClass, filterClassWidth.tag]"
          >
            <AmSelect
              v-model="tagFilter"
              filterable
              clearable
              :placeholder="labelsDisplay('event_type')"
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
            class="am-els__filters-menu__items"
            :class="[responsiveClass, filterClassWidth.location]"
          >
            <AmSelect
              v-model="locationFilter"
              filterable
              clearable
              :placeholder="labelsDisplay('event_location')"
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
          <div class="am-els__filters-menu__items" :class="responsiveClass">
            <AmDatePickerFull
              :existing-date="dateFilter"
              :presistant="false"
              :disabled="false"
            ></AmDatePickerFull>
          </div>
        </div>
      </Transition>
    </div>
    <!-- /Events Filters -->

    <!-- Event List -->
    <div class="am-els__wrapper">
      <template v-for="event in events" :key="event.id">
        <EventCard
          :event="event"
          parent-key="list"
          :locations="locations"
          @click="selectEvent"
        ></EventCard>
      </template>
      <!-- /Event Card -->
    </div>
    <!-- /Event List -->

    <!-- Events Pagination -->
    <div
      v-if="total && Math.ceil(total / showItems) > 1"
      class="am-els__pagination"
    >
      <div class="am-els__pagination-info">
        {{
          `${labelsDisplay('event_page')} ${currentPage} / ${Math.ceil(
            total / showItems
          )}`
        }}
      </div>
      <AmPagination
        v-model:current-page="currentPage"
        :page-count="Math.ceil(total / showItems)"
        :total="total"
        :page-size="showItems"
        layout="prev, pager, next"
        :hide-on-single-page="true"
        @current-change="changePage"
      />
    </div>
    <!-- /Events Pagination -->
  </div>
</template>

<script setup>
// * Structure Components
import AmButton from '../../../../../_components/button/AmButton.vue'
import AmSelect from '../../../../../_components/select/AmSelect.vue'
import AmOption from '../../../../../_components/select/AmOption.vue'
import AmInput from '../../../../../_components/input/AmInput.vue'
import AmDatePickerFull from '../../../../../_components/date-picker-full/AmDatePickerFull.vue'
import IconComponent from '../../../../../_components/icons/IconComponent.vue'
import AmPagination from '../../../../../_components/pagination/AmPagination.vue'

// * Dedicated Components
import EventCard from './parts/EventCard.vue'

// * Import from Vue
import { ref, inject, computed, defineComponent } from 'vue'

// * Import Composable
import { useColorTransparency } from '../../../../../../assets/js/common/colorManipulation'
import { useResponsiveClass } from '../../../../../../assets/js/common/responsive'
import { useReactiveCustomize } from '../../../../../../assets/js/admin/useReactiveCustomize.js'
import moment from 'moment'

// * Plugin Licence
let licence = inject('licence')

// * Container width
let cWidth = inject('containerWidth')

let responsiveClass = computed(() => {
  return useResponsiveClass(cWidth.value)
})

// * Base Urls
const baseUrls = inject('baseUrls')

// * Events array
let events = ref([
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
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 2,
    bookable: true,
    color: '#1788FB',
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
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 3,
    bookable: true,
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
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 4,
    bookable: true,
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
    name: 'Event 4',
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
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 5,
    bookable: false,
    color: '#1788FB',
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
    providers: [],
    show: true,
    status: 'rejected',
    tags: [{}],
  },
  {
    id: 6,
    bookable: false,
    color: '#1788FB',
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
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
  {
    id: 7,
    bookable: true,
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
    places: 1,
    price: 10,
    providers: [],
    show: true,
    status: 'approved',
    tags: [{}],
  },
])

// * Events Pagination
let currentPage = ref(1)

// * Total pages
let total = ref(12)

// * Show items on pages
let showItems = ref(6)

function changePage() {}

let filterIcon = defineComponent({
  components: { IconComponent },
  template: `<IconComponent icon="filter"/>`,
})

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

// * Filter class generated according to filter elements visibility
let filterClassWidth = computed(() => {
  let tagVisibility = tags.value.length > 0
  let locationVisibility = locations.value.length > 0

  let classFilter = {
    tag: 'am-mw33',
    location: 'am-mw33',
    date: 'am-mw33'
  }

  if (!tagVisibility) {
    classFilter.location = 'am-mw60'
    classFilter.date = 'am-mw40'
  }

  if (!locationVisibility) {
    classFilter.tag = 'am-mw60'
    classFilter.date = 'am-mw40'
  }

  if (!tagVisibility && !locationVisibility) {
    classFilter.date = 'am-mw100'
  }

  if (cWidth.value <= 500) {
    classFilter.tag = 'am-mw100'
    classFilter.location = 'am-mw100'
    classFilter.date = 'am-mw100'
  }

  return classFilter
})

function selectEvent() {}

// * Filters
let eventSearch = ref('')

let iconSearch = defineComponent({
  components: { IconComponent },
  template: `<IconComponent icon="search"/>`,
})

let tagFilter = ref('')

let locationFilter = ref('')

let dateFilter = ref('')

let filtersMenuVisibility = ref(false)

// * Customize
const { amCustomize } = useReactiveCustomize()

// * Options
let customizeOptions = computed(() => {
  return amCustomize.value.elf.list.options
})

let langKey = inject('langKey')
let amLabels = inject('labels')

function labelsDisplay(label) {
  let computedLabel = computed(() => {
    let translations = amCustomize.value.elf.list.translations
    return translations &&
      translations[label] &&
      translations[label][langKey.value]
      ? translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Colors
let amColors = inject('amColors')

// * Css Vars
let cssVars = computed(() => {
  return {
    '--am-c-els-text': amColors.value.colorMainText,
    '--am-c-els-head-text': amColors.value.colorMainText,
    '--am-c-els-bgr': amColors.value.colorMainBgr,
    '--am-c-els-text-op10': useColorTransparency(
      amColors.value.colorMainText,
      0.1
    ),
    '--am-c-els-text-op25': useColorTransparency(
      amColors.value.colorMainText,
      0.25
    ),
    '--am-c-els-text-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
  }
})
</script>

<script>
export default {
  name: 'EventsList',
  key: 'list',
}
</script>

<style lang="scss">
@import '../../../../../../assets/scss/common/transitions/transitions-mixin';

#amelia-app-backend-new #amelia-container {
  // am - amelia
  // els - events list step
  .am-els {
    &__available {
      font-size: 18px;
      font-weight: 500;
      line-height: 1.55556;
      color: var(--am-c-els-head-text);
      margin-bottom: 16px;
    }

    &__filters {
      @include slide-fade;

      &-top {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
      }

      &-search {
        flex: 0 1 auto;
        width: 100%;
        border-radius: 6px;
        box-shadow: 0 5px 5px var(--am-c-els-text-op10);
      }

      &-menu {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;

        &.am-rw-500 {
          flex-wrap: wrap;
        }

        &__btn {
          border-radius: 6px;
          box-shadow: 0 5px 5px var(--am-c-els-text-op10);
          margin-left: 8px;

          //&-inner {
          //  &.am-button.am-button--plain{
          //    &:not(.is-disabled):active, &:not(.is-disabled):focus, &:not(.is-disabled):hover {
          //      --am-c-btn-border: var(--am-c-primary);
          //      --am-c-btn-shadow: var(--am-c-primary);
          //      box-shadow: 0 1px 3px var(--am-c-btn-shadow);
          //    }
          //  }
          //}

          .am-icon-filter {
            font-size: 24px;
          }
        }

        &__items {
          width: 100%;
          padding-right: 16px;

          &.am-mw {
            &33 {
              max-width: 33.333%;
            }

            &40 {
              max-width: 40%;
            }

            &60 {
              max-width: 60%;
            }

            &100 {
              max-width: 100%;
            }
          }

          &.am-rw-500 {
            width: 100%;
            padding: 0;
            margin: 0 0 12px;
          }

          &:last-child {
            padding-right: 0;
          }

          &-label {
            display: block;
            font-size: 15px;
            font-weight: 500;
            line-height: 1.4;
            color: var(--am-c-els-text);
            margin-bottom: 4px;
          }
        }
      }
    }

    &__pagination {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-top: 1px solid var(--am-c-els-text-op25);
      margin: 48px 0 0;
      padding: 16px 0 0;

      &-info {
        font-size: 13px;
        font-weight: 400;
        line-height: 1.3;
        color: var(--am-c-els-text-op60);
      }
    }
  }
}
</style>
