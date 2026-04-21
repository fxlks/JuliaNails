<template>
  <el-collapse
    v-model="activeBookingIndex"
    class="am-capai-collapse am-capai-linked"
    accordion
    :style="cssVars"
  >
    <template
      v-for="(recurringBooking, recurringIndex) in store.getters['appointment/getBookings']"
      :key="recurringIndex"
    >
      <el-collapse-item
        v-if="linkedArray(recurringBooking).length"
        :name="recurringIndex"
        class="am-capai-collapse__item"
      >

        <template #title>
          <!-- Customer Name -->
          <div
            class="am-capai-customer__name"
            :class="[{'am-pb-16': !((recurringBooking.customer.email && customerEmailVisibility) || (recurringBooking.customer.phone && customerPhoneVisibility))}, noShowData(recurringBooking.customer.noShowCount).class]"
          >
          <span
            v-if="noShowData(recurringBooking.customer.noShowCount).icon"
            :class="`am-icon-${noShowData(recurringBooking.customer.noShowCount).icon}`"
          />
            {{ recurringBooking.customer.firstName + ' ' + recurringBooking.customer.lastName }}
          </div>
          <!-- /Customer Name -->
        </template>

        <!-- Customer Info -->
        <div
          v-if="(recurringBooking.customer.email && customerEmailVisibility) || (recurringBooking.customer.phone && customerPhoneVisibility)"
          class="am-capai-customer__data-wrapper"
          :class="props.responsiveClass"
        >
          <!-- Customer Email -->
          <div
            v-if="recurringBooking.customer.email && customerEmailVisibility"
            class="am-capai-customer__data"
            :class="props.responsiveClass"
          >
            <span class="am-icon-email"></span>
            <a :href="`mailto:${recurringBooking.customer.email}`">
              {{ recurringBooking.customer.email }}
            </a>
          </div>
          <!-- /Customer Email -->

          <!-- Customer Phone -->
          <div
            v-if="recurringBooking.customer.phone && customerPhoneVisibility"
            class="am-capai-customer__data"
            :class="props.responsiveClass"
          >
            <span class="am-icon-phone"></span>
            <a :href="`tel:${recurringBooking.customer.phone}`">
              {{ recurringBooking.customer.phone }}
            </a>
          </div>
          <!-- /Customer Phone -->
        </div>
        <!-- /Customer Info -->

        <div class="am-capai-linked__list">
          <div
            v-for="(linked, linkedIndex) in linkedArray(recurringBooking)"
            :key="recurringBooking.id + '-' + linkedIndex"
            class="am-capai-linked__list-item"
            :class="props.responsiveClass"
          >
            <div class="am-capai-linked__list-index">
              {{ linkedIndex + 1 }}
            </div>
            <div class="am-capai-linked__list-date">
              <span class="am-icon-calendar" />
              {{ getFrontedFormattedDateTime(linked.bookingStart) }}
            </div>
            <div class="am-capai-linked__list-action">
              <AmButton
                category="primary"
                size="small"
                @click="editLinkedAppointment(linked)"
              >
                {{ amLabels.edit }}
              </AmButton>
            </div>
          </div>
        </div>
      </el-collapse-item>
    </template>
  </el-collapse>
</template>

<script setup>
// * Import from Vue
import {
  inject,
  ref,
  onMounted,
  computed,
} from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Import Composables
import { getFrontedFormattedDateTime } from '../../../../../../assets/js/common/date'
import { useColorTransparency } from "../../../../../../assets/js/common/colorManipulation";

// * Import Components
import AmButton from '../../../../../_components/button/AmButton.vue'

// * Store
let store = useStore()

// * Props
let props = defineProps({
  responsiveClass: {
    type: String,
    default: '',
  },
  linkedAppointments: {
    type: Array,
    required: true
  },
})

// * Emits
const emits = defineEmits(['editLinkedAppointment'])

// * Data in shortcode
const shortcodeData = inject('shortcodeData')

// * Customize data
let amCustomize = inject('amCustomize')

let activeBookingIndex = ref(null)

function linkedArray (recurringBooking) {
  return props.linkedAppointments.filter((a) =>
    a.bookings.filter(
      (b) =>
        parseInt(b.customer.id) ===
        parseInt(recurringBooking.customer.id)
    ).length
  )
}

const { noShowData } = inject('noShowData')

// * Labels
let amLabels = inject('amLabels')

function editLinkedAppointment(appointment) {
  store.commit('appointment/resetAppointment', {providerId: store.getters['auth/getProfile'].id})
  store.commit('customerInfo/setCustomers', [])
  store.commit('customerInfo/setCustomersIds', [])

  emits('editLinkedAppointment', appointment)
}

// * Customized options
let customerPhoneVisibility = computed(() => shortcodeData.value.cabinetType === 'employee' ? amCustomize.value.appointments.options.customerPhone.visibility : true)
let customerEmailVisibility = computed(() => shortcodeData.value.cabinetType === 'employee' ? amCustomize.value.appointments.options.customerEmail.visibility : true)

// * Colors
let amColors = inject('amColors')
// * CSS Variables
let cssVars = computed(() => {
  return {
    '--am-c-linked-bgr-op05': useColorTransparency(amColors.value.colorMainText, 0.05),
  }
})

onMounted(() => {
  if (store.getters['appointment/getBookings'].filter((b) => b.status !== 'canceled' && b.status !== 'rejected').length < 2) {
    activeBookingIndex.value = 0
  }
})
</script>

<style lang="scss">
@mixin am-cabinet-appointment-linked {
  // am    - amelia
  // capai - cabinet-panel-appointment-item
  // linked   - extra
  .am-capai-linked {
    &__list {
      display: flex;
      flex-direction: column;
      gap: 10px;

      &-item {
        display: flex;
        align-items: center;
        padding: 8px;
        border: 1px solid var(--am-c-inp-border);
        border-radius: 6px;
        background-color: var(--am-c-cat-main-bgr);
        transition: all 0.3s;
        cursor: pointer;

        &.am-rw {
          &-420 {
            justify-content: flex-start;
            flex-wrap: wrap;
            gap: 4px;

            .am-capai-linked__list {
              &-date {
                width: calc(100% - 34px);
              }

              &-action {
                width: 100%;
                text-align: unset;
                justify-content: flex-start;

                & > .am-button {
                  width: 100%;
                }
              }
            }
          }
        }

        &:hover {
          background-color: var(--am-c-linked-bgr-op05);
        }
      }

      &-index {
        width: 30px;
        text-align: center;
        color: var(--am-c-main-text);
        font-size: 14px;
        font-weight: 500;
        line-height: 24px;
      }

      &-date {
        display: flex;
        align-items: center;
        gap: 4px;
        width: calc(70% - 15px);
        font-size: 14px;
        font-weight: 500;
        line-height: 24px;

        [class^="am-icon-"] {
          font-size: 24px;
          line-height: 24px;
        }
      }

      &-action {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        width: calc(30% - 15px);
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-appointment-linked;
}
</style>