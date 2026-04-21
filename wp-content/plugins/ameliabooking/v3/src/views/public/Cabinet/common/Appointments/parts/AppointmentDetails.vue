<template>
  <el-form
    ref="detailsFormRef"
    :model="appointmentFormData"
    :rules="appointmentFormRules"
    class="am-capai-def"
  >
    <!-- Select Categories -->
    <el-form-item
      v-if="categories.length > 1"
      :label="`${amLabels.category}`"
      class="am-capai-def__item"
    >
      <AmSelect
        v-model="appointmentFormData.categoryId"
        clearable
        filterable
        :placeholder="`${amLabels.select_service_category}`"
        :filter-method="filterCategory"
      >
        <AmOption
          v-for="entity in queryFilteredCategories"
          :key="entity.id"
          :value="entity.id"
          :label="entity.name"
          :disabled="appointmentFormData.serviceId && typeof filteredCategories.find(i => i.id === entity.id) === 'undefined'"
        />
      </AmSelect>
    </el-form-item>
    <!-- /Select Categories -->

    <!-- Select Services -->
    <el-form-item
      :label="`${amLabels.services_dropdown}`"
      :prop="'serviceId'"
      class="am-capai-def__item"
    >
      <AmSelect
        v-model="appointmentFormData.serviceId"
        :disabled="services.length === 1"
        clearable
        filterable
        :placeholder="`${amLabels.select_service}`"
        :filter-method="filterService"
      >
        <AmOption
          v-for="entity in queryFilteredServices"
          :key="entity.id"
          :value="entity.id"
          :label="entity.name"
          :disabled="typeof filteredServices.find(i => i.id === entity.id) === 'undefined'"
        />
        <AmOption
          v-if="detachedService"
          :key="detachedService.id"
          :value="detachedService.id"
          :label="detachedService.name"
        />
      </AmSelect>
    </el-form-item>
    <!-- /Select Services -->

    <!-- Select Locations -->
    <el-form-item
      v-if="!licence.isStarter && locations.length > 1"
      :label="`${amLabels.location}`"
      class="am-capai-def__item"
    >
      <AmSelect
        v-model="appointmentFormData.locationId"
        :disabled="locations.length === 1"
        clearable
        filterable
        :placeholder="`${amLabels.select_location}`"
        :filter-method="filterLocation"
        @change="changedSlotCondition"
      >
        <AmOption
          v-for="entity in queryFilteredLocations"
          :key="entity.id"
          :value="entity.id"
          :label="entity.name"
          :disabled="detachedLocation === null && typeof filteredLocations.find(i => i.id === entity.id) === 'undefined'"
        />
        <AmOption
          v-if="detachedLocation"
          :key="detachedLocation.id"
          :value="detachedLocation.id"
          :label="detachedLocation.name"
        />
      </AmSelect>
    </el-form-item>
    <!-- /Select Locations -->

    <!-- Select Lesson Space -->
    <el-form-item
      v-if="amSettings.lessonSpace.enabled"
      :label="`${amLabels.lesson_space}`"
      class="am-capai-def__item"
    >
      <AmSelect
        v-model="appointmentFormData.lessonSpace"
        filterable
        clearable
        remote
        :remote-method="searchSpaces"
        :placeholder="`${amLabels.select}`"
      >
        <AmOption
          :key="0"
          :value="0"
          :label="amLabels.lesson_space_new_space"
        />
        <AmOption
          v-for="entity in spaces"
          :key="entity.id"
          :value="entity.id"
          :label="entity.name"
        />
      </AmSelect>
    </el-form-item>
    <!-- /Select Lesson Space -->

    <!-- Select Start Date -->
    <el-form-item
      :label="`${amLabels.date}`"
      :prop="'startDate'"
      class="am-capai-def__item am-w-50"
      :class="responsiveClass"
    >
      <AmDatePicker
        v-model="appointmentFormData.startDate"
        type="date"
        :format="momentDateFormat()"
        :placeholder="amLabels.select_date"
        :clearable="true"
        :lang="localLanguage"
        :disabled="!appointmentFormData.serviceId"
        :disabled-date="(date, monthOrYear) => {return props.isDisabledDate(date, null, true, monthOrYear)}"
        :popper-class="slotsLoading ? 'am-slots-loader' : ''"
        :cell-class-name="getDateClass"
        @panel-change="(date) => {fetchSlots(date, null, true)}"
        @change="selectDate"
        @focus="appointmentFormData.serviceId && !appointmentFormData.startDate ? fetchSlots(null, null, true) : false"
      />
    </el-form-item>
    <!-- /Select Start Date -->

    <!-- Select Start Time -->
    <el-form-item
      :label="`${amLabels.time}`"
      :prop="'startTime'"
      class="am-capai-def__item am-w-50"
      :class="responsiveClass"
    >
      <AmSelect
        v-model="appointmentFormData.startTime"
        :placeholder="`${amLabels.select_time}`"
        :disabled="!appointmentFormData.startDate"
        prefix-icon="clock"
        @change="selectTime"
      >
        <AmOption
          v-for="slot in props.getFreeTimes(null, {date: appointmentFormData.startDate, times: props.times, time: appointmentFormData.startTime}, true)"
          :key="slot"
          :value="slot"
          :label="slot"
        >
          {{ getFrontedFormattedTime(slot, 'UTC') }} <span style="float: right;">{{ getTimePrice(slot) }}</span>
        </AmOption>
      </AmSelect>
    </el-form-item>
    <!-- /Select Start Time -->

    <!-- Notify Customers -->
    <el-form-item class="am-capai-def__item">
      <div class="am-capai-def__notify">
        <AmCheckbox
          v-model="appointmentFormData.notifyParticipants"
          :label="amLabels.notify_customers"
        />
        <el-tooltip
          effect="dark"
          placement="top"
        >
          <template #content>
            <div v-html="amLabels.notify_customers_tooltip"></div>
          </template>
          <IconComponent icon="info-reverse" />
        </el-tooltip>
      </div>
    </el-form-item>
    <!-- /Notify Customers -->

    <!-- Create payment links -->
    <el-form-item v-if="appointmentFormData.notifyParticipants && appointmentFormData.serviceId && isPaymentLinkEnabled(appointmentFormData.serviceId)" class="am-capai-def__item">
      <div class="am-capai-def__notify">
        <AmCheckbox
            v-model="appointmentFormData.createPaymentLinks"
            :label="amLabels.generate_payment_links"
        />
        <el-tooltip
            effect="dark"
            placement="top"
        >
          <template #content>
            <div v-html="amLabels.generate_payment_links_tooltip"></div>
          </template>
          <IconComponent icon="info-reverse" />
        </el-tooltip>
      </div>
    </el-form-item>
    <!-- /Notify Customers -->

    <!-- Internal Notes -->
    <el-form-item
      :label="`${amLabels.note_internal}`"
      class="am-capai-def__item"
    >
      <AmInput
        v-model="appointmentFormData.internalNotes"
        :type="'textarea'"
      />
    </el-form-item>
    <!-- /Internal Notes -->
  </el-form>
