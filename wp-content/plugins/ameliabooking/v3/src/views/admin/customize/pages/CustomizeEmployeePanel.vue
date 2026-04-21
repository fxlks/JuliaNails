<template>
  <div
    v-if="isLoaded"
    id="amelia-container"
    ref="ameliaContainer"
    class="am-cap__wrapper"
    :class="[
      { 'am-collapsed': sidebarCollapsed },
      { 'am-auth': pagesType === 'auth' },
    ]"
    :style="cssVars"
  >
    <template v-if="pagesType === 'auth'">
      <component :is="stepsArray[stepIndex]"></component>
    </template>
    <template v-if="pagesType === 'panel'">
      <SideBar
        v-if="sidebarVisibility"
        class="am-fs-sb"
        :class="{ 'am-collapsed': sidebarCollapsed }"
        :style="{
          width: !sidebarCollapsed ? '240px' : '72px',
          paddingBottom: `${sidebarFooterHeight + 16}px`,
        }"
      >
        <template #step-list>
          <div class="am-fs-sb__page-wrapper">
            <template v-for="(step, index) in sidebarSteps" :key="step.key">
              <div
                class="am-fs-sb__page"
                :class="[
                  { selected: stepName === step.key },
                  {
                    selected:
                      (stepName === 'packagesList' ||
                        stepName === 'packageAppointmentsList') &&
                      step.key === 'packages',
                  },
                ]"
              >
                <div
                  class="am-fs-sb__page-inner"
                  :class="{ 'am-collapsed': sidebarCollapsed }"
                >
                  <div class="am-fs-sb__page-icon">
                    <span :class="`am-icon-${step.icon}`"></span>
                  </div>
                  <transition name="fade">
                    <p
                      v-if="!sidebarCollapsed"
                      class="am-fs-sb__page-heading"
                      :class="sidebarCollapseItemsClass"
                    >
                      {{ step.label }}
                    </p>
                  </transition>
                  <transition name="fade">
                    <div
                      v-if="!sidebarCollapsed"
                      class="am-fs-sb__page-indicator"
                      :class="sidebarCollapseItemsClass"
                    >
                      <span class="am-icon-arrow-big-right"></span>
                    </div>
                  </transition>
                </div>
              </div>
              <div v-if="index === 0" class="am-fs-sb__page-divider"></div>
            </template>
          </div>
        </template>
        <template #support-info>
          <div ref="sidebarFooterRef" class="am-fs-sb__footer">
            <div
              class="am-fs-sb__page"
              @click="sidebarCollapsed = !sidebarCollapsed"
            >
              <div class="am-fs-sb__page-inner">
                <div class="am-fs-sb__page-icon">
                  <span class="am-icon-dashboard"></span>
                </div>
                <transition name="fade">
                  <p
                    v-if="!sidebarCollapsed"
                    class="am-fs-sb__page-heading"
                    :class="sidebarCollapseItemsClass"
                  >
                    {{ labelsDisplay('toggle_sidebar', 'sidebar') }}
                  </p>
                </transition>
              </div>
            </div>
            <div class="am-fs-sb__page-divider"></div>
            <div class="am-fs-sb__page">
              <div class="am-fs-sb__page-inner">
                <div class="am-fs-sb__page-icon">
                  <span class="am-icon-logout"></span>
                </div>
                <transition name="fade">
                  <p
                    v-if="!sidebarCollapsed"
                    class="am-fs-sb__page-heading"
                    :class="sidebarCollapseItemsClass"
                  >
                    {{ labelsDisplay('log_out', 'sidebar') }}
                  </p>
                </transition>
              </div>
            </div>
          </div>
        </template>
      </SideBar>
      <MainContent :max-width="786" :old-responsive="false">
        <template #header>
          <MainPanelHeader>
            <template #default>
              <div class="am-caph">
                <div class="am-caph__text">
                  {{ sidebarSteps[stepIndex + 1].label }}
                </div>
                <div
                  v-if="!sidebarVisibility"
                  class="am-cap__menu"
                  @click="() => (menuVisibility = !menuVisibility)"
                >
                  <span class="am-icon-menu"></span>
                </div>
                <TimeZoneSelect
                  v-if="
                    sidebarVisibility &&
                    stepName !== 'profile' &&
                    customizedOptions.timeZone.visibility
                  "
                />
                <MenuSlideDialog
                  :menu-items="sidebarSteps"
                  :monitor="stepName"
                  :visibility="menuVisibility"
                  :custom-css="cssSideMenu"
                  :width="240"
                  :customized-labels="globalStepLabels('sidebar')"
                  position="right"
                  @update:visibility="(e) => (menuVisibility = e)"
                  @logout="menuLogout"
                />
              </div>
            </template>
          </MainPanelHeader>
        </template>
        <template #step>
          <component
            :is="stepsArray[stepIndex]"
            class="am-fs__main-content"
          ></component>
        </template>
      </MainContent>
    </template>
  </div>
