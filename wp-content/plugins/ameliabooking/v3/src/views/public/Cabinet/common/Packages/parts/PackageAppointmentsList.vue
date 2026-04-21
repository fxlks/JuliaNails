<template>
  <div
    class="am-cappa"
    :style="cssVars"
  >
    <AmAlert
        v-if="alertVisibility"
        ref="alertContainer"
        :type= "alertType"
        :show-border="true"
        :close-after="5000"
        custom-class="am-cap__alert"
        @close="closeAlert"
        @trigger-close="closeAlert"
    >
      <template #title>
        <span class="am-icon-checkmark-circle-full"></span> {{ alertMessage }}
      </template>
    </AmAlert>

    <div class="am-cappa__back">
      <AmButton
        custom-class="am-cappa__back-btn"
        :icon="arrowLeft"
        :type="amCustomize.packageAppointmentsList.options.backBtn.buttonType"
        :size="'micro'"
        :category="'secondary'"
        icon-only
        :aria-label="amLabels.back_btn"
        @click="goBack"
      ></AmButton>
      <span>
        {{ amLabels.back_btn }}
      </span>
    </div>

    <!-- Cabinet Package card heading -->
    <div
      class="am-cappa__heading"
      :class="responsiveClass"
    >
      <div class="am-cappa__heading-left">
        <div
          class="am-cappa__img"
          :style="props.data.packageData.pictureThumbPath ? {backgroundImage: `url(${props.data.packageData.pictureThumbPath})`} : {}"
        >
          <span
            v-if="!props.data.packageData.pictureThumbPath"
            class="am-cappa__img-name"
          >
            {{getNameInitials(props.data.packageData.name)}}
          </span>
          <span class="am-cappa__img-color" :style="{backgroundColor: props.data.packageData.color}"></span>
        </div>
        <div class="am-cappa__info">
          <div class="am-cappa__name">
            {{ props.data.packageData.name }}
          </div>
          <div
            v-if="props.data.packageData.end"
            class="am-cappa__date"
          >
            {{ amLabels.package_book_expire}} {{ getFrontedFormattedDate(props.data.packageData.end.split(' ')[0]) }}
          </div>
          <div v-else class="am-cappa__date">
            {{ `${amLabels.package_book_expiration} ${amLabels.package_book_unlimited}` }}
          </div>
          <div
            v-if="Object.keys(props.data.services).length > 1"
            class="am-cappa__capacity"
          >
            {{ packagesSlotsCalculation(props.data) }}
          </div>
        </div>
      </div>
      <div
        class="am-cappa__heading-right"
        :class="responsiveClass"
      >
        <AmButton
          v-if="amSettings.roles.allowCustomerCancelPackages && props.data.packageData.status !== 'canceled'"
          :category="'danger'"
          :size="'small'"
          :class="['am-cappa__actions', responsiveClass]"
          :type="amCustomize.packageAppointmentsList.options.cancelBtn.buttonType"
          @click="() => { activePurchaseCancel = true }"
        >
          {{ amLabels.cancel }}
        </AmButton>
        <PaymentButton
          v-if="props.data.packageData.status === 'approved' && props.data.packageData.price > 0"
          btn-size="small"
          :class="`am-cappa__actions ${responsiveClass}`"
          :type="amCustomize.packageAppointmentsList.options.payBtn.buttonType"
          :reservation="targetPurchase"
          :bookable="selectedPackage"
        >
        </PaymentButton>
      </div>
    </div>

    <el-tabs
      v-model="selectedServiceId"
      class="am-cappa__service"
      @tab-click="selectService"
    >
      <el-tab-pane
        v-for="serviceId in servicesOrderInPackage"
        :key="serviceId"
        :name="serviceId"
      >
        <template #label>
          <div class="am-cappa__service-heading">
            {{ props.data.services[serviceId].purchaseData.name }}
          </div>
        </template>
        <template v-if="selectedServiceId === serviceId">
          <div
            class="am-cappa__service-top"
            :class="responsiveClass"
          >
            <div class="am-cappa__service-right">
              <div
                class="am-cappa__service-img"
                :style="props.data.services[selectedServiceId].purchaseData.pictureThumbPath ? {backgroundImage: `url(${props.data.services[selectedServiceId].purchaseData.pictureThumbPath})`} : {}"
              >
                <span
                  v-if="!props.data.services[selectedServiceId].purchaseData.pictureThumbPath"
                  class="am-cappa__service-img__name"
                >
                  {{getNameInitials(props.data.services[selectedServiceId].purchaseData.name)}}
                </span>
                <span
                  class="am-cappa__service-img__color"
                  :style="{backgroundColor: props.data.services[selectedServiceId].purchaseData.color}"
                ></span>
              </div>
              <div class="am-cappa__service-info">
                <div class="am-cappa__service-name">
                  {{ props.data.services[selectedServiceId].purchaseData.name }}
                </div>
                <div
                  v-if="!props.data.packageData.sharedCapacity"
                  class="am-cappa__service-capacity"
                >
                  {{ packagesSlotsCalculation(props.data, selectedServiceId) }}
                </div>
              </div>
            </div>

            <AmButton
              v-if="props.data.packageData.status !== 'canceled'"
              :class="['am-cappa__service-book', responsiveClass]"
              size="small"
              :type="amCustomize.packageAppointmentsList.options.bookBtn.buttonType"
              :disabled="getPurchasedCount(props.data.services, selectedServiceId, 'count') === 0"
              @click="bookAppointment"
            >
              {{ amLabels.book_now }}
            </AmButton>
          </div>

          <AppointmentsList
            :grouped-appointments="props.data.services[selectedServiceId].appointments"
            :page-width="pageWidth"
            :responsive-class="responsiveClass"
            :is-package-booking="true"
            step-key="packageAppointmentsList"
            @canceled="getPackageAppointments"
            @booked="getPackageAppointments"
          ></AppointmentsList>
        </template>
      </el-tab-pane>
    </el-tabs>

    <CancelPopup
      :loading="waitingForCancelation"
      :visibility="activePurchaseCancel"
      :title="customizedLabels('cancelPackage').cancel_package"
      :description="customizedLabels('cancelPackage').confirm_cancel_package"
      :close-btn-text="customizedLabels('cancelPackage').close"
      :confirm-btn-text="customizedLabels('cancelPackage').confirm"
      :customized-options="customizedOptions('cancelPackage')"
      @close="() => { activePurchaseCancel = false }"
      @decline="() => { activePurchaseCancel = false }"
      @confirm="cancelPurchase"
    >
    </CancelPopup>

    <AppointmentBooking
      :visibility="targetAppointment !== null && !targetAppointment.id"
      :appointment="targetAppointment"
      :employees="employees"
      :locations="locations"
      :slots-params="slotsParams"
      :labels="customizedLabels('bookAppointment')"
      :customized-options="customizedOptions('bookAppointment')"
      @close="targetAppointment = null"
      @success="managedAppointment"
      @error="() => {}"
      @employee-selection="(val) => selectedEmployee = val"
      @location-selection="(val) => selectedLocation = val"
    >
    </AppointmentBooking>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  computed,
  inject,
  provide,
  onMounted,
  reactive
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Import from Libraries
import httpClient from "../../../../../../plugins/axios";

