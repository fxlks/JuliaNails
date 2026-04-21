<template>
  <el-dialog
    ref="amDialogRef"
    v-model="model"
    :modal-class="`am-dialog-popup ${props.modalClass}`"
    :class="props.customClass"
    :title="props.title"
    :width="props.width"
    :fullscreen="props.fullscreen"
    :top="props.top"
    :modal="props.modal"
    :append-to-body="props.appendToBody"
    :lock-scroll="props.lockScroll"
    :open-delay="props.openDelay"
    :close-delay="props.closeDelay"
    :close-on-click-modal="props.closeOnClickModal"
    :close-on-press-escape="props.closeOnPressEscape"
    :show-close="props.showClose"
    :before-close="props.beforeClose"
    :center="props.center"
    :destroy-on-close="props.destroyOnClose"
    :close-icon="props.closeIcon"
    :style="props.customStyles"
    @close="emits('close')"
    @open="emits('open')"
    @closed="emits('closed')"
    @opened="emits('opened')"
  >
    <template #header>
      <span v-if="title" class="am-dialog__title">{{ title }}</span>
      <slot v-else name="title" />
    </template>
    <slot />
    <template #footer>
      <slot name="footer"/>
    </template>
  </el-dialog>
</template>

<script setup>
import AmeliaIconClose from '../icons/IconClose.vue'

// * Import from Vue
import {
  toRefs,
  computed,
  ref,
  onMounted
} from "vue";

/**
 * Component Props
 */
const props = defineProps({
  modelValue: {
    type: [String, Array, Object, Number, Boolean],
  },
  modalClass: {
    type: String,
    default: ''
  },
  title: {
    type: String,
    default: ''
  },
  width: {
    type: [String, Number],
    default: '50%'
  },
  fullscreen: {
    type: Boolean,
    default: false
  },
  top: {
    type: String,
    default: '15vh'
  },
  modal: {
    type: Boolean,
    default: true
  },
  appendToBody: {
    type: Boolean,
    default: false
  },
  alignCenter: {
    type: Boolean,
    default: false
  },
  lockScroll: {
    type: Boolean,
    default: true
  },
  customClass: {
    type: String,
    default: ''
  },
  openDelay: {
    type: Number,
    default: 0
  },
  closeDelay: {
    type: Number,
    default: 0
  },
  closeOnClickModal: {
    type: Boolean,
    default: true
  },
  closeOnPressEscape: {
    type: Boolean,
    default: true
  },
  showClose: {
    type: Boolean,
    default: true
  },
  beforeClose: {
    type: Function
  },
  center: {
    type: Boolean,
    default: false
  },
  destroyOnClose: {
    type: Boolean,
    default: false
  },
  closeIcon: {
    type: [Object, Function],
    default: AmeliaIconClose
  },
  customStyles: {
    type: Object
  },
  usedForShortcode: {
    type: Boolean,
    default: false
  }
})

const amDialogRef = ref(null)

onMounted(() => {
  if (props.usedForShortcode) {
    amDialogRef.value.rendered = true
  }
})

/**
 * Component Emits
 * */
const emits = defineEmits(['close', 'open', 'closed', 'opened', 'update:modelValue'])

/**
 * Component model
 */
let { modelValue } = toRefs(props)
let model = computed({
  get: () => modelValue.value,
  set: (val) => {
    emits('update:modelValue', val)
  }
})

</script>

<script>
export default {
  inheritAttrs: false,
}
</script>

<style lang="scss">
@import '../../../../src/assets/scss/common/quill/quill';

.am-dialog-popup {
  .el-dialog {
    max-width: var(--el-dialog-width, 50%);
    width: 100%;
    margin: var(--el-dialog-margin-top,15vh) auto 50px;
    padding: 0;

    .el-dialog {
      &__header {
        padding: 16px;
      }

      &__headerbtn {
        width: auto;
        height: auto;
      }

      &__body {
        padding: 16px;
      }

      &__footer {
        padding: 16px;
      }
    }
  }
}
</style>
