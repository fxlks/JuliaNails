<template>
  <div
    class="am-cat__wrapper"
    :class="wrapperClass"
    role="main"
  >
    <div
      ref="catHeader"
      class="am-cat__header"
    >
      <slot name="header"></slot>
    </div>
    <div
      ref="catContainer"
      class="am-cat__main"
    >
      <slot name="side"></slot>
      <div
        ref="catForm"
        class="am-cat__form"
        :class="formClass"
        :style="cssVars"
      >
        <div
          ref="catHeading"
          class="am-cat__heading"
          :class="headingClass"
        >
          <slot name="heading"></slot>
        </div>

        <div
          ref="catContent"
          class="am-cat__content"
          :class="contentClass"
        >
          <slot name="content"></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  inject,
  ref,
  nextTick,
  onMounted,
  computed
} from 'vue'
import { useColorTransparency } from '../../../../assets/js/common/colorManipulation'

defineProps({
  wrapperClass: {
    type: String,
    default: ''
  },
  formClass: {
    type: String,
    default: ''
  },
  headingClass: {
    type: String,
    default: ''
  },
  contentClass: {
    type: String,
    default: ''
  }
})

let catHeader = ref(null)
let catHeaderWidth = ref(0);

let catContainer = ref(null)
let catContainerWidth = ref(0)

let catForm = ref(null)
let catFormWidth = ref(0)

let catHeading = ref(null)
// let catHeadingWidth = ref(0)

let catContent = ref(null)

let scrollBlockHeight = ref(0)

// * window resize listener
window.addEventListener('resize', resize);

// * resize function
function resize() {
  nextTick(() => {
    if (catHeader.value) {
      catHeaderWidth.value = catHeader.value.offsetWidth
    }

    if (catContainer.value) {
      catContainerWidth.value = catContainer.value.offsetWidth
    }

    if (catForm.value) {
      catFormWidth.value = catForm.value.offsetWidth
    }
  })
}

onMounted(() => {
  nextTick(() => {
    if (catForm.value && catHeading.value) {
      let height = catForm.value.offsetHeight - catHeading.value.offsetHeight - 2
      scrollBlockHeight.value = height <= 656 ? 656 : height
    }

    resize()
  })
})

defineExpose({
  catHeaderWidth,
  catFormWidth,
  catContainerWidth
})

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {

  return {
    '--am-h-cat-content': `${scrollBlockHeight.value}px`,
    '--am-c-scroll-op30': useColorTransparency(amColors.value.colorPrimary, 0.3),
    '--am-c-scroll-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),
  }
})
</script>

<script>
export default {
  name: "MainContent"
}
</script>

<style lang="scss">
@mixin catalog-content {
  .am-cat {
    &__wrapper {
      --am-c-cat-main-bgr: var(--am-c-main-bgr);
      width: 100%;
      background-color: var(--am-c-main-bgr);
    }

    &__main {
      max-width: 100%;
      width: 100%;
      display: flex;
      flex-direction: row;
    }

    &__form {
      width: 100%;
    }

    &__content {
      max-height: var(--am-h-cat-content);
      overflow-x: hidden;

      // Main Scroll styles
      &::-webkit-scrollbar {
        width: 6px;
      }

      &::-webkit-scrollbar-thumb {
        border-radius: 6px;
        background: var(--am-c-scroll-op30);
      }

      &::-webkit-scrollbar-track {
        border-radius: 6px;
        background: var(--am-c-scroll-op10);
      }
    }

  }
}

// Public
.amelia-v2-booking #amelia-container {
  @include catalog-content;
}

// Admin
#amelia-app-backend-new #amelia-container {
  @include catalog-content;
}
</style>