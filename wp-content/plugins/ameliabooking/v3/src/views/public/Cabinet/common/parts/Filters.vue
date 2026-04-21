<template>
  <div
    v-if="!loading && ready"
    class="am-capf"
    :style="cssVars"
  >
    <div v-if="cWidth <= 480 && customizedOptions.timeZone.visibility && props.stepKey !== 'packages'" class="am-capf__zone">
      <TimeZoneSelect size="small"/>
    </div>
    <div class="am-capf__menu">
      <TimeZoneSelect
        v-if="cWidth <= 480 && customizedOptions.timeZone.visibility && props.stepKey === 'packages'"
        size="small"
      />
      <AmDatePicker
        v-if="props.stepKey !== 'packages'"
        v-model="selection.dates"
        type="daterange"
        :editable="false"
        size="small"
        :clearable="false"
        :format="momentDateFormat()"
        :style="cssVars"
        :lang="localLanguage"
        :class="['am-capf__menu-datepicker', props.responsiveClass]"
        :popper-class="'am-capf__menu-datepicker-popper'"
        :start-placeholder="amLabels.start_date"
        :end-placeholder="amLabels.end_date"
      />
      <template v-if="!props.empty && filterCustomizeVisibility && filterVisibility">
        <AmButton
          :icon="filterIcon"
          :icon-only="cWidth <= 700"
          custom-class="am-capf__menu-btn"
          size="small"
          category="primary"
          :aria-label="amLabels.filters"
          :type="customizedOptions.filterBtn ? customizedOptions.filterBtn.buttonType : 'filled'"
          @click="filtersMenuVisibility = !filtersMenuVisibility"
        >
          <span class="am-icon-filter"/>
          <span v-if="cWidth > 700">{{amLabels.filters}}</span>
        </AmButton>
      </template>
    </div>
    <Transition name="am-slide-fade">
      <div v-if="filtersMenuVisibility && !props.empty" class="am-capf__list">
        <template
          v-for="(name) in Object.keys(options)"
          :key="name"
        >
          <span
            v-if="(options[name].length > 0 || name === 'customers' || name === 'events') && amCustomize[objectRecognition].options[`${name}Filter`].visibility"
            class="am-capf__list-item"
            :class="[{'am-selected': selection[name].length > 0}, `am-capf__list-item-${itemsNumber}`, props.responsiveClass]"
          >
            <AmSelect
              :id="`am-select-${name}`"
              v-model="selection[name]"
              :multiple="name !== 'customers' && name !== 'events'"
              :loading="name !== 'customers' && name !== 'events' ? false : loadingInput[name]"
              remote
              clearable
              :filterable="name === 'customers' || name === 'events'"
              :placeholder="amLabels[name + '' + (name === 'customers' ? '' : '_dropdown')]"
              :prefix-icon="inputIcon(name)"
              :collapse-tags="true"
              size="small"
              :popper-class="'am-filter-select-popper'"
              :remote-method="(val) => { searchFilter(name, val) }"
              @change="changeFilters"
              @clear="loadDefault"
            >
              <AmOption
                v-for="entity in options[name]"
                :key="entity.id"
                :value="entity.id"
                :label="entity.firstName ? (entity.firstName + ' ' + entity.lastName) : entity.name"
              />
            </AmSelect>
          </span>
        </template>
      </div>
    </Transition>
  </div>
</template>

<script setup>
// * _components
import AmDatePicker from "../../../../_components/datePicker/AmDatePicker.vue";
import AmSelect from "../../../../_components/select/AmSelect.vue";
import AmOption from "../../../../_components/select/AmOption.vue";

// * Components
import TimeZoneSelect from "./TimeZoneSelect.vue";

// * Import from Vue
import {
  ref,
  reactive,
  computed,
  inject,
  onMounted,
} from "vue";

// * Composables
import {
  useColorTransparency
} from "../../../../../assets/js/common/colorManipulation";
import {
  useCustomers,
} from "../../../../../assets/js/admin/customers";
import {
  useEvents,
} from "../../../../../assets/js/public/events";

import {
  momentDateFormat,
  setDatePickerSelectedDaysCount
} from "../../../../../assets/js/common/date";

