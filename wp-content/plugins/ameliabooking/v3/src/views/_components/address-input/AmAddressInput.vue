<template>
  <!-- Address Field -->
  <div
    v-if="googleMapsLoaded()"
    ref="wrapperElement"
    class="am-input-wrapper"
    :style="cssVars"
  >
    <div class="el-input am-input am-input--default">
      <div
        v-click-outside="handleClickOutside"
        class="el-input__wrapper"
        :class="{'is-focus': isFocus}"
        @click="() => isFocus = true"
      >
        <input
          :id="`amelia-address-autocomplete-${props.id}`"
          ref="inputElement"
          v-model="inputValue"
          type="text"
          class="el-input__inner"
          :placeholder="props.placeholder"
          :aria-label="props.ariaLabel"
          @input="handleInputChange"
          @focus="handleFocus"
          @keydown="handleKeydown"
        />
      </div>
    </div>
  </div>
  <AmInput
    v-else
    v-model="model"
    :placeholder="props.placeholder"
  />
  <!-- /Address Field -->

  <!-- Autocomplete dropdown (Teleported to body) -->
  <Teleport to="body">
    <div
      v-if="showDropdown && predictions.length > 0"
      class="am-address-dropdown"
      :style="[cssVars, dropdownStyle]"
    >
      <div
        v-for="(prediction, index) in predictions"
        :key="prediction.place_id"
        class="am-address-dropdown-item"
        :class="{ 'is-active': index === selectedIndex }"
        @mousedown.prevent="selectPrediction(prediction)"
        @mouseenter="selectedIndex = index"
      >
        <div class="am-address-main">{{ prediction.main_text }}</div>
        <div v-if="prediction.secondary_text" class="am-address-secondary">
          {{ prediction.secondary_text }}
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
// * Vue imports
import {
  computed,
  ref,
  toRefs,
  onMounted,
  onUnmounted,
  inject,
  watch
} from "vue";

// * Element Plus
import { ClickOutside as vClickOutside} from "element-plus";

// * Components
import AmInput from "../input/AmInput.vue";

// * Composables
import { useColorTransparency} from "../../../assets/js/common/colorManipulation";

// * Import from Vuex
import { useStore } from "vuex";

// * Store
let store = useStore()

// * Component Props
const props = defineProps({
  modelValue: {
    type: [String, Array, Object, Number],
    default: '',
    required: true
  },
  id: {
    type: [String, Number],
    required: true
  },
  placeholder: {
    type: String,
    default: ''
  },
  ariaLabel: {
    type: String,
    default: 'address input'
  },
})

// * Define Emits
const emits = defineEmits(['update:modelValue', 'address-selected'])

// * Component Refs
let inputElement = ref(null)
let wrapperElement = ref(null)

// * Component model
let { modelValue } = toRefs(props)
let model = computed({
  get: () => modelValue.value,
  set: (val) => {
    emits('update:modelValue', val)
  }
})

// * Local state
let isFocus = ref(false)
let inputValue = ref(modelValue.value || '')
let showDropdown = ref(false)
let selectedIndex = ref(-1)
let dropdownStyle = ref({})

// * Google Places API state
let predictions = ref([])
let sessionToken = null
let debounceTimer = null
let hasPermanentError = ref(false)
let originalConsoleError = null
let errorSuppressionActive = false

const DEBOUNCE_MS = 300

/**
 * Suppress Google Maps API console errors globally
 */
function suppressGoogleMapsErrors() {
  if (errorSuppressionActive) return

  originalConsoleError = console.error
  errorSuppressionActive = true
}

function googleMapsLoaded () {
  return window.google && window.google.maps?.places && store.state.settings.general.gMapApiKey
}

/**
 * Initialize session token for new Places API
 */
function initializeSessionToken() {
  if (window.google?.maps?.places?.AutocompleteSessionToken) {
    const { AutocompleteSessionToken } = window.google.maps.places
    sessionToken = new AutocompleteSessionToken()
  }
}

/**
 * Fetch predictions using the new Place Autocomplete Data API only
 */
