<template>
  <el-form
    ref="employeeFormRef"
    :model="employeeFormData"
    :rules="employeeFormRules"
    label-position="top"
    class="am-caepif"
    :class="props.responsiveClass"
  >
    <div
      class="am-caepif__block"
      :class="[{ 'am-w-70': employee.pictureFullPath }, props.responsiveClass]"
    >
      <template
        v-for="(item, name) in employeeInfoFormConstruction"
        :key="name"
      >
        <component
          :is="item.template"
          ref="customerCollectorRef"
          v-model="employeeFormData[name]"
          v-model:countryPhoneIso="item.countryPhoneIso"
          v-bind="item.props"
        ></component>
      </template>
    </div>
    <div
      v-if="employee.pictureFullPath"
      class="am-caepif__block am-w-30"
      :class="props.responsiveClass"
    >
      <div
        class="am-caepif__img"
        :style="{ backgroundImage: `url(${employee.pictureFullPath})` }"
      ></div>
    </div>
    <div class="am-caepif__block" :class="props.responsiveClass">
      <template
        v-for="(item, name) in employeeDataFormConstruction"
        :key="name"
      >
        <component
          :is="item.template"
          ref="customerCollectorRef"
          v-model="employeeFormData[name]"
          v-bind="item.props"
          v-on="item.handlers ? item.handlers : {}"
        >
          <template v-if="item.slots && item.slots.default">
            <div v-html="item.slots.default" />
          </template>
        </component>
      </template>
      <el-form-item :label="amLabels.description_colon">
        <DescriptionEditor
          v-model="employeeFormData.description"
          :mode="store.getters['employee/getDescriptionMode']"
          @set-mode="(val) => store.commit('employee/setDescriptionMode', val)"
        />
      </el-form-item>
    </div>
  </el-form>
</template>

<script setup>
// * Import from Vue
import {computed, ref, inject, onBeforeMount} from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Import Form Templates
import { formFieldsTemplates } from '../../../../../../assets/js/common/formFieldsTemplates'

// * Components
import DescriptionEditor from '../../parts/DescriptionEditor.vue'

// * Props
const props = defineProps({
  responsiveClass: {
    type: String,
    default: '',
  },
})

// * Store
const store = useStore()

// * Settings
const amSettings = inject('settings')

// * Labels
let amLabels = inject('amLabels')

let employee = computed(() => {
  return store.getters['entities/getEmployee'](store.getters['employee/getId'])
})

// * Locations
let locations = computed(() => store.getters['entities/getLocations'])

// * Locations options
let locationsOptions = computed(() => {
  return locations.value.map((location) => {
    return {
      value: location.id,
      label: location.name,
    }
  })
})

// * Form Reference
let employeeFormRef = ref(null)

// * Phone Error
let phoneError = ref(false)

// * Form Data
let employeeFormData = ref({
  firstName: computed({
    get: () => store.getters['employee/getFirstName'],
    set: (val) => {
      store.commit('employee/setFirstName', val ? val : '')
    },
  }),
  lastName: computed({
    get: () => store.getters['employee/getLastName'],
    set: (val) => {
      store.commit('employee/setLastName', val ? val : '')
    },
  }),
  email: computed({
    get: () => store.getters['employee/getEmail'],
    set: (val) => {
      store.commit('employee/setEmail', val ? val : '')
    },
  }),
  phone: computed({
    get: () => store.getters['employee/getPhone'],
    set: (val) => {
      store.commit('employee/setPhone', val ? val : '')
    },
  }),
  locationId: computed({
    get: () => store.getters['employee/getLocationId'],
    set: (val) => {
      store.commit('employee/setLocationId', val ? val : '')
    },
  }),
  description: computed({
    get: () => store.getters['employee/getDescription'],
    set: (val) => {
      store.commit('employee/setDescription', val ? val : '')
    },
  }),
})

// * Form validation rules
let employeeFormRules = computed(() => {
  return {
    firstName: [
      {
        required: true,
        message: amLabels.enter_first_name_warning,
        trigger: ['submit', 'change'],
      },
    ],
    lastName: [
      {
        required: true,
        message: amLabels.enter_last_name_warning,
        trigger: ['submit', 'change'],
      },
    ],
    email: [
      {
        required: true,
        message: amLabels.enter_email_warning,
        trigger: ['submit', 'change'],
      },
    ],
    locationId: [
      {
        required:
          locations.value.filter((i) => i.status === 'visible').length > 0,
        message: amLabels.enter_location_warning,
        trigger: ['submit', 'change'],
      },
    ],
  }
})

