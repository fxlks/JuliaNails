<template>
  <div
    ref="pageContainer"
    class="am-cap am-capa-main"
    :class="{'am-capai-main': appointmentVisibility}"
  >
    <div
      class="am-capa-main__inner"
      :class="responsiveClass"
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
          <span v-if="alertType === 'success'" class="am-icon-checkmark-circle-full"></span>
          <span v-if="alertType === 'error'" class="am-icon-clearable"></span>
          {{ alertMessage }}
        </template>
      </AmAlert>

      <CabinetFilters
        v-if="!appointmentVisibility && ready"
        :step-key="'appointments'"
        :responsive-class="responsiveClass"
        :empty="false"
        @add-appointment="addAppointment"
        @change-filters="getAppointments"
      />

      <div
        v-if="shortcodeData.cabinetType === 'employee' && amSettings.roles.allowWriteAppointments && !appointmentVisibility && ready"
        class="am-cap__actions"
      >
        <AmButton
          prefix="plus"
          size="small"
          category="primary"
          :type="amCustomize.appointments.options.newAppBtn.buttonType"
          @click="addAppointment"
        >
          <span>{{amLabels.new_appointment}}</span>
        </AmButton>
      </div>

      <template v-if="!loading && ready">
        <AppointmentsList
          v-if="!appointmentVisibility && dateGroupedAppointments && Object.keys(dateGroupedAppointments).length > 0"
          :grouped-appointments="dateGroupedAppointments"
          :page-width="pageWidth"
          :responsive-class="responsiveClass"
          step-key="appointments"
          @canceled="getAppointments"
          @booked="getAppointments"
          @edit-appointment="editAppointment"
          @status-change="appointmentStatusChange"
        ></AppointmentsList>
        <EmptyState
          v-else-if="!appointmentVisibility"
          :heading="amLabels.no_app_found"
          :text="amLabels.have_no_app"
        ></EmptyState>
        <AmPagination
          v-if="!appointmentVisibility && dateGroupedAppointments && Object.keys(dateGroupedAppointments).length > 0 && appointmentsCount > amSettings.general.itemsPerPage"
          :page-size="amSettings.general.itemsPerPage"
          :pager-count="5"
          layout="prev, pager, next"
          :total="appointmentsCount"
          :current-page="appointmentsPage"
          @current-change="appointmentsPageChange"
        />
        <Appointment
          v-if="appointmentVisibility"
          :page-width="pageWidth"
          :responsive-class="responsiveClass"
          :linked-appointments="linkedAppointments"
          @edit-linked-appointment="editAppointment"
          @close="closeAppointment"
          @save="saveAppointmentCallback"
        ></Appointment>
      </template>
      <Skeleton v-else></Skeleton>
    </div>
  </div>
</template>

<script setup>
// * import from Vue
import {
  ref,
  computed,
  inject,
  provide,
  onMounted,
  watch,
  reactive,
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Import from Libraries
import httpClient from "../../../../../plugins/axios";
import moment from "moment";

// * Components Width and Height
import { useElementSize } from '@vueuse/core';

// * Components
import AmAlert from "../../../../_components/alert/AmAlert.vue";
import AmButton from "../../../../_components/button/AmButton.vue";
import AmPagination from "../../../../_components/pagination/AmPagination.vue";

// * Parts
import CabinetFilters from "../parts/Filters.vue";
import AppointmentsList from "./parts/AppointmentsList.vue";
import Appointment from "./parts/Appointment.vue";
import Skeleton from "../../common/parts/Skeleton.vue";
import EmptyState from "../parts/EmptyState.vue"

// * Composables
import {
  useAuthorizationHeaderObject
} from "../../../../../assets/js/public/panel";
import {
  getDateRange,
} from "../../../../../assets/js/common/date";
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
  useScrollTo
} from "../../../../../assets/js/common/scrollElements";

// * Store
let store = useStore()

// * Root Settings
const amSettings = inject('settings')

// * Customized form data
let amCustomize = inject('amCustomize')

// * Data in shortcode
const shortcodeData = inject('shortcodeData')

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  let customizedLabels = amCustomize.value.appointments.translations
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

// * Page Content width
const pageContainer = ref(null)
const { width: pageWidth } = useElementSize(pageContainer)

