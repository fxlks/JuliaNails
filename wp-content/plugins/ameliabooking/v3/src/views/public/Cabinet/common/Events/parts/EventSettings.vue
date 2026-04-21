<template>
  <el-form
    ref="settingsFormRef"
    :model="settingsFormData"
    class="am-capei-sef"
    :style="cssVars"
  >
    <el-collapse
      v-model="activeSettings"
      accordion
      class="am-capei-sef__collapse"
    >
      <el-collapse-item
        :name="'general'"
        class="am-capei-sef__collapse-item"
      >
        <template #title>
          <div class="am-capei-sef__header">
            <span class="am-icon-options" />
            <div class="am-capei-sef__header-text">
              {{ amLabels.general }}
            </div>
          </div>
        </template>

        <div class="am-capei-sef__content">
          <el-form-item>
            <template #label>
              {{ amLabels.minimum_time_before_canceling }}
              <el-tooltip
                effect="dark"
                placement="top"
              >
                <template #content>
                  <div v-html="amLabels.minimum_time_before_canceling_tooltip"></div>
                </template>
                <IconComponent class="am-tooltip__trigger" icon="info-reverse" />
              </el-tooltip>
            </template>
            <AmSelect
              v-model="settingsFormData.generalMinimumTimeRequirementPriorToCanceling"
              :placeholder="durations.find(item => item.value === amSettings.general.minimumTimeRequirementPriorToCanceling).label"
              clearable
            >
              <AmOption
                v-for="(duration, index) in durations"
                :key="index"
                :value="duration.value"
                :label="duration.label"
              />
            </AmSelect>
          </el-form-item>

          <el-form-item>
            <template #label>
              {{ amLabels.redirect_url_after_appointment }}
              <el-tooltip
                effect="dark"
                placement="top"
              >
                <template #content>
                  <div v-html="amLabels.redirect_url_after_appointment_tooltip"></div>
                </template>
                <IconComponent class="am-tooltip__trigger" icon="info-reverse" />
              </el-tooltip>
            </template>
            <AmInput
              v-model="settingsFormData.generalRedirectUrlAfterAppointment"
              :placeholder="amSettings.general.redirectUrlAfterAppointment"
            />
          </el-form-item>
        </div>
      </el-collapse-item>

      <el-collapse-item
        :name="'payment'"
        class="am-capei-sef__collapse-item"
      >
        <template #title>
          <div class="am-capei-sef__header">
            <span class="am-icon-payments" />
            <div class="am-capei-sef__header-text">
              {{ amLabels.payments }}
            </div>
          </div>
        </template>

        <div class="am-capei-sef__content">

          <div class="am-capei-sef__segment">
            <div v-if="!licence.isStarter" class="am-capei-sef__heading">
              <div class="am-capei-sef__heading-text">
                {{ amLabels.payment_links_enable }}
                <el-tooltip
                  effect="dark"
                  placement="top"
                >
                  <template #content>
                    <div v-html="amLabels.payment_links_enable_tooltip"></div>
                  </template>
                  <IconComponent class="am-tooltip__trigger" icon="info-reverse" />
                </el-tooltip>
              </div>
              <AmSwitch
                v-model="settingsFormData.paymentsPaymentLinksEnabled"
              />
            </div>

            <el-form-item
              v-if="settingsFormData.paymentsPaymentLinksEnabled && !licence.isStarter"
              :label="amLabels.redirect_url_after_appointment"
            >
              <template #label>
                {{ amLabels.redirect_url_after_appointment }}
                <el-tooltip
                  effect="dark"
                  placement="top"
                >
                  <template #content>
                    <div v-html="(licence.isPro || licence.isDeveloper) ? amLabels.redirect_url_after_app_or_package_tt : amLabels.redirect_url_after_appointment_tooltip"/>
                  </template>
                  <IconComponent class="am-tooltip__trigger" icon="info-reverse" />
                </el-tooltip>
              </template>
              <AmInput
                v-model="settingsFormData.paymentsPaymentLinksRedirectUrl"
                :placeholder="amSettings.payments.paymentLinks.redirectUrl"
              />
            </el-form-item>
          </div>

          <div
            v-if="amSettings.payments.onSite"
            class="am-capei-sef__heading"
          >
            <div class="am-capei-sef__heading-text">
              {{ amLabels.on_site }}
            </div>
            <AmSwitch
              v-model="settingsFormData.paymentsOnSite"
            />
          </div>

          <div class="am-capei-sef__segment">
            <div
              v-if="amSettings.payments.wc.enabled"
              class="am-capei-sef__heading"
            >
              <div class="am-capei-sef__heading-text">
                {{ amLabels.wc_service }}
              </div>
              <AmSwitch
                v-model="settingsFormData.paymentsWcEnabled"
              />
            </div>

            <el-form-item v-if="settingsFormData.paymentsWcEnabled">
              <AmSelect
                v-model="settingsFormData.paymentsWcProductId"
                clearable
                filterable
                remote
                :placeholder="defaultProduct"
                :loading="loadingProducts"
                :remote-method="searchProducts"
              >
                <AmOption
                  v-for="(product, index) in products"
                  :key="index"
                  :value="product.id"
                  :label="product.name"
                />
              </AmSelect>
            </el-form-item>
          </div>

          <div
            v-if="amSettings.payments.payPal.enabled"
            class="am-capei-sef__heading"
          >
            <img style='width: 60px;' :src="baseUrls.wpAmeliaPluginURL + 'public/img/payments/paypal-light.svg'" alt="PayPal" />
            <AmSwitch
              v-model="settingsFormData.paymentsPayPalEnabled"
            />
          </div>

          <div
            v-if="amSettings.payments.stripe.enabled"
            class="am-capei-sef__heading"
          >
            <img style='width: 60px;' :src="baseUrls.wpAmeliaPluginURL + 'public/img/payments/stripe.svg'" alt="Stripe" />
            <AmSwitch
              v-model="settingsFormData.paymentsStripeEnabled"
            />
          </div>

          <div
            v-if="amSettings.payments.mollie.enabled"
            class="am-capei-sef__heading"
          >
            <img style='width: 60px;' :src="baseUrls.wpAmeliaPluginURL + 'public/img/payments/mollie.svg'" alt="Mollie" />
            <AmSwitch
              v-model="settingsFormData.paymentsMollieEnabled"
            />
          </div>

          <div
            v-if="amSettings.payments.square.enabled"
            class="am-capei-sef__heading"
          >
            <img style='width: 60px;' :src="baseUrls.wpAmeliaPluginURL + 'public/img/payments/square.svg'" alt="Square" />
            <AmSwitch
              v-model="settingsFormData.paymentsSquareEnabled"
            />
          </div>

          <div
            v-if="amSettings.payments.razorpay.enabled"
            class="am-capei-sef__heading"
          >
            <img style='width: 60px;' :src="baseUrls.wpAmeliaPluginURL + 'public/img/payments/razorpay.svg'" alt="Razorpay" />
            <AmSwitch
              v-model="settingsFormData.paymentsRazorpayEnabled"
            />
          </div>

          <div
            v-if="amSettings.payments.barion.enabled"
            class="am-capei-sef__heading"
          >
            <img style='width: 60px;' :src="baseUrls.wpAmeliaPluginURL + 'public/img/payments/barion.svg'" alt="Barion" />
            <AmSwitch
              v-model="settingsFormData.paymentsBarionEnabled"
            />
          </div>
        </div>
      </el-collapse-item>

      <el-collapse-item
        v-if="(amSettings.googleCalendar.enabled && amSettings.googleMeet.enabled) ||
          (amSettings.outlookCalendar.enabled && amSettings.microsoftTeams.enabled) ||
          amSettings.lessonSpace.enabled"
        :name="'integrations'"
        class="am-capei-sef__collapse-item"
      >
        <template #title>
          <div class="am-capei-sef__header">
            <span class="am-icon-options" />
            <div class="am-capei-sef__header-text">
              {{ amLabels.integrations_settings }}
            </div>
          </div>
        </template>

        <div
          v-if="amSettings.googleCalendar.enabled && amSettings.googleMeet.enabled"
          class="am-capei-sef__heading"
        >
          <div class="am-capei-sef__heading-text">
            {{ amLabels.enable_google_meet }}
          </div>
          <AmSwitch
            v-model="settingsFormData.integrationsGoogleMeetEnabled"
          />
        </div>

        <div
          v-if="amSettings.outlookCalendar.enabled && amSettings.microsoftTeams.enabled"
          class="am-capei-sef__heading"
        >
          <div class="am-capei-sef__heading-text">
            {{ amLabels.enable_microsoft_teams }}
          </div>
          <AmSwitch
            v-model="settingsFormData.integrationsMicrosoftTeamsEnabled"
          />
        </div>

        <div
          v-if="amSettings.lessonSpace.enabled"
          class="am-capei-sef__heading"
        >
          <div class="am-capei-sef__heading-text">
            {{ amLabels.lesson_space }}
          </div>
          <AmSwitch
            v-model="settingsFormData.integrationsLessonSpaceEnabled"
          />
        </div>
      </el-collapse-item>
    </el-collapse>
  </el-form>
