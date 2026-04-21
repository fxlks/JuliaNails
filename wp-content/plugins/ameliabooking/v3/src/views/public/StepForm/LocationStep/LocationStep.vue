<template>
  <StepCardLayout
    ref="stepCardLayoutReference"
    :custom-class="[props.globalClass, 'am-fs-location-step']"
    :card-selected="
      store.getters['booking/getLocationId'] !== null || anyLocation
    "
    :allow-popup="
      serviceStepsIndex < 0 && stepIndex === 0 && store.getters['booking/getServiceId'] !== null
    "
  >
    <!-- Any Location -->
    <div
      v-if="customizeOptions.location.required === false"
      class="am-fs__init-item"
      :class="{
        'am--selected': anyLocation,
      }"
      tabindex="0"
      @click="chooseAnyLocation"
    >
      <div class="am-fs__init-item__img am-item-any">
        <span class="am-icon-locations" />
      </div>
      <div class="am-fs__init-item__content am-item-any">
        <div class="am-fs__init-item__heading" :class="responsiveClass">
          <div class="am-fs__init-item__name" :class="responsiveClass">
            {{ amLabels.any_location }}
          </div>
        </div>
      </div>
    </div>
    <!-- /Any Location -->

    <!-- Card -->
    <StepCard
      v-for="location in locationOptions"
      :key="location.id"
      :item="location"
      :item-name="location.name"
      :selected="store.getters['booking/getLocationId'] === location.id"
      :disabled="disabledLocation(location.id)"
      :is-person="false"
      :labels="amLabels"
      :info-items="
        location.address && customizeOptions.address.visibility
          ? [
              {
                icon: 'locations',
                name: location.address,
                isLink: true
              },
            ]
          : []
      "
      :parent-width="componentWidth"
      :info-btn-visibility="!!location.description && customizeOptions.moreBtn.visibility"
      @select-item="selectLocation(location)"
      @trigger-info-popup="
        () => {
          moreInfoVisibility = true
          selectedLocation = location
        }
      "
    />
    <!-- /Card -->
  </StepCardLayout>

  <!-- More Info -->
  <MoreInfoPopup
    v-if="moreInfoVisibility"
    v-model:visibility="moreInfoVisibility"
    :heading="amLabels.location_information"
    :labels="amLabels"
    :item="selectedLocation"
    :item-name="selectedLocation.name"
    :is-person="false"
    :plain-text="true"
  />
  <!-- /More Info -->
</template>

<script setup>
import StepCardLayout from '../common/StepCardLayout.vue'
import StepCard from '../common/StepCard.vue'
import MoreInfoPopup from '../common/MoreInfoPopup.vue'

// * Import from Vue
import { computed, inject, ref, reactive, onMounted, onUnmounted } from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Composables
import { useCartItem } from '../../../../assets/js/public/cart'
import useAction from '../../../../assets/js/public/actions'
import { useResponsiveClass } from '../../../../assets/js/common/responsive'
import { useElementSize } from '@vueuse/core'
import { defaultCustomizeSettings } from '../../../../assets/js/common/defaultCustomize'

// * Store
const store = useStore()

// * Props
const props = defineProps({
  globalClass: {
    type: String,
    default: '',
  },
  cardSelected: {
    type: Boolean,
    default: false,
  },
})

// * Amelia Settings
const amSettings = inject('settings')

// * Entities
let amEntities = computed(() => {
  return store.state.entities
})

// * Customize
const amCustomize = inject('amCustomize')

// * Component References
const stepCardLayoutReference = ref(null)
const stepCardLayoutDom = computed(
  () => stepCardLayoutReference.value?.stepCardLayoutRef || null
)

// * Component width
const { width: componentWidth } = useElementSize(stepCardLayoutDom)

// * Responsive Class
let responsiveClass = computed(() => {
  return useResponsiveClass(componentWidth.value)
})

// * Steps Array
const stepsArray = inject('stepsArray', ref([]))
const serviceStepsIndex = computed(() => {
  return stepsArray.value.findIndex(step => step.name === 'ServiceStep')
})

// * Step Index
const stepIndex = inject('stepIndex', 0)

// * Labels
let labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Labels
const amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  const customizedLabels = amCustomize.locationStep?.translations

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

const { footerBtnDisabledUpdater } = inject('changingStepsFunctions', {
  footerBtnDisabledUpdater: () => {},
})

let customizeOptions = computed(() => amCustomize.locationStep?.options || defaultCustomizeSettings.sbsNew.locationStep.options)