</template>

<script setup>
// * Import form Vue
import {
  computed,
  ref,
  inject,
  watch,
  onMounted,
} from "vue";

// * Import store
import { useStore } from "vuex";

import { useElementSize } from "@vueuse/core";

// * Import libraries
import moment from 'moment'

// * Import Composables
import {
  momentDateFormat,
  getFrontedFormattedTime,
} from "../../../../../../assets/js/common/date";

// * Import Components
import AmCheckbox from "../../../../../_components/checkbox/AmCheckbox.vue";
import AmDatePicker from "../../../../../_components/datePicker/AmDatePicker.vue";
import AmOption from "../../../../../_components/select/AmOption.vue";
import AmInput from "../../../../../_components/input/AmInput.vue";
import AmSelect from "../../../../../_components/select/AmSelect.vue";
import IconComponent from "../../../../../_components/icons/IconComponent.vue";
import {useResponsiveClass} from "../../../../../../assets/js/common/responsive";
import httpClient from "../../../../../../plugins/axios";

// * Component props
let props = defineProps({
  slotsProps: {
    type: Object,
    required: true,
  },
  slotsLoading: {
    type: Boolean,
    required: true,
  },
  isDisabledDate: {
    type: Function,
    required: true,
  },
  freeSlots: {
    type: Object,
    required: true,
  },
  times: {
    type: Array,
    required: true,
  },
  getFreeTimes: {
    type: Function,
    required: true,
  },
})

