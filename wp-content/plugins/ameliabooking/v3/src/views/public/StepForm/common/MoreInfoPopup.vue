<template>
  <AmSlidePopup
    v-if="props.visibility"
    :visibility="props.visibility"
    class="am-fs-iipu"
    :footer-visibility="false"
    :custom-css="cssVars"
    @update:visibility="(value) => emits('update:visibility', value)"
  >
    <template #header>
      <div class="am-fs-iipu__header">
        {{ props.heading }}
      </div>
    </template>
    <div class="am-fs-iipu__content">
      <div class="am-fs-iipu__heading">
        <img
          :src="usePictureLoad(baseUrls, props.item, props.isPerson)"
          :alt="props.itemName"
        />
        <div class="am-fs-iipu__name">{{ props.itemName }}</div>
      </div>

      <!-- EMPLOYEES -->
      <div
        v-if="props.employeesData.length"
        class="am-fs-iipu__employee"
      >
        <div class="am-fs-iipu__employee-label">
          {{ `${props.employeesHeading}:` }}
        </div>
        <div
          class="am-fs-iipu__employee-wrapper"
          @click="expandEmployeesData()"
        >
          <img
            v-for="employee in props.employeesData"
            :key="employee.id"
            class="am-fs-iipu__employee-img"
            :src="usePictureLoad(baseUrls, employee, true)"
            :alt="`${employee.firstName} ${employee.lastName}`"
          />
        </div>
        <div
          v-if="expandEmployees"
          class="am-fs-iipu__employee-display__wrapper"
        >
          <div
            v-for="employee in props.employeesData"
            :key="employee.id"
            class="am-fs-iipu__employee-display"
          >
            <img
              :src="usePictureLoad(baseUrls, employee, true)"
              :alt="`${employee.firstName} ${employee.lastName}`"
            />

            <div class="am-fs-iipu__employee-display-name">
              {{ employee.firstName }} {{ employee.lastName }}
            </div>

            <div class="am-fs-iipu__employee-display-price">
              {{ employee.price }}
            </div>
          </div>
        </div>
      </div>
      <!-- /EMPLOYEES -->

      <!-- LOCATIONS -->
      <div
        v-if="props.locationsData.length"
        class="am-fs-iipu__location"
      >
        <div class="am-fs-iipu__location-label">
          {{ `${props.locationsHeading}:` }}
        </div>
        <div class="am-fs-iipu__location-wrapper">
          <div
            v-for="location in props.locationsData"
            :key="location.id"
            class="am-fs-iipu__location-inner"
          >
          <span>
            {{ location.name }}
            <template v-if="location.address"> - </template>
          </span>
            <a
              v-if="location.address"
              class="am-cc__data-text"
              :href="`https://maps.google.com/?q=${location.address}`"
              target="_blank"
              tabindex="-1"
            >
              {{ location.address }}
            </a>
          </div>
        </div>
      </div>
      <!-- /LOCATIONS -->

      <!-- DESCRIPTION -->
      <div
        v-if="useDescriptionVisibility(props.item.description)"
        class="am-fs-iipu__description ql-description"
        :style="props.plainText ? 'white-space: pre-wrap' : ''"
        v-html="props.item.description"
      />
      <!-- /DESCRIPTION -->
    </div>
  </AmSlidePopup>
</template>

<script setup>
// * Import from Vue
import { computed, inject, ref } from 'vue'

// * Composables
import { usePictureLoad } from '../../../../assets/js/common/image'
import { useDescriptionVisibility } from '../../../../assets/js/common/helper'
import { useColorTransparency } from '../../../../assets/js/common/colorManipulation'

// * Components
import AmSlidePopup from '../../../_components/slide-popup/AmSlidePopup.vue'

