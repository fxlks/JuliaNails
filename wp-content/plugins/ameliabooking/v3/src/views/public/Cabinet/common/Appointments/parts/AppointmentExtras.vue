<template>
  <el-collapse
    v-model="activeBookingIndex"
    class="am-capai-ext am-capai-collapse"
    accordion
    :style="cssVars"
  >
    <el-collapse-item
      v-for="(item, index) in appointmentBookings.filter((b) => b.status !== 'canceled' && b.status !== 'rejected')"
      :key="index"
      :name="index"
      class="am-capai-ext__item am-capai-collapse__item"
    >
      <template #title>
        <div
          class="am-capai-ext__heading"
          :class="{'am-pb-16': !((item.customer.email && customerEmailVisibility) || (item.customer.phone && customerPhoneVisibility))}"
        >
          <!-- Customer Name -->
          <div
            class="am-capai-customer__name"
            :class="noShowData(item.customer.noShowCount).class"
          >
            <span
              v-if="noShowData(item.customer.noShowCount).icon"
              :class="`am-icon-${noShowData(item.customer.noShowCount).icon}`"
            />
            {{ item.customer.firstName + ' ' + item.customer.lastName }}
          </div>
          <!-- /Customer Name -->

          <!-- Selected Extras Number -->
          <div
            v-if="selectedExtrasCount(item.extras) > 0"
            class="am-capai-ext__heading-extra"
          >
            {{`${amLabels.extras}: ${selectedExtrasCount(item.extras)}` }}
          </div>
          <!-- /Selected Extras Number -->
        </div>
      </template>

      <!-- Customer Info -->
      <div
        v-if="(item.customer.email && customerEmailVisibility) || (item.customer.phone && customerPhoneVisibility)"
        class="am-capai-customer__data-wrapper"
        :class="props.responsiveClass"
      >
        <!-- Customer Email -->
        <div
          v-if="item.customer.email && customerEmailVisibility"
          class="am-capai-customer__data"
          :class="props.responsiveClass"
        >
          <span class="am-icon-email"></span>
          <a :href="`mailto:${item.customer.email}`">
            {{ item.customer.email }}
          </a>
        </div>
        <!-- /Customer Email -->

        <!-- Customer Phone -->
        <div
          v-if="item.customer.phone && customerPhoneVisibility"
          class="am-capai-customer__data"
          :class="props.responsiveClass"
        >
          <span class="am-icon-phone"></span>
          <a :href="`tel:${item.customer.phone}`">
            {{ item.customer.phone }}
          </a>
        </div>
        <!-- /Customer Phone -->
      </div>
      <!-- /Customer Info -->

      <!-- Extra -->
      <div class="am-capai-ext__wrapper">
        <div
          v-for="(extra, extraIndex) in employeeService.extras"
          :key="extraIndex"
          class="am-capai-ext__inner"
          :class="[{'am-border-bottom': extraIndex !== employeeService.extras.length - 1}, props.responsiveClass]"
        >
          <div class="am-capai-ext__inner-info">
            <div class="am-capai-ext__inner-name">
              {{ extra.name }}
            </div>
            <div
              v-if="extra.duration"
              class="am-capai-ext__inner-duration"
            >
              {{ `${amLabels.duration}: ${useSecondsToDuration(extra.duration, amLabels.h, amLabels.min)}` }}
            </div>
          </div>
          <div
            class="am-capai-ext__inner-selection"
            :class="props.responsiveClass"
          >
            <div
              class="am-capai-ext__inner-price"
              :class="props.responsiveClass"
            >
              <template v-if="extra.price">
                {{ useFormattedPrice('price' in item.extras[extraIndex] ? item.extras[extraIndex].price : extra.price) }}
              </template>
              <template v-else>
                {{ amLabels.free }}
              </template>
            </div>
            <AmInputNumber
              v-model="item.extras[extraIndex].quantity"
              :class="props.responsiveClass"
              size="small"
              :min="0"
              :max="extra.maxQuantity"
              @change="changeExtraQuantity(extra, extraIndex, item)"
            />
          </div>
        </div>

        <div class="am-capai-ext__total">
          {{ `${amLabels.subtotal}: ${useFormattedPrice(getBookingExtrasSubtotal(item))}`}}
        </div>
      </div>
      <!-- /Extra -->
    </el-collapse-item>
  </el-collapse>
</template>

