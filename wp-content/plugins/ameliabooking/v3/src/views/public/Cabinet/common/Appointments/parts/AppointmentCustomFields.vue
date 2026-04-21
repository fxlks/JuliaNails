<template>
  <el-form
    ref="customFieldsFormRef"
    :model="customFieldsForm"
    :rules="customFieldsFormRules"
    label-position="top"
    class="am-capai-cfields am-capai-cfields__form"
    :class="props.responsiveClass"
  >
    <el-collapse
      v-model="activeBookingIndex"
      class="am-capai-collapse"
      accordion
    >
      <el-collapse-item
        v-for="(item, index) in store.getters['appointment/getBookings'].filter((b) => b.status !== 'canceled' && b.status !== 'rejected')"
        :key="index"
        :name="index"
        class="am-capai-collapse__item"
      >
        <template #title>
          <!-- Customer Name -->
          <div
            class="am-capai-customer__name"
            :class="[{'am-pb-16': !((item.customer.email && customerEmailVisibility) || (item.customer.phone && customerPhoneVisibility))}, noShowData(item.customer.noShowCount).class]"
          >
          <span
            v-if="noShowData(item.customer.noShowCount).icon"
            :class="`am-icon-${noShowData(item.customer.noShowCount).icon}`"
          />
            {{ item.customer.firstName + ' ' + item.customer.lastName }}
          </div>
          <!-- /Customer Name -->
        </template>

        <!-- Customer Info -->
        <div
          v-if="(item.customer.email && customerEmailVisibility) || (item.customer.phone && customerPhoneVisibility)"
          class="am-capai-customer__data-wrapper"
          :class="props.responsiveClass"
        >
          <!-- Customer Email -->
          <div
            v-if="item.customer.email && customerEmailVisibility"
            class="am-capai-customer__data"
            :class="props.responsiveClass"
          >
            <span class="am-icon-email"></span>
            <a :href="`mailto:${item.customer.email}`">
              {{ item.customer.email }}
            </a>
          </div>
          <!-- /Customer Email -->

          <!-- Customer Phone -->
          <div
            v-if="item.customer.phone && customerPhoneVisibility"
            class="am-capai-customer__data"
            :class="props.responsiveClass"
          >
            <span class="am-icon-phone"></span>
            <a :href="`tel:${item.customer.phone}`">
              {{ item.customer.phone }}
            </a>
          </div>
          <!-- /Customer Phone -->
        </div>
        <!-- /Customer Info -->

          <template v-for="cf in customFields" :key="index + '_' + cf.id">
            <component
              :is="cfFormConstruction[index][cf.id].template"
              v-if="index in cfFormConstruction && `${index}cf${cf.id}` in customFieldsForm"
              v-model="customFieldsForm[`${index}cf${cf.id}`]"
              v-bind="cfFormConstruction[index][cf.id].props"
            ></component>
          </template>
      </el-collapse-item>
    </el-collapse>
  </el-form>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  inject,
  computed,
  watch,
  onBeforeMount,
  onMounted
} from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Import Form Templates
import { formFieldsTemplates } from '../../../../../../assets/js/common/formFieldsTemplates'

// * Props
const props = defineProps({
  responsiveClass: {
    type: String,
    default: '',
  },
})

// * Store
let store = useStore()

// * Data in shortcode
const shortcodeData = inject('shortcodeData')

// * Customize data
let amCustomize = inject('amCustomize')

// * Settings
const amSettings = inject('settings')

// * Labels
let amLabels = inject('amLabels')

let customFieldsFormRef = ref(null)
let customFieldsForm = ref({})
let customFieldsFormRules = computed(
  () => store.getters['appointment/getCustomFieldsRules']
)
let cfFormConstruction = ref({})

let customFields = computed(() => store.getters['appointment/getCustomFields'])

let serviceId = computed(() => store.getters['appointment/getServiceId'])
let customersIds = computed(() => store.getters['customerInfo/getCustomersIds'])

let activeBookingIndex = ref(null)

function setFormData(index, field) {
  let bookingId = store.getters['appointment/getBookings'][index].id

  if (field.type !== 'file' || bookingId) {
    customFieldsForm.value[`${index}cf${field.id}`] = computed({
      get: () => {
        let value = store.getters['appointment/getBookings'][index].customFields[field.id]?.value
        return value ? value : ''
      },
      set: (value) =>
        store.dispatch('appointment/updateBookingCustomField', {
          bookingKey: index,
          fieldId: field.id,
          value,
        }),
    })
  }
}

