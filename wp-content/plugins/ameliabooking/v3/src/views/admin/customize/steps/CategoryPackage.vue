<template>
  <Content
    ref="contentRef"
    wrapper-class="am-fcip"
    form-class="am-fcip__form"
    heading-class="am-fcip__header"
    :content-class="`am-fcip__content ${
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
        class="am-fcip__header-top"
        :class="[{'am-tablet': pageWidth <= 678}, {'am-mobile': pageWidth < 450}]"
      >
        <div class="am-fcip__header-text">
          <span class="am-fcip__header-name">
            <span>
              {{ pack.name }}
            </span>
          </span>
          <div
            v-if="customizeOptions.packageBadge.visibility"
            class="am-fcip__badge am-package"
          >
            <span class="am-icon-shipment"></span>
            <span>
              {{ labelsDisplay('package') }}
            </span>
          </div>
        </div>
        <div class="am-fcip__header-action">
          <span
            v-if="pack.discount && customizeOptions.packagePrice.visibility"
            class="am-fcip__header-discount"
          >
            {{ `${labelsDisplay('save')} ${pack.discount}%` }}
          </span>
          <span
            v-if="customizeOptions.packagePrice.visibility"
            class="am-fcip__header-price"
          >
            {{
              pack.price
                ? useFormattedPrice(usePackageAmount(pack))
                : amLabels.free
            }}
          </span>
          <span class="am-fcip__header-btn">
            <AmButton :type="customizeOptions.bookingBtn.buttonType">
              {{ labelsDisplay('book_now') }}
            </AmButton>
          </span>
        </div>
      </div>
      <div
        v-if="
          customizeOptions.packageCategory.visibility ||
          customizeOptions.packageDuration.visibility ||
          customizeOptions.packageCapacity.visibility ||
          customizeOptions.packageLocation.visibility
        "
        class="am-fcip__header-bottom"
      >
        <div class="am-fcip__mini-info">
          <div
            v-if="customizeOptions.packageCategory.visibility"
            class="am-fcip__mini-info__inner"
          >
            <span class="am-icon-folder"></span>
            <span>Category 1</span>
          </div>
          <div
            v-if="customizeOptions.packageDuration.visibility"
            class="am-fcip__mini-info__inner"
          >
            <span class="am-icon-clock"></span>
            <span v-if="pack.endDate">
              {{
                `${labelsDisplay('expires_at')} ${pack.endDate.split(' ')[0]}`
              }}
            </span>
            <span v-else-if="pack.durationCount">
              {{
                `${labelsDisplay('expires_after')} ${
                  pack.durationCount
                } ${durationTypeLabel(pack.durationCount, pack.durationType)}`
              }}
            </span>
            <span v-else>
              {{ labelsDisplay('without_expiration') }}
            </span>
          </div>
          <div
            v-if="customizeOptions.packageCapacity.visibility"
            class="am-fcip__mini-info__inner"
          >
            <span class="am-icon-user"></span>
            <span>1/1</span>
          </div>
          <div
            v-if="customizeOptions.packageLocation.visibility"
            class="am-fcip__mini-info__inner"
          >
            <span class="am-icon-locations"></span>
            <span>
              {{ labelsDisplay('multiple_locations') }}
            </span>
          </div>
        </div>
      </div>
    </template>
    <template #content>
      <!-- Package Gallery -->
      <div v-if="pack.gallery.length" class="am-fcip__gallery">
        <div
          class="am-fcip__gallery-hero"
          :class="[{'w100': pack.gallery.length === 1}, {'am-mobile w100': pageWidth < 678}]"
          :style="{
            backgroundImage: `url(${pack.gallery[0].pictureFullPath})`,
          }"
        ></div>
        <div
          v-if="packageThumbsGallery.length && pageWidth > 677"
          class="am-fcip__gallery-thumb__wrapper"
        >
          <div
            v-for="(img, index) in packageThumbsGallery"
            :key="index"
            class="am-fcip__gallery-thumb"
            :class="{ 'am-one-thumb': packageThumbsGallery.length === 1 }"
            :style="{ backgroundImage: `url(${img.pictureFullPath})` }"
          ></div>
          <AmButton
            :custom-class="`am-fcip__gallery-btn${pageWidth < 678 ? ' am-mobile' : ''}`"
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
      </div>
      <!-- Service Gallery -->

      <!-- Gallery Images -->
      <AmDialog
        v-model="galleryDialog"
        :modal-class="'amelia-v2-booking amelia-v2-gdp'"
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
                        ? pack.gallery.length - 1
                        : activeImage - 1)
                "
              ></span>
              <span
                class="am-icon-arrow-right"
                @click="
                  () =>
                    (activeImage =
                      pack.gallery.length - 1 === activeImage
                        ? 0
                        : activeImage + 1)
                "
              ></span>
            </div>
            <div
              v-for="(img, index) in pack.gallery"
              :key="index"
              class="am-gd__display"
              :style="{ display: index === activeImage ? 'flex' : 'none' }"
              @click="
                () =>
                  (activeImage =
                    pack.gallery.length - 1 === activeImage
                      ? 0
                      : activeImage + 1)
              "
            >
              <img :src="img.pictureFullPath" :alt="index" />
            </div>
          </div>
          <div class="am-gd__selection">
            {{ `${activeImage + 1}/${pack.gallery.length}` }}
          </div>
          <div class="am-gd__thumb-wrapper">
            <div
              v-for="(img, index) in pack.gallery"
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

      <!-- Package Info - (description, employees) -->
      <div
        v-if="
          customizeOptions.packageDescription.visibility ||
          customizeOptions.packageEmployees.visibility
        "
        class="am-fcip__info"
      >
        <div class="am-fcip__info-tab__wrapper">
          <div
            v-if="
              pack.description && customizeOptions.packageDescription.visibility
            "
            class="am-fcip__info-tab"
            :class="{ 'am-active': tabsActive === 'description' }"
            @click="() => (tabsActive = 'description')"
          >
            {{ labelsDisplay('about_package') }}
          </div>
          <div
            v-if="customizeOptions.packageEmployees.visibility"
            :class="{ 'am-active': tabsActive === 'employees' }"
            class="am-fcip__info-tab"
            @click="() => (tabsActive = 'employees')"
          >
            {{ labelsDisplay('tab_employees') }}
          </div>
        </div>
        <div class="am-fcip__info-content__wrapper">
          <!-- Description -->
          <div
            v-if="
              pack.description && customizeOptions.packageDescription.visibility
            "
            v-show="tabsActive === 'description'"
            class="am-fcip__info-content"
          >
            <div
              class="am-fcip__info-service__desc"
              v-html="pack.description"
            ></div>
          </div>
          <!-- /Description -->

          <!-- Employees -->
          <div
            v-if="customizeOptions.packageEmployees.visibility"
            v-show="tabsActive === 'employees'"
            class="am-fcip__info-content"
          >
            <AmCollapse>
              <AmCollapseItem
                v-for="employee in packageEmployees"
                :key="employee.id"
                side
              >
                <template #heading>
                  <div class="am-fcip__info-employee">
                    <div class="am-fcip__info-employee__hero">
                      <div
                        class="am-fcip__info-employee__img"
                        :style="{ ...employeeImage(employee) }"
                      >
                        {{ employeeSign(employee) }}
                      </div>
                      <div class="am-fcip__info-employee__name">
                        {{ employee.firstName }} {{ employee.lastName }}
                      </div>
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

      <!-- Available Service in Packages -->
      <div
        v-if="customizeOptions.packageServices.visibility"
        class="am-fcip__include-wrapper"
      >
        <div class="am-fcip__include-heading">
          <span class="am-fcip__include-heading__text">
            {{ `${labelsDisplay('package_includes')}:` }}
          </span>
        </div>
        <AmCollapse>
          <AmCollapseItem
            v-for="book in pack.services"
            :key="book.id"
            :side="true"
          >
            <template #heading>
              <div class="am-fcip__include-service">
                <div
                  class="am-fcip__include-service__img"
                  :style="{ ...cardImage(book) }"
                >
                  {{ cardSign(book) }}
                </div>
                {{
                  book.name +
                  (!pack.sharedCapacity ? ' x ' + book.quantity : '')
                }}
              </div>
            </template>
            <template #default>
              <div class="am-fcip__include-service__info">
                <span>{{ `${labelsDisplay('tab_employees')}:` }}</span>
                <template
                  v-for="employee in packageEmployees"
                  :key="employee.id"
                >
                  <div
                    class="am-fcip__include-service__employee"
                    :style="{ ...cardImage(employee) }"
                  >
                    {{ cardSign(employee) }}
                  </div>
                </template>
                <span v-if="packageEmployees.length > 6">
                  + {{ packageEmployees.length - 6 }}
                </span>
                <template v-if="book.description">
                  <p v-html="book.description"></p>
                </template>
              </div>
            </template>
          </AmCollapseItem>
        </AmCollapse>
        <div class="am-fcip__include-footer">
          <span class="am-fcip__include-footer__text">
            {{ labelsDisplay('package_book_service') }}
          </span>
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
import AmCollapse from '../../../_components/collapse/AmCollapse.vue'
import AmCollapseItem from '../../../_components/collapse/AmCollapseItem.vue'