// * Import from Vuex
import { useStore } from "vuex";
import AmButton from "../../../../_components/button/AmButton.vue";
import IconComponent from "../../../../_components/icons/IconComponent.vue";
import moment from "moment";
import {settings} from "../../../../../plugins/settings";

// * Component properties
let props = defineProps({
  paramList: {
    type: [Object, Array],
    default: () => []
  },
  stepKey: {
    type: String,
    default: ''
  },
  responsiveClass: {
    type: String,
    default: ''
  },
  empty: {
    type: Boolean,
    default: false
  }
})

// * Component emits
const emits = defineEmits([
  'changeFilters',
  'addAppointment',
])

// * Store
let store = useStore()

let ready = computed(() => store.getters['entities/getReady'])

// * Loading
let loading = computed(() => store.getters['getLoading'])

// * Loading input data
let loadingInput = ref({
  customers: false,
  events: false,
})

let objectRecognition = computed(() => props.stepKey === 'packages' ? 'packagesList' : props.stepKey )

// * Data in shortcode
const shortcodeData = inject('shortcodeData')

// * Root Settings
const amSettings = inject('settings')

// * Customized form data
let amCustomize = inject('amCustomize')

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  if (props.stepKey) {
    let customizedLabels = amCustomize.value[objectRecognition.value].translations
    if (customizedLabels) {
      Object.keys(customizedLabels).forEach(labelKey => {
        if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
          computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
        } else if (customizedLabels[labelKey].default) {
          computedLabels[labelKey] = customizedLabels[labelKey].default
        }
      })
    }
  }

  return computedLabels
})

function inputIcon (name) {
  if (name === 'customers') return 'user'
  if (name === 'services') return 'service'
  if (name === 'providers') return 'employee'
  if (name === 'locations') return 'locations'
  if (name === 'packages') return 'shipment'
  if (name === 'events') return 'star-outline'
  return ''
}

function changeFilters () {
  emits('changeFilters')
}

let cWidth = inject('containerWidth')

let filterIcon = {
  components: {IconComponent},
  template: `<IconComponent icon="filter"/>`
}

let filtersMenuVisibility = ref(false)

// * Filters model
let selection = ref({
  dates: computed({
    get: () => store.getters['cabinetFilters/getDates'],
    set: (val) => {
      setDatePickerSelectedDaysCount(val[0], val[1])
      store.commit('cabinetFilters/setDates', val)
      emits('changeFilters')
    }
  }),
  customers: computed({
    get: () => store.getters['cabinetFilters/getCustomers'],
    set: (val) => {
      store.commit('cabinetFilters/setCustomers', val ? val : [])
    }
  }),
  services: computed({
    get: () => store.getters['cabinetFilters/getServices'],
    set: (val) => {
      store.commit('cabinetFilters/setServices', val)
    }
  }),
  providers: computed({
    get: () => store.getters['cabinetFilters/getProviders'],
    set: (val) => {
      store.commit('cabinetFilters/setProviders', val)
    }
  }),
  locations: computed({
    get: () => store.getters['cabinetFilters/getLocations'],
    set: (val) => {
      store.commit('cabinetFilters/setLocations', val)
    }
  }),
  packages: computed({
    get: () => store.getters['cabinetFilters/getPackages'],
    set: (val) => {
      store.commit('cabinetFilters/setPackages', val)
    }
  }),
  events: computed({
    get: () => store.getters['cabinetFilters/getEvents'],
    set: (val) => {
      store.commit('cabinetFilters/setEvents', val ? val : [])
    }
  })
})

// * Filters options
let options = computed(() => {
  if (props.stepKey === 'events') return store.getters['cabinetFilters/getEventFiltersOption'](shortcodeData.value.cabinetType)
  if (props.stepKey === 'packages') return store.getters['cabinetFilters/getPackageFilterOptions']
  return store.getters['cabinetFilters/getAppointmentFilterOptions'](shortcodeData.value.cabinetType)
})

let itemsNumber = computed(() => {
  let numb = 0
  Object.keys(options.value).forEach(name => {
    if (amCustomize.value[objectRecognition.value].options[`${name}Filter`].visibility) {
      if (name === 'customers' || name === 'events') {
        numb++
      } else if (options.value[name].length > 0) {
        numb++
      }
    }
  })

  return numb
})

