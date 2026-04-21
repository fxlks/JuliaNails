<template>
  <StepCardLayout
    ref="stepCardLayoutReference"
    :custom-class="[props.globalClass, 'am-fs-service-step']"
    :allow-popup="true"
    :card-selected="store.getters['booking/getServiceId'] !== null"
  >
    <template #filters>
      <AmInput
        v-if="customizeOptions.search.visibility"
        v-model="searchFilter"
        :placeholder="amLabels.filter_input"
        :prefix-icon="iconSearch"
      />
      <AmSelect
        v-if="customizeOptions.category.visibility && categoryOptions.length > 1"
        v-model="categoryFilter"
        :placeholder="amLabels.select_service_category"
        class="am-fs__init-category-select"
        :no-match-text="amLabels.dropdown_empty"
        :filterable="customizeOptions.category.filterable"
        :fit-input-width="true"
        clearable
        :filter-method="filterCategory"
      >
        <AmOption
          v-for="category in filteredCategories"
          :key="category.id"
          :value="category.id"
          :label="category.name"
        >
          {{ category.name }}
        </AmOption>
      </AmSelect>
    </template>

    <!-- Card -->
    <StepCard
      v-for="service in serviceOptions"
      :key="service.id"
      :item="service"
      :selected="store.getters['booking/getServiceId'] === service.id"
      :disabled="disabledService(service)"
      :is-person="false"
      :item-name="service.name"
      :price="calculateServicePrice(service.id)"
      :price-visibility="customizeOptions.price.visibility"
      :tax-visibility="customizeOptions.tax.visibility && useTaxVisibility(store, service.id, 'service')"
      :tax-excluded="amSettings.payments.taxes.excluded"
      :info-items="serviceInfoArray(service)"
      :info-btn-visibility="customizeOptions.moreBtn.visibility"
      :packages-btn-visibility="
        !!(!licence.isLite &&
        !licence.isStarter &&
        servicePackage(service.id).length &&
        shortcodeData.show !== 'services' &&
        useCart(store).length <= 1) && customizeOptions.packagesBtn.visibility
      "
      :parent-width="componentWidth"
      :labels="amLabels"
      @select-item="selectService(service)"
      @trigger-info-popup="() => {
        moreInfoVisibility = true
        selectedService = service
      }"
      @trigger-packages-popup="triggerPackagesPopup(service)"
    />
    <!-- /Card -->

    <!-- Empty State -->
    <EmptyState
      v-if="serviceOptions.length === 0"
      :heading="amLabels.no_results_found"
    />
    <!-- /Empty State -->
  </StepCardLayout>

  <!-- More Info -->
  <MoreInfoPopup
    v-if="moreInfoVisibility"
    v-model:visibility="moreInfoVisibility"
    :heading="amLabels.service_information"
    :item-name="selectedService.name"
    :item="selectedService"
    :employees-heading="amLabels.employees"
    :employees-data="displayServiceEmployees(selectedService.id)"
    :locations-heading="amLabels.locations"
    :locations-data="displayServiceLocation(selectedService.id)"
  />
  <!-- /More Info -->
</template>

<script setup>
// * Import from Vue
import { ref, computed, inject, nextTick, watchEffect, reactive, onMounted, onUnmounted } from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Import components
import StepCardLayout from '../common/StepCardLayout.vue'
import AmInput from '../../../_components/input/AmInput.vue'
import AmSelect from '../../../_components/select/AmSelect.vue'
import AmOption from '../../../_components/select/AmOption.vue'
import IconComponent from '../../../_components/icons/IconComponent.vue'
import MoreInfoPopup from '../common/MoreInfoPopup.vue'
import StepCard from '../common/StepCard.vue'
import EmptyState from '../../Cabinet/common/parts/EmptyState.vue'

// * Composables
import useAction from '../../../../assets/js/public/actions'
import {
  useServiceLocation,
  useEmployeesServiceCapacity,
  useServiceDuration,
  useServicePrice,
} from '../../../../assets/js/public/catalog'
import { useTaxVisibility } from '../../../../assets/js/common/pricing'
import { useFormattedPrice } from '../../../../assets/js/common/formatting'
import { useElementSize } from '@vueuse/core'
import { useCart } from '../../../../assets/js/public/cart'

import  { defaultCustomizeSettings } from '../../../../assets/js/common/defaultCustomize'

// * Store
const store = useStore()

// * Props
const props = defineProps({
  globalClass: {
    type: String,
    default: '',
  },
})

// * Short Code
const shortcodeData = inject('shortcodeData')

// * Customize
const amCustomize = inject('amCustomize')
let customizeOptions =  computed(() => amCustomize.serviceStep?.options || defaultCustomizeSettings.sbsNew.serviceStep.options)

// * Component reference
const stepCardLayoutReference = ref(null)
const stepCardLayoutDom = computed(
  () => stepCardLayoutReference.value?.stepCardLayoutRef || null
)

