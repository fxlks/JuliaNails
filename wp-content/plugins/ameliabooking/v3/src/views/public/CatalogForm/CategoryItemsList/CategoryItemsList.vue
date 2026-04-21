<template>
  <Content
    v-if="!empty"
    ref="contentRef"
    :wrapper-class="`am-fcil ${pageWidth < 481 ? 'am-mobile' : ''}`"
    :form-class="`am-fcil__main ${pageWidth < 481 ? 'am-mobile' : ''}`"
    :content-class="`am-fcil__wrapper ${pageWidth < 481 ? 'am-mobile' : ''} ${scrollVisibility ? '' : 'no-scroll'}`"
    :style="cssVars"
  >
    <template #header>
      <span class="am-fcil__filter-buttons">
        <Header
          v-if="preselectedCategories !== 1 && !shortcodeData.categories_hidden && availableCategories.length !== 1"
          :btn-size="filterWidth < 481 ? 'medium' : 'mini'"
          :btn-string="amLabels.back_btn"
          :btn-type="customizedOptions.backBtn.buttonType"
          :cart-btn-visibility="cartBtnVisibility"
          :cart-items-number="cartItemsNumber"
          @go-back="goBack"
          @go-to-cart="goToCart"
        ></Header>
        <AmButton
          v-if="filterWidth < 481"
          size="medium"
          category="secondary"
          :type="customizedOptions.filterMenuBtn.buttonType"
          custom-class="am-fcil__filter-buttons__menu"
          :icon-only="true"
          :icon="iconSearchMenu"
          @click="() => filterMobileMenu = !filterMobileMenu"
        ></AmButton>
      </span>
      <div class="am-fcil__filter">
        <div
          v-if="customizedOptions.searchInput.visibility"
          role="search"
          :aria-label="amLabels.filter_input"
          class="am-fcil__filter-item"
          :class="filterClassWidth.search"
        >
          <AmInput
            v-model="searchFilter"
            :placeholder="amLabels.filter_input"
            :prefix-icon="iconSearch"
          />
        </div>
        <Transition name="slide-fade">
          <div
            v-if="shortcodeData.employee.length !== 1 && amEntities.employees.length > 1 && customizedOptions.filterEmployee.visibility && filterMobileMenu && !licence.isLite"
            class="am-fcil__filter-item"
            role="search"
            :aria-label="amLabels.filter_employee"
            :class="filterClassWidth.employee"
          >
            <AmSelect
              v-model="employeeFilter"
              clearable
              filterable
              :placeholder="amLabels.filter_employee"
              :aria-label="amLabels.filter_employee"
              :filter-method="filterEmployee"
              :fit-input-width="true"
            >
              <AmOption
                v-for="employee in filteredEmployees"
                :key="employee.id"
                :value="employee.id"
                :label="`${employee.firstName} ${employee.lastName}`"
              >
              </AmOption>
            </AmSelect>
          </div>
        </Transition>
        <Transition name="slide-fade">
          <div
            v-if="shortcodeData.location.length !== 1 && amEntities.locations.length > 1 && customizedOptions.filterLocation.visibility && filterMobileMenu && !licence.isLite && !licence.isStarter"
            class="am-fcil__filter-item"
            role="search"
            :aria-label="amLabels.filter_location"
            :class="filterClassWidth.location"
          >
            <AmSelect
              v-model="locationFilter"
              clearable
              filterable
              :placeholder="amLabels.filter_location"
              :aria-label="amLabels.filter_location"
              :fit-input-width="true"
              :filter-method="filterLocation"
            >
              <AmOption
                v-for="location in filteredLocations"
                :key="location.id"
                :value="location.id"
                :label="location.name"
              >
              </AmOption>
            </AmSelect>
          </div>
        </Transition>
        <Transition name="slide-fade">
          <div
            v-if="availableCategories.length !== 1 && preselectedCategories !== 1 && customizedOptions.sidebar.visibility && !sideMenuVisibility && filterMobileMenu"
            class="am-fcil__filter-item am-w100"
            :class="filterClassWidth.category"
          >
            <AmSelect
              v-model="categorySelected"
              :clearable="false"
              :filterable="false"
              :placeholder="''"
              :fit-input-width="true"
            >
              <AmOption
                v-for="cat in categoriesMenu"
                :key="cat.id"
                :value="cat.id"
                :label="cat.name"
              >
              </AmOption>
            </AmSelect>
          </div>
        </Transition>
        <div
          v-if="!preselected.show && customizedOptions.filterButtons.visibility && categoryPackages.length !== 0 && categoryServices.length !== 0 && !useCartHasItems(store)"
          role="search"
          aria-label="Filter Buttons"
          class="am-fcil__filter-item"
          :class="filterClassWidth.buttons"
        >
          <div class="am-fcil__filter-item__btn-wrapper">
            <div
              class="am-fcil__filter-item__btn"
              :class="{'am-active': displayCategoryPackages && displayCategoryServices}"
              @click="changeCategoryItemsVisibility('all')"
            >
              <span>
                {{ amLabels.filter_all }}
              </span>
            </div>
            <div
              class="am-fcil__filter-item__btn"
              :class="{'am-active': displayCategoryPackages && !displayCategoryServices}"
              @click="changeCategoryItemsVisibility('packages')"
            >
              <span>
                {{ amLabels.filter_packages }}
              </span>
            </div>
            <div
              class="am-fcil__filter-item__btn"
              :class="{'am-active': !displayCategoryPackages && displayCategoryServices}"
              @click="changeCategoryItemsVisibility('services')"
            >
              <span>
                {{ amLabels.filter_services }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template v-if="availableCategories.length !== 1 && preselectedCategories !== 1 && customizedOptions.sidebar.visibility && sideMenuVisibility" #side>
      <SideMenu
        :menu-items="categoriesMenu"
        :init-selection="categorySelected"
        identifier="id"
        name-identifier="name"
        :footer-string="amLabels.get_in_touch"
        :company-email="amSettings.company.email"
        @click="selectCategory"
      ></SideMenu>
    </template>
    <template #heading>
      <div
        role="heading"
        aria-level="2"
        class="am-fcil__heading"
      >
        {{ headingStringRender }}
      </div>
    </template>
    <template #content>
      <!-- Packages -->
      <template v-if="displayCategoryPackages && !useCartHasItems(store)">
        <div
          v-for="item in categoryPackages"
          :key="item.id"
          role="group"
          class="am-fcil__item"
          :class="{'am-mobile': containerWidth < 481}"
        >
          <div
            class="am-fcil__item-inner"
            :class="{'am-mobile': containerWidth < 481}"
          >
            <div
              class="am-fcil__item-content"
              :style="customizedOptions.cardColor && customizedOptions.cardColor.visibility ?
              {backgroundColor: useColorTransparency(item.color, 0.1)} : {}"
            >
              <!-- Card Badge -->
              <div
                v-if="customizedOptions.packageBadge.visibility"
                class="am-fcil__item-badge__wrapper"
              >
                <div class="am-fcil__item-badge am-package">
                  <span class="am-icon-shipment"></span>
                  <span>
                    {{ amLabels.package }}
                  </span>
                </div>
              </div>

              <!-- Card Hero Image -->
              <div v-if="item.pictureFullPath" class="am-fcil__item-hero" :style="{backgroundImage: `url(${item.pictureFullPath})`}"></div>

              <!-- Card Heading -->
              <div class="am-fcil__item-heading">
                <div class="am-fcil__item-name">
                  {{ item.name }}
                </div>
                <div
                  v-if="customizedOptions.packagePrice.visibility"
                  class="am-fcil__item-cost"
                >
                  <span v-if="item.discount" class="am-fcil__item-discount">
                    {{`${amLabels.save} ${item.discount}%`}}
                  </span>
                  <span class="am-fcil__item-price">
                    {{ item.price ? useFormattedPrice(usePackageAmount(item)) : amLabels.free }}
                  </span>
                  <span v-if="item.price && (customizedOptions.tax?.visibility ?? true) && useTaxVisibility(store,item.id, 'package')" class="am-fcil__item-price">
                    <template v-if="amSettings.payments.taxes.excluded">
                      {{ `+${amLabels.total_tax_colon}` }}
                    </template>
                    <template v-else>
                      {{amLabels.incl_tax}}
                    </template>
                  </span>
                </div>
              </div>

              <!-- Card Info -->
              <div class="am-fcil__item-info">
                <div
                  v-if="customizedOptions.packageCategory.visibility && categorySelected"
                  class="am-fcil__item-info__inner"
                >
                  <span class="am-icon-folder"></span>
                  <span>{{ availableCategories.find(a => a.id === categorySelected).name }}</span>
                </div>
                <div
                  v-if="customizedOptions.packageDuration.visibility"
                  class="am-fcil__item-info__inner"
                >
                  <span class="am-icon-clock"></span>
                  <span v-if="item.endDate">
                    {{ `${amLabels.expires_at} ${item.endDate.split(' ')[0]}` }}
                  </span>
                  <span v-else-if="item.durationCount">
                    {{ `${amLabels.expires_after} ${item.durationCount} ${packageDurationLabel(item.durationCount, item.durationType)}` }}
                  </span>
                  <span v-else>
                    {{ amLabels.without_expiration }}
                  </span>
                </div>
                <div
                  v-if="customizedOptions.packageCapacity.visibility"
                  class="am-fcil__item-info__inner"
                >
                  <span class="am-icon-user"></span>
                  <span>1/1</span>
                </div>
                <div
                  v-if="usePackageLocations(amEntities, item, shortcodeData).length && customizedOptions.packageLocation.visibility"
                  class="am-fcil__item-info__inner"
                >
                  <span class="am-icon-locations"></span>
                  <span>
                    {{ displayPackageLocationLabel(amEntities, item) }}
                  </span>
                </div>
              </div>

              <div
                v-if="customizedOptions.packageServices.visibility"
                class="am-fcil__item-services"
              >
                <span>
                  {{ `${amLabels.in_package}:` }}
                </span>
                <span v-for="obj in item.bookable" :key="obj.id">
                  {{obj.service.name}}
                </span>
              </div>
            </div>

            <!-- Card Footer -->
            <div
              class="am-fcil__item-footer"
              :class="[{'am-mobile': containerWidth < 481}, {'am-micro': containerWidth < 320}]"
            >
              <AmButton
                v-if="customizedOptions.cardEmployeeBtn.visibility"
                :class="{'am-w100': containerWidth < 320}"
                size="small"
                :type="customizedOptions.cardEmployeeBtn.buttonType"
                @click="getDialogPackageEmployees(item)"
              >
                {{ amLabels.view_employees }}
              </AmButton>
              <AmButton
                :class="[{'am-w100': !customizedOptions.cardEmployeeBtn.visibility}, {'am-micro am-w100': containerWidth < 320}]"
                size="small"
                :type="customizedOptions.cardContinueBtn.buttonType"
                @click="selectPackage(item)"
              >
                {{ amLabels.continue }}
              </AmButton>
            </div>
          </div>
        </div>
      </template>
      <!-- /Packages -->

      <!-- Services -->
      <template v-if="displayCategoryServices">
        <div
          v-for="item in categoryServices"
          :key="item.id"
          class="am-fcil__item"
          :class="{'am-mobile': containerWidth < 481}"
        >
          <div
            class="am-fcil__item-inner"
            :class="{'am-mobile': containerWidth < 481}"
          >
            <div
              class="am-fcil__item-content"
              :style="customizedOptions.cardColor && customizedOptions.cardColor.visibility ?
              {backgroundColor: useColorTransparency(item.color, 0.1)} : {}"
            >
              <!-- Card Badge -->
              <div
                v-if="customizedOptions.serviceBadge.visibility"
                class="am-fcil__item-badge__wrapper"
              >
                <div class="am-fcil__item-badge am-service">
                  <span class="am-icon-service"></span>
                  <span>
                    {{ amLabels.heading_service }}
                  </span>
                </div>
              </div>
              <!-- /Card Badge -->

              <!-- Card Hero Image -->
              <div v-if="item.pictureFullPath" class="am-fcil__item-hero" :style="{backgroundImage: `url(${item.pictureFullPath})`}"></div>
              <!-- /Card Hero Image -->

              <!-- Card Heading -->
              <div class="am-fcil__item-heading">
                <div class="am-fcil__item-name">
                  {{ item.name }}
                </div>
                <div
                  v-if="customizedOptions.servicePrice.visibility"
                  class="am-fcil__item-cost"
                >
                  <span v-if="useServicePrice(amEntities, item.id).min || useServicePrice(amEntities, item.id).max" class="am-fcil__item-price">
                    {{ useServicePrice(amEntities, item.id).price }}
                  </span>
                  <span v-if="(customizedOptions.tax?.visibility ?? true) && useTaxVisibility(store,item.id, 'service')" class="am-fcil__item-price">
                    <template v-if="amSettings.payments.taxes.excluded">
                      {{ `+${amLabels.total_tax_colon}` }}
                    </template>
                    <template v-else>
                      {{amLabels.incl_tax}}
                    </template>
                  </span>
                </div>
              </div>
              <!-- /Card Heading -->

              <!-- Card Info -->
              <div class="am-fcil__item-info">
                <div
                  v-if="customizedOptions.serviceCategory.visibility && categorySelected"
                  class="am-fcil__item-info__inner"
                >
                  <span class="am-icon-folder"></span>
                  <span>{{ availableCategories.find(a => a.id === categorySelected).name }}</span>
                </div>
                <div
                  v-if="customizedOptions.serviceDuration.visibility"
                  class="am-fcil__item-info__inner"
                >
                  <span class="am-icon-clock"></span>
                  <span>{{ useServiceDuration(item.duration) }}</span>
                </div>
                <div
                  v-if="customizedOptions.serviceCapacity.visibility && !licence.isLite"
                  class="am-fcil__item-info__inner"
                >
                  <span class="am-icon-user"></span>
                  <span>{{ useEmployeesServiceCapacity(amEntities , item.id) }}</span>
                </div>
                <div
                  v-if="useServiceLocation(amEntities, item.id).length && customizedOptions.serviceLocation.visibility"
                  class="am-fcil__item-info__inner"
                >
                  <span class="am-icon-locations"></span>
                  <span>
                    {{ displayServiceLocationLabel(amEntities, item.id) }}
                  </span>
                </div>
              </div>
              <!-- /Card Info -->
            </div>

            <!-- Card Footer -->
            <div
              class="am-fcil__item-footer"
              :class="[{'am-mobile': containerWidth < 481}, {'am-micro': containerWidth < 320}]"
            >
              <AmButton
                v-if="customizedOptions.cardEmployeeBtn.visibility && !licence.isLite"
                :class="{'am-w100': containerWidth < 320}"
                size="small"
                :type="customizedOptions.cardEmployeeBtn.buttonType"
                @click="getDialogServiceEmployees(item.id)"
              >
                {{ amLabels.view_employees }}
              </AmButton>
              <AmButton
                :class="[{'am-w100': !customizedOptions.cardEmployeeBtn.visibility}, {'am-micro am-w100': containerWidth < 320}]"
                size="small"
                :type="customizedOptions.cardContinueBtn.buttonType"
                @click="selectService(item.id)"
              >
                {{ amLabels.continue }}
              </AmButton>
            </div>
            <!-- /Card Footer -->
          </div>
        </div>
      </template>
      <!-- /Services -->

      <!-- Employees Dialog -->
      <AmDialog
        v-model="dialogEmployees"
        :append-to-body="true"
        :modal-class="'am-fcil-employee'"
        :destroy-on-close="true"
        :lock-scroll="true"
        :custom-styles="popupCssVars"
        width="648px"
        @close="closeEmployeeDialog"
      >
        <template #title>
          <div class="am-fcil-employee__header">
            {{ amLabels.employee_info }}
          </div>
        </template>
        <template #default>
          <div>
            <AmCollapse>
              <AmCollapseItem
                v-for="(employee, index) in dialogEmployeesArray"
                :key="index"
                side
              >
                <template #heading>
                  <div class="am-fcil-employee__heading">
                    <div class="am-fcil-employee__heading-left">
                      <AmImagePlaceholder
                        item-class="am-fcil-employee__img"
                        :item-data="employee"
                        :trim-string="2"
                      ></AmImagePlaceholder>
                      <div class="am-fcil-employee__name">
                        {{ `${employee.firstName} ${employee.lastName}` }}
                        <span
                          v-if="employee.badge"
                          :style="{background: employee.badge.color}"
                          class="am-fcil-employee__badge"
                        >
                          {{ employee.badge.content }}
                        </span>
                      </div>
                    </div>
                    <div
                      v-if="dialogEmployeesType === 'service' && employeePrice(employee) !== 0"
                      class="am-fcil-employee__heading-right"
                    >
                      <div class="am-fcil-employee__price">
                        {{ employeePrice(employee) }}
                      </div>
                    </div>
                  </div>
                </template>
                <template #default>
                  <div
                    v-if="useDescriptionVisibility(employee.description)"
                    class="am-fcil-employee__text ql-description"
                    v-html="employee.description"
                  ></div>
                </template>
              </AmCollapseItem>
            </AmCollapse>
          </div>
        </template>
        <template #footer>
          <AmButton
            v-if="customizedOptions.dialogEmployeeBtn.visibility"
            :type="customizedOptions.dialogEmployeeBtn.buttonType"
            category="primary"
            @click="dialogBooking()"
          >
            {{ dialogEmployeesType === 'service' ? amLabels.book_service : amLabels.book_package }}
          </AmButton>
        </template>
      </AmDialog>
      <!-- /Employees Dialog -->

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
        <CategoryBooking :show-cart="showCart"></CategoryBooking>
      </AmDialog>
    </template>
  </Content>
  <div
    v-else
    ref="ameliaContainer"
    class="am-empty"
  >
    <img :src="baseUrls.wpAmeliaPluginURL+'/v3/src/assets/img/am-empty-booking.svg'" alt="Empty">
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

<script setup>
import AmInput from '../../../_components/input/AmInput.vue'
import AmSelect from '../../../_components/select/AmSelect.vue'
import AmOption from '../../../_components/select/AmOption.vue'
import AmButton from '../../../_components/button/AmButton.vue'
import IconComponent from '../../../_components/icons/IconComponent.vue'
import Header from '../../../common/CatalogFormConstruction/Header/Header.vue'
import SideMenu from '../../../common/CatalogFormConstruction/SideMenu/SideMenu.vue'
import Content from '../../../common/CatalogFormConstruction/Content/Content.vue'
import AmDialog from '../../../_components/dialog/AmDialog.vue'
import AmCollapse from '../../../_components/collapse/AmCollapse.vue'
import AmCollapseItem from '../../../_components/collapse/AmCollapseItem.vue'
import AmImagePlaceholder from '../../../_components/image-placeholder/AmImagePlaceholder.vue'
import CategoryBooking from '../CategoryBooking/CategoryBooking.vue'

import {
  inject,
  ref,
  reactive,
  nextTick,
  computed,
  onBeforeMount,
  onMounted,
  provide
} from "vue";

import { useStore } from "vuex";

// * Composables
import {
  useAvailableCategories,
  useEmployeesServiceCapacity,
  useServiceDuration,
  useServicePrice,
  useServiceLocation,
  usePackageEmployees,
  usePackageLocations
} from '../../../../assets/js/public/catalog.js'
import {
  useCartHasItems,
  useRemoveLastCartItem,
  useCartStep
} from '../../../../assets/js/public/cart'
import { useFormattedPrice } from '../../../../assets/js/common/formatting.js'
import { useColorTransparency } from '../../../../assets/js/common/colorManipulation.js'
import {
  useBuildPackage,
  usePackageAmount,
} from "../../../../assets/js/public/package";
import useAction from "../../../../assets/js/public/actions";
import { useDescriptionVisibility } from "../../../../assets/js/common/helper";
import { useTaxVisibility } from "../../../../assets/js/common/pricing";

// * Plugin Licence
let licence = inject('licence')

let {
  nextPage,
  previousPage
} = inject('changingPageFunctions', {
  nextPage: () => {},
  previousPage: () => {}
})

// * Root Urls
const baseUrls = inject('baseUrls')

// * Empty state
let empty = ref(false)

// * Booking Dialog
let openBooking = ref(false)

let bookingDialogWidth = ref('760px')
provide('bookingDialogWidth', bookingDialogWidth)

function onDialogClosing() {
  bookingDialogWidth.value = '760px'
}

let showCart = ref(false)

// * Page Width and Reference
let contentRef = ref()
let pageWidth = inject('containerWidth')

// * Customized data
let customizedDataForm = inject('customizedDataForm')

// * Customized options
let customizedOptions = computed(() => {
  return customizedDataForm.value.categoryItemsList.options
})

let scrollVisibility = computed(() => {
  if (!('pageScroll' in customizedOptions.value)) return true

  return customizedOptions.value.pageScroll.visibility
})

// * Root Settings
const amSettings = inject('settings')

// * Store
let store = useStore()

// * Shortcode
const shortcodeData = inject('shortcodeData')
const preselected = computed(() => store.getters['entities/getPreselected'])

let preselectedCategories = computed (() => {
  let categoryArray = Array.isArray(shortcodeData.value.category) ? shortcodeData.value.category : shortcodeData.value.category.split(',')
  categoryArray = categoryArray.filter(c => c)
  return categoryArray.length
})

// * Sidebar Menu Visibility
let sideMenuVisibility = computed(() => {
  let sidebarByContainer = contentRef.value && contentRef.value.catContainerWidth ? contentRef.value.catContainerWidth > 768 : true
  return preselectedCategories.value !== 1 && customizedOptions.value.sidebar.visibility && sidebarByContainer
})

// * Entities
let amEntities = inject('amEntities')

// * Filtered employees
let queryEmployeeLower = ref('')
function filterEmployee (query) {
  queryEmployeeLower.value = query.toLowerCase()
}
let filteredEmployees = computed(() => {
  if (queryEmployeeLower.value) {
    return amEntities.value.employees.filter(item => {
      const fullName = `${item.firstName} ${item.lastName}`.toLowerCase()
      return fullName.includes(queryEmployeeLower.value)
    })
  }

  return amEntities.value.employees
})

// * Filtered Locations
let queryLocationLower = ref('')
function filterLocation (query) {
  queryLocationLower.value = query.toLowerCase()
}
let filteredLocations = computed(() => {
  if (queryLocationLower.value) {
    return amEntities.value.locations.filter(item => {
      return item.name.toLowerCase().includes(queryLocationLower.value)
    })
  }

  return amEntities.value.locations
})

// * Cart button
let cartBtnVisibility = computed(() => useCartStep(store) && useCartHasItems(store) > 0)
let cartItemsNumber = computed(() => useCartHasItems(store))

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  if (amSettings.customizedData && amSettings.customizedData.cbf && amSettings.customizedData.cbf.categoryItemsList.translations) {
    let customizedLabels = amSettings.customizedData.cbf.categoryItemsList.translations
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

// * FILTERS
let searchFilter = ref('')

let iconSearch = {
  components: {IconComponent},
  template: `<IconComponent icon="search"/>`
}

let filterMobileMenu = ref(true)

let iconSearchMenu = {
  components: {IconComponent},
  template: `<IconComponent icon="filter"/>`
}

let filterWidth = computed(() => {
  return contentRef.value && contentRef.value.catHeaderWidth ? contentRef.value.catHeaderWidth : 0
})

// * window resize listener
window.addEventListener('resize', resize);

// * resize function
function resize() {
  nextTick(() => {
    if (filterWidth.value > 480) {
      filterMobileMenu.value = true
    }
  })
}

onMounted(() => {
  resize()
})

function searchingStrings (name) {
  let arr = []
  searchFilter.value.toLowerCase().split(' ').forEach(item => {
    arr.push(name.toLowerCase().includes(item))
  })

  return arr.filter(a => a === false).length <= 0
}

// * Categories menu
let availableCategories = inject('availableCategories')

let categoriesMenu = computed(() => {
  let arr = [...availableCategories.value]
  let arrMenu = []
  arr.forEach(item => {
    arrMenu.push({
      id: item.id,
      name: item.name
    })
  })

  arrMenu.unshift({
    id: 0,
    name: amLabels.value.filter_all
  })

  return arrMenu
})

// * Selected category
let categorySelected = inject('categorySelected')
let categoriesFiltered = computed(() => useAvailableCategories(amEntities.value, shortcodeData.value, employeeFilter.value, locationFilter.value))

let categoryPackages = computed(() => {
  let arr = []
  if (categoriesFiltered.value.find(cat => cat.id === categorySelected.value)) {
    amEntities.value.packages.forEach(pack => {
      categoriesFiltered.value.find(cat => cat.id === categorySelected.value).packageList.forEach(packId => {
        if (
          pack.id === packId
          && (searchFilter.value ? searchingStrings(pack.name) : true)
        ) {
          arr.push(pack)
        }
      })
    })
  } else {
    amEntities.value.packages.forEach(pack => {
      categoriesFiltered.value.forEach(cat => {
        cat.packageList.forEach(packId => {
          if (
            pack.id === packId
            && !arr.find(p => p.id === packId)
            && (searchFilter.value ? searchingStrings(pack.name) : true)
          ) {
            arr.push(pack)
          }
        })
      })
    })
  }

  return arr
})
let categoryServices = computed(() => {
  let arr = []

  if (categorySelected.value && categoriesFiltered.value.find(cat => cat.id === categorySelected.value)) {
    amEntities.value.services.forEach(service => {
      categoriesFiltered.value.find(cat => cat.id === categorySelected.value).serviceIdList.forEach(serviceId => {
        if (
          service.id === serviceId &&
          (searchFilter.value ? searchingStrings(service.name) : true)
        ) {
          arr.push(service)
        }
      })
    })
  } else {
    amEntities.value.services.forEach(service => {
      categoriesFiltered.value.forEach(cat => {
        cat.serviceIdList.forEach(serviceId => {
          if (
            service.id === serviceId &&
            (searchFilter.value ? searchingStrings(service.name) : true)
          ) {
            arr.push(service)
          }
        })
      })
    })
  }

  return arr
})

let employeeFilter = ref(null)

let locationFilter = ref(null)

// * Category items (packages, services) visibility
let displayCategoryPackages = ref(true)
let displayCategoryServices = ref(true)

function changeCategoryItemsVisibility(key) {
  if (key === 'all') {
    displayCategoryPackages.value = true
    displayCategoryServices.value = true
  }

  if (key === 'packages') {
    displayCategoryPackages.value = true
    displayCategoryServices.value = false
  }

  if (key === 'services') {
    displayCategoryPackages.value = false
    displayCategoryServices.value = true
  }
}

onBeforeMount(() => {
  if (preselected.value.show) changeCategoryItemsVisibility(preselected.value.show)
})

let filterClassWidth = computed(() => {
  // p - preselected
  // a - array
  let pEmployee = shortcodeData.value.employee
  let aEmployee = amEntities.value.employees.length
  let pLocation = shortcodeData.value.location
  let aLocation = amEntities.value.locations.length

  let searchVisibility = customizedOptions.value.searchInput.visibility
  let employeeVisibility = customizedOptions.value.filterEmployee.visibility && pEmployee.length !== 1 && aEmployee > 1 && !licence.isLite
  let locationVisibility = customizedOptions.value.filterLocation.visibility && pLocation.length !== 1 && aLocation > 1 && !licence.isLite && !licence.isStarter
  let buttonsVisibility = customizedOptions.value.filterButtons.visibility
    && preselected.value.show !== 'packages'
    && preselected.value.show !== 'services'
    && categoryPackages.value.length !== 0
    && categoryServices.value.length !== 0
    && !useCartHasItems(store)

  let classFilter = {
    search: 'am-w30',
    employee: 'am-w20',
    location: 'am-w20',
    buttons: 'am-w30',
    category: 'am-w100'
  }

  if (filterWidth.value > 992) {
    if (!searchVisibility || !buttonsVisibility) {
      classFilter.employee = (!searchVisibility && !buttonsVisibility) ? 'am-w50' : 'am-w35'
      classFilter.location = (!searchVisibility && !buttonsVisibility) ? 'am-w50' : 'am-w35'
      classFilter.search = !buttonsVisibility && !locationVisibility && !employeeVisibility ? 'am-w100' : 'am-w30'

      if (!employeeVisibility) {
        classFilter.location = (!searchVisibility && !buttonsVisibility) ? 'am-w100' : 'am-w70'
      }

      if (!locationVisibility) {
        classFilter.employee = (!searchVisibility && !buttonsVisibility) ? 'am-w100' : 'am-w70'
      }
    } else {
      if (!employeeVisibility) {
        classFilter.location = 'am-w40'
      }

      if (!locationVisibility) {
        classFilter.employee = 'am-w40'
      }

      if (!employeeVisibility && !locationVisibility) {
        classFilter.search = 'am-w70'
      }
    }
  } else if (filterWidth.value > 768) {
    classFilter.search = buttonsVisibility ? 'am-w50 am-tablet am-order1' : 'am-w100 am-tablet am-order1'
    classFilter.buttons = searchVisibility ? 'am-w50 am-tablet am-order2' : 'am-w100 tablet am-order2'
    classFilter.employee = locationVisibility ? 'am-w50 am-tablet am-order3' : 'am-w100 am-tablet am-order3'
    classFilter.location = employeeVisibility ? 'am-w50 am-tablet am-order4' : 'am-w100 am-tablet am-order4'
    classFilter.category = 'am-w100 am-tablet am-order5'
  } else if (filterWidth.value > 480) {
    classFilter.search = buttonsVisibility ? 'am-w50 am-tablet am-order1' : 'am-w100 am-tablet am-order1'
    classFilter.buttons = searchVisibility ? 'am-w50 am-tablet am-order2' : 'am-w100 tablet am-order2'
    classFilter.employee = locationVisibility ? 'am-w50 am-tablet am-order3' : 'am-w100 am-tablet am-order3'
    classFilter.location = employeeVisibility ? 'am-w50 am-tablet am-order4' : 'am-w100 am-tablet am-order4'
    classFilter.category = 'am-w100 am-tablet am-order5'
  } else {
    classFilter.employee = 'am-w100 am-mobile'
    classFilter.location = 'am-w100 am-mobile'
    classFilter.search = 'am-w100 am-mobile'
    classFilter.buttons = 'am-w100 am-mobile'
    classFilter.category = 'am-w100 am-mobile'
  }

  return classFilter
})

// * Container width
let containerWidth = computed(() => {
  return contentRef.value && contentRef.value.catContainerWidth ? contentRef.value.catContainerWidth : 0
})

let headingStringRender = computed(() => {
  let packagesLength = categoryPackages.value.length && !useCartHasItems(store) ? categoryPackages.value.length : 0

  let serviceString = categoryServices.value.length > 1 ? amLabels.value.heading_services : amLabels.value.heading_service
  let packageString = packagesLength ? packagesLength > 1 ? amLabels.value.packages : amLabels.value.package : ''

  if (!categoryServices.value.length && !packagesLength) {
    return amLabels.value.no_search_data
  }

  if (displayCategoryServices.value && (!displayCategoryPackages.value || !packagesLength)) {
    return `${amLabels.value.available} - ${ categoryServices.value.length } ${serviceString}`
  }

  if ((!displayCategoryServices.value || !categoryServices.value.length) && displayCategoryPackages.value) {
    return `${amLabels.value.available} - ${packagesLength } ${packageString}`
  }

  let connective = packagesLength ? '/' : ''

  return `${amLabels.value.available} - ${categoryServices.value.length} ${serviceString} ${connective} ${packagesLength} ${packageString}`
})

onMounted(() => {
  nextTick(() => {
    empty.value = categoryPackages.value.length === 0 && categoryServices.value.length === 0
  })
})

// * Choose category from categories menu
function selectCategory (category) {
  const categoryId = category.id === 0 ? null : category.id
  categorySelected.value = categoryId
  store.commit('booking/setCategoryId',categoryId)
}

let itemType = inject('itemType')

// * Select Service
function selectService (id) {
  store.commit('booking/setServiceId', parseInt(id))
  store.commit('booking/setBookableType', 'appointment')

  itemType.value = 'appointment'

  useAction(store, {}, 'SelectService', 'appointment', null, null)

  nextTick(() => {
    nextPage()
  })
}

// * Select Package
function selectPackage (pack) {
  store.commit('booking/setServiceId', null)
  store.commit('booking/setPackageId', pack.id)
  store.commit('booking/setBookableType', 'package')
  store.commit('booking/setMultipleAppointments', useBuildPackage(0, pack))
  store.commit('booking/setCartItemIndex', 0)

  itemType.value = 'package'

  useAction(store, {}, 'SelectPackage', 'package', null, null)

  nextTick(() => {
    nextPage()
  })
}

function goBack () {
  categorySelected.value = null
  store.commit('booking/setCategoryId', null)
  previousPage()
}

function goToCart () {
  useRemoveLastCartItem(store)

  showCart.value = true

  openBooking.value = true
}

// * Dialog Employees
let dialogEmployees = ref(false)
let dialogEmployeesType = ref('')
let dialogEmployeesArray = ref([])
let dialogPackage = ref({})
let dialogServiceId = ref(null)

function closeEmployeeDialog () {
  dialogEmployeesType.value = ''
  dialogEmployeesArray.value = []
  dialogPackage.value = {}
  dialogServiceId.value = null
}

function getDialogPackageEmployees (pack) {
  dialogEmployeesArray.value = usePackageEmployees(amEntities.value, pack, shortcodeData.value)
  dialogPackage.value = pack
  dialogEmployees.value = true
}

function getDialogServiceEmployees (serviceId) {
  let arr = []
  let employeesIds = Object.keys(amEntities.value.entitiesRelations)

  employeesIds.forEach((employeeId) => {
    if (
      employeeId in amEntities.value.entitiesRelations
      && serviceId in amEntities.value.entitiesRelations[employeeId]
      && amEntities.value.employees.find(a => a.id === parseInt(employeeId))
    ) {
      arr.push(amEntities.value.employees.find(a => a.id === parseInt(employeeId)))
    }
  })

  dialogEmployeesArray.value = arr
  dialogEmployeesType.value = 'service'
  dialogEmployees.value = true

  dialogServiceId.value = serviceId
}

function employeePrice(employee) {
  let servicePrice = amEntities.value.services.find(a => a.id === dialogServiceId.value).price
  let employeeServicePrice = employee.serviceList.find(a => a.id === dialogServiceId.value).price

  return employeeServicePrice !== servicePrice ? `${employeeServicePrice - servicePrice > 0 ? (servicePrice > 0 ? '+' : '') : '-'} ${useFormattedPrice(employeeServicePrice - servicePrice)}` : 0
}

function dialogBooking () {
  if (dialogEmployeesType.value === 'service') {
    selectService(dialogServiceId.value)
  } else {
    selectPackage(dialogPackage.value)
  }

  closeEmployeeDialog()
}

function packageDurationLabel(duration, type) {
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

function displayPackageLocationLabel (entities, item) {
  let locations = usePackageLocations(entities, item)
  if (locations.length === 1 || (locations.length && locations.every(location => location.id === locations[0].id))) {
    return locations[0].address ? locations[0].address : locations[0].name
  }
  return amLabels.value.multiple_locations
}

function displayServiceLocationLabel (entities, id) {
  let locations = useServiceLocation(entities, id)
  if (locations.length === 1 || (locations.length && locations.every(location => location.id === locations[0].id))) {
    return locations[0].address ? locations[0].address : locations[0].name
  }
  return amLabels.value.multiple_locations
}

// * Fonts
let amFonts = inject('amFonts', ref({
  fontFamily: 'Amelia Roboto, sans-serif',
  fontUrl: '',
  customFontFamily: '',
  fontFormat: '',
  customFontSelected: false
}))

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-fcil-text-op-10': useColorTransparency(amColors.value.colorSbText, 0.1),
    '--am-c-fcil-main-text-op15': useColorTransparency(amColors.value.colorMainText, 0.15),
    '--am-c-fcil-card-text-op15': useColorTransparency(amColors.value.colorCardText, 0.15),
    '--am-c-fcil-card-text-op80': useColorTransparency(amColors.value.colorCardText, 0.80),
    '--am-c-fcil-primary-op20': useColorTransparency(amColors.value.colorPrimary, 0.20),
    '--am-c-fcil-success-op20': useColorTransparency(amColors.value.colorSuccess, 0.20),
    '--am-c-fcil-filter-text-op10': useColorTransparency(amColors.value.colorInpText, 0.1),
    '--am-w-fcil-main': availableCategories.value.length !== 1 && preselectedCategories.value !== 1 && customizedOptions.value.sidebar.visibility && sideMenuVisibility.value ? 'calc(100% - 220px)' : '100%',
    '--am-w-fcil-card': contentRef.value && contentRef.value.catFormWidth < 580 ? '100%' : '50%',
  }
})

