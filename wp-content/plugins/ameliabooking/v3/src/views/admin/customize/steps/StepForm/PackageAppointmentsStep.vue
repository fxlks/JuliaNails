<template>
  <div class="am-fs__pas" :class="props.globalClass">
    <div class="am-fs__pas-service">
      {{ packageServiceSelection.service.name }}
    </div>
    <div class="am-fs__pas-msg">
      {{
        `${labelsDisplay('package_appointment_required')}: 1. ${labelsDisplay(
          'package_appointment_remaining'
        )}`
      }}
    </div>
    <el-form
      ref="packageFormRef"
      :rules="rules"
      :model="packageFormData"
      label-position="top"
    >
      <div class="am-fs__pas-filter">
        <el-form-item
          v-if="
            amCustomize[pageRenderKey].packageAppointmentsStep.options.employee
              .visibility
          "
          :prop="'provider'"
        >
          <template #label>
            <p>{{ `${labelsDisplay('package_appointment_employee')}:` }}</p>
          </template>
          <AmSelect
            v-model="packageFormData.provider"
            clearable
            filterable
            :placeholder="`${labelsDisplay('package_select_employee')}...`"
            :fit-input-width="true"
          >
            <AmOption
              v-for="provider in filteredEmployees"
              :key="provider.id"
              :value="provider.id"
              :label="provider.firstName + ' ' + provider.lastName"
            >
              <AmOptionTemplate2
                :identifier="provider.id"
                :label="`${provider.firstName} ${provider.lastName}`"
                :dialog-title="labelsDisplay('employee_information_package')"
                :dialog-button-text="
                  labelsDisplay('select_this_employee_package')
                "
                :description="provider.description"
              ></AmOptionTemplate2>
            </AmOption>
          </AmSelect>
        </el-form-item>

        <el-form-item
          v-if="
            amCustomize[pageRenderKey].packageAppointmentsStep.options.location
              .visibility
          "
          :prop="'location'"
        >
          <template #label>
            <p>{{ `${labelsDisplay('package_appointment_location')}:` }}</p>
          </template>
          <AmSelect
            v-model="packageFormData.location"
            clearable
            filterable
            :placeholder="`${labelsDisplay('package_select_location')}...`"
            :fit-input-width="true"
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

    <p class="am-fs__pas-app-label">
      {{ `${labelsDisplay('package_appointments')}:` }}
    </p>

    <AmCollapse>
      <AmCollapseItem
        v-for="(appointment, index) in packageServiceSelection.list"
        :ref="(el) => (packageAppointmentsList[index] = el)"
        :key="index"
        :name="index.toString()"
        class="am-fs__pas-app-items"
        :side="true"
        :delay="500"
        @collapse-open="selectCalendar(index)"
        @collapse-clicked="collapseClicked(index)"
      >
        <template #heading>
          <div class="am-fs__pas-app-heading">
            <span>
              {{
                index +
                1 +
                '. ' +
                (appointment.date && appointment.time
                  ? getFrontedFormattedDate(appointment.date) +
                    ' ' +
                    getFrontedFormattedTime(appointment.time)
                  : labelsDisplay('package_appointments_date'))
              }}
            </span>
          </div>
        </template>
        <template #icon-start>
          <span
            class="am-fs__pas-app-text"
            :class="{
              'am-fs__pas-app-text-selected':
                appointment.date && appointment.time,
            }"
          >
            {{
              appointment.date && appointment.time
                ? labelsDisplay('package_appointments_selected')
                : labelsDisplay('package_appointments_select')
            }}
          </span>
        </template>
        <template #default>
          <div class="am-fs__pas-app-content">
            <AmAdvancedSlotCalendar
              :id="0"
              :slots="calendarEvents"
              :calendar-minimum-date="moment().format('YYYY-MM-DD hh:mm')"
              :calendar-maximum-date="
                moment().add(1, 'year').format('YYYY-MM-DD hh:mm')
              "
              :time-zone="
                features.timeZones &&
                amCustomize[pageRenderKey].packageAppointmentsStep.options
                  .timeZoneVisibility.visibility
              "
              :show-people-waiting="false"
              :label-slots-selected="
                labelsDisplay('package_appointments_slots_selected')
              "
            ></AmAdvancedSlotCalendar>
          </div>
        </template>
      </AmCollapseItem>
    </AmCollapse>

    <div class="am-fs__pas-btn">
      <AmButton
        size="medium"
        category="secondary"
        type="plain"
        :prefix="'plus'"
      >
        {{ labelsDisplay('package_appointments_add_more') }}
      </AmButton>
    </div>
  </div>