// * Vue
import {
  ref,
  inject,
  computed,
  onMounted,
  watchEffect,
  onBeforeUnmount,
} from 'vue'

// * Composables
import { useFormattedPrice } from '../../../../assets/js/common/formatting.js'
import {
  amCardColors,
  useColorTransparency,
} from '../../../../assets/js/common/colorManipulation.js'
import { usePackageAmount } from '../../../../assets/js/public/package.js'
import { useReactiveCustomize } from '../../../../assets/js/admin/useReactiveCustomize.js'

// * Customize
const { amCustomize } = useReactiveCustomize()

// * Page Width
let contentRef = ref()
let pageWidth = inject('containerWidth')

// *  Customize Options
let customizeOptions = computed(() => {
  return amCustomize.value.cbf.categoryPackage.options
})

// * Base URLs
const baseUrls = inject('baseUrls')

// * Selected service
let pack = ref({
  id: 1,
  name: 'Package 1',
  color: '#689BCA',
  calculatedPrice: false,
  deposit: 50,
  discount: 10,
  price: 500,
  depositPayment: 'percentage',
  description:
    "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
  durationCount: 2,
  durationType: 'week',
  endDate: null,
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
  ],
  pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
  pictureThumbPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
  locations: ['Location 1, Location 2'],
  services: [
    {
      id: 1,
      name: 'Service 1',
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
      quantity: 5,
    },
    {
      id: 2,
      name: 'Service 2',
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
      quantity: 5,
    },
    {
      id: 3,
      name: 'Service 3',
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
      quantity: 5,
    },
    {
      id: 4,
      name: 'Service 4',
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
      quantity: 5,
    },
    {
      id: 5,
      name: 'Service 5',
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
      quantity: 5,
    },
  ],
})