let responsiveClass = computed(() => {
  return useResponsiveClass(pageWidth.value)
})

provide('pageWidth', pageWidth)

// * Alert block
let alertContainer = ref(null)
let alertVisibility = ref(false)
let alertType = ref('success')

let alertMessage = ref('')

function closeAlert () {
  alertVisibility.value = false
  store.commit('cabinet/setPaymentLinkError', {value: false, type: 'appointment'})
}

/********
 * Form *
 ********/
let props = defineProps({
  loadBookingsCounter: {
    type: Number,
    default: 0
  },
  appointments: {
    type: Object,
    default: null
  },
})

// * Cabinet type
let cabinetType = inject('cabinetType')
store.commit('cabinetFilters/setDates', getDateRange(cabinetType.value))

// * Loading
let loading = computed(() => store.getters['cabinet/getAppointmentsLoading'])

let ready = computed(() => store.getters['entities/getReady'])

let dateGroupedAppointments = ref(null)

let appointmentsCount = ref(0)

let appointmentsPage = ref(1)

function appointmentsPageChange(page) {
  appointmentsPage.value = page

  getAppointments(null, page)
}

function getAppointments (passedData = null, page = 1) {
  store.commit('cabinet/setAppointmentsLoading', true)

  let params = JSON.parse(JSON.stringify(store.getters['cabinetFilters/getAppointmentsFilters']))
  let timeZone = store.getters['cabinet/getTimeZone']

  params.dates = params.dates.map(d => moment(d).format('YYYY-MM-DD'))
  params.timeZone = timeZone
  params.source = 'cabinet-' + cabinetType.value

  params.page = page

  store.commit('auth/setLoadingAppointmentsCounter', store.getters['auth/getLoadingAppointmentsCounter'] + 1)

  let loadingCounter = store.getters['auth/getLoadingAppointmentsCounter']

  httpClient.get(
    '/appointments',
    Object.assign(
      useAuthorizationHeaderObject(store),
      {
        params: useUrlParams(params)
      }
    )
  ).then((response) => {
    if (loadingCounter !== store.getters['auth/getLoadingAppointmentsCounter']) {
      return
    }

    appointmentsCount.value = response.data.data.total

    dateGroupedAppointments.value = useParsedAppointments(
      response.data.data.appointments,
      timeZone,
      cabinetType.value === 'provider'
    )
  }).catch((error) => {
    if(error?.response?.data?.data?.reauthorize !== undefined && error.response.data.data.reauthorize) {
      store.dispatch('auth/logout')
    }

    console.log(error)
  }).finally(() => {
    if (loadingCounter !== store.getters['auth/getLoadingAppointmentsCounter']) {
      return
    }

    store.commit('cabinet/setAppointmentsLoading', false)
    if (passedData && 'message' in passedData) {
      alertVisibility.value = true
      alertMessage.value = passedData.message
      alertType.value = 'success'

      if (pageContainer.value && alertContainer.value) {
        setTimeout(function () {
          useScrollTo(pageContainer.value, alertContainer.value.$el, 0, 300)
        }, 500)
      }
    }
  })
}

/***************
 * Appointment *
 ***************/
let linkedAppointments = ref([])