</template>

<script setup>
// * Form construction
import MainContent from '../../../common/SbsFormConstruction/MainContent/MainContent.vue'
import SideBar from '../../../common/SbsFormConstruction/SideBar/SideBar.vue'
import MainPanelHeader from '../../../common/SbsFormConstruction/MainContent/parts/MainPanelHeader.vue'

// * Form Component Collection
// authentication
import SignIn from '../steps/Cabinet/common/Authentication/SignIn.vue'
import SendAccessLink from '../steps/Cabinet/common/Authentication/SendAccessLink.vue'
import SendAccessLinkSuccess from '../steps/Cabinet/common/Authentication/SendAccessLinkSuccess.vue'
import SetPass from '../steps/Cabinet/common/Authentication/SetPass.vue'
// panel
import Appointments from '../steps/Cabinet/common/Appointments/Appointments.vue'
import Events from '../steps/Cabinet/common/Events/Events.vue'

// * Dedicated Components
import TimeZoneSelect from '../steps/Cabinet/common/parts/TimeZoneSelect.vue'
import MenuSlideDialog from '../../../common/SbsFormConstruction/MenuSlideDialog/MenuSlideDialog.vue'

// * import from Vue
import {
  ref,
  provide,
  inject,
  markRaw,
  computed,
  onBeforeMount,
  onMounted,
  readonly,
  watchEffect,
  onUnmounted,
  nextTick,
} from 'vue'

// * import composable
import { defaultCustomizeSettings } from '../../../../assets/js/common/defaultCustomize.js'
import { useColorTransparency } from '../../../../assets/js/common/colorManipulation.js'
import { useReactiveCustomize } from '../../../../assets/js/admin/useReactiveCustomize.js'

// * Root Settings
let amSettings = inject('settings')

// * Features
let features = useReactiveCustomize().features
provide('features', features)

// * Step index
let stepIndex = useReactiveCustomize().stepIndex
provide('stepIndex', stepIndex)

// * Pages type
let pagesType = useReactiveCustomize().pagesType
provide('pagesType', pagesType)

const amLabels = inject('labels')

// * Language key
let langKey = useReactiveCustomize().langKey
provide('langKey', langKey)

// * Step name
let stepName = useReactiveCustomize().stepName
provide('stepName', stepName)

// * Sub step name
let subStepName = useReactiveCustomize().subStepName
provide('subStepName', subStepName)

// * Page render key
let pageRenderKey = ref('cape')
provide('pageRenderKey', pageRenderKey)

// * Customize data
const { amCustomize } = useReactiveCustomize()
provide('customize', amCustomize)

let amFonts = computed(() => {
  return amCustomize.value.fonts
})
provide('amFonts', amFonts)

// * Cabinet Type
let cabinetType = ref('customer')
provide('cabinetType', cabinetType)

// * Autehtication steps
let signIn = markRaw(SignIn)
let sendAccessLink = markRaw(SendAccessLink)
let sendAccessLinkSuccess = markRaw(SendAccessLinkSuccess)
let setPass = markRaw(SetPass)

