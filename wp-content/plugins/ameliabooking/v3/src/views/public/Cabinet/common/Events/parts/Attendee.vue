<template>
  <div
    v-if="!loading"
    class="am-capei-atti"
    :class="[{ 'am-no-border': true }, props.responsiveClass]"
    :style="cssVars"
  >
    <AmAlert
      v-if="alertVisibility"
      ref="alertContainer"
      type="error"
      :show-border="true"
      :close-after="10000"
      custom-class="am-csd__alert"
      @close="closeAlert"
      @trigger-close="closeAlert"
    >
      <template #title>
        <span class="am-icon-clearable"/> {{ alertMessage }}
      </template>
    </AmAlert>

    <el-tabs v-model="activeTab">
      <!-- Details -->
      <el-tab-pane
        :label="amLabels.details"
        name="details"
      >
        <AttendeeDetails
          ref="attendeeDetailsRef"
          :event="props.event"
          :is-new="props.isNew"
          :page-width="props.pageWidth"
        />
      </el-tab-pane>
      <!-- /Details -->
      <!-- Custom Fields -->
      <el-tab-pane
        v-if="Object.keys(customFields).length"
        :label="amLabels.custom_fields"
        name="customFields"
      >
        <AttendeeCustomFields
          ref="attendeeCustomFieldsRef"
          :event="props.event"
        />
      </el-tab-pane>
      <!-- /Custom Fields -->
      <!-- Payment -->
      <el-tab-pane
        v-if="store.getters['attendee/getId']"
        :label="amLabels.payment"
        name="payment"
      >
        <AttendeePayment
          :event="props.event"
          :page-width="props.pageWidth"
        />
      </el-tab-pane>
      <!-- /Payment -->
    </el-tabs>

    <div class="am-capei-atti__footer">
      <AmButton
        category="secondary"
        :size="'default'"
        :type="'plain'"
        @click="close"
      >
        {{ amSettings.roles.allowWriteEvents ? amLabels.cancel : amLabels.close }}
      </AmButton>
      <AmButton
        v-if="amSettings.roles.allowWriteEvents"
        :size="'default'"
        @click="validateSave"
      >
        {{ amLabels.save }}
      </AmButton>
    </div>

    <CancelPopup
      :visibility="amountChanged"
      :title="amLabels.confirm"
      :description="amLabels.price_changed_message"
      :close-btn-text="amLabels.no"
      :confirm-btn-text="amLabels.yes"
      :customized-options="{cancelBtn: {buttonType: 'plain'}, confirmBtn: {buttonType: 'filled'}}"
      @decline="saveAttendee(false)"
      @confirm="saveAttendee(true)"
      @close="amountChanged = false"
    >
    </CancelPopup>
  </div>
  <Skeleton v-else></Skeleton>
</template>

