<template>
  <div
    class="am-els"
    :style="cssVars"
  >
    <div
      v-if="ready && !loading ? customizedOptions.header.visibility && !onlyOneEvent : false"
      class="am-els__available"
    >
      {{`${total} ${total === 1 ? amLabels.event_available : amLabels.events_available }`}}
    </div>

    <EventListHeaderSkeleton v-if="!ready || loading"></EventListHeaderSkeleton>

    <!-- Events Filters -->
    <div
      v-if="customizedOptions.filters.visibility && !onlyOneEvent"
      class="am-els__filters"
    >
      <div v-if="ready" class="am-els__filters-top">
        <div
          class="am-els__filters-search"
          role="search"
        >
          <AmInput
            v-model="eventSearch"
            :aria-label="amLabels.event_search"
            :placeholder="`${amLabels.event_search}...`"
            :prefix-icon="iconSearch"
          />
        </div>
        <div class="am-els__filters-menu__btn">
          <AmButton
            :icon="filterIcon"
            :icon-only="cWidth <= 500"
            custom-class="am-els__filters-menu__btn-inner"
            category="secondary"
            :aria-label="amLabels.event_filters"
            :type="customizedOptions.filterBtn.buttonType"
            @click="filtersMenuVisibility = !filtersMenuVisibility"
          >
            <span class="am-icon-filter"></span>
            <span v-if="cWidth > 500">{{amLabels.event_filters}}</span>
          </AmButton>
        </div>
      </div>
      <EventListFiltersSkeleton v-else></EventListFiltersSkeleton>

      <Transition name="am-slide-fade">
        <div
          v-if="filtersMenuVisibility"
          class="am-els__filters-menu"
          :class="responsiveClass"
        >
          <div
            v-if="(!store.getters['params/getShortcodeParams'].tags || store.getters['params/getShortcodeParams'].tags.length !== 1) && tags.length > 0"
            class="am-els__filters-menu__items"
            :class="[responsiveClass, filterClassWidth.tag]"
          >
            <AmSelect
              v-model="tagFilter"
              filterable
              clearable
              :aria-label="amLabels.event_type"
              :placeholder="amLabels.event_type"
              :filter-method="filterQueryTag"
            >
              <AmOption
                v-for="(tag, index) in filteredTags"
                :key="index"
                :class="`am-els-tag__${tag.name.replace(/\s+/g, '')}`"
                :value="tag.name"
                :label="tag.name"
              >
              </AmOption>
            </AmSelect>
          </div>
          <div
            v-if="(!store.getters['params/getShortcodeParams'].locations || store.getters['params/getShortcodeParams'].locations.length !== 1) && locations.length > 0"
            class="am-els__filters-menu__items"
            :class="[responsiveClass, filterClassWidth.location]"
          >
            <AmSelect
              v-model="locationFilter"
              filterable
              clearable
              :placeholder="amLabels.event_location"
              :aria-label="amLabels.event_location"
              :filter-method="filterQueryLocation"
            >
              <AmOption
                v-for="location in filteredLocations"
                :key="location.id"
                :class="`am-els-location__${location.id}`"
                :value="location.id"
                :label="location.name"
              >
              </AmOption>
            </AmSelect>
          </div>
          <div
            class="am-els__filters-menu__items"
            :class="[responsiveClass, filterClassWidth.date]"
          >
            <AmDatePickerFull
              :input-placeholder="amLabels.date_picker_placeholder"
              :existing-date="dateFilter"
              :presistant="false"
              :disabled="false"
              :clearable="!!shortcode.range"
              :readonly="!shortcode.range"
              @selected-date="(dateString) => {store.commit('params/setDates', [dateString])}"
              @clear-date="() => {store.commit('params/setDates', [])}"
            />
          </div>
        </div>
      </Transition>
    </div>
    <!-- /Events Filters -->

    <template v-if="!empty">
      <!-- Event List -->
      <div v-if="!loading" class="am-els__wrapper">
        <template
          v-for="event in events"
          :key="event.id"
        >
          <EventCard
            :event="event"
            :labels="amLabels"
            :locations="locations"
            :customized-options="customizedOptions"
            :tax-visible="useTaxVisibility(store, event.id, 'event') && (customizedOptions.tax?.visibility ?? true)"
            @click="selectEvent"
          ></EventCard>
        </template>
        <!-- /Event Card -->
      </div>
      <EventListSkeleton
        v-else
        :display-number="amSettings.general.itemsPerPage"
      ></EventListSkeleton>
      <!-- /Event List -->

      <!-- Events Pagination -->
      <div
        v-if="total && Math.ceil(total / showItems) > 1"
        class="am-els__pagination"
      >
        <div class="am-els__pagination-info">
          {{ `${amLabels.event_page} ${currentPage} / ${Math.ceil(total / showItems)}` }}
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
    </template>

    <EmptyState v-if="ready && !loading && empty" />

  </div>