// * Any Employee
let anyLocation = computed(() => !store.getters['booking/getLocationId'])

let initStepOrder = [
  { id: 'ServiceStep'},
  { id: 'EmployeeStep' },
  { id: 'LocationStep' },
]
let stepOrder = computed(() => {
  return 'order' in amCustomize ? amCustomize.order : initStepOrder
})

const filterRules = computed(() => {
  const idx = stepOrder.value.findIndex(step => step.id === 'LocationStep')
  let arrRules = stepOrder.value.slice(0, idx)
  const obj = { serviceId: null, providerId: null, locationId: null }
  if (arrRules.some(step => step.id === 'ServiceStep')) delete obj.serviceId
  if (arrRules.some(step => step.id === 'EmployeeStep')) delete obj.providerId
  return obj
})

// * Set Location Options from entities
let locationOptions = computed(() => {
  const relations = amEntities.value.entitiesRelations

  // Collect connected location IDs
  const locationIdSet = new Set(
    Object.values(relations)
      .flatMap(emp => Object.values(emp).flat())
      .map(id => parseInt(id))
  )

  const locations = store.getters['entities/filteredLocations'](
    Object.assign(store.getters['booking/getSelection'], filterRules.value)
  )

  // Filter locations by connected IDs
  return locations.filter(loc => locationIdSet.has(parseInt(loc.id)))
})

function selectLocation(location) {
  if (disabledLocation(location.id)) return

  let locationData = {
    reference: 'location',
    // position will depends on fields order
    position: 0,
    value:
      location.id !== store.getters['booking/getLocationId']
        ? location.name
        : '',
  }

  if (location.id !== store.getters['booking/getLocationId']) {
    store.commit('booking/setLocationId', location.id)
  } else {
    store.commit('booking/setLocationId', null)
  }

  writeLocationData(locationData)
}

function disabledLocation(locationId) {
  const relations = amEntities.value.entitiesRelations
  const employeeId = store.getters['booking/getEmployeeId']
  const serviceId = store.getters['booking/getServiceId']

  if (employeeId) {
    if (serviceId) {
      return serviceId in relations[employeeId] ? !relations[employeeId][serviceId].some(locId => parseInt(locId) === locationId) : true
    }
    return Object.values(relations[employeeId]).every(
      sIds => !sIds.some(lId => parseInt(lId) === locationId)
    )
  }

  if (serviceId) {
    return Object.values(relations).every(
      emp => !(serviceId in emp) || !emp[serviceId].some(locId => parseInt(locId) === locationId)
    )
  }

  return false
}

function chooseAnyLocation() {
  let locationData = {
    reference: 'location',
    // position will depends on fields order
    position: 0,
    value: '',
  }

  store.commit('booking/setLocationId', null)

  writeLocationData(locationData)
}

function writeLocationData(locationData) {
  let cartItem = useCartItem(store)

  store.commit('booking/unsetMultipleAppointmentsData', cartItem.index)

  sidebarDataCollector(locationData)

  useAction(store, {}, 'SelectLocation', 'appointment', null, null)

  if (customizeOptions.value.location.required) {
    if (locationData.value) {
      footerBtnDisabledUpdater(false)
    } else {
      footerBtnDisabledUpdater(true)
    }
  } else {
    footerBtnDisabledUpdater(false)
  }
}

// * Location - More Info
let moreInfoVisibility = ref(false)
let selectedLocation = ref(null)

// * Mounted hook
onMounted(() => {
  if (!!customizeOptions.value.location.required && store.getters['booking/getLocationId'] === null) {
    footerBtnDisabledUpdater(true)
  }
})

onUnmounted(() => {
  footerBtnDisabledUpdater(false)
})
</script>

<script>
export default {
  name: 'LocationStep',
  key: 'locationStep',
  sidebarData: {
    label: 'location_selection',
    icon: 'locations',
    stepSelectedData: [],
    finished: false,
    selected: false,
  },
}
</script>

<style lang="scss">
.amelia-v2-booking #amelia-container {
  .am-fs-location-step {
    .am-fs__init {
      &-item {
        // Card content
        &__content {
          justify-content: space-between;
          &.am-item-any {
            justify-content: center;
          }
        }

        // Card heading
        &__heading {
          &.am-rw-370 {
            flex-wrap: nowrap;
            justify-content: unset;
            margin-bottom: 8px;
          }
        }

        // Card name
        &__name {
          &.am-rw-370 {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: auto;
            order: unset;
            justify-self: flex-start;
          }
        }
      }
    }
  }
}
</style>