// * Composables
import {
  getFrontedFormattedDate
} from "../../../../../../assets/js/common/date";
import {
  useAuthorizationHeaderObject
} from "../../../../../../assets/js/public/panel";
import {
  getNameInitials,
} from "../../../../../../assets/js/common/image";
import {
  useColorTransparency
} from "../../../../../../assets/js/common/colorManipulation";

// * _components
import AmButton from "../../../../../_components/button/AmButton.vue";
import IconComponent from "../../../../../_components/icons/IconComponent.vue";

// * Dedicated components
import CancelPopup from "../../parts/CancelPopup.vue";
import AppointmentsList from "../../Appointments/parts/AppointmentsList.vue";
import AppointmentBooking from "../../parts/AppointmentBooking.vue";
import PaymentButton from "../../parts/PaymentButton.vue";
import AmAlert from "../../../../../_components/alert/AmAlert.vue";

// * Component props
let props = defineProps({
  data: {
    type: [Object, Array],
    default: () => {}
  },
  responsiveClass: {
    type: String,
    default: ''
  },
  pageWidth: {
    type: Number
  }
})

// * Component emits
let emits = defineEmits(['goBack', 'booked', 'canceled', 'cancelError'])

let store = useStore()

// * Global settings
let amSettings = computed(() => store.getters['getSettings'])

let arrowLeft = {
  components: {IconComponent},
  template: `<IconComponent icon="arrow-left"></IconComponent>`
}

let { selectedPackageCustomerId } = inject('packageSelection')

let selectedPackage = computed(() => {
  return store.getters['entities/getPackage'](props.data.packageData.id)
})

let servicesOrderInPackage = computed(() =>
  Object.keys(props.data.services)
    .map(id => id.toString())
)