let popupCssVars = computed(() => {
  return {
    '--am-f-fcil-employee-f': amFonts.value.fontFamily,
    '--am-c-fcil-employee-bgr': amColors.value.colorMainBgr,
    '--am-c-fcil-employee-heading': amColors.value.colorMainHeadingText,
    '--am-c-fcil-employee-text': amColors.value.colorMainText,
    '--am-c-fcil-employee-text-op80': useColorTransparency(amColors.value.colorMainText, 0.80),
    '--am-c-fcil-employee-text-op15': useColorTransparency(amColors.value.colorMainText, 0.15),
    '--am-c-fcil-employee-primary': amColors.value.colorPrimary,
    '--am-c-fcil-employee-primary-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),
    '--am-c-inp-border': amColors.value.colorInpBorder,
    '--am-c-main-text': amColors.value.colorMainText,
  }
})
</script>

<script>
export default {
  name: "CategoryItemsList",
  key: "categoryItemsList"
}
</script>

<style lang="scss">
@import '../../../../../src/assets/scss/common/quill/_quill-mixin.scss';

.amelia-v2-booking #amelia-container {
  // am-    amelia
  // -c-    color
  // -fcil- form category items list
  // -sb-   sidebar
  // -bgr   background
  .am-fcil {
    --am-c-fcil-filter-text: var(--am-c-inp-text);
    --am-c-fcil-filter-placeholder: var(--am-c-inp-placeholder);
    --am-c-fcil-filter-inp-bgr: var(--am-c-inp-bgr);
    --am-c-fcil-main-bgr: var(--am-c-main-bgr);
    --am-c-fcil-main-heading: var(--am-c-main-heading-text);
    --am-c-fcil-main-text: var(--am-c-main-text);
    --am-c-fcil-card-bgr: var(--am-c-card-bgr);
    --am-c-fcil-card-text: var(--am-c-card-text);
    --am-c-fcil-card-border: var(--am-c-card-border);
    --am-c-fcil-primary: var(--am-c-primary);
    --am-c-fcil-success: var(--am-c-success);
    width: 100%;
    padding: 24px;
    border-radius: 10px;

    &.am-mobile {
      padding: 8px;
    }

    &__filter {
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      margin: 12px 0;

      &-buttons {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;

        &__menu {
          font-size: 24px;
          flex: 0 0 auto;
          margin: 0 0 0 8px;
        }
      }

      &-item {
        width: 100%;
        padding: 0 8px;
        margin: 12px 0;

        &:first-child {
          padding-left: 0;
        }

        &:last-child {
          padding-right: 0;
        }

        &.am-tablet {
          &.am-order {
            &1 {
              order: 1;
            }
            &2{
              order: 2;
            }
            &3 {
              order: 3;
              padding-left: 0;
            }
            &4 {
              order: 4;
              padding-right: 0;
            }
            &5 {
              order: 5;
            }
          }
        }

        &__btn {
          width: 100%;
          max-width: 30%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 15px;
          font-weight: 500;
          line-height: 1.6;
          padding: 2px 8px;
          border-radius: 6px;
          transition: all .2s ease-in-out;
          cursor: pointer;
          color: var(--am-c-fcil-filter-placeholder);

          &:hover, &.am-active {
            color: var(--am-c-fcil-filter-text);
            background-color: var(--am-c-fcil-filter-inp-bgr);
          }

          & span {
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
          }

          &-wrapper {
            width: 100%;
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: center;
            justify-content: space-between;
            background-color: var(--am-c-fcil-filter-text-op10);
            border-radius: 6px;
            padding: 6px;
          }
        }

        &.am- {
          &w20 {
            max-width: 20%;
          }

          &w30 {
            max-width: 30%;
          }

          &w35 {
            max-width: 35%;
          }

          &w40 {
            max-width: 40%;
          }

          &w50 {
            max-width: 50%;
          }

          &w60 {
            max-width: 60%;
          }

          &w70 {
            max-width: 70%;
          }

          &w100 {
            max-width: 100%;
            padding: 0;
          }
        }
      }

      .slide-fade-enter-active {
        transition: all 0.3s ease-out;
      }

      .slide-fade-leave-active {
        transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
      }

      .slide-fade-enter-from,
      .slide-fade-leave-to {
        transform: translatey(20px);
        opacity: 0;
      }
    }

    &__main {
      width: 100%;
      max-width: var(--am-w-fcil-main);
      border: 1px solid var(--am-c-fcil-main-text-op15);
      border-radius: 6px;

      &.am-mobile {
        border: none;
      }
    }

    &__heading {
      font-size: 18px;
      font-weight: 500;
      line-height: 1.555555;
      color: var(--am-c-fcil-main-heading);
      padding: 16px 24px 16px;
    }

    &__wrapper{
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      justify-content: center;
      padding: 0 16px 16px;

      &.am-mobile {
        padding: 0;
      }

      &.no-scroll {
        max-height: unset;
        overflow-x: unset;
      }
    }

    &__item {
      max-width: var(--am-w-fcil-card);
      width: 100%;
      display: flex;
      padding: 8px;
      background-color: transparent;

      &-inner {
        display: flex;
        flex-direction: column;
        position: relative;
        width: 100%;
        padding: 0 0 56px;
        border-radius: 6px;
        background-color: var(--am-c-fcil-card-bgr);
        box-shadow: 0 0 6px 2px var(--am-c-fcil-main-text-op15);

        &.am-mobile {
          padding: 12px;
        }
      }

      &-content {
        height: 100%;
        padding: 12px 12px 0;
      }

      &-badge {
        display: inline-flex;
        align-items: center;
        padding: 0 8px 0 4px;
        border-radius: 12px;

        &.am-package {
          background: linear-gradient(95.75deg, var(--am-c-fcil-card-bgr) -110.8%,  var(--am-c-warning) 114.33%);
          span {
            color: var(--am-c-fcil-card-bgr);
          }
        }

        &.am-service {
          background-color: var(--am-c-fcil-success-op20);
          span {
            color:  var(--am-c-fcil-success);
          }
        }

        &__wrapper {
          height: 24px;
          margin: 0 0 12px;
        }

        span {
          display: block;
          font-size: 14px;
          font-weight: 400;
          line-height: 1;

          &[class*="am-icon"] {
            font-size: 24px;
          }
        }
      }

      &-hero {
        padding: 56.25% 0 0;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        border: 1px solid var(--am-c-fcil-main-text-op15);
        border-radius: 4px;
      }

      &-heading {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        margin: 12px 0;
      }

      &-name {
        flex: 1 1 30%;
        font-size: 18px;
        font-weight: 500;
        line-height: 1.55556;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        color: var(--am-c-fcil-card-text);
        margin: 0 4px 0 0;
      }

      &-cost {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;

        & > span {
          display: inline-flex;
          font-size: 14px;
          font-weight: 500;
          line-height: 20px;
          padding: 2px 8px;
          border-radius: 24px;
          margin: 0 8px 8px 0;
          flex: 0 1 auto;

          &:last-child {
            margin-right: 0;
          }
        }
      }

      &-price {
        color: var(--am-c-fcil-primary);
        background-color: var(--am-c-fcil-primary-op20);
      }

      &-discount {
        color: var(--am-c-fcil-success);
        background-color: var(--am-c-fcil-success-op20);
      }

      &-info {
        display: flex;
        flex-wrap: wrap;
        align-items: center;

        &__inner {
          height: 18px;
          display: inline-flex;
          align-items: center;
          max-width: 100%;
          padding: 0 8px 0 0;
          margin: 0 0 8px;

          &:last-child {
            padding: 0;
          }

          span {
            font-size: 13px;
            font-weight: 400;
            color: var(--am-c-fcil-card-text-op80);
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;

            &[class*="am-icon"] {
              flex: 0 0 auto;
              font-size: 24px;
              color: var(--am-c-fcil-primary);
            }
          }
        }
      }

      &-services {
        margin: 0 0 12px;

        span {
          position: relative;
          display: inline-flex;
          font-size: 13px;
          font-weight: 400;
          line-height: 1.384615;
          word-break: break-word;
          color: var(--am-c-fcil-card-text-op80);
          padding: 0 8px 0 0;

          &:after {
            content: '';
            position: absolute;
            top: 50%;
            right: 2px;
            transform: translateY(-50%);
            border: 2px solid var(--am-c-fcil-card-text-op80);
            border-radius: 50%;
          }

          &:first-child {
            padding-right: 2px;
          }

          &:first-child, &:last-child {
            &:after {
              display: none;
            }
          }
        }
      }

      &-footer {
        position: absolute;
        bottom: 12px;
        left: 12px;
        width: calc(100% - 24px);
        display: flex;
        align-items: center;
        justify-content: space-between;

        &.am-mobile {
          position: relative;
          bottom: 0;
          left: 0;
          width: 100%;
          flex-wrap: wrap;
        }

        .am-button {
          &.am-micro {
            margin-top: 8px;
          }
        }
      }
    }
  }
}

