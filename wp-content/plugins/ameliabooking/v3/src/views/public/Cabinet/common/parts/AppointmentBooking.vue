<template>
  <AmSlidePopup
    :visibility="props.visibility"
    custom-class="am-csd am-csd__reschedule"
    :style="cssDialogVars"
    position="center"
  >
    <div
      ref="popupInnerRef"
      class="am-csd__inner"
    >
      <div class="am-csd__header">
        <div class="am-csd__header-text">
          {{ labels.no_selected_slot_requirements }}
        </div>
        <div
          class="am-csd__header-btn"
          @click="closeDialog"
        >
          <IconComponent icon="close"></IconComponent>
        </div>
      </div>
      <div
        ref="rescheduleRef"
        class="am-csd__content"
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
            <span class="am-icon-clearable"></span> {{ alertMessage }}
          </template>
        </AmAlert>

        <!-- Packages filters -->
        <template v-if="filterFormVisibility">
          <el-form
            ref="packageFormRef"
            class="am-csd__filter-wrapper"
            :rules="rules"
            :model="packageFormData"
            label-position="top"
          >
            <div
              class="am-csd__filter am-csd__filter-employee"
              :class="responsiveClass"
            >
              <el-form-item
                v-if="props.employees.length && employeeVisibility && props.customizedOptions.employee.visibility"
                :class="[{'am-csd__filter-full': !(props.locations.length && locationVisibility && props.customizedOptions.location.visibility)}, responsiveClass]"
                :label="`${labels.package_appointment_employee}:`"
                :prop="'employee'"
              >
                <AmSelect
                  v-model="packageFormData.employee"
                  clearable
                  :filterable="props.customizedOptions.employee.filterable"
                  :placeholder="`${labels.package_select_employee}...`"
                  :fit-input-width="true"
                  :popper-class="'am-csd__filter-employees'"
                  :disabled="slotsAreLoading"
                  :filter-method="filterEmployee"
                  @change="changeFilter"
                >
                  <AmOption
                    v-for="provider in filteredEmployees"
                    :key="provider.id"
                    :value="provider.id"
                    :label="`${provider.firstName} ${provider.lastName}`"
                  >
                    <AmOptionTemplate2
                      :identifier="provider.id"
                      :label="`${provider.firstName} ${provider.lastName}`"
                      :price="0"
                      :image-thumb="provider.pictureThumbPath"
                      :description="provider.description"
                      :dialog-title="labels.employee_information_package"
                      :dialog-button-text="labels.select_this_employee_package"
                      :badge="provider.badge"
                      @click="selectEmployee"
                    ></AmOptionTemplate2>
                  </AmOption>
                </AmSelect>
              </el-form-item>

              <el-form-item
                v-if="props.locations.length && locationVisibility && props.customizedOptions.location.visibility"
                :class="[{'am-csd__filter-full': !(props.employees.length && employeeVisibility && props.customizedOptions.employee.visibility)}, responsiveClass]"
                :label="`${labels.package_appointment_location}:`"
                :prop="'location'"
              >
                <AmSelect
                  v-model="packageFormData.location"
                  clearable
                  :filterable="props.customizedOptions.location.filterable"
                  :placeholder="`${labels.package_select_location}...`"
                  :fit-input-width="true"
                  :disabled="slotsAreLoading"
                  :filter-method="filterLocation"
                  @change="changeFilter"
                >
                  <AmOption
                    v-for="location in filteredLocations"
                    :key="location.id"
                    :value="location.id"
                    :label="location.name"
                  ></AmOption>
                </AmSelect>
              </el-form-item>
            </div>
          </el-form>
        </template>

        <Calendar
          v-if="props.appointment !== null"
          :id="0"
          ref="calendarRef"
          :preselect-slot="false"
          :load-counter="loadCounter"
          :end-time="props.customizedOptions.endTimeVisibility.visibility"
          :time-zone="props.customizedOptions.timeZoneVisibility.visibility"
          :show-estimated-pricing="props.appointment.id && 'estimatedPricingVisibility' in props.customizedOptions ? props.customizedOptions.estimatedPricingVisibility.visibility : false"
          :show-indicator-pricing="props.appointment.id && 'indicatorPricingVisibility' in props.customizedOptions ? props.customizedOptions.indicatorPricingVisibility.visibility : false"
          :show-slot-pricing="props.appointment.id && 'slotPricingVisibility' in props.customizedOptions ? props.customizedOptions.slotPricingVisibility.visibility : false"
          :label-slots-selected="labels.date_time_slots_selected"
          :fetched-slots="null"
          :service-id="parseInt(props.appointment.serviceId)"
          :is-package="props.appointment.bookings[0].packageCustomerService !== null"
          :date="props.appointment && props.appointment.bookingStart ? props.appointment.bookingStart.split(' ')[0] : ''"
          :slots-params="slotsProps"
        ></Calendar>
      </div>
    </div>
    <template #footer>
      <div class="am-csd__footer">
        <AmButton
          category="secondary"
          :size="popupInnerWidth <= 360 ? 'small' : 'default'"
          :type="props.customizedOptions.cancelBtn.buttonType"
          :disabled="slotsAreLoading || calendarLoadingState"
          @click="closeDialog"
        >
          {{ labels.cancel }}
        </AmButton>
        <AmButton
          :size="popupInnerWidth <= 360 ? 'small' : 'default'"
          :type="props.customizedOptions.continueBtn.buttonType"
          :disabled="!!(slotsAreLoading || calendarLoadingState || !appointmentDate || !appointmentTime)"
          @click="processBooking"
        >
          {{ labels.continue }}
        </AmButton>
      </div>
    </template>
  </AmSlidePopup>
