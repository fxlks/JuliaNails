<template>
  <div class="am-fs__pis" :class="props.globalClass" :style="cssVars">

    <PackageInfo :pack="pack"></PackageInfo>

    <div class="am-fs__pis-includes">
      <p class="am-fs__pis-includes__heading">
        {{ `${pack.name} ${labelsDisplay('package_info_includes')}:` }}
      </p>
      <AmCollapse>
        <AmCollapseItem
          v-for="book in pack.bookable"
          :key="book.id"
          :side="true"
          :delay="500"
        >
          <template #heading>
            <div class="am-fs__pis-service">
              <img
                :src="usePictureLoad(baseUrls, book.service, false)"
                :alt="book.service.name"
              />
              {{ `${book.service.name} x ${book.quantity}` }}
            </div>
          </template>
          <template #default>
            <div class="am-fs__pis-service-info">
              <span>{{ `${labelsDisplay('package_info_employees')}:` }}</span>
              <img
                v-for="employee in book.employees"
                :key="employee.id"
                :src="usePictureLoad(baseUrls, employee, true)"
                :alt="`${employee.firstName} ${employee.lastName}`"
              />
              <p v-html="book.service.description"></p>
            </div>
          </template>
        </AmCollapseItem>
      </AmCollapse>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, inject, ref } from 'vue'
import AmCollapseItem from '../../../../_components/collapse/AmCollapseItem'
import AmCollapse from '../../../../_components/collapse/AmCollapse'
import PackageInfo from '../parts/PackageInfo'
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

// * Labels
let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

// * Label computed function
function labelsDisplay (label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value].packageInfoStep
      .translations &&
      amCustomize.value[pageRenderKey.value].packageInfoStep.translations[
        label
      ] &&
      amCustomize.value[pageRenderKey.value].packageInfoStep.translations[
        label
      ][langKey.value]
      ? amCustomize.value[pageRenderKey.value].packageInfoStep.translations[
          label
        ][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

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

let { sidebarDataCollector } = inject('sidebarStepsFunctions', {
  sidebarDataCollector: () => {},
})

onMounted(() => {
  let packageData = {
    reference: 'package',
    // position will depend on fields order
    position: 1,
    value: '',
  }
  packageData.value = pack.value ? pack.value.name : ''

  sidebarDataCollector(packageData)
})

// * Colors block
let amColors = inject('amColors')
let cssVars = computed(() => {
  return {
    '--am-c-pis-text-op80': useColorTransparency(
      amColors.value.colorMainText,
      0.8
    ),
  }
})
</script>


<script>
export default {
  name: 'PackageInfoStep',
  key: 'packageInfoStep',
  sidebarData: {
    label: 'package_info_step',
    icon: 'shipment',
    stepSelectedData: [],
    finished: false,
    selected: false,
  },
}
</script>


<style lang="scss">
#amelia-app-backend-new {
  #amelia-container {
    .am-fs__pis {
      // am - amelia
      // c  - color
      // pis - package info step
      --am-c-pis-text: var(--am-c-main-text);
      --am-c-pis-bgr: var(--am-c-main-bgr);

      & > * {
        $count: 2;
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

      &-includes {
        margin: 16px 0 0;

        .am-collapse-item {
          $count: 100;
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
            padding: 8px;
            transition-delay: 0.5s;

            &-side {
              transition-delay: 0s;
            }
          }
        }

        &__heading {
          display: flex;
          justify-content: space-between;
          font-size: 14px;
          font-weight: 500;
          line-height: 1.42857;
          /* $shade-900 */
          color: var(--am-c-pis-text);
          padding: 0;
          margin: 0 0 8px;
        }
      }

      &-service {
        display: flex;
        align-items: center;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        /* $shade-900 */
        color: var(--am-c-pis-text);

        img {
          width: 54px;
          height: 54px;
          border-radius: 4px;
          margin: 0 12px 0 0;
        }

        &-info {
          & > span:first-child {
            font-size: 13px;
            font-weight: 400;
            line-height: 1.384615;
            /* $shade-700 */
            color: var(--am-c-pis-text-op80);
            margin-right: 20px;
          }

          & > img {
            width: 36px;
            height: 36px;
            display: inline-block;
            vertical-align: middle;
            margin-left: -12px;
            border-radius: 50%;
            border: 3px solid var(--am-c-pis-bgr);
          }

          & > p {
            font-size: 15px;
            font-weight: 400;
            line-height: 1.6;
            /* $shade-700 */
            color: var(--am-c-pis-text-op80);
            margin: 16px 0 0;

            & * {
              color: var(--am-c-pis-text-op80);
            }
          }
        }

      }
    }
  }
}
</style>
