<template>
  <template v-if="ready">
    <Content
      v-if="!empty"
      ref="contentRef"
      wrapper-class="am-fcis"
      form-class="am-fcis__form"
      heading-class="am-fcis__header"
      :content-class="`am-fcis__content ${scrollVisibility ? '' : 'no-scroll'}`"
      :style="cssVars"
    >
      <template #header>
        <Header
          :btn-string="amLabels.back_btn"
          :btn-type="customizedOptions.backBtn.buttonType"
          :back-btn-visibility="backBtnVisibility"
          :cart-btn-visibility="cartBtnVisibility"
          :cart-items-number="cartItemsNumber"
          role="navigation"
          aria-label="Navigation Controls"
          @go-back="goBack"
          @go-to-cart="goToCart"
        ></Header>
      </template>

      <template #heading>
        <div
          :class="[{'am-tablet': pageWidth <= 678}, {'am-mobile': pageWidth < 450}]"
          class="am-fcis__header-top"
          role="region"
          aria-label="Service Information"
        >
          <div class="am-fcis__header-text">
            <span
              class="am-fcis__header-name"
              role="heading"
              aria-level="1"
            >
              {{service.name}}
            </span>
            <div
              v-if="customizedOptions.serviceBadge.visibility"
              class="am-fcis__badge am-service"
            >
              <span class="am-icon-service"></span>
              <span>
                {{ amLabels.heading_service }}
              </span>
            </div>
          </div>
          <div class="am-fcis__header-action">
            <span
              v-if="customizedOptions.servicePrice.visibility"
              class="am-fcis__header-price"
            >
              {{ (useServicePrice(amEntities, service.id).min || useServicePrice(amEntities, service.id).max) ? useServicePrice(amEntities, service.id).price : amLabels.free }}
            </span>
            <span
              v-if="(customizedOptions.tax?.visibility ?? true) && useTaxVisibility(store, service.id, 'service')"
              class="am-fcis__header-tax"
            >
              <template v-if="amSettings.payments.taxes.excluded">
                {{ `+${amLabels.total_tax_colon}` }}
              </template>
              <template v-else>
                {{amLabels.incl_tax}}
              </template>
            </span>
            <span class="am-fcis__header-btn">
              <AmButton
                :type="customizedOptions.bookingBtn.buttonType"
                @click="bookNow"
              >
                {{ amLabels.book_now }}
              </AmButton>
            </span>
          </div>
        </div>
        <div
          v-if="customizedOptions.serviceCategory.visibility
          || customizedOptions.serviceDuration.visibility
          || customizedOptions.serviceCapacity.visibility
          || customizedOptions.serviceLocation.visibility"
          class="am-fcis__header-bottom"
        >
          <div class="am-fcis__mini-info">
            <div
              v-if="customizedOptions.serviceCategory.visibility && category"
              class="am-fcis__mini-info__inner"
            >
              <span class="am-icon-folder"></span>
              <span>{{ category.name }}</span>
            </div>
            <div
              v-if="customizedOptions.serviceDuration.visibility"
              class="am-fcis__mini-info__inner"
            >
              <span class="am-icon-clock"></span>
              <span>{{ useServiceDuration(service.duration) }}</span>
            </div>
            <div
              v-if="customizedOptions.serviceCapacity.visibility && !licence.isLite"
              class="am-fcis__mini-info__inner"
            >
              <span class="am-icon-user"></span>
              <span>{{ useEmployeesServiceCapacity(amEntities , service.id) }}</span>
            </div>
            <div
              v-if="useServiceLocation(amEntities, service.id).length && customizedOptions.serviceLocation.visibility && !licence.isLite && !licence.isStarter"
              class="am-fcis__mini-info__inner"
            >
              <span class="am-icon-locations"></span>
              <span>
                {{ displayServiceLocationLabel(amEntities, service.id) }}
              </span>
            </div>
          </div>
        </div>
      </template>

      <template #content>
        <!-- Service Gallery -->
        <div v-if="service.gallery.length" class="am-fcis__gallery">
          <div
            class="am-fcis__gallery-hero"
            :class="[{'w100': service.gallery.length === 1}, {'am-mobile w100': pageWidth < 678}]"
            :style="{backgroundImage: `url(${service.gallery[0].pictureFullPath})`}"
          ></div>
          <div
            v-if="serviceThumbsGallery.length && pageWidth > 677"
            class="am-fcis__gallery-thumb__wrapper"
          >
            <div
              v-for="(img, index) in serviceThumbsGallery"
              :key="index"
              class="am-fcis__gallery-thumb"
              :class="{'am-one-thumb': serviceThumbsGallery.length === 1}"
              :style="{backgroundImage: `url(${img.pictureFullPath})`}"
            ></div>
          </div>
          <AmButton
            :custom-class="`am-fcis__gallery-btn${pageWidth < 678 ? ' am-mobile' : ''}`"
            category="secondary"
            type="filled"
            @click="() => galleryDialog = true"
          >
            {{ amLabels.view_all_photos }}
          </AmButton>
        </div>
        <!-- Service Gallery -->

        <!-- Gallery Images -->
        <AmDialog
          v-model="galleryDialog"
          :modal-class="'amelia-v2-booking amelia-v2-sgd'"
          :append-to-body="true"
          :center="true"
          :lock-scroll="false"
          width="768px"
        >
          <div class="am-gd" :style="cssGalleryDialogVars">
            <div class="am-gd__display-wrapper">
              <div class="am-gd__arrows" style="display: flex; justify-content: space-between">
                <span
                  class="am-icon-arrow-left"
                  @click="() => activeImage = activeImage <= 0 ? service.gallery.length - 1 : activeImage - 1"
                ></span>
                <span
                  class="am-icon-arrow-right"
                  @click="() => activeImage = service.gallery.length - 1 === activeImage ? 0 : activeImage + 1"
                ></span>
              </div>
              <div
                v-for="(img, index) in service.gallery"
                :key="index"
                class="am-gd__display"
                :style="{display: index === activeImage ? 'flex': 'none'}"
                @click="() => activeImage = service.gallery.length - 1 === activeImage ? 0 : activeImage + 1"
              >
                <img :src="img.pictureFullPath" :alt="index">
              </div>
            </div>
            <div class="am-gd__selection">
              {{`${activeImage + 1}/${service.gallery.length}`}}
            </div>
            <div class="am-gd__thumb-wrapper">
              <div
                v-for="(img, index) in service.gallery"
                :key="index"
                class="am-gd__thumb"
                :class="{'am-active': index === activeImage}"
                :style="{backgroundImage: `url(${img.pictureFullPath})`}"
                @click="() => activeImage = index"
              >
              </div>
            </div>
          </div>
        </AmDialog>
        <!-- /Gallery Images -->

        <!-- Service Info - (description, employees) -->
        <div
          v-if="(customizedOptions.serviceDescription.visibility && useDescriptionVisibility(service.description)) || customizedOptions.serviceEmployees.visibility"
          class="am-fcis__info"
        >
          <div class="am-fcis__info-tab__wrapper">
            <div
              v-if="useDescriptionVisibility(service.description) && customizedOptions.serviceDescription.visibility"
              class="am-fcis__info-tab"
              :class="{'am-active': tabsActive === 'description'}"
              @click="() => tabsActive = 'description'"
            >
              {{ amLabels.about_service }}
            </div>
            <div
              v-if="customizedOptions.serviceEmployees.visibility && !licence.isLite"
              :class="{'am-active': tabsActive === 'employees'}"
              class="am-fcis__info-tab"
              @click="() => tabsActive = 'employees'"
            >
              {{ amLabels.tab_employees }}
            </div>
          </div>
          <div class="am-fcis__info-content__wrapper">
            <!-- Description -->
            <div
              v-if="useDescriptionVisibility(service.description) && customizedOptions.serviceDescription.visibility"
              v-show="tabsActive === 'description'"
              class="am-fcis__info-content"
            >
              <div
                class="am-fcis__info-service__desc ql-description"
                v-html="service.description"
              ></div>
            </div>
            <!-- /Description -->

            <!-- Employees -->
            <div
              v-if="customizedOptions.serviceEmployees.visibility"
              v-show="tabsActive === 'employees'"
              class="am-fcis__info-content"
            >
              <AmCollapse>
                <AmCollapseItem
                  v-for="employee in serviceEmployees"
                  :key="employee.id"
                  side
                >
                  <template #heading>
                    <div
                      :class="{'am-mobile': pageWidth < 451}"
                      class="am-fcis__info-employee"
                    >
                      <div class="am-fcis__info-employee__hero">
                        <AmImagePlaceholder
                          item-class="am-fcis__info-employee__img"
                          :item-data="employee"
                          :trim-string="3"
                        ></AmImagePlaceholder>
                        <div class="am-fcis__info-employee__heading">
                          <div class="am-fcis__info-employee__name">
                            {{ employee.firstName }} {{ employee.lastName }}
                            <span
                              v-if="employee.badge"
                              :style="{background: employee.badge.color}"
                              class="am-fcis__info-employee__badge"
                            >
                              {{ employee.badge.content }}
                            </span>
                          </div>
                          <div
                            v-if="serviceEmployeePrice(employee) && customizedOptions.serviceEmployeePrice.visibility"
                            class="am-fcis__info-employee__price"
                          >
                            {{ serviceEmployeePrice(employee) }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </template>
                  <template #default>
                    <div
                      v-if="useDescriptionVisibility(employee.description)"
                      class="am-fcis__info-employee__description ql-description"
                      v-html="employee.description"
                    ></div>
                  </template>
                </AmCollapseItem>
              </AmCollapse>
            </div>
            <!-- /Employees -->
          </div>
        </div>

        <!-- Available Service in Packages -->
        <div
          v-if="servicePackages.length && preselected.show !== 'services' && customizedOptions.servicePackages.visibility && !useCartHasItems(store)"
          class="am-fcis__include-wrapper"
        >
          <div class="am-fcis__include-heading">
            <span class="am-fcis__include-heading__text">
              {{ amLabels.service_available_in_package }}
            </span>
            <span
              v-if="servicePackages.length > 2"
              class="am-fcis__include-heading__btn"
              @click="() => displayAllPackages = !displayAllPackages"
            >
              <template v-if="!displayAllPackages">
                {{ amLabels.more_packages }}
              </template>
              <template v-else>
                {{ amLabels.less_packages }}
              </template>
            </span>
          </div>
          <div
            v-for="pack in displayServicePackages"
            :key="pack.id"
            class="am-fcis__include"
            @click="selectServicePackage(pack)"
          >
            <div class="am-fcis__include-hero">
              <AmImagePlaceholder
                v-if="pageWidth > 450"
                item-class="am-fcis__include-img"
                :item-data="pack"
                :trim-string="3"
              ></AmImagePlaceholder>
              <div
                :class="{'am-mobile': pageWidth < 451}"
                class="am-fcis__include-text"
              >
                <div
                  class="am-fcis__include-header"
                  :class="{'am-mobile': pageWidth < 600}"
                >
                  <div
                    class="am-fcis__include-name"
                    :class="{'am-mobile': pageWidth < 600}"
                  >
                    {{ pack.name }}
                  </div>
                  <div
                    v-if="customizedOptions.packagePrice.visibility"
                    class="am-fcis__include-cost"
                  >
                    <span
                      v-if="pack.discount"
                      class="am-fcis__include-discount"
                    >
                      {{`${amLabels.save} ${pack.discount}%`}}
                    </span>
                    <span class="am-fcis__include-price">
                      {{ pack.price ? useFormattedPrice(usePackageAmount(pack)) : amLabels.free }}
                    </span>
                    <span
                      v-if="(customizedOptions.tax?.visibility ?? true) && useTaxVisibility(store, pack.id, 'package')"
                      class="am-fcis__include-price"
                    >
                      <template v-if="amSettings.payments.taxes.excluded">
                        {{ `+${amLabels.total_tax_colon}` }}
                      </template>
                      <template v-else>
                        {{amLabels.incl_tax}}
                      </template>
                    </span>
                  </div>
                </div>
                <div class="am-fcis__include-info">
                  <div
                    v-if="customizedOptions.packageCategory.visibility && category"
                    class="am-fcis__include-info__inner"
                  >
                    <span class="am-icon-folder"></span>
                    <span>{{ category.name }}</span>
                  </div>
                  <div
                    v-if="customizedOptions.packageDuration.visibility"
                    class="am-fcis__include-info__inner"
                  >
                    <span class="am-icon-clock"></span>
                    <span v-if="pack.endDate">
                  {{ `${amLabels.expires_at} ${pack.endDate.split(' ')[0]}` }}
                </span>
                    <span v-else-if="pack.durationCount">
                  {{ `${amLabels.expires_after} ${pack.durationCount} ${durationTypeLabel(pack.durationCount, pack.durationType)}` }}
                </span>
                    <span v-else>
                  {{ amLabels.without_expiration }}
                </span>
                  </div>
                  <div
                    v-if="customizedOptions.packageCapacity.visibility"
                    class="am-fcis__include-info__inner"
                  >
                    <span class="am-icon-user"></span>
                    <span>1/1</span>
                  </div>
                  <div
                    v-if="customizedOptions.packageLocation.visibility && packageLocations.length"
                    class="am-fcis__include-info__inner"
                  >
                    <span class="am-icon-locations"></span>
                    <span>
                      {{ packageLocations.length === 1 ? (packageLocations[0].address ? packageLocations[0].address : packageLocations[0].name) : amLabels.multiple_locations }}
                    </span>
                  </div>
                  <div
                    v-if="customizedOptions.packageServices.visibility"
                    class="am-fcis__include-info__inner am-fcis__include-info__services"
                  >
                    <span>
                      {{ `${amLabels.in_package}:` }}
                    </span>
                    <span v-for="obj in pack.bookable" :key="obj.id">
                      {{obj.service.name}}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /Available Package in Service -->

        <AmDialog
          v-model="openBooking"
          :append-to-body="true"
          :modal-class="'amelia-v2-booking amelia-v2-booking-dialog'"
          :destroy-on-close="true"
          :center="true"
          :lock-scroll="false"
          :close-on-click-modal="false"
          :close-on-press-escape="false"
          :width="bookingDialogWidth"
          @closed="onDialogClosing"
        >
          <CategoryBooking
            :show-cart="showCart"
            @add-to-cart="addToCart"
            @emptied-cart="openBooking = false"
          >
          </CategoryBooking>
        </AmDialog>
      </template>
    </Content>
    <div
      v-if="empty"
      ref="ameliaContainer"
      class="am-empty"
    >
      <img
        :src="baseUrls.wpAmeliaPluginURL+'/v3/src/assets/img/am-empty-booking.svg'"
        :alt="preselected.show !== 'packages' ? amLabels.no_services_employees : amLabels.no_package_services"
      >
      <div class="am-empty__heading">
        {{ amLabels.oops }}
      </div>
      <div class="am-empty__subheading">
        {{ preselected.show !== 'packages' ? amLabels.no_services_employees : amLabels.no_package_services }}
      </div>
      <div class="am-empty__text">
        <span v-if="preselected.show !== 'packages'">
          {{ amLabels.add_services_employees }}&nbsp;
        </span>
        <a href="https://wpamelia.com/services-and-categories/" rel="nofollow">
          {{ amLabels.add_services_url }}&nbsp;
        </a>
        <span v-if="preselected.show !== 'packages'">
          {{ amLabels.and }}&nbsp;
        </span>
        <a
          v-if="preselected.show !== 'packages'"
          href="https://wpamelia.com/employees/"
          rel="nofollow"
        >
          {{ amLabels.add_employees_url }}
        </a>
      </div>
    </div>
  </template>
</template>

<script setup>

// * Components
import Content from '../../../common/CatalogFormConstruction/Content/Content.vue'
import Header from '../../../common/CatalogFormConstruction/Header/Header.vue'
import AmButton from '../../../_components/button/AmButton.vue'
import AmDialog from '../../../_components/dialog/AmDialog.vue'
import CategoryBooking from '../CategoryBooking/CategoryBooking.vue'
import AmImagePlaceholder from '../../../_components/image-placeholder/AmImagePlaceholder.vue'
import AmCollapse from '../../../_components/collapse/AmCollapse.vue'
import AmCollapseItem from '../../../_components/collapse/AmCollapseItem.vue'

// * Vue
import {
  ref,
  inject,
  nextTick,
  computed,
  onMounted,
  reactive,
  provide,
  watch
} from "vue";

// * Vuex
import { useStore } from 'vuex'

// * Composables
import {
  useEmployeesServiceCapacity,
  useServiceDuration,
  useServiceLocation,
  useServicePrice
} from '../../../../assets/js/public/catalog.js'
import { useFormattedPrice } from '../../../../assets/js/common/formatting.js'
import { useColorTransparency } from '../../../../assets/js/common/colorManipulation.js'
import {
  usePackageAmount,
  useBuildPackage,
} from '../../../../assets/js/public/package.js'
import { useDescriptionVisibility } from "../../../../assets/js/common/helper";
import { useFillAppointments } from "../../../../assets/js/common/appointments";
import {
  useResetCart,
  useInitCartItem,
  useInitSelection,
  useRemoveLastCartItem,
  useCartHasItems,
  useCartStep
} from '../../../../assets/js/public/cart'
import { useTaxVisibility } from "../../../../assets/js/common/pricing";

// * Plugin Licence
let licence = inject('licence')

// * Global functions
let {
  previousPage
} = inject('changingPageFunctions', {
  previousPage: () => {
  }
})

let ready = computed(() => {
  return store.getters['entities/getReady']
})

// * Root Urls
const baseUrls = inject('baseUrls')

// * Page Width and Reference
let contentRef = ref()
let pageWidth = inject('containerWidth')

// * Root Settings
const amSettings = inject('settings')

// * Fonts
let amFonts = inject('amFonts')

// * Labels
const labels = inject('labels')

// * Local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  if (amSettings.customizedData && amSettings.customizedData.cbf && amSettings.customizedData.cbf.categoryService.translations) {
    let customizedLabels = amSettings.customizedData.cbf.categoryService.translations
    Object.keys(customizedLabels).forEach(labelKey => {
      if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
        computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
})

let activeImage = ref(0)

// * Store
let store = useStore()

// * Entities
let amEntities = inject('amEntities')

// * Cart button
let cartBtnVisibility = computed(() => useCartStep(store) && useCartHasItems(store) > 0)
let cartItemsNumber = computed(() => useCartHasItems(store))

// * Customized data
let customizedDataForm = inject('customizedDataForm')

// * Customized options
let customizedOptions = computed(() => {
  return customizedDataForm.value.categoryService.options
})

let scrollVisibility = computed(() => {
  if (!('pageScroll' in customizedOptions.value)) return true

  return customizedOptions.value.pageScroll.visibility
})

// * Booking Type (appointment, booking)
let itemType = inject('itemType')

// * Selected category
let categorySelected = inject('categorySelected')
let category = computed(() => {
  return amEntities.value.categories.find(item => item.id === categorySelected.value)
})

// * Selected service
let service = computed(() => {
  let serviceObj = amEntities.value.services.find(item => item.id === store.getters['booking/getServiceId'])
  return serviceObj ? serviceObj : {}
})

function displayServiceLocationLabel (entities, id) {
  let locations = useServiceLocation(entities, id)
  if (locations.length === 1 || (locations.length && locations.every(location => location.id === locations[0].id))) {
    return locations[0].address ? locations[0].address : locations[0].name
  }
  return amLabels.value.multiple_locations
}

let tabsActive = ref('description')

let serviceEmployees = computed(() => {
  let arr = []
  let employeesIds = Object.keys(amEntities.value.entitiesRelations)

  employeesIds.forEach((employeeId) => {
    if (amEntities.value.entitiesRelations[employeeId][service.value.id]
      && amEntities.value.employees.find(a => a.id === parseInt(employeeId))
    ) {
      arr.push(amEntities.value.employees.find(a => a.id === parseInt(employeeId)))
    }
  })

  return arr
})

function serviceEmployeePrice(employee) {
  let servicePrice = employee.serviceList.find(a => a.id === service.value.id).price
  if (servicePrice - service.value.price !== 0) return `${servicePrice - service.value.price > 0 ? (service.value.price > 0 ? '+' : '') : '-'} ${useFormattedPrice(servicePrice - service.value.price)}`
  return ''
}

onMounted(() => {
  if (!customizedOptions.value.serviceDescription.visibility) tabsActive.value = 'employees'
  if (!useDescriptionVisibility(service.value.description)) tabsActive.value = 'employees'
})

let serviceThumbsGallery = computed(() => {
  let thumbs = service.value.gallery.length ? JSON.parse(JSON.stringify(service.value.gallery)) : []
  if (service.value.gallery.length === 1) return []
  thumbs.shift()
  if (thumbs.length > 2) thumbs.splice(2, thumbs.length - 2)
  return thumbs
})

let servicePackages = computed(() => {
  let arr = []
  amEntities.value.packages.forEach(pack => {
    if (
      pack.bookable.filter(a => a.service.id === service.value.id).length &&
      !arr.filter(b => b.id === pack.id).length &&
      pack.available &&
      pack.status === 'visible'
    ) {
      arr.push(pack)
    }
  })

  return arr
})

let displayAllPackages = ref(false)
let displayServicePackages = computed(() => {
  let arr = [...servicePackages.value]
  if (!displayAllPackages.value) return arr.slice(0, 2)
  return arr
})

let packageLocations = computed(() => store.getters['entities/filteredLocations'](store.getters['booking/getSelection']))

function goBack() {
  itemType.value = ''

  nextTick(() => {
    previousPage()
  })
}

let showCart = ref(false)

function goToCart () {
  store.commit('booking/setShownCart', false)

  useRemoveLastCartItem(store)

  showCart.value = true

  openBooking.value = true
}

function addToCart() {
  openBooking.value = false

  if (store.getters['entities/getPreselected'].service.length > 1) {
    goBack()
  }
}

// * Booking Dialog
let openBooking = ref(false)

let bookingDialogWidth = ref('760px')
provide('bookingDialogWidth', bookingDialogWidth)

function bookNow() {
  let preselect = store.getters['entities/getPreselected']
  if (serviceEmployees.value.length === 1) {
    store.commit('booking/setEmployeeId', parseInt(serviceEmployees.value[0].id))
    preselect.employee = [parseInt(serviceEmployees.value[0].id)]
    store.commit('entities/setPreselected', preselect)
  }

  showCart.value = false

  openBooking.value = false

  useResetCart(store)

  useInitSelection(store, false)

  useInitCartItem(store)

  let serviceLocations = useServiceLocation(amEntities.value, service.value.id);
  if (serviceLocations.length === 1) {
    store.commit('booking/setLocationId', parseInt(serviceLocations[0].id))
    preselect.location = [parseInt(serviceLocations[0].id)]
    store.commit('entities/setPreselected', preselect)
  }

  store.commit('booking/setBookableType', 'appointment')
  openBooking.value = true
}

let restoreFormData = inject('restoreFormData')

if (restoreFormData.value) {
  bookNow()
}

function selectServicePackage(pack) {
  store.commit('booking/setPackageId', pack.id)
  store.commit('booking/setBookableType', 'package')
  store.commit('booking/setMultipleAppointments', useBuildPackage(0, pack))
  store.commit('booking/setMultipleAppointmentsIndex', 0)

  if (serviceEmployees.value.length === 1) {
    store.commit('booking/setEmployeeId', parseInt(serviceEmployees.value[0].id))
  }

  if (useServiceLocation(amEntities.value, service.value.id).length === 1) {
    store.commit('booking/setLocationId', parseInt(useServiceLocation(amEntities.value, service.value.id)[0].id))
  }

  nextTick(() => {
    openBooking.value = true
  })
}

// * Shortcode
const preselected = computed(() => store.getters['entities/getPreselected'])

let backBtnVisibility = ref(amEntities.value.services.length !== 1 && preselected.value.service.length !== 1)

let stepName = ref('')
provide('stepName', stepName)

function onFinishedBooking () {
  let entity = store.getters['entities/getBookableFromBookableEntities'](
    store.getters['booking/getSelection']
  )

  let entitySettings = entity.settings ? JSON.parse(entity.settings) : amSettings

  if ('general' in entitySettings && 'redirectUrlAfterAppointment' in entitySettings.general && entitySettings.general.redirectUrlAfterAppointment) {
    window.location.href = entitySettings.general.redirectUrlAfterAppointment
  } else if (amSettings.general.redirectUrlAfterAppointment) {
    window.location.href = amSettings.general.redirectUrlAfterAppointment
  } else {
    window.location.reload()
  }
}
function onDialogClosing() {
  if (stepName.value && stepName.value === 'CongratulationsStep') {
    onFinishedBooking()
  } else {
    if (stepName.value && stepName.value === 'DateTimeStep') {
      let bookingFailed = useFillAppointments(store)

      if (bookingFailed) {
        console.log(bookingFailed)
      }
    }

    // * Reset Employee selection
    store.commit('booking/setEmployeeId', null)

    // * Reset Location selection
    store.commit('booking/setLocationId', null)

    store.commit('entities/setPreselected', store.getters['entities/getOriginalPreselected'])
  }

  bookingDialogWidth.value = '760px'
}

let galleryDialog = ref(false)

function durationTypeLabel(duration, type) {
  let string = ''
  if (duration > 1) {
    if (type === 'day') string = amLabels.value.expires_days
    if (type === 'week') string = amLabels.value.expires_weeks
    if (type === 'month') string = amLabels.value.expires_months
  } else {
    if (type === 'day') string = amLabels.value.expires_day
    if (type === 'week') string = amLabels.value.expires_week
    if (type === 'month') string = amLabels.value.expires_month
  }
  return string
}

// * Empty state
let empty = computed(() => {
  return Object.keys(service.value).length === 0 || serviceEmployees.value.length === 0
})

// * Appointment Waiting List: populate module on service selection
watch(() => store.getters['booking/getServiceId'], (newServiceId) => {
  // Reset first
  store.dispatch('appointmentWaitingListOptions/resetWaitingOptions')

  if (!newServiceId) return

  let globalEnabled = !!(amSettings.featuresIntegrations?.waitingListAppointments?.enabled)
  if (!globalEnabled) return

  let service = store.getters['entities/getService'](newServiceId)
  if (!service || !service.settings) return

  let serviceSettings = null
  try {
    serviceSettings = JSON.parse(service.settings)
  } catch (e) {
    serviceSettings = null
  }
  if (!serviceSettings || !serviceSettings.waitingList || !serviceSettings.waitingList.enabled) return

  const wl = serviceSettings.waitingList
  // Backend may not yet supply peopleWaiting for appointments; default 0
  let waitingPayload = {
    enabled: !!wl.enabled,
    maxCapacity: wl.maxCapacity || 0,
    maxExtraPeople: wl.maxExtraPeople || 0,
    maxExtraPeopleEnabled: !!wl.maxExtraPeopleEnabled,
    peopleWaiting: wl.peopleWaiting || 0,
    isWaitingListSlot: false
  }
  store.commit('appointmentWaitingListOptions/setAllData', waitingPayload)
}, { immediate: true })

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-fcis-success-op20': useColorTransparency(amColors.value.colorSuccess, 0.20),
    '--am-c-fcis-primary-op20': useColorTransparency(amColors.value.colorPrimary, 0.20),
    '--am-c-fcis-text-op80': useColorTransparency(amColors.value.colorMainText, 0.80),
    '--am-c-fcis-text-op03': useColorTransparency(amColors.value.colorMainText, 0.03),
    '--am-c-fcis-btn-op50': useColorTransparency(amColors.value.colorBtnSec, 0.5),
  }
})