function setFormConstruction(index, field) {
  let bookingId = store.getters['appointment/getBookings'][index].id

  if (field.type !== 'file' || bookingId) {
    cfFormConstruction.value[index][field.id] = {
      template: formFieldsTemplates[field.type],
      props: {
        id: field.id,
        itemName: `${index}cf${field.id}`,
        label: field.label,
        options: field.options,
        class: `am-capai-cfields__item am-cf-width-${field.width}`,
      },
    }
  }

  if (field.type === 'checkbox' || field.type === 'radio') {
    cfFormConstruction.value[index][field.id].props.options = cfFormConstruction.value[index][field.id].props.options.map((option) => {
      return {
        ...option,
        value: option.label,
      }
    })
  }

  if (field.type === 'text-area') {
    cfFormConstruction.value[index][field.id].props = {
      ...cfFormConstruction.value[index][field.id].props,
      ...{ itemType: 'textarea' },
    }
  }

  if (field.type === 'file' && bookingId) {
    cfFormConstruction.value[index][field.id].props = {
      ...cfFormConstruction.value[index][field.id].props,
      ...{ btnLabel: amLabels.value.upload_file_here, isUpload: false, bookingId: bookingId },
    }
  }

  if (field.type === 'datepicker') {
    cfFormConstruction.value[index][field.id].props = {
      ...cfFormConstruction.value[index][field.id].props,
      ...{ weekStartsFromDay: amSettings.wordpress.startOfWeek },
    }
  }
}

function initCustomFieldsForm() {
  customFieldsForm.value = {}
  store.getters['appointment/getBookings'].forEach((booking, index) => {
    cfFormConstruction.value[index] = {}

    store.getters['appointment/getCustomFields'].forEach((field) => {
      setFormData(index, field)

      setFormConstruction(index, field)
    })
  })
}

const { noShowData } = inject('noShowData')

// * Customized options
let customerPhoneVisibility = computed(() => shortcodeData.value.cabinetType === 'employee' ? amCustomize.value.appointments.options.customerPhone.visibility : true)
let customerEmailVisibility = computed(() => shortcodeData.value.cabinetType === 'employee' ? amCustomize.value.appointments.options.customerEmail.visibility : true)

watch([serviceId, customersIds], () => {
  initCustomFieldsForm()
})

onBeforeMount(() => {
  initCustomFieldsForm()
})

onMounted(() => {
  if (store.getters['appointment/getBookings'].filter((b) => b.status !== 'canceled' && b.status !== 'rejected').length < 2) {
    activeBookingIndex.value = 0
  }
})

defineExpose({
  customFieldsFormRef,
})
</script>

<style lang="scss">
@mixin am-cabinet-appointment-custom-fields {
  // am      - amelia
  // capai   - cabinet-panel-appointment-item
  // cfields - custom fields
  .am-capai-cfields {
    &__form {
      &.am-rw-500 {
        .am-capai-cfields__item {
          width: 100%;
        }
      }

      & > * {
        $count: 100;
        @for $i from 0 through $count {
          &:nth-child(#{$i + 1}) {
            animation: 600ms cubic-bezier(.45,1,.4,1.2) #{$i*100}ms am-animation-slide-up;
            animation-fill-mode: both;
          }
        }
      }

      .am-capai-cfields__item {
        width: calc(50% - 12px);

        &.am-cf-width-100 {
          width: 100%;
        }

        &.is-required {
          .el-form-item {
            &__label {
              position: relative;
              padding-left: 10px;
            }
          }
        }

        .el-form-item {
          &__label {
            display: inline-block;
            color: var(--am-c-main-text);
            font-family: var(--am-font-family), sans-serif;
            font-weight: 500;
            line-height: unset;
            margin-bottom: 4px;
            padding: 0;

            &:before {
              color: var(--am-c-error);
            }
          }

          &__error {
            line-height: 1;
            color: var(--am-c-error);
          }

          &__content {
            color: var(--am-c-main-text);
          }
        }
      }

      &__label {
        display: inline-block;
        color: var(--am-c-main-text);
        font-family: var(--am-font-family), sans-serif;
        font-weight: 500;
        margin-bottom: 4px;
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-appointment-custom-fields;
}
</style>