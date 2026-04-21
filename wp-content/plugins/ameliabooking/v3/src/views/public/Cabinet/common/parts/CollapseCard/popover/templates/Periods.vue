<template>
  <div
    class="am-cc__periods"
    :class="responsiveClass"
  >
    <div
      v-for="(item, index) in props.data"
      :key="index"
      class="am-cc__periods-item"
    >
      <span class="am-cc__periods-date">
        {{ getFrontedFormattedDate(item.date) }}
      </span>

      <span class="am-cc__periods-time">
        {{ `${getFrontedFormattedTime(item.startTime)} - ${getFrontedFormattedTime(item.endTime)}` }}
      </span>

      <div
        v-if="
          (amSettings.featuresIntegrations.googleCalendar.enabled && item.googleMeetLink) ||
          (amSettings.featuresIntegrations.zoom.enabled && item.zoomLink) ||
          (amSettings.featuresIntegrations.lessonSpace.enabled && item.lessonSpaceLink) ||
          (amSettings.featuresIntegrations.outlookCalendar.enabled && item.microsoftTeamsLink)
        "
        class="am-cc__periods-link__list"
      >
        <!-- GoogleMeet Link -->
        <a v-if="amSettings.featuresIntegrations.googleCalendar.enabled && item.googleMeetLink" class="am-cc__periods-link" :href="item.googleMeetLink" target="_blank">
          <span class="am-icon-link"></span>
          {{ amLabels.google_meet_link }}
        </a>
        <!-- /GoogleMeet link -->

        <!-- Microsoft Teams Link -->
        <a v-if="amSettings.featuresIntegrations.outlookCalendar.enabled && item.microsoftTeamsLink" class="am-cc__periods-link" :href="item.microsoftTeamsLink" target="_blank">
          <span class="am-icon-link"></span>
          {{ amLabels.microsoft_teams_link }}
        </a>
        <!-- /Microsoft Teams link -->

        <!-- Zoom Link -->
        <a v-if="amSettings.featuresIntegrations.zoom.enabled && item.zoomLink" class="am-cc__periods-link" :href="item.zoomLink" target="_blank">
          <span class="am-icon-link"></span>
          {{ amLabels.zoom_link }}
        </a>
        <!-- /Zoom link -->

        <!-- Lesson Space Link -->
        <a v-if="amSettings.featuresIntegrations.lessonSpace.enabled && item.lessonSpaceLink" class="am-cc__periods-link" :href="item.lessonSpaceLink" target="_blank">
          <span class="am-icon-link"></span>
          {{ amLabels.lesson_space_link }}
        </a>
        <!-- /Lesson Space Link -->
      </div>
    </div>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  computed,
  inject
} from "vue";

// * Composables
import { useResponsiveClass } from "../../../../../../../../assets/js/common/responsive.js";
import { getFrontedFormattedDate, getFrontedFormattedTime } from "../../../../../../../../assets/js/common/date";

// * Vars
const amLabels = inject('amLabels')

// * Settings
const amSettings = inject('settings')

let props = defineProps({
  data: {
    type: [Array, Object, String]
  }
})

let cWidth = inject('containerWidth')

let responsiveClass = computed(() => {
  return useResponsiveClass(cWidth.value)
})
</script>

<script>
export default {
  name: "TemplatePeriods"
}
</script>

<style lang="scss">
@mixin popover-template-periods {
  .am-cc__periods {
    width: 340px;

    &.am-w-420 {
      width: 100%;
    }

    &-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 0 0 4px;

      &:last-child {
        margin: 0;
      }
    }

    &-date, &-time {
      display: inline-flex;
      font-size: 14px;
      font-weight: 400;
      line-height: 1.428571429;
      color: var(--am-c-cc-text);
    }

    &-link {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      cursor: pointer;
      font-size: 14px;
      color: var(--am-c-cc-primary);
      text-decoration: none;

      &:hover {
        color: var(--am-c-cc-primary-op70);

        [class^="am-icon"] {
          color: var(--am-c-cc-primary-op70);
        }
      }

      [class^="am-icon"] {
        font-size: 18px;
        color: var(--am-c-cc-primary);
      }

      &__list {
        display: block;
      }
    }
  }
}

.el-popover {
  @include popover-template-periods;
}
</style>