let cssGalleryDialogVars = computed(() => {
  return {
    '--am-c-fcis-bgr': amColors.value.colorMainBgr,
    '--am-c-fcis-text': amColors.value.colorMainText,
    '--am-c-fcis-success': amColors.value.colorSuccess,
    '--am-c-fcis-primary': amColors.value.colorPrimary,
    '--am-c-scroll-op30': useColorTransparency(amColors.value.colorPrimary, 0.3),
    '--am-c-scroll-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),
    '--am-font-family': amFonts.fontFamily,
  }
})
</script>

<script>
export default {
  name: "CategoryService"
}
</script>

<style lang="scss">
@import '../../../../../src/assets/scss/common/quill/_quill-mixin.scss';

.amelia-v2-booking {
  // am-    amelia
  // -c-    color
  // -fcis-  form category item service
  // -bgr   background
  #amelia-container {
    .am-fcis {
      --am-c-fcis-header-text: var(--am-c-main-heading-text);
      --am-c-fcis-bgr: var(--am-c-main-bgr);
      --am-c-fcis-text: var(--am-c-main-text);
      --am-c-fcis-success: var(--am-c-success);
      --am-c-fcis-primary: var(--am-c-primary);
      width: 100%;
      padding: 24px;
      border-radius: 10px;

      &__form {
        padding-top: 24px;
      }

      &__header {
        &-top {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 0 0 16px;

          &.am-tablet {
            flex-wrap: wrap;

            .am-fcis__header-text {
              width: 100%;
              justify-content: space-between;
              margin-bottom: 12px;
            }

            .am-fcis__header-action {
              width: 100%;
              justify-content: space-between;
              flex: unset;
            }

            &.am-mobile {
              .am-fcis__header {
                &-text {
                  flex-wrap: wrap;
                }

                &-name {
                  width: 100%;
                }

                &-action {
                  flex-wrap: wrap;
                }

                &-price {
                  margin-bottom: 12px;
                }

                &-tax {
                  margin-bottom: 12px;
                }

                &-btn {
                  width: 100%;
                  .am-button {
                    width: 100%;
                  }
                }
              }

              .am-fcis__badge {
                margin-left: 0;
              }
            }
          }
        }

        &-name {
          display: inline-flex;
          font-size: 28px;
          font-weight: 500;
          color: var(--am-c-fcis-header-text);
        }

        &-action {
          display: flex;
          align-items: center;
          justify-content: flex-end;
          flex: 0 0 auto;
        }

        &-price {
          font-size: 18px;
          font-weight: 500;
          margin-right: 20px;
          color: var(--am-c-fcis-primary);
        }

        &-tax {
          display: inline-flex;
          flex: 0 0 auto;
          align-items: center;
          height: 28px;
          font-size: 18px;
          font-weight: 500;
          line-height: 1;
          border-radius: 14px;
          padding: 0 8px;
          margin-right: 10px;
          background-color: var(--am-c-fcis-primary-op20);
          color: var(--am-c-fcis-primary)
        }

        &-bottom {
          padding: 0 0 16px;
        }
      }

      &__content {
        &.no-scroll {
          max-height: unset;
          overflow-x: unset;
        }
      }

      &__badge {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        height: 28px;
        padding: 0 12px 0 8px;
        border-radius: 14px;
        margin-left: 10px;

        &.am-service {
          background-color: var(--am-c-fcis-success-op20);
          span {
            color:  var(--am-c-fcis-success);
          }
        }

        span {
          display: block;
          font-size: 18px;
          font-weight: 400;
          line-height: 1;

          &[class*="am-icon"] {
            font-size: 24px;
          }
        }
      }

      &__mini-info {
        display: flex;
        flex-wrap: wrap;
        align-items: center;

        &__inner {
          display: inline-flex;
          align-items: center;
          max-width: 100%;
          padding: 0 8px 0 0;
          margin: 0 0 8px;

          &:last-child {
            padding: 0;
          }

          span {
            font-size: 18px;
            font-weight: 400;
            color: var(--am-c-fcis-text-op80);
            line-height: 1.2;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;

            &[class*="am-icon"] {
              flex: 0 0 auto;
              font-size: 24px;
              color: var(--am-c-fcis-primary);
            }
          }
        }
      }

      &__gallery {
        display: flex;
        position: relative;
        transition: all 0.3s ease-in-out;
        margin: 0 0 32px;
        padding: 0 12px;

        &-hero {
          width: calc(100% - 264px);
          border-top-left-radius: 8px;
          border-bottom-left-radius: 8px;
          margin: 0;
          background-size: cover;
          background-repeat: no-repeat;
          background-position: center;
          transition: all 0.3s ease-in-out;
          overflow: hidden;

          &.w100 {
            width: 100%;
            padding-top: 25%;
            border-radius: 8px;
            margin-bottom: 0;

            &.am-mobile {
              padding-top: 150px;
            }
          }
        }

        &-thumb {
          position: relative;
          display: inline-block;
          width: calc(100% - 16px);
          padding-top: calc(50% - 16px);
          margin: 0 0 16px 16px;
          background-size: cover;
          background-repeat: no-repeat;
          background-position: center;
          overflow: hidden;
          //cursor: pointer;
          float: left;

          &:first-child {
            border-top-right-radius: 8px;
          }

          &:last-of-type {
            border-bottom-right-radius: 8px;
            margin-bottom: 0;
          }

          &__wrapper {
            width: 100%;
            max-width: 264px;
            position: relative;

            &:after, &:before {
              content: '';
              display: block;
              clear: both;
            }
          }

          &.am-one-thumb {
            padding-top: calc(100% - 16px);
          }
        }

        &-btn {
          position: absolute;
          bottom: 12px;
          right: 24px;
          display: flex;
          align-items: center;
          justify-content: center;
          width: calc(264px - 40px);

          &.am-mobile {
            width: calc(100% - 48px);
          }

          &.am-button.am-button--filled {
            --am-c-btn-bgr: var(--am-c-btn-second);
            --am-c-btn-text: var(--am-c-btn-first);
            --am-c-btn-border: var(--am-c-fcis-btn-op50);

            &:not(.is-disabled) {
              &:hover {
                --am-c-btn-bgr: var(--am-c-btn-second);
                --am-c-btn-text: var(--am-c-btn-first);
                --am-c-btn-border: var(--am-c-fcis-btn-op50);
              }
            }
          }

          .am-icon-gallery {
            font-size: 24px;
            margin-right: 4px;
          }
        }
      }

      &__info {
        padding: 0 12px;
        margin: 0 0 32px;

        &-tab {
          font-size: 14px;
          font-weight: 500;
          color: var(--am-c-fcis-text);
          padding: 12px 16px;
          cursor: pointer;

          &:hover {
            color: var(--am-c-fcis-primary);
          }

          &.am-active {
            color: var(--am-c-fcis-primary);
            border-bottom: 3px solid var(--am-c-fcis-primary);
            padding-bottom: 9px;
          }

          &__wrapper {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            position: relative;
          }
        }

        &-content {
          padding: 24px 0;

          .am-collapse-item {

            $count: 100;
            @for $i from 0 through $count {
              &:nth-child(#{$i + 1}) {
                animation: 600ms cubic-bezier(.45,1,.4,1.2) #{$i*100}ms am-animation-slide-up;
                animation-fill-mode: both;
              }
            }

            &__heading {
              padding: 12px;
              transition-delay: .5s;

              &-side {
                transition-delay: 0s;
              }
            }
          }
        }

        &-service {
          &__desc {
            font-size: 15px;
            line-height: 2;
            word-break: break-word;
            white-space: break-spaces;
            color: var(--am-c-fcis-text-op80);

            * {
              font-size: 15px;
              line-height: 2;
              word-break: break-word;
              white-space: break-spaces;
              color: var(--am-c-fcis-text-op80);
            }

            a {
              color: var(--am-c-fcis-primary);
            }

            &.ql-description {
              @include quill-styles;
            }
          }
        }

        &-employee {
          width: 100%;
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          justify-content: space-between;

          &:last-child {
            margin: 0;
          }

          &__hero {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
          }

          &__img {
            width: 54px;
            height: 54px;
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            background-position: center;
            background-size: cover;
            border-radius: 4px;
            border: 1px solid var(--am-c-inp-border);
            color: var(--am-c-fcis-bgr);
            font-size: 18px;
            font-weight: 500;
          }

          &__heading {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
          }

          &__name {
            font-size: 15px;
            font-weight: 500;
            color: var(--am-c-fcis-text);
            margin: 0 0 0 12px;
          }

          &__badge {
            color: #FFF;
            border-radius: 4px;
            padding: 0 4px;
            font-size: 13px;
            line-height: 19px;
          }

          &__price {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 24px;
            font-size: 14px;
            font-weight: 500;
            line-height: 1;
            color: var(--am-c-fcis-primary);
            background-color: var(--am-c-fcis-primary-op20);
            padding: 0 8px;
            border-radius: 12px;
            margin: 0 0 0 12px;
          }

          &__description {
            color: var(--am-c-fcis-text);

            &.ql-description {
              @include quill-styles;
            }
          }
        }
      }

      &__include {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        border-radius: 6px;
        border: 1px solid var(--am-c-inp-border);
        background-color: var(--am-c-fcis-bgr);
        padding: 12px;
        margin: 12px 0 0;
        cursor: pointer;

        &-wrapper {
          padding: 12px;
          background-color: var(--am-c-fcis-text-op03);
          border-radius: 8px;
        }

        &-heading {
          display: flex;
          align-items: center;
          justify-content: space-between;

          &__text {
            font-size: 18px;
            font-weight: 500;
            line-height: 1.55555;
            color: var(--am-c-fcis-text);
          }

          &__btn {
            font-size: 15px;
            font-weight: 500;
            color: var(--am-c-fcis-primary);
            cursor: pointer;
            padding: 4px 10px;
            border-radius: 4px;

            &:hover {
              transition: background-color 0.3s ease-in-out;
              background-color: var(--am-c-fcis-primary-op20);
            }
          }
        }

        &-hero {
          display: flex;
          align-items: flex-start;
          justify-content: flex-start;
          width: 100%;
        }

        &-img {
          width: 76px;
          height: 76px;
          display: flex;
          flex: 0 0 auto;
          align-items: center;
          justify-content: center;
          background-position: center;
          background-size: cover;
          border-radius: 4px;
          border: 1px solid var(--am-c-inp-border);
          color: var(--am-c-fcis-bgr);
          font-size: 28px;
        }

        &-text {
          width: 100%;
          margin: 0 0 0 12px;

          &.am-mobile {
            margin: 0;
          }
        }

        &-header {
          display: flex;
          align-items: center;
          justify-content: space-between;
          width: 100%;

          &.am-mobile {
            flex-wrap: wrap;
          }
        }

        &-name {
          font-size: 15px;
          font-weight: 500;
          line-height: 1.6;
          color: var(--am-c-fcis-text);
          margin: 0 0 4px;

          &.am-mobile {
            width: 100%;
          }
        }

        &-cost {
          display: flex;
          align-items: center;
          justify-content: space-between;
          flex: 0 0 auto;

          & > span {
            display: inline-flex;
            height: 24px;
            font-size: 14px;
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 24px;
            margin-right: 8px;
            flex: 0 1 auto;

            &:last-child {
              margin-right: 0;
            }
          }
        }

        &-price {
          color: var(--am-c-fcis-primary);
          background-color: var(--am-c-fcis-primary-op20);
        }

        &-discount {
          color: var(--am-c-fcis-success);
          background-color: var(--am-c-fcis-success-op20);
        }

        &-info {
          display: flex;
          flex-wrap: wrap;
          align-items: center;

          &__inner {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            max-width: 100%;
            height: auto;
            line-height: 20px;
            padding: 0 8px 0 0;
            margin: 0 0 8px;

            &:last-child {
              padding: 0;
            }

            span {
              font-size: 13px;
              font-weight: 400;
              line-height: unset;
              color: var(--am-c-fcis-text-op80);
              flex-wrap: wrap;

              &[class*="am-icon"] {
                flex: 0 0 auto;
                font-size: 24px;
                color: var(--am-c-fcis-primary);
              }
            }
          }

          &__services {
            width: 100%;
            height: auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            margin: 0;

            span {
              position: relative;
              display: inline-flex;
              flex: 0 1 auto;
              font-size: 13px;
              font-weight: 400;
              line-height: 1.384615;
              word-break: break-word;
              color: var(--am-c-fcis-text-op80);
              padding: 0 0 0 8px;

              &::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 2px;
                transform: translateY(-50%);
                border: 2px solid var(--am-c-fcis-text-op80);
                border-radius: 50%;
              }

              &:first-child, &:nth-child(2) {
                padding-right: 2px;

                &::after {
                  display: none;
                }
              }
            }
          }
        }
      }
    }
  }

  &.amelia-v2-booking-dialog {
    --am-c-fcis-header-text: var(--am-c-main-heading-text);
    --am-c-fcis-bgr: var(--am-c-main-bgr);
    --am-c-fcis-text: var(--am-c-main-text);
    --am-c-fcis-success: var(--am-c-success);
    --am-c-fcis-primary: var(--am-c-primary);
    z-index: 99999999 !important;

    .el-overlay-dialog {
      background-color: rgba(26, 44, 55, 0.5);
    }

    .el-dialog {
      max-width: var(--el-dialog-width, 50%);
      width: 100%;
      border-radius: 8px;
      background-color: transparent;

      &__header {
        padding: 0;
      }

      &__headerbtn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 19px;
        height: 19px;
        background-color: var(--am-c-fcis-bgr);
        color: var(--am-c-fcis-text);
        border-radius: 50%;
        z-index: 1000000;

        .el-dialog__close {
          line-height: 1;
        }
      }

      &__body {
        padding: 0;
        word-break: break-word;
      }

      &__footer {
        display: none;
      }
    }
  }

  // - sgd - service gallery dialog
  &.amelia-v2-sgd {
    z-index: 9999999 !important;

    .el-overlay-dialog {
      background-color: rgba(26, 44, 55, 0.5);
    }

    .el-dialog {
      max-width: var(--el-dialog-width);
      width: 100%;
      border-radius: 8px;
      background-color: transparent;
      margin-top: var(--el-dialog-margin-top);

      &__header {
        padding: 0;
      }

      &__headerbtn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 19px;
        height: 19px;
        background-color: var(--am-c-fcis-bgr);
        color: var(--am-c-fcis-text);
        border-radius: 50%;
        z-index: 1000000;

        .el-dialog__close {
          line-height: 1;
        }
      }

      &__body {
        padding: 0;
        word-break: break-word;
        background-color: transparent;
      }

      &__footer {
        display: none;
      }
    }

    .am-gd {
      background-color: var(--am-c-fcis-bgr);
      border-radius: 8px;
      padding: 0 0 8px;

      * {
        font-family: var(--am-font-family);
      }

      &__display {
        display: flex;
        align-items: center;
        justify-content: center;

        img {
          width: 100%;
          border-radius: 8px;
        }

        &-wrapper {
          position: relative;
          padding: 24px 60px 12px;
        }
      }

      &__arrows {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 100;

        span {
          position: absolute;
          width: 60px;
          height: 100%;
          font-size: 38px;
          color: var(--am-c-fcis-text);
          cursor: pointer;

          &::before {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
          }

          &:first-child {
            left: 0;
          }

          &:last-child {
            right: 0;
          }
        }
      }

      &__selection {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        line-height: 1.42857;
        color: var(--am-c-fcis-text);
      }

      &__thumb {
        position: relative;
        width: 100px;
        height: 100px;
        flex: 0 0 auto;
        cursor: pointer;
        margin: 0 8px 8px 0;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;

        &:last-child {
          margin-right: 0;
        }

        &.am-active {
          &:before {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 100%;
            height: 4px;
            background-color: var(--am-c-fcis-primary);
          }
        }

        &-wrapper {
          display: flex;
          max-width: calc(100% - 48px);
          width: calc(100% - 48px);
          white-space: nowrap;
          margin: 0 24px;
          overflow-y: hidden;
          padding-bottom: 8px;

          // Main Scroll styles
          &::-webkit-scrollbar {
            height: 6px;
          }

          &::-webkit-scrollbar-thumb {
            border-radius: 6px;
            background: var(--am-c-scroll-op30);
          }

          &::-webkit-scrollbar-track {
            border-radius: 6px;
            background: var(--am-c-scroll-op10);
          }
        }
      }
    }
  }
}
</style>
