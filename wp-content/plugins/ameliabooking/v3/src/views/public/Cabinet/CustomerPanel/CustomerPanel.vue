<template>
  <template v-if="!amFonts.customFontSelected">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      rel="stylesheet"
      type="text/css"
      :href="`${baseUrls.wpAmeliaPluginURL}v3/src/assets/scss/common/fonts/font.css`"
      media="all"
    />
  </template>
  <div
    id="amelia-container"
    ref="ameliaContainer"
    class="am-cap__wrapper"
    :class="[
      { 'am-collapsed': sidebarCollapsed },
      { 'am-auth': !authenticated },
    ]"
    :style="cssVars"
  >
    <Auth v-if="!authenticated"></Auth>
    <template v-if="authenticated">
      <SideBar
        v-if="sidebarVisibility"
        class="am-fs-sb"
        :class="[
          { 'am-collapsed': sidebarCollapsed },
          { 'am-rtl': isRtl }
        ]"
        :style="{
          width: !sidebarCollapsed ? '240px' : '72px',
          paddingBottom: `${sidebarFooterHeight + 16}px`,
        }"
      >
        <template #step-list>
          <div class="am-fs-sb__page-wrapper am-fs-sb__page-wrapper__cabinet">
            <template v-for="(step, index) in sidebarSteps" :key="step.key">
              <div
                v-if="
                  step.key !== 'packages' ||
                  store.getters['entities/getPackages'].length
                "
                class="am-fs-sb__page"
                :class="{ selected: pageKey === step.key }"
                :style="{'pointer-events': shortcodeData.cabinetType === 'employee' && shortcodeData.profile && step.key === 'profile' ? 'none' : ''}"
                @click="() => {!(shortcodeData.cabinetType === 'employee' && shortcodeData.profile && step.key === 'profile') ? sidebarSelection(step, index) : () => {}}"
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
                      :class="[sidebarCollapseItemsClass, {'am-rtl': isRtl}]"
                    >
                      {{ step.label }}
                    </p>
                  </transition>
                  <transition name="fade">
                    <div
                      v-if="!sidebarCollapsed"
                      class="am-fs-sb__page-indicator"
                      :class="[sidebarCollapseItemsClass, {'am-rtl': isRtl}]"
                    >
                      <span :class="isRtl ? 'am-icon-arrow-big-left' : 'am-icon-arrow-big-right'"></span>
                    </div>
                  </transition>
                </div>
              </div>
              <div v-if="index === 0 && sidebarSteps[0].key === 'profile'" class="am-fs-sb__page-divider"></div>
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
                    {{ amLabels.toggle_sidebar }}
                  </p>
                </transition>
              </div>
            </div>
            <div class="am-fs-sb__page-divider"></div>
            <div class="am-fs-sb__page" @click="logout">
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
                    {{ amLabels.log_out }}
                  </p>
                </transition>
              </div>
            </div>
          </div>
        </template>
      </SideBar>
      <MainContent :max-width="786" :old-responsive="false">
        <template #header>
          <MainPanelHeader :ready="ready">
            <template #default>
              <div class="am-caph">
                <div class="am-caph__text">
                  {{ header }}
                </div>
                <div
                  v-if="!sidebarVisibility"
                  class="am-caph__menu"
                  @click="() => (menuVisibility = !menuVisibility)"
                >
                  <span class="am-icon-menu"></span>
                </div>
                <TimeZoneSelect
                  v-if="
                    entitiesReady &&
                    sidebarVisibility &&
                    timeZoneSelectVisibility
                  "
                />
                <MenuSlideDialog
                  :menu-items="sidebarSteps"
                  :monitor="pageKey"
                  :visibility="menuVisibility"
                  :custom-css="cssSideMenu"
                  :customized-labels="amLabels"
                  position="right"
                  :width="240"
                  @update:visibility="(e) => (menuVisibility = e)"
                  @click="menuSelection"
                  @logout="menuLogout"
                />
              </div>
            </template>
          </MainPanelHeader>
        </template>
        <template #step>
          <component
            :is="pagesObj[pageKey]"
            class="am-fs__main-content"
            :load-bookings-counter="loadBookingsCounter"
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