let bookable = computed(() => selectedPackage.value.bookable.find(i => parseInt(i.service.id) === parseInt(selectedServiceId.value)))
let selectedEmployee = ref(null)
let selectedLocation = ref(null)

let employees = computed(() => {
  let relations = store.getters['entities/getEntitiesRelations']
  let unfilteredEmployees = store.getters['entities/getUnfilteredEmployees']
  let employeesIds = []

  if (bookable.value) {
    if (selectedLocation.value) {
      Object.keys(relations).forEach((employeeId) => {
        if (
          targetAppointment.value &&
          targetAppointment.value.serviceId in relations[employeeId] &&
          relations[employeeId][parseInt(targetAppointment.value.serviceId)].indexOf(selectedLocation.value) !== -1
        ) {
          employeesIds.push(parseInt(employeeId))
        }
      })
    } else {
      let locationsIds = []

      if (bookable.value.locations.length) {
        locationsIds = bookable.value.locations.map(item => item.id)
      } else {
        for (let employeeId in relations) {
          if (bookable.value.service.id in relations[employeeId]) {
            locationsIds = locationsIds.concat(relations[employeeId][bookable.value.service.id])
          }
        }
      }

      for (let employeeId in relations) {
        if (bookable.value.service.id in relations[employeeId]) {
          locationsIds.forEach(locationId => {
            if (relations[employeeId][bookable.value.service.id].indexOf(locationId) !== -1) {
              employeesIds.push(parseInt(employeeId))
            }
          })
        }
      }
    }

    let availableEmployeesIds = bookable.value.providers.length ?
      bookable.value.providers.map(item => item.id) : unfilteredEmployees.map(item => item.id)


    return unfilteredEmployees.filter(item =>
      availableEmployeesIds.indexOf(item.id) !== -1 &&
      employeesIds.indexOf(item.id) !== -1 &&
      (store.getters['entities/getShowHidden'] || (item.status === 'visible' && item.show))
    )
  }

  return store.getters['entities/getServiceEmployees'](selectedServiceId.value)
})

let locations = computed(() => {
  let relations = store.getters['entities/getEntitiesRelations']
  let unfilteredLocations = store.getters['entities/getUnfilteredLocations']
  let locationsIds = []

  if (bookable.value) {
    if (selectedEmployee.value) {
      locationsIds = relations[selectedEmployee.value][bookable.value.service.id]
    } else {
    let employeesIds = []

    if (bookable.value.providers.length) {
      employeesIds = bookable.value.providers.map(item => item.id)
    } else {
      for (let employeeId in relations) {
        if (bookable.value.service.id in relations[employeeId]) {
          employeesIds.push(parseInt(employeeId))
        }
      }
    }

    for (let employeeId in relations) {
      if (bookable.value.service.id in relations[employeeId] && employeesIds.indexOf(parseInt(employeeId)) !== -1) {
        locationsIds = locationsIds.concat(relations[employeeId][bookable.value.service.id])
      }
    }
  }

    let availableLocationsIds = bookable.value.locations.length ?
      bookable.value.locations.map(item => item.id) : unfilteredLocations.map(item => item.id)

    return unfilteredLocations.filter(item =>
      availableLocationsIds.indexOf(item.id) !== -1 &&
      locationsIds.indexOf(item.id) !== -1 &&
      (store.getters['entities/getShowHidden'] || item.status === 'visible')
    )
  }

  return []
})

let targetPurchase = computed(() => {
  let packageReservations = []

  Object.keys(props.data.services).forEach((serviceId) => {
    Object.keys(props.data.services[serviceId].appointments).forEach((dateKey) => {
      props.data.services[serviceId].appointments[dateKey].appointments.forEach((appointment) => {
        packageReservations.push(appointment)
      })
    })
  })

  return {
    type: 'package',
    package: selectedPackage.value,
    booking: null,
    bookable: selectedPackage.value,
    paymentId: props.data.packageData.payments.length
      ? props.data.packageData.payments[0].id
      : null,
    customer: store.getters['auth/getProfile'],
    packageCustomerId: parseInt(selectedPackageCustomerId.value),
    packageReservations: packageReservations,
    payments: props.data.packageData.payments,
    price: props.data.packageData.price,
    tax: props.data.packageData.tax,
    coupon: props.data.packageData.coupon,
    discount: props.data.packageData.discount,
    end: props.data.packageData.end
  }
})

let activePurchaseCancel = ref(false)

let selectedServiceId = ref(null)

