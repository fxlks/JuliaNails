<template>
  description
  <AmSlidePopup
    :visibility="props.visibility"
    custom-class="am-csd am-csd__cancel"
    :style="cssDialogVars"
    position="center"
  >
    <div class="am-csd__inner">
      <div class="am-csd__header">
        <div class="am-csd__header-text">
          {{ labelsDisplay(props.titleKey) }}
        </div>
        <div class="am-csd__header-btn">
          <IconComponent icon="close"></IconComponent>
        </div>
      </div>
      <div class="am-csd__content">
        <p>
          {{ labelsDisplay(props.contentKey) }}
        </p>
      </div>
    </div>
    <template #footer>
      <div class="am-csd__footer">
        <AmButton
          category="secondary"
          :size="cWidth <= 360 ? 'small' : 'default'"
          :type="
            amCustomize[pageRenderKey][stepRecognition].options.cancelBtn
              .buttonType
          "
        >
          {{ labelsDisplay('close') }}
        </AmButton>
        <AmButton
          category="danger"
          :size="cWidth <= 360 ? 'small' : 'default'"
          :type="
            amCustomize[pageRenderKey][stepRecognition].options.confirmBtn
              .buttonType
          "
        >
          {{ labelsDisplay('confirm') }}
        </AmButton>
      </div>
    </template>
  </AmSlidePopup>
</template>

<script setup>
// * import from Vue
import { computed, inject } from 'vue'

// * _components
import AmSlidePopup from '../../../../../../_components/slide-popup/AmSlidePopup.vue'
import IconComponent from '../../../../../../_components/icons/IconComponent.vue'
import AmButton from '../../../../../../_components/button/AmButton.vue'

// * Composables
import { useReactiveCustomize } from '../../../../../../../assets/js/admin/useReactiveCustomize.js'

/********
 * Form *
 ********/
let props = defineProps({
  visibility: {
    type: Boolean,
    required: true,
  },
  titleKey: {
    type: String,
    required: true,
  },
  contentKey: {
    type: String,
    required: true,
  },
})

let cWidth = inject('containerWidth')

/*************
 * Customize *
 *************/
// * Labels
let langKey = inject('langKey')
let amLabels = inject('labels')

let stepName = inject('stepName')
let subStepName = inject('subStepName')
let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

let stepRecognition = computed(() => {
  if (subStepName.value) return subStepName.value
  return stepName.value
})

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value][stepRecognition.value]
      .translations &&
      amCustomize.value[pageRenderKey.value][stepRecognition.value]
        .translations[label] &&
      amCustomize.value[pageRenderKey.value][stepRecognition.value]
        .translations[label][langKey.value]
      ? amCustomize.value[pageRenderKey.value][stepRecognition.value]
          .translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

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
  name: 'CancelBooking',
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

    &__inner {
    }

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
      padding: 0 24px;
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

      .el-skeleton-item,
      .el-skeleton-item-wrapper-title {
        padding: 6px 0;
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-slide-dialog;
}
</style>