// * Dedicated Components
import TimeZoneSelect from '../common/parts/TimeZoneSelect.vue'
import MenuSlideDialog from '../../../common/SbsFormConstruction/MenuSlideDialog/MenuSlideDialog.vue'

// * import from Vue
import {
  ref,
  reactive,
  watch,
  provide,
  inject,
  markRaw,
  computed,
  onMounted,
  readonly,
  onBeforeMount,
} from 'vue'

// * import from Vuex
import { useStore } from 'vuex'

// * import composable
import { defaultCustomizeSettings } from '../../../../assets/js/common/defaultCustomize.js'
import { useColorTransparency } from '../../../../assets/js/common/colorManipulation.js'
import { useCurrentTimeZone } from '../../../../assets/js/common/helper'
import { useElementSize } from '@vueuse/core'
import { useFrontendEmployeeServiceList } from '../../../../assets/js/common/employee'

// * Form Component Collection
import Auth from '../common/Authentication/Auth.vue'
import Profile from '../common/Profile/Profile.vue'
import EmployeeProfile from '../common/Profile/EmployeeProfile.vue'
import Appointments from '../common/Appointments/Appointments.vue'
import Events from '../common/Events/Events.vue'
import Packages from '../common/Packages/Packages.vue'

// * Import from Libraries
import { useCookies } from 'vue3-cookies'
import { usePopulateSettings } from '../../../../assets/js/common/settings'
import { useInitAttendee } from '../../../../assets/js/common/attendees'

// * Vars
const vueCookies = useCookies()['cookies']

const store = useStore()

// * RTL - right to left
let isRtl = computed(() => store.getters['getIsRtl'])

// * Data in shortcode
const shortcodeData = inject('shortcodeData')

// * Authenticated
let authenticated = computed(() => {
  return store.getters['auth/getAuthenticated']
})

watch(authenticated, (current) => {
  if (current) {
    store.commit('auth/setNewPassword', '')
    store.commit('auth/setConfirmPassword', '')
  }
})

// * Cabinet Type
let cabinetType = ref(
  shortcodeData.value.cabinetType === 'employee' ? 'provider' : 'customer'
)
provide('cabinetType', cabinetType)

// * Plugin Licence
let licence = inject('licence')

// * Origin key
let originKey = computed(() => {
  if (cabinetType.value === 'provider') return 'cape'
  return 'capc'
})
provide('originKey', originKey)

// * Component reference
let ameliaContainer = ref(null)

// * Plugin wrapper width
let { width: containerWidth } = useElementSize(ameliaContainer)
provide('containerWidth', containerWidth)

// * Root Settings
const amSettings = inject('settings')

let pageKey = ref(
  shortcodeData.value.appointments || !shortcodeData.value.events
    ? 'appointments'
    : 'events'
)

// * Customize
const amCustomize = computed(() => {
  return amSettings.customizedData && originKey.value in amSettings.customizedData
    ? amSettings.customizedData[originKey.value]
    : defaultCustomizeSettings[originKey.value]
})
provide('amCustomize', amCustomize)

// * Fonts
const amFonts = ref(
  amSettings.customizedData
    ? amSettings.customizedData.fonts
    : defaultCustomizeSettings.fonts
)
provide('amFonts', amFonts)

// * labels
const labels = inject('labels')

provide('amLabels', labels)

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() =>
  amSettings.general.usedLanguages.includes(localLanguage.value)
)

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({ ...labels })

  if (amSettings.customizedData) {
    Object.keys(amCustomize.value).forEach((stepKey) => {
      if (stepKey !== 'colors' && amCustomize.value[stepKey].translations) {
        let customizedLabels = amCustomize.value[stepKey].translations
        Object.keys(customizedLabels).forEach((labelKey) => {
          if (
            customizedLabels[labelKey][localLanguage.value] &&
            langDetection.value
          ) {
            computedLabels[labelKey] =
              customizedLabels[labelKey][localLanguage.value]
          } else if (customizedLabels[labelKey].default) {
            computedLabels[labelKey] = customizedLabels[labelKey].default
          }
        })
      }
    })
  }
  return computedLabels
})

