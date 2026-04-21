<template>
  <div ref="pageContainer" class="am-cap">
    <div class="am-cappa" :style="cssVars">
      <div class="am-cappa__back">
        <AmButton
          custom-class="am-cappa__back-btn"
          :icon="arrowLeft"
          :type="
            amCustomize[pageRenderKey][stepName].options.backBtn.buttonType
          "
          :size="'micro'"
          :category="'secondary'"
          icon-only
        ></AmButton>
        <span>
          {{ labelsDisplay('back_btn') }}
        </span>
      </div>
      <!-- Cabinet Package card heading -->
      <div class="am-cappa__heading" :class="responsiveClass">
        <div class="am-cappa__heading-left">
          <div
            class="am-cappa__img"
            :style="{
              backgroundImage: `url(${usePictureLoad(
                baseUrls,
                packObj,
                false
              )})`,
            }"
          >
            <span :style="{ backgroundColor: packObj.color }"></span>
          </div>
          <div class="am-cappa__info">
            <div class="am-cappa__name">
              {{ packObj.name }}
            </div>
            <div class="am-cappa__date">
              {{ labelsDisplay('package_book_expire') }}
              {{
                getFrontedFormattedDate(
                  moment().add(5, 'days').format('YYYY-MM-DD')
                )
              }}
            </div>
            <div class="am-cappa__capacity">6/15 {{ bookedNumberText(6) }}</div>
          </div>
        </div>
        <div class="am-cappa__heading-right" :class="responsiveClass">
          <AmButton
            :category="'danger'"
            :size="'small'"
            :type="
              amCustomize[pageRenderKey][stepName].options.cancelBtn.buttonType
            "
            :class="['am-cappa__actions', responsiveClass]"
          >
            {{ labelsDisplay('package_cancel') }}
          </AmButton>
          <PaymentButton
            btn-size="small"
            :type="
              amCustomize[pageRenderKey][stepName].options.payBtn.buttonType
            "
            :class="`am-cappa__actions ${responsiveClass}`"
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
          v-for="serviceId in Object.keys(packApps)"
          :key="serviceId"
          :name="serviceId"
        >
          <template #label>
            <div class="am-cappa__service-heading">
              {{ packApps[serviceId].purchaseData.name }}
            </div>
          </template>
          <template v-if="selectedServiceId === serviceId">
            <div class="am-cappa__service-top" :class="responsiveClass">
              <div class="am-cappa__service-right">
                <div
                  class="am-cappa__service-img"
                  :style="{
                    backgroundImage: `url(${usePictureLoad(
                      baseUrls,
                      packApps[selectedServiceId].purchaseData,
                      false
                    )})`,
                  }"
                >
                  <span
                    :style="{
                      backgroundColor:
                        packApps[selectedServiceId].purchaseData.color,
                    }"
                  ></span>
                </div>
                <div class="am-cappa__service-info">
                  <div class="am-cappa__service-name">
                    {{ packApps[selectedServiceId].purchaseData.name }}
                  </div>
                  <div
                    v-if="
                      getPurchasedCount(packApps, selectedServiceId, 'count')
                    "
                    class="am-cappa__service-capacity"
                  >
                    {{
                      getPurchasedCount(packApps, selectedServiceId, 'count')
                    }}/{{
                      getPurchasedCount(packApps, selectedServiceId, 'total')
                    }}
                    {{
                      bookedNumberText(
                        getPurchasedCount(packApps, selectedServiceId, 'count')
                      )
                    }}
                  </div>
                </div>
              </div>

              <AmButton
                :class="['am-cappa__service-book', responsiveClass]"
                size="small"
                :type="
                  amCustomize[pageRenderKey][stepName].options.bookBtn
                    .buttonType
                "
                :disabled="
                  getPurchasedCount(packApps, selectedServiceId, 'count') === 0
                "
              >
                {{ labelsDisplay('book_now') }}
              </AmButton>
            </div>

            <AppointmentsList
              :grouped-appointments="packApps[selectedServiceId].appointments"
              :responsive-class="responsiveClass"
              :page-width="pageWidth"
              :is-package-booking="true"
            ></AppointmentsList>
          </template>
        </el-tab-pane>
      </el-tabs>

      <AppointmentBooking v-if="bookVisibility" :visibility="bookVisibility">
      </AppointmentBooking>

      <CancelPopup
        v-if="cancelVisibility"
        :visibility="cancelVisibility"
        title-key="cancel_package"
        content-key="confirm_cancel_package"
      >
      </CancelPopup>
    </div>
  </div>
