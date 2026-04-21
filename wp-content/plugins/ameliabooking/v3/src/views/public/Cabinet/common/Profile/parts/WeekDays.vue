<template>
  <div
    class="am-caewh"
    :style="cssVars"
  >
    <div
      v-for="(weekDay, weekDayIndex) in employee.weekDayList"
      :key="weekDayIndex"
      class="am-caewh__item"
    >
      <div class="am-caewh__header">
        <div class="am-caewh__header-text">
          {{ weekDays[weekDayIndex].label }}
        </div>

        <AmSwitch
          v-model="weekDay.enabled"
          size="small"
          :aria-label="weekDays[weekDayIndex].label"
        />
      </div>

      <div class="am-caewh__content">
        <Periods
          :title="amLabels.working_hours"
          :enabled="weekDay.enabled"
          :index="weekDayIndex"
          :type="'weekDayList'"
          :responsive-class="props.responsiveClass"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  inject,
  computed,
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * _components
import AmSwitch from "../../../../../_components/switch/AmSwitch.vue";

// * Dedicated components
import Periods from "./Periods.vue";

// * Composables
import { useColorTransparency } from "../../../../../../assets/js/common/colorManipulation";

let props = defineProps({
  responsiveClass: {
    type: String,
    default: '',
  }
})

// * Store
let store = useStore()

// * Labels
let amLabels = inject('amLabels')

let employee = computed(() => {
  return store.getters['employee/getEmployee']
})

let weekDays = ref([
  {
    label: amLabels.weekday_monday,
    value: 'monday'
  },
  {
    label: amLabels.weekday_tuesday,
    value: 'tuesday'
  },
  {
    label: amLabels.weekday_wednesday,
    value: 'wednesday'
  },
  {
    label: amLabels.weekday_thursday,
    value: 'thursday'
  },
  {
    label: amLabels.weekday_friday,
    value: 'friday'
  },
  {
    label: amLabels.weekday_saturday,
    value: 'saturday'
  },
  {
    label: amLabels.weekday_sunday,
    value: 'sunday'
  }
])

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-caewh-primary': amColors.value.colorPrimary,
    '--am-c-caewh-bgr': amColors.value.colorMainBgr,
    '--am-c-caewh-text': amColors.value.colorMainText,
    '--am-c-caewh-text-op03': useColorTransparency(amColors.value.colorMainText, 0.03),
    '--am-c-caewh-bgr-op03': useColorTransparency(amColors.value.colorInpBgr, 0.03),
  }
})
</script>

<script>
export default {
  name: 'WeekDays'
}
</script>

<style lang="scss">
@mixin am-cabinet-employee-weekdays {
  //am    - amelia
  //caewh - cabinet employee working hours
  .am-caewh {
    display: flex;
    flex-direction: column;
    gap: 8px;

    &__item {
      padding: 0;
      border-radius: 8px;
      background-color: var(--am-c-caewh-text-op03);
    }

    &__header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 24px;
      gap: 8px;

      &-text {
        color: var(--am-c-caewh-text);
      }
    }

    &__content {
      display: flex;
      padding: 0 24px;
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-employee-weekdays;
}
</style>
