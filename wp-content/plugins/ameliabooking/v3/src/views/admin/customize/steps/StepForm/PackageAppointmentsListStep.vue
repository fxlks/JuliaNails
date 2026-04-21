<template>
  <div class="am-fs__ps-overview" :class="props.globalClass" :style="cssVars">
    <div class="am-fs__ps-overview-msg">
      <IconCheck></IconCheck>
      <span>{{ labelsDisplay('package_overview_all_selected') }}</span>
    </div>
    <PackageInfo :pack="pack"></PackageInfo>
    <div class="am-fs__ps-overview-includes">
      <div>
        <p>{{ `${labelsDisplay('package_overview_info')}:` }}</p>
      </div>
      <div>
        <AmCollapse>
          <AmCollapseItem
            v-for="book in packageAppointments"
            :key="book.id"
            :side="true"
          >
            <template #heading>
              <div class="am-fs__ps-overview-service">
                <img :src="usePictureLoad(baseUrls, book.service, false)" :alt="book.service.name">
                {{ `${book.service.name} x ${book.quantity}` }}
              </div>
            </template>
            <template #default>
              <div class="am-fs__ps-overview-service-booked">
                <div>
                  <p>#</p>
                  <p>{{ labelsDisplay('package_overview_date') }}</p>
                  <p>{{ labelsDisplay('package_overview_time') }}</p>
                  <p>{{ labelsDisplay('package_overview_employee') }}</p>
                  <p>{{ labelsDisplay('package_overview_location') }}</p>
                </div>
                <div>
                  <p>1</p>
                  <p>{{ `${moment().add(5, 'days').format('HH')}:45` }}</p>
                  <p>{{ moment().add(5, 'days').format('DD. MM YYYY.') }}</p>
                  <p>John Doe</p>
                  <p>Location 1</p>
                  <p></p>
                </div>
              </div>
            </template>
          </AmCollapseItem>
        </AmCollapse>
      </div>
    </div>
  </div>
</template>

<script setup>
import moment from 'moment'
import { computed, inject, ref } from 'vue'
import IconCheck from '../../../../_components/icons/IconCheck'
import PackageInfo from '../parts/PackageInfo'
import AmCollapseItem from '../../../../_components/collapse/AmCollapseItem'
import AmCollapse from '../../../../_components/collapse/AmCollapse'
import { usePictureLoad } from '../../../../../assets/js/common/image'
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation.js'
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

let props = defineProps({
  globalClass: {
    type: String,
    default: '',
  },
})
const baseUrls = inject('baseUrls')
let packageAppointments = ref([
  {
    id: 1,
    service: {
      name: 'Service 1',
    },
    quantity: 3,
  },
  {
    id: 2,
    service: {
      name: 'Service 2',
    },
    quantity: 4,
  },
  {
    id: 3,
    service: {
      name: 'Service 3',
    },
    quantity: 5,
  },
])
let pack = ref({
  name: 'Package 1',
  description:
    "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
  bookable: [
    {
      id: 1,
      service: {
        name: 'Service 1',
        description:
          "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
      },
      quantity: 5,
      employees: [
        {
          id: 1,
          firstName: 'Jon',
          lastName: 'Doe',
        },
        {
          id: 2,
          firstName: 'Jane',
          lastName: 'Doe',
        },
      ],
    },
    {
      id: 2,
      service: {
        name: 'Service 2',
        description:
          "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
      },
      quantity: 3,
      employees: [
        {
          id: 1,
          firstName: 'Jon',
          lastName: 'Doe',
        },
        {
          id: 2,
          firstName: 'Jane',
          lastName: 'Doe',
        },
      ],
    },
    {
      id: 2,
      service: {
        name: 'Service 3',
        description:
          "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
      },
      quantity: 9,
      employees: [
        {
          id: 1,
          firstName: 'Jon',
          lastName: 'Doe',
        },
        {
          id: 2,
          firstName: 'Jane',
          lastName: 'Doe',
        },
      ],
    },
    {
      id: 3,
      service: {
        name: 'Service 4',
        description:
          "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
      },
      quantity: 9,
      employees: [
        {
          id: 1,
          firstName: 'Jon',
          lastName: 'Doe',
        },
        {
          id: 2,
          firstName: 'Jane',
          lastName: 'Doe',
        },
      ],
    },
    {
      id: 4,
      service: {
        name: 'Service 5',
        description:
          "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
      },
      quantity: 1,
      employees: [
        {
          id: 1,
          firstName: 'Jon',
          lastName: 'Doe',
        },
        {
          id: 2,
          firstName: 'Jane',
          lastName: 'Doe',
        },
      ],
    },
  ],
})

