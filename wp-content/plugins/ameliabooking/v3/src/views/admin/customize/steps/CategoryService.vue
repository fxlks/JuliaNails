<template>
  <Content
    ref="contentRef"
    wrapper-class="am-fcis"
    form-class="am-fcis__form"
    heading-class="am-fcis__header"
    :content-class="`am-fcis__content ${
      customizeOptions.pageScroll.visibility ? '' : 'no-scroll'
    }`"
    :style="cssVars"
  >
    <template #header>
      <Header
        :btn-string="labelsDisplay('back_btn')"
        :btn-type="customizeOptions.backBtn.buttonType"
      ></Header>
    </template>
    <template #heading>
      <div
        :class="[{'am-tablet': pageWidth <= 678}, {'am-mobile': pageWidth < 450}]"
        class="am-fcis__header-top"
      >
        <div class="am-fcis__header-text">
          <span class="am-fcis__header-name">
            {{ service.name }}
          </span>
          <div
            v-if="customizeOptions.serviceBadge.visibility"
            class="am-fcis__badge am-service"
          >
            <span class="am-icon-service"></span>
            <span>
              {{ labelsDisplay('heading_service') }}
            </span>
          </div>
        </div>
        <div class="am-fcis__header-action">
          <span
            v-if="customizeOptions.servicePrice.visibility"
            class="am-fcis__header-price"
          >
            {{
              service.price
                ? useFormattedPrice(service.price)
                : labelsDisplay('free')
            }}
          </span>
          <span class="am-fcis__header-btn">
            <AmButton :type="customizeOptions.bookingBtn.buttonType">
              {{ labelsDisplay('book_now') }}
            </AmButton>
          </span>
        </div>
      </div>
      <div
        v-if="
          customizeOptions.serviceCategory.visibility ||
          customizeOptions.serviceDuration.visibility ||
          customizeOptions.serviceCapacity.visibility ||
          customizeOptions.serviceLocation.visibility
        "
        class="am-fcis__header-bottom"
      >
        <div class="am-fcis__mini-info">
          <div
            v-if="customizeOptions.serviceCategory.visibility"
            class="am-fcis__mini-info__inner"
          >
            <span class="am-icon-folder"></span>
            <span>{{ category.name }}</span>
          </div>
          <div
            v-if="customizeOptions.serviceDuration.visibility"
            class="am-fcis__mini-info__inner"
          >
            <span class="am-icon-clock"></span>
            <span>{{ serviceDuration(service.duration) }}</span>
          </div>
          <div
            v-if="
              customizeOptions.serviceCapacity.visibility && !licence.isLite
            "
            class="am-fcis__mini-info__inner"
          >
            <span class="am-icon-user"></span>
            <span>1/10</span>
          </div>
          <div
            v-if="
              customizeOptions.serviceLocation.visibility &&
              !licence.isLite &&
              !licence.isStarter
            "
            class="am-fcis__mini-info__inner"
          >
            <span class="am-icon-locations"></span>
            <span>{{
              service.location
                ? service.location
                : labelsDisplay('multiple_locations')
            }}</span>
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
          :style="{
            backgroundImage: `url(${service.gallery[0].pictureFullPath})`,
          }"
        ></div>
        <div
          v-if="serviceThumbsGallery.length && pageWidth > 677"
          class="am-fcis__gallery-thumb__wrapper"
        >
          <div
            v-for="(img, index) in serviceThumbsGallery"
            :key="index"
            class="am-fcis__gallery-thumb"
            :class="{ 'am-one-thumb': serviceThumbsGallery.length === 1 }"
            :style="{ backgroundImage: `url(${img.pictureFullPath})` }"
          ></div>
        </div>
        <AmButton
          :custom-class="`am-fcis__gallery-btn${pageWidth < 678 ? ' am-mobile' : ''}`"
          category="secondary"
          type="filled"
          @click="() => (galleryDialog = true)"
        >
          <span class="am-icon-gallery"></span>
          <span>
              {{ labelsDisplay('view_all_photos') }}
            </span>
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
      >
        <div class="am-gd" :style="cssGalleryDialogVars">
          <div class="am-gd__display-wrapper">
            <div
              class="am-gd__arrows"
              style="display: flex; justify-content: space-between"
            >
              <span
                class="am-icon-arrow-left"
                @click="
                  () =>
                    (activeImage =
                      activeImage <= 0
                        ? service.gallery.length - 1
                        : activeImage - 1)
                "
              ></span>
              <span
                class="am-icon-arrow-right"
                @click="
                  () =>
                    (activeImage =
                      service.gallery.length - 1 === activeImage
                        ? 0
                        : activeImage + 1)
                "
              ></span>
            </div>
            <div
              v-for="(img, index) in service.gallery"
              :key="index"
              class="am-gd__display"
              :style="{ display: index === activeImage ? 'flex' : 'none' }"
              @click="
                () =>
                  (activeImage =
                    service.gallery.length - 1 === activeImage
                      ? 0
                      : activeImage + 1)
              "
            >
              <img :src="img.pictureFullPath" :alt="index" />
            </div>
          </div>
          <div class="am-gd__selection">
            {{ `${activeImage + 1}/${service.gallery.length}` }}
          </div>
          <div class="am-gd__thumb-wrapper">
            <div
              v-for="(img, index) in service.gallery"
              :key="index"
              class="am-gd__thumb"
              :class="{ 'am-active': index === activeImage }"
              :style="{ backgroundImage: `url(${img.pictureFullPath})` }"
              @click="() => (activeImage = index)"
            ></div>
          </div>
        </div>
      </AmDialog>
      <!-- /Gallery Images -->

      <!-- Service Info - (description, employees) -->
      <div
        v-if="
          customizeOptions.serviceDescription.visibility ||
          customizeOptions.serviceEmployees.visibility
        "
        class="am-fcis__info"
      >
        <div class="am-fcis__info-tab__wrapper">
          <div
            v-if="
              service.description &&
              customizeOptions.serviceDescription.visibility
            "
            class="am-fcis__info-tab"
            :class="{ 'am-active': tabsActive === 'description' }"
            @click="() => (tabsActive = 'description')"
          >
            {{ labelsDisplay('about_service') }}
          </div>
          <div
            v-if="
              customizeOptions.serviceEmployees.visibility && !licence.isLite
            "
            :class="{ 'am-active': tabsActive === 'employees' }"
            class="am-fcis__info-tab"
            @click="() => (tabsActive = 'employees')"
          >
            {{ labelsDisplay('tab_employees') }}
          </div>
        </div>
        <div
          v-if="
            customizeOptions.serviceDescription.visibility ||
            customizeOptions.serviceEmployees.visibility
          "
          class="am-fcis__info-content__wrapper"
        >
          <!-- Description -->
          <div
            v-if="
              service.description &&
              customizeOptions.serviceDescription.visibility
            "
            v-show="tabsActive === 'description'"
            class="am-fcis__info-content"
          >
            <div
              class="am-fcis__info-service__desc"
              v-html="service.description"
            ></div>
          </div>
          <!-- /Description -->

          <!-- Employees -->
          <div
            v-if="
              customizeOptions.serviceEmployees.visibility && !licence.isLite
            "
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
                      <div
                        class="am-fcis__info-employee__img"
                        :style="{ ...employeeImage(employee) }"
                      >
                        {{ employeeSign(employee) }}
                      </div>
                      <div class="am-fcis__info-employee__name">
                        {{ employee.firstName }} {{ employee.lastName }}
                      </div>
                    </div>
                    <div
                      v-if="
                  serviceEmployeePrice(employee) &&
                  customizeOptions.serviceEmployeePrice.visibility
                "
                      class="am-fcis__info-employee__price"
                    >
                      {{ serviceEmployeePrice(employee) }}
                    </div>
                  </div>
                </template>
                <template #default></template>
              </AmCollapseItem>
            </AmCollapse>
          </div>
          <!-- /Employees -->
        </div>
      </div>
      <!-- /Service Info - (description, employees) -->

      <!-- Available Service in Packages -->
      <div
        v-if="
          customizeOptions.servicePackages.visibility &&
          features.packages
        "
        class="am-fcis__include-wrapper"
      >
        <div class="am-fcis__include-heading">
          <span class="am-fcis__include-heading__text">
            {{ labelsDisplay('service_available_in_package') }}
          </span>
          <span
            class="am-fcis__include-heading__btn"
            @click="() => (displayAllPackages = !displayAllPackages)"
          >
            <template v-if="!displayAllPackages">
              {{ labelsDisplay('more_packages') }}
            </template>
            <template v-else>
              {{ labelsDisplay('less_packages') }}
            </template>
          </span>
        </div>
        <div
          v-for="pack in displayServicePackages"
          :key="pack.id"
          class="am-fcis__include"
        >
          <div class="am-fcis__include-hero">
            <div
              v-if="pageWidth > 450"
              class="am-fcis__include-img"
              :style="{ ...packageImage(pack) }"
            >
              {{ !pack.pictureFullPath ? pack.name.charAt(0) : '' }}
            </div>
            <div
              class="am-fcis__include-text"
              :class="{'am-mobile': pageWidth < 451}"
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
                  v-if="customizeOptions.packagePrice.visibility"
                  class="am-fcis__include-cost"
                >
                  <span v-if="pack.discount" class="am-fcis__include-discount">
                    {{ `${labelsDisplay('save')} ${pack.discount}%` }}
                  </span>
                  <span class="am-fcis__include-price">
                    {{
                      pack.price
                        ? useFormattedPrice(usePackageAmount(pack))
                        : labelsDisplay('free')
                    }}
                  </span>
                </div>
              </div>
              <div class="am-fcis__include-info">
                <div
                  v-if="customizeOptions.packageCategory.visibility"
                  class="am-fcis__include-info__inner"
                >
                  <span class="am-icon-folder"></span>
                  <span>{{ category.name }}</span>
                </div>
                <div
                  v-if="customizeOptions.packageDuration.visibility"
                  class="am-fcis__include-info__inner"
                >
                  <span class="am-icon-clock"></span>
                  <span v-if="pack.endDate">
                    {{
                      `${labelsDisplay('expires_at')} ${
                        pack.endDate.split(' ')[0]
                      }`
                    }}
                  </span>
                  <span v-else-if="pack.durationCount">
                    {{
                      `${labelsDisplay('expires_after')} ${
                        pack.durationCount
                      } ${durationTypeLabel(
                        pack.durationCount,
                        pack.durationType
                      )}`
                    }}
                  </span>
                  <span v-else>
                    {{ labelsDisplay('without_expiration') }}
                  </span>
                </div>
                <div
                  v-if="customizeOptions.packageCapacity.visibility"
                  class="am-fcis__include-info__inner"
                >
                  <span class="am-icon-user"></span>
                  <span>1/1</span>
                </div>
                <div
                  v-if="
                    pack.locations.length &&
                    customizeOptions.packageLocation.visibility
                  "
                  class="am-fcis__include-info__inner"
                >
                  <span class="am-icon-locations"></span>
                  <span>
                    {{
                      pack.locations.length === 1
                        ? pack.locations[0].name
                        : labelsDisplay('multiple_locations')
                    }}
                  </span>
                </div>
                <div
                  v-if="customizeOptions.packageServices.visibility"
                  class="am-fcis__include-info__inner am-fcis__include-info__services"
                >
                  <span>
                    {{ `${labelsDisplay('in_package')}:` }}
                  </span>
                  <span v-for="obj in pack.services" :key="obj.id">
                    {{ obj.name }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /Available Package in Service -->
    </template>
  </Content>
</template>

<script setup>
// * Components
import Content from '../../../common/CatalogFormConstruction/Content/Content.vue'
import Header from '../../../common/CatalogFormConstruction/Header/Header.vue'
import AmButton from '../../../_components/button/AmButton.vue'
import AmDialog from '../../../_components/dialog/AmDialog.vue'

// * Vue
import {
  ref,
  inject,
  computed,
  onMounted,
  onBeforeUnmount,
  watchEffect,
} from 'vue'

// * Composables
import { useFormattedPrice } from '../../../../assets/js/common/formatting.js'
import {
  amCardColors,
  useColorTransparency,
} from '../../../../assets/js/common/colorManipulation.js'
import { usePackageAmount } from '../../../../assets/js/public/package.js'
import { useReactiveCustomize } from '../../../../assets/js/admin/useReactiveCustomize.js'
import AmCollapse from "../../../_components/collapse/AmCollapse.vue";
import AmCollapseItem from "../../../_components/collapse/AmCollapseItem.vue";

// * Plugin Licence
let licence = inject('licence')

// * Features
let features = inject('features')

// * Customize
const { amCustomize } = useReactiveCustomize()

// * Page Width and Reference
let contentRef = ref()
let pageWidth = inject('containerWidth')

// * Options
let customizeOptions = computed(() => {
  return amCustomize.value.cbf.categoryService.options
})

// * Base URLs
const baseUrls = inject('baseUrls')

// * Gallery active image
let activeImage = ref(0)

// * Selected category
let category = ref({
  id: 1,
  name: 'Category 1',
})

// * Service info tabs
let tabsActive = ref('description')

watchEffect(() => {
  if (!customizeOptions.value.serviceDescription.visibility)
    tabsActive.value = 'employees'
  if (!customizeOptions.value.serviceEmployees.visibility)
    tabsActive.value = 'description'
})

onBeforeUnmount(() => {
  watchEffect(() => {})
})

// * Selected service
let service = ref({
  id: 1,
  name: 'Service 1',
  category: null,
  categoryId: 1,
  color: '#1788FB',
  customPricing: {
    3600: {
      price: 23,
      rules: [],
    },
  },
  price: 100,
  deposit: 5,
  description:
    "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
  duration: 3600,
  extras: [
    {
      id: 10,
      extraId: 10,
      aggregatedPrice: false,
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
      duration: 1800,
      maxQuantity: 5,
      name: 'Extra 2-1',
      position: 1,
      price: 5,
      serviceId: null,
      translations: null,
    },
  ],
  gallery: [
    {
      pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_gallery_placeholder.svg`,
      pictureThumbPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
    },
    {
      pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
      pictureThumbPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
    },
    {
      pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
      pictureThumbPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
    },
    {
      pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
      pictureThumbPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
    },
    {
      pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
      pictureThumbPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
    },
  ],
  pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
  pictureThumbPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
  location: 'Location 1',
})

