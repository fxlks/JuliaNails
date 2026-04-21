<template>
  <div
    class="am-gc"
    :style="cssVars"
  >
    <div
      v-if="gallery.length > 1"
      class="am-gc__arrows"
    >
      <span
        class="am-icon-arrow-left"
        @click="() => activeImage = activeImage <= 0 ? gallery.length - 1 : activeImage - 1"
      ></span>
      <span
        class="am-icon-arrow-right"
        @click="() => activeImage = gallery.length - 1 === activeImage ? 0 : activeImage + 1"
      ></span>
    </div>
    <div
      v-for="(img, index) in gallery"
      :key="index"
      class="am-gc__display"
      :style="{display: index === activeImage ? 'flex': 'none', backgroundImage: `url(${img.pictureFullPath})`}"
      @click="() => activeImage = gallery.length - 1 === activeImage ? 0 : activeImage + 1"
    ></div>
    <div class="am-gc__bullets">
      <span
        v-for="(img, index) in gallery"
        :key="index"
        class="am-gc__bullets-item"
        :class="{'am-active': index === activeImage}"
        @click="() => activeImage = index"
      >
      </span>
    </div>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  inject,
  computed
} from "vue";

// * Comosables
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation.js'

defineProps({
  gallery: {
    type: Array,
    required: true
  }
})

let activeImage = ref(0)

// * Colors
let amColors = inject('amColors')

// * Css Vars
let cssVars = computed(() => {
  return {
    '--am-c-gc-bgr': amColors.value.colorMainBgr,
    '--am-c-gc-bgr-op70': useColorTransparency(amColors.value.colorMainBgr, 0.7),
    '--am-c-gc-text': amColors.value.colorMainText,
    '--am-c-gc-text-op20': useColorTransparency(amColors.value.colorMainText, 0.2),
    '--am-c-gc-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
  }
})
</script>

<script>
export default {
  name: "GalleryCarousel"
}
</script>

<style lang="scss">
@mixin gallery-carousel {
  // am - amelia
  // gc - gallery carousel
  .am-gc {
    position: relative;

    &__arrows {
      display: flex;
      justify-content: space-between;
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
      z-index: 100;

      span {
        position: absolute;
        width: 60px;
        height: 100%;
        font-size: 38px;
        cursor: pointer;

        &[class*="am-icon"] {
          flex: 0 0 auto;
          font-size: 28px;
        }

        &::before {
          color: var(--am-c-gc-text);
          background: var(--am-c-gc-bgr);
          border: 1px solid var(--am-c-gc-text-op20);
          box-shadow: 0px 1px 3px var(--am-c-gc-text-op10);
          border-radius: 6px;
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
        }

        &:first-child {
          left: 0;
        }

        &:last-child {
          right: 0;
        }
      }
    }

    &__display {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 300px;
      background-size: auto 100%;
      background-repeat: no-repeat;
      background-position: center;
      border-radius: 6px;
    }

    &__bullets {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      bottom: 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      z-index: 100;

      &-item {
        display: inline-flex;
        width: 8px;
        height: 8px;
        background-color: var(--am-c-gc-bgr-op70);
        border-radius: 50%;
        cursor: pointer;
        margin: 4px;

        &.am-active {
          width: 16px;
          height: 16px;
          background-color: var(--am-c-gc-bgr);
        }
      }
    }
  }
}

// Public
.amelia-v2-booking #amelia-container {
  @include gallery-carousel;
}

// Admin
#amelia-app-backend-new {
  @include gallery-carousel;
}
</style>