// * Package info tabs
let tabsActive = ref('description')

watchEffect(() => {
  if (!customizeOptions.value.packageDescription.visibility)
    tabsActive.value = 'employees'
  if (!customizeOptions.value.packageEmployees.visibility)
    tabsActive.value = 'description'
})

onBeforeUnmount(() => {
  watchEffect(() => {})
})

let packageEmployees = ref([
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
    let firstName = employee.firstName.charAt(0)
    let lastName = employee.lastName.charAt(0)
    return `${firstName}${lastName}`
  }
  return ''
}

onMounted(() => {
  tabsActive.value = pack.value.description ? 'description' : 'employees'
})

// * Gallery
let activeImage = ref(0)
let galleryDialog = ref(false)

let packageThumbsGallery = computed(() => {
  let thumbs = pack.value.gallery.length
    ? JSON.parse(JSON.stringify(pack.value.gallery))
    : []
  if (pack.value.gallery.length === 1) return []
  thumbs.shift()
  if (thumbs.length > 2) thumbs.splice(2, thumbs.length - 2)
  return thumbs
})

function cardImage(info) {
  if (info.pictureFullPath)
    return { backgroundImage: `url(${info.pictureFullPath})` }

  return {
    backgroundColor: `${
      amCardColors.value[Math.floor(Math.random() * amCardColors.value.length)]
    }`,
  }
}

