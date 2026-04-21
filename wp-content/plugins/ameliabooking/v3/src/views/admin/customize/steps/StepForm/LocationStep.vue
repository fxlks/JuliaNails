<template>
  <StepCardLayout
    ref="stepCardLayoutReference"
    :custom-class="[props.class, 'am-fs-location-step']"
    :card-selected="selectedLocation !== null || anyLocation"
  >
    <!-- Any Location -->
    <div
      v-if="!amCustomize[pageRenderKey].locationStep.options.location.required"
      class="am-fs__init-item"
      :class="{
        'am--selected': anyLocation,
      }"
      tabindex="0"
      @click="() => {
        selectedLocation = null
        anyLocation = true
      }"
    >
      <div class="am-fs__init-item__img am-item-any">
        <span class="am-icon-locations" />
      </div>
      <div class="am-fs__init-item__content am-item-any">
        <div class="am-fs__init-item__heading" :class="responsiveClass">
          <div class="am-fs__init-item__name" :class="responsiveClass">
            {{ labelsDisplay('any_location') }}
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
      :selected="selectedLocation?.id === location.id"
      :disabled="false"
      :is-person="false"
      :labels="amLabels"
      :info-items="
        location.address && amCustomize[pageRenderKey].locationStep.options.address.visibility
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
      :info-btn-visibility="!!location.description && amCustomize[pageRenderKey].locationStep.options.moreBtn.visibility"
      @select-item="() => {
        selectedLocation = location
        anyLocation = false
      }"
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
    :heading="labelsDisplay('location_information')"
    :item="selectedLocation"
    :item-name="selectedLocation.name"
    :is-person="false"
  />
  <!-- /More Info -->
</template>

<script setup>
import StepCardLayout from './common/StepCardLayout.vue'
import StepCard from './common/StepCard.vue'
import MoreInfoPopup from '../../../../public/StepForm/common/MoreInfoPopup.vue'

// * Import from Vue
import { computed, inject, ref } from 'vue'

// * Composables
import { useResponsiveClass } from '../../../../../assets/js/common/responsive'
import { useElementSize } from '@vueuse/core'

// * Props
const props = defineProps({
  class: {
    type: String,
    default: '',
  }
})

// * Lang key
let langKey = inject('langKey')

// * Labels
let amLabels = inject('labels')

let stepName = inject('stepName')
let pageRenderKey = inject('pageRenderKey')
let amCustomize = inject('customize')

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

function labelsDisplay(label) {
  return computed(() => {
    return (
      amCustomize.value[pageRenderKey.value]?.[stepName.value]?.translations?.[
        label
        ]?.[langKey.value] || amLabels[label]
    )
  }).value
}

// * Any Employee
let anyLocation = ref(true)

let selectedLocation = ref(null)

// * Set Location Options from entities
let locationOptions = ref([
  {
    id: 1,
    name: 'Nail Lounge',
    address: '123 Beauty Street, Downtown, NY 10001',
    description: 'Premium nail salon offering luxury manicures, pedicures, and nail art services in a relaxing spa-like environment.'
  },
  {
    id: 2,
    name: 'Medical Center',
    address: '456 Health Ave, Medical District, NY 10002',
    description: 'Full-service medical facility providing comprehensive healthcare services with state-of-the-art equipment and experienced medical professionals.'
  },
  {
    id: 3,
    name: 'Fitness Gym',
    address: '789 Workout Blvd, Sports Complex, NY 10003',
    description: 'Modern fitness center with cutting-edge equipment, personal training services, and group fitness classes for all fitness levels.'
  },
  {
    id: 4,
    name: 'Downtown Salon',
    address: '321 Style Street, Fashion District, NY 10004',
    description: 'Trendy hair salon specializing in cuts, coloring, styling, and treatments using premium products and latest techniques.'
  },
  {
    id: 5,
    name: 'Wellness Center',
    address: '654 Serenity Lane, Wellness Quarter, NY 10005',
    description: 'Holistic wellness facility offering massage therapy, meditation classes, yoga sessions, and various healing treatments.'
  },
  {
    id: 6,
    name: 'Beauty Spa',
    address: '987 Pamper Plaza, Luxury District, NY 10006',
    description: 'Luxurious spa retreat providing facial treatments, body therapies, and rejuvenating services in an elegant setting.'
  },
  {
    id: 7,
    name: 'Beauty Studio',
    address: '147 Glamour Street, Beauty Row, NY 10007',
    description: 'Contemporary beauty studio offering makeup services, eyebrow shaping, eyelash extensions, and skincare treatments.'
  },
  {
    id: 8,
    name: 'Color Studio',
    address: '258 Artistic Ave, Creative Quarter, NY 10008',
    description: 'Specialized color studio focusing on hair coloring, balayage, highlights, and creative color transformations.'
  }
])

// * Location - More Info
let moreInfoVisibility = ref(false)
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
#amelia-app-backend-new #amelia-container {
  .am-fs-location-step {
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
