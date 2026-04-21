<template>
  <AmSlidePopup
    :visibility="props.visibility"
    custom-class="am-csd am-csd__cancel"
    :style="cssDialogVars"
    position="center"
  >
    <div class="am-csd__inner">
      <template v-if="!loading">
        <div class="am-csd__header">
          <div class="am-csd__header-text">
            {{ props.title }}
          </div>
          <div
            class="am-csd__header-btn"
            @click="() => { emits('close') }"
          >
            <IconComponent icon="close"></IconComponent>
          </div>
        </div>
        <div class="am-csd__content">
          <p>
            {{ props.description }}
          </p>
        </div>
      </template>
      <el-skeleton
        v-else
        animated
        class="am-ccpss"
      >
        <template #template>
          <div class="am-ccpss__heading">
            <el-skeleton-item variant="rect" />
          </div>

          <div v-for="item in new Array(props.count)" :key="item" class="am-ccpss__text">
            <el-skeleton-item variant="rect" />
          </div>
        </template>
      </el-skeleton>
    </div>
    <template #footer>
      <div class="am-csd__footer">
        <AmButton
          category="secondary"
          :size="cWidth <= 360 ? 'small' : 'default'"
          :type="customizedOptions.cancelBtn.buttonType"
          :disabled="loading"
          @click="() => { emits('decline') }"
        >
          {{ props.closeBtnText }}
        </AmButton>
        <AmButton
          :category="'danger'"
          :size="cWidth <= 360 ? 'small' : 'default'"
          :type="customizedOptions.confirmBtn.buttonType"
          :disabled="loading"
          @click="() => { emits('confirm') }"
        >
          {{ props.confirmBtnText }}
        </AmButton>
      </div>
    </template>
  </AmSlidePopup>
</template>

<script setup>
// * import from Vue
import {
  computed,
  inject,
} from "vue";

// * _components
import AmSlidePopup from "../../../../_components/slide-popup/AmSlidePopup.vue";
import IconComponent from "../../../../_components/icons/IconComponent.vue";
import AmButton from "../../../../_components/button/AmButton.vue";

// * Component emits
const emits = defineEmits(['close', 'confirm', 'decline'])

/********
 * Form *
 ********/
let props = defineProps({
  visibility: {
    type: Boolean,
    required: true,
  },
  title: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  closeBtnText: {
    type: String,
    required: true
  },
  confirmBtnText: {
    type: String,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  count: {
    type: Number,
    default: 1
  },
  customizedOptions: {
    type: Object,
    required: true
  }
})

let cWidth = inject('containerWidth')

/*************
 * Customize *
 *************/
// * Fonts
let amFonts = inject('amFonts')

// * Colors block
let amColors = inject('amColors')

let cssDialogVars = computed(() => {
  return {
    '--am-c-csd-text': amColors.value.colorMainText,
    '--am-c-csd-bgr': amColors.value.colorMainBgr,
    '--am-font-family': amFonts.value.fontFamily,
  }
})

</script>

<script>
export default {
  name: 'CancelBooking'
}
</script>

<style lang="scss">
@mixin am-cabinet-slide-dialog {
  // csd - cabinet slide dialog
  .am-csd {
    border-radius: 8px;
    padding: 0;

    $mainClass: '.am-csd';

    * {
      font-family: var(--am-font-family);
      box-sizing: border-box;
    }

    &__cancel {
      &.am-slide-popup__block-inner.am-position-center {
        max-width: 480px;
        width: 100%;
        padding: 0;
        border-radius: 8px;
        background-color: var(--am-c-csd-bgr);
      }

      #{$mainClass} {
        &__content {
          p {
            font-size: 15px;
            font-weight: 400;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: var(--am-c-csd-text);
          }
        }
      }
    }

    &__inner {}

    &__header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 20px 24px 24px;

      &-text {
        font-size: 18px;
        font-weight: 500;
        color: var(--am-c-csd-text);
      }

      &-btn {
        color: var(--am-c-csd-text);
        cursor: pointer;
      }
    }

    &__content {
      padding: 0  24px;
    }

    &__footer {
      padding: 24px;
    }

    .am-ccpss {
      --el-skeleton-to-color: var(--am-c-skeleton-op60);
      --el-skeleton-color: var(--am-c-skeleton-op20);

      &__heading {
        display: flex;
        width: 50%;
        padding: 20px 24px 24px;
      }

      &__text {
        padding: 0 24px;
      }

      .el-skeleton__item {
        height: 25px;
      }

      .el-skeleton-item, .el-skeleton-item-wrapper-title {
        padding: 6px 0;
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-slide-dialog;
}
</style>