// * Form Construction
let employeeInfoFormConstruction = ref({
  firstName: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'firstName',
      label: amLabels.first_name_colon,
      placeholder: amLabels.enter_first_name,
      class: computed(() => `am-caepif__item ${props.responsiveClass}`)
    },
  },
  lastName: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'lastName',
      label: amLabels.last_name_colon,
      placeholder: amLabels.enter_last_name,
      class: computed(() => `am-caepif__item ${props.responsiveClass}`),
    },
  },
  email: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'email',
      label: amLabels.email_colon,
      placeholder: amLabels.enter_email,
      class: computed(() => `am-caepif__item ${props.responsiveClass}`),
    },
  },
  phone: {
    countryPhoneIso: computed({
      get: () =>
        store.getters['auth/getProfile'].countryPhoneIso
          ? store.getters['auth/getProfile'].countryPhoneIso
          : '',
      set: (val) => {
        store.commit(
          'auth/setProfileCountryPhoneIso',
          val ? val.toLowerCase() : ''
        )
      },
    }),
    template: formFieldsTemplates.phone,
    props: {
      itemName: 'phone',
      label: amLabels.phone_colon,
      placeholder: amLabels.enter_phone,
      defaultCode: computed(() =>
        store.getters['auth/getProfile'].countryPhoneIso
          ? store.getters['auth/getProfile'].countryPhoneIso
          : ''
      ),
      phoneError: computed(() => phoneError.value),
      whatsAppLabel: amLabels.whatsapp_opt_in_text,
      isWhatsApp: amSettings.notifications.whatsAppEnabled,
      class: computed(() => `am-caepif__item ${props.responsiveClass}`),
    },
  },
})

let employeeDataFormConstruction = ref({
  locationId: {
    template: formFieldsTemplates.select,
    props: {
      itemName: 'locationId',
      label: `${amLabels.location}:`,
      placeholder: amLabels.select_location,
      class: computed(
        () =>
          `am-caepif__item ${props.responsiveClass} ${
            amSettings.zoom.enabled || 'am-w-100'
          }`
      ),
      options: locationsOptions.value,
    },
  },
})

onBeforeMount(() => {
  if (!locations.value.length) {
    delete employeeDataFormConstruction.value.locationId
  }
})

// * Expose
defineExpose({
  employeeFormRef,
})
</script>

<style lang="scss">
@mixin employee-details-block {
  // am    - amelia
  // caepif - cabinet employee personal information form
  .am-caepif {
    display: flex;
    flex-wrap: wrap;
    padding: 24px 0;
    gap: 24px;

    &.am-rw- {
      &620 {
        justify-content: center;
        gap: 24px;
      }
    }

    // Form Block
    &__block {
      width: 100%;
      display: flex;
      align-items: flex-start;
      gap: 24px 16px;
      flex-wrap: wrap;
      box-sizing: border-box;
      margin-bottom: 8px;

      &.am-rw- {
        &620 {
          order: 3;
        }
      }

      &.am-w-70 {
        width: calc(70% - 12px);

        &.am-rw- {
          &620 {
            width: 100%;
            order: 2;
          }
        }
      }

      &.am-w-30 {
        width: calc(30% - 12px);

        &.am-rw- {
          &620 {
            order: 1;
          }

          &460 {
            width: 50%;
          }

          &320 {
            width: 100%;
          }
        }
      }
    }

    // Form Item
    &__item {
      width: calc(50% - 8px);

      &.am-rw- {
        &460 {
          width: 100%;
        }
      }

      &.am-w-100 {
        width: 100%;
      }

      &.el-form-item {
        margin-bottom: 0 !important;
      }

      &.am-button {
        align-self: flex-end;
      }

      .el-form-item {
        &__label {
          padding-bottom: 4px;
        }
      }
    }

    // Employee Image
    &__img {
      width: 100%;
      padding-top: 100%;
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      border-radius: 50%;
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include employee-details-block;
}
</style>