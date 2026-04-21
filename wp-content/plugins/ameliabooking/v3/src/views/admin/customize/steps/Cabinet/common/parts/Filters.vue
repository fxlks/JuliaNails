<template>
  <div
    class="am-capf"
    :style="cssVars"
  >
    <div v-if="cWidth <= 480 && customizeOptions.timeZone.visibility && stepName !== 'packagesList'" class="am-capf__zone">
      <TimeZoneSelect size="small"></TimeZoneSelect>
    </div>
    <div class="am-capf__menu">
      <TimeZoneSelect
        v-if="cWidth <= 480 && customizeOptions.timeZone.visibility && stepName === 'packagesList'"
        size="small"
      ></TimeZoneSelect>
      <AmDatePicker
        v-if="Object.values(props.paramList).find((p) => p.name === 'dates')"
        v-model="filterParams.dates"
        type="daterange"
        :editable="false"
        size="small"
        :clearable="false"
        :format="momentDateFormat()"
        :style="cssVars"
        :lang="localLanguage"
        class="am-capf__menu-datepicker"
        @calendar-change="datesChanged"
      ></AmDatePicker>
      <div>
        <AmButton
          v-if="filterBtnVisibility"
          :icon="filterIcon"
          :icon-only="cWidth <= 700"
          custom-class="am-capf__menu-btn"
          size="small"
          category="primary"
          :type="customizeOptions.filterBtn.buttonType"
          @click="filtersMenuVisibility = !filtersMenuVisibility"
        >
          <span class="am-icon-filter"></span>
          <span v-if="cWidth > 700">{{ labelsDisplay('filters') }}</span>
        </AmButton>
      </div>
    </div>

    <Transition name="am-slide-fade">
      <div v-if="filtersMenuVisibility" class="am-capf__list">
        <template
          v-for="(param, index) in Object.values(props.paramList).filter(
            (p) => p.name !== 'dates'
          )"
          :key="index"
        >
          <span
            v-if="
              entities[param.name].length > 0 &&
              amCustomize[pageRenderKey][stepName].options[
                `${param.name}Filter`
              ].visibility
            "
            class="am-capf__list-item"
            :class="[
              { 'am-selected': filterParams[param.name].length > 0 },
              `am-capf__list-item-${itemsNumber}`,
              props.responsiveClass,
            ]"
          >
            <AmSelect
              :id="`am-select-${param.name}`"
              v-model="filterParams[param.name]"
              :multiple="true"
              :placeholder="labelsDisplay(`${param.name}_dropdown`)"
              :prefix-icon="param.icon"
              :collapse-tags="true"
              size="small"
              :popper-class="'am-filter-select-popper'"
              clearable
              @change="changeFilters"
            >
              <AmOption
                v-for="entity in entities[param.name]"
                :key="entity.id"
                :value="entity.id"
                :label="
                  entity.firstName
                    ? entity.firstName + ' ' + entity.lastName
                    : entity.name
                "
              />
            </AmSelect>
          </span>
        </template>
      </div>
    </Transition>
  </div>
</template>

<script setup>
// * _components
import AmDatePicker from '../../../../../../_components/datePicker/AmDatePicker.vue'
import AmSelect from '../../../../../../_components/select/AmSelect.vue'
import AmOption from '../../../../../../_components/select/AmOption.vue'
import AmButton from '../../../../../../_components/button/AmButton.vue'
import IconComponent from '../../../../../../_components/icons/IconComponent.vue'
import TimeZoneSelect from './TimeZoneSelect.vue'

// * Import from Vue
import { ref, computed, inject } from 'vue'

// * Composables
import { useColorTransparency } from '../../../../../../../assets/js/common/colorManipulation'
import moment from 'moment/moment'
import { momentDateFormat } from '../../../../../../../assets/js/common/date'
import { useReactiveCustomize } from '../../../../../../../assets/js/admin/useReactiveCustomize.js'

