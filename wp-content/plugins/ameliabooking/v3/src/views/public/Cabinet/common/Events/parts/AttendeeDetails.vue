<template>
  <div class="am-cap__att">
    <el-form
      ref="detailsFormRef"
      :model="detailsForm"
      :rules="detailsFormRules"
      class="am-cap__att-form"
    >
      <div class="am-cap-customers">
        <!-- Select Customer -->
        <el-form-item
          v-if="props.isNew"
          :prop="'customerId'"
          class="am-cap__att-form__item am-w-100"
        >
          <AmSelect
            v-model="detailsForm.customerId"
            filterable
            clearable
            collapse-tags
            collapse-tags-tooltip
            remote
            prefix-icon="search"
            prefix-icon-color="var(--am-c-inp-text)"
            popper-class="am-cap-cust__popper"
            :placeholder="amLabels.event_attendees_search"
            :remote-method="(val) => {useSearchCustomers(store, val)}"
            :loading="searchingCustomers"
            @focus="useFocusCustomers(store)"
          >
            <AmOption
              v-for="item in customers"
              :key="item.id"
              :value="item.id"
              :label="item.email ? item.email : item.firstName + ' ' + item.lastName"
            >
              <div class="am-cap__cust-option">
                <div class="am-cap__cust-option__heading">{{ item.firstName + ' ' + item.lastName }}</div>
                <div
                  v-if="item.email && customerEmailVisibility"
                  class="am-cap__cust-option__inner"
                >
                  {{ item.email }}
                </div>
                <div
                  v-if="item.phone && customerPhoneVisibility"
                  class="am-cap__cust-option__inner"
                >
                  {{ item.phone }}
                </div>
              </div>
            </AmOption>
          </AmSelect>
        </el-form-item>
        <!-- /Select Customer -->

        <!-- Add Customer -->
        <AmButton
          v-if="!store.getters['attendee/getId'] && amSettings.roles.allowWriteCustomers"
          class="am-capai-cuf__heading-add"
          :class="props.responsiveClass"
          :size="'default'"
          :disabled="!!detailsForm.customerId"
          prefix="plus"
          icon="plus"
          :icon-only="props.pageWidth <= 420"
          @click="addCustomer"
        >
          {{ amLabels.new_customer }}
        </AmButton>
        <!-- /Add Customer -->
      </div>
      
      <div
        v-if="customer"
        class="am-cap-attendee"
        :class="responsiveClass"
      >
        <div
          class="am-cap-cust"
          :class="responsiveClass"
          :style="cssVars"
        >
          <!-- Customer Name -->
          <div
            class="am-cap-cust__name"
            :class="noShowData(0).class"
          >
            <span
              v-if="noShowData(0).icon"
              :class="`am-icon-${noShowData(0).icon}`"
            />
            {{ customer.firstName + ' ' + customer.lastName }}
          </div>
          <!-- /Customer Name -->

          <!-- Customer Phone & Email -->
          <div
            v-if="(customer.phone && customerPhoneVisibility) || (customer.email && customerEmailVisibility)"
            class="am-cap-cust__data-wrapper"
          >
            <!-- Customer Phone -->
            <div
              v-if="customer.phone && customerPhoneVisibility"
              class="am-cap-cust__data"
            >
              <span class="am-icon-phone"></span>
              <a :href="`tel:${customer.phone}`">
                {{ customer.phone }}
              </a>
            </div>
            <!-- /Customer Phone -->

            <!-- Customer Email -->
            <div
              v-if="customer.email && customerEmailVisibility"
              class="am-cap-cust__data"
            >
              <span class="am-icon-email"></span>
              <a :href="`mailto:${customer.email}`">
                {{ customer.email }}
              </a>
            </div>
            <!-- /Customer Email -->
          </div>
          <!-- /Customer Phone & Email -->
        </div>

        <!-- Select Status -->
        <el-form-item
          :prop="'status'"
          class="am-cap__att-form__item"
          :class="responsiveClass"
        >
          <AmSelect
            v-model="detailsForm.status"
            :disabled="!props.isNew"
            :placeholder="amLabels.select"
            :prefix-icon="
              attendeeStatuses.find((i) => i.value === detailsForm.status).icon
            "
            :prefix-icon-color="
              attendeeStatuses.find((i) => i.value === detailsForm.status).color
            "
            popper-class="am-capei-att__popper"
          >
            <AmOption
              v-for="status in attendeeStatuses"
              :key="status.value"
              :value="status.value"
              :label="status.label"
            >
            <span
              :class="`am-icon-${status.icon}`"
              :style="{ color: status.color }"
            />
              {{ status.label }}
            </AmOption>
          </AmSelect>
        </el-form-item>
        <!-- /Select Status -->

        <DotsPopup
          v-if="amSettings.roles.allowWriteCustomers"
          :have-delete="false"
          :reference-class="responsiveClass"
          @edit="editCustomer(customer.id)"
        />
      </div>

      <template v-if="customer">
        <el-form-item
          v-if="!props.event.customPricing"
          :prop="'persons'"
          :label="`${amLabels.event_book_persons}:`"
          class="am-cap__att-form__item"
          :class="responsiveClass"
        >
          <AmInputNumber
            v-model="detailsForm.persons"
            :min="1"
            :disabled="!amSettings.roles.allowWriteEvents"
          />
        </el-form-item>

        <template v-else>
          <div class="am-cap__att-heading">
            {{ `${amLabels.event_book_tickets}:` }}
          </div>
          <el-form-item
            v-for="(item, index) in detailsForm.tickets"
            :key="index"
            :label="props.event.customTickets.find((i) => i.id === item.eventTicketId).name"
            class="am-cap__att-form__item"
            :class="responsiveClass"
          >
            <AmInputNumber
              v-model="item.persons"
              :min="0"
              :disabled="!amSettings.roles.allowWriteEvents"
            />
          </el-form-item>
        </template>
      </template>

      <EmptyState
        v-if="!customer"
        :heading="amLabels.no_attendees_yet"
      />

    </el-form>

    <!-- Add/Edit Customer -->
    <AddEditCustomerDialog
      v-if="addEditCustomerDialogVisibility"
      :visibility="addEditCustomerDialogVisibility"
      :responsive-class="props.responsiveClass"
      @update:visibility="(e) => addEditCustomerDialogVisibility = e"
      @added-customer="addedCustomer"
    />
    <!-- /Add/Edit Customer -->
  </div>