// * Component width
const { width: componentWidth } = useElementSize(stepCardLayoutDom)

// * Plugin Licence
let licence = inject('licence')

// * Amelia Settings
const amSettings = inject('settings')

// * Labels
let labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Labels
const amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  const customizedLabels = amCustomize.serviceStep?.translations

  if (customizedLabels) {
    Object.keys(customizedLabels).forEach((labelKey) => {
      const labelData = customizedLabels[labelKey]
      const localizedLabel = labelData[localLanguage.value]

      if (localizedLabel && langDetection.value) {
        computedLabels[labelKey] = localizedLabel
      } else if (labelData.default) {
        computedLabels[labelKey] = labelData.default
      }
    })
  }

  return computedLabels
})

// * Sidebar steps
let { sidebarDataCollector } = inject('sidebarStepsFunctions', {
  sidebarDataCollector: () => {},
})

const { changeInitStepDataService } = inject('initDataChanges', {
  changeInitStepDataService: () => {},
})

const { footerBtnDisabledUpdater } = inject('changingStepsFunctions', {
  footerBtnDisabledUpdater: () => {},
})

// * Entities
let amEntities = computed(() => {
  return store.state.entities
})

const locationsRules = computed(() => store.getters['entities/getLocations'].length <= 1)
const employeesRules = computed(() => store.getters['entities/getEmployees'].length === 1)

// * Set Service and Category Options from entities
let initStepOrder = [
  { id: 'ServiceStep'},
  { id: 'EmployeeStep' },
  { id: 'LocationStep' },
]

let stepOrder = computed(() => {
  return 'order' in amCustomize ? amCustomize.order : initStepOrder
})

function filterRules (entityName = '') {
  const idx = stepOrder.value.findIndex(step => step.id === 'ServiceStep')
  let arrRules = stepOrder.value.slice(0, idx)
  const obj = { serviceId: null, categoryId: entityName === 'category' ? null : categoryFilter.value, providerId: null, locationId: null }
  if (arrRules.some(step => step.id === 'EmployeeStep') || employeesRules.value) delete obj.providerId
  if (arrRules.some(step => step.id === 'LocationStep') || locationsRules.value) delete obj.locationId
  return obj
}

// * FILTER
let searchFilter = computed({
  get: () => store.getters['stepByStepFilters/getSearchFilterText'],
  set: (value) => {
    store.commit('stepByStepFilters/setSearchFilterText', value)
  }
})

let iconSearch = {
  components: { IconComponent },
  template: `<IconComponent icon="search"/>`,
}
function searchingStrings(name) {
  let arr = []
  searchFilter.value
    .toLowerCase()
    .split(' ')
    .forEach((item) => {
      arr.push(name.toLowerCase().includes(item))
    })

  return arr.filter((a) => a === false).length <= 0
}

// * Category Filter
const categoryFilter = computed({
  get: () => store.getters['stepByStepFilters/getCategoryFilter'],
  set: (value) => {
    value = value ? value : null
    store.commit('stepByStepFilters/setCategoryFilter', value)
  }
})

// * Set Category Options from entities
let categoryOptions = computed(() =>
  store.getters['entities/filteredCategories'](
    Object.assign(store.getters['booking/getSelection'], filterRules('category'))
  )
)

let queryLower = ref('')
function filterCategory(query) {
  queryLower.value = query.toLowerCase()
}

let filteredCategories = computed(() => {
  if (queryLower.value) {
    return categoryOptions.value.filter(item => {
      return item.name.toLowerCase().includes(queryLower.value)
    })
  }
  return categoryOptions.value
})

let serviceOptions = computed(() => {
  const services = store.getters['entities/filteredServices'](
    Object.assign(store.getters['booking/getSelection'], filterRules())
  )

  if (searchFilter.value) {
    return services.filter((service) => searchingStrings(service.name))
  }

  if (categoryFilter.value) {
    return services
  }

  // Sort services by position property in ascending order
  return services.sort((a, b) => a.position - b.position)
})

let selectedService = ref(null)

function selectService(service = null) {
  if (disabledService(service)) {
    return
  }

  let serviceData = {
    reference: 'service',
    // position will depends on fields order
    position: 0,
    value:
      service.id !== store.getters['booking/getServiceId'] ? service.name : '',
  }

  if (service.id !== store.getters['booking/getServiceId']) {
    store.commit('booking/setServiceId', service.id)
    let category = categoryOptions.value.find(
      (item) => item.id === service.categoryId
    )
    store.commit('booking/setCategoryId', category ? category.id : null)
  } else {
    store.commit('booking/setServiceId', null)
    store.commit('booking/setCategoryId', null)
  }

  changeInitStepDataService()

  useAction(store, {}, 'SelectService', 'appointment', null, null)

  sidebarDataCollector(serviceData)

  if (serviceData.value) {
    footerBtnDisabledUpdater(false)
  } else {
    footerBtnDisabledUpdater(true)
  }
}