// * Component properties
let props = defineProps({
  paramList: {
    type: [Object, Array],
    default: () => [],
  },
  responsiveClass: {
    type: String,
    default: '',
  },
})

// * Component emits
const emits = defineEmits(['changeFilters'])

// * Customize
const { amCustomize } = useReactiveCustomize()

// * Root Settings
let amSettings = inject('settings')

let localLanguage = inject('localLanguage')

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

// * Cabinet type
// let cabinetType = inject('cabinetType')

let filterParams = ref({
  dates: [moment().toDate(), moment().add(6, 'days').toDate()],
  services: [],
  providers: [],
  customers: [],
  locations: [],
  events: [],
  packages: [],
})

let entities = ref({
  services: [
    {
      name: 'Entrepreneurial and private clients',
      id: 1,
    },
    {
      name: 'Family business services',
      id: 2,
    },
    {
      name: 'Sustainability and climate change',
      id: 3,
    },
    {
      name: 'Audit and assurance',
      id: 4,
    },
    {
      name: 'Industrial engineering',
      id: 5,
    },
    {
      name: 'Operations management',
      id: 6,
    },
    {
      name: 'Organizational Development',
      id: 7,
    },
  ],
  providers: [
    {
      id: 1,
      firstName: 'John',
      lastName: 'Doe',
    },
    {
      id: 2,
      firstName: 'Jane',
      lastName: 'Doe',
    },
  ],
  customers: [
    {
      id: 1,
      firstName: 'John',
      lastName: 'Doe',
    },
    {
      id: 2,
      firstName: 'Jane',
      lastName: 'Doe',
    },
  ],
  locations: [
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
  ],
  events: [
    {
      name: 'Healthy Plate Symposium',
      id: 1,
    },
    {
      name: 'Nutrition Nirvana Workshop',
      id: 2,
    },
    {
      name: 'Culinary Wellness Cooking Class',
      id: 3,
    },
  ],
  packages: [
    {
      name: 'Package 1',
      id: 1,
    },
    {
      name: 'Package 2',
      id: 2,
    },
  ],
})

let filterIcon = {
  components: { IconComponent },
  template: `<IconComponent icon="filter"/>`,
}

// * Filter button visibility
let filterBtnVisibility = computed(() => {
  return Object.values(props.paramList).filter((p) => p.name !== 'dates').some((p) => amCustomize.value[pageRenderKey.value][stepName.value].options[`${p.name}Filter`].visibility && entities.value[p.name].length > 0)
})

let cWidth = inject('containerWidth')

let filtersMenuVisibility = ref(false)

// * Options
let customizeOptions = computed(() => {
  return amCustomize.value[pageRenderKey.value][stepName.value].options
})

function datesChanged(val) {
  if (val && val[0] && val[1]) {
    filterParams.value.dates = val
    emits('changeFilters', filterParams.value)
  }
}

function changeFilters() {
  emits('changeFilters', filterParams.value)
}

let itemsNumber = computed(() => {
  let numb = 0
  Object.values(props.paramList).forEach((p) => {
    if (
      p.name !== 'dates' &&
      entities.value[p.name].length > 0 &&
      amCustomize.value[pageRenderKey.value][stepName.value].options[
        `${p.name}Filter`
      ].visibility
    ) {
      numb++
    }
  })
  return numb
})

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-capf-heading-text': amColors.value.colorMainText,
    '--am-c-capf-text': amColors.value.colorInpText,
    '--am-c-capf-primary': amColors.value.colorPrimary,
    '--am-c-capf-text-op10': useColorTransparency(
      amColors.value.colorInpText,
      0.1
    ),
    '--am-c-capf-primary-op10': useColorTransparency(
      amColors.value.colorPrimary,
      0.1
    ),
    '--am-c-capf-primary-op70': useColorTransparency(
      amColors.value.colorPrimary,
      0.7
    ),
  }
})
</script>

<script>
export default {
  name: 'CabinetFilters',
}
</script>

