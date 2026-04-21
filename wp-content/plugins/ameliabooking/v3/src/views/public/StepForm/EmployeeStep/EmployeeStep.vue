<template>
  <StepCardLayout
    ref="stepCardLayoutReference"
    :custom-class="[props.globalClass, 'am-fs-employee-step']"
    :card-selected="
      store.getters['booking/getEmployeeId'] !== null || anyEmployee
    "
    :allow-popup="
     serviceStepsIndex < 0 && stepIndex === 0 && store.getters['booking/getServiceId'] !== null
    "
  >
    <!-- Any Employee -->
    <div
      v-if="customizeOptions.employee.required === false"
      class="am-fs__init-item"
      :class="{
        'am--selected': anyEmployee,
      }"
      tabindex="0"
      @click="chooseAnyEmployee"
    >
      <div class="am-fs__init-item__img am-item-any">
        <span class="am-icon-user" />
      </div>
      <div class="am-fs__init-item__content am-item-any">
        <div class="am-fs__init-item__heading" :class="responsiveClass">
          <div class="am-fs__init-item__name" :class="responsiveClass">
            {{ amLabels.any_employee }}
          </div>
        </div>
      </div>
    </div>
    <!-- /Any Employee -->

    <StepCard
      v-for="employee in employeeOptions"
      :key="employee.id"
      :item="employee"
      :item-name="`${employee.firstName} ${employee.lastName}`"
      :selected="store.getters['booking/getEmployeeId'] === employee.id"
      :disabled="disabledEmployee(employee)"
      :is-person="true"
      :price="getEmployeePrice(employee)"
      :price-visibility="customizeOptions.price.visibility && getEmployeePrice(employee) !== ''"
      :labels="amLabels"
      :parent-width="componentWidth"
      :info-btn-visibility="!!employee.description && customizeOptions.moreBtn.visibility"
      @select-item="selectEmployee(employee)"
      @trigger-info-popup="() => {
        moreInfoVisibility = true
        selectedEmployee = employee
      }"
    />
  </StepCardLayout>

  <!-- More Info -->
  <MoreInfoPopup
    v-if="moreInfoVisibility"
    v-model:visibility="moreInfoVisibility"
    :heading="amLabels.employee_information"
    :labels="amLabels"
    :item="selectedEmployee"
    :item-name="`${selectedEmployee.firstName} ${selectedEmployee.lastName}`"
    :is-person="true"
  />
  <!-- /More Info -->
</template>

<script setup>
// * Import from Vue
import { computed, inject, ref, onMounted, reactive, onUnmounted } from 'vue'

// * Import components
import StepCardLayout from '../common/StepCardLayout.vue'
import StepCard from '../common/StepCard.vue'
import MoreInfoPopup from '../common/MoreInfoPopup.vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Composables
import { useFormattedPrice } from '../../../../assets/js/common/formatting'
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
})

// * Amelia Settings
const amSettings = inject('settings')

// * Entities
let amEntities = computed(() => {
  return store.state.entities
})

let locationsRules = computed(() => store.getters['entities/getLocations'].length <= 1)

// * Customize
const amCustomize = inject('amCustomize')

// * Component reference
const stepCardLayoutReference = ref(null)
const stepCardLayoutDom = computed(
  () => stepCardLayoutReference.value?.stepCardLayoutRef || null
)

// * Component width
const { width: componentWidth } = useElementSize(stepCardLayoutDom)

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

  const customizedLabels = amCustomize.employeeStep?.translations

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

let customizeOptions =  computed(() => amCustomize.employeeStep?.options || defaultCustomizeSettings.sbsNew.employeeStep.options)

// * Any Employee
let anyEmployee = computed(() => !store.getters['booking/getEmployeeId'])

// * Set Employee Options from entities
let initStepOrder = [
  { id: 'ServiceStep'},
  { id: 'EmployeeStep' },
  { id: 'LocationStep' },
]

let stepOrder = computed(() => {
  return 'order' in amCustomize ? amCustomize.order : initStepOrder
})

const filterRules = computed(() => {
  const idx = stepOrder.value.findIndex(step => step.id === 'EmployeeStep')
  let arrRules = stepOrder.value.slice(0, idx)
  const obj = { serviceId: null, providerId: null, locationId: null }
  if (arrRules.some(step => step.id === 'ServiceStep')) delete obj.serviceId
  if (arrRules.some(step => step.id === 'LocationStep') || locationsRules.value) delete obj.locationId
  return obj
})