let targetAppointment = ref(null)

let slotsParams = ref(null)

onMounted(() => {
  selectedServiceId.value = servicesOrderInPackage.value[0]
})

function getPurchasedCount (purchaseServiceData, id, type) {
  let count = 0

  Object.keys(purchaseServiceData).forEach((serviceId) => {
    if (id === null || parseInt(id) === parseInt(serviceId)) {
      count += purchaseServiceData[serviceId].purchaseData[type]
    }
  })

  return count
}

function bookedNumberText (numb) {
  return numb === 1 ? amLabels.value.appointment_booked : amLabels.value.appointments_booked
}

function packagesSlotsCalculation(data, id = null) {
  let notBooked = data.packageData.sharedCapacity ? data.packageData.sharedCount : getPurchasedCount(data.services, id, 'count')
  let capacity = data.packageData.sharedCapacity ? data.packageData.sharedTotal : getPurchasedCount(data.services, id, 'total')

  return `${capacity - notBooked}/${capacity} ${bookedNumberText(capacity - notBooked)}`
}

function selectService (tab) {
  selectedServiceId.value = tab.paneName
}

function bookAppointment () {
  let user = store.getters['auth/getProfile']

  let timeZone = store.getters['cabinet/getTimeZone'] ? store.getters['cabinet/getTimeZone'] : 'UTC'

  let params = {
    serviceId: parseInt(selectedServiceId.value),
    group: 1,
    timeZone: store.getters['cabinet/getTimeZone'],
    structured: true,
    page: 'cabinet'
  }

  let bookable = selectedPackage.value.bookable.find(
    i => parseInt(i.service.id) === parseInt(selectedServiceId.value)
  )

  // Return employees for this specific service if service is removed from the package, but it is in the "package booking"
  let employeeIds = bookable
    ? bookable.providers.map(i => i.id)
    : store.getters['entities/getServiceEmployees'](selectedServiceId.value).map(i => i.id)

  if (employeeIds.length) {
    params.providerIds = employeeIds
  }

  params.locationId = null

  slotsParams.value = params

  targetAppointment.value = {
    type: 'appointment',
    bookings: [
      {
        packageCustomerService: {
          id: props.data.services[selectedServiceId.value].purchaseData.packageCustomerServiceId
        },
        customer: {
          id: user.id,
          firstName: user.firstName,
          lastName: user.lastName,
          email: user.email,
          phone: user.phone,
          countryPhoneIso: user.countryPhoneIso,
          externalId: user.externalId,
          translations: user.translations
        },
        customerId: user.id,
        customFields: null,
        persons: 1,
        extras: [],
        deposit: 0,
      }
    ],
    serviceId: selectedServiceId.value,
    locationId: props.data.services[selectedServiceId.value].purchaseData.locitionId,
    providerId: props.data.services[selectedServiceId.value].purchaseData.employeeId,
    isGroup: true,
    notifyParticipants: 1,
    payment: null,
    recurring: [],
    package: [],
    timeZone: timeZone === '' ? Intl.DateTimeFormat().resolvedOptions().timeZone : timeZone,
    utc: timeZone === '',
    locale: window.localeLanguage[0],
  }
}

function managedAppointment (passedData) {
  targetAppointment.value = null

  emits('booked', passedData)
}

let waitingForCancelation = ref(false)

function cancelPurchase () {
  waitingForCancelation.value = true
  httpClient.post(
    '/packages/customers/' + parseInt(selectedPackageCustomerId.value),
    {
      status: 'canceled'
    },
    useAuthorizationHeaderObject(store)
  ).then(() => {
    activePurchaseCancel.value = false
    emits('canceled', {message: amLabels.value.package_purchase_canceled})
  }).catch((error) => {
    if (error.response) {
      emits('cancelError', error.response.data)
    }
  }).finally(() => {
    waitingForCancelation.value = false
  })
}

function getPackageAppointments (passedData) {
  emits('booked', passedData)
}

function goBack () {
  emits('goBack')
}