let authFlow = ref([signIn, sendAccessLink, sendAccessLinkSuccess, setPass])

// * Panel steps
let appointments = markRaw(Appointments)
let events = markRaw(Events)

let panelFlow = ref([appointments, events])

let stepsArray = computed(() => {
  if (pagesType.value === 'auth') return authFlow.value

  return panelFlow.value
})

// * Component reference
let ameliaContainer = ref(null)

let isLoaded = ref(false)

onMounted(() => {
  window.customizeComponentLoaded = true

  if (typeof window.stylesInjectedV3 === 'function') {
    if (window.v3.componentToMount === 'CustomizeEmployeePanel') {
      window.stylesInjectedV3 = () => {
        nextTick().then(() => {
          isLoaded.value = true
        })
      }
    }
  }
})

onUnmounted(() => {
  window.customizeComponentLoaded = false
  // Reset to no-op function
  window.stylesInjectedV3 = async function () {}
})

// * Plugin wrapper width
let containerWidth = ref()
provide('containerWidth', containerWidth)

// * Mobile menu
let menuVisibility = ref(false)

function menuLogout() {
  menuVisibility.value = false
}

// * Form Sidebar Collapse
let sidebarCollapsed = ref(false)
provide('sidebarCollapsed', readonly(sidebarCollapsed))

let sidebarCollapseItemsClass = ref('')

watchEffect(() => {
  if (sidebarCollapsed.value) {
    setTimeout(() => {
      sidebarCollapseItemsClass.value = 'am-collapsed'
    }, 1000)
  } else {
    sidebarCollapseItemsClass.value = ''
  }
})

let toggleSidebar = computed(() => {
  return amCustomize.value.cape.sidebar.options.toggle.visibility
})

watchEffect(() => {
  sidebarCollapsed.value = toggleSidebar.value
})

let sidebarFooterRef = ref(null)
let sidebarFooterHeight = ref(0)

// * Form Sidebar Visibility
let sidebarVisibility = ref(true)

onMounted(() => {
  if (sidebarFooterRef.value) {
    setTimeout(() => {
      sidebarFooterHeight.value = sidebarFooterRef.value.offsetHeight
    }, 200)
  }

  if (ameliaContainer.value) {
    containerWidth.value = ameliaContainer.value.offsetWidth
    sidebarCollapsed.value = !toggleSidebar.value
      ? ameliaContainer.value.offsetWidth <= 600
      : toggleSidebar.value
    sidebarVisibility.value = ameliaContainer.value.offsetWidth > 480
  }
})

// * window resize listener
window.addEventListener('resize', resize)
// * resize function
function resize() {
  if (ameliaContainer.value) {
    containerWidth.value = ameliaContainer.value.offsetWidth
    sidebarCollapsed.value = !toggleSidebar.value
      ? ameliaContainer.value.offsetWidth <= 600
      : toggleSidebar.value
    sidebarVisibility.value = ameliaContainer.value.offsetWidth > 480
    menuVisibility.value =
      ameliaContainer.value.offsetWidth > 481 ? false : menuVisibility.value
  }
}

// * Root Urls
const baseUrls = inject('baseUrls')

// * Array of Sidebar steps
const sidebarSteps = computed(() => [
  {
    key: 'profile',
    icon: 'user',
    pageLabel: amLabels.my_profile,
    label: 'John Doe',
  },
  {
    key: 'appointments',
    icon: 'service',
    label: labelsDisplay('appointments', 'appointments'),
  },
  {
    key: 'events',
    icon: 'star-outline',
    label: labelsDisplay('events', 'events'),
  },
])
provide('sidebarSteps', sidebarSteps)

let sidebarIndex = ref(0)

onMounted(() => {
  sidebarIndex.value = sidebarSteps.value.findIndex(
    (a) => a.key === stepName.value
  )
})