// * Activate custom font styles
function activateCustomFontStyles () {
  let head = document.head || document.getElementsByTagName('head')[0]
  if (head.querySelector('#amCustomFont')) {
    head.querySelector('#amCustomFont').remove()
  }

  let css = `@font-face {font-family: 'comic'; src: url(http://localhost/amelia-test/wp-content/uploads/amelia/fonts/comic.woff);}`
  let style = document.createElement('style')
  head.appendChild(style)
  style.setAttribute('type', 'text/css')
  style.setAttribute('id', 'amCustomFont')
  style.appendChild(document.createTextNode(css))
}

if (amFonts.value.customFontSelected) activateCustomFontStyles()

// * Customized Options
let customizedOptions = computed(() => {
  if (pageKey.value === 'packages')
    return amCustomize.value.packagesList.options
  return amCustomize.value[pageKey.value].options
})

let timeZoneSelectVisibility = computed(() => {
  let key = pageKey.value

  if (cabinetType.value === 'customer') {
    return key !== 'profile' ? customizedOptions.value.timeZone.visibility : true
  }

  return key === 'appointments' || key === 'events' ? customizedOptions.value.timeZone.visibility : true
})

// * Mobile menu
let menuVisibility = ref(false)

// * Form Sidebar Collapse
let sidebarCollapsed = ref(false)
provide('sidebarCollapsed', readonly(sidebarCollapsed))

let sidebarCollapseItemsClass = ref('')

watch(sidebarCollapsed, (current) => {
  if (current) {
    setTimeout(() => {
      sidebarCollapseItemsClass.value = 'am-collapsed'
    }, 1000)
  } else {
    sidebarCollapseItemsClass.value = ''
  }
})

let sidebarFooterRef = ref(null)
let sidebarFooterHeight = ref(0)

// * Form Sidebar Visibility
let sidebarVisibility = computed(() => containerWidth.value > 480)

onMounted(() => {
  if (sidebarFooterRef.value) {
    setTimeout(() => {
      sidebarFooterHeight.value = sidebarFooterRef.value.offsetHeight
    }, 200)
  }

  setTimeout(() => {
    sidebarCollapsed.value =
      containerWidth.value <= 600
        ? true
        : amCustomize.value.sidebar.options.toggle.visibility
  }, 1300)
})

/*************
 * Watchers *
 *************/
const handleResize = () => {
  if (containerWidth.value <= 600) {
    sidebarCollapsed.value = true
  } else {
    sidebarCollapsed.value = amCustomize.value.sidebar.options.toggle.visibility
  }
}

window.addEventListener('resize', handleResize)

watch(containerWidth, (current) => {
  menuVisibility.value = current > 481 ? false : menuVisibility.value
})

function populateFilterOptions () {
  store.dispatch(
    'cabinetFilters/injectServiceOptions',
    store.getters['entities/getServices'].map(i => i.id)
  )

  store.dispatch(
    'cabinetFilters/injectProviderOptions',
    cabinetType.value === 'customer'
      ? store.getters['entities/getEmployees'].map(i => i.id)
      : []
  )

  store.dispatch(
    'cabinetFilters/injectLocationOptions',
    store.getters['entities/getLocations'].map(i => i.id)
  )
}

const entitiesReady = computed(() => store.getters['entities/getReady'])
watch(entitiesReady, (ready) => {
  if (ready) {
    if (shortcodeData.value.cabinetType === 'employee' && store.getters['employee/getId']) {
      let employee = store.getters['employee/getEmployee']

      store.commit('employee/setServiceList', useFrontendEmployeeServiceList(store, employee.serviceList))

      store.commit('entities/setEmployees', [store.getters['entities/getEmployee'](store.getters['employee/getId'])])
    }

    populateFilterOptions()
  }
})

// * Root Urls
const baseUrls = inject('baseUrls')

let entities = [
  'employees',
  'categories',
  'locations',
  'packages',
  'entitiesRelations',
  'customFields',
]

