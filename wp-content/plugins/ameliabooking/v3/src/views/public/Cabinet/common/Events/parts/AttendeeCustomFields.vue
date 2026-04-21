<template>
  <el-form
    ref="customFieldsFormRef"
    :model="customFieldsForm"
    :rules="customFieldsFormRules"
    label-position="top"
    class="am-capei-att-cf__form"
    :class="props.responsiveClass"
  >
    <template v-for="id in Object.keys(customFields)" :key="id">
      <component
        :is="cfFormConstruction[id].template"
        v-if="id in customFieldsForm"
        v-model="customFieldsForm[id]"
        v-bind="cfFormConstruction[id].props"
      ></component>
    </template>
  </el-form>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  inject,
  computed,
  onMounted,
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

// * Settings
const amSettings = inject('settings')

// * Labels
let amLabels = inject('amLabels')

let customFieldsFormRef = ref(null)
let customFieldsForm = ref({})
let customFieldsFormRules = ref({})
let cfFormConstruction = ref({})

let customFields = computed(() => store.getters['attendee/getCustomFields'])

function setFormData(field, id) {
  let bookingId = store.getters['attendee/getId']

  if (field.type !== 'file' || bookingId) {
    customFieldsForm.value[id] = computed({
      get: () => {
        let value = store.getters['attendee/getCustomFields'][id]?.value

        return value ? value : ''
      },
      set: (value) =>
        store.commit('attendee/setCustomFields', {id: id, value: value}),
    })
  }
}

function setFormConstruction(field, id) {
  let bookingId = store.getters['attendee/getId']

  if (field.type !== 'file' || bookingId) {
    cfFormConstruction.value[id] = {
      template: formFieldsTemplates[field.type],
      props: {
        id: id,
        itemName: id.toString(),
        label: field.label,
        options: field.options,
        class: `am-capei-att-cf__item am-cf-width-${field.width}`,
      },
    }
  }

  if (field.type === 'checkbox' || field.type === 'radio') {
    cfFormConstruction.value[id].props.options = cfFormConstruction.value[id].props.options.map((option) => {
      return {
        ...option,
        value: option.label,
      }
    })
  }

  if (field.type === 'text-area') {
    cfFormConstruction.value[id].props = {
      ...cfFormConstruction.value[id].props,
      ...{ itemType: 'textarea' },
    }
  }

  if (field.type === 'file' && bookingId) {
    cfFormConstruction.value[id].props = {
      ...cfFormConstruction.value[id].props,
      ...{
        btnLabel: amLabels.value.upload_file_here,
        isUpload: false,
        bookingId: bookingId,
        source: 'cabinet-provider'
      },
    }
  }

  if (field.type === 'datepicker') {
    cfFormConstruction.value[id].props = {
      ...cfFormConstruction.value[id].props,
      ...{ weekStartsFromDay: amSettings.wordpress.startOfWeek },
    }
  }
}

function setFormRules(field) {
  let bookingId = store.getters['attendee/getId']

  if (field.type !== 'file' || bookingId) {
    customFieldsFormRules.value[field.id] = [
      {
        message: amLabels.value.required_field,
        required: field.required,
        trigger: ['submit', 'change'],
      },
    ]
  }
}

onMounted(() => {
  customFieldsForm.value = {}

  cfFormConstruction.value = {}

  for (let id in customFields.value) {
    setFormData(customFields.value[id], id)

    setFormConstruction(customFields.value[id], id)

    setFormRules(customFields.value[id])
  }
})

defineExpose({
  customFieldsFormRef,
})
</script>

<style lang="scss">
@mixin am-cabinet-appointment-custom-fields {
  // am      - amelia
  // capei   - cabinet-panel-event-item
  // att     - attendee
  // cf      - custom fields
  .am-capei-att-cf {
    &__form {
      display: flex;
      flex-wrap: wrap;
      gap: 0 24px;

      &.am-rw-500 {
        .am-capei-att-cf__item {
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

      .am-capei-att-cf__item {
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
