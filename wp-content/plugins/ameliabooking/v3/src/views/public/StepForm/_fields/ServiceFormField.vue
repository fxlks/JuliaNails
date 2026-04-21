<template>
  <el-form-item
    v-if="store.state.entities.services.length > 1 && store.state.entities.preselected.service.length !== 1"
    class="am-fs__init-form__item"
    :prop="store.state.entities.categories.length > 1 ? 'service' : 'onlyService'"
    :class="'am-service-select'"
    label-position="top"
  >
    <template #label>
      <span class="am-fs__init-form__label">
        {{amLabels.service_colon}}:
      </span>
    </template>
    <AmAdvancedSelect
      v-if="store.state.entities.categories.length > 1"
      v-model="initFormData.service"
      :filterable="props.filterable"
      :options="categoryOptions"
      :props-data="advSelectProps"
      :placeholder="amLabels.select_service"
      :category-name="amLabels.dropdown_category_heading"
      :sub-category-name="amLabels.dropdown_items_heading"
      :empty-state-string="amLabels.dropdown_empty"
      :tax-options="taxServicesOptions"
      :tax-label="`+${amLabels.total_tax_colon}`"
      :tax-label-incl="amLabels.incl_tax"
      :tax-visible="props.taxVisible"
      :aria-label="amLabels.select_service"
      @change="changeService"
    ></AmAdvancedSelect>
    <AmSelect
      v-else
      v-model="initFormData.onlyService"
      clearable
      :filterable="props.filterable"
      :popper-class="'am-service-dropdown'"
      :placeholder="amLabels.select_service"
      :aria-label="amLabels.select_service"
      :filter-method="filterService"
      @change="changeOnlyService"
    >
      <AmOption
        v-for="service in filteredServices"
        :key="service.id"
        :value="service.id"
        :label="service.name"
      >
        <div class="am-select-service">
          <span class="am-select-service-name">
            {{ service.name }}
          </span>
          <span
            v-if="service.price > 0"
            class="am-select-service-price"
          >
            {{ useFormattedPrice(service.price) }}
            <span
              v-if="taxVisibility(service.id)"
              class="am-select-service-tax"
            >
              <template v-if="taxServicesOptions.find(a => a.id === service.id).excluded">
                {{ `+${amLabels.total_tax_colon}` }}
              </template>
              <template v-else>
                {{ `${amLabels.incl_tax}` }}
              </template>
            </span>
          </span>
        </div>
      </AmOption>
    </AmSelect>
  </el-form-item>
</template>

<script setup>
import { useStore } from "vuex";
import { computed, inject, reactive, ref } from "vue";
import AmSelect from '../../../_components/select/AmSelect'
import AmOption from '../../../_components/select/AmOption'
import AmAdvancedSelect from '../../../_components/advanced-select/AmAdvancedSelect'
import useAction from "../../../../assets/js/public/actions";
import { useFormattedPrice } from "../../../../assets/js/common/formatting"
import { useEntityTax } from "../../../../assets/js/common/pricing";

let props = defineProps({
  filterable: {
    type: Boolean,
    default: true
  },
  taxVisible: {
    type: Boolean,
    default: true
  }
})

// * Amelia Settings
const amSettings = inject('settings')

// * Store
let store = useStore();

// * Labels
const amLabels = inject('amLabels')

// * Sidebar steps
let { sidebarDataCollector } = inject('sidebarStepsFunctions', {
  sidebarDataCollector: () => {}
})

const { changeInitStepDataService } = inject('initDataChanges', {
  changeInitStepDataService: () => {}
})

const { footerBtnDisabledUpdater } = inject('changingStepsFunctions', {
  footerBtnDisabledUpdater: () => {}
})

// Container Width
let cWidth = inject('containerWidth', 0)
let checkScreen = computed(() => cWidth.value < 560 || (cWidth.value - 240 < 520))

/**
 * * Category - Service Block
 */

let initFormData = inject('initFormData')

// * Service select props
let advSelectProps = reactive({
  expandTrigger: checkScreen.value ? 'click': 'hover',
  multiple: false,
  checkStrictly: false,
  emitPath: true,
  lazy: false,
  value: 'id',
  label: 'name',
  children: 'serviceList',
  disabled: 'disabled',
  leaf: 'leaf'
})

// * Set Category Options from entities
let categoryOptions = computed(() => store.getters['entities/filteredCategories'](
  Object.assign(store.getters['booking/getSelection'], {serviceId: null, categoryId: null})
))

// * Set Service Options from entities
let serviceOptions = computed(() => store.getters['entities/filteredServices'](
    Object.assign(store.getters['booking/getSelection'], {serviceId: null, categoryId: null})
))
let taxServicesOptions = computed(() => {
  let arr = []

  if (amSettings.payments.taxes.enabled) {
    serviceOptions.value.forEach(service => {
      let tax = useEntityTax(store, service.id, 'service')
      if (tax) {
        let obj = {
          id: service.id,
          excluded: amSettings.payments.taxes.excluded
        }
        arr.push(obj)
      }
    })
  }

  return arr
})

// * Filter service
let queryLower = ref('')

function filterService (query) {
  queryLower.value = query.toLowerCase()
}

let filteredServices = computed(() => {
  if (queryLower.value) {
    return serviceOptions.value.filter((item) => {
      return item.name.toLowerCase().includes(queryLower.value)
    })
  }

  return serviceOptions.value
})

/**
 * Change Service function
 * @param val
 */
let changeService = (val) => {
  let serviceData = {
    reference: 'service',
    // position will depends on fields order
    position: 0,
    value: ''
  }

  if (Array.isArray(val)) {
    serviceData.value = categoryOptions.value.find((item) => item.id === val[0]).serviceList.find(item => item.id === val[1]).name
  }

  if (!store.getters['booking/getPackageId']) {
    changeInitStepDataService()
  }

  useAction(store, {}, 'SelectService', 'appointment', null, null)

  sidebarDataCollector(serviceData)

  footerBtnDisabledUpdater(false)
}

function changeOnlyService (val) {
  let serviceData = {
    reference: 'service',
    // position will depends on fields order
    position: 0,
    value: ''
  }

  if (val) {
    let service = serviceOptions.value.find((item) => item.id === val)
    serviceData.value = service ? service.name : ''
  }

  changeInitStepDataService()

  useAction(store, {}, 'SelectService', 'appointment', null, null)

  sidebarDataCollector(serviceData)
}

function taxVisibility (id) {
  return props.taxVisible && !!taxServicesOptions.value.filter(a => a.id === id).length
}
</script>

<script>
export default {
  name: "ServiceFormField"
}
</script>

<style lang="scss">
.am-service-dropdown {
  .am-select-service {
    --am-c-ss-item-name: var(--am-c-option-text);
    --am-c-ss-item-price: var(--am-c-option-selected);

    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;

    &-name {
      display: block;
      font-size: 14px;
      font-weight: 400;
      line-height: 1.43;
      color: var(--am-c-ss-item-name);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    &-price {
      font-size: 14px;
      font-weight: 400;
      line-height: 1.714;
      color: var(--am-c-ss-item-price);
    }

    &-tax {
      display: inline-flex;
      font-size: 14px;
      font-weight: 400;
      line-height: 1;
      color: var(--am-c-ss-item-price);
      background-color: var(--am-c-option-selected-op10);
      border-radius: 10px;
      padding: 3px 8px;
    }
  }
}
</style>