if (shortcodeData.value.cabinetType === 'employee') {
  entities = entities.concat(['taxes', 'spaces', 'tags', 'settings'])
}

store.dispatch('entities/getEntities', {
  types: entities,
  licence: 'basic',
  loadEntities: true,
  showHidden: true,
  isPanel: true,
})

// * Data loaded
let ready = computed(() => store.getters['getReady'])

// * Panel Fluid Pages
let pagesObj = ref({
  profile: markRaw(
    shortcodeData.value.cabinetType === 'employee' ? EmployeeProfile : Profile
  ),
})

if (shortcodeData.value.appointments || !shortcodeData.value.events) {
  pagesObj.value.appointments = markRaw(Appointments)
}

if (shortcodeData.value.events || !shortcodeData.value.appointments) {
  pagesObj.value.events = markRaw(Events)
}

if (
  amSettings.featuresIntegrations.packages.enabled &&
  (shortcodeData.value.appointments || !shortcodeData.value.events) &&
  (licence.isPro || licence.isDeveloper)
) {
  pagesObj.value.packages = markRaw(Packages)
}

// * Array of Sidebar steps
const sidebarSteps = ref([{
  key: 'profile',
  icon: 'user',
  pageLabel: amLabels.value.my_profile,
  label: computed(() => {
    if (originKey.value === 'cape') {
      return `${store.getters['auth/getProfile'].firstName} ${store.getters['auth/getProfile'].lastName}`
    } else {
      if (!amCustomize.value.profile.options.lastName.visibility) return `${store.getters['auth/getProfile'].firstName}`
      return `${store.getters['auth/getProfile'].firstName} ${store.getters['auth/getProfile'].lastName}`
    }
  }),
}])

if (shortcodeData.value.appointments || !shortcodeData.value.events) {
  sidebarSteps.value.push({
    key: 'appointments',
    icon: 'service',
    label: amLabels.value.appointments,
  })
}

if (shortcodeData.value.events || !shortcodeData.value.appointments) {
  sidebarSteps.value.push({
    key: 'events',
    icon: 'star-outline',
    label: amLabels.value.events,
  })
}

if (
  amSettings.featuresIntegrations.packages.enabled &&
  shortcodeData.value.cabinetType === 'customer' &&
  (shortcodeData.value.appointments || !shortcodeData.value.events) &&
  (licence.isPro || licence.isDeveloper)
) {
  sidebarSteps.value.push({
    key: 'packages',
    icon: 'shipment',
    label: amLabels.value.packages,
  })
}

provide('sidebarSteps', sidebarSteps)

onMounted(() => {
  sidebarIndex.value = sidebarSteps.value.findIndex(
    (a) => a.key === pageKey.value
  )
})

let sidebarIndex = ref(0)

function sidebarSelection(step, index) {
  if (shortcodeData.value.cabinetType === 'employee') {
    if (step.key !== 'appointments') {
      store.commit('appointment/resetAppointment', {providerId: store.getters['auth/getProfile'].id, categoryId: null, serviceId: null, locationId: null})
      store.commit('customerInfo/setCustomers', [])
      store.commit('customerInfo/setCustomersIds', [])
    }

    if (step.key !== 'events') {
      store.commit(
        'event/setEvent',
        {settings: usePopulateSettings(amSettings, store.getters['event/getSettings'], {}, {})}
      )

      store.commit('attendee/setAttendee', useInitAttendee(store, store.getters['attendee/getDefaultAttendee']))
    }
  }

  if (step.key === 'appointments' || step.key === 'events') {
    populateFilterOptions()
  }

  pageKey.value = step.key
  sidebarIndex.value = index
  store.commit('cabinetFilters/setResetFilters')
}

function menuSelection(obj) {
  if (shortcodeData.value.cabinetType === 'employee' && shortcodeData.value.profile && obj.step.key === 'profile') return
  sidebarSelection(obj.step, obj.index)
  store.commit('cabinetFilters/setResetFilters')
}

function logout() {
  store.dispatch('auth/logout')
}

function menuLogout() {
  menuVisibility.value = false
  logout()
}

let loadBookingsCounter = ref(0)