</template>

<script setup>
// * Structure Components
import AmButton from '../../../../_components/button/AmButton.vue'
import AmSelect from '../../../../_components/select/AmSelect.vue'
import AmOption from '../../../../_components/select/AmOption.vue'
import AmInput from '../../../../_components/input/AmInput.vue'
import AmDatePickerFull from '../../../../_components/date-picker-full/AmDatePickerFull.vue'
import IconComponent from '../../../../_components/icons/IconComponent.vue'
import AmPagination from '../../../../_components/pagination/AmPagination.vue'

// * Dedicated Components
import EventCard from '../../Common/Parts/EventCard.vue'
import EventListSkeleton from '../Parts/EventListSkeleton.vue'
import EventListFiltersSkeleton from '../Parts/EventListFiltersSkeleton.vue'
import EventListHeaderSkeleton from '../Parts/EventListHeaderSkeleton.vue'
import EmptyState from "../../Common/Parts/EmptyState.vue";

// * Import from Vue
import {
  ref,
  watch,
  inject,
  computed,
  reactive,
} from 'vue'

// * Import from vuex
import { useStore } from "vuex";

// * Import Composable
import {
  useColorTransparency
} from "../../../../../assets/js/common/colorManipulation";
import useAction from "../../../../../assets/js/public/actions";
import { useResponsiveClass } from "../../../../../assets/js/common/responsive";
import { useUrlQueryParams } from "../../../../../assets/js/common/helper";
import { useTaxVisibility } from "../../../../../assets/js/common/pricing";

// * Define store
const store = useStore()

// * Root Settings
const amSettings = inject('settings')

let shortcode = computed(() => store.getters['shortcodeParams/getShortcodeParams'])

let onlyOneEvent = computed (() => {
  let shortcodeEvents = shortcode.value.eventId.split(',').filter(e => e)//.length === 1
  let urlParams = useUrlQueryParams(window.location.href)
  let urlEvents = urlParams && urlParams.ameliaEventId ? urlParams.ameliaEventId.split(',') : []
  return shortcodeEvents.length === 1 || urlEvents.length === 1
})

// * Ready state
let ready = computed(() => store.getters['getReady'])

// * Load state
let loading = computed(() => store.getters['getLoading'])

let urlParams = useUrlQueryParams(window.location.href)
watch(ready, (readyState) => {
  if (readyState &&
    urlParams &&
    urlParams.ameliaEventPopup &&
    store.getters['eventEntities/getEvents'].filter(i => i.id === parseInt(urlParams.ameliaEventPopup)).length
  ) {
    selectEvent(parseInt(urlParams.ameliaEventPopup))
  }
})

// * Container width
let cWidth = inject('containerWidth')

let responsiveClass = computed(() => useResponsiveClass(cWidth.value))

// * Event form customization
let customizedDataForm = inject('customizedDataForm')