</template>

<script setup>
// * libraries
import moment from 'moment/moment'

// * Import from Vue
import {
  ref,
  computed,
  inject,
  onMounted,
  defineComponent,
  watch,
  nextTick,
} from 'vue'

// * Composables
import { useResponsiveClass } from '../../../../../../../assets/js/common/responsive'
import { getFrontedFormattedDate } from '../../../../../../../assets/js/common/date'
import { usePictureLoad } from '../../../../../../../assets/js/common/image'
import { useColorTransparency } from '../../../../../../../assets/js/common/colorManipulation'
import { useReactiveCustomize } from '../../../../../../../assets/js/admin/useReactiveCustomize.js'

// * _components
import AmButton from '../../../../../../_components/button/AmButton.vue'
import IconComponent from '../../../../../../_components/icons/IconComponent.vue'

// * Dedicated components
import CancelPopup from '../parts/CancelPopup.vue'
import AppointmentsList from '../Appointments/parts/AppointmentsList.vue'
import PaymentButton from '../parts/PaymentButton.vue'
import AppointmentBooking from '../parts/AppointmentBooking.vue'

// * Base Urls
const baseUrls = inject('baseUrls')

// * Page Content width
let pageContainer = ref(null)
let pageWidth = ref(0)

// * Sidebar collapsed var
let sidebarCollapsed = inject('sidebarCollapsed')

// * window resize listener
window.addEventListener('resize', resize)
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

