import { createApp, defineAsyncComponent } from 'vue/dist/vue.esm-bundler'
import { provide, ref, reactive, readonly, nextTick } from 'vue'
import { useLicence } from '../common/licence'
import store from '../../../store'

window.stylesInjectedV3 = async function () {}

const CustomizeCatalogWrapper = defineAsyncComponent({
  loader: () =>
    import('../../../views/admin/customize/pages/CustomizeCatalog.vue'),
})

const CustomizeCustomerPanelWrapper = defineAsyncComponent({
  loader: () =>
    import('../../../views/admin/customize/pages/CustomizeCustomerPanel.vue'),
})

const CustomizeEmployeePanelWrapper = defineAsyncComponent({
  loader: () =>
    import('../../../views/admin/customize/pages/CustomizeEmployeePanel.vue'),
})

const CustomizeEventCalendarWrapper = defineAsyncComponent({
  loader: () =>
    import('../../../views/admin/customize/pages/CustomizeEventCalendar.vue'),
})

const CustomizeEventListWrapper = defineAsyncComponent({
  loader: () =>
    import('../../../views/admin/customize/pages/CustomizeEventList.vue'),
})

const CustomizeStepNewWrapper = defineAsyncComponent({
  loader: () =>
    import('../../../views/admin/customize/pages/CustomizeStepNew.vue'),
})

const dynamicCdn = window.wpAmeliaUrls.wpAmeliaPluginURL + 'v3/public/'

window.__dynamic_handler__ = function (importer) {
  return dynamicCdn + 'assets/' + importer
}
// @ts-ignore
window.__dynamic_preload__ = function (preloads) {
  return preloads.map((preload) => dynamicCdn + preload)
}

function getVueStyleElements() {
  let styleElements = []
  if (import.meta.env.DEV) {
    if (window.customizeComponentLoaded) {
      styleElements = document.querySelectorAll('#vite-dev-vue3')
    }
  } else {
    // Collect stylesheet link elements injected by Vue in production mode
    const linkElements = Array.from(document.getElementsByTagName('link')).filter(
      (el) =>
        el.getAttribute('rel') === 'stylesheet' &&
        el.getAttribute('href')?.includes('ameliabooking/v3/public/assets')
    )

    // Collect inline style elements that contain /* purgecss start ignore */
    // These are styles injected by maz-ui and other third-party components
    const inlineStyleElements = Array.from(document.getElementsByTagName('style')).filter(
      (el) => el.textContent?.includes('/* purgecss start ignore */')
    )

    styleElements = [...linkElements, ...inlineStyleElements]
  }
  return styleElements
}

async function injectVueStyles(shadowRoot) {
  if (!shadowRoot) return

  const waitForStyles = () =>
    new Promise((resolve) => {
      // Check immediately first
      const checkStyles = () => {
        const styles = getVueStyleElements()
        if (
          styles !== undefined &&
          styles.length > 0 &&
          window.customizeComponentLoaded
        ) {
          return styles
        }
        return null
      }

      const immediateResult = checkStyles()
      if (immediateResult) {
        resolve(immediateResult)
        return
      }

      // Use MutationObserver to watch for style changes in <head>
      const observer = new MutationObserver(() => {
        const styles = checkStyles()
        if (styles) {
          observer.disconnect()
          resolve(styles)
        }
      })

      observer.observe(document.head, {
        childList: true,
        subtree: true,
      })

      // Fallback for customizeComponentLoaded flag using requestAnimationFrame
      const fallbackCheck = () => {
        const styles = checkStyles()
        if (styles) {
          observer.disconnect()
          resolve(styles)
        } else if (!window.customizeComponentLoaded) {
          requestAnimationFrame(fallbackCheck)
        }
      }
      requestAnimationFrame(fallbackCheck)
    })

  const styleElements = await waitForStyles()
  const fragment = document.createDocumentFragment()

  styleElements.forEach((node, index) => {
    const clone = node.cloneNode(true)
    clone.id = `v3-style-${index}`

    // For link elements, ensure href is preserved correctly
    if (node.tagName === 'LINK' && node.href) {
      clone.href = node.href
    }

    fragment.appendChild(clone)
  })

  shadowRoot.appendChild(fragment)
  window.stylesInjectedV3()
}