let serviceEmployees = ref([
  {
    id: 1,
    firstName: 'Herman',
    lastName: 'Small',
    pictureFullPath: null,
    pictureThumbPath: null,
    price: 20,
  },
  {
    id: 2,
    firstName: 'Abbie',
    lastName: 'William',
    pictureFullPath: null,
    pictureThumbPath: null,
    price: 150,
  },
  {
    id: 3,
    firstName: 'Miya',
    lastName: 'Burrows',
    pictureFullPath: null,
    pictureThumbPath: null,
    price: 80,
  },
  {
    id: 4,
    firstName: 'Derrick',
    lastName: 'Cardenas',
    pictureFullPath: null,
    pictureThumbPath: null,
    price: 120,
  },
  {
    id: 5,
    firstName: 'Zeynep',
    lastName: 'Whittington',
    pictureFullPath: null,
    pictureThumbPath: null,
    price: 45,
  },
])

function employeeImage(employee) {
  if (employee.pictureFullPath)
    return { backgroundImage: `url(${employee.pictureFullPath})` }

  return {
    backgroundColor: `${
      amCardColors.value[Math.floor(Math.random() * amCardColors.value.length)]
    }`,
  }
}

function employeeSign(employee) {
  if (!employee.pictureFullPath) {
    let name = `${employee.firstName} ${employee.lastName}`
    return name
      .split(' ')
      .map((s) => s.charAt(0))
      .join('')
      .toUpperCase()
      .substring(0, 3)
      .replace(/[^\w\s]/g, '')
  }
  return ''
}