</template>

<script setup>
// * Import form Vue
import {
  ref,
  computed,
  inject,
  onMounted,
} from "vue";

// * Import store
import { useStore } from "vuex";

// * Import Components
import AmOption from "../../../../../_components/select/AmOption.vue";
import AmSelect from "../../../../../_components/select/AmSelect.vue";
import AmInput from "../../../../../_components/input/AmInput.vue";
import AmSwitch from "../../../../../_components/switch/AmSwitch.vue";

import httpClient from '../../../../../../plugins/axios'

// * Composables
import { useColorTransparency } from '../../../../../../assets/js/common/colorManipulation'
import IconComponent from "../../../../../_components/icons/IconComponent.vue";

// * Store
let store = useStore();

// * Plugin Licence
let licence = inject('licence')

// * Labels
let amLabels = inject('amLabels')

// * Settings
const amSettings = inject('settings')

// * Root Urls
const baseUrls = inject('baseUrls')

let durations = ref([
  {
    label: amLabels.value.disabled,
    value: 0
  },
  {
    label: amLabels.value.min10,
    value: 600
  },
  {
    label: amLabels.value.min12,
    value: 720
  },
  {
    label: amLabels.value.min15,
    value: 900
  },
  {
    label: amLabels.value.min30,
    value: 1800
  },
  {
    label: amLabels.value.min45,
    value: 2700
  },
  {
    label: amLabels.value.h1,
    value: 3600
  },
  {
    label: amLabels.value.h1min30,
    value: 5400
  },
  {
    label: amLabels.value.h2,
    value: 7200
  },
  {
    label: amLabels.value.h3,
    value: 10800
  },
  {
    label: amLabels.value.h4,
    value: 14400
  },
  {
    label: amLabels.value.h6,
    value: 21600
  },
  {
    label: amLabels.value.h8,
    value: 28800
  },
  {
    label: amLabels.value.h9,
    value: 32400
  },
  {
    label: amLabels.value.h10,
    value: 36000
  },
  {
    label: amLabels.value.h11,
    value: 39600
  },
  {
    label: amLabels.value.h12,
    value: 43200
  },
  {
    label: amLabels.value.day1,
    value: 86400
  },
  {
    label: amLabels.value.days2,
    value: 172800
  },
  {
    label: amLabels.value.days3,
    value: 259200
  },
  {
    label: amLabels.value.days4,
    value: 345600
  },
  {
    label: amLabels.value.days5,
    value: 432000
  },
  {
    label: amLabels.value.days6,
    value: 518400
  },
  {
    label: amLabels.value.week1,
    value: 604800
  },
  {
    label: amLabels.value.weeks2,
    value: 1209600
  },
  {
    label: amLabels.value.weeks3,
    value: 1814400
  },
  {
    label: amLabels.value.weeks4,
    value: 2419200
  },
  {
    label: amLabels.value.months3,
    value: 7884000
  },
  {
    label: amLabels.value.months6,
    value: 15768000
  }
])