</template>

<script setup>
// * Import Composable
import { useFocusCustomers, useSearchCustomers } from '../../../../../../assets/js/admin/customers'
import { useResponsiveClass } from '../../../../../../assets/js/common/responsive'
import { useAttendeeStatuses } from '../../../../../../assets/js/common/attendees'

// * Import Components
import AmOption from '../../../../../_components/select/AmOption.vue'
import AmSelect from '../../../../../_components/select/AmSelect.vue'
import AmInputNumber from '../../../../../_components/input-number/AmInputNumber.vue'
import AmButton from "../../../../../_components/button/AmButton.vue";

// * Dedicated Components
import AddEditCustomerDialog from "../../Appointments/parts/common/AddEditCustomerDialog.vue";
import EmptyState from "../../parts/EmptyState.vue";
import DotsPopup from "../../../../Parts/DotsPopup.vue";

// * Import from Vue
import { computed, ref, inject, onMounted } from 'vue'

// * Import from library
import httpClient from "../../../../../../plugins/axios";

// * Import Store
import { useStore } from 'vuex'
import {useColorTransparency} from "../../../../../../assets/js/common/colorManipulation";
import {useAuthorizationHeaderObject} from "../../../../../../assets/js/public/panel";
import {useFrontendCustomer} from "../../../../../../assets/js/common/customer";

// * Component Props
const props = defineProps({
  event: {
    type: Object,
    default: () => {},
  },
  isNew: {
    type: Boolean,
    default: false,
  },
  pageWidth: {
    type: Number,
    required: true
  },
})

let responsiveClass = computed(() => {
  return useResponsiveClass(props.pageWidth)
})

// * Store
let store = useStore()