// * Customized form data
let amCustomize = inject('amCustomize')

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.value.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  let customizedLabels = amCustomize.value.packageAppointmentsList.translations
  if (customizedLabels) {
    Object.keys(customizedLabels).forEach(labelKey => {
      if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
        computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
})

provide('amLabels', amLabels)


let alertVisibility = computed(() => {
  return store.getters['cabinet/getPaymentLinkError'].package
})
let alertType = ref('error')

let alertMessage = ref(amLabels.value.payment_link_error)

function closeAlert () {
  alertVisibility.value = false
  store.commit('cabinet/setPaymentLinkError', {value: false, type: 'package'})
}

function customizedLabels (step) {
  let computedLabels = reactive({...labels})

  let customizedLabels = amCustomize.value[step].translations
  if (customizedLabels) {
    Object.keys(customizedLabels).forEach(labelKey => {
      if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
        computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
}

function customizedOptions (step) {
  return amCustomize.value[step].options
}

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-cappa-text': amColors.value.colorMainText,
    '--am-c-cappa-bgr': amColors.value.colorMainBgr,
    '--am-c-cappa-text-op80': useColorTransparency(amColors.value.colorMainText, 0.8),
    '--am-c-cappa-text-op70': useColorTransparency(amColors.value.colorMainText, 0.7),
    '--am-c-cappa-text-op20': useColorTransparency(amColors.value.colorMainText, 0.2),
    '--am-c-cappa-primary': amColors.value.colorPrimary
  }
})
</script>

<script>
export default {
  name: 'CabinetPackageAppointmentsList',
  key: 'packagesList',
}
</script>

<style lang="scss">
@mixin cabinet-package-appointments {
  // am - amelia
  // cappa - cabinet panel package appointments
  .am-cappa {

    &__back {
      display: flex;
      align-items: center;
      padding-bottom: 16px;
      gap: 12px;

      & > span {
        display: inline-flex;
        font-size: 18px;
        font-weight: 500;
        line-height: 1.5555556;
        color: var(--am-c-cappa-text-op70);
      }
    }

    &__heading {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 0;

      &.am-rw-500 {
        flex-wrap: wrap;
      }

      &-left, &-right {
        display: flex;
        align-items: center;
      }

      &-right {
        &.am-rw-500 {
          width: 100%;
          justify-content: space-between;
          margin-top: 16px;
        }
      }
    }

    &__img {
      position: relative;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      flex: 0 0 auto;
      background-position: center;
      background-size: cover;
      margin-right: 16px;

      &-name {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        color: var(--am-c-cappa-bgr);
        font-size: 18px;
        font-weight: 500;
        line-height: 1;
        background-color: var(--am-c-primary);
        border-radius: 50%;
      }

      &-color {
        display: block;
        width: 22px;
        height: 22px;
        position: absolute;
        bottom: -3px;
        right: -3px;
        border-radius: 50%;
        border: 3px solid var(--am-c-cappa-bgr);
      }
    }

    &__name {
      width: 100%;
      font-size: 18px;
      font-weight: 500;
      line-height: 1.5555556;
      color: var(--am-c-cappa-text);
    }

    &__date {
      width: 100%;
      font-size: 14px;
      font-weight: 400;
      line-height: 1.42857;
      color: var(--am-c-cappa-text-op80);
    }

    &__capacity {
      width: 100%;
      font-size: 15px;
      font-weight: 400;
      line-height: 1.6;
      color: var(--am-c-cappa-text);
    }

    &__actions {
      &.am-button {
        margin-left: 16px;

        &.am-rw-500 {
          margin: 0;
        }
      }
    }

    &__service {
      &-heading {
        font-size: 15px;
        font-weight: 500;
        color: var(--am-c-cappa-text);
      }

      &-top {
        display: flex;
        align-items: center;
        justify-content: space-between;

        &.am-rw-500 {
          flex-wrap: wrap;
        }
      }

      &-right {
        display: flex;
        align-items: center;
      }

      &-img {
        position: relative;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        flex: 0 0 auto;
        background-position: center;
        background-size: cover;
        margin-right: 16px;

        &__name {
          display: flex;
          align-items: center;
          justify-content: center;
          width: 100%;
          height: 100%;
          color: var(--am-c-cappa-bgr);
          font-size: 18px;
          font-weight: 500;
          line-height: 1;
          background-color: var(--am-c-primary);
          border-radius: 50%;
        }

        &__color {
          display: block;
          width: 22px;
          height: 22px;
          position: absolute;
          bottom: -3px;
          right: -3px;
          border-radius: 50%;
          border: 3px solid var(--am-c-cappa-bgr);
        }
      }

      &-name {
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        color: var(--am-c-cappa-text);
      }

      &-capacity {
        font-size: 13px;
        font-weight: 400;
        line-height: 1.384615;
        color: var(--am-c-cappa-text-op80);
      }

      &-book {
        &.am-rw-500 {
          width: 100%;
          margin-top: 16px;
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include cabinet-package-appointments;
}
</style>