// * Emits
let emits = defineEmits(['removeSlots', 'fetchSlots', 'changedSlotCondition', 'getSelectedDatesTimes', 'filterOut', 'getFreeTimes', 'selectDate', 'selectTime'])

// * Store
let store = useStore();

// * Labels
let amLabels = inject('amLabels')

// * Language
let localLanguage = inject('localLanguage')

// * Root Settings
const amSettings = inject('settings')

// * Plugin Licence
let licence = inject('licence')

let slotsPricing = inject('slotsPricing')

// let startDateString = computed(() => {
//   return appointmentFormData.value.startDate ? moment(appointmentFormData.value.startDate).format('YYYY-MM-DD') : ''
// })

function getTimePrice (slot) {
  // if (startDateString.value in slotsPricing.value && slot in slotsPricing.value[startDateString.value].slots) {
  //   return useFormattedPrice(slotsPricing.value[startDateString.value].slots[slot].price)
  // }

  return ''
}

let getDateClass = (date) => {
  let dateString = moment(date).format('YYYY-MM-DD')

  if (dateString in slotsPricing.value) {
    return 'am-slots-' + slotsPricing.value[dateString].type
  }

  return ''
}

// * Form Reference
const detailsFormRef = ref(null)

// * Tab Width
let { width: pageWidth } = useElementSize(detailsFormRef)

let responsiveClass = computed(() => {
  return useResponsiveClass(pageWidth.value)
})

// * Form data
let appointmentFormData = ref({
  categoryId: computed({
    get: () => store.getters['appointment/getCategoryId'],
    set: (val) => {
      store.commit('appointment/setCategoryId', val ? val : null)
    }
  }),
  serviceId: computed({
    get: () => store.getters['appointment/getServiceId'],
    set: (val) => {
      store.commit('appointment/setServiceId', val ? val : null)
    }
  }),
  providerId: computed({
    get: () => store.getters['appointment/getProviderId'],
    set: (val) => {
      store.commit('appointment/setProviderId', val ? val : null)
    }
  }),
  locationId: computed({
    get: () => store.getters['appointment/getLocationId'],
    set: (val) => {
      store.commit('appointment/setLocationId', val ? val : null)
    }
  }),
  lessonSpace: computed({
    get: () => store.getters['appointment/getLessonSpace'],
    set: (val) => {
      store.commit('appointment/setLessonSpace', val ? val : '')
    }
  }),
  startDate: computed({
    get: () => store.getters['appointment/getStartDate'],
    set: (val) => {
      store.commit('appointment/setStartDate', val ? val : '')
    }
  }),
  startTime: computed({
    get: () => store.getters['appointment/getStartTime'],
    set: (val) => {
      store.commit('appointment/setStartTime', val ? val : '')
    }
  }),
  notifyParticipants: computed({
    get: () => store.getters['appointment/getNotifyParticipants'],
    set: (val) => {
      store.commit('appointment/setNotifyParticipants', val)
    }
  }),
  createPaymentLinks: computed({
    get: () => store.getters['appointment/getCreatePaymentLinks'],
    set: (val) => {
      store.commit('appointment/setCreatePaymentLinks', val)
    }
  }),
  internalNotes: computed({
    get: () => store.getters['appointment/getInternalNotes'],
    set: (val) => {
      store.commit('appointment/setInternalNotes', val ? val : '')
    }
  }),
})

