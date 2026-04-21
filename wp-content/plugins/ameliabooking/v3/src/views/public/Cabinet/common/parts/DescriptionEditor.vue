<template>
  <div v-if="mode" class="am-desc-editor" :style="cssVars">
    <div class="am-desc-editor__header">
      <AmButtonGroup>
        <AmButton
          :category="mode === 'text' ? 'primary' : 'secondary'"
          :type="mode === 'text' ? 'filled' : 'plain'"
          size="small"
          @click="setTextMode"
        >
          {{ amLabels.text_mode }}
        </AmButton>
        <AmButton
          :category="mode === 'html' ? 'primary' : 'secondary'"
          :type="mode === 'html' ? 'filled' : 'plain'"
          size="small"
          @click="setHtmlMode"
        >
          {{ amLabels.html_mode }}
        </AmButton>
      </AmButtonGroup>
      <div class="am-desc-editor__header-warning">
        <span class="am-icon-triangle-info" />
        {{ amLabels.content_mode_tooltip }}
      </div>
    </div>
    <AmInput
      v-if="mode === 'html'"
      v-model="model"
      type="textarea"
      :rows="3"
      :style="{ width: '100%' }"
    />
    <div v-if="mode === 'text'" class="am-quill-wrapper">
      <AmQuill v-model:value="model" :options="options" />
    </div>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  computed,
  inject,
} from 'vue'

// * Import from Quill
import AmQuill from "./AmQuill.vue";

// * Components
import AmButtonGroup from '../../../../_components/button/AmButtonGroup.vue'
import AmButton from '../../../../_components/button/AmButton.vue'
import AmInput from '../../../../_components/input/AmInput.vue'

// * Composables
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation'

// * Component props
const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  mode: {
    type: String,
    default: 'html',
  },
})

// * Component emits
const emit = defineEmits(['update:modelValue', 'setMode'])

// * Labels
let amLabels = inject('amLabels')

let mode = ref(props.mode)

let model = computed({
  get: () => {
    return props.modelValue
  },
  set: (value) => {
    emit('update:modelValue', value)
  },
})

function setTextMode() {
  mode.value = 'text'

  emit('setMode', 'text')
}

function setHtmlMode() {
  mode.value = 'html'

  emit('setMode', 'html')
}

const options = {
  theme: 'snow',
  modules: {
    toolbar: [
      ['bold', 'italic', 'underline', 'strike'],
      ['blockquote'],
      [{ list: 'ordered' }, { list: 'bullet' }],
      [{ script: 'sub' }, { script: 'super' }],
      [{ indent: '-1' }, { indent: '+1' }],
      [{ direction: 'rtl' }],
      [{ color: [] }, { background: [] }],
      [{ size: ['small', false, 'large', 'huge'] }],
      [{ header: [1, 2, 3, 4, 5, 6, false] }],
      [{ font: [] }],
      [{ align: [] }],
      ['clean'],
      ['link', 'image'],
    ],
  },
  readOnly: false,
}

// * Color Vars
let amColors = inject(
  'amColors',
  ref({
    colorPrimary: '#1246D6',
    colorSuccess: '#019719',
    colorError: '#B4190F',
    colorWarning: '#CCA20C',
    colorMainBgr: '#FFFFFF',
    colorMainHeadingText: '#33434C',
    colorMainText: '#1A2C37',
    colorSbBgr: '#17295A',
    colorSbText: '#FFFFFF',
    colorInpBgr: '#FFFFFF',
    colorInpBorder: '#D1D5D7',
    colorInpText: '#1A2C37',
    colorInpPlaceHolder: '#808A90',
    colorDropBgr: '#FFFFFF',
    colorDropBorder: '#D1D5D7',
    colorDropText: '#0E1920',
    colorBtnPrim: '#265CF2',
    colorBtnPrimText: '#FFFFFF',
    colorBtnSec: '#1A2C37',
    colorBtnSecText: '#FFFFFF',
  })
)

// * Css Variables
let cssVars = computed(() => {
  // desce - description editor
  return {
    '--am-c-desce-text-op03': useColorTransparency(
      amColors.value.colorMainText,
      0.03
    ),
    '--am-c-desce-warn-op10': useColorTransparency(
      amColors.value.colorWarning,
      0.1
    ),
  }
})
</script>

<style lang="scss">
@import '../../../../../../src/assets/scss/common/quill/quill-mixin.scss';

@mixin am-desc-editor-block {
  .am-desc-editor {
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    background-color: var(--am-c-desce-text-op03);

    &__header {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;

      .am-button-group {
        width: 100%;
        justify-content: flex-end;
      }

      &-warning {
        display: flex;
        align-items: center;
        width: 100%;
        color: var(--am-c-warning);
        background-color: var(--am-c-desce-warn-op10);
        font-size: 12px;
        line-height: 18px;
        gap: 0 8px;
        padding: 4px 8px;
        border-radius: 6px;

        .am-icon-triangle-info {
          font-size: 24px;
          flex: 0 0 24px;
        }
      }
    }

    .am-quill-wrapper {
      display: flex;
      flex-direction: column;
    }

    .ql-toolbar {
      padding: 8px;
      border: 1px solid var(--am-c-inp-border);
      background-color: var(--am-c-inp-bgr);
      border-radius: 6px 6px 0 0;
      color: var(--am-c-inp-text);
    }

    .ql-show {
      .ql-toolbar,
      &.ql-toolbar {
        button {
          &:hover,
          &:focus,
          &.ql-active {
            .ql-stroke,
            .ql-stroke-miter {
              stroke: var(--am-c-primary);
            }
          }
        }

        .ql-picker-label,
        .ql-picker-item {
          &:hover,
          &.ql-active,
          &.ql-selected {
            .ql-stroke,
            .ql-stroke-miter {
              stroke: var(--am-c-primary);
            }
          }
        }
      }

      .ql-stroke {
        stroke: var(--am-c-inp-text);
      }
    }

    .ql-snow {
      .ql-toolbar,
      &.ql-toolbar {
        button {
          &:hover,
          &:focus,
          &.ql-active {
            color: var(--am-c-primary);
          }
        }

        .ql-picker-label,
        .ql-picker-item {
          &:hover,
          &.ql-active,
          &.ql-selected {
            color: var(--am-c-primary);
          }
        }
      }

      .ql-picker {
        color: var(--am-c-inp-text);
      }
    }

    .ql-editor {
      padding: 8px 12px;
      border: 1px solid var(--am-c-inp-border);
      border-top: none;
      background-color: var(--am-c-inp-bgr);
      border-radius: 0 0 6px 6px;

      * {
        color: var(--am-c-inp-text);
      }
      @include quill-styles;
    }
  }
}

// public
.amelia-v2-booking #amelia-container {
  @include am-desc-editor-block;
}
</style>