<script setup>
// * import from Vue
import {
  ref,
  computed,
  inject,
  provide,
  onMounted,
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * _components
import Skeleton from '../../Authentication/parts/Skeleton.vue'
import AmButton from "../../../../../_components/button/AmButton.vue";
import AmAlert from "../../../../../_components/alert/AmAlert.vue";

// * Dedicated Components
import AttendeeDetails from "./AttendeeDetails";
import AttendeeCustomFields from "./AttendeeCustomFields";
import AttendeePayment from "./AttendeePayment";
import CancelPopup from "../../parts/CancelPopup.vue";

import httpClient from "../../../../../../plugins/axios";

import { useColorTransparency } from "../../../../../../assets/js/common/colorManipulation.js";
import { useAttendeeAmount, useBackAttendee } from "../../../../../../assets/js/common/attendees";
import { useAuthorizationHeaderObject } from "../../../../../../assets/js/public/panel";

// * Components props
let props = defineProps({
  title: {
    type: String,
    default: ''
  },
  visibility: {
    type: Boolean,
    default: false
  },
  isNew: {
    type: Boolean,
    default: false
  },
  event: {
    type: Object,
    default: () => {}
  },
  pageWidth: {
    type: Number,
    default: 0
  }
})

// * Component emits
let emits = defineEmits(['close', 'save'])

// * Vars
let store = useStore()

// * Labels
let amLabels = inject('amLabels')

// * Settings
const amSettings = inject('settings')

let loading = ref(false)

let customFields = computed(() => store.getters['attendee/getCustomFields'])

/********
 * Tabs *
 ********/
const activeTab = ref('details')

let attendeeDetailsRef = ref(null)

let attendeeCustomFieldsRef = ref(null)

/********
 * Form *
 ********/

// * Alert
let alertVisibility = ref(false)
let alertContainer = ref(null)
let alertMessage = ref('')

function closeAlert () {
  alertVisibility.value = false
  alertMessage.value = ''
}

/********
 * Form *
 ********/
let amountChanged = ref(false)

let attendeeRef = ref()

provide('formWrapper', attendeeRef)

function getAmount() {
  let amountData = useAttendeeAmount(store)

  return amountData.price - amountData.discount + amountData.tax
}

function validateSave () {
  let validData = true

  attendeeCustomFieldsRef.value?.customFieldsFormRef.validate((valid) => {
    if (!valid) {
      activeTab.value = 'customFields'

      validData = false
    }
  })

  attendeeDetailsRef.value?.detailsFormRef.validate((valid) => {
    if (!valid) {
      activeTab.value = 'details'

      validData = false
    }
  })

  if (!validData) {
    return
  }

  let paymentLinksEnabled = amSettings.payments.paymentLinks && amSettings.payments.paymentLinks.enabled

  let eventSettings = props.event.settings ? JSON.parse(props.event.settings) : null

  if (eventSettings &&
    'payments' in eventSettings &&
    'paymentLinks' in eventSettings.payments &&
    'enabled' in eventSettings.payments.paymentLinks
  ) {
    paymentLinksEnabled = eventSettings.payments.paymentLinks.enabled
  }

  if (paymentLinksEnabled && store.getters['attendee/getId'] && getAmount() > oldAmount.value) {
    amountChanged.value = true
  } else {
    saveAttendee(false)
  }
}

function saveAttendee (createPaymentLinks) {
  loading.value = true

  httpClient
    .post(
      store.getters['attendee/getId'] ? '/events/bookings/' + store.getters['attendee/getId'] : '/bookings',
      Object.assign(
        useBackAttendee(store),
        {
          runInstantPostBookingActions: true,
          createPaymentLinks: createPaymentLinks,
        }
      ),
      Object.assign(
        useAuthorizationHeaderObject(store),
        {
          params: {
            source: 'cabinet-provider',
          },
        }
      )
    )
    .then(() => {
      emits('save')
      store.commit('attendee/setActive', false)
    })
    .catch((e) => {
      console.log(e)
    })
    .finally(() => {
      loading.value = false
    })
}

let oldAmount = ref(0)

function close() {
  store.commit('attendee/setActive', false)

  emits('close')
}

onMounted(() => {
  store.commit('attendee/setActive', true)

  if (store.getters['attendee/getId']) {
    oldAmount.value = getAmount()
  }
})

// * Colors
let amColors = inject('amColors')

// * CSS Variables
let cssVars = computed(() => {
  return {
    '--am-c-capai-text': amColors.value.colorMainText,
    '--am-c-capai-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
    '--am-c-capai-primary': amColors.value.colorPrimary,

    '--am-c-cust-no1': amColors.value.colorMainText,
    '--am-c-cust-no1-bgr': useColorTransparency(
      amColors.value.colorMainText,
      0.1
    ),
    '--am-c-cust-no2': amColors.value.colorWarning,
    '--am-c-cust-no2-bgr': useColorTransparency(
      amColors.value.colorWarning,
      0.1
    ),
    '--am-c-cust-no3': amColors.value.colorError,
    '--am-c-cust-no3-bgr': useColorTransparency(amColors.value.colorError, 0.1),
    '--am-c-cust-link': useColorTransparency(amColors.value.colorMainText, 0.5),
    '--am-c-cust-text': amColors.value.colorMainText,
  }
})
</script>

<script>
export default {
  name: 'CabinetEventAttendee',
}
</script>

<style lang="scss">
@mixin am-cabinet-appointment {
  // am    - amelia
  // capai - cabinet-panel-appointment-item
  .am-capei-atti {

    // Customer
    &-customer {
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
          width: 100%;
          display: flex;
          align-items: center;
          gap: 8px;
          padding: 4px 0 16px;

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
          line-height: 1.42857;
          color: var(--am-c-cust-link);
          text-decoration: none;
        }

        [class^='am-icon-'] {
          font-size: 28px;
          line-height: 0;
          color: var(--am-c-cust-text);
        }
      }
    }

    .am-select {
      &-wrapper {
        &.am-rw {
          &-520 {
            max-width: 100%;
            width: 100%;
            order: 2;
          }
        }
      }

      .el-input__prefix {
        left: 6px;

        [class^='am-icon-'] {
          font-size: 24px;
          line-height: 0;
        }
      }
    }

    // Divider
    .am-divider {
      width: 100%;
      height: 1px;
      background-color: var(--am-c-inp-border);
      margin: 6px 0;
    }

    &__footer {
      position: absolute;
      bottom: 16px;
      right: 32px;

      .am-button {
        margin-left: 20px;
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-appointment;
}
</style>