// * Form rules
let appointmentFormRules = computed(() => {
  return {
    serviceId: [
      {
        required: true,
        message: amLabels.value.please_select_service,
        trigger: ['submit', 'change'],
      }
    ],
    startDate: [
      {
        required: true,
        message: amLabels.value.select_date_warning,
        trigger: ['submit', 'change'],
      }
    ],
    startTime: [
      {
        required: true,
        message: amLabels.value.select_time_warning,
        trigger: ['submit', 'change'],
      }
    ]
  }
})

// * Categories
let filteredCategories = computed(
  () => store.getters['entities/filteredCategories'](
    store.getters['appointment/getSelection']
  )
)

let categories = ref(
  store.getters['entities/filteredCategories'](
    {
      categoryId: null,
      serviceId: null,
      providerId: store.getters['appointment/getProviderId'],
      locationId: null,
    }
  )
)

let queryCategory = ref('')
function filterCategory (query) {
  queryCategory.value = query.toLowerCase()
}

let queryFilteredCategories = computed(() => {
  if (queryCategory.value) {
    return categories.value.filter((category) => {
      return category.name.toLowerCase().includes(queryCategory.value)
    })
  }

  return categories.value
})

// * Services
let filteredServices = computed(
  () => store.getters['entities/filteredServices'](
    store.getters['appointment/getSelection']
  )
)

let services = ref(
  store.getters['entities/filteredServices'](
    {
      categoryId: null,
      serviceId: null,
      providerId: store.getters['appointment/getProviderId'],
      locationId: null,
    }
  )
)

function isPaymentLinkEnabled (serviceId) {
  let service = store.getters['entities/getService'](serviceId)
  let entitySettings = service && service.settings ? JSON.parse(service.settings) : null

  let paymentLinksEnabled = entitySettings
  && 'payments' in entitySettings
  && entitySettings.payments
  && 'paymentLinks' in entitySettings.payments
  && entitySettings.payments.paymentLinks
  && 'enabled' in entitySettings.payments.paymentLinks
      ? entitySettings.payments.paymentLinks
      : amSettings.payments.paymentLinks

  return paymentLinksEnabled && paymentLinksEnabled.enabled
}

let queryService = ref('')
function filterService (query) {
  queryService.value = query.toLowerCase()
}
let queryFilteredServices = computed(() => {
  if (queryService.value) {
    return services.value.filter((service) => {
      return service.name.toLowerCase().includes(queryService.value)
    })
  }

  return services.value
})

let detachedService = computed(() => {
  return store.getters['appointment/getId'] &&
    store.getters['appointment/getServiceId'] &&
    services.value.map(i => i.id).indexOf(store.getters['appointment/getServiceId']) === -1
    ? store.getters['entities/getService'](store.getters['appointment/getServiceId'])
    : null
})

// * Locations
let filteredLocations = computed(
  () => store.getters['entities/filteredLocations'](
    store.getters['appointment/getSelection']
  )
)

let locations = ref(
  store.getters['entities/filteredLocations'](
    {
      categoryId: null,
      serviceId: null,
      providerId: store.getters['appointment/getProviderId'],
      locationId: null,
    }
  )
)

let queryLocation = ref('')
function filterLocation (query) {
  queryLocation.value = query.toLowerCase()
}
let queryFilteredLocations = computed(() => {
  if (queryLocation.value) {
    return locations.value.filter((location) => {
      return location.name.toLowerCase().includes(queryLocation.value)
    })
  }

  return locations.value
})

let detachedLocation = computed(() => {
  return store.getters['appointment/getId'] &&
    store.getters['appointment/getLocationId'] &&
    locations.value .map(i => i.id).indexOf(store.getters['appointment/getLocationId']) === -1
    ? store.getters['entities/getLocation'](store.getters['appointment/getLocationId'])
    : null
})

let spaces = ref(
  store.getters['entities/getSpaces']
)

/*****
 * Actions *
 *****/
function removeSlots () {
  store.commit('appointment/setTargetedDate', null)
  store.commit('appointment/setStartDate', null)
  store.commit('appointment/setStartTime', null)
  emits('removeSlots')
}

