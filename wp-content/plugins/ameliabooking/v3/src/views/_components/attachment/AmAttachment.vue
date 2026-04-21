<template>
  <el-upload
    ref="attachments"
    v-model:file-list="model"
    :class="['am-attachment', props.customClass]"
    :action="props.action"
    :multiple="props.multiple"
    :data="props.data"
    :name="props.name"
    :with-credentials="props.withCredentials"
    :show-file-list="props.showFileList"
    :drag="props.drag"
    :accept="props.accept"
    :auto-upload="props.autoUpload"
    :disabled="props.disabled"
    :limit="props.limit"
    :on-change="onChange"
    :on-remove="onRemove"
  >
    <AmButton
      class="am-attachment__btn"
      :icon-only="props.iconOnly"
      :size="props.btnSize"
      :category="props.btnCategory"
      :type="props.btnType"
      :native-type="props.btnNativeType"
      :round="props.btnRound"
      :circle="props.btnCircle"
      :loading="props.btnLoading"
      :autofocus="props.btnAutofocus"
      :prefix="props.btnPrefix"
      :suffix="props.btnSuffix"
      :icon="props.btnIcon"
      :loading-icon="props.btnLoadingIcon"
      :disabled="props.disabled"
    >
      <slot></slot>
    </AmButton>
  </el-upload>
</template>

<script setup>
import AmButton from '../button/AmButton.vue'
import { computed, ref } from "vue";

/**
 * Component Props
 */
const props = defineProps({
  id: {
    type: [String, Number],
    default: 0
  },
  modelValue: {
    type: Array
  },
  action: {
    type: String,
    default: ''
  },
  multiple: {
    type: Boolean,
    default: false
  },
  data: {
    type: [String, Object, Array, Function, Number],
  },
  name: {
    type: String,
    name: 'file'
  },
  withCredentials: {
    type: Boolean,
    default: false
  },
  showFileList: {
    type: Boolean,
    default: true
  },
  drag: {
    type: Boolean,
    default: false
  },
  accept: String,
  autoUpload: {
    type: Boolean,
    default: true
  },
  disabled: {
    type: Boolean,
    default: false
  },
  limit: Number,
  customClass: {
    type: String,
    default: ''
  },
  iconOnly: {
    type: Boolean,
    default: false
  },
  btnSize: {
    // default / medium / small / mini / micro
    type: String,
    default: 'default',
    validator(value) {
      return ['default', 'medium', 'small', 'mini', 'micro'].includes(value)
    }
  },
  btnCategory: {
    // primary / secondary / success / warning / danger / error
    type: String,
    default: 'primary',
    validator(value) {
      return ['primary', 'secondary', 'success', 'warning', 'danger', 'error'].includes(value)
    }
  },
  btnType: {
    // filled / plain / text
    type: String,
    default: 'filled',
    validator(value) {
      return ['filled', 'plain', 'text'].includes(value)
    }
  },
  btnNativeType: {
    // button / submit / reset
    type: String,
    default: 'button',
    validator(value) {
      return ['button', 'submit', 'reset'].includes(value)
    }
  },
  btnRound: {
    type: Boolean,
    default: false
  },
  btnCircle: {
    type: Boolean,
    default: false
  },
  btnLoading: {
    type: Boolean,
    default: false
  },
  btnAutofocus: {
    type: Boolean,
    default: false
  },
  btnPrefix: {
    type: [String, Object, Function],
    default: ''
  },
  btnSuffix: {
    type: [String, Object, Function],
    default: ''
  },
  btnIcon: {
    type: [String, Object, Function],
    default: ''
  },
  btnLoadingIcon: {
    type: [String, Object, Function],
    default: ''
  }
})

/**
 * Component Emits
 * */
const emits = defineEmits(['change', 'remove', 'update:modelValue'])

// * Component model
let model = computed({
  get: () => props.modelValue,
  set: (val) => {
    emits('update:modelValue', val)
  }
})

const attachments = ref(null)

/**
 * Component Event Handlers
 */
function onChange (uploadFile, uploadFiles) {
  let file = {
    id: props.id,
    raw: uploadFiles
  }

  emits('change', file)
}

function onRemove (removedFile, arrayOfFiles) {
  let file = {
    id: props.id,
    raw: arrayOfFiles
  }

  emits('remove', file)
}
</script>

<style lang="scss">
.amelia-v2-booking {
  #amelia-container {
    .am-attachment {
      // -atf- attachment file
      --am-c-atf-text: var(--am-c-main-text);
      --am-c-atf-bgr: var(--am-c-main-bgr);
      max-width: 100%;
      width: 100%;

      &__btn {
        width: 100%;
        margin: 0;

        &.is-disabled {
          cursor: not-allowed;
        }
      }

      .el-upload {
        width: 100%;

        &-list {
          &__item {
            width: 100%;
            margin: 4px 0;
            background-color: var(--am-c-atf-bgr);

            &:hover {
              --am-c-atf-bgr: var(--am-c-inp-bgr);
              --am-c-atf-text: var(--am-c-inp-text);
            }

            &-name {
              max-width: 200px;
              padding: 4px;
              margin: 0 24px 0 0;
              color: var(--am-c-atf-text);

              * {
                color: var(--am-c-atf-text);
              }
            }

            &-status-label {
              top: 7px;
              i {
                color: var(--am-c-primary);
              }
            }

            .el-icon--close {
              color: var(--am-c-atf-text);
            }

            .el-progress {
              display: none;
            }
          }
        }
      }
    }
  }
}
</style>