// * Labels
let amLabels = inject('amLabels')

// * Settings
const amSettings = inject('settings')

// * Data in shortcode
const shortcodeData = inject('shortcodeData')

// * Customize data
let amCustomize = inject('amCustomize')

// * Customized options
let customerPhoneVisibility = computed(() => shortcodeData.value.cabinetType === 'employee' ? amCustomize.value.events.options.customerPhone.visibility : true)
let customerEmailVisibility = computed(() => shortcodeData.value.cabinetType === 'employee' ? amCustomize.value.events.options.customerEmail.visibility : true)

// * Form Reference
let detailsFormRef = ref(null)

// * Form Data
let detailsForm = ref({
  customerId: computed({
    get: () => store.getters['attendee/getCustomerId'],
    set: (value) => store.commit('attendee/setCustomerId', value),
  }),
  status: computed({
    get: () => store.getters['attendee/getStatus'],
    set: (value) => store.commit('attendee/setStatus', value),
  }),
  persons: computed({
    get: () => store.getters['attendee/getPersons'],
    set: (value) => store.commit('attendee/setPersons', value),
  }),
  tickets: computed({
    get: () => store.getters['attendee/getTickets'],
    set: (value) => store.commit('attendee/setTickets', value),
  }),
})

// * Form Rules
let detailsFormRules = ref({
  customerId: [
    {
      required: true,
      message: amLabels.value.select_customer,
      trigger: ['submit', 'change'],
    },
  ],
})

let eventSettings = computed(() => {
  return props.event.settings ? JSON.parse(props.event.settings) : null
})

let attendeeStatuses = ref(useAttendeeStatuses().filter(
    i => i.value !== 'canceled' && (
      !amSettings.featuresIntegrations.waitingList.enabled ||
      (
        eventSettings.value
          ? 'waitingList' in eventSettings.value && !eventSettings.value.waitingList.enabled
          : true
      ) ? i.value !== 'waiting' : true
    ) && i.value !== 'no-show'
  )
)

// * Customer Searching
let searchingCustomers = computed(
  () => store.getters['customerInfo/getLoading']
)

// * Customers
let customers = computed(() => store.getters['customerInfo/getCustomers'])

let customer = computed(() => detailsForm.value.customerId ? customers.value.find((i) => i.id === detailsForm.value.customerId) : null)

function editCustomer(id) {
  store.dispatch('customerInfo/resetCustomer')

  store.commit('customerInfo/setLoading', true)

  addEditCustomerDialogVisibility.value = true

  httpClient.get(
    '/users/customers/' + id,
    Object.assign(
      {
        params: {
          source: 'cabinet-provider',
        },
      },
      useAuthorizationHeaderObject(store)
    )
  ).then((response) => {
    store.commit('customerInfo/setCustomer', useFrontendCustomer(store, response.data.data.user))
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    store.commit('customerInfo/setLoading', false)
  })
}

let addEditCustomerDialogVisibility = ref(false)

function addCustomer() {
  store.dispatch('customerInfo/resetCustomer')

  addEditCustomerDialogVisibility.value = true
}

function addedCustomer(customer) {
  store.commit('customerInfo/setCustomers', [customer])

  store.commit('attendee/setCustomerId', customer.id)
}

onMounted(() => {
  if (!store.getters['attendee/getId']) {
    store.commit('customerInfo/setCustomers', store.getters['auth/getPreloadedCustomers'])
  }
})

// * Expose
defineExpose({
  detailsFormRef,
})

