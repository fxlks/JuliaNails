<template>
  <StepCardLayout
    ref="stepCardLayoutReference"
    :custom-class="[props.class, 'am-fs-service-step']"
    :allow-popup="true"
    :card-selected="selectedService !== null"
  >
    <template #filters>
      <AmInput
        v-if="serviceCustomizeOptions.search.visibility"
        v-model="searchFilter"
        :placeholder="labelsDisplay('filter_input')"
        :prefix-icon="iconSearch"
      />
      <AmSelect
        v-if="serviceCustomizeOptions.category.visibility"
        v-model="categoryFilter"
        :placeholder="labelsDisplay('select_service_category')"
        class="am-fs__init-category-select"
        :filterable="serviceCustomizeOptions.category.filterable"
        clearable
        :fit-input-width="true"
        :no-match-text="labelsDisplay('dropdown_empty')"
      >
        <AmOption
          v-for="category in categoryOptions"
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
      :selected="selectedService === service.id"
      :disabled="false"
      :is-person="false"
      :item-name="service.name"
      :price="service.price"
      :price-visibility="amCustomize[pageRenderKey].serviceStep.options.price.visibility"
      :tax-visibility="(licence.isPro || licence.isDeveloper) && amCustomize[pageRenderKey].serviceStep.options.tax.visibility "
      :tax-excluded="false"
      :info-items="service.infoItems"
      :info-btn-visibility="amCustomize[pageRenderKey].serviceStep.options.moreBtn.visibility"
      :packages-btn-visibility="features.packages && amCustomize[pageRenderKey].serviceStep.options.packagesBtn.visibility"
      :parent-width="componentWidth"
      @select-item="selectService(service)"
      @trigger-info-popup="
        () => {
          moreInfoVisibility = true
          selectedService = service
        }
      "
    />
    <!-- /Card -->
  </StepCardLayout>

  <!-- More Info -->
  <MoreInfoPopup
    v-if="moreInfoVisibility"
    v-model:visibility="moreInfoVisibility"
    :heading="labelsDisplay('service_information')"
    :item-name="selectedService.name"
    :item="selectedService"
    :is-person="false"
    :employees-heading="!licence.isLite ? labelsDisplay('employees') : ''"
    :employees-data="!licence.isLite ? employeesData : []"
    :locations-heading="licence.isBasic || licence.isPro || licence.isDeveloper ? labelsDisplay('locations') : ''"
    :locations-data="licence.isBasic || licence.isPro || licence.isDeveloper ? locationsData : []"
  />
  <!-- /More Info -->
</template>

<script setup>
// * Import for Vue
import { ref, inject, computed, watchEffect, provide } from 'vue'

// * Components
import AmSelect from '../../../../_components/select/AmSelect.vue'
import AmOption from '../../../../_components/select/AmOption.vue'
import AmInput from '../../../../_components/input/AmInput.vue'
import IconComponent from '../../../../_components/icons/IconComponent.vue'

// * Dedicated components
import StepCardLayout from './common/StepCardLayout.vue'
import StepCard from './common/StepCard.vue'
import MoreInfoPopup from '../../../../public/StepForm/common/MoreInfoPopup.vue'

// * Composables
import { useElementSize } from '@vueuse/core'

// * Props
const props = defineProps({
  class: {
    type: [String, Array],
    default: '',
  },
})

// * Step contains slide popups
let inPopup = ref(true)
provide('inPopup', {inPopup})

// * Features integrations
let features = inject('features')

// * Lang key
let langKey = inject('langKey')

// * Labels
let amLabels = inject('labels')

let stepName = inject('stepName')
let pageRenderKey = inject('pageRenderKey')
let amCustomize = inject('customize')

// * Plugin Licence
let licence = inject('licence')

// * Component reference
const stepCardLayoutReference = ref(null)
const stepCardLayoutDom = computed(
  () => stepCardLayoutReference.value?.stepCardLayoutRef || null
)

// * Component width
const { width: componentWidth } = useElementSize(stepCardLayoutDom)

function labelsDisplay(label) {
  return computed(() => {
    return (
      amCustomize.value[pageRenderKey.value]?.[stepName.value]?.translations?.[
        label
      ]?.[langKey.value] || amLabels[label]
    )
  }).value
}