function bookingsCounterChanger() {
  loadBookingsCounter.value++
}

let header = computed(() => {
  switch (sidebarSteps.value[sidebarIndex.value].key) {
    case 'profile':
      return sidebarSteps.value[sidebarIndex.value].pageLabel

    case 'appointments':
      if (
        store.getters['appointment/getActive'] &&
        !store.getters['appointment/getId']
      ) {
        return amLabels.value.new_appointment
      } else if (
        store.getters['appointment/getActive'] &&
        store.getters['appointment/getId']
      ) {
        return amLabels.value.edit_appointment
      } else {
        return sidebarSteps.value[sidebarIndex.value].label
      }

    case ('events'):
      if (store.getters['attendee/getActive'] && !store.getters['attendee/getId']) {
        return amLabels.value.event_add_attendee
      } else if (store.getters['attendee/getActive'] && store.getters['attendee/getId']) {
        return amLabels.value.event_edit_attendee
      } else if (store.getters['event/getActive'] && !store.getters['event/getId']) {
        return amLabels.value.new_event
      } else if (store.getters['event/getActive'] && store.getters['event/getId']) {
        return amLabels.value.edit_event
      }  else {
        return sidebarSteps.value[sidebarIndex.value].label
      }

    default:
      return sidebarSteps.value[sidebarIndex.value].label
  }
})

provide('bookingsCounterChanger', {
  bookingsCounterChanger,
})

const adminTimeZone = inject('timeZone')

onBeforeMount(() => {
  if (!amSettings.general.showClientTimeZone) {
    let initialTimeZone = adminTimeZone.value

    if (vueCookies.get('ameliaUserTimeZone')) {
      initialTimeZone = vueCookies.get('ameliaUserTimeZone')
    }

    store.commit('cabinet/setTimeZone', initialTimeZone)
  }

  if (!store.getters['cabinet/getTimeZone'])
    store.commit('cabinet/setTimeZone', useCurrentTimeZone())
})