watchEffect(() => {
  if (amCustomize.value.fonts.customFontSelected) {
    activateCustomFontStyles()
  } else {
    importDefaultFonts()
  }
})

function activateCustomFontStyles() {
  let head = document.head || document.getElementsByTagName('head')[0]
  if (head.querySelector('#amCustomFont')) {
    head.querySelector('#amCustomFont').remove()
  }

  if (head.querySelector('#amDefaultFontImport')) {
    head.querySelector('#amDefaultFontImport').remove()
  }

  let css = `@font-face {font-family: '${amCustomize.value.fonts.fontFamily}'; src: url(${amCustomize.value.fonts.fontUrl});}`
  let style = document.createElement('style')
  head.appendChild(style)
  style.setAttribute('type', 'text/css')
  style.setAttribute('id', 'amCustomFont')
  style.appendChild(document.createTextNode(css))
}

function importDefaultFonts() {
  const head = document.head || document.getElementsByTagName('head')[0]
  if (head.querySelector('#amDefaultFontImport')) {
    return
  }

  const base = baseUrls.value.wpAmeliaPluginURL
  const style = document.createElement('style')
  style.setAttribute('type', 'text/css')
  style.setAttribute('id', 'amDefaultFontImport')
  style.textContent = `@import url("${base}v3/src/assets/scss/common/fonts/font.css");`
  head.appendChild(style)
}

/**
 * Lifecycle Hooks
 */
onBeforeMount(() => {
  window.scrollTo({
    top: 0,
    left: 0,
    behavior: 'smooth',
  })
})

// * Label computed function
function labelsDisplay(label, stepKey) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value][stepKey].translations &&
      amCustomize.value[pageRenderKey.value][stepKey].translations[label] &&
      amCustomize.value[pageRenderKey.value][stepKey].translations[label][
        langKey.value
      ]
      ? amCustomize.value[pageRenderKey.value][stepKey].translations[label][
          langKey.value
        ]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Step labels passed to inner components
function globalStepLabels(step) {
  let stepLabels = {}
  let customizedLabels = step
    ? amCustomize.value[pageRenderKey.value][step].translations
    : null
  if (customizedLabels && Object.keys(customizedLabels)) {
    Object.keys(
      amCustomize.value[pageRenderKey.value][step].translations
    ).forEach((label) => {
      stepLabels[label] =
        amCustomize.value[pageRenderKey.value][step].translations[label][
          langKey.value
        ]
    })
  } else {
    stepLabels = {}
  }

  return stepLabels
}

// * Custmozes Options
let customizedOptions = computed(() => {
  if (stepName.value === 'packageAppointmentsList')
    return amCustomize.value[pageRenderKey.value].packagesList.options
  return amCustomize.value[pageRenderKey.value][stepName.value].options
})

// * Colors block
let amColors = computed(() => {
  return amCustomize.value[pageRenderKey.value]
    ? amCustomize.value[pageRenderKey.value].colors
    : defaultCustomizeSettings[pageRenderKey.value].colors
})
provide('amColors', amColors)