// Function to wait for mount element and then mount the app
function waitForMountElement() {
  const host = document.getElementsByClassName('amd-cp__preview')[0]
  const shadowRoot = host?.shadowRoot
  const mountElement = shadowRoot?.getElementById('amelia-app-backend-new')

  if (!mountElement) {
    setTimeout(waitForMountElement, 100)
    return
  }

  const componentToMount =
    window.v3?.componentToMount !== undefined
      ? window.v3.componentToMount
      : 'Customize'

  let selectedComponent
  let componentName

  switch (componentToMount) {
    case 'CustomizeCatalog':
      selectedComponent = CustomizeCatalogWrapper
      componentName = 'CustomizeCatalog'
      break
    case 'CustomizeCustomerPanel':
      selectedComponent = CustomizeCustomerPanelWrapper
      componentName = 'CustomizeCustomerPanel'
      break
    case 'CustomizeEmployeePanel':
      selectedComponent = CustomizeEmployeePanelWrapper
      componentName = 'CustomizeEmployeePanel'
      break
    case 'CustomizeEventCalendar':
      selectedComponent = CustomizeEventCalendarWrapper
      componentName = 'CustomizeEventCalendar'
      break
    case 'CustomizeEventList':
      selectedComponent = CustomizeEventListWrapper
      componentName = 'CustomizeEventList'
      break
    case 'CustomizeStepNew':
      selectedComponent = CustomizeStepNewWrapper
      componentName = 'CustomizeStepNew'
      break
    default:
      selectedComponent = CustomizeStepNewWrapper
      componentName = 'CustomizeStepNew'
  }

  createApp({
    setup() {
      const baseURLs = ref(window.wpAmeliaUrls)
      const languages = reactive(window.wpAmeliaLanguages)
      const settings = reactive(window.wpAmeliaSettings)
      const timeZone = ref(
        'wpAmeliaTimeZone' in window ? window.wpAmeliaTimeZone[0] : ''
      )
      const localLanguage = ref(window.localeLanguage[0])
      const labels = reactive(window.wpAmeliaLabels)
      const licence = reactive(useLicence())

      provide('settings', readonly(settings))
      provide('baseUrls', readonly(baseURLs))
      provide('timeZone', readonly(timeZone))
      provide('localLanguage', readonly(localLanguage))
      provide('languages', readonly(languages))
      provide('labels', readonly(labels))
      provide('licence', licence)

      onMounted(async () => {
        await injectVueStyles(shadowRoot)
      })
    },
    template: `<${componentName} />`,
  })
    .component(componentName, selectedComponent)
    .use(store)
    .mount(mountElement)
}

// Add this before calling mountUnmountV3App
if (!window.onUnmountedV3Callback) {
  window.onUnmountedV3Callback = async () => {}
}

async function unmountV3App() {
  const host = document.getElementsByClassName('amd-cp__preview')[0]
  const shadowRoot = host?.shadowRoot
  const mountElement = shadowRoot?.getElementById('amelia-app-backend-new')
  const appInstance = mountElement?.__vue_app__

  // remove injected styles from shadow DOM
  const styleElements = shadowRoot?.querySelectorAll('[id^="v3-style-"]')
  styleElements?.forEach((styleElement) => {
    styleElement.remove()
  })

  if (appInstance) {
    appInstance.unmount()

    await nextTick()

    // Verify unmount by checking if the mount element is empty
    const isUnmounted =
      !mountElement?.innerHTML || mountElement.innerHTML.trim() === ''

    if (typeof window.onUnmountedV3Callback === 'function' && isUnmounted) {
      await window.onUnmountedV3Callback()
    }
  }
}

window.mountUnmountV3App = async () => {
  if (window.v3) {
    waitForMountElement()
  } else {
    await unmountV3App()
  }
}