function collapseTriggered() {
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

let packObj = {
  color: '#1A84EE',
  name: 'Package Name',
  count: 6,
  total: 15,
}

let arrService = [
  {
    id: 1,
    name: 'Entrepreneurial and private clients',
    color: '#774DFB',
    count: 2,
    total: 5,
    pictureThumbPath: '',
    employeeId: 1,
  },
  {
    id: 2,
    name: 'Family business services',
    color: '#774DFB',
    count: 2,
    total: 5,
    pictureThumbPath: '',
    employeeId: 1,
  },
  {
    id: 3,
    name: 'Sustainability and climate change',
    color: '#774DFB',
    count: 2,
    total: 5,
    pictureThumbPath: '',
    employeeId: 1,
  },
]

let arrApp = [
  {
    when: 0,
    status: 'approved',
  },
  {
    when: 2,
    status: 'pending',
  },
  {
    when: 2,
    status: 'canceled',
  },
  {
    when: 5,
    status: 'rejected',
  },
]

let packApps = ref({})

arrService.forEach((serv) => {
  packApps.value[serv.id] = {
    purchaseData: {
      color: serv.color,
      count: serv.count,
      employeeId: 1,
      locationId: 1,
      name: serv.name,
      packageCustomerServiceId: serv.id,
      pictureThumbPath: '',
      total: serv.total,
    },
  }
  let dateGroupedAppointments = {}
  arrApp.forEach((item, index) => {
    if (
      Object.keys(dateGroupedAppointments).indexOf(
        moment().add(item.when, 'days').format('YYYY-MM-DD')
      ) < 0
    ) {
      dateGroupedAppointments[
        moment().add(item.when, 'days').format('YYYY-MM-DD')
      ] = {}
      dateGroupedAppointments[
        moment().add(item.when, 'days').format('YYYY-MM-DD')
      ].date = moment().add(item.when, 'days').format('YYYY-MM-DD')
      dateGroupedAppointments[
        moment().add(item.when, 'days').format('YYYY-MM-DD')
      ].appointments = []
    }

    let app = {
      id: index,
      bookingEnd: `${moment()
        .add(item.when, 'days')
        .format('YYYY-MM-DD')} 08:10:00`,
      bookingStart: `${moment()
        .add(item.when, 'days')
        .format('YYYY-MM-DD')} 08:00:00`,
      bookings: [
        {
          actionsCompleted: null,
          aggregatedPrice: true,
          appointmentId: 1,
          coupon: null,
          couponId: null,
          created: '2023-10-02 13:46:48',
          customFields: [
            { label: 'Custom field 1', value: '' },
            { label: 'Custom field 2', value: '' },
            { label: 'Custom field 3', value: '' },
          ],
          customer: {
            birthday: null,
            countryPhoneIso: null,
            email: 'testjanedoe@test.com',
            externalId: null,
            firstName: 'Jane',
            gender: null,
            id: 1,
            lastName: 'Doe',
            note: null,
            phone: '',
            pictureFullPath: null,
            pictureThumbPath: null,
            status: 'visible',
            translations: null,
            type: 'customer',
            zoomUserId: null,
          },
          customerId: 1,
          duration: null,
          extras: [
            {
              aggregatedPrice: false,
              customerBookingId: 2443,
              description: '',
              duration: 1800,
              extraId: 1,
              id: 1,
              maxQuantity: 5,
              name: 'Extra 1',
              position: 1,
              price: 10,
              quantity: 2,
              serviceId: null,
              translations: null,
            },
            {
              aggregatedPrice: false,
              customerBookingId: 2443,
              description: '',
              duration: 1800,
              extraId: 2,
              id: 2,
              maxQuantity: 5,
              name: 'Extra 2',
              position: 2,
              price: 20,
              quantity: 3,
              serviceId: null,
              translations: null,
            },
            {
              aggregatedPrice: false,
              customerBookingId: 2443,
              description: '',
              duration: 1800,
              extraId: 3,
              id: 3,
              maxQuantity: 5,
              name: 'Extra 3',
              position: 3,
              price: 15,
              quantity: 1,
              serviceId: null,
              translations: null,
            },
          ],
          id: 1,
          info: '{"firstName":"Jane","lastName":"Doe","phone":null,"locale":"en_US","timeZone":"Europe\\/Belgrade","urlParams":null}',
          isChangedStatus: null,
          isLastBooking: null,
          isUpdated: null,
          packageCustomerService: {
            bookingsCount: null,
            id: 243,
            locationId: null,
            packageCustomer: {
              bookingsCount: null,
              coupon: null,
              couponId: null,
              customerId: null,
              end: null,
              id: null,
              packageId: null,
              payments: [],
              price: null,
              purchased: null,
              start: null,
              status: null,
            },
            providerId: null,
            serviceId: null,
          },
          payments: [],
          persons: 1,
          price: 0,
          status: item.status,
          ticketsData: [],
          token: null,
          utcOffset: null,
        },
      ],
      cancelable: true,
      googleCalendarEventId: null,
      googleMeetUrl: '#',
      internalNotes: '',
      isFull: null,
      isGroup: false,
      isRescheduled: null,
      lessonSpace: '#',
      location: 'Location 1',
      locationId: 1,
      notifyParticipants: 1,
      outlookCalendarEventId: null,
      microsoftTeamsUrl: '#',
      appleCalendarEventId: null,
      parentId: null,
      past: false,
      provider: {
        firstName: 'John',
        lastName: 'Doe',
      },
      providerId: 1,
      reschedulable: true,
      resources: [],
      service: {
        name: serv.name,
      },
      serviceId: index,
      status: item.status,
      type: 'appointment',
      zoomMeeting: {
        joinUrl: '#',
      },
    }

    dateGroupedAppointments[
      moment().add(item.when, 'days').format('YYYY-MM-DD')
    ].appointments.push(app)
  })

  packApps.value[serv.id].appointments = dateGroupedAppointments
})

let arrowLeft = defineComponent({
  components: { IconComponent },
  template: `<IconComponent icon="arrow-left"></IconComponent>`,
})

let selectedServiceId = ref(null)

onMounted(() => {
  selectedServiceId.value = Object.keys(packApps.value)[0]
})

function selectService(tab) {
  selectedServiceId.value = tab.paneName
}

function getPurchasedCount(purchaseServiceData, id, type) {
  let count = 0

  Object.keys(purchaseServiceData).forEach((serviceId) => {
    if (id === null || parseInt(id) === parseInt(serviceId)) {
      count += purchaseServiceData[serviceId].purchaseData[type]
    }
  })

  return count
}

/**
 * * Customize
 * */
// * Customize
const { amCustomize } = useReactiveCustomize()
let pageRenderKey = inject('pageRenderKey')
let stepName = inject('stepName')
let subStepName = inject('subStepName')

// * Labels
let langKey = inject('langKey')
let amLabels = inject('labels')

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value][stepName.value]
      .translations &&
      amCustomize.value[pageRenderKey.value][stepName.value].translations[
        label
      ] &&
      amCustomize.value[pageRenderKey.value][stepName.value].translations[
        label
      ][langKey.value]
      ? amCustomize.value[pageRenderKey.value][stepName.value].translations[
          label
        ][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

function bookedNumberText(numb) {
  return numb === 1
    ? labelsDisplay('appointment_booked')
    : labelsDisplay('appointments_booked')
}

// * Book appointment
let bookVisibility = computed(() => {
  return subStepName.value === 'bookAppointment'
})

// * Cancel appointment
let cancelVisibility = computed(() => {
  return subStepName.value === 'cancelPackage'
})

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-cappa-text': amColors.value.colorMainText,
    '--am-c-cappa-text-op80': useColorTransparency(
      amColors.value.colorMainText,
      0.8
    ),
    '--am-c-cappa-text-op70': useColorTransparency(
      amColors.value.colorMainText,
      0.7
    ),
    '--am-c-cappa-text-op20': useColorTransparency(
      amColors.value.colorMainText,
      0.2
    ),
    '--am-c-cappa-primary': amColors.value.colorPrimary,
  }
})
</script>