let employeeOptions = computed(() =>
  store.getters['entities/filteredEmployees'](
    Object.assign(store.getters['booking/getSelection'], filterRules.value)
  )
)

function getEmployeePrice(employee) {
  if (store.getters['booking/getSelection'].serviceId) {
    let employeeServices = store.getters['entities/getEmployeeServices']({
      serviceId: store.getters['booking/getSelection'].serviceId,
      providerId: employee.id,
    })
    let service = store.getters['entities/getBookableFromBookableEntities'](
      store.getters['booking/getSelection']
    )

    if (employeeServices.length === 0 || !service) {
      return ''
    }
    let priceDiff = employeeServices[0].price - service.price
    let sign = priceDiff > 0 ? (service.price > 0 ? '+' : '') : '-'
    return priceDiff !== 0 ? sign + useFormattedPrice(priceDiff) : ''
  }

  return ''
}

function selectEmployee(employee) {
  if (disabledEmployee(employee)) {
    return
  }

  // Sidebar employee data
  let employeeData = {
    reference: 'employee',
    // position will depends on fields order
    position: 0,
    value:
      employee.id !== store.getters['booking/getEmployeeId']
        ? `${employee.firstName} ${employee.lastName}`
        : '',
  }

  if (employee.id !== store.getters['booking/getEmployeeId']) {
    store.commit('booking/setEmployeeId', employee.id)
  } else {
    store.commit('booking/setEmployeeId', null)
  }

  writeEmployeeData(employeeData)
}
function disabledEmployee(employee) {
  const relations = amEntities.value.entitiesRelations
  const serviceId = store.getters['booking/getServiceId']
  const locationId = store.getters['booking/getLocationId']

  if (locationId) {
    if (serviceId) {
      return !(
        relations[employee.id] &&
        serviceId in relations[employee.id] &&
        relations[employee.id][serviceId].includes(locationId)
      )
    }

    return Object.values(relations[employee.id]).every(
      sIds => !sIds.includes(locationId)
    )
  }

  return false
}

function chooseAnyEmployee() {
  // Sidebar employee data
  let employeeData = {
    reference: 'employee',
    // position will depends on fields order
    position: 0,
    value: '',
  }

  store.commit('booking/setEmployeeId', null)

  writeEmployeeData(employeeData)
}

function writeEmployeeData(employeeData) {
  let cartItem = useCartItem(store)

  store.commit('booking/unsetMultipleAppointmentsData', cartItem.index)

  sidebarDataCollector(employeeData)

  useAction(store, {}, 'SelectEmployee', 'appointment', null, null)

  if (customizeOptions.value.employee.required) {
    if (employeeData.value) {
      footerBtnDisabledUpdater(false)
    } else {
      footerBtnDisabledUpdater(true)
    }
  } else {
    footerBtnDisabledUpdater(false)
  }
}

// * Employee - More Info
let moreInfoVisibility = ref(false)
let selectedEmployee = ref(null)

// * Mounted hook
onMounted(() => {
  if (!!customizeOptions.value.employee.required && store.getters['booking/getEmployeeId'] === null) {
    footerBtnDisabledUpdater(true)
  }
})

onUnmounted(() => {
  footerBtnDisabledUpdater(false)
})
</script>

<script>
export default {
  name: 'EmployeeStep',
  key: 'employeeStep',
  sidebarData: {
    label: 'employee_selection',
    icon: 'employee',
    stepSelectedData: [],
    finished: false,
    selected: false,
  },
}
</script>

<style lang="scss">
.amelia-v2-booking #amelia-container {
  .am-fs-employee-step {
    .am-fs__init {
      &-item {
        // Card content
        &__content {
          &.am-item-any {
            justify-content: center;
          }
        }

        // Card heading
        &__heading {
          &.am-rw-370 {
            flex-wrap: nowrap;
            justify-content: unset;
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

        // Card cost
        &__cost {
          &.am-rw-370 {
            flex: 0 0 auto;
            margin-left: auto;
          }
        }
      }
    }
  }
}
</style>