// * FILTER
let searchFilter = ref('')
let categoryFilter = ref('')

// * Category options
let categoryOptions = ref([
  { id: 1, name: 'Hair Services' },
  { id: 2, name: 'Spa & Wellness' },
  { id: 3, name: 'Beauty & Makeup' },
  { id: 4, name: 'Medical Services' },
  { id: 5, name: 'Fitness & Training' },
])

let iconSearch = {
  components: { IconComponent },
  template: `<IconComponent icon="search"/>`,
}

let selectedService = ref(null)

let serviceOptions = ref([
  {
    id: 1,
    name: 'Haircut & Styling',
    price: '$45',
    categoryId: 1,
    infoItems: [
      { icon: 'folder', name: 'Hair Services' },
      { icon: 'clock', name: '60min' },
      { icon: 'user', name: '1/15' },
      { icon: 'locations', name: 'Nail Lounge' },
    ],
    pictureThumbPath: '',
  },
  {
    id: 2,
    name: 'Deep Tissue Massage',
    price: '',
    categoryId: 2,
    infoItems: [
      { icon: 'folder', name: 'Spa & Wellness' },
      { icon: 'clock', name: '90min' },
      { icon: 'user', name: '1/15 James Miller' },
      { icon: 'locations', name: 'Medical Center' },
    ],
  },
  {
    id: 3,
    name: 'Facial Treatment',
    price: '$65',
    categoryId: 2,
    infoItems: [
      { icon: 'folder', name: 'Spa & Wellness' },
      { icon: 'clock', name: '75min' },
      { icon: 'user', name: '1/15' },
      { icon: 'locations', name: 'Fitness Gym' },
    ],
  },
  {
    id: 4,
    name: 'Makeup Application',
    price: '$55',
    categoryId: 3,
    infoItems: [
      { icon: 'folder', name: 'Beauty & Makeup' },
      { icon: 'clock', name: '45min' },
      { icon: 'user', name: '1/15' },
      { icon: 'locations', name: 'Downtown Salon' },
    ],
  },
  {
    id: 5,
    name: 'Manicure & Pedicure',
    price: '$35',
    categoryId: 3,
    infoItems: [
      { icon: 'folder', name: 'Beauty & Makeup' },
      { icon: 'clock', name: '60min' },
      { icon: 'user', name: '1/15' },
      { icon: 'locations', name: 'Wellness Center' },
    ],
  },
  {
    id: 6,
    name: 'General Consultation',
    price: '$120',
    categoryId: 4,
    infoItems: [
      { icon: 'folder', name: 'Medical Services' },
      { icon: 'clock', name: '30min' },
      { icon: 'user', name: '1/15' },
      { icon: 'locations', name: 'Beauty Spa' },
    ],
  },
  {
    id: 7,
    name: 'Personal Training Session',
    price: '$75',
    categoryId: 5,
    infoItems: [
      { icon: 'folder', name: 'Fitness & Training' },
      { icon: 'clock', name: '60min' },
      { icon: 'user', name: '1/15' },
      { icon: 'locations', name: 'Beauty Studio' },
    ],
  },
  {
    id: 8,
    name: 'Hair Coloring',
    price: '$95',
    categoryId: 1,
    infoItems: [
      { icon: 'folder', name: 'Hair Services' },
      { icon: 'clock', name: '120min' },
      { icon: 'user', name: '1/15' },
      { icon: 'locations', name: 'Color Studio' },
    ],
  },
])

let serviceCustomizeOptions = computed(() => {
  return amCustomize.value[pageRenderKey.value].serviceStep.options
})

const durationMap = {
  1: '60min',
  2: '90min',
  3: '75min',
  4: '45min',
  5: '60min',
  6: '30min',
  7: '60min',
  8: '120min',
}