// * Customization options
let customizedOptions = computed(() => {
  return customizedDataForm.value.list.options
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

  if (customizedDataForm.value.list.translations) {
    let customizedLabels = customizedDataForm.value.list.translations
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

// * Events array
let events = computed(() => store.getters['eventEntities/getEvents'])

// * Empty state
let empty = computed(() => store.getters['getReady'] && !store.getters['getLoading'] ? events.value.length === 0 : false)

// * Events Pagination
let currentPage = computed({
  get: () => store.getters['pagination/getPage'],
  set: (val) => {
    store.commit('pagination/setPage', val)
  }
})

// * Total pages
let total = computed(() => store.getters['pagination/getCount'])

// * Show items on pages
let showItems = computed(() => store.getters['pagination/getShow'])

function changePage () {
  store.commit('setLoading', true)
  store.dispatch('eventEntities/requestEvents')
}

let filterIcon = {
  components: {IconComponent},
  template: `<IconComponent icon="filter"/>`
}

// * Locations array
let locations = computed(() => store.getters['eventEntities/getFilteredLocations'](store.getters['params/getShortcodeParams']))

// * Tags array
let tags = computed(() => store.getters['eventEntities/getFilteredTags'](store.getters['params/getShortcodeParams']))

// * Filter class generated according to filter elements visibility
let filterClassWidth = computed(() => {
  // p - preselected
  // a - array
  let pTag = store.getters['params/getShortcodeParams'].tags && store.getters['params/getShortcodeParams'].tags.length === 1
  let aTag = tags.value.length
  let pLocation = store.getters['params/getShortcodeParams'].locations && store.getters['params/getShortcodeParams'].locations.length === 1
  let aLocation = locations.value.length

  let tagVisibility = !pTag && aTag > 0
  let locationVisibility = !pLocation && aLocation > 0

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

let popupVisible = inject('popupVisible')
function selectEvent (id) {
  popupVisible.value = true
  store.commit('setLoading', true)
  store.commit('eventBooking/setEventId', id)
  useAction(store, {}, 'SelectEvent', 'event', null, null)
}

// * Filters
let eventSearch = computed({
  get: () => {
    return store.getters['params/getSearch']
  },
  set: (val) => {
    store.commit('params/setSearch', val)
  }
})

let iconSearch = {
  components: {IconComponent},
  template: `<IconComponent icon="search"/>`
}

let tagFilter = computed({
  get: () => {
    return store.getters['params/getTag']
  },
  set: (val) => {
    store.commit('params/setTag', val)
  }
})

let queryTags = ref('')
function filterQueryTag (query) {
  queryTags.value = query.toLowerCase()
}

let filteredTags = computed(() => {
  if (queryTags.value) {
    return tags.value.filter((tag) => {
      return tag.name.toLowerCase().includes(queryTags.value)
    })
  }

  return tags.value
})

let locationFilter = computed({
  get: () => {
    return store.getters['params/getLocationIdParam']
  },
  set: (val) => {
    store.commit('params/setLocationIdParam', val)
  }
})

let queryLocation = ref('')
function filterQueryLocation (query) {
  queryLocation.value = query.toLowerCase()
}

let filteredLocations = computed(() => {
  if (queryLocation.value) {
    return locations.value.filter((location) => {
      return location.name.toLowerCase().includes(queryLocation.value)
    })
  }

  return locations.value
})

let dateFilter = computed(() => {
  return store.getters['params/getDates'][0]
})

let typingInSearch = ref(null)
watch([eventSearch, tagFilter, locationFilter, dateFilter], () => {
  if (store.getters['getRestoring'] && !store.getters['getRestored']) {
    store.commit('setRestored', true)

    return
  }

  store.commit('setLoading', true)
  // * Need to set events api for search param
  clearTimeout(typingInSearch.value)
  typingInSearch.value = setTimeout(() => {
    store.commit('pagination/setPage', 1)
    store.dispatch('eventEntities/requestEvents')
  }, 1000)
})

let filtersMenuVisibility = ref(false)

// * Colors
let amColors = inject('amColors')

// * Css Vars
let cssVars = computed(() => {
  return {
    '--am-c-els-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
    '--am-c-els-text-op25': useColorTransparency(amColors.value.colorMainText, 0.25),
    '--am-c-els-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
  }
})
</script>

<script>
export default {
  name: "EventsList",
  key: "eventsList"
}
</script>

<style lang="scss">
@import '../../../../../assets/scss/common/transitions/_transitions-mixin.scss';

.amelia-v2-booking #amelia-container {

  // am - amelia
  // els - events list step
  .am-els {
    //--am-c-els-bgr: var(--am-c-main-bgr); - mozda bi trebalo da se ubaci kao background na event listi
    --am-c-els-text: var(--am-c-main-text);
    --am-c-els-bgr: var(--am-c-main-bgr);

    * {
      box-sizing: border-box;
      font-family: var(--am-font-family), sans-serif;
      word-break: break-word;
    }

    &__available {
      font-size: 18px;
      font-weight: 500;
      line-height: 1.55556;
      color: var(--am-c-els-text);
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