function fetchSlots (selectedDate, recurringIndex = null, isNavigation = false) {
  let targetedDate = store.getters['appointment/getTargetedDate']

  if (!selectedDate && !targetedDate) {
    selectedDate = moment().toDate()
  } else if (!selectedDate && targetedDate) {
    selectedDate = targetedDate
  }

  store.commit('appointment/setTargetedDate', selectedDate && isNavigation ? selectedDate : targetedDate)

  if (appointmentFormData.value.serviceId) {
    emits('fetchSlots', selectedDate, recurringIndex, isNavigation)
  }
}

function changedSlotCondition () {
  if (appointmentFormData.value.startDate) {
    fetchSlots(appointmentFormData.value.startDate)
  }
}

function selectTime(value) {
  emits('selectTime', value)
}

function selectDate (value) {
  if (!value) {
    removeSlots()
  } else {
    emits('selectDate', value)
  }
}

function searchSpaces (query) {
  if (query) {
    clearTimeout(this.searchSpacesTimer)

    store.commit('auth/setSpacesLoading', true)

    httpClient.get(
      '/entities',
      {
        params: {types: ['spaces'], lessonSpaceSearch: query}
      }
    ).then((response) => {
      spaces.value = response.data.data.spaces
    }).catch((e) => {
      console.log(e)
    }).finally(() => {
      store.commit('auth/setSpacesLoading', false)
    })
  } else {
    spaces.value = store.getters['entities/getSpaces']
  }
}

watch (() => appointmentFormData.value.serviceId, (value) => {
  // * Update employee service and set filtered custom fields
  store.dispatch('appointment/updateEmployeeService', value ? value : null)

  if (value) {
    store.dispatch('appointment/recreateAllBookingCustomFields', {label: amLabels.value.required_field})

    changedSlotCondition()
  }
})

onMounted(() => {
  if (services.value.length === 1 && !store.getters['appointment/getId']) {
    store.commit('appointment/setServiceId', services.value[0].id)
  }

  if (locations.value.length === 1 && !store.getters['appointment/getId']) {
    store.commit('appointment/setLocationId', locations.value[0].id)
  }
})

// * Expose
defineExpose({
  detailsFormRef
})
</script>

<style lang="scss">
@mixin am-cabinet-appointment-item {
  // am    - amelia
  // capai - cabinet-panel-appointment-item
  // def   - details form

  .el-form.am-capai-def {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;

    .am-capai-def{
      &__item {
        width: 100%;
        margin-bottom: 24px;

        &.am-w-50 {
          width: calc(50% - 12px);

          &.am-rw-480 {
            width: 100%;
          }
        }

        .el-checkbox__input, .el-checkbox__label {
          align-self: unset;
          line-height: 1;
          padding: 0;
        }

        .el-form-item__error {
          font-size: 12px;
          color: var(--am-c-error);
          padding: 0;
        }
      }

      &__notify {
        display: flex;
        align-items: center;

        .am-checkbox-wrapper {
          margin: 0 6px 0 0;
        }

        .am-icon-info-reverse {
          font-size: 20px;
          color: var(--am-c-main-text);
        }
      }
    }
  }
}

//.am-slots-low {
//  background-color: rgba(196, 255, 201, 1);
//  border-radius: 50%;
//}
//
//.am-slots-mid {
//  background-color: rgba(196, 235, 255, 1);
//  border-radius: 50%;
//}
//
//.am-slots-high {
//  background-color: rgba(255, 196, 196, 1);
//  border-radius: 50%;
//}

.am-slots-loader {
  &:before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    background-color: var(--am-c-drop-text-op10);
    z-index: 10;
  }

  &::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    border: 6px solid var(--am-c-drop-bgr);
    animation: spin 1s linear infinite;
    top: calc(50% - 35px);
    left: calc(50% - 35px);
    width: 70px;
    height: 70px;
    border-top-color: var(--am-c-drop-text);
    z-index: 11;
  }
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-appointment-item;
}
</style>