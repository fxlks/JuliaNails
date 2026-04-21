<template>
  <div
    ref="pageContainer"
    class="am-cap"
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
        <span class="am-icon-checkmark-circle-full"></span> {{ successMessage }}
      </template>
    </AmAlert>

    <CabinetFilters
      v-show="pageKey === 'list'"
      :step-key="'packages'"
      :responsive-class="responsiveClass"
      @change-filters="filterPackages"
    />
    <component
      :is="packagePages[pageKey].template"
      v-if="!loading"
      v-bind="packagePages[pageKey].props"
      v-on="packagePages[pageKey].handlers"
    ></component>
    <Skeleton v-else></Skeleton>
  </div>
</template>

<script setup>
import moment from "moment";

// * import from Vue
import {
  ref,
  computed,
  inject,
  onMounted,
  watch,
  provide,
  markRaw,
  nextTick
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Import from Libraries
import httpClient from "../../../../../plugins/axios";

// * Dedicated components
import PackagesList from "./parts/PackagesList.vue";
import PackageAppointmentsList from "./parts/PackageAppointmentsList.vue";
import CabinetFilters from "../parts/Filters.vue";

// * Templates
import Skeleton from "../../common/parts/Skeleton.vue";

// * Composables
import {
  useAuthorizationHeaderObject
} from "../../../../../assets/js/public/panel";
import {
  useParsedAppointments,
} from "../../../../../assets/js/admin/appointment";
import {
  useUrlParams
} from "../../../../../assets/js/common/helper";
import {
  useResponsiveClass
} from "../../../../../assets/js/common/responsive";
import {
  getDateRange
} from "../../../../../assets/js/common/date";
import AmAlert from "../../../../_components/alert/AmAlert.vue";
import {useScrollTo} from "../../../../../assets/js/common/scrollElements";

// * Vars
let store = useStore()

let props = defineProps({
  loadBookingsCounter: {
    type: Number,
    default: 0
  },
})

// * Page Content width
let pageContainer = ref(null)
let pageWidth = ref(0)

// * Sidebar collapsed var
let sidebarCollapsed = inject('sidebarCollapsed')

// * window resize listener
window.addEventListener('resize', resize);
// * resize function
function resize() {
  if (pageContainer.value) {
    pageWidth.value = pageContainer.value.offsetWidth
  }
}

watch(sidebarCollapsed, (current) => {
  if (current) {
    setTimeout(() => {
      collapseTriggered()
    }, 1500)
  } else {
    setTimeout(() => {
      collapseTriggered()
    }, 500)
  }
})

function collapseTriggered () {
  pageWidth.value = pageContainer.value.offsetWidth
}

onMounted(() => {
  nextTick(() => {
    pageWidth.value = pageContainer.value.offsetWidth
  })
})

let responsiveClass = computed(() => {
  return useResponsiveClass(pageWidth.value)
})

// * Alert block
let alertContainer = ref(null)
let alertVisibility = ref(false)
let alertType = ref('success')

// * Success message
let successMessage = ref('')

function closeAlert () {
  alertVisibility.value = false
  successMessage.value = ''
}

// * Cabinet type
let cabinetType = inject('cabinetType')
store.commit('cabinetFilters/setDates', getDateRange(cabinetType.value))

// * Loading
let loading = computed(() => store.getters['cabinet/getPackageLoading'])

let purchases = ref([])

let selectedPackageCustomerId = ref(null)

function filterPackages () {
  let packagesFilter = store.getters['cabinetFilters/getPackages']
  let servicesFilter = store.getters['cabinetFilters/getServices']
  let providersFilter = store.getters['cabinetFilters/getProviders']
  let locationsFilter = store.getters['cabinetFilters/getLocations']
  purchases.value = allPurchases.value.filter((item) => {
    let entities = store.getters['entities/getPackageEntities'](item[1].packageData.id)
    return (servicesFilter.length === 0 || servicesFilter.filter(s => entities.services.includes(s)).length > 0) &&
      (providersFilter.length === 0 || providersFilter.filter(p => entities.providers.includes(p)).length > 0) &&
      (locationsFilter.length === 0 || locationsFilter.filter(l => entities.locations.includes(l)).length > 0) &&
        (packagesFilter.length === 0 || packagesFilter.filter(p => entities.packages.includes(p)).length > 0)
  })
}

function purchasedCount (data, id, type) {
  let count = 0

  Object.keys(data).forEach((serviceId) => {
    if (id === null || parseInt(id) === parseInt(serviceId)) {
      count += data[serviceId].purchaseData[type]
    }
  })

  return count
}

function packagesSlotsCalculation (data) {
  let notBooked = purchasedCount(data.services, null, 'count')
  let capacity = purchasedCount(data.services, null, 'total')

  return (capacity - notBooked) === capacity
}

function getPackagesAppointments (passedData) {
  store.commit('cabinet/setPackageLoading', true)

  let timeZone = store.getters['cabinet/getTimeZone']

  let params = JSON.parse(JSON.stringify(store.getters['cabinetFilters/getPackagesFilters']))
  params.timeZone = timeZone
  params.source = 'cabinet-' + cabinetType.value
  params.activePackages = 0

  httpClient.get(
    '/appointments',
    useUrlParams(
      Object.assign(
        useAuthorizationHeaderObject(store),
          {params: params}
      )
    )
  ).then((response) => {
    let purchasesItems = {}

    response.data.data.availablePackageBookings.forEach((item) => {
      item.packages.forEach((packageItem) => {
        packageItem.services.forEach((serviceItem) => {
          serviceItem.bookings.forEach((bookingItem) => {
            let pack = store.getters['entities/getPackage'](packageItem.packageId)

            if (pack) {
              if (!(bookingItem.packageCustomerId in purchasesItems)) {
                purchasesItems[bookingItem.packageCustomerId] = {
                  packageData: {
                    id: pack.id,
                    name: pack.name,
                    start: bookingItem.start,
                    end: bookingItem.end,
                    price: bookingItem.price,
                    tax: bookingItem.tax,
                    coupon: bookingItem.coupon,
                    status: bookingItem.status,
                    type: 'package',
                    payments: bookingItem.payments,
                    sharedCapacity: bookingItem.sharedCapacity,
                    sharedTotal: bookingItem.total,
                    sharedCount: bookingItem.count,
                    discount: 0,
                    color: pack.color,
                    pictureFullPath: pack.pictureFullPath,
                    pictureThumbPath: pack.pictureThumbPath
                  },
                  services: {},
                }
              }

              if (!(serviceItem.serviceId in purchasesItems[bookingItem.packageCustomerId].services)) {
                let service = store.getters['entities/getService'](serviceItem.serviceId)

                purchasesItems[bookingItem.packageCustomerId].services[serviceItem.serviceId] = {
                  appointments: {},
                  purchaseData: {
                    packageCustomerServiceId: bookingItem.id,
                    total: bookingItem.total,
                    count: bookingItem.count,
                    employeeId: bookingItem.employeeId,
                    locationId: bookingItem.locationId,
                    name: service.name,
                    color: service.color,
                    pictureThumbPath: service.pictureThumbPath,
                  },
                }
              }
            }
          })
        })
      })
    })

    let parsedAppointments = useParsedAppointments(response.data.data.appointments, timeZone, cabinetType.value === 'provider')

    Object.keys(parsedAppointments).forEach((dateKey) => {
      parsedAppointments[dateKey].appointments.forEach((appointment) => {
        appointment.bookings.forEach((booking) => {
          let pcId = booking.packageCustomerService.packageCustomer.id

          if (pcId in purchasesItems) {
            if (!(dateKey in purchasesItems[pcId].services[appointment.serviceId].appointments)) {
              purchasesItems[pcId].services[appointment.serviceId].appointments[dateKey] = {
                date: dateKey,
                appointments: [],
              }
            }

            purchasesItems[pcId].services[appointment.serviceId].appointments[dateKey].appointments.push(appointment)
          }
        })
      })
    })

    let packCanceled = Object.entries(purchasesItems).filter(item => item[1].packageData.status === 'canceled')
    let packFull = Object.entries(purchasesItems).filter(item => {
      return item[1].packageData.status !== 'canceled'
        && packagesSlotsCalculation(item[1])
    })
    let packUnlimited = Object.entries(purchasesItems).filter(item => {
      return item[1].packageData.status !== 'canceled'
        && !packagesSlotsCalculation(item[1])
        && item[1].packageData.end === null
    })
    let packExpire = Object.entries(purchasesItems).filter(item => {
      return item[1].packageData.status !== 'canceled'
        && !packagesSlotsCalculation(item[1])
        && item[1].packageData.end !== null
    }).sort((a, b) => moment(a[1].packageData.end, 'YYYY-MM-DD HH:mm:ss').diff(moment(b[1].packageData.end, 'YYYY-MM-DD HH:mm:ss'), 'minutes'))

    purchases.value = [...packExpire, ...packUnlimited, ...packFull, ...packCanceled]
    allPurchases.value = [...packExpire, ...packUnlimited, ...packFull, ...packCanceled]

    let filterEmpty = !(params.packages.length || params.services.length || params.providers.length || params.locations.length)

    if (filterEmpty) {
      let serviceIds = []
      let providerIds = []
      let locationIds = []
      let packagesIds = []
     purchases.value.forEach(pack => {
        let entities = store.getters['entities/getPackageEntities'](pack[1].packageData.id)
        serviceIds = serviceIds.concat(entities.services)
        providerIds = providerIds.concat(entities.providers)
        locationIds = locationIds.concat(entities.locations)
        packagesIds = packagesIds.concat(entities.packages)
      })

      store.dispatch('cabinetFilters/injectServiceOptions', serviceIds.filter((item, index, array) => array.indexOf(item) === index))
      store.dispatch('cabinetFilters/injectProviderOptions', providerIds.filter((item, index, array) => array.indexOf(item) === index))
      store.dispatch('cabinetFilters/injectLocationOptions', locationIds.filter((item, index, array) => array.indexOf(item) === index))
      store.dispatch('cabinetFilters/injectPackagesOptions', packagesIds.filter((item, index, array) => array.indexOf(item) === index))
    }
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    store.commit('cabinet/setPackageLoading', false)
    if (passedData && 'message' in passedData) {
      alertVisibility.value = true
      successMessage.value = passedData.message

      if (pageContainer.value && alertContainer.value) {
        setTimeout(function () {
          useScrollTo(pageContainer.value, alertContainer.value.$el, 0, 300)
        }, 500)
      }
    }
  })
}

function packageCancelationError (passedData) {
  alertVisibility.value = true
  successMessage.value = passedData.message
  alertType.value = 'error'

  if (pageContainer.value && alertContainer.value) {
    setTimeout(function () {
      useScrollTo(pageContainer.value, alertContainer.value.$el, 0, 300)
    }, 500)
  }
}

provide('packageSelection', {
  selectedPackageCustomerId,
})

let allPurchases = ref([])

watch(() => props.loadBookingsCounter, () => {
  getPackagesAppointments()
})

onMounted(() => {
  getPackagesAppointments()
})

let packagePages = computed(() => {
  return {
    list: {
      template: markRaw(PackagesList),
      props: {
        packages: purchases.value,
        responsiveClass: responsiveClass.value,
      },
      handlers: {
        click: selectPurchase
      }
    },
    item: {
      template: markRaw(PackageAppointmentsList),
      props: {
        data: selectedPackageCustomerId.value ? purchases.value.find(p => p[0] === selectedPackageCustomerId.value)[1] : {},
        responsiveClass: responsiveClass.value,
        pageWidth: pageWidth.value,
      },
      handlers: {
        goBack: goBack,
        booked: getPackagesAppointments,
        canceled: getPackagesAppointments,
        cancelError: packageCancelationError
      }
    }
  }
})

let pageKey = ref('list')

function selectPurchase (packageCustomerId) {
  selectedPackageCustomerId.value = packageCustomerId
  pageKey.value = 'item'
}

function goBack () {
  selectedPackageCustomerId.value = null
  pageKey.value = 'list'
}
</script>

<script>
export default {
  name: 'CabinetPackages',
  key: 'packages'
}
</script>

<style lang="scss">
@mixin am-cabinet-appointments {
  .am-cap {
    &__alert {
      margin: 0 0 16px;
      .el-alert {
        padding: 4px 12px 4px 0;
        box-sizing: border-box;

        &__content {
          .el-alert__closebtn {
            top: 50%;
            transform: translateY(-50%);
          }
        }

        &__title {
          display: flex;
          align-items: center;
          font-size: 16px;
          line-height: 1.5;

          .am-icon-checkmark-circle-full {
            font-size: 28px;
            line-height: 1;
            color: var(--am-c-alerts-bgr);
          }
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-appointments;
}
</style>