function noShowData(noShowCount) {
  if (noShowCount) {
    if (noShowCount === 1) {
      return {
        class: 'am-no-show-1',
        icon: 'no-show-1',
      }
    }
    if (noShowCount === 2) {
      return {
        class: 'am-no-show-2',
        icon: 'no-show-2',
      }
    }

    if (noShowCount > 2) {
      return {
        class: 'am-no-show-3',
        icon: 'no-show-3',
      }
    }
  }

  return {
    class: '',
    icon: '',
  }
}

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-cust-no1': amColors.value.colorMainText,
    '--am-c-cust-no1-bgr': useColorTransparency(amColors.value.colorMainText, 0.1),
    '--am-c-cust-no2': amColors.value.colorWarning,
    '--am-c-cust-no2-bgr': useColorTransparency(amColors.value.colorWarning, 0.1),
    '--am-c-cust-no3': amColors.value.colorError,
    '--am-c-cust-no3-bgr': useColorTransparency(amColors.value.colorError, 0.1),
    '--am-c-cust-text': amColors.value.colorMainText,
    '--am-c-cust-link': useColorTransparency(amColors.value.colorMainText, 0.5),
  }
})
</script>

<style lang="scss">
@mixin am-cabinet-panel-attendee-block {
  // am   - amelia
  // cap  - cabinet panel
  // att  - attendee
  .am-cap__att {
    display: flex;
    flex-direction: column;
    gap: 24px;

    &-form {
      display: flex;
      flex-direction: column;
      gap: 24px;

      &__item.el-form-item {
        width: 100%;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        flex-wrap: nowrap;
        align-items: center;
        margin: 0;
        gap: 8px 24px;

        &.am-rw-520 {
          flex-direction: column;
          align-items: flex-start;
          order: 2;
        }

        .el-form-item__label {
          flex: 1 1 auto;
          white-space: break-spaces;
        }

        .el-form-item__content {
          justify-content: flex-end;
        }
      }
    }

    &-heading {
      width: 100%;
      font-size: 15px;
      font-weight: 500;
      line-height: 1.3;
      color: var(--am-c-main-text);
    }

    .am-cap-customers {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .am-cap-attendee {
      display: flex;
      padding: 16px;
      border: 1px solid var(--am-c-inp-border);
      border-radius: 8px;
      align-items: center;

      &.am-rw {
        &-520 {
          flex-wrap: wrap;
          gap: 16px 0;
        }
      }

      .am-cc__edit-btn {
        &.am-rw {
          &-520 {
            order: 1;
          }
        }
      }
    }

    .am-cap-cust {
      flex: 0 0 auto;
      display: flex;
      flex-direction: column;
      gap: 8px 4px;
      width: 50%;
      align-items: flex-start;

      &.am-rw {
        &-520 {
          width: calc(100% - 24px);
        }
      }

      // Customer Name
      &__name {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 18px;
        font-weight: 500;
        line-height: 1.555556;
        color: var(--am-c-cust-text);

        &[class*='am-no-show-'] {
          border: 1px solid var(--am-c-no-show);
          padding: 0 8px 0 4px;
          border-radius: 6px;
          background-color: var(--am-c-no-show-bgr);
        }

        [class^='am-icon-'] {
          font-size: 28px;
          line-height: 1;
          color: var(--am-c-no-show);
        }

        &.am-no-show-1 {
          --am-c-no-show: var(--am-c-cust-no1);
          --am-c-no-show-bgr: var(--am-c-cust-no1-bgr);
        }

        &.am-no-show-2 {
          --am-c-no-show: var(--am-c-cust-no2);
          --am-c-no-show-bgr: var(--am-c-cust-no2-bgr);
        }

        &.am-no-show-3 {
          --am-c-no-show: var(--am-c-cust-no3);
          --am-c-no-show-bgr: var(--am-c-cust-no3-bgr);
        }
      }

      // Customer Email and Phone
      &__data {
        display: flex;
        align-items: center;
        gap: 0 4px;

        &.am-rw {
          &-420 {
            width: 100%;
          }
        }

        &-wrapper {
          display: flex;
          align-items: center;
          gap: 8px;

          &.am-rw {
            &-420 {
              flex-wrap: wrap;
              gap: 4px;
            }
          }
        }

        a {
          font-size: 14px;
          font-weight: 400;
          line-height: 1;
          color: var(--am-c-cust-link);
          text-decoration: none;
        }

        [class^='am-icon-'] {
          font-size: 28px;
          line-height: 1;
          color: var(--am-c-cust-text);
        }
      }

      &__data {
        &-wrapper {
          flex-wrap: wrap;
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-panel-attendee-block;
}
</style>