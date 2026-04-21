<template>
  <StepCardLayout
    ref="stepCardLayoutReference"
    :custom-class="[props.class, 'am-fs-employee-step']"
    :card-selected="selectedEmployee !== null || anyEmployee"
  >
    <!-- Any Employee -->
    <div
      v-if="!amCustomize[pageRenderKey].employeeStep.options.employee.required"
      class="am-fs__init-item"
      :class="{
        'am--selected': anyEmployee,
      }"
      tabindex="0"
      @click="() => {
        selectedEmployee = null
        anyEmployee = true
      }"
    >
      <div class="am-fs__init-item__img am-item-any">
        <span class="am-icon-user" />
      </div>
      <div class="am-fs__init-item__content am-item-any">
        <div class="am-fs__init-item__heading" :class="responsiveClass">
          <div class="am-fs__init-item__name" :class="responsiveClass">
            {{ labelsDisplay('any_employee') }}
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
      :selected="selectedEmployee?.id === employee.id"
      :disabled="false"
      :is-person="true"
      :price="employee.price"
      :price-visibility="amCustomize[pageRenderKey].employeeStep.options.price.visibility"
      :labels="amLabels"
      :parent-width="componentWidth"
      :info-btn-visibility="!!employee.description && amCustomize[pageRenderKey].employeeStep.options.moreBtn.visibility"
      @select-item="() => {
        selectedEmployee = employee
        anyEmployee = false
      }"
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
    :heading="labelsDisplay('employee_information')"
    :item="selectedEmployee"
    :item-name="`${selectedEmployee.firstName} ${selectedEmployee.lastName}`"
    :is-person="true"
  />
  <!-- /More Info -->
</template>

<script setup>
// * Import from Vue
import { ref, computed, inject } from 'vue'

// * Dedicated components
import StepCardLayout from './common/StepCardLayout.vue'
import StepCard from './common/StepCard.vue'
import MoreInfoPopup from '../../../../public/StepForm/common/MoreInfoPopup.vue'

// * Composables
import { useElementSize } from '@vueuse/core'
import { useResponsiveClass } from '../../../../../assets/js/common/responsive'

// * Props
const props = defineProps({
  class: {
    type: [String, Array],
    default: '',
  },
})

// * Lang key
let langKey = inject('langKey')

// * Labels
let amLabels = inject('labels')

let stepName = inject('stepName')
let pageRenderKey = inject('pageRenderKey')
let amCustomize = inject('customize')

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

function labelsDisplay(label) {
  return computed(() => {
    return (
      amCustomize.value[pageRenderKey.value]?.[stepName.value]?.translations?.[
        label
        ]?.[langKey.value] || amLabels[label]
    )
  }).value
}

let anyEmployee = ref(true)

let selectedEmployee = ref(null)

let employeeOptions = ref([
  {
    id: 1,
    firstName: 'Sarah',
    lastName: 'Johnson',
    description: 'Senior nail technician with 8 years of experience specializing in gel manicures and nail art. Sarah is known for her attention to detail and creative designs.',
    price: '$45',
  },
  {
    id: 2,
    firstName: 'Michael',
    lastName: 'Davis',
    description: 'Licensed massage therapist specializing in deep tissue and Swedish massage. Michael has extensive training in sports massage and injury recovery.',
    price: '',
  },
  {
    id: 3,
    firstName: 'Emma',
    lastName: 'Wilson',
    description: 'Professional hairstylist and colorist with expertise in balayage, highlights, and modern cuts. Emma stays current with the latest trends and techniques.',
    price: '',
  },
  {
    id: 4,
    firstName: 'James',
    lastName: 'Rodriguez',
    description: 'Certified personal trainer with specialization in strength training and weight loss. James creates personalized workout plans for all fitness levels.',
    price: '$55',
  },
  {
    id: 5,
    firstName: 'Lisa',
    lastName: 'Chen',
    description: 'Licensed esthetician specializing in facials, chemical peels, and anti-aging treatments. Lisa provides personalized skincare consultations.',
    price: '$75',
  },
  {
    id: 6,
    firstName: 'David',
    lastName: 'Thompson',
    description: 'Master barber with traditional and modern cutting techniques. David offers classic shaves, beard trimming, and contemporary men\'s styling.',
    price: '$40',
  },
  {
    id: 7,
    firstName: 'Amanda',
    lastName: 'Brown',
    description: 'Yoga instructor certified in Hatha, Vinyasa, and Restorative yoga. Amanda focuses on mindfulness and proper alignment for all skill levels.',
    price: '',
  },
  {
    id: 8,
    firstName: 'Robert',
    lastName: 'Martinez',
    description: 'Licensed physiotherapist specializing in injury rehabilitation and pain management. Robert uses evidence-based treatments for optimal recovery.',
    price: '',
  }
])

// * Service - More Info
let moreInfoVisibility = ref(false)
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
#amelia-app-backend-new #amelia-container {
  .am-fs-employee-step {
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
          }
        }
      }
    }
  }
}
</style>
