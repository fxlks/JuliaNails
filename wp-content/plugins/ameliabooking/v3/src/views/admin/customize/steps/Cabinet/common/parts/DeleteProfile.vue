<template>
  <AmSlidePopup
    :visibility="props.visibility"
    custom-class="am-csd am-csd__cancel"
    :style="cssDialogVars"
    position="center"
  >
    <div class="am-csd__inner">
      <div class="am-csd__header">
        <div class="am-csd__header-text">
          {{ displayLabels('delete_profile') }}
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
          {{ displayLabels('delete_profile_description') }}
        </p>
      </div>
    </div>
    <template #footer>
      <div class="am-csd__footer">
        <AmButton
          category="secondary"
          :size="cWidth <= 360 ? 'small' : 'default'"
          :type="props.customizedOptions.closeBtn.buttonType"
          @click="() => { emits('close') }"
        >
          {{ displayLabels('close') }}
        </AmButton>
        <AmButton
          category="danger"
          :size="cWidth <= 360 ? 'small' : 'default'"
          :type="props.customizedOptions.confirmBtn.buttonType"
          :loading-icon="'loading'"
          @click="() => { emits('deleteProfile') }"
        >
          {{ displayLabels('delete') }}
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
import AmSlidePopup from "../../../../../../_components/slide-popup/AmSlidePopup.vue";
import IconComponent from "../../../../../../_components/icons/IconComponent.vue";
import AmButton from "../../../../../../_components/button/AmButton.vue";

// * Composables
import { useColorTransparency } from "../../../../../../../assets/js/common/colorManipulation";

// * Component Props
let props = defineProps({
  visibility: {
    type: Boolean,
    required: true
  },
  customizedLabels: {
    type: Object,
    default: () => {
      return {}
    }
  },
  customizedOptions: {
    type: Object,
    default: () => {
      return {}
    }
  }
})

let cWidth = inject('containerWidth')

// * Component Emits
const emits = defineEmits(['close', 'deleteProfile'])

/*************
 * Customize *
 *************/
// * Labels
const amLabels = inject('labels')
function displayLabels (label) {
  return Object.keys(props.customizedLabels).length && props.customizedLabels[label] ? props.customizedLabels[label] : amLabels[label]
}

// * Fonts
let amFonts = inject('amFonts')

// * Colors block
let amColors = inject('amColors')

let cssDialogVars = computed(() => {
  return {
    '--am-c-csd-text': amColors.value.colorMainText,
    '--am-c-csd-bgr': amColors.value.colorMainBgr,
    '--am-c-csd-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
    '--am-font-family': amFonts.value.fontFamily,
  }
})
</script>

<script>
export default {
  name: 'DeleteProfile'
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
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      padding: 16px 24px;
      gap: 16px;
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-slide-dialog;
}

#amelia-app-backend-new #amelia-container {
  @include am-cabinet-slide-dialog;
}
</style>