function editAppointment (appointment) {
  store.commit('cabinet/setAppointmentsLoading', true)

  httpClient.get(
    '/appointments/' + appointment.id,
    Object.assign(
      {
        params: {
          source: 'cabinet-' + cabinetType.value,
          timeZone: store.getters['cabinet/getTimeZone'],
        },
      },
      useAuthorizationHeaderObject(store)
    )
  ).then((response) => {
    let startDateTime = response.data.data.appointment.bookingStart.split(' ')

    let service = store.getters['entities/getService'](response.data.data.appointment.serviceId)

    let bookings = []

    response.data.data.appointment.bookings.forEach((booking) => {
      let extras = []

      service.extras.sort((a, b) => a.position - b.position).forEach((extra) => {
        extras.push({
          extraId: extra.id,
          quantity: 0,
        })

        let bookingExtra = booking.extras.find(i => i.extraId === extra.id)

        if (typeof bookingExtra !== 'undefined') {
          extras[extras.length - 1] = bookingExtra
        }
      })

      let customFields = booking.customFields ? JSON.parse(booking.customFields) : {}

      store.getters['entities/getCustomFields'].forEach((customField) => {
        if (customField.allServices ||
          customField.services.some((e) => e.id === service.id)
        ) {
          customFields[customField.id] = customFields[customField.id] ? Object.assign(
            {},
            customFields[customField.id],
            {
              value: customFields[customField.id].type === 'datepicker'
                ? (customFields[customField.id].value ? moment(customFields[customField.id].value).toDate() : '')
                : customFields[customField.id].value,
            }
          ) : {
            label: customField.label,
            type: customField.type,
            value: customField.type === 'checkbox' ? [] : '',
            position: customField.position,
          }
        }
      })

      bookings.push({
        id: booking.id,
        customer: appointment.bookings.find(b => b.id === booking.id).customer,
        persons: booking.persons,
        status: booking.status,
        duration: booking.duration ? booking.duration.toString() : null,
        extras: extras,
        customFields: customFields,
        payments: booking.payments,
        price: booking.price,
        aggregatedPrice: booking.aggregatedPrice,
        tax: booking.tax,
        coupon: booking.coupon,
      })
    })

    store.commit(
      'appointment/setAppointment',
      {
        id: response.data.data.appointment.id,
        categoryId: service.categoryId,
        serviceId: response.data.data.appointment.serviceId,
        providerId: response.data.data.appointment.providerId,
        locationId: response.data.data.appointment.locationId,
        internalNotes: response.data.data.appointment.internalNotes,
        lessonSpace: response.data.data.appointment.lessonSpace !== null
          ? response.data.data.appointment.lessonSpace.split('https://www.thelessonspace.com/space/')[1]
          : null,
        startDate: moment(startDateTime[0]).toDate(),
        startTime: startDateTime[1].substring(0, 5),
        bookings: bookings,
        notifyParticipants: !!response.data.data.appointment.notifyParticipants,
        createPaymentLinks: !!response.data.data.appointment.createPaymentLinks,
      }
    )

    linkedAppointments.value = response.data.data.recurring

    appointmentVisibility.value = true
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    store.commit('cabinet/setAppointmentsLoading', false)
  })
}

let appointmentVisibility = ref(false)

function saveAppointmentCallback () {
  alertVisibility.value = true
  alertMessage.value = amLabels.value.appointment_saved
  alertType.value = 'success'

  if (pageContainer.value && alertContainer.value) {
    setTimeout(function () {
      useScrollTo(pageContainer.value, alertContainer.value.$el, 0, 300)
    }, 500)
  }

  getAppointments()

  closeAppointment()
}

function appointmentStatusChange (message, status) {
  alertMessage.value = message
  alertType.value = status

  alertVisibility.value = true
}

function addAppointment () {
  resetAppointment()

  appointmentVisibility.value = true
}

function closeAppointment () {
  appointmentVisibility.value = false

  resetAppointment()
}

function resetAppointment () {
  store.commit('appointment/resetAppointment', {providerId: store.getters['auth/getProfile'].id})
  store.commit('customerInfo/setCustomers', [])
  store.commit('customerInfo/setCustomersIds', [])
}

watch(() => props.loadBookingsCounter, () => {
  getAppointments()
})

onMounted(() => {
  getAppointments()
})
</script>

<script>
export default {
  name: 'CabinetAppointments',
  key: 'appointments'
}
</script>

<style lang="scss">
@mixin am-cabinet-appointments {
  .am-cap {
    &__actions {
      width: 100%;
      display: flex;
      justify-content: flex-end;
      margin: 0 0 16px;

      .am-button {
        .am-icon-plus {
          font-size: 24px;
        }
      }
    }
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

          .am-icon-clearable {
            font-size: 28px;
            line-height: 1;
            color: var(--am-c-alerte-bgr);
          }
        }
      }
    }
  }

  .am-fs__main-content.am-cap {
    //  am    - amelia
    //  capai - cabinet panel appointments item
    &.am-capai-main {
      height: calc(100% - 148px);
    }

    &.am-capa-main {
      padding: 0;

      .am-capa-main__inner {
        display: block;
        padding: 16px 32px;

        &.am-rw- {
          &480 {
            padding: 16px;
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