const props = defineProps({
  visibility: {
    type: Boolean,
    default: false
  },
  heading: {
    type: String,
    required: '',
  },
  item: {
    type: Object,
    required: true,
  },
  itemName: {
    type: String,
    required: true,
  },
  isPerson: {
    type: Boolean,
    default: false,
  },
  employeesHeading: {
    type: String,
    default: 'Employees',
  },
  employeesData: {
    type: Array,
    default: () => [],
  },
  locationsHeading: {
    type: String,
    default: 'Locations',
  },
  locationsData: {
    type: Array,
    default: () => [],
  },
  plainText: {
    type: Boolean,
    default: false,
  },
})

const emits = defineEmits(['update:visibility'])

// * Base URLs
const baseUrls = inject('baseUrls')

// * EMPLOYEES
let expandEmployees = ref(false)
function expandEmployeesData() {
  expandEmployees.value = !expandEmployees.value
}

const amColors = inject(
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

let cssVars = computed(() => {
  return {
    '--am-c-primary-op05': useColorTransparency(
      amColors.value.colorPrimary,
      0.05
    ),
  }
})
</script>

<style lang="scss">
@import '../../../../../src/assets/scss/common/quill/_quill-mixin.scss';

@mixin init-info-popup {
  // iipu - init info popup
  .am-fs-iipu {
    .am-slide-popup__block-header {
      margin-bottom: 16px;
    }

    &__header {
      font-size: 15px;
      font-weight: 500;
      line-height: 1.6;
      color: var(--am-c-main-heading-text);
    }

    &__content {
      display: flex;
      flex-direction: column;
      gap: 8px;
      max-height: 426px;
      overflow-x: hidden;

      // Scroll styles
      &::-webkit-scrollbar {
        width: 6px;
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

    &__heading {
      display: flex;
      align-items: center;
      gap: 8px 12px;

      img {
        width: 54px;
        height: 54px;
        border-radius: 50%;
      }
    }

    &__name {
      font-size: 15px;
      font-weight: 500;
      line-height: 1.55556;
      color: var(--am-c-main-text);
    }

    // Employee section
    &__employee {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: flex-start;
      gap: 8px;

      &-wrapper {
        display: flex;
        align-items: center;
        cursor: pointer;
      }

      &-label {
        font-size: 15px;
        font-weight: 500;
        line-height: 1.42857;
        color: var(--am-c-main-text);
        text-transform: capitalize;
      }

      &-img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        box-shadow: 0 0 0 2px var(--am-c-main-bgr);
        margin-left: -8px;

        &:first-child {
          margin-left: 0;
        }
      }

      &-display {
        width: 100%;
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 4px 8px;

        &__wrapper {
          width: 100%;
          display: flex;
          flex-direction: column;
          gap: 8px;
          border-bottom: 1px solid var(--am-c-inp-border);
          padding: 0 0 8px;
        }

        img {
          width: 34px;
          height: 34px;
          border-radius: 50%;
        }

        &-name {
          font-size: 14px;
          font-weight: 400;
          line-height: 1.42857;
          color: var(--am-c-main-text);
        }

        &-price {
          font-size: 13px;
          font-weight: 500;
          line-height: 1.38462;
          color: var(--am-c-primary);
          margin-left: auto;
        }
      }
    }

    // Location section
    &__location {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: flex-start;
      gap: 4px 0;

      &-label {
        font-size: 15px;
        font-weight: 500;
        line-height: 1.42857;
        color: var(--am-c-main-text);
        text-transform: capitalize;
      }

      &-wrapper {
        display: flex;
        flex-direction: column;
        gap: 4px;
        width: 100%;
        background-color: var(--am-c-primary-op05);
        border-radius: 6px;
        padding: 6px 12px;
      }

      &-inner {
        color: var(--am-c-main-text);
        padding: 2px 0;
        border-radius: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;

        & span {
          font-weight: 400;
          font-size: 14px;
          line-height: 24px;
        }

        & a {
          color: var(--am-c-primary);
          text-decoration: none;
          font-weight: 400;
          font-size: 14px;
          line-height: 24px;
        }
      }
    }

    // Description section
    &__description {
      color: var(--am-c-main-text);

      &.ql-description {
        @include quill-styles;
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include init-info-popup;
}

#amelia-app-backend-new #amelia-container {
  @include init-info-popup;
}
</style>
