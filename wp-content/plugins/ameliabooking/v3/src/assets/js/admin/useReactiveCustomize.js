import { ref, computed } from 'vue'

// Global tracker for window.v3 changes - shared across all components
let globalWindowV3Tracker = null
let isGlobalTriggerSetup = false

/**
 * Composable for reactive amCustomize that works with both window.v3 and inject
 * This handles the reactivity bridge between host app (redesign) and V3 components
 *
 * This ensures that amCustomize will react to changes made by the host app (redesign)
 * when window.triggerV3Reactivity() is called.
 *
 * @returns {Object} - Object containing reactive amCustomize computed ref
 */
export function useReactiveCustomize() {
  // Initialize global tracker if not already done
  if (!globalWindowV3Tracker) {
    globalWindowV3Tracker = ref(0)
  }

  // Set up global trigger function once
  if (!isGlobalTriggerSetup && typeof window !== 'undefined') {
    window.triggerV3Reactivity = () => {
      if (globalWindowV3Tracker) {
        globalWindowV3Tracker.value++
      }
    }
    isGlobalTriggerSetup = true
  }

  // Create reactive amCustomize that responds to both Vue reactivity and manual triggers
  const amCustomize = computed(() => {
    // Access the global tracker to create dependency on window.v3 changes
    globalWindowV3Tracker.value

    return window.v3?.customize?.value
  })

  const stepIndex = computed(() => {
    globalWindowV3Tracker.value
    return window.v3?.stepIndex !== undefined ? window.v3.stepIndex.value : 0
  })

  const stepName = computed(() => {
    globalWindowV3Tracker.value
    return window.v3?.stepName !== undefined ? window.v3.stepName.value : ''
  })

  const amTranslations = computed(() => {
    globalWindowV3Tracker.value
    return window.v3?.translations?.value
  })

  const flowLayout = computed(() => {
    globalWindowV3Tracker.value
    return window.v3?.flowLayout?.value
  })

  const subStepName = computed(() => {
    globalWindowV3Tracker.value
    return window.v3?.subStepName !== undefined ? window.v3.subStepName.value : ''
  })

  const bookableType = computed(() => {
    globalWindowV3Tracker.value
    return window.v3?.bookableType !== undefined ? window.v3.bookableType.value : ''
  })

  const langKey = computed(() => {
    globalWindowV3Tracker.value
    return window.v3?.langKey !== undefined ? window.v3.langKey.value : ''
  })

  const pagesType = computed(() => {
    globalWindowV3Tracker.value
    return window.v3?.pagesType !== undefined ? window.v3.pagesType.value : ''
  })

  const features = computed(() => {
    globalWindowV3Tracker.value
    return window.v3?.features !== undefined ? window.v3.features.value : {}
  })

  return {
    amCustomize,
    stepIndex,
    stepName,
    amTranslations,
    flowLayout,
    subStepName,
    bookableType,
    langKey,
    pagesType,
    features,
  }
}