function searchFilter(name, val) {
  switch (name) {
    case 'customers':
      searchCustomers(val)

      break
    case 'events':
      searchEvents(val)

      break
  }
}

function loadDefault () {
  if (shortcodeData.value.cabinetType === 'employee') {
    searchCustomers('', store.getters['auth/getPreloadedCustomers'])
  }
}

function searchCustomers(val = '', customers = []) {
  loadingInput.value.customers = true
  if (customers.length) {
    store.dispatch('cabinetFilters/injectCustomerOptions', customers)
    loadingInput.value.customers = false

    return
  }

  setTimeout(
    () => {
      useCustomers(
        {
          search: val,
          page: 1,
          limit: settings.general.customersFilterLimit,
          skipCount: 1,
        },
        (result) => {
          store.dispatch(
            'cabinetFilters/injectCustomerOptions',
            result
          )

          if (store.getters['auth/getPreloadedCustomers'].length === 0) {
            store.commit('auth/setPreloadedCustomers', result)
          }

          loadingInput.value.customers = false
        }
      )
    },
    500
  )
}

// * Cabinet type
const cabinetType = inject('cabinetType')

function searchEvents(val = '', events = []) {
  loadingInput.value.events = true
  if (events.length) {
    store.dispatch('cabinetFilters/injectEventsOptions', events)
    loadingInput.value.events = false

    return
  }

  setTimeout(
    () => {
      useEvents(
        {
          dates: [moment().format('YYYY-MM-DD')],
          timeZone: store.getters['cabinet/getTimeZone'],
          group: true,
          page: 1,
          limit: amSettings.general.itemsPerPage,
          search: val,
          source: 'cabinet-' + cabinetType.value
        },
        (result) => {
          store.dispatch(
            'cabinetFilters/injectEventsOptions',
            result
          )

          loadingInput.value.events = false
        }
      )
    },
    500
  )
}

let customizedOptions = computed(() => {
  return amCustomize.value[objectRecognition.value].options
})

let filterCustomizeVisibility = computed(() => {
  if (props.stepKey === 'appointments') {
    if (shortcodeData.value.cabinetType === 'employee') return customizedOptions.value.servicesFilter.visibility || customizedOptions.value.customersFilter.visibility || customizedOptions.value.locationsFilter.visibility
    return customizedOptions.value.servicesFilter.visibility || customizedOptions.value.providersFilter.visibility || customizedOptions.value.locationsFilter.visibility
  }
  if (props.stepKey === 'events') {
    if (shortcodeData.value.cabinetType === 'employee') return customizedOptions.value.eventsFilter.visibility || customizedOptions.value.customersFilter.visibility || customizedOptions.value.locationsFilter.visibility
    return customizedOptions.value.eventsFilter.visibility || customizedOptions.value.providersFilter.visibility || customizedOptions.value.locationsFilter.visibility
  }
  return customizedOptions.value.packagesFilter.visibility || customizedOptions.value.servicesFilter.visibility || customizedOptions.value.providersFilter.visibility || customizedOptions.value.locationsFilter.visibility
})

let filterVisibility = computed(() => {
  if (props.stepKey === 'appointments') {
    if (shortcodeData.value.cabinetType === 'employee') return !!(options.value.services.length || options.value.customers.length || options.value.locations.length)
    return !!(options.value.services.length || options.value.providers.length || options.value.locations.length)
  }
  if (props.stepKey === 'events') {
    if (shortcodeData.value.cabinetType === 'employee') return !!(options.value.events.length || options.value.customers.length || options.value.locations.length)
    return !!(options.value.events.length || options.value.providers.length || options.value.locations.length)
  }
  return !!(options.value.packages.length || options.value.services.length || options.value.providers.length || options.value.locations.length)
})

onMounted(() => {
  loadDefault()
})

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-capf-heading-text': amColors.value.colorMainText,
    '--am-c-capf-text': amColors.value.colorInpText,
    '--am-c-capf-text-op10': useColorTransparency(amColors.value.colorInpText, 0.10),
    '--am-c-select-border': amColors.value.colorInpBorder,
    '--am-c-select-bgr': amColors.value.colorInpBgr
  }
})
</script>

