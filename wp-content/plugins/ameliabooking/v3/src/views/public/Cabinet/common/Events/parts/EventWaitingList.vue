<template>
  <div
    class="am-capei-waiting"
    :style="cssVars"
  >
    <div class="am-capei-waiting__header">
      <div class="am-capei-waiting__header-text">
        {{ amLabels.events_show_waiting_list_booking_full }}
      </div>
      <Am-Switch
        v-model="waitingListFormData.enabled"
        :aria-label="amLabels.events_show_waiting_list_booking_full"
      />
    </div>

    <!-- WaitingList form -->
    <el-form
      v-if="waitingListFormData.enabled"
      ref="waitingListFormRef"
      :model="waitingListFormData"
      label-position="top"
    >
      <el-form-item
        v-if="!customPricing || maxCustomCapacityEnabled"
        :label="`${amLabels.maximum_capacity}`"
        class="am-capei-waiting__item"
        :class="responsiveClass"
      >
        <AmInputNumber
          v-model="waitingListFormData.maxCapacity"
          :min="1"
        />
      </el-form-item>
      <template v-if="customPricing">
        <template v-for="(item, index) in tickets" :key="index">
          <el-form-item
            :label="item.name"
            class="am-capei-waiting__item"
            :class="responsiveClass"
          >
            <AmInputNumber
              v-model="item.waitingListSpots"
              :min="1"
              :disabled="maxCustomCapacityEnabled"
            />
          </el-form-item>
        </template>
      </template>

      <div class="am-capei-waiting__header">
        <div class="am-capei-waiting__header-text">
          {{ amLabels.limit_extra_people }}
        </div>
        <Am-Switch
          v-model="waitingListFormData.maxExtraPeopleEnabled"
          :aria-label="amLabels.limit_extra_people"
        />
      </div>

      <el-form-item
        v-if="waitingListFormData.maxExtraPeopleEnabled"
        :class="responsiveClass"
      >
        <AmInputNumber
          v-model="waitingListFormData.maxExtraPeople"
          :min="0"
          :disabled="!waitingListFormData.maxExtraPeopleEnabled"
        />
      </el-form-item>
    </el-form>
    <!-- /WaitingList form -->
  </div>
</template>

<script setup>
// * Import from Vue
import { ref, inject, computed } from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Import Composables
import { useColorTransparency } from "../../../../../../assets/js/common/colorManipulation";
import { useResponsiveClass } from "../../../../../../assets/js/common/responsive";

// * Import Parts
import AmInputNumber from "../../../../../_components/input-number/AmInputNumber.vue";
import AmSwitch from "../../../../../_components/switch/AmSwitch.vue";

// * Props
let props = defineProps({
  pageWidth: {
    type: Number,
    default: 0,
  },
  waitingListUntil: {
    type: Object,
    default: null,
  },
});

let responsiveClass = computed(() => {
  return useResponsiveClass(props.pageWidth)
});

// * Store
const store = useStore()

// * Labels
let amLabels = inject('amLabels')

/********
 * Form *
 ********/
let waitingListFormRef = ref(null)

let waitingListFormData = ref({
  enabled: computed({
    get: () => store.getters['event/getWaitingListEnabled'],
    set: (val) => {
      store.commit('event/setWaitingListEnabled', val)
    }
  }),
  maxCapacity: computed({
    get: () => store.getters['event/getWaitingListMaxCapacity'],
    set: (val) => {
      store.commit('event/setWaitingListMaxCapacity', val)
    }
  }),
  maxExtraPeopleEnabled: computed({
    get: () => store.getters['event/getWaitingListMaxExtraPeopleEnabled'],
    set: (val) => {
      store.commit('event/setWaitingListMaxExtraPeopleEnabled', val)
    }
  }),
  maxExtraPeople: computed({
    get: () => store.getters['event/getWaitingListMaxExtraPeople'],
    set: (val) => {
      store.commit('event/setWaitingListMaxExtraPeople', val)
    }
  }),
})

let tickets = computed(() => store.getters['event/getCustomTickets'])

let customPricing = computed(() => store.getters['event/getCustomPricing'])

let maxCustomCapacityEnabled = computed(() => store.getters['event/getMaxCustomCapacityEnabled'])

// * Expose
defineExpose({
  waitingListFormRef
})

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-capei-waiting-text': amColors.value.colorMainText,
    '--am-c-capei-waiting-text-op03': useColorTransparency(amColors.value.colorMainText, 0.03),
    '--am-c-capei-waiting-inp-bgr-op03': useColorTransparency(amColors.value.colorInpBgr, 0.03),
    '--am-c-capei-waiting-bgr-op05': useColorTransparency(amColors.value.colorPrimary, 0.05),
  }
})
</script>

<style lang="scss">
@mixin cabinet-event-waiting-list-block {
  .am-capei-waiting {
    display: flex;
    flex-direction: column;
    gap: 16px;
    width: 100%;

    &__header {
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      width: 100%;

      &-text {
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        color: var(--am-c-capei-waiting-text);
      }
    }

    &__item {
      &.el-form-item {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 8px;

        .el-form-item{
          &__content {
            width: auto;
            justify-content: flex-end;
          }
        }

        &.am-rw- {
          &520 {
            .el-form-item{
              &__label {
                width: 100%;
                justify-content: flex-start;
              }

              &__content {
                width: 100%;
                justify-content: flex-start;
              }
            }
          }
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include cabinet-event-waiting-list-block;
}
</style>