async function fetchPredictions(input) {
  if (!input.trim()) {
    predictions.value = []
    showDropdown.value = false
    return
  }

  // Don't make API calls if we already know there's an error
  if (hasPermanentError.value) {
    return
  }

  // Only use new API, no fallback
  if (!window.google?.maps?.places?.AutocompleteSuggestion) {
    hasPermanentError.value = true
    return
  }

  try {
    const { AutocompleteSuggestion } = window.google.maps.places

    const { suggestions } = await AutocompleteSuggestion.fetchAutocompleteSuggestions({
      input,
      sessionToken,
    })

    if (suggestions && suggestions.length > 0) {
      predictions.value = suggestions
        .filter(s => s.placePrediction)
        .map(s => {
          const p = s.placePrediction
          const fullText = p.text?.toString() || ''
          let mainText = fullText
          let secondaryText = ''

          if (p.structuredFormat) {
            mainText = p.structuredFormat.mainText?.toString() || mainText
            secondaryText = p.structuredFormat.secondaryText?.toString() || ''
          } else {
            const commaIndex = fullText.indexOf(', ')
            if (commaIndex > -1) {
              mainText = fullText.substring(0, commaIndex)
              secondaryText = fullText.substring(commaIndex + 2)
            }
          }

          return {
            place_id: p.placeId,
            main_text: mainText,
            secondary_text: secondaryText,
            _prediction: p,
          }
        })

      showDropdown.value = true
      selectedIndex.value = -1
      updateDropdownPosition()
    } else {
      predictions.value = []
      showDropdown.value = false
    }
  } catch (err) {
    const errorMessage = err?.message || err?.toString() || ''
    // Mark as permanent error for 403 or API errors and suppress future errors
    if (errorMessage.includes('AutocompleteSuggestion') ||
        errorMessage.includes('NOT_FOUND') ||
        errorMessage.includes('Places API') ||
        errorMessage.includes('Forbidden') ||
        errorMessage.includes('403') ||
        err?.code === 'NOT_FOUND' ||
        err?.status === 403) {
      hasPermanentError.value = true
      suppressGoogleMapsErrors()
    }
    predictions.value = []
    showDropdown.value = false
  }
}

/**
 * Handle input changes with debouncing
 */
function handleInputChange() {
  if (debounceTimer) {
    clearTimeout(debounceTimer)
  }

  debounceTimer = setTimeout(() => {
    fetchPredictions(inputValue.value)
  }, DEBOUNCE_MS)
}

/**
 * Get place details when a prediction is selected (new API only)
 */
async function selectPrediction(prediction) {
  try {
    if (!prediction._prediction) {
      return
    }

    const place = prediction._prediction.toPlace()

    // Fetch place fields (this concludes the session)
    await place.fetchFields({
      fields: ['formattedAddress', 'addressComponents']
    })

    if (place.formattedAddress) {
      inputValue.value = place.formattedAddress
      emits('update:modelValue', place.formattedAddress)
    }

    // Emit address components if available
    if (place.addressComponents) {
      const addressComponents = place.addressComponents.map(component => ({
        long_name: component.longText,
        short_name: component.shortText,
        types: component.types
      }))
      emits('address-selected', addressComponents)
    }

    // Create new session token for next autocomplete session
    initializeSessionToken()

    // Hide dropdown and reset
    showDropdown.value = false
    predictions.value = []
    selectedIndex.value = -1
  } catch (err) {
    console.error('Error fetching place details:', err)

    const fallbackAddress = prediction.secondary_text
      ? `${prediction.main_text}, ${prediction.secondary_text}`
      : prediction.main_text

    inputValue.value = fallbackAddress
    emits('update:modelValue', fallbackAddress)

    showDropdown.value = false
    predictions.value = []
    selectedIndex.value = -1
  }
}

/**
 * Handle keyboard navigation
 */
function handleKeydown(event) {
  if (!showDropdown.value || predictions.value.length === 0) return

  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault()
      selectedIndex.value = Math.min(selectedIndex.value + 1, predictions.value.length - 1)
      break
    case 'ArrowUp':
      event.preventDefault()
      selectedIndex.value = Math.max(selectedIndex.value - 1, -1)
      break
    case 'Enter':
      event.preventDefault()
      if (selectedIndex.value >= 0 && selectedIndex.value < predictions.value.length) {
        selectPrediction(predictions.value[selectedIndex.value])
      }
      break
    case 'Escape':
      event.preventDefault()
      showDropdown.value = false
      selectedIndex.value = -1
      break
  }
}

/**
 * Update dropdown position
 */
function updateDropdownPosition() {
  if (!wrapperElement.value) return

  const rect = wrapperElement.value.getBoundingClientRect()
  dropdownStyle.value = {
    position: 'fixed',
    top: `${rect.bottom + 4}px`,
    left: `${rect.left}px`,
    width: `${rect.width}px`,
  }
}

/**
 * Handle focus
 */
function handleFocus() {
  isFocus.value = true
  if (showDropdown.value && predictions.value.length > 0) {
    updateDropdownPosition()
  }
}