</template>

<script setup>
import { ref, provide, inject, computed } from 'vue'
import AmCollapse from '../../../../_components/collapse/AmCollapse.vue'
import AmCollapseItem from '../../../../_components/collapse/AmCollapseItem.vue'
import AmSelect from '../../../../_components/select/AmSelect.vue'
import AmOption from '../../../../_components/select/AmOption.vue'
import AmOptionTemplate2 from '../../../../_components/select/parts/AmOptionTemplate2.vue'
import AmButton from '../../../../_components/button/AmButton.vue'
import AmAdvancedSlotCalendar from '../../../../_components/advanced-slot-calendar/AmAdvancedSlotCalendar.vue'
import {
  getFrontedFormattedTime,
  getFrontedFormattedDate,
} from '../../../../../assets/js/common/date'
import moment from 'moment'
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

let props = defineProps({
  globalClass: {
    type: String,
    default: '',
  },
})

// * Features
let features = inject('features')

let packageServiceSelection = ref({
  service: {
    name: 'Service 1',
  },
  list: [
    { date: moment().format('YYYY-MM-DD'), time: '13:05' },
    {
      date: moment().add(3, 'days').format('YYYY-MM-DD'),
      time: '13:05',
      isSubstitute: false,
    },
    { date: '', time: '', isSubstitute: true },
    {
      date: moment().add(9, 'days').format('YYYY-MM-DD'),
      time: '13:05',
      isSubstitute: true,
    },
  ],
})

let calendarEvents = ref([])

let today = moment().format('YYYY-MM-DD')

for (let i = 0; i <= 31; i++) {
  let block = {
    display: 'background',
    extendedProps: {
      slots: { '09:00': [7, 3] },
      slotsAvailable: 1,
      slotsTotal: 100,
    },
    start: moment(today).add(i, 'd').format('YYYY-MM-DD'),
    title: 'e',
  }
  calendarEvents.value.push(block)
}

let calendarEventDate = ref('')
let calendarServiceDuration = ref('0')
let calendarEventSlots = ref([])
let calendarEventSlot = ref('')
let calendarStartDate = ref(moment().format('YYYY-MM-DD'))
let calendarChangeSideBar = ref(true)
let calendarWaitingListSlots = ref([])

provide('calendarEventDate', calendarEventDate)
provide('calendarServiceDuration', calendarServiceDuration)
provide('calendarEvents', calendarEvents)
provide('calendarEventSlots', calendarEventSlots)
provide('calendarEventSlot', calendarEventSlot)
provide('calendarStartDate', calendarStartDate)
provide('calendarChangeSideBar', calendarChangeSideBar)
provide('calendarWaitingListSlots', calendarWaitingListSlots)

/**********
 * Filter *
 *********/

let packageFormRef = ref(null)

let packageFormData = ref({
  provider: '',
  location: '',
})

let langKey = inject('langKey')
let amLabels = inject('labels')

const { amCustomize } = useReactiveCustomize()
let pageRenderKey = inject('pageRenderKey')

// * Form validation rules
let rules = computed(() => {
  return {
    provider: [
      {
        required:
          amCustomize.value[pageRenderKey.value].packageAppointmentsStep.options
            .employee.required,
        message: labelsDisplay('please_select_employee', 'initStep'),
        trigger: ['blur', 'change'],
      },
    ],
    location: [
      {
        required:
          amCustomize.value[pageRenderKey.value].packageAppointmentsStep.options
            .location.required,
        message: labelsDisplay('please_select_location', 'initStep'),
        trigger: ['blur', 'change'],
      },
    ],
  }
})

let filteredEmployees = ref([
  {
    id: 1,
    firstName: 'Jane',
    lastName: 'Doe',
    price: 125,
    pictureThumbPath: '',
    description: "This is Jane Doe's description",
  },
  {
    id: 2,
    firstName: 'John',
    lastName: 'Doe',
    price: 125,
    pictureThumbPath: '',
    description: '',
  },
  {
    id: 3,
    firstName: 'Oktavian',
    lastName: 'Avgust',
    price: 125,
    pictureThumbPath: '',
    description: '',
  },
])

let filteredLocations = ref([
  {
    id: 1,
    name: 'Location 1',
  },
  {
    id: 2,
    name: 'Location 2',
  },
  {
    id: 3,
    name: 'Location 3',
  },
  {
    id: 4,
    name: 'Location 4',
  },
  {
    id: 5,
    name: 'Location 5',
  },
])

