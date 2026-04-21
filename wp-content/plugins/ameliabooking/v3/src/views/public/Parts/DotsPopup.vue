<template>
  <el-popover
    ref="editRef"
    :visible="editPopVisible"
    :persistent="false"
    :show-arrow="false"
    :width="'auto'"
    :popper-class="'am-cc__popper'"
    :popper-style="cssVars"
    trigger="click"
  >
    <template #reference>
      <span
        class="am-cc__edit-btn am-icon-dots-vertical"
        :class="props.referenceClass"
        @click="clickItem"
      ></span>
    </template>
    <div
      v-click-outside="close"
      class="am-cc__edit"
    >
      <!-- Edit -->
      <div
        v-if="props.haveEdit"
        class="am-cc__edit-item"
        @click="editItem"
      >
        <span class="am-icon-edit"></span>
        <span class="am-cc__edit-text">
          {{ amLabels.edit }}
        </span>
      </div>
      <!-- /Edit -->

      <!-- View -->
      <div
        v-if="props.haveView"
        class="am-cc__edit-item"
        @click="editItem"
      >
        <span class="am-icon-search"></span>
        <span class="am-cc__edit-text">
          {{ amLabels.view }}
        </span>
      </div>
      <!-- /View -->

      <!-- Delete -->
      <div
        v-if="props.haveDelete"
        class="am-cc__edit-item am-delete"
        @click="removeItem"
      >
        <span class="am-icon-clearable"></span>
        <span class="am-cc__edit-text">
          {{ amLabels.delete }}
        </span>
      </div>
      <!-- /Delete -->

      <!-- Duplicate -->
      <div
        v-if="props.haveDuplicate"
        class="am-cc__edit-item am-clone"
        @click="duplicateItem"
      >
        <span class="am-icon-clearable"></span>
        <span class="am-cc__edit-text">
          {{ amLabels.duplicate }}
        </span>
      </div>
      <!-- /Duplicate -->
    </div>
  </el-popover>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  inject,
  computed,
} from "vue";

// * Dedicated components
import { ClickOutside as vClickOutside } from "element-plus";
import { useColorTransparency } from "../../../assets/js/common/colorManipulation";

// * Component properties
let props = defineProps({
  index: {
    type: Number,
    default: 0
  },
  referenceClass: {
    type: String,
    default: ''
  },
  haveDuplicate: {
    type: Boolean,
    default: false
  },
  haveDelete: {
    type: Boolean,
    default: true
  },
  haveEdit: {
    type: Boolean,
    default: true
  },
  haveView: {
    type: Boolean,
    default: false
  },
})

// * Components emits
let emits = defineEmits([
  'remove',
  'edit',
  'duplicate',
])

// * Labels
let amLabels = inject('amLabels')

let editPopVisible = ref(false)

function clickItem () {
  editPopVisible.value = true
}

function editItem () {
  emits('edit', props.index)

  close()
}

function removeItem () {
  emits('remove', props.index)

  close()
}

function duplicateItem () {
  emits('duplicate', props.index)

  close()
}

function close () {
  editPopVisible.value= false
}

/*************
 * Customize *
 *************/
// * Fonts
let amFonts = inject('amFonts')

// * Colors block
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-cc-error': amColors.value.colorError,
    '--am-c-cc-error-op15': useColorTransparency(amColors.value.colorError, 0.15),
    '--am-c-cc-bgr': amColors.value.colorMainBgr,
    '--am-c-cc-text': amColors.value.colorMainText,
    '--am-c-cc-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
    '--am-c-cc-text-op15': useColorTransparency(amColors.value.colorMainText, 0.15),
    '--am-font-family': amFonts.value.fontFamily,
  }
})
</script>

<script>
export default {
  name: 'DotsPopup'
}
</script>

<style lang="scss">
@mixin am-dots-popup {
  .am-cc {
    &__edit-btn {
      cursor: pointer;
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-dots-popup;
}
</style>