function disabledService(service) {
  const employeeId = store.getters['booking/getEmployeeId']
  const locationId = store.getters['booking/getLocationId']
  if (employeeId !== null) {
    return !displayServiceEmployees(service.id).some(
      (emp) => emp.id === employeeId
    )
  }

  if (locationId !== null) {
    return !displayServiceLocation(service.id).some(
      (loc) => loc.id === locationId
    )
  }
  return false
}

function servicePackage(serviceId) {
  return store.getters['entities/filteredPackages'](
    Object.assign(store.getters['booking/getSelection'], {categoryId: null, serviceId: serviceId})
  ).filter(pack => pack.available && pack.status === 'visible')
}

function calculateServicePrice(id) {
  if (
    useServicePrice(amEntities.value, id).min ||
    useServicePrice(amEntities.value, id).max
  ) {
    return useServicePrice(amEntities.value, id).price
  }

  return amLabels.value.free
}

function serviceInfoArray(service) {
  let arr = []
  if (service.categoryId && customizeOptions.value.serviceCategory.visibility) {
    let category = categoryOptions.value.find(
      (c) => c.id === service.categoryId
    )
    if (category) {
      arr.push({
        icon: 'folder',
        name: category.name,
      })
    }
  }

  if (service.duration && customizeOptions.value.serviceDuration.visibility) {
    arr.push({
      icon: 'clock',
      name: useServiceDuration(service.duration),
    })
  }

  if (!licence.isLite && !licence.isStarter && customizeOptions.value.serviceCapacity.visibility) {
    arr.push({
      icon: 'user',
      name: useEmployeesServiceCapacity(amEntities.value, service.id),
    })
  }

  if (customizeOptions.value.serviceLocation.visibility && displayServiceLocationLabel(service.id).length > 0) {
    arr.push({
      icon: 'locations',
      name: displayServiceLocationLabel(service.id),
    })

  }
  return arr
}

// * EMPLOYEES
function displayServiceEmployees(serviceId) {
  let arr = []
  let employeesIds = Object.keys(amEntities.value.entitiesRelations)

  employeesIds.forEach((employeeId) => {
    if (
      serviceId in amEntities.value.entitiesRelations[employeeId] &&
      amEntities.value.employees.find((a) => a.id === parseInt(employeeId)) &&
      amEntities.value.employees.find((a) => a.id === parseInt(employeeId))
        .status === 'visible'
    ) {
      let employee = amEntities.value.employees.find((a) => a.id === parseInt(employeeId))
      let price = employee ? serviceEmployeePrice(employee) : ''
      arr.push({ ...employee, price })
    }
  })

  return arr
}

let infoEmployeeData = ref([])

function serviceEmployeePrice(employee) {
  if (selectedService.value) {
    let employeeService = employee.serviceList.find(
      (a) => a.id === selectedService.value.id
    )

    if (employeeService && (employeeService.price - selectedService.value.price !== 0))
      return `${
        employeeService.price - selectedService.value.price > 0
          ? selectedService.value.price > 0
            ? '+'
            : ''
          : '-'
      } ${useFormattedPrice(employeeService.price - selectedService.value.price)}`
  }
  return ''
}

// * LOCATION
function displayServiceLocationLabel(id) {
  let locations = useServiceLocation(amEntities.value, id)
  if (locations.length === 0) return ''

  if (
    locations.length === 1 ||
    (locations.length &&
      locations.every((location) => location.id === locations[0].id))
  ) {
    return locations[0].address ? locations[0].address : locations[0].name
  }
  return amLabels.value.multiple_locations
}

function displayServiceLocation(serviceId) {
  return useServiceLocation(amEntities.value, serviceId).filter(
    (loc, idx, arr) => arr.findIndex((l) => l.id === loc.id) === idx
  )
}

// * Service - More Info
let moreInfoVisibility = ref(false)

watchEffect(() => {
  if (!moreInfoVisibility.value) {
    infoEmployeeData.value = []
  }
})

// * Service Packages
function triggerPackagesPopup(service) {
  if (disabledService(service)) {
    return
  }

  if (service.id !== store.getters['booking/getServiceId']) {
    selectService(service)
  }
  nextTick(() => {
    stepCardLayoutReference.value.packagesPopupFooterVisibility = false
    stepCardLayoutReference.value.packagesVisibility = true
  })
}

// * Mounted hook
onMounted(() => {
  // Update footer button state
  footerBtnDisabledUpdater(store.getters['booking/getServiceId'] === null)
})

onUnmounted(() => {
  footerBtnDisabledUpdater(false)
})
</script>

<script>
export default {
  name: 'ServiceStep',
  key: 'serviceStep',
  sidebarData: {
    label: 'service_selection',
    icon: 'service',
    stepSelectedData: [],
    finished: false,
    selected: false,
  },
}
</script>
