<template>
  <div
    v-if="!empty"
    ref="ameliaContainer"
    class="am-fcl"
    role="main"
    :style="cssVars"
  >
    <template v-for="category in categoriesList" :key="category.id">
      <div
        class="am-fcl__item"
        role="group"
        :class="itemWidth"
      >
        <div class="am-fcl__item-inner">
          <div
            class="am-fcl__item-content"
            :style="customizedOptions.cardSideColor.visibility
            ? {
              borderLeft: '7px solid',
              borderLeftColor: category.color
            }
            : {}"
          >
            <div
              class="am-fcl__item-heading"
              :style="customizedOptions.cardColor && customizedOptions.cardColor.visibility
              ? {
                backgroundColor: useColorTransparency(category.color, 0.1)
              } : {}"
            >
              <div
                v-if="category.pictureFullPath"
                :style="{backgroundImage: `url(${category.pictureFullPath})`}"
                class="am-fcl__item-hero"
              >
              </div>
              <div class="am-fcl__item-name">
                {{ category.name }}
              </div>
              <div class="am-fcl__item-segments">
                <div
                  v-if="(!preselected.show || preselected.show === 'services') && customizedOptions.services.visibility"
                  class="am-fcl__item-segments__item"
                >
                  <span class="am-fcl__item-segments__item-icon am-icon-service"></span>
                  <span class="am-fcl__item-segments__item-count">
                    {{ useAvailableServiceIdsInCategory(shortcodeData, category, amEntities).length }}
                  </span>
                </div>
                <div
                  v-if="(!preselected.show || preselected.show === 'packages') && category.packageList.length && customizedOptions.packages.visibility"
                  class="am-fcl__item-segments__item"
                >
                  <span class="am-fcl__item-segments__item-icon am-icon-shipment"></span>
                  <span class="am-fcl__item-segments__item-count">
                    {{ category.packageList.length }}
                  </span>
                </div>
              </div>
            </div>
            <div class="am-fcl__item-footer">
              <AmButton
                class="am-fcl__item-btn"
                size="mini"
                category="secondary"
                :type="customizedOptions.cardButton.buttonType"
                :suffix="IconArrowRight"
                @click="chooseCategory(category.id)"
              >
                {{ amLabels.view_all }}
              </AmButton>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
  <div
    v-else
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

<script setup>
import AmButton from '../../../_components/button/AmButton.vue'
import IconArrowRight from "../../../_components/icons/IconArrowRight";

// * Vue
import {
  ref,
  computed,
  inject,
  reactive, onMounted
} from "vue";

// * Store
import { useStore } from "vuex";

// * Composables
import {
  useAvailableServiceIdsInCategory
} from '../../../../assets/js/public/catalog.js'
import {
  useColorTransparency
} from '../../../../assets/js/common/colorManipulation.js'
import useAction from "../../../../assets/js/public/actions";

// * Root Urls
const baseUrls = inject('baseUrls')

// * Component reference
let ameliaContainer = ref(null)

// * Plugin wrapper width
let containerWidth = ref(0)

// * window resize listener
window.addEventListener('resize', resize);

// * resize function
function resize() {
  if (ameliaContainer.value) {
    containerWidth.value = ameliaContainer.value.offsetWidth
  }
}

onMounted(() => {
  if (ameliaContainer.value) {
    containerWidth.value = ameliaContainer.value.offsetWidth
  }
})

let itemWidth = computed(() => {
  if (containerWidth.value <= 500) {
    return 'am-w100'
  }

  if (containerWidth.value <= 600) {
    return 'am-w50'
  }

  if (containerWidth.value <= 768) {
    return 'am-w33'
  }

  return ''
})

// * Step functionality
let { nextPage } = inject('changingPageFunctions', {
  nextPage: () => {}
})

// * Root Settings
const amSettings = inject('settings')

// * Store
let store = useStore()

// * Entities
let amEntities = inject('amEntities')

// * Customized data
let customizedDataForm = inject('customizedDataForm')

// * Customized options
let customizedOptions = computed(() => {
  return customizedDataForm.value.categoriesList.options
})

// * Shortcode
const shortcodeData = inject('shortcodeData')
const preselected = computed(() => store.getters['entities/getPreselected'])

let availableCategories = inject('availableCategories')
let categoriesList = computed(() => availableCategories.value)

let categorySelected = inject('categorySelected')
function chooseCategory (id) {
  categorySelected.value = id
  store.commit('booking/setCategoryId', parseInt(id))

  useAction(store, {}, 'SelectCategory', 'appointment', null, null)

  nextPage()
}

// * Empty state
let empty = computed(() => {
  return categoriesList.value.length === 0
})

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  if (amSettings.customizedData && amSettings.customizedData.cbf && amSettings.customizedData.cbf.categoriesList.translations) {
    let customizedLabels = amSettings.customizedData.cbf.categoriesList.translations
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

// * Colors
let amColors = inject('amColors')
let cssVars = computed(() => {
  return {
    '--am-c-fcl-card-text-op80': useColorTransparency(amColors.value.colorCardText, 0.8)
  }
})
</script>

<script>
export default {
  name: "CategoriesList",
  key: "categoriesList"
}
</script>

<style lang="scss">
.amelia-v2-booking #amelia-container.am-fc__wrapper {
  .am-fcl {
    // -fcl- form category list
    // -ff- font family
    --am-c-fcl-primary: var(--am-c-primary);
    --am-c-fcl-bgr: var(--am-c-main-bgr);
    --am-c-fcl-card-bgr: var(--am-c-card-bgr);
    --am-c-fcl-card-border: var(--am-c-card-border);
    --am-c-fcl-card-text: var(--am-c-card-text);
    --am-ff-fcl: var(--am-font-family);
    display: flex;
    align-items: unset;
    justify-content: center;
    flex-wrap: wrap;
    width: 100%;
    background-color: var(--am-c-main-bgr);
    border-radius: 12px;

    * {
      font-family: var(--am-ff-fcl);
      box-sizing: border-box;
    }

    &__item {
      max-width: 25%;
      width: 100%;
      display: flex;
      background-color: transparent;
      padding: 8px;

      &.am-w33 {
        max-width: 33.33333%;
      }

      &.am-w50 {
        max-width: 50%;
      }

      &.am-w100 {
        max-width: 100%;
      }

      &-inner {
        display: flex;
        width: 100%;
        background-color: transparent;
        border: 1px solid var(--am-c-fcl-card-border);
        border-radius: 8px;
        overflow: hidden;
      }

      &-content {
        position: relative;
        display: flex;
        width: 100%;
        background-color: var(--am-c-fcl-card-bgr);
        padding: 0 0 52px;
      }

      &-hero {
        padding: 56.25% 0 0;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        border-radius: 4px;
        margin: 0 0 8px;
      }

      &-heading {
        width: 100%;
        padding: 12px;
      }

      &-name {
        width: 100%;
        font-family: var(--am-ff-fcl);
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        color: var(--am-c-fcl-card-text);
        margin: 0 0 12px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      &-segments {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        flex-wrap: wrap;

        &__item {
          display: flex;
          align-items: center;
          height: 18px;

          &:first-child {
            margin-right: 4px;
          }

          &-icon {
            font-size: 24px;
            color: var(--am-c-fcl-primary);
          }

          &-count {
            font-family: var(--am-ff-fcl);
            font-size: 13px;
            font-weight: 400;
            line-height: 1.3846;
            color: var(--am-c-fcl-card-text-op80);
          }
        }
      }

      &-footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        padding: 12px;
      }

      &-btn {
        width: 100%;
      }
    }
  }
}
</style>