let products = ref([])

let loadingProducts = ref(false)

let loadingProductsCounter = ref(0)

let searchProductsTimer = ref(null)

let defaultProduct = computed(() => {
  if (products.value.length) {
    let product = products.value.find(item => item.id === amSettings.payments.wc.productId)

    return typeof product !== 'undefined' ? product.name : amLabels.value.select
  }

  return ''
})

function setProducts () {
  if (amSettings.payments.wc.enabled) {
    fetchProducts(
      {id: settingsFormData.value.paymentsWcProductId},
      (result) => {
        products.value = result.length ? result : []
      }
    )
  }
}

function fetchProducts (params, callbackMethod) {
  clearTimeout(searchProductsTimer.value)

  loadingProducts.value = true
  loadingProductsCounter.value++

  searchProductsTimer.value = setTimeout(() => {
    let lastSearchCounter = loadingProductsCounter.value

    httpClient
      .get(
        '/payment/wc/products',
        {
          params: params
        }
      )
      .then((response) => {
        if (lastSearchCounter >= loadingProductsCounter.value) {
          callbackMethod(
            response.data.data.products.sort(
              (a, b) => (a.name.toLowerCase() > b.name.toLowerCase()) ? 1 : -1
            )
          )
        }
      })
      .finally(() => {
        loadingProducts.value = false
      })
    },
    500
  )
}