.am-dialog-popup {
  &.am-fcil-employee {
    z-index: 9999999 !important;

    * {
      font-family: var(--am-f-fcil-employee-f), sans-serif;
    }

    .el-dialog {
      background-color: var(--am-c-fcil-employee-bgr, $am-white);

      &__header {
        border: 1px solid transparent;
        border-bottom-color: var(--am-c-fcil-employee-text-op15);
        padding: 16px 24px;
      }

      &__headerbtn {
        text-decoration: none;

        * {
          color: var(--am-c-fcil-employee-text);
        }

        &:hover {
          * {
            color: var(--am-c-fcil-employee-primary);
            text-decoration: none;
          }
        }
      }
    }

    .am-collapse-item {

      $count: 100;
      @for $i from 0 through $count {
        &:nth-child(#{$i + 1}) {
          animation: 600ms cubic-bezier(.45,1,.4,1.2) #{$i*100}ms am-animation-slide-up;
          animation-fill-mode: both;
        }
      }

      &__heading {
        padding: 8px;
        transition-delay: .5s;

        &-side {
          transition-delay: 0s;
        }
      }
    }

    .am-fcil-employee {
      &__header {
        font-size: 18px;
        font-weight: 500;
        line-height: 1.55555;
        color: var(--am-c-fcil-employee-heading);
      }

      &__heading {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;

        &-left {
          display: flex;
          align-items: center;
          justify-content: flex-start;
        }
      }

      &__img {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        width: 54px;
        height: 54px;
        border-radius: 4px;
        border: 1px solid var(--am-c-inp-border);
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        font-size: 18px;
        font-weight: 500;
        color: var(--am-c-fcil-employee-bgr);
        margin-right: 12px;
      }

      &__name {
        font-size: 15px;
        font-weight: 500;
        line-height: 1.33333;
        color: var(--am-c-fcil-employee-text);
      }

      &__badge {
        color: #FFF;
        border-radius: 4px;
        padding: 0 4px;
        font-size: 13px;
        line-height: 19px;
      }

      &__text {
        font-size: 15px;
        font-weight: 400;
        line-height: 1.6;
        color: var(--am-c-fcil-employee-text-op80);

        &.ql-description {
          @include quill-styles;
        }
      }

      &__price {
        display: inline-flex;
        flex: 0 0 auto;
        font-size: 14px;
        font-weight: 500;
        color: var(--am-c-fcil-employee-primary);
        background-color: var(--am-c-fcil-employee-primary-op10);
        border-radius: 20px;
        padding: 2px 8px;
      }
    }
  }
}
</style>