<script>
export default {
  name: 'CabinetPackageAppointmentsList',
  key: 'packageAppointmentsList',
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

      &-left,
      &-right {
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

      span {
        display: block;
        width: 22px;
        height: 22px;
        position: absolute;
        bottom: -3px;
        right: -3px;
        border-radius: 50%;
        border: 3px solid #fff;
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
      margin-left: 16px;

      &.am-rw-500 {
        margin: 0;
      }
    }

    &__service {
      .el-tabs {
        &__content {
          position: static;
          overflow: unset;
        }

        &__nav-wrap {
          &::after {
            background-color: var(--am-c-cappa-text-op20);
          }
        }

        &__active-bar {
          background-color: var(--am-c-cappa-primary);
        }

        &__item {
          color: var(--am-c-cappa-text);

          &.is-active {
            color: var(--am-c-cappa-primary);
          }

          &:focus.is-active.is-focus:not(:active) {
            box-shadow: none;
          }
        }
      }

      &-heading {
        font-size: 15px;
        font-weight: 500;
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

        span {
          display: block;
          width: 22px;
          height: 22px;
          position: absolute;
          bottom: -3px;
          right: -3px;
          border-radius: 50%;
          border: 3px solid #fff;
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

    .el-tabs {
      &__header {
        min-height: 40px;
        overflow-y: hidden;
      }

      &__nav-wrap {
        position: absolute;
      }
    }
  }
}

#amelia-app-backend-new #amelia-container {
  @include cabinet-package-appointments;
}
</style>