const locationMap = {
  1: 'Nail Lounge',
  2: 'Medical Center',
  3: 'Fitness Gym',
  4: 'Downtown Salon',
  5: 'Wellness Center',
  6: 'Beauty Spa',
  7: 'Beauty Studio',
  8: 'Color Studio',
}
watchEffect(() => {

  if (!serviceCustomizeOptions.value.serviceCategory.visibility) {
    serviceOptions.value.forEach((service) => {
      if (service.infoItems.some((item) => item.icon === 'folder')) {
        service.infoItems = service.infoItems.filter(
          (item) => item.icon !== 'folder'
        )
      }
    })
  } else {
    serviceOptions.value.forEach((service) => {
      if (!service.infoItems.some((item) => item.icon === 'folder')) {
        service.infoItems.splice(0, 0, {
          icon: 'folder',
          name: categoryOptions.value.find(
            (cat) => cat.id === service.categoryId
          ).name,
        })
      }
    })
  }

  if (!serviceCustomizeOptions.value.serviceDuration.visibility) {
    serviceOptions.value.forEach((service) => {
      if (service.infoItems.some((item) => item.icon === 'clock')) {
        service.infoItems = service.infoItems.filter(
          (item) => item.icon !== 'clock'
        )
      }
    })
  } else {
    serviceOptions.value.forEach((service) => {
      if (!service.infoItems.some((item) => item.icon === 'clock')) {
        let position =
          service.infoItems.findIndex((item) => item.icon === 'folder') !== -1
            ? 1
            : 0
        service.infoItems.splice(position, 0, {
          icon: 'clock',
          name: durationMap[service.id],
        })
      }
    })
  }

  if (!serviceCustomizeOptions.value.serviceCapacity.visibility || (licence.isStarter || licence.isLite)) {
    serviceOptions.value.forEach((service) => {
      if (service.infoItems.some((item) => item.icon === 'user')) {
        service.infoItems = service.infoItems.filter(
          (item) => item.icon !== 'user'
        )
      }
    })
  } else {
    serviceOptions.value.forEach((service) => {
      if (!service.infoItems.some((item) => item.icon === 'user')) {
        let indexC = service.infoItems.findIndex((item) => item.icon === 'folder')
        let indexD = service.infoItems.findIndex((item) => item.icon === 'clock')
        let position = indexD < 0 ? indexC < 0 ? 0 : indexC + 1 : indexD + 1
        service.infoItems.splice(position, 0, {
          icon: 'user',
          name: '1/15',
        })
      }
    })
  }

  if (!serviceCustomizeOptions.value.serviceLocation.visibility || (licence.isStarter || licence.isLite)) {
    serviceOptions.value.forEach((service) => {
      if (service.infoItems.some((item) => item.icon === 'locations')) {
        service.infoItems = service.infoItems.filter(
          (item) => item.icon !== 'locations'
        )
      }
    })
  } else {
    serviceOptions.value.forEach((service) => {
      if (!service.infoItems.some((item) => item.icon === 'locations')) {
        let position = service.infoItems.length > 0 ? service.infoItems.length : 0
        service.infoItems.splice(position, 0, {
          icon: 'locations',
          name: locationMap[service.id],
        })
      }
    })
  }
})

// Methods
const selectService = (service) => {
  selectedService.value = service.id
}

// * Service - More Info
let moreInfoVisibility = ref(false)

let employeesData = ref([
  {
    id: 1,
    firstName: 'James',
    lastName: 'Miller',
    pictureThumbPath: '',
  },
  {
    id: 2,
    firstName: 'Sarah',
    lastName: 'Johnson',
    pictureThumbPath: '',
  },
  {
    id: 3,
    firstName: 'Emily',
    lastName: 'Davis',
    pictureThumbPath: '',
  },
  {
    id: 4,
    firstName: 'Michael',
    lastName: 'Smith',
    pictureThumbPath: '',
  },
  {
    id: 5,
    firstName: 'David',
    lastName: 'Brown',
    pictureThumbPath: '',
  },
])

let locationsData = ref([
  {
    id: 1,
    name: 'Nail Lounge',
    address: '123 Nail St, City, Country',
    pictureThumbPath: '',
  },
  {
    id: 2,
    name: 'Medical Center',
    address: '456 Medical Ave, City, Country',
    pictureThumbPath: '',
  },
  {
    id: 3,
    name: 'Fitness Gym',
    address: '789 Fitness Rd, City, Country',
    pictureThumbPath: '',
  },
])
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
