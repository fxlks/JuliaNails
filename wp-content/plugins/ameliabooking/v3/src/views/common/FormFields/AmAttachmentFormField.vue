<template>
  <el-form-item
    v-if="props.isUpload"
    :id="props.id"
    ref="formFieldRef"
    class="am-ff__item"
    :prop="props.itemName"
    :label-position="props.labelPosition"
  >
    <template #label>
      <span class="am-ff__item-label" v-html="props.label" />
    </template>
    <AmAttachment
      :id="props.id"
      v-model="model"
      :auto-upload="false"
      :accept="customFieldsAllowedExtensions"
      @change="onAddFile"
      @remove="onRemoveFile"
    >
      {{props.btnLabel}}
    </AmAttachment>
  </el-form-item>
  <div v-else>
    <p v-for="(fileInfo, index) in props.modelValue" :key="index">
      <a
        :key="index"
        :href="ajaxUrl + '/fields/' + props.id + '/' + props.bookingId + '/' + index + '&source=' + props.source"
        target="_blank"
      >
        {{ fileInfo.name }}
      </a>
    </p>
  </div>
</template>

<script setup>
// * _components
import AmAttachment from '../../_components/attachment/AmAttachment.vue'

// * Import from Vue
import {
  computed, onMounted,
  ref,
  toRefs
} from "vue";
import {settings} from "../../../plugins/settings";

// * Import from Vuex
import { useStore } from 'vuex'

// * Form Item Props
let props = defineProps({
  modelValue: {
    type: [String, Array, Object, Number],
    required: true
  },
  id: {
    type: [String, Number]
  },
  bookingId: {
    type: [String, Number]
  },
  itemName: {
    type: String,
    required: true
  },
  isUpload: {
    type: Boolean,
    default: true
  },
  label: {
    type: String
  },
  labelPosition: {
    type: String,
    default: 'top'
  },
  btnLabel: {
    type: String
  },
  source: {
    type: String,
    default: 'cabinet-provider'
  }
})

// * Define Emits
const emits = defineEmits(['update:modelValue', 'change', 'remove'])

const store = useStore()

let ajaxUrl = computed(() => store.getters['getBaseUrls'].wpAmeliaPluginAjaxURL)

// * Component model
let { modelValue } = toRefs(props)
let model = computed({
  get: () => modelValue.value,
  set: (val) => {
    emits('update:modelValue', val)
  }
})

// * Form Item Reference
let formFieldRef = ref(null)

defineExpose({
  formFieldRef
})

let customFieldsAllowedExtensions = ref('')

onMounted(() => {
  if (settings.general.customFieldsAllowedExtensions) {
    customFieldsAllowedExtensions.value = Object.keys(settings.general.customFieldsAllowedExtensions).join(', ')
  }
})

function onAddFile (a) {
  emits('update:modelValue', a.raw)
}

function onRemoveFile (a) {
  emits('update:modelValue', a.raw)
}
</script>

<script>
export default {
  name: "AttachmentFormField"
}
</script>