let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value].packageAppointmentsListStep
      .translations &&
      amCustomize.value[pageRenderKey.value].packageAppointmentsListStep
        .translations[label] &&
      amCustomize.value[pageRenderKey.value].packageAppointmentsListStep
        .translations[label][langKey.value]
      ? amCustomize.value[pageRenderKey.value].packageAppointmentsListStep
          .translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Colors
let amColors = inject('amColors')
let cssVars = computed(() => {
  return {
    '--am-c-pso-text-op50': useColorTransparency(
      amColors.value.colorMainText,
      0.5
    ),
    '--am-c-pso-success-op10': useColorTransparency(
      amColors.value.colorSuccess,
      0.1
    ),
    '--am-c-pso-success-op60': useColorTransparency(
      amColors.value.colorSuccess,
      0.6
    ),
    '--am-c-pso-primary-op10': useColorTransparency(
      amColors.value.colorPrimary,
      0.1
    ),
  }
})
</script>

<script>
export default {
  name: 'PackageAppointmentsListStep',
  key: 'packageAppointmentsListStep',
  sidebarData: {
    label: 'package_booking_overview',
    icon: 'calendar-check',
    stepSelectedData: [],
    finished: false,
    selected: false,
    type: 'PackageAppointmentListStep',
  },
}
</script>

<style lang="scss">
// am -- amelia
// fs -- form steps

#amelia-app-backend-new #amelia-container {
  // Amelia Form Steps
  .am-fs {
    &__ps-overview {
      --am-c-pso-text: var(--am-c-main-text);
      --am-c-pso-bgr: var(--am-c-main-bgr);
      --am-c-pso-success: var(--am-c-success);
      --am-c-pso-primary: var(--am-c-primary);

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
        /* $green-300 */
        background-color: var(--am-c-pso-success-op10);
        /* $green-600 */
        border: 1px solid var(--am-c-pso-success-op60);
        box-sizing: border-box;
        box-shadow: 0 2px 3px var(--am-c-pso-success-op10);
        border-radius: 6px;
        width: 100%;
        padding: 11px;
        margin-bottom: 8px;

        .am-icon-check {
          /* $green-900 */
          background-color: var(--am-c-pso-success);
          border-radius: 50%;
          color: var(--am-c-pso-bgr);
          font-size: 18px;
          margin-right: 11px;
        }

        span {
          font-weight: 500;
          font-size: 14px;
          line-height: 1.714286;
          /* $shade-900 */
          color: var(--am-c-pso-text);
          vertical-align: middle;
        }
      }

      &-includes {
        margin-top: 16px;

        .am-collapse-item {
          &__heading {
            padding: 8px;
          }

          &__open {
            /* $blue-900 */
            --am-combo-collapse-border: 1px solid var(--am-c-pso-primary);
            box-shadow: 0 1px 1px var(--am-c-pso-primary-op10);

            transition: border ease-in-out 0.5s;
          }
        }

        & > div:first-child {
          display: flex;
          justify-content: space-between;
          margin-bottom: 8px;

          & > p:first-child {
            font-weight: 500;
            font-size: 14px;
            line-height: 1.42857;
            /* $shade-900 */
            color: var(--am-c-pso-text);
          }
        }
      }

      &-service {
        display: flex;
        align-items: center;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        /* $shade-900 */
        color: var(--am-c-pso-text);

        img {
          width: 54px;
          height: 54px;
          border-radius: 4px;
          margin-right: 12px;
        }

        &-booked {
          display: table;
          width: 100%;
          padding: 0;

          & > div:first-child {
            display: table-header-group;

            & > p {
              display: table-cell;
              width: 23%;
              font-size: 13px;
              font-weight: 400;
              line-height: 1.384615;
              /* $shade-400 */
              color: var(--am-c-pso-text-op50);
              padding: 4px;

              &:first-child {
                width: 8%;
                padding-left: 16px;
              }

              &:last-child {
                padding-right: 16px;
              }
            }
          }

          & > div:not(:first-child) {
            /* $shade-900 */
            color: var(--am-c-pso-text);
            display: table-row;

            &:nth-child(even) {
              /* $blue-300 */
              background-color: var(--am-c-pso-primary-op10);
            }

            &:nth-child(odd) {
              background-color: var(--am-c-pso-bgr);
            }

            & > p {
              display: table-cell;
              width: 23%;
              vertical-align: middle;
              padding: 4px;
              &:first-child {
                width: 8%;
                padding-left: 16px;
              }

              &:last-child {
                padding-right: 16px;
              }
            }
          }
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
</style>