<script>
export default {
  name: "CabinetFilters"
}
</script>

<style lang="scss">
@import '../../../../../assets/scss/common/transitions/_transitions-mixin.scss';

.amelia-v2-booking {
  #amelia-container {
    // am - amelia
    // capf - catalog panel filters
    .am-capf{
      @include slide-fade;
      margin-bottom: 20px;

      &__zone {
        padding: 0 0 16px;
      }

      &__heading {
        font-size: 14px;
        font-weight: 500;
        line-height: 1.42857;
        color: var(--am-c-capf-heading-text);
        margin-bottom: 8px;
      }

      &__list {
        display: flex;
        flex-wrap: wrap;
        margin-top: 10px;
        justify-content: space-between;

        &-item {
          position: relative;

          .el-input {
            &__prefix {
              font-size: 24px;
              color: var(--am-c-capf-text);
            }

            &__inner {
              font-weight: 500;
              font-size: 14px;
              color: var(--am-c-capf-text);
              padding-left: 40px !important;

              &::placeholder {
                color: var(--am-c-capf-text);
              }
            }
          }

          .el-select {
            &__tags {
              display: flex;
              max-width: calc(100% - 68px) !important;
              left: 40px;

              & > span {
                display: flex;
                width: 100%;
                flex-wrap: nowrap;
              }

              &-text {
                max-width: unset !important;
                width: 100%;
                font-size: 14px;
                color: var(--am-c-capf-text);
              }

              .el-tag {
                min-width: 46px;
                overflow: hidden;
                background-color: transparent;
                margin: 0;
                padding: 0;
                border: none;

                &.is-closable {
                  .el-select__tags-text {
                    display: block;
                  }
                }

                &__content {
                  width: 100%;
                }

                &__close {
                  display: none;
                }
              }
            }
          }

          &.am-selected {
            .el-input {
              &__suffix {
                &-inner {
                  display: none;
                }
              }
            }
          }

          &.am-capf__list-item-4 {
            width: calc(25% - 2px);

            &.am-rw-650 {
              width: calc(50% - 2px);
              margin: 0 0 4px;
            }

            &.am-rw-500 {
              width: 100%;
              margin: 0 0 4px;
            }
          }

          &.am-capf__list-item-3 {
            width: calc(33.333% - 2px);

            &.am-rw-500 {
              width: 100%;
              margin: 0 0 4px;
            }
          }

          &.am-capf__list-item-2 {
            width: calc(50% - 2px);

            &.am-rw-500 {
              width: 100%;
              margin: 0 0 4px;
            }
          }

          &.am-capf__list-item-1 {
            width: 100%;
          }
        }
      }

      &__menu {
        display: flex;
        flex-wrap: nowrap;
        justify-content: right;
        gap: 0 16px;

        &-datepicker {
          &.am-rw- {
            &400 {
              .el-date-editor.el-date-editor--daterange {
                &.el-input__wrapper {
                  height: 48px;
                }
              }
            }
          }
        }

        &-btn {
          border-radius: 6px;
          box-shadow: 0 5px 5px var(--am-c-capf-text-op10);

          .am-icon-filter {
            font-size: 24px;
          }

          .am-icon-plus {
            font-size: 24px;
          }
        }

      }

      &__clear {
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        width: 13px;
        height: 32px;
        top: 0;
        right: 10px;
        color: var(--am-c-primary);
        cursor: pointer;
        z-index: 999;
      }
    }
  }
}

.am-filter-select-popper {
  .am-select-option {
    &.selected {
      span {
        max-width: calc(100% - 20px);
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      span:after {
        position: absolute;
        right: 10px;
        content: "\2713";
      }
    }
  }
}


.am-capf__menu-datepicker-popper {
  @media only screen and (max-width: 660px) {
    left: 0 !important;
    width: 100%;

    .el-picker-panel.el-date-range-picker {
      width: 100%;

      .el-picker-panel__body {
        min-width: unset;

        @media only screen and (max-width: 570px) {
          display: flex;
          flex-direction: column;

          .el-date-range-picker__content {
            width: 100%;

            &.is-left {
              border: none;
            }
          }
        }
      }
    }
  }
}
</style>