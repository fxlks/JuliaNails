<template>
  <div
    :style="cssVars"
    class="am-fs__main-content am-fs__congrats"
    :class="[{ 'am-fs-sb-atc': checkScreen }, props.globalClass]"
  >
    <div class="am-fs__congrats-main">
      <img :src="baseUrls.wpAmeliaPluginURL+'/v3/src/assets/img/congratulations/congratulations.svg'" :alt="labelsDisplay('congratulations')" />
      <p class="am-fs__congrats-main-heading">
        {{ labelsDisplay('congratulations') }}
      </p>
      <span v-if="bookableType === 'appointment'"
        >{{ labelsDisplay('appointment_id') }} #981543</span
      >

      <AddToCalendar
        v-if="checkScreen"
        :calendar-string="labelsDisplay('add_to_calendar')"
        class="am-fs__congrats-main-atc"
      ></AddToCalendar>
    </div>

    <div
      class="am-fs__congrats-info"
      :class="{ 'am-fs__congrats-info-mobile': checkScreen }"
    >
      <div class="am-fs__congrats-info-customer">
        <component :is="componentTypes[bookableType]"></component>
        <div>
          <span> {{ labelsDisplay('congrats_total_amount') }}: </span>
          <span> 1081.00$ - On-site </span>
        </div>
        <div class="am-fs__congrats-info-customer-border">
          <span>{{ labelsDisplay('your_name_colon') }}:</span>
          <span>Jon Doe</span>
        </div>
        <div>
          <span>{{ labelsDisplay('email_address_colon') }}:</span>
          <span>support@wpamelia.com</span>
        </div>
        <div>
          <span>{{ labelsDisplay('phone_number_colon') }}:</span>
          <span>0000000000</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import AddToCalendar from '../parts/AddToCalendar.vue'
import AppointmentInfoService from '../parts/AppointmentInfoService.vue'
import PackageInfoService from '../parts/PackageInfoService.vue'
import { computed, inject, markRaw } from 'vue'
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation'
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

let props = defineProps({
  globalClass: {
    type: String,
    default: '',
  },
})

let baseUrls = inject('baseUrls')

let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value].congratulations
      .translations &&
      amCustomize.value[pageRenderKey.value].congratulations.translations[
        label
      ] &&
      amCustomize.value[pageRenderKey.value].congratulations.translations[
        label
      ][langKey.value]
      ? amCustomize.value[pageRenderKey.value].congratulations.translations[
          label
        ][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

let bookableType = inject('bookableType')
const componentTypes = {
  appointment: markRaw(AppointmentInfoService),
  package: markRaw(PackageInfoService),
}

let cWidth = inject('containerWidth', 0)
let checkScreen = computed(() => cWidth.value < 540)

let amColors = inject('amColors')

const cssVars = computed(() => {
  if (checkScreen.value) {
    return {
      '--am-c-atc-text-op40': useColorTransparency(
        amColors.value.colorMainText,
        0.4
      ),
      '--am-c-atc-heading-text-op40': useColorTransparency(
        amColors.value.colorSbText,
        0.4
      ),
      '--am-c-atc-text-op30': useColorTransparency(
        amColors.value.colorMainText,
        0.3
      ),
      '--am-c-atc-text': amColors.value.colorMainText,
      '--am-c-atc-heading-text': amColors.value.colorSbText,
    }
  } else {
    return {
      '--am-c-atc-text-op40': useColorTransparency(
        amColors.value.colorMainText,
        0.4
      ),
      '--am-c-atc-heading-text-op40': useColorTransparency(
        amColors.value.colorMainHeadingText,
        0.4
      ),
      '--am-c-atc-text-op30': useColorTransparency(
        amColors.value.colorMainText,
        0.3
      ),
      '--am-c-atc-text': amColors.value.colorMainText,
      '--am-c-atc-heading-text': amColors.value.colorMainHeadingText,
    }
  }
})
</script>

<script>
export default {
  name: 'CongratulationsStep',
  key: 'congratulations',
  sidebarData: {
    label: 'congratulations',
    icon: 'pennant',
    selected: true,
    finished: true,
  },
}
</script>

<style lang="scss">
#amelia-app-backend-new #amelia-container {
  .am-fs__main-content.am-fs__congrats {
    padding: 16px 16px 16px;
    height: calc(100% - 56px);
    margin-top: 0;
  }

  .am-fs-sb-atc {
    --am-c-sb-bgr-atc: var(--am-c-sb-bgr);
    background-image: linear-gradient(
      var(--am-c-sb-bgr-atc) 80%,
      transparent 20%
    );
  }

  .am-fs__congrats {
    &-main {
      margin-top: 22px;
      display: flex;
      flex-direction: column;
      align-items: center;
      font-family: var(--am-font-family);

      & > * {
        $count: 5;
        @for $i from 0 through $count {
          &:nth-child(#{$i + 1}) {
            animation: 600ms
              cubic-bezier(0.45, 1, 0.4, 1.2)
              #{$i *
              100}ms
              am-animation-slide-up;
            animation-fill-mode: both;
          }
        }
      }

      & img {
        width: 54px;
        margin-bottom: 8px;
      }
      &-heading {
        margin-bottom: 2px;
        font-weight: 500;
        font-size: 18px;
        line-height: 28px;
        color: var(--am-c-atc-heading-text);
      }
      &-atc {
        margin-top: 16px;
      }
      & span {
        color: var(--am-c-atc-heading-text-op40);
        font-weight: 400;
        font-size: 13px;
        line-height: 18px;
      }
    }
    &-info-mobile {
      padding: 16px;
      margin-top: 24px;
      border-radius: 6px;
      box-shadow: 0 2px 3px 2px rgba(26, 44, 55, 0.1);
    }
    &-info {
      --am-c-atc-main-bgr: var(--am-c-main-bgr);
      background-color: var(--am-c-atc-main-bgr);
      &-customer {
        &-border {
          border-top: 1px solid var(--am-c-atc-text-op30);
          padding-top: 16px;
        }

        & > div {
          $count: 20;
          @for $i from 0 through $count {
            &:nth-child(#{$i + 1}) {
              animation: 600ms
                cubic-bezier(0.45, 1, 0.4, 1.2)
                #{$i *
                100}ms
                am-animation-slide-up;
              animation-fill-mode: both;
            }
          }
        }

        & div {
          display: flex;
          justify-content: space-between;
          font-weight: 400;
          font-size: 14px;
          margin-bottom: 12px;
          line-height: 20px;
          & span {
            color: var(--am-c-atc-text-op40);
          }
          & span:nth-child(2) {
            color: var(--am-c-atc-text);
          }
        }
      }
    }
  }

  .am-skeleton-congratz {
    &-heading {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 13px;

      :first-child {
        width: 52px;
        height: 52px;
        margin-bottom: 9px;
      }

      :nth-child(2) {
        height: 28px;
        max-width: 132px;
        margin-bottom: 4px;
      }

      :last-child {
        max-width: 148px;
        height: 18px;
      }
    }

    &-booking-info {
      & > div {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;

        .el-skeleton__item {
          height: 20px;
        }
      }

      .am-customer-info {
        border-bottom: 1px solid #d1d5d7;
        padding-bottom: 12px;
      }
    }
  }
}
</style>
