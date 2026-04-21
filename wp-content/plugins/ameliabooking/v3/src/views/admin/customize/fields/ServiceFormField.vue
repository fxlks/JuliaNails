<template>
  <el-form-item
    class="am-fs__init-form__item"
    prop="service"
    label-position="top"
  >
    <template #label>
      <span class="am-fs__init-form__label">
        {{ `${labelsDisplay('service_colon')}:` }}
      </span>
    </template>
    <AmAdvancedSelect
      v-model="formData.service"
      :options="categoryOptions"
      :props-data="advSelectProps"
      :filterable="
        amCustomize[pageRenderKey].initStep.options.service.filterable
      "
      :placeholder="labelsDisplay('select_service')"
      :category-name="labelsDisplay('dropdown_category_heading')"
      :sub-category-name="labelsDisplay('dropdown_items_heading')"
      :empty-state-string="labelsDisplay('dropdown_empty')"
      @change="changeService"
    ></AmAdvancedSelect>
  </el-form-item>
</template>

<script setup>
import AmAdvancedSelect from '../../../_components/advanced-select/AmAdvancedSelect.vue'
import { computed, inject, reactive, ref } from 'vue'
import { useReactiveCustomize } from '../../../../assets/js/admin/useReactiveCustomize.js'

let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value].initStep.translations &&
      amCustomize.value[pageRenderKey.value].initStep.translations[label] &&
      amCustomize.value[pageRenderKey.value].initStep.translations[label][
        langKey.value
      ]
      ? amCustomize.value[pageRenderKey.value].initStep.translations[label][
          langKey.value
        ]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Sidebar steps
let { sidebarDataCollector } = inject('sidebarStepsFunctions', {
  sidebarDataCollector: () => {},
})

// * Form field data
let formData = inject('formData')

/**
 * * Category - Service Block
 */
// * Service select props
let advSelectProps = reactive({
  expandTrigger: 'hover',
  multiple: false,
  checkStrictly: false,
  emitPath: true,
  lazy: false,
  value: 'id',
  label: 'name',
  children: 'serviceList',
  disabled: 'disabled',
  leaf: 'leaf',
})

// * Set Category Options from entities
let categoryOptions = ref([
  {
    id: 1,
    name: 'Category 1',
    serviceList: [
      {
        id: 10,
        name: 'Service 11',
        price: 125,
      },
      {
        id: 11,
        name: 'Service 12',
        price: 125,
      },
      {
        id: 12,
        name: 'Service 13',
        price: 125,
      },
    ],
  },
  {
    id: 2,
    name: 'Category 2',
    serviceList: [
      {
        id: 10,
        name: 'Service 21',
        price: 125,
      },
      {
        id: 11,
        name: 'Service 22',
        price: 125,
      },
      {
        id: 12,
        name: 'Service 23',
        price: 125,
      },
    ],
  },
  {
    id: 3,
    name: 'Category 3',
    serviceList: [
      {
        id: 10,
        name: 'Service 31',
        price: 125,
      },
      {
        id: 11,
        name: 'Service 32',
        price: 125,
      },
      {
        id: 12,
        name: 'Service 33',
        price: 125,
      },
    ],
  },
  {
    id: 4,
    name: 'Category 4',
    serviceList: [
      {
        id: 10,
        name: 'Service 41',
        price: 125,
      },
      {
        id: 11,
        name: 'Service 42',
        price: 125,
      },
      {
        id: 12,
        name: 'Service 43',
        price: 125,
      },
    ],
  },
  {
    id: 5,
    name: 'Category 5',
    serviceList: [
      {
        id: 10,
        name: 'Service 51',
        price: 125,
      },
      {
        id: 11,
        name: 'Service 52',
        price: 125,
      },
      {
        id: 12,
        name: 'Service 53',
        price: 125,
      },
    ],
  },
])

/**
 * Change Service function
 * @param val
 */
let changeService = (val) => {
  let serviceData = reactive({
    reference: 'service',
    // position will depends on fields order
    position: 0,
    value: '',
  })

  if (Array.isArray(val)) {
    serviceData.value = categoryOptions.value
      .find((item) => item.id === val[0])
      .serviceList.find((item) => item.id === val[1]).name
  }

  sidebarDataCollector(serviceData)
}
</script>

<script>
export default {
  name: 'ServiceFormField',
}
</script>