let cssVars = computed(() => {
  return {
    '--am-c-primary': amColors.value.colorPrimary,
    '--am-c-success': amColors.value.colorSuccess,
    '--am-c-error': amColors.value.colorError,
    '--am-c-warning': amColors.value.colorWarning,
    '--am-c-main-bgr': amColors.value.colorMainBgr,
    '--am-c-main-heading-text': amColors.value.colorMainHeadingText,
    '--am-c-main-text': amColors.value.colorMainText,
    '--am-c-sb-bgr': amColors.value.colorSbBgr,
    '--am-c-sb-text': amColors.value.colorSbText,
    '--am-c-inp-bgr': amColors.value.colorInpBgr,
    '--am-c-inp-border': amColors.value.colorInpBorder,
    '--am-c-inp-text': amColors.value.colorInpText,
    '--am-c-inp-placeholder': amColors.value.colorInpPlaceHolder,
    '--am-c-drop-bgr': amColors.value.colorDropBgr,
    '--am-c-drop-text': amColors.value.colorDropText,
    '--am-c-btn-prim': amColors.value.colorBtnPrim,
    '--am-c-btn-prim-text': amColors.value.colorBtnPrimText,
    '--am-c-btn-sec': amColors.value.colorBtnSec,
    '--am-c-btn-sec-text': amColors.value.colorBtnSecText,
    '--am-c-skeleton-op20': useColorTransparency(
      amColors.value.colorMainText,
      0.2
    ),
    '--am-c-skeleton-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
    '--am-c-skeleton-sb-op20': useColorTransparency(
      amColors.value.colorSbText,
      0.2
    ),
    '--am-c-skeleton-sb-op60': useColorTransparency(
      amColors.value.colorSbText,
      0.6
    ),
    '--am-font-family': amFonts.value.fontFamily,

    // css properties
    '--am-rad-inp': '6px',
    '--am-fs-inp': '15px',
    // -mw- max width
    // -brad- border-radius
    '--am-mw-main': sidebarVisibility.value
      ? sidebarCollapsed.value
        ? '858px'
        : '1024px'
      : '520px',
    '--am-brad-main': sidebarVisibility.value ? '0 0.5rem 0.5rem 0' : '0.5rem',
  }
})

let cssSideMenu = computed(() => {
  return {
    '--am-c-msd-bgr': amColors.value.colorSbBgr,
    '--am-c-msd-text': amColors.value.colorSbText,
    '--am-c-msd-text-op05': useColorTransparency(
      amColors.value.colorSbText,
      0.05
    ),
    '--am-c-msd-text-op10': useColorTransparency(
      amColors.value.colorSbText,
      0.1
    ),
    '--am-c-msd-text-op60': useColorTransparency(
      amColors.value.colorSbText,
      0.6
    ),
  }
})
</script>

<script>
export default {
  name: 'CustomerPanel',
}
</script>

<style lang="scss">
@import '../../../../assets/scss/public/overides/overides';
@import '../../../../assets/scss/common/empty-state/_empty-state-mixin.scss';
@import '../../../../assets/scss/common/transitions/_transitions-mixin.scss';

#amelia-app-backend-new {
  background-color: transparent;

  #amelia-container {
    background-color: transparent;

    * {
      font-family: var(--am-font-family);
      font-style: initial;
      box-sizing: border-box;
      word-break: break-word;
      letter-spacing: normal;
    }

    // cap - cabinet panel
    &.am-cap {
      // Container Wrapper
      &__wrapper {
        display: flex;
        justify-content: center;
        max-width: var(--am-mw-main);
        width: 100%;
        height: 700px;
        margin: 16px auto 100px;
        border-radius: 8px;
        box-shadow: 0 30px 40px rgba(0, 0, 0, 0.12);
        transition: max-width 0.3s ease-in-out;

        &.am-auth {
          box-shadow: unset;
          height: auto;
        }

        &.am-collapsed {
          transition-delay: 1s;
        }

        * {
          font-family: var(--am-font-family), sans-serif;
          box-sizing: border-box;
        }
      }
    }

    .am-cap {
      &__header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        .am-select-wrapper {
          max-width: 250px;
        }
      }
    }

    .am-asi {
      .el-form {
        &-item {
          display: block;
          font-family: var(--am-font-family), sans-serif;
          font-size: var(--am-fs-label);
          margin-bottom: 24px;

          &__label {
            flex: 0 0 auto;
            text-align: left;
            font-size: var(--am-fs-label);
            line-height: 1.3;
            color: var(--am-c-main-text);
            box-sizing: border-box;
            margin: 0;

            &:before {
              color: var(--am-c-error);
            }
          }

          &__content {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            flex: 1;
            position: relative;
            font-size: var(--am-fs-inp);
            min-width: 0;
          }

          &__error {
            font-size: 12px;
            color: var(--am-c-error);
            padding-top: 4px;
          }
        }
      }
    }
  }
}
</style>