function cardSign(info) {
  if (!info.pictureFullPath) {
    let name =
      'firstName' in info ? `${info.firstName} ${info.lastName}` : info.name
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

// * Labels
let langKey = inject('langKey')
let amLabels = inject('labels')

function labelsDisplay(label) {
  let computedLabel = computed(() => {
    let translations = amCustomize.value.cbf.categoryPackage.translations
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

// * Fonts
let amFonts = inject('amFonts')

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-fcip-success-op20': useColorTransparency(
      amColors.value.colorSuccess,
      0.2
    ),
    '--am-c-fcip-primary-op20': useColorTransparency(
      amColors.value.colorPrimary,
      0.2
    ),
    '--am-c-fcip-text-op80': useColorTransparency(
      amColors.value.colorMainText,
      0.8
    ),
    '--am-c-fcip-text-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
    '--am-c-fcip-text-op03': useColorTransparency(
      amColors.value.colorMainText,
      0.03
    ),
    '--am-c-fcip-btn-op50': useColorTransparency(
      amColors.value.colorBtnSec,
      0.5
    ),
  }
})

let cssGalleryDialogVars = computed(() => {
  return {
    '--am-c-fcip-bgr': amColors.value.colorMainBgr,
    '--am-c-fcip-text': amColors.value.colorMainText,
    '--am-c-fcip-success': amColors.value.colorSuccess,
    '--am-c-fcip-primary': amColors.value.colorPrimary,
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
  name: 'CategoryPackage',
  key: 'categoryPackage',
}
</script>

<style lang="scss">
@import '../../../../../src/assets/scss/common/quill/_quill-mixin.scss';

#amelia-app-backend-new {
  // am-    amelia
  // -c-    color
  // -fcip-  form category item package
  // -bgr   background
  #amelia-container {
    .am-fcip {
      --am-c-fcip-header-text: var(--am-c-main-heading-text);
      --am-c-fcip-bgr: var(--am-c-main-bgr);
      --am-c-fcip-text: var(--am-c-main-text);
      --am-c-fcip-success: var(--am-c-success);
      --am-c-fcip-primary: var(--am-c-primary);
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

            .am-fcip__header-text {
              width: 100%;
              justify-content: space-between;
              margin-bottom: 12px;
            }

            .am-fcip__header-action {
              width: 100%;
              justify-content: space-between;
            }

            &.am-mobile {
              .am-fcip__header {
                &-text {
                  flex-wrap: wrap;
                }

                &-name {
                  width: 100%;
                }

                &-action {
                  flex-wrap: wrap;
                }

                &-discount, &-price, &-tax {
                  margin-bottom: 6px;
                }

                &-btn {
                  width: 100%;
                  .am-button {
                    width: 100%;
                    margin-top: 6px;
                  }
                }
              }

              .am-fcip__badge {
                margin-left: 0;
              }
            }
          }
        }

        &-text {
          display: flex;
          padding-right: 12px;
          align-items: center;
        }

        &-name {
          display: inline-flex;

          span  {
            display: -webkit-box;
            font-size: 28px;
            font-weight: 500;
            line-height: 1.2;
            color: var(--am-c-fcip-header-text);
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
          }
        }

        &-action {
          display: flex;
          align-items: center;
          justify-content: flex-end;
          flex: 0 0 auto;
        }

        &-discount {
          display: inline-flex;
          align-items: center;
          height: 28px;
          padding: 0 12px 0;
          border-radius: 14px;
          font-size: 18px;
          font-weight: 500;
          margin: 0 12px 0 0;
          color: var(--am-c-fcip-success);
          background-color: var(--am-c-fcip-success-op20);
        }

        &-price {
          font-size: 18px;
          font-weight: 500;
          margin-right: 20px;
          color: var(--am-c-fcip-primary);
        }

        &-tax {
          display: inline-flex;
          align-items: center;
          height: 28px;
          padding: 0 12px 0;
          border-radius: 14px;
          font-size: 18px;
          font-weight: 500;
          margin: 0 12px 0 0;
          color: var(--am-c-fcip-primary);
          background-color: var(--am-c-fcip-primary-op20);
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

        &.am-package {
          background: linear-gradient(95.75deg, var(--am-c-fcip-bgr) -110.8%,  var(--am-c-warning) 114.33%);
          span {
            color: var(--am-c-fcip-bgr);
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
            color: var(--am-c-fcip-text-op80);
            line-height: 1.2;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;

            &[class*="am-icon"] {
              flex: 0 0 auto;
              font-size: 24px;
              color: var(--am-c-fcip-primary);
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
            --am-c-btn-border: var(--am-c-fcip-btn-op50);

            &:not(.is-disabled) {
              &:hover {
                --am-c-btn-bgr: var(--am-c-btn-second);
                --am-c-btn-text: var(--am-c-btn-first);
                --am-c-btn-border: var(--am-c-fcip-btn-op50);
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
          color: var(--am-c-fcip-text);
          padding: 12px 16px;
          cursor: pointer;

          &:hover {
            color: var(--am-c-fcip-primary);
          }

          &.am-active {
            color: var(--am-c-fcip-primary);
            border-bottom: 3px solid var(--am-c-fcip-primary);
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
            background-color: var(--am-c-fcip-bgr);

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
            color: var(--am-c-fcip-text-op80);

            * {
              font-size: 15px;
              line-height: 2;
              word-break: break-word;
              white-space: break-spaces;
              color: var(--am-c-fcip-text-op80);
            }

            a {
              color: var(--am-c-fcip-primary);
            }

            &.ql-description {
              @include quill-styles;
            }
          }
        }

        &-employee {
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
            color: var(--am-c-fcip-bgr);
            font-size: 18px;
            font-weight: 500;
          }

          &__name {
            font-size: 15px;
            font-weight: 500;
            color: var(--am-c-fcip-text);
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
            line-height: 1;
            color: var(--am-c-fcip-primary);
            background-color: var(--am-c-fcip-primary-op20);
            padding: 0 8px;
            border-radius: 12px;
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
        &-wrapper {
          padding: 12px;
          background-color: var(--am-c-fcip-text-op03);
          border-radius: 8px;

          .am-collapse-item {
            background-color: var(--am-c-fcip-bgr);

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
        }

        &-heading {
          display: flex;
          align-items: center;

          &__text {
            display: inline-flex;
            font-size: 14px;
            font-weight: 500;
            color: var(--am-c-fcip-text);
          }
        }

        &-footer {
          display: flex;
          align-items: center;

          &__text {
            display: inline-flex;
            font-size: 13px;
            font-weight: 400;
            color: var(--am-c-fcip-text-op60);
          }
        }

        &-service {
          display: flex;
          align-items: center;
          font-size: 15px;
          font-weight: 500;
          line-height: 1.6;
          /* $shade-900 */
          color: var(--am-c-fcip-text);

          &__img {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            width: 54px;
            height: 54px;
            color: var(--am-c-fcip-bgr);
            font-size: 18px;
            font-weight: 500;
            line-height: 1;
            border-radius: 4px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0 8px 0 0;
          }

          &__employee {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            color: var(--am-c-fcip-bgr);
            font-size: 12px;
            font-weight: 500;
            line-height: 1;
            border-radius: 50%;
            border: 3px solid var(--am-c-fcip-bgr);
            margin: 0 -12px 0 0;
          }

          &__info {
            & > span:first-child {
              font-size: 13px;
              font-weight: 400;
              line-height: 1.384615;
              /* $shade-700 */
              color: var(--am-c-fcip-text-op80);
              margin-right: 20px;
            }

            & > .am-fcip__include-service__info-name {
              display: inline-flex;
              align-items: center;
              justify-content: center;
              width: 36px;
              height: 36px;
              vertical-align: middle;
              font-size: 13px;
              font-weight: 500;
              color: var(--am-c-fcip-bgr);
              margin-left: -10px;
              border-radius: 50%;
              border: 2px solid var(--am-c-fcip-bgr);
              background-color: var(--am-c-fcip-bgr);
              background-size: cover;
              background-position: center;
              background-repeat: no-repeat;
            }

            & > p {
              font-size: 15px;
              font-weight: 400;
              line-height: 1.6;
              /* $shade-700 */
              color: var(--am-c-fcip-text-op80);
              margin: 16px 0 0;

              & * {
                color: var(--am-c-fcip-text-op80)
              }
            }

            & .am-fcip__include-service__info-description {
              &.ql-description {
                @include quill-styles;
              }
            }
          }
        }
      }
    }
  }

  &.amelia-v2-booking-dialog {
    --am-c-fcip-header-text: var(--am-c-main-heading-text);
    --am-c-fcip-bgr: var(--am-c-main-bgr);
    --am-c-fcip-text: var(--am-c-main-text);
    --am-c-fcip-success: var(--am-c-success);
    --am-c-fcip-primary: var(--am-c-primary);

    .el-overlay-dialog {
      background-color: rgba(26, 44, 55, 0.5);
    }

    .el-dialog {
      max-width: var(--el-dialog-width);
      width: 100%;
      border-radius: 8px;

      &__header {
        padding: 0;
      }

      &__headerbtn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 19px;
        height: 19px;
        background-color: var(--am-c-fcip-bgr);
        color: var(--am-c-fcip-text);
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
}

// - gdp - gallery dialog package
.amelia-v2-booking.amelia-v2-gdp {
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
      background-color: var(--am-c-fcip-bgr);
      color: var(--am-c-fcip-text);
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
    border-radius: 8px;
    background-color: var(--am-c-fcip-bgr);
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
        color: var(--am-c-fcip-text);
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
      color: var(--am-c-fcip-text);
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
          background-color: var(--am-c-fcip-primary);
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