/**
 * Handle click outside
 */
function handleClickOutside() {
  isFocus.value = false
  showDropdown.value = false
  selectedIndex.value = -1
}

// Watch for external changes to modelValue
watch(modelValue, (newValue) => {
  if (newValue !== inputValue.value) {
    inputValue.value = newValue || ''
  }
})

onMounted(() => {
  // Listen for scroll/resize to update dropdown position
  window.addEventListener('scroll', updateDropdownPosition, true)
  window.addEventListener('resize', updateDropdownPosition)

  const initializePlaces = async () => {
    if (!window.google?.maps?.places || !store.state.settings.general.gMapApiKey) {
      hasPermanentError.value = true
      return
    }

    try {
      await window.google.maps.importLibrary("places")

      if (window.google?.maps?.places?.AutocompleteSuggestion) {
        initializeSessionToken()
      } else {
        hasPermanentError.value = true
        console.warn('New Places API not available. Autocomplete disabled.')
      }
    } catch (err) {
      console.error('Failed to initialize Google Places:', err)
      hasPermanentError.value = true
    }
  }

  initializePlaces()
})

onUnmounted(() => {
  if (debounceTimer) {
    clearTimeout(debounceTimer)
  }
  window.removeEventListener('scroll', updateDropdownPosition, true)
  window.removeEventListener('resize', updateDropdownPosition)

  if (errorSuppressionActive && originalConsoleError) {
    console.error = originalConsoleError
  }
})

// * Color Vars
let amColors = inject(
  'amColors',
  ref({
    colorPrimary: '#1246D6',
    colorSuccess: '#019719',
    colorError: '#B4190F',
    colorWarning: '#CCA20C',
    colorMainBgr: '#FFFFFF',
    colorMainHeadingText: '#33434C',
    colorMainText: '#1A2C37',
    colorSbBgr: '#17295A',
    colorSbText: '#FFFFFF',
    colorInpBgr: '#FFFFFF',
    colorInpBorder: '#D1D5D7',
    colorInpText: '#1A2C37',
    colorInpPlaceHolder: '#808A90',
    colorDropBgr: '#FFFFFF',
    colorDropBorder: '#D1D5D7',
    colorDropText: '#0E1920',
    colorBtnPrim: '#265CF2',
    colorBtnPrimText: '#FFFFFF',
    colorBtnSec: '#1A2C37',
    colorBtnSecText: '#FFFFFF',
  })
)

// * Css Variables
let cssVars = computed(() => {
  return {
    '--am-c-inp-bgr': amColors.value.colorInpBgr,
    '--am-c-inp-border': amColors.value.colorInpBorder,
    '--am-c-inp-text': amColors.value.colorInpText,
    '--am-c-inp-text-op03': useColorTransparency(
      amColors.value.colorInpText,
      0.03
    ),
    '--am-c-inp-text-op05': useColorTransparency(
      amColors.value.colorInpText,
      0.05
    ),
    '--am-c-inp-text-op40': useColorTransparency(
      amColors.value.colorInpText,
      0.4
    ),
    '--am-c-inp-text-op60': useColorTransparency(
      amColors.value.colorInpText,
      0.6
    ),
    '--am-c-inp-placeholder': amColors.value.colorInpPlaceHolder,
    '--am-c-drop-bgr': amColors.value.colorDropBgr,
    '--am-c-drop-border': amColors.value.colorDropBorder,
    '--am-c-drop-text': amColors.value.colorDropText,
  }
})
</script>

<style scoped>
.am-address-dropdown {
  background: var(--am-c-drop-bgr);
  border: 1px solid var(--am-c-drop-border);
  border-radius: 4px;
  max-height: 300px;
  overflow-y: auto;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  z-index: 9999999999 !important;
  font-family: 'Amelia Roboto', sans-serif;
}

.am-address-dropdown-item {
  padding: 0 12px;
  min-height: 40px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  cursor: pointer;
  border-bottom: 1px solid var(--am-c-inp-text-op05);
  transition: background-color 0.2s;
}

.am-address-dropdown-item:last-child {
  border-bottom: none;
}

.am-address-dropdown-item:hover,
.am-address-dropdown-item.is-active {
  background: var(--am-c-inp-text-op05);
}

.am-address-main {
  color: var(--am-c-drop-text);
  font-weight: 500;
  font-size: 15px;
  line-height: 1.4;
  margin-bottom: 2px;
}

.am-address-secondary {
  color: var(--am-c-inp-text-op60);
  font-size: 13px;
  line-height: 1.3;
}

.am-input-wrapper {
  position: relative;
}
</style>