function serviceEmployeePrice(employee) {
  let servicePrice = employee.price
  if (servicePrice - service.value.price !== 0)
    return `${
      servicePrice - service.value.price > 0 ? '+' : '-'
    } ${useFormattedPrice(servicePrice - service.value.price)}`
  return ''
}

onMounted(() => {
  tabsActive.value = service.value.description ? 'description' : 'employees'
})

let serviceThumbsGallery = computed(() => {
  let thumbs = service.value.gallery.length
    ? JSON.parse(JSON.stringify(service.value.gallery))
    : []
  if (service.value.gallery.length === 1) return []
  thumbs.shift()
  if (thumbs.length > 2) thumbs.splice(2, thumbs.length - 2)
  return thumbs
})

let servicePackages = ref([
  {
    id: 1,
    name: 'Package 1',
    calculatedPrice: false,
    price: 500,
    deposit: 50,
    discount: 10,
    depositPayment: 'percentage',
    color: '#689BCA',
    description:
      'My name a Borat. I come from Kazakhstan. Can I say a first, we support your war of terror! May we show our support to our boys in Iraq! May US and A kill every single terrorist! May your George Bush drink the blood of every single man, women, and child of Iraq! May you destroy their country so that for next thousand years not even a single lizard will survive in their desert!',
    durationCount: 2,
    durationType: 'week',
    endDate: null,
    locations: [{ name: 'Location 1' }, { name: 'Location 2' }],
    pictureFullPath: null,
    pictureThumbPath: null,
    services: [
      {
        id: 1,
        name: 'Service 1',
      },
      {
        id: 2,
        name: 'Service 2',
      },
      {
        id: 3,
        name: 'Service 3',
      },
      {
        id: 4,
        name: 'Service 4',
      },
      {
        id: 5,
        name: 'Service 5',
      },
    ],
  },
  {
    id: 2,
    name: 'Package 2',
    calculatedPrice: false,
    price: 500,
    deposit: 50,
    discount: 10,
    depositPayment: 'percentage',
    color: '#689BCA',
    description:
      'My name a Borat. I come from Kazakhstan. Can I say a first, we support your war of terror! May we show our support to our boys in Iraq! May US and A kill every single terrorist! May your George Bush drink the blood of every single man, women, and child of Iraq! May you destroy their country so that for next thousand years not even a single lizard will survive in their desert!',
    durationCount: 2,
    durationType: 'week',
    endDate: null,
    locations: [{ name: 'Location 1' }, { name: 'Location 2' }],
    pictureFullPath: null,
    pictureThumbPath: null,
    services: [
      {
        id: 1,
        name: 'Service 1',
      },
      {
        id: 2,
        name: 'Service 2',
      },
      {
        id: 3,
        name: 'Service 3',
      },
      {
        id: 4,
        name: 'Service 4',
      },
      {
        id: 5,
        name: 'Service 5',
      },
    ],
  },
  {
    id: 3,
    name: 'Package 3',
    calculatedPrice: false,
    price: 500,
    deposit: 50,
    discount: 10,
    depositPayment: 'percentage',
    color: '#689BCA',
    description:
      'My name a Borat. I come from Kazakhstan. Can I say a first, we support your war of terror! May we show our support to our boys in Iraq! May US and A kill every single terrorist! May your George Bush drink the blood of every single man, women, and child of Iraq! May you destroy their country so that for next thousand years not even a single lizard will survive in their desert!',
    durationCount: 2,
    durationType: 'week',
    endDate: null,
    locations: [{ name: 'Location 1' }, { name: 'Location 2' }],
    pictureFullPath: null,
    pictureThumbPath: null,
    services: [
      {
        id: 1,
        name: 'Service 1',
      },
      {
        id: 2,
        name: 'Service 2',
      },
      {
        id: 3,
        name: 'Service 3',
      },
      {
        id: 4,
        name: 'Service 4',
      },
      {
        id: 5,
        name: 'Service 5',
      },
    ],
  },
  {
    id: 4,
    name: 'Package 4',
    calculatedPrice: false,
    price: 500,
    deposit: 50,
    discount: 10,
    depositPayment: 'percentage',
    color: '#689BCA',
    description:
      'My name a Borat. I come from Kazakhstan. Can I say a first, we support your war of terror! May we show our support to our boys in Iraq! May US and A kill every single terrorist! May your George Bush drink the blood of every single man, women, and child of Iraq! May you destroy their country so that for next thousand years not even a single lizard will survive in their desert!',
    durationCount: 2,
    durationType: 'week',
    endDate: null,
    locations: [{ name: 'Location 1' }, { name: 'Location 2' }],
    pictureFullPath: null,
    pictureThumbPath: null,
    services: [
      {
        id: 1,
        name: 'Service 1',
      },
      {
        id: 2,
        name: 'Service 2',
      },
      {
        id: 3,
        name: 'Service 3',
      },
      {
        id: 4,
        name: 'Service 4',
      },
      {
        id: 5,
        name: 'Service 5',
      },
    ],
  },
  {
    id: 5,
    name: 'Package 5',
    calculatedPrice: false,
    price: 500,
    deposit: 50,
    discount: 10,
    depositPayment: 'percentage',
    color: '#689BCA',
    description:
      'My name a Borat. I come from Kazakhstan. Can I say a first, we support your war of terror! May we show our support to our boys in Iraq! May US and A kill every single terrorist! May your George Bush drink the blood of every single man, women, and child of Iraq! May you destroy their country so that for next thousand years not even a single lizard will survive in their desert!',
    durationCount: 2,
    durationType: 'week',
    endDate: null,
    locations: [{ name: 'Location 1' }, { name: 'Location 2' }],
    pictureFullPath: null,
    pictureThumbPath: null,
    services: [
      {
        id: 1,
        name: 'Service 1',
      },
      {
        id: 2,
        name: 'Service 2',
      },
      {
        id: 3,
        name: 'Service 3',
      },
      {
        id: 4,
        name: 'Service 4',
      },
      {
        id: 5,
        name: 'Service 5',
      },
    ],
  },
])