<script setup>
// * Import from Vue
import {
  computed,
  inject,
  onMounted,
  ref,
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Import Composables
import { useSecondsToDuration } from "../../../../../../assets/js/common/date";
import { useFormattedPrice } from "../../../../../../assets/js/common/formatting";
import { useColorTransparency } from "../../../../../../assets/js/common/colorManipulation";

// * Components
import AmInputNumber from "../../../../../_components/input-number/AmInputNumber.vue";

// * Props
const props = defineProps({
  responsiveClass: {
    type: String,
    default: "",
  },
});

// * Emits
const emits = defineEmits(["changedSlotCondition"]);

// * Store
const store = useStore();

// * Data in shortcode
const shortcodeData = inject('shortcodeData')

// * Customize data
let amCustomize = inject('amCustomize')

// * Labels
let amLabels = inject('amLabels')

// * Employee Service
let employeeService = computed(() => store.getters["appointment/getEmployeeService"]);

// * Appointment Bookings
let appointmentBookings = computed(() => store.getters["appointment/getBookings"])

let activeBookingIndex = ref(null)

function changeExtraQuantity(serviceExtra, extraIndex, booking) {
  if (serviceExtra.duration && (booking.extras[extraIndex].quantity === 0 || booking.extras[extraIndex].quantity === 1)) {
    emits('changedSlotCondition')
  }
}

function getBookingExtrasSubtotal(item) {
  if (item.extras.length) {
    return item.extras.reduce((acc, extra) => {
      return acc + ('price' in extra ? extra.price : employeeService.value.extras.find(e => e.id === extra.extraId).price) * extra.quantity
    }, 0)
  }
  return 0
}

function selectedExtrasCount(extras) {
  return extras.filter((e) => e.quantity > 0).length
}

const { noShowData } = inject('noShowData')

// * Customized options
let customerPhoneVisibility = computed(() => shortcodeData.value.cabinetType === 'employee' ? amCustomize.value.appointments.options.customerPhone.visibility : true)
let customerEmailVisibility = computed(() => shortcodeData.value.cabinetType === 'employee' ? amCustomize.value.appointments.options.customerEmail.visibility : true)

// * Colors
let amColors = inject('amColors')
// * CSS Variables
let cssVars = computed(() => {
  return {
    '--am-c-ext-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6)
  }
})

onMounted(() => {
  if (appointmentBookings.value.filter((b) => b.status !== 'canceled' && b.status !== 'rejected').length < 2) {
    activeBookingIndex.value = 0
  }
})
</script>

<style lang="scss">
@mixin am-cabinet-appointment-extra {
  // am    - amelia
  // capai - cabinet-panel-appointment-item
  // ext   - extra
  .am-capai-ext {
    &__heading {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 16px 0 0;

      &.am-pb-16 {
        padding-bottom: 16px;
      }

      &-extra {
        font-size: 15px;
        font-style: normal;
        font-weight: 500;
        line-height: 1.6;
        color: var(--am-c-main-text);
      }
    }

    &__wrapper {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    &__inner {
      display: flex;
      align-items: center;
      justify-content: space-between;

      &.am-rw {
        &-420 {
          flex-wrap: wrap;
          gap: 4px;
        }
      }

      &.am-border-bottom {
        padding: 0 0 12px;
        border-bottom: 1px solid var(--am-c-inp-border);
      }

      &-info {
        display: flex;
        flex-direction: column;
      }

      &-name {
        font-size: 14px;
        font-style: normal;
        font-weight: 700;
        line-height: 1.42857;
        color: var(--am-c-main-text);
      }

      &-duration {
        font-size: 13px;
        font-style: normal;
        font-weight: 400;
        line-height: 1.38462;
        color: var(--am-c-ext-text-op60);
      }

      &-selection {
        display: flex;
        flex: 0 0 auto;
        align-items: center;
        gap: 8px;

        &.am-rw {
          &-420 {
            width: 100%;
            flex-wrap: wrap;
            gap: 4px;

            .am-input-number {
              width: 100%;
            }
          }
        }
      }

      &-price {
        font-size: 14px;
        font-style: normal;
        font-weight: 500;
        line-height: 1.42857;
        color: var(--am-c-primary);

        &.am-rw {
          &-420 {
            width: 100%;
          }
        }
      }
    }

    &__total {
      display: flex;
      justify-content: flex-end;
      font-size: 15px;
      font-style: normal;
      font-weight: 500;
      line-height: 1.6;
      color: var(--am-c-main-text);
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-appointment-extra;
}
</style>