</template>

<script setup>
// * import from Vue
import {
  ref,
  computed,
  inject,
  watch,
  provide,
  onMounted,
  nextTick
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Import from Libraries
import httpClient from "../../../../../plugins/axios";
import moment from "moment";

// * _components
import AmSlidePopup from "../../../../_components/slide-popup/AmSlidePopup.vue";
import IconComponent from "../../../../_components/icons/IconComponent.vue";
import AmButton from "../../../../_components/button/AmButton.vue";
import AmOption from "../../../../_components/select/AmOption.vue";
import AmSelect from "../../../../_components/select/AmSelect.vue";
import AmOptionTemplate2 from "../../../../_components/select/parts/AmOptionTemplate2.vue";
import AmAlert from "../../../../_components/alert/AmAlert.vue";

// * Templates
import Calendar from "../../../Parts/Calendar.vue";

// * Composables
import { useAuthorizationHeaderObject } from "../../../../../assets/js/public/panel";
import {
  useUtcValue,
  useUtcValueOffset,
} from "../../../../../assets/js/common/date";
import { useColorTransparency } from "../../../../../assets/js/common/colorManipulation.js";
import {useCurrentTimeZone, useSortedDateStrings} from "../../../../../assets/js/common/helper.js";
import { useScrollTo } from "../../../../../assets/js/common/scrollElements";
import { useResponsiveClass } from "../../../../../assets/js/common/responsive";
import {useCreateBookingSuccess} from "../../../../../assets/js/public/booking";

// * Vars
let store = useStore()

// * Component emits
const emits = defineEmits(['close', 'success', 'error', 'employee-selection', 'location-selection'])

// * Settings
const amSettings = inject('settings')

/********
 * Form *
 ********/
let props = defineProps({
  visibility: {
    type: Boolean,
    default: false
  },
  appointment: {
    type: Object,
    default: null
  },
  employees: {
    type: Array,
    default: () => {
      return []
    }
  },
  locations: {
    type: Array,
    default: () => {
      return []
    }
  },
  slotsParams: {
    type: Object,
    default: () => {}
  },
  isPackage: {
    type: Boolean,
    default: false
  },
  labels: {
    type: Object,
    required: true
  },
  customizedOptions: {
    type: Object,
    required: true
  }
})

// * Filter Employees and Locations
let queryEmployeeLower = ref('')
function filterEmployee (query) {
  queryEmployeeLower.value = query.toLowerCase()
}
let filteredEmployees = computed(() => {
  if (queryEmployeeLower.value) {
    return props.employees.filter(item => {
      const fullName = `${item.firstName} ${item.lastName}`.toLowerCase()
      return fullName.includes(queryEmployeeLower.value) && item.show
    })
  }
  return props.employees.filter(e => e.show)
})

let queryLocationLower = ref('')
function filterLocation (query) {
  queryLocationLower.value = query.toLowerCase()
}
let filteredLocations = computed(() => {
  if (queryLocationLower.value) {
    return props.locations.filter(item => {
      return item.name.toLowerCase().includes(queryLocationLower.value)
    })
  }
  return props.locations
})

let amLabels = inject('amLabels')

let appointmentDate = ref(null)
let appointmentTime = ref(null)

watch(() => props.appointment, (current) => {
  setTimeout(() => {
    loadCounter.value++
  }, 200)

  let init = JSON.parse(JSON.stringify(current))
  if (init && init.bookingStart) {
    appointmentDate.value = init.bookingStart.split(' ')[0]
    appointmentTime.value = init.bookingStart.split(' ')[1].substring(0, 5)
  }
})

// * Popup contnte width
let popupInnerRef = ref(null)
let popupInnerWidth = ref(0)

// * Sidebar collapsed var
let sidebarCollapsed = inject('sidebarCollapsed')

// * window resize listener
window.addEventListener('resize', resize);

// * resize function
function resize() {
  if (popupInnerRef.value) {
    popupInnerWidth.value = popupInnerRef.value.offsetWidth
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
  popupInnerWidth.value = popupInnerRef.value.offsetWidth
}

onMounted(() => {
  nextTick(() => {
    popupInnerWidth.value = popupInnerRef.value.offsetWidth
  })
})

let popupReady = computed(() => props.visibility)
watch(popupReady, (newVal) => {
  if (newVal) {
    setTimeout(() => {
      popupInnerWidth.value = popupInnerRef.value.offsetWidth
    }, 300)
  }
})

let responsiveClass = computed(() => {
  return useResponsiveClass(popupInnerWidth.value)
})

// * Alert
let alertVisibility = ref(false)
let alertContainer = ref(null)
let alertMessage = ref('')

function closeAlert () {
  alertVisibility.value = false
  alertMessage.value = ''
}

// * Calendar component reference
let calendarRef = ref(null)

let calendarLoadingState = computed(() => {
  return calendarRef.value ? calendarRef.value.calendarSlotsLoading : true
})

// * Cabinet type
let cabinetType = inject('cabinetType')

let loadCounter = ref(0)

let slotsAreLoading = ref(false)

/**********
 * Filter *
 *********/
let packageFormData = ref({
  employee: null,
  location: null
})

let employeeVisibility = ref(true)
let locationVisibility = ref(true)

let filterFormVisibility = computed(() => {
  return (props.appointment && !props.appointment.id)
    && (props.employees.length || props.locations.length)
    && (props.customizedOptions.employee.visibility || props.customizedOptions.location.visibility)
    && (employeeVisibility.value || locationVisibility.value)
})

let packageFormRef = ref(null)

// * Form validation rules
let rules = computed(() => {
  if (filterFormVisibility.value) {
    return {
      employee: [
        {
          required: 'employee' in props.customizedOptions ? props.customizedOptions.employee.required : false,
          message: props.labels.please_select_employee,
          trigger: 'submit',
        }
      ],
      location: [
        {
          required: 'location' in props.customizedOptions ? props.customizedOptions.location.required : false,
          message: props.labels.please_select_location,
          trigger: 'submit',
        }
      ]
    }
  }

  return {}
})

let slotsProps = computed(() => {
  let params = {}

  if (packageFormData.value.employee) {
    params.providerIds = [packageFormData.value.employee]
  } else if ('providerIds' in props.slotsParams && props.slotsParams.providerIds.length) {
    params.providerIds = props.slotsParams.providerIds
  }

  if (packageFormData.value.location) {
    params.locationId = packageFormData.value.location
  } else if ('locationId' in props.slotsParams && props.slotsParams.locationId) {
    params.locationId = props.slotsParams.locationId
  } else {
    params.locationId = null
  }

  return Object.assign({}, props.slotsParams, params)
})

function changeFilter() {
  if (packageFormRef.value) {
    packageFormRef.value.clearValidate()
  }

  slotsAreLoading.value = true
  loadCounter.value++

  emits('employee-selection', packageFormData.value.employee)
  emits('location-selection', packageFormData.value.location)
}

function selectEmployee (val) {
  packageFormData.value.employee = val
  changeFilter()
}

function closeDialog () {
  if (filterFormVisibility.value) {
    packageFormData.value.employee = null
    packageFormData.value.location = null
    employeeVisibility.value = true
    locationVisibility.value = true
    changeFilter()
  }
  emits('close')
}

watch(popupReady, (newVal) => {
  if (newVal && props.employees.length === 1) {
    packageFormData.value.employee = props.employees[0].id
    employeeVisibility.value = false
  }

  if (newVal && props.locations.length === 1) {
    packageFormData.value.location = props.locations[0].id
    locationVisibility.value = false
  }

  if (newVal && (props.locations.length === 1 || props.employees.length === 1)) {
    changeFilter()
  }
})

let rescheduleRef = ref()
provide('formWrapper', rescheduleRef)

let calendarSlotDuration = computed(() => {
  return store.getters['entities/getService'](props.appointment.serviceId).duration
})

provide('calendarSlotDuration', calendarSlotDuration)

let calendarChangeSideBar = ref(true)

provide('calendarChangeSideBar', calendarChangeSideBar)

let calendarServiceDuration = ref(0)

provide('calendarServiceDuration', calendarServiceDuration)

function processBooking () {
  if ('id' in props.appointment) {
    rescheduleBooking()
  } else {
    addPackageBooking()
  }
}

function getDateTimeData (useUtc) {
  let timeZone = useUtc ? 'UTC' : store.getters['cabinet/getTimeZone']

  let localBookingStart = appointmentDate.value + ' ' + appointmentTime.value

  let bookingStart = null

  let utcOffset = null

  if (useUtc) {
    let utcBookingStart = useUtcValue(localBookingStart)

    utcOffset = useUtcValueOffset(utcBookingStart)

    bookingStart = utcBookingStart
  } else {
    bookingStart = localBookingStart
  }

  return {
    utcOffset: utcOffset,
    bookingStart: bookingStart,
    timeZone: timeZone,
  }
}

function rescheduleBooking () {
  calendarRef.value.calendarSlotsLoading = true

  let bookingData = getDateTimeData(false)

  if (useCurrentTimeZone() === store.getters['cabinet/getTimeZone'] && amSettings.general.showClientTimeZone) {
    bookingData.utcOffset = useUtcValueOffset(useUtcValue(appointmentDate.value + ' ' + appointmentTime.value))
  }

  httpClient.post(
    '/bookings/reassign/' + props.appointment.bookings.filter(i => i.status === 'approved' || i.status === 'pending' || i.status === 'waiting')[0].id,
      bookingData,
    Object.assign(useAuthorizationHeaderObject(store), {params: {source: 'cabinet-' + cabinetType.value}})
  ).then(() => {
    calendarRef.value.calendarSlotsLoading = false
    emits('success', {message: amLabels.value.appointment_rescheduled})
  }).catch((error) => {
    calendarRef.value.calendarSlotsLoading = false
    if (error.response) {
      if (!('data' in error.response.data) && 'message' in error.response.data) {
        alertVisibility.value = true
        alertMessage.value = error.response.data.message
        setTimeout(function () {
          useScrollTo(rescheduleRef.value, alertContainer.value.$el, 0, 300)
        }, 500)
      }

      if ('data' in error.response && 'data' in error.response.data && 'customerAlreadyBooked' in error.response.data.data) {
        alertVisibility.value = true
        alertMessage.value = props.labels.customer_already_booked_app
        setTimeout(function () {
          useScrollTo(rescheduleRef.value, alertContainer.value.$el, 0, 300)
        }, 500)
      }

      if ('data' in error.response && 'data' in error.response.data && 'cancelBookingUnavailable' in error.response.data.data) {
        alertVisibility.value = true
        alertMessage.value = props.labels.booking_cancel_exception
        setTimeout(function () {
          useScrollTo(rescheduleRef.value, alertContainer.value.$el, 0, 300)
        }, 500)
      }

      if ('data' in error.response && 'data' in error.response.data && 'timeSlotUnavailable' in error.response.data.data && error.response.data.data.timeSlotUnavailable === true) {
        alertVisibility.value = true
        alertMessage.value = props.labels.time_slot_unavailable
        setTimeout(function () {
          useScrollTo(rescheduleRef.value, alertContainer.value.$el, 0, 300)
        }, 500)
      }

      if ('data' in error.response && 'data' in error.response.data && 'rescheduleBookingUnavailable' in error.response.data.data && error.response.data.data.rescheduleBookingUnavailable === true) {
        alertVisibility.value = true
        alertMessage.value = props.labels.booking_reschedule_exception
        setTimeout(function () {
          useScrollTo(rescheduleRef.value, alertContainer.value.$el, 0, 300)
        }, 500)
      }
    }
    emits('error')
  })
}

function packageBookingApp () {
  calendarRef.value.calendarSlotsLoading = true

  let bookingDateTimeData = getDateTimeData(
    amSettings.general.showClientTimeZone &&
    store.getters['cabinet/getTimeZone'] === useCurrentTimeZone()
  )

  let data = JSON.parse(JSON.stringify(props.appointment))

  data.bookingStart = bookingDateTimeData.bookingStart

  data.bookings[0].utcOffset = bookingDateTimeData.utcOffset

  data.bookings[0].timeZone = bookingDateTimeData.timeZone

  data.providerId = parseInt(appointmentProviderId.value)

  data.locationId = parseInt(appointmentLocationId.value)

  httpClient.post(
    '/bookings',
    data,
    useAuthorizationHeaderObject(store)
  ).then((response) => {
    calendarRef.value.calendarSlotsLoading = false
    useCreateBookingSuccess(store, response)
    emits('success', {message: amLabels.value.booking_added_success})
  }).catch((error) => {
    calendarRef.value.calendarSlotsLoading = false
    if (error.response) {
      if ('customerAlreadyBooked' in error.response.data.data && error.response.data.data.customerAlreadyBooked === true) {
        alertVisibility.value = true
        alertMessage.value = props.labels.customer_already_booked_app
        setTimeout(function () {
          useScrollTo(rescheduleRef.value, alertContainer.value.$el, 0, 300)
        }, 500)
      }

      if ('timeSlotUnavailable' in error.response.data.data && error.response.data.data.timeSlotUnavailable === true) {
        alertVisibility.value = true
        alertMessage.value = props.labels.time_slot_unavailable
        setTimeout(function () {
          useScrollTo(rescheduleRef.value, alertContainer.value.$el, 0, 300)
        }, 500)
      }

      if ('packageBookingUnavailable' in error.response.data.data && error.response.data.data.packageBookingUnavailable === true) {
        alertVisibility.value = true
        alertMessage.value = props.labels.package_booking_unavailable
        setTimeout(function () {
          useScrollTo(rescheduleRef.value, alertContainer.value.$el, 0, 300)
        }, 500)
      }

      if ('customerBlocked' in error.response.data.data && error.response.data.data.customerBlocked === true) {
        alertVisibility.value = true
        alertMessage.value = props.labels.customer_blocked
        setTimeout(function () {
          useScrollTo(rescheduleRef.value, alertContainer.value.$el, 0, 300)
        }, 500)
      }
    }

    emits('error', error)
  })
}

function addPackageBooking () {
  if (!filterFormVisibility.value) {
    packageBookingApp()
  } else {
    packageFormRef.value.validate((valid) => {
      if (valid) {
        packageBookingApp()
      } else {
        setTimeout(() => {
          useScrollTo(rescheduleRef.value, packageFormRef.value.$el, 0, 300)
        }, 200)
      }
    })
  }
}

let appointmentProviderId = ref(null)
let appointmentLocationId = ref(null)
let dateSlots = ref(null)

function useSlotsCallback(
  store,
  slots
) {
  dateSlots.value = slots

  slotsAreLoading.value = false

  if (!props.appointment.bookingStart) {
    let dates = useSortedDateStrings(Object.keys(slots))

    return {
      calendarStartDate: dates.length ? dates[0] : null,
      calendarEventSlots: [],
      calendarEventDate: null,
      calendarEventSlot: null,
    }
  }

  let bookingStartParts = props.appointment.bookingStart.split(' ')

  return {
    calendarStartDate: bookingStartParts[0],
    calendarEventSlots: bookingStartParts[0] in slots ? Object.keys(slots[bookingStartParts[0]]) : [],
    calendarEventDate: bookingStartParts[0],
    calendarEventSlot: bookingStartParts[1].slice(0, 5),
  }
}

provide('useSlotsCallback', useSlotsCallback)

provide('useRange', () => {
  if (!props.appointment.bookingStart) {
    return {
      start: null,
      end: null
    }
  }

  let bookingStart = props.appointment.bookingStart.split(' ')[0]

  return {
    start: moment(bookingStart, 'YYYY-MM-DD').startOf('month').subtract(6, 'days').format('YYYY-MM-DD'),
    end: moment(bookingStart, 'YYYY-MM-DD').endOf('month').add(12, 'days').format('YYYY-MM-DD')
  }
})

provide('useSelectedDuration', () => {})

function setBookingData () {
  let slots = Object.keys(dateSlots.value[appointmentDate.value])

  if (slots.length) {
    appointmentProviderId.value =
        appointmentTime.value && dateSlots.value[appointmentDate.value][appointmentTime.value] ?
            dateSlots.value[appointmentDate.value][appointmentTime.value][0].e
            : dateSlots.value[appointmentDate.value][slots[0]][0].e

    appointmentLocationId.value =
        appointmentTime.value && dateSlots.value[appointmentDate.value][appointmentTime.value] ?
            dateSlots.value[appointmentDate.value][appointmentTime.value][0].l
            : dateSlots.value[appointmentDate.value][slots[0]][0].l
  }
}

provide('useBusySlots', () => [])

provide('useSelectedDate', (store, date) => {
  appointmentDate.value = date

  if (props.appointment && props.appointment.bookingStart) {
    appointmentTime.value = props.appointment.bookingStart.split(' ')[1].substring(0, 5)
  }

  setBookingData()

  return Object.keys(dateSlots.value[date])
})

provide('useSelectedTime', (store, time) => {
  appointmentTime.value = time.substring(0, 5)

  if (appointmentDate.value) {
    setBookingData()
  } else if (props.appointment) {
    appointmentDate.value = props.appointment.bookingStart.split(' ')[0]

    setBookingData()
  }
})

provide('useDeselectedDate', () => {
  appointmentDate.value = ''
  appointmentTime.value = ''

  appointmentProviderId.value = null

  appointmentLocationId.value = null
})

/*************
 * Customize *
 *************/
// * Fonts
let amFonts = inject('amFonts')

// * Colors block
let amColors = inject('amColors')

let cssDialogVars = computed(() => {
  return {
    '--am-c-csd-text': amColors.value.colorMainText,
    '--am-c-csd-bgr': amColors.value.colorMainBgr,
    '--am-c-csd-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
    '--am-c-scroll-op30': useColorTransparency(amColors.value.colorPrimary, 0.3),
    '--am-c-scroll-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),
    '--am-font-family': amFonts.value.fontFamily,
  }
})
</script>

<script>
export default {
  name: 'AppointmentBooking'
}
</script>

<style lang="scss">
@mixin am-cabinet-slide-dialog {
  // csd - cabinet slide dialog
  .am-csd {
    border-radius: 8px;
    padding: 0;

    $mainClass: '.am-csd';

    * {
      font-family: var(--am-font-family);
      box-sizing: border-box;
    }

    &__cancel {
      &.am-slide-popup__block-inner.am-position-center {
        max-width: 480px;
        width: 100%;
        padding: 0;
        border-radius: 8px;
        background-color: var(--am-c-csd-bgr);
      }

      #{$mainClass} {
        &__content {
          p {
            font-size: 15px;
            font-weight: 400;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: var(--am-c-csd-text);
          }
        }
      }
    }

    &__reschedule {
      &.am-slide-popup__block-inner.am-position-center {
        max-width: 520px;
        width: 100%;
        padding: 0;
        border-radius: 8px;
        background-color: var(--am-c-csd-bgr);
      }

      &#{$mainClass} {
        display: flex;
        flex-direction: column;
      }

      #{$mainClass} {
        &__inner {
          display: flex;
          flex-direction: column;
          height: 100%;
          overflow: hidden;
        }

        &__header {
          border-bottom: 1px solid var(--am-c-csd-text-op10);
        }

        &__content {
          padding: 24px;
          overflow-x: hidden;

          table {
            margin: 0;
            border: none;
          }

          // Main Scroll styles
          &::-webkit-scrollbar {
            width: 6px;
          }

          &::-webkit-scrollbar-thumb {
            border-radius: 6px;
            background: var(--am-c-scroll-op30);
          }

          &::-webkit-scrollbar-track {
            border-radius: 6px;
            background: var(--am-c-scroll-op10);
          }
        }

        &__footer {
          border-top: 1px solid var(--am-c-csd-text-op10);
        }
      }
    }

    &__header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 20px 24px 24px;

      &-text {
        font-size: 18px;
        font-weight: 500;
        color: var(--am-c-csd-text);
      }

      &-btn {
        color: var(--am-c-csd-text);
        cursor: pointer;
      }
    }

    &__content {
      padding: 0  24px;
    }

    &__alert {
      margin: 0 0 24px;

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

          .am-icon-clearable {
            font-size: 28px;
            line-height: 1;
            color: var(--am-c-alerte-bgr);
          }
        }
      }
    }

    &__filter {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 0 16px;

      &.am-rw-480 {
        flex-wrap: wrap;
      }

      &-wrapper {
        .el-form {
          &-item {
            display: block;
            font-family: var(--am-font-family);
            font-size: var(--am-fs-label);
            margin-bottom: 24px;

            &__label {
              flex: 0 0 auto;
              text-align: left;
              font-size: var(--am-fs-label);
              line-height: 1.3;
              color: var(--am-c-main-text);
              box-sizing: border-box;
              margin: 0;

              &:before {
                color: var(--am-c-error);
              }
            }

            &__content {
              display: flex;
              flex-wrap: wrap;
              align-items: center;
              flex: 1;
              position: relative;
              font-size: var(--am-fs-inp);
              min-width: 0;
            }

            &__error {
              font-size: 12px;
              color: var(--am-c-error);
              padding-top: 4px;
            }
          }
        }
      }

      .el-form {
        &-item{
          width: calc(50% - 12px);

          &.am-csd__filter-full {
            width: 100%;
          }

          &.am-rw-480 {
            width: 100%;
          }

          &__error {
            line-height: 1;
          }

          .el-input {
            &__inner {
              height: 40px !important;
            }
          }
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-slide-dialog;
}
</style>