// * Colors block
let amColors = computed(() => {
  return amCustomize.value.colors
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
    '--am-c-main-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
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
    '--am-c-btn-danger': amColors.value.colorBtnDanger,
    '--am-c-btn-danger-text': amColors.value.colorBtnDangerText,
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
    '--am-c-scroll-op30': useColorTransparency(
      amColors.value.colorPrimary,
      0.3
    ),
    '--am-c-scroll-op10': useColorTransparency(
      amColors.value.colorPrimary,
      0.1
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
@import '../../../../assets/scss/common/reset/reset';
@import '../../../../assets/scss/common/icon-fonts/style';
@import '../../../../assets/scss/common/skeleton/skeleton.scss';
@import '../../../../assets/scss/common/empty-state/_empty-state-mixin.scss';
@import '../../../../assets/scss/common/transitions/_transitions-mixin.scss';

:root {
  // Colors
  // shortcuts
  // -c-    color
  // -bgr-  background
  // -prim- primary
  // -sec-  secondary
  // primitive colors
  --am-c-primary: #{$blue-1000};
  --am-c-success: #{$green-1000};
  --am-c-error: #{$red-900};
  --am-c-warning: #{$yellow-1000};
  // main container colors - right part of the form
  --am-c-main-bgr: #{$am-white};
  --am-c-main-heading-text: #{$shade-800};
  --am-c-main-text: #{$shade-900};
  // sidebar container colors - left part of the form
  --am-c-sb-bgr: #17295a;
  --am-c-sb-text: #{$am-white};
  // input global colors - usage input, textarea, checkbox, radio button, select input, adv select input
  --am-c-inp-bgr: #{$am-white};
  --am-c-inp-border: #{$shade-250};
  --am-c-inp-text: #{$shade-900};
  --am-c-inp-placeholder: #{$shade-500};
  // dropdown global colors - usage select dropdown, adv select dropdown
  --am-c-drop-bgr: #{$am-white};
  --am-c-drop-text: #{$shade-1000};
  // button global colors
  --am-c-btn-prim: #{$blue-900};
  --am-c-btn-prim-text: #{$am-white};
  --am-c-btn-sec: #{$am-white};
  --am-c-btn-sec-text: #{$shade-900};

  // Properties
  // shortcuts
  // -h- height
  // -fs- font size
  // -rad- border radius
  --am-h-inp: 40px;
  --am-fs-inp: 15px;
  --am-rad-inp: 6px;
  --am-fs-label: 15px;
  --am-fs-btn: 15px;

  // Font
  --am-font-family: 'Amelia Roboto', sans-serif;
}

.amelia-v2-booking {
  background-color: transparent;

  #amelia-container {
    background-color: transparent;

    * {
      font-family: var(--am-font-family), sans-serif;
      font-style: initial;
      box-sizing: border-box;
      word-break: break-word;
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
        margin: 100px auto;
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

        // overrides for element plus components
        // tabs
        .el-tabs {
          display: flex;
          flex-direction: column-reverse;
          gap: 16px 0;

          &__nav {
            gap: 0 40px;

            &-wrap {
              &:after {
                background-color: var(--am-c-main-text-op10);
              }

              &.is-scrollable {
                padding: 0 24px;
              }
            }

            &-next,
            &-prev {
              color: var(--am-c-main-text);
              top: 11px;
            }
          }

          &__active-bar {
            background-color: var(--am-c-primary);
          }

          &__item {
            padding: 0;
            line-height: 40px;
            color: var(--am-c-main-text);
            transition: color 0.2s ease-in-out;

            &:nth-child(2) {
              padding-left: 0;
            }

            &:last-child {
              padding-right: 0;
            }

            &.is-active {
              color: var(--am-c-primary);
            }

            &:focus-visible {
              box-shadow: none;
            }

            &:hover {
              color: var(--am-c-primary);
            }

            &.is-focus {
              color: var(--am-c-main-text);

              &.is-active {
                color: var(--am-c-primary);
              }
            }
          }

          &__content {
            overflow: unset;
            position: static;
          }
        }

        // collapse
        .el-collapse-item {
          &__wrap {
            overflow: visible;
          }

          &__header {
            border: none;
            font-size: var(--am-fs-label);
            line-height: 1.6;
            height: unset;
          }
        }

        * {
          font-family: var(--am-font-family), sans-serif;
          box-sizing: border-box;
        }

        .am-fs-sb__page {
          max-height: 40px;
        }
      }
    }

    // cap - cabinet panel for inner elements
    .am-cap {
      &__header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        .am-select-wrapper {
          max-width: 250px;
        }
      }

      .el-collapse-item {
        &__wrap {
          background-color: transparent;
        }

        &__content {
          background-color: transparent;
        }
      }

      .el-form-item {
        &__label {
          display: inline-flex;
          align-items: center;
          justify-content: flex-start;
          gap: 4px;
        }
      }

      .am-tooltip {
        &__trigger {
          color: var(--am-c-main-text);
          cursor: help;
        }
      }
    }

    .am-asi {
      .el-form {
        &-item {
          display: block;
          font-family: var(--am-font-family), sans-serif;
          font-size: var(--am-fs-label);
          margin-bottom: 30px;

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

// * Date picker override
.am-cap__datepicker-popper {
  @media only screen and (max-width: 660px) {
    left: 0 !important;
    width: 100%;

    .el-picker-panel.el-date-range-picker {
      width: 100%;

      .el-picker-panel__body {
        min-width: unset;

        @media only screen and (max-width: 570px) {
          display: flex;
          flex-direction: column;

          .el-date-range-picker__content {
            width: 100%;

            &.is-left {
              border: none;
            }
          }
        }
      }
    }
  }
}

// * Customer select options styles
.am-cap-cust__popper {
  .el-select-dropdown__item {
    --am-pad-select-option: 8px 8px 0;
  }

  .am-cap__cust-option {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--am-c-option-text-op15);

    &__heading {
      font-size: 15px;
      font-weight: 500;
      color: var(--am-c-option-text);
    }

    &__inner {
      font-size: 12px;
      font-weight: 400;
      color: var(--am-c-option-text-op65);
    }
  }
}
</style>