let displayAllPackages = ref(false)
let displayServicePackages = computed(() => {
  let arr = [...servicePackages.value]
  if (!displayAllPackages.value) return arr.slice(0, 2)
  return arr
})

function packageImage(pack) {
  if (pack.pictureFullPath)
    return { backgroundImage: `url(${pack.pictureFullPath})` }

  return { backgroundColor: pack.color }
}

let galleryDialog = ref(false)

// * Labels
let langKey = inject('langKey')
let amLabels = inject('labels')

function labelsDisplay(label) {
  let computedLabel = computed(() => {
    let translations = amCustomize.value.cbf.categoryService.translations
    return translations &&
      translations[label] &&
      translations[label][langKey.value]
      ? translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

function durationTypeLabel(duration, type) {
  let string = ''
  if (duration > 1) {
    if (type === 'day') string = labelsDisplay('expires_days')
    if (type === 'week') string = labelsDisplay('expires_weeks')
    if (type === 'month') string = labelsDisplay('expires_months')
  } else {
    if (type === 'day') string = labelsDisplay('expires_day')
    if (type === 'week') string = labelsDisplay('expires_week')
    if (type === 'month') string = labelsDisplay('expires_month')
  }
  return string
}

function serviceDuration(seconds) {
  let hours = Math.floor(seconds / 3600)
  let minutes = (seconds / 60) % 60

  return (
    (hours ? hours + amLabels.h + ' ' : '') +
    ' ' +
    (minutes ? minutes + amLabels.min : '')
  )
}

// * Fonts
let amFonts = inject('amFonts')

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-fcis-success-op20': useColorTransparency(
      amColors.value.colorSuccess,
      0.2
    ),
    '--am-c-fcis-primary-op20': useColorTransparency(
      amColors.value.colorPrimary,
      0.2
    ),
    '--am-c-fcis-text-op80': useColorTransparency(
      amColors.value.colorMainText,
      0.8
    ),
    '--am-c-fcis-text-op03': useColorTransparency(
      amColors.value.colorMainText,
      0.03
    ),
    '--am-c-fcis-btn-op50': useColorTransparency(
      amColors.value.colorBtnSec,
      0.5
    ),
  }
})

let cssGalleryDialogVars = computed(() => {
  return {
    '--am-c-fcis-bgr': amColors.value.colorMainBgr,
    '--am-c-fcis-text': amColors.value.colorMainText,
    '--am-c-fcis-success': amColors.value.colorSuccess,
    '--am-c-fcis-primary': amColors.value.colorPrimary,
    '--am-c-scroll-op30': useColorTransparency(
      amColors.value.colorPrimary,
      0.3
    ),
    '--am-c-scroll-op10': useColorTransparency(
      amColors.value.colorPrimary,
      0.1
    ),
    '--am-font-family': amFonts.fontFamily,
  }
})
</script>

<script>
export default {
  name: 'CategoryService',
  key: 'categoryService',
}
</script>

<style lang="scss">
@import '../../../../../src/assets/scss/common/quill/_quill-mixin.scss';

#amelia-app-backend-new {
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
          align-items: center;
          justify-content: space-between;

          &:last-child {
            margin: 0;
          }

          &__hero {
            display: flex;
            align-items: center;
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
            flex: 0 0 auto;
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
}

// - sgd - service gallery dialog
.amelia-v2-booking.amelia-v2-sgd {
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
</style>