function searchProducts (query) {
  if (query) {
    fetchProducts(
      {name: query},
      (result) => {
        products.value = result
      }
    )
  }
}

let activeSettings = ref('')

// * Form Reference
const settingsFormRef = ref(null)

// * Form data
let settingsFormData = ref({
  generalMinimumTimeRequirementPriorToCanceling: computed({
    get: () => store.getters['event/getSettingsGeneralMinimumTimeRequirementPriorToCanceling'],
    set: (val) => {
      store.commit('event/setSettingsGeneralMinimumTimeRequirementPriorToCanceling', val)
    }
  }),
  generalRedirectUrlAfterAppointment: computed({
    get: () => store.getters['event/getSettingsGeneralRedirectUrlAfterAppointment'],
    set: (val) => {
      store.commit('event/setSettingsGeneralRedirectUrlAfterAppointment', val)
    }
  }),
  paymentsPaymentLinksEnabled: computed({
    get: () => store.getters['event/getSettingsPaymentsPaymentLinksEnabled'],
    set: (val) => {
      store.commit('event/setSettingsPaymentsPaymentLinksEnabled', val)
    }
  }),
  paymentsPaymentLinksRedirectUrl: computed({
    get: () => store.getters['event/getSettingsPaymentsPaymentLinksRedirectUrl'],
    set: (val) => {
      store.commit('event/setSettingsPaymentsPaymentLinksRedirectUrl', val)
    }
  }),
  paymentsOnSite: computed({
    get: () => store.getters['event/getSettingsPaymentsOnSite'],
    set: (val) => {
      store.commit('event/setSettingsPaymentsOnSite', val)
    }
  }),
  paymentsWcEnabled: computed({
    get: () => store.getters['event/getSettingsPaymentsWcEnabled'],
    set: (val) => {
      store.commit('event/setSettingsPaymentsWcEnabled', val)
    }
  }),
  paymentsWcProductId: computed({
    get: () => store.getters['event/getSettingsPaymentsWcProductId'],
    set: (val) => {
      store.commit('event/setSettingsPaymentsWcProductId', val)
    }
  }),
  paymentsStripeEnabled: computed({
    get: () => store.getters['event/getSettingsPaymentsStripeEnabled'],
    set: (val) => {
      store.commit('event/setSettingsPaymentsStripeEnabled', val)
    }
  }),
  paymentsPayPalEnabled: computed({
    get: () => store.getters['event/getSettingsPaymentsPayPalEnabled'],
    set: (val) => {
      store.commit('event/setSettingsPaymentsPayPalEnabled', val)
    }
  }),
  paymentsMollieEnabled: computed({
    get: () => store.getters['event/getSettingsPaymentsMollieEnabled'],
    set: (val) => {
      store.commit('event/setSettingsPaymentsMollieEnabled', val)
    }
  }),
  paymentsRazorpayEnabled: computed({
    get: () => store.getters['event/getSettingsPaymentsRazorpayEnabled'],
    set: (val) => {
      store.commit('event/setSettingsPaymentsRazorpayEnabled', val)
    }
  }),
  paymentsBarionEnabled: computed({
    get: () => store.getters['event/getSettingsPaymentsBarionEnabled'],
    set: (val) => {
      store.commit('event/setSettingsPaymentsBarionEnabled', val)
    }
  }),
  paymentsSquareEnabled: computed({
    get: () => store.getters['event/getSettingsPaymentsSquareEnabled'],
    set: (val) => {
      store.commit('event/setSettingsPaymentsSquareEnabled', val)
    }
  }),
  integrationsGoogleMeetEnabled: computed({
    get: () => store.getters['event/getSettingsIntegrationsGoogleMeetEnabled'],
    set: (val) => {
      store.commit('event/setSettingsIntegrationsGoogleMeetEnabled', val)
    }
  }),
  integrationsMicrosoftTeamsEnabled: computed({
    get: () => store.getters['event/getSettingsIntegrationsMicrosoftTeamsEnabled'],
    set: (val) => {
      store.commit('event/setSettingsIntegrationsMicrosoftTeamsEnabled', val)
    }
  }),
  integrationsLessonSpaceEnabled: computed({
    get: () => store.getters['event/getSettingsIntegrationsLessonSpaceEnabled'],
    set: (val) => {
      store.commit('event/setSettingsIntegrationsLessonSpaceEnabled', val)
    }
  }),
})

