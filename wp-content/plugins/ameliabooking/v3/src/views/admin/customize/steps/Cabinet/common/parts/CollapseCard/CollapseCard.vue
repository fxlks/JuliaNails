<template>
  <div class="am-cc" :style="cssVars">
    <AmCollapse>
      <AmCollapseItem
        heading-class="am-cc__heading-wrapper"
        :side="props.parentWidth > 500"
      >
        <template #heading>
          <div class="am-cc__heading" :class="props.responsiveClass">
            <div class="am-cc__heading-info" :class="props.responsiveClass">
              <div
                class="am-cc__heading-info__part"
                :class="props.responsiveClass"
              >
                <div class="am-cc__time">
                  {{ props.start }}
                </div>
                <div v-if="props.parentWidth > 650" class="am-cc__name">
                  {{ props.name }}
                </div>
                <div
                  v-if="props.parentWidth <= 650"
                  class="am-cc__status"
                  :class="`am-cc__status-${props.booking.status}`"
                >
                  {{ labelsDisplay(props.booking.status) }}
                </div>
              </div>
              <div
                class="am-cc__heading-info__part"
                :class="props.responsiveClass"
              >
                <div v-if="props.parentWidth <= 650" class="am-cc__name">
                  {{ props.name }}
                </div>
                <div
                  v-if="props.parentWidth > 650"
                  class="am-cc__status"
                  :class="`am-cc__status-${props.booking.status}`"
                >
                  {{ labelsDisplay(props.booking.status) }}
                </div>
              </div>
            </div>
            <div
              v-if="(props.booking.status === 'approved' || props.booking.status === 'pending') &&
              (!props.isPackageBooking || (props.reservation.cancelable || props.reservation.reschedulable) || !licence.isStarter)"
              class="am-cc__heading-actions"
              :class="props.responsiveClass"
            >
              <PaymentButton
                v-if="
                  !props.isPackageBooking &&
                  !licence.isStarter &&
                  pageRenderKey !== 'cape'
                "
              />
              <el-popover
                v-if="props.reservation.cancelable || props.reservation.reschedulable || !licence.isStarter"
                ref="editRef"
                :visible="editPopVisible"
                :persistent="false"
                :show-arrow="false"
                :width="'auto'"
                :popper-class="'am-cc__popper'"
                :popper-style="cssVars"
                trigger="click"
              >
                <template #reference>
                  <span
                    v-if="props.booking"
                    class="am-cc__edit-btn am-icon-dots-vertical"
                    @click="editItem"
                  ></span>
                </template>
                <div
                  v-if="pageRenderKey === 'capc'"
                  v-click-outside="closeEditItemPopup"
                  class="am-cc__edit"
                >
                  <!-- Reschedule -->
                  <div
                    v-if="
                      props.reservation.type === 'appointment' &&
                      props.reservation.reschedulable
                    "
                    class="am-cc__edit-item"
                    @click="rescheduleItem"
                  >
                    <span class="am-icon-date-time"></span>
                    <span class="am-cc__edit-text">
                      {{ labelsDisplay('reschedule') }}
                    </span>
                  </div>
                  <!-- /Reschedule -->

                  <!-- Invoice -->
                  <div
                      v-if="features.invoices"
                      class="am-cc__edit-item"
                  >
                    <span class="am-icon-eye"></span>
                    <span class="am-cc__edit-text">
                      {{ labelsDisplay('preview_invoice') }}
                    </span>
                  </div>

                  <div
                      v-if="features.invoices"
                      class="am-cc__edit-item"
                  >
                    <span class="am-icon-download"></span>
                    <span class="am-cc__edit-text">
                      {{ labelsDisplay('download_invoice') }}
                    </span>
                  </div>
                  <!-- /Invoice -->

                  <!-- Cancel item -->
                  <div
                    v-if="props.reservation.cancelable"
                    class="am-cc__edit-item am-delete"
                    @click="cancelItem"
                  >
                    <span class="am-icon-clearable"></span>
                    <span class="am-cc__edit-text">
                      {{ labelsDisplay('cancel') }}
                    </span>
                  </div>
                  <!-- /Cancel item -->
                </div>

                <template v-if="pageRenderKey === 'cape'">
                  <div v-click-outside="closeEditItemPopup" class="am-cc__edit">
                    <!-- Edit Appointment -->
                    <div class="am-cc__edit-item am-edit">
                      <span class="am-icon-edit" />
                      <span class="am-cc__edit-text">
                        {{ amLabels.edit }}
                      </span>
                    </div>
                    <!-- /Edit Appointment -->
                  </div>

                  <template v-if="props.reservation.type === 'event'">
                    <div
                      v-click-outside="closeEditItemPopup"
                      class="am-cc__edit"
                    >
                      <!-- Edit Attendee -->
                      <div class="am-cc__edit-item am-edit">
                        <span class="am-icon-users-plus"></span>
                        <span class="am-cc__edit-text">
                          {{ amLabels.event_add_attendee }}
                        </span>
                      </div>
                      <!-- /Edit Attendee -->
                    </div>
                    <div
                      v-click-outside="closeEditItemPopup"
                      class="am-cc__edit"
                    >
                      <!-- List Event Attendees -->
                      <div class="am-cc__edit-item am-edit">
                        <span class="am-icon-user"></span>
                        <span class="am-cc__edit-text">
                          {{ amLabels.attendees }}
                        </span>
                      </div>
                      <!-- /List Event Attendees -->
                    </div>
                  </template>
                </template>
              </el-popover>
            </div>
          </div>
        </template>
        <template #default>
          <div class="am-cc__content" :class="props.responsiveClass">
            <div class="am-cc__content-inner">
              <!-- Employee -->
              <template
                v-if="
                  pageRenderKey === 'capc' &&
                  props.employee &&
                  props.customizedOptions.employee.visibility
                "
              >
                <CollapseCardPopover
                  v-if="
                    Array.isArray(props.employee)
                      ? props.employee.length
                      : Object.keys(props.employee).length
                  "
                  :header-text="
                    Array.isArray(props.employee)
                      ? labelsDisplay('view_employees')
                      : labelsDisplay('provider_profile')
                  "
                  type="employee"
                  :content-data="props.employee"
                >
                  <template #default>
                    <div class="am-cc__data">
                      <span class="am-icon-user"></span>
                      <span class="am-cc__data-text">
                        {{ employeesString(props.employee) }}
                      </span>
                    </div>
                  </template>
                </CollapseCardPopover>
              </template>
              <!-- /Employee -->

              <!-- Customers -->
              <template v-if="pageRenderKey === 'cape' && props.reservation.type === 'appointment' && props.customizedOptions.customer.visibility">
                <CollapseCardPopover
                    v-if="props.customers.length"
                    :header-text="props.customers.length > 1 ? labelsDisplay('customers') : labelsDisplay('customer') "
                    type="customers"
                    :content-data="props.customers"
                >
                  <template #default>
                    <div class="am-cc__data">
                      <span class="am-icon-user"></span>
                      <span class="am-cc__data-text">
                      {{ props.customers.length > 1 ? labelsDisplay('customers') : labelsDisplay('customer') }}
                    </span>
                    </div>
                  </template>
                </CollapseCardPopover>
              </template>
              <!-- /Customers -->

              <!-- Price -->
              <div
                v-if="props.customizedOptions.price.visibility"
                class="am-cc__data"
              >
                <span class="am-icon-payments"></span>
                <span class="am-cc__data-text">{{
                  useFormattedPrice(props.price)
                }}</span>
              </div>
              <!-- /Price -->

              <!-- Slot -->
              <div v-if="props.duration !== null" class="am-cc__data">
                <span class="am-icon-clock"></span>
                <span class="am-cc__data-text">{{
                  useSecondsToDuration(props.duration, amLabels.h, amLabels.min)
                }}</span>
              </div>
              <!-- /Slot -->

              <!-- Periods -->
              <CollapseCardPopover
                v-if="periods.length > 1"
                :header-text="labelsDisplay('event_timetable')"
                type="period"
                :content-data="props.periods"
              >
                <template #default>
                  <div class="am-cc__data">
                    <span class="am-icon-clock"></span>
                    <span class="am-cc__data-text">
                      {{ labelsDisplay('event_timetable') }}
                    </span>
                  </div>
                </template>
              </CollapseCardPopover>
              <!-- /Periods -->

              <!-- GoogleMeet Link -->
              <div
                v-if="props.googleMeetLink && features.googleCalendar"
                class="am-cc__data link"
              >
                <span class="am-icon-link"></span>
                <a class="am-cc__data-text link">
                  {{ labelsDisplay('google_meet_link') }}
                </a>
              </div>
              <!-- /GoogleMeet link -->

              <!-- Microsoft Teams Link -->
              <div
                v-if="props.microsoftTeamsLink && features.outlookCalendar"
                class="am-cc__data link"
              >
                <span class="am-icon-link"></span>
                <a class="am-cc__data-text link">
                  {{ labelsDisplay('microsoft_teams_link') }}
                </a>
              </div>
              <!-- /Microsoft Teams link -->

              <!-- Zoom Link -->
              <div
                v-if="props.zoomLink && features.zoom"
                class="am-cc__data link"
              >
                <span class="am-icon-link"></span>
                <a class="am-cc__data-text link">
                  {{ labelsDisplay('zoom_link') }}
                </a>
              </div>
              <!-- /Zoom link -->

              <!-- Lesson Space Link -->
              <div v-if="features.lessonSpace && props.lessonSpaceLink" class="am-cc__data link">
                <span class="am-icon-link"></span>
                <a class="am-cc__data-text link">
                  {{ labelsDisplay('lesson_space_link') }}
                </a>
              </div>
              <!-- /Lesson Space Link -->

              <!-- Extras -->
              <CollapseCardPopover
                v-if="props.extras.length && features.extras"
                :header-text="`${props.extras.length} ${labelsDisplay(
                  'extras'
                )}`"
                type="extras"
                :content-data="props.extras"
              >
                <template #default>
                  <div class="am-cc__data">
                    <span class="am-icon-border-plus"></span>
                    <span class="am-cc__data-text">
                      {{ `${props.extras.length} ${labelsDisplay('extras')}` }}
                    </span>
                  </div>
                </template>
              </CollapseCardPopover>
              <!-- /Extras -->

              <!-- Custom Fields -->
              <CollapseCardPopover
                v-if="props.customFields.length && features.customFields"
                :header-text="labelsDisplay('custom_fields')"
                type="customField"
                :content-data="props.customFields"
              >
                <template #default>
                  <div class="am-cc__data">
                    <span class="am-icon-file-text"></span>
                    <span class="am-cc__data-text">
                      {{ labelsDisplay('custom_fields') }}
                    </span>
                  </div>
                </template>
              </CollapseCardPopover>
              <!-- /Custom Fields -->

              <!-- Tickets -->
              <CollapseCardPopover
                v-if="props.tickets.length && features.tickets"
                :header-text="labelsDisplay('event_tickets')"
                type="ticket"
                :content-data="props.tickets"
              >
                <template #default>
                  <div class="am-cc__data">
                    <span class="am-icon-tickets"></span>
                    <span class="am-cc__data-text">
                      {{ labelsDisplay('event_tickets') }}
                    </span>
                  </div>
                </template>
              </CollapseCardPopover>
              <!-- /Tickets -->

              <!-- Location -->
              <div
                v-if="location !== null && !licence.isStarter"
                class="am-cc__data link"
              >
                <span class="am-icon-locations"></span>
                <a class="am-cc__data-text">
                  {{ props.location }}
                </a>
              </div>
              <!-- /Location -->

              <!-- QR Code -->
              <CollapseCardPopover
                v-if="props.qrCodes.length && features.eTickets"
                type="qrCode"
                :header-text="labelsDisplay('e_tickets')"
                :content-data="props.qrCodes"
              >
                <template #default>
                  <div class="am-cc__data">
                    <span class="am-icon-tickets"></span>
                    <span class="am-cc__data-text">
                      {{ labelsDisplay('e_tickets') }}
                    </span>
                  </div>
                </template>
              </CollapseCardPopover>
              <!-- /QR Code -->
            </div>
          </div>
        </template>
      </AmCollapseItem>
    </AmCollapse>
  </div>
</template>

<script setup>
// * Import from Vue
import { ref, computed, inject } from 'vue'

// * Import from Libraries
import { ClickOutside as vClickOutside } from 'element-plus'

// * Components
import AmCollapse from '../../../../../../../_components/collapse/AmCollapse.vue'
import AmCollapseItem from '../../../../../../../_components/collapse/AmCollapseItem.vue'
import CollapseCardPopover from './popover/CollapseCardPopover.vue'
import PaymentButton from '../PaymentButton.vue'

// * Composables
import { useColorTransparency } from '../../../../../../../../assets/js/common/colorManipulation'
import { useFormattedPrice } from '../../../../../../../../assets/js/common/formatting'
import { useSecondsToDuration } from '../../../../../../../../assets/js/common/date'
import { useReactiveCustomize } from '../../../../../../../../assets/js/admin/useReactiveCustomize.js'

// * Component porps
let props = defineProps({
  start: {
    type: [String, Object, Array, Function],
    required: true,
  },
  name: {
    type: String,
    default: '',
  },
  employee: {
    type: [String, Object],
    default: '',
  },
  customers: {
    type: [Array],
    default: () => []
  },
  location: {
    type: [String, Object],
    default: '',
  },
  price: {
    type: Number,
    default: 0,
  },
  duration: {
    type: Number,
    default: 0,
  },
  periods: {
    type: Array,
    default: () => {},
  },
  extras: {
    type: Array,
    default: () => {},
  },
  tickets: {
    type: Array,
    default: () => {},
  },
  customFields: {
    type: Array,
    default: () => [],
  },
  googleMeetLink: {
    type: String,
    default: '',
  },
  microsoftTeamsLink: {
    type: String,
    default: '',
  },
  zoomLink: {
    type: String,
    default: '',
  },
  lessonSpaceLink: {
    type: String,
    default: '',
  },
  reservation: {
    type: Object,
    default: () => {},
  },
  bookable: {
    type: Object,
    default: () => {},
  },
  booking: {
    type: Object,
    default: () => {},
  },
  isPackageBooking: {
    type: Boolean,
    default: false,
  },
  responsiveClass: {
    type: String,
    default: '',
  },
  parentWidth: {
    type: Number,
    default: 1200,
  },
  customizedOptions: {
    type: Object,
    required: true,
  },
  qrCodes: {
    type: Array,
    default: () => [],
  },
})

// * Plugin Licence
let licence = inject('licence')

// * Features
let features = inject('features')

// * Amelia Settings
const amSettings = inject('settings')

// * Customize
const { amCustomize } = useReactiveCustomize()

// * Labels
let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
let stepName = inject('stepName')

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

let editPopVisible = ref(false)

function editItem(e) {
  e.stopPropagation()
  editPopVisible.value = !editPopVisible.value
}

function closeEditItemPopup() {
  editPopVisible.value = false
}

function cancelItem() {
  editPopVisible.value = false
}

function rescheduleItem() {
  editPopVisible.value = false
}

function employeesString(emp) {
  if (Array.isArray(emp)) {
    return labelsDisplay('event_staff')
  }

  if (!Array.isArray(emp) && emp.rank && emp.rank === 'organizer') {
    return labelsDisplay('event_organizer')
  }

  return `${props.employee.firstName} ${props.employee.lastName}`
}

/*************
 * Customize *
 *************/
// * Fonts
let amFonts = inject('amFonts')

// * Colors block
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-cc-primary': amColors.value.colorPrimary,
    '--am-c-cc-primary-op70': useColorTransparency(
      amColors.value.colorPrimary,
      0.7
    ),
    '--am-c-cc-error': amColors.value.colorError,
    '--am-c-cc-error-op15': useColorTransparency(
      amColors.value.colorError,
      0.15
    ),
    '--am-c-cc-warning': amColors.value.colorWarning,
    '--am-c-cc-warning-op15': useColorTransparency(
      amColors.value.colorWarning,
      0.15
    ),
    '--am-c-cc-success': amColors.value.colorSuccess,
    '--am-c-cc-success-op15': useColorTransparency(
      amColors.value.colorSuccess,
      0.15
    ),
    '--am-c-cc-bgr': amColors.value.colorMainBgr,
    '--am-c-cc-text': amColors.value.colorMainText,
    '--am-c-cc-text-op03': useColorTransparency(
      amColors.value.colorMainText,
      0.03
    ),
    '--am-c-cc-text-op10': useColorTransparency(
      amColors.value.colorMainText,
      0.1
    ),
    '--am-c-cc-text-op15': useColorTransparency(
      amColors.value.colorMainText,
      0.15
    ),
    '--am-c-cc-text-op70': useColorTransparency(
      amColors.value.colorMainText,
      0.7
    ),
    '--am-c-cc-text-op90': useColorTransparency(
      amColors.value.colorMainText,
      0.9
    ),
    '--am-font-family': amFonts.value.fontFamily,

    // css properties
    '--am-rad-inp': '6px',
    '--am-fs-inp': '15px',
  }
})
</script>

<script>
export default {
  name: 'CollapseCard',
}
</script>

<style lang="scss">
@mixin collapse-card {
  // cc - collapse card
  .am-cc {
    * {
      font-family: var(--am-font-family), sans-serif;
      box-sizing: border-box;
    }

    &__heading {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;

      &.am-rw-500 {
        flex-wrap: wrap;
      }

      &-wrapper {
        padding: 12px 16px;
      }

      &-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;

        &.am-rw-650 {
          flex-wrap: wrap;
        }

        &__part {
          display: flex;
          align-items: center;

          &.am-rw-650 {
            width: 100%;
          }

          &.am-rw-500 {
            justify-content: space-between;
          }

          &.am-rw-420 {
            flex-wrap: wrap;
          }
        }
      }

      &-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 0 0 0 8px;

        &.am-rw-500 {
          width: 100%;

          .am-cc__edit-btn {
            margin-left: auto;
          }
        }

        .am-button {
          --am-padd-btn: 4px 6px 4px 12px;
        }

        .am-button-single {
          --am-padd-btn: 4px 12px 4px 12px;
        }
      }
    }

    &__time {
      display: flex;
      flex: 0 0 auto;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      font-weight: 400;
      line-height: 1.428571429;
      color: var(--am-c-cc-text);
      background-color: var(--am-c-cc-text-op10);
      border-radius: 12px;
      padding: 2px 12px;
      margin: 4px 8px 4px 0;
    }

    &__name {
      display: flex;
      font-size: 15px;
      font-weight: 500;
      line-height: 1.6;
      color: var(--am-c-cc-text);
      margin: 4px 8px;
      width: 100%;
    }

    &__employee {
      display: flex;
      font-size: 15px;
      font-weight: 400;
      line-height: 1.6;
      color: var(--am-c-cc-text);
      margin: 4px 8px;
    }

    &__status {
      display: flex;
      flex: 0 0 auto;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      font-weight: 400;
      line-height: 1.428571429;
      border-radius: 12px;
      padding: 2px 12px;
      margin: 4px 8px;

      &:before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin: 0 4px 0 0;
      }

      &-canceled {
        color: var(--am-c-cc-error);
        background-color: var(--am-c-cc-error-op15);

        &:before {
          background-color: var(--am-c-cc-error);
        }
      }

      &-rejected {
        color: var(--am-c-cc-text);
        background-color: var(--am-c-cc-text-op15);

        &:before {
          background-color: var(--am-c-cc-text);
        }
      }

      &-pending {
        color: var(--am-c-cc-warning);
        background-color: var(--am-c-cc-warning-op15);

        &:before {
          background-color: var(--am-c-cc-warning);
        }
      }

      &-approved {
        color: var(--am-c-cc-success);
        background-color: var(--am-c-cc-success-op15);

        &:before {
          background-color: var(--am-c-cc-success);
        }
      }
    }

    &__actions {
    }

    &__edit {
      &-btn {
        margin: 0;
        font-size: 24px;
        color: var(--am-c-cc-text);
      }

      &-item {
        display: flex;
        align-items: center;
        color: var(--am-c-cc-text);

        span[class^='am-icon'] {
          font-size: 24px;
        }
      }
    }

    &__content {
      display: flex;
      width: 100%;
      align-items: center;
      justify-content: center;
      padding: 0 16px 16px;

      &.am-rw-500 {
        padding: 16px 16px 0;
      }

      &-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        width: 100%;
        border-radius: 8px;
        background-color: var(--am-c-cc-text-op03);
        padding: 12px;
      }
    }

    &__data {
      display: flex;
      align-items: center;
      color: var(--am-c-cc-text);

      &.el-tooltip__trigger,
      &.link {
        color: var(--am-c-cc-primary);
        cursor: pointer;

        &:hover {
          color: var(--am-c-cc-primary-op70);
        }
      }

      &-text {
        font-size: 14px;
        font-weight: 400;
        line-height: 2;
        color: inherit;

        &.link {
          text-decoration: none;
        }
      }

      [class^='am-icon'] {
        font-size: 28px;
        color: inherit;
      }
    }

    .am-collapse-item {
      &__content {
        border-top: 0;
      }
    }
  }
}

@mixin collapse-card-popper {
  // cc - collapse card
  .am-cc {
    &__edit {
      &-item {
        display: flex;
        align-items: center;
        color: var(--am-c-cc-text);
        border-radius: 4px;
        padding: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease-in-out;

        &:hover {
          background-color: var(--am-c-cc-text-op15);
        }

        &.am-delete {
          --am-c-cc-text: var(--am-c-cc-error);

          &:hover {
            background-color: var(--am-c-cc-error-op15);
          }
        }

        span[class^='am-icon'] {
          font-size: 24px;
          color: inherit;
          margin: 0 4px 0 0;
        }
      }

      &-text {
        font-size: 14px;
        line-height: 1.7142857;
        color: inherit;
      }
    }
  }

  &.am-cc__popper {
    padding: 6px 4px;
    background-color: var(--am-c-cc-bgr);
    border-color: var(--am-c-cc-bgr);
    box-shadow: 0 2px 12px 0 var(--am-c-cc-text-op10);

    * {
      font-family: var(--am-font-family), sans-serif;
      box-sizing: border-box;
    }
  }
}

#amelia-app-backend-new #amelia-container {
  @include collapse-card;
}

.el-popper.el-popover {
  @include collapse-card-popper;
}
</style>