/*********
 * Other *
 ********/
let calendarSlotDuration = 3600
provide('calendarSlotDuration', calendarSlotDuration)

let packageAppointmentsList = ref([])

function collapseClicked(value) {
  packageAppointmentsList.value.forEach((item, index) => {
    if (index !== value && item.contentVisibility) {
      item.closingFromParent()
    }
  })
}

function selectCalendar() {}

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value].packageAppointmentsStep
      .translations &&
      amCustomize.value[pageRenderKey.value].packageAppointmentsStep
        .translations[label] &&
      amCustomize.value[pageRenderKey.value].packageAppointmentsStep
        .translations[label][langKey.value]
      ? amCustomize.value[pageRenderKey.value].packageAppointmentsStep
          .translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}
</script>

<script>
export default {
  name: 'PackageAppointmentsStep',
  key: 'packageAppointmentsStep',
  sidebarData: {
    label: 'package_appointment_step',
    icon: 'calendar-pencil',
    stepSelectedData: [],
    finished: false,
    selected: false,
    type: 'packageAppointments',
  },
}
</script>

<style lang="scss">
// am -- amelia
// fs -- form steps

#amelia-app-backend-new {
  #amelia-container {
    // Amelia Form Steps
    .am-fs {
      // pas - package appointment step
      &__pas {
        --am-c-pas-text: var(--am-c-main-text);
        --am-c-pas-success: var(--am-c-success);

        & > * {
          $count: 5;
          @for $i from 0 through $count {
            &:nth-child(#{$i + 1}) {
              animation: 600ms
                cubic-bezier(0.45, 1, 0.4, 1.2)
                #{$i *
                100}ms
                am-animation-slide-up;
              animation-fill-mode: both;
            }
          }
        }

        &-msg {
          font-weight: 400;
          font-size: 13px;
          line-height: 18px;
          /* $shade-900 */
          color: var(--am-c-pas-text);
          margin-bottom: 16px;
        }

        &-service {
          font-size: 18px;
          font-weight: 500;
          line-height: 1.6;
          color: var(--am-c-pas-text);
          margin: 0 0 8px;
        }

        &-filter {
          display: flex;
          gap: 12px;

          & > div {
            & > p,
            & > label > p {
              font-weight: 500;
              font-size: 15px;
              line-height: 20px;
              /* $shade-900 */
              color: var(--am-c-pas-text);
              margin-bottom: 4px;
              display: inline;
            }
          }
        }

        &-app {
          &-label {
            font-weight: 500;
            font-size: 14px;
            line-height: 20px;
            /* $shade-900 */
            color: var(--am-c-pas-text);
            margin-top: 16px;
            margin-bottom: 16px;
          }

          &-items {
            .am-collapse-item {
              &__content {
                display: block;
              }

              &__trigger {
                padding: 0;
              }
            }
          }

          &-heading {
            font-weight: 400;
            font-size: 14px;
            line-height: 20px;
            /* $shade-900 */
            color: var(--am-c-pas-text);
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-right: 20px;
          }

          &-content {
            .fc-col-header {
              width: 100%;
            }

            .fc-view-harness {
              min-height: 240px;
            }

            .fc-scrollgrid-sync-table {
              width: 100%;
              min-height: 190px;
            }

            .fc-daygrid-body-balanced {
              width: 100%;
            }
          }

          &-text {
            font-weight: 500;
            font-size: 14px;
            line-height: 20px;
            /* $shade-900 */
            color: var(--am-c-pas-text);
            &-selected {
              /* $green-900 */
              color: var(--am-c-pas-success);
            }
            margin-right: 10px;
            white-space: nowrap;
          }
        }

        &-btn {
          display: flex;
          justify-content: center;
          margin-top: 32px;

          .am-button__inner {
            font-weight: 500;
          }
          .am-icon-plus {
            font-size: 16px;
            margin-right: 8px;
          }
        }

        .am-collapse-item {
          $count: 20;
          @for $i from 0 through $count {
            &:nth-child(#{$i + 1}) {
              animation: 600ms
                cubic-bezier(0.45, 1, 0.4, 1.2)
                #{$i *
                100}ms
                am-animation-slide-up;
              animation-fill-mode: both;
            }
          }

          &__heading {
            transition-delay: 0.5s;

            &-side {
              transition-delay: 0s;
            }
          }
        }
      }
    }
  }
}
</style>