<style lang="scss">
@import '../../../../../../../assets/scss/common/transitions/_transitions-mixin.scss';

#amelia-app-backend-new {
  #amelia-container {
    // am - amelia
    // capf - catalog panel filters
    .am-capf {
      @include slide-fade;
      margin-bottom: 20px;

      &__zone {
        padding: 0 0 16px;
      }

      &__heading {
        font-size: 14px;
        font-weight: 500;
        line-height: 1.42857;
        color: var(--am-c-capf-heading-text);
        margin-bottom: 8px;
      }

      &__list {
        display: flex;
        flex-wrap: wrap;
        margin-top: 10px;
        justify-content: space-between;

        &-item {
          position: relative;

          .el-input {
            &__prefix {
              font-size: 24px;
              color: var(--am-c-capf-text);
            }

            &__inner {
              font-weight: 500;
              font-size: 14px;
              color: var(--am-c-capf-text);
              padding-left: 40px !important;

              &::placeholder {
                color: var(--am-c-capf-text);
              }
            }
          }

          .el-select {
            &__tags {
              display: flex;
              max-width: calc(100% - 68px) !important;
              left: 40px;

              & > span {
                display: flex;
                width: 100%;
              }

              &-text {
                max-width: unset !important;
                width: 100%;
                font-size: 14px;
              }

              .el-tag {
                min-width: 46px;
                overflow: hidden;
                background-color: transparent;
                margin: 0;
                padding: 0;
                border: none;

                &.is-closable {
                  .el-select__tags-text {
                    display: block;
                  }
                }

                &__content {
                  width: 100%;
                }

                &__close {
                  display: none;
                }
              }
            }
          }

          &.am-selected {
            .el-input {
              &__suffix {
                &-inner {
                  display: none;
                }
              }
            }
          }

          &.am-capf__list-item-4 {
            width: calc(25% - 2px);

            &.am-rw-650 {
              width: calc(50% - 2px);
              margin: 0 0 4px;
            }

            &.am-rw-500 {
              width: 100%;
              margin: 0 0 4px;
            }
          }

          &.am-capf__list-item-3 {
            width: calc(33.333% - 2px);

            &.am-rw-500 {
              width: 100%;
              margin: 0 0 4px;
            }
          }

          &.am-capf__list-item-2 {
            width: calc(50% - 2px);

            &.am-rw-500 {
              width: 100%;
              margin: 0 0 4px;
            }
          }

          &.am-capf__list-item-1 {
            width: 100%;
          }
        }
      }

      &__menu {
        display: flex;
        flex-wrap: nowrap;
        justify-content: right;
        margin-bottom: 20px;

        &-datepicker {
          .el-input {
            &__prefix {
              font-size: 24px;
              color: var(--am-c-capf-text);
            }

            &__inner {
              font-weight: 500;
              font-size: 14px;
              color: var(--am-c-capf-text);
              padding-left: 40px !important;

              &::placeholder {
                color: var(--am-c-capf-text);
              }
            }
          }

          .el-range {
            &-editor {
              height: 32px;
              padding-left: 10px !important;
              width: 100%;
            }

            &__icon {
              font-size: 24px;
              color: var(--am-c-capf-text);
            }

            &-input {
              font-size: 14px;
              font-weight: 500;
              color: var(--am-c-capf-text);
              background-color: transparent;
            }

            &-separator {
              color: var(--am-c-capf-text);
            }
          }
        }

        &-btn {
          border-radius: 6px;
          box-shadow: 0 5px 5px var(--am-c-capf-text-op10);

          &.am-button {
            margin-left: 16px;
          }

          .am-icon-filter {
            font-size: 24px;
          }
        }
      }

      &__clear {
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        width: 13px;
        height: 32px;
        top: 0;
        right: 10px;
        color: var(--am-c-primary);
        cursor: pointer;
        z-index: 999;
      }
    }
  }
}
</style>