onMounted(() => {
  if (settingsFormData.value.generalMinimumTimeRequirementPriorToCanceling === amSettings.general.minimumTimeRequirementPriorToCanceling) {
    store.commit('event/setSettingsGeneralMinimumTimeRequirementPriorToCanceling', '')
  }

  if (settingsFormData.value.generalRedirectUrlAfterAppointment === amSettings.general.redirectUrlAfterAppointment) {
    store.commit('event/setSettingsGeneralRedirectUrlAfterAppointment', '')
  }

  if (settingsFormData.value.paymentsPaymentLinksRedirectUrl === amSettings.payments.paymentLinks.redirectUrl) {
    store.commit('event/setSettingsPaymentsPaymentLinksRedirectUrl', '')
  }

  setProducts()
})

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-capei-sef-primary': amColors.value.colorPrimary,
    '--am-c-capei-sef-text': amColors.value.colorMainText,
    '--am-c-capei-sef-text-op03': useColorTransparency(amColors.value.colorMainText, 0.03),
    '--am-c-capei-sef-bgr': amColors.value.colorMainBgr,
  }
})
</script>

<style lang="scss">
@mixin cabinet-event-settings-block {
  // am   - amelia
  // capei - cabinet panel event item
  // sef  - settings form
  .am-capei-sef {
    &__collapse {
      display: flex;
      flex-direction: column;
      width: 100%;
      gap: 24px;

      &-item {
        border-radius: 8px;
        background-color: var(--am-c-capei-sef-text-op03);
      }
    }

    &__header {
      display: flex;
      align-items: center;
      gap: 8px;

      &-text {
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        color: var(--am-c-capei-sef-text);
      }

      [class*="am-icon"] {
        font-size: 20px;
        color: var(--am-c-capei-sef-primary);
      }
    }

    &__content {
      display: flex;
      flex-direction: column;
      gap: 16px;

      .el-form-item {
        margin-bottom: 0;
      }
    }

    &__heading {
      display: flex;
      align-items: center;
      justify-content: space-between;

      &-text {
        font-size: 16px;
        color: var(--am-c-capei-sef-text);
      }
    }

    &__segment {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .el-collapse-item {
      &__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: unset;
        background-color: transparent;
        padding: 16px;
      }

      &__wrap {
        padding: 0 16px 16px;
        background-color: transparent;
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include cabinet-event-settings-block;
}
</style>