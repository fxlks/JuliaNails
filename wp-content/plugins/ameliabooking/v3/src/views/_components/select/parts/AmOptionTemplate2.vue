<template>
  <span class="am-oit__wrapper">
    <span class="am-oit">
      <span class="am-oit__img">
        <span class="am-oit__img-placeholder" :style="{backgroundImage: `url(${prop.imageThumb})`}">
          <span v-if="!prop.imageThumb">
            {{ imagePlaceholder() }}
          </span>
        </span>
      </span>
      <span class="am-oit__content am-oit__content-short">
        <span class="am-oit__data">
          <span :class="`am-oit__data-label ${badge ? 'am-oit__data-label-wrap':''}`">
            <span class="am-oit__data-label-name">
              {{ label }}
            </span>
            <span v-if="badge" class="am-oit__data-label-badge" :style="{background: badge.color}">
              {{ badge.content }}
            </span>
          </span>
          <span v-if="!badge && useDescriptionVisibility(description)" class="am-oit__data-description" v-html="replaceNewLines(description)"></span>
        </span>
        <span style="display: flex;">
          <span v-if="prop.price" class="am-oit__price" :style="cssVarsPrice">
            {{ prop.price }}
          </span>
          <span v-if="useDescriptionVisibility(description)" class="am-oit__info-trigger" @click="dialogInfoVisible">
            <span class="am-icon-circle-info"></span>
          </span>
        </span>
      </span>
    </span>
    <AmDialog
      v-if="useDescriptionVisibility(description)"
      v-model="infoVisible"
      :custom-class="'am-option-template-dialog am-dialog ql-description'"
      :append-to-body="true"
      :custom-styles="cssVars"
      :destroy-on-close="true"
      :align-center="true"
      :modal-class="'am-dialog-employee-description'"
    >
      <template #title>
        <span class="am-dialog__title">
          {{ dialogTitle }}
        </span>
      </template>
      <div class="am-dialog__body">
        <div class="am-dialog__body-heading">
          <span class="am-oit__img">
            <span class="am-oit__img-placeholder" :style="{backgroundImage: `url(${prop.imageThumb})`}">
              <span v-if="!prop.imageThumb">
                {{ imagePlaceholder() }}
              </span>
            </span>
          </span>
          <div class="am-dialog__body-heading__text">
            <span class="am-dialog__body-heading__text-name">
              {{ label }}
            </span>
            <span v-if="badge" class="am-dialog__body-heading__text-badge" :style="{background: badge.color}">
              {{ badge.content }}
            </span>
          </div>
        </div>
        <div class="am-dialog__body-content" v-html="description">
        </div>
      </div>
      <template #footer>
        <AmButton :type="primFooterBtnType" category="primary"  @click="handleClick">
          {{ dialogButtonText }}
        </AmButton>
      </template>
    </AmDialog>
  </span>
</template>

<script setup>
// * Import components
import AmDialog from '../../dialog/AmDialog.vue'
import AmButton from '../../button/AmButton.vue'

// * Import from Vue
import {
  computed,
  inject,
  ref
} from 'vue'

// * Composables
import { useColorTransparency } from "../../../../assets/js/common/colorManipulation";
import { useDescriptionVisibility } from "../../../../assets/js/common/helper";

/**
 * Component Props
 */
const prop = defineProps({
  identifier: {
    type: [String, Number],
    required: true
  },
  imageThumb: {
    type: String,
    default: ''
  },
  label: {
    type: [String, Number],
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  price: {
    type: [String, Number],
    default: ''
  },
  dialogTitle: {
    type: String,
    default: ''
  },
  dialogButtonText: {
    type: String,
    default: ''
  },
  badge: {
    type: [String, Number, Object]
  }
})

/**
 * Component Emits
 * */
const emits = defineEmits(['click'])

// * Dialog Visibility
let infoVisible = ref(false)

/**
 * Component Methods
 */
const dialogInfoVisible = (e) => {
  e.stopPropagation()
  infoVisible.value = true
}

/**
 * Component Event Handlers
 */
const handleClick = () => {
  infoVisible.value = false
  emits('click', prop.identifier)
}

function replaceNewLines (description) {
  if (!description) return ''
  let text = description.replace(/<br\s*\/?>/gi, ' ')
  text = text.replace(/<\/p>/gi, ' ').replace(/<\/div>/gi, ' ').replace(/<\/li>/gi, ' ')
  text = text.replace(/<[^>]*>/g, '')
  text = text.replace(/&nbsp;/g, ' ')
  text = text.replace(/\s+/g, ' ').trim()
  return text
}

function imagePlaceholder () {
  if (prop.label) {
    let shortLabel = ''
    prop.label.split(' ').forEach(item => {
      shortLabel += item.charAt(0).toUpperCase()
    })

    return shortLabel
  }
}

let primFooterBtnType = inject('primDescBtnType', 'filled')


// * Global colors
let amColors = inject('amColors');
let cssVars = computed(() => {
  return {
    '--am-oit-c-main-bgr': amColors.value.colorMainBgr,
    '--am-oit-c-main-btn-color': amColors.value.colorBtnPrim,
    '--am-oit-c-main-btn-color-text': amColors.value.colorBtnPrimText,
    '--am-oit-c-main-heading-text': amColors.value.colorMainHeadingText,
    '--am-oit-c-main-heading-text-op90': useColorTransparency(amColors.value.colorMainHeadingText, 0.9),
    '--am-oit-c-main-text': amColors.value.colorMainText,
    '--am-c-option-img-text': amColors.value.colorMainBgr,
  }
})
let cssVarsPrice = computed(() => {
  return {
    '--am-c-option-selected': amColors.value.colorPrimary
  }
})

</script>

<script>
export default {
  inheritAttrs: false,
}
</script>

<style lang="scss">
// am - Amelia
// oit - option inner template
.am-select-popper {
  .am-oit {
    width: 100%;
    display: flex;
    align-items: center;

    * {
      font-family: var(--am-font-family);
    }

    &__wrapper {
      display: flex;
      width: 100%;
    }

    &__img {
      display: flex;
      flex-shrink: 0;
      width: 36px;
      height: 36px;
      margin-right: 8px;

      &-placeholder {
        position: relative;
        display: block;
        width: 100%;
        height: 100%;
        background-color: #00a32a;
        border-radius: 50%;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;

        span {
          display: block;
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          font-size: 11px;
          font-weight: 500;
          line-height: 1;
          color: var(--am-c-option-img-text);
        }

      }
    }

    &__content {
      display: flex;
      width: 100%;
      &-short {
        width: 88%;
      }
    }

    &__data {
      display: flex;
      flex-shrink: 1;
      min-width: 0;
      width: 100%;
      justify-content: space-between;
      flex-direction: column;

      &-label {
        font-size: 16px;
        font-weight: 500;
        line-height: 1.25;

        &-name {
          color: var(--am-c-option-text);
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
          margin-right: 4px;
        }

        &-badge {
          color: #FFF;
          border-radius: 4px;
          padding: 0 4px;
          font-size: 13px;
          line-height: 19px;
        }

        &-wrap {
          display: flex;
          flex-wrap: wrap;
        }
      }

      &-description {
        font-size: 12px;
        font-weight: 400;
        line-height: 1.5;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--am-c-option-text);

        & * {
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
          margin: 0;
          padding: 0;
          all: unset;
        }

        & br, img {
          display: none !important;
        }
      }
    }

    &__price {
      display: flex;
      flex-shrink: 0;
      align-self: center;
      justify-content: center;
      min-width: 70px;
      font-size: 16px;
      font-weight: 500;
      line-height: 1.25;
      color: var(--am-c-option-selected);
    }

    &__info {
      &-trigger {
        position: absolute;
        top: 6px;
        right: 6px;
        color: var(--am-c-option-text);
        .am-icon-circle-info {
          font-size: 16px;
        }
      }
    }
  }

  .am-select-option {
    &.is-selected {
      .am-oit {
        &__data {
          &-label {
            &-name {
              color: var(--am-c-primary);
            }
          }

          &-description {
            color: var(--am-c-primary);
          }
        }
      }
    }
  }
}

.am-dialog-employee-description {
  z-index: 9999999999 !important;
}

.am-option-template-dialog {
  box-shadow: 0 3px 15px rgba(0, 0, 0, 0.25);
  border-radius: 6px;
  background-color: var(--am-oit-c-main-bgr);

  font-family: var(--am-font-family);

  &.el-dialog {
    max-width: 520px;
  }

  .el-dialog {
    &__header {
      font-size: 16px;
      font-weight: 500;
      line-height: 1.5;
      color: #808A90;
      padding: 16px;
    }

    &__headerbtn {
      font-size: 10px;
    }

    &__body {
      padding: 0 16px 48px;
    }
  }

  .el-dialog__close {
    font-size: 18px;
    width: 24px;
    height: 24px;
    color: var(--am-oit-c-main-heading-text);
  }

  .am-dialog {
    &__title {
      color: var(--am-oit-c-main-heading-text-op90);
    }
    &__body {
      &-heading {
        display: flex;
        align-items: center;
        margin-bottom: 8px;

        &__avatar {
          display: flex;
          width: 54px;
          height: 54px;
          background-color: #00a32a;
          border-radius: 50%;
          background-size: cover;
          background-repeat: no-repeat;
          background-position: center;
          margin-right: 12px;
        }

        &__text {
          font-size: 18px;
          font-weight: 500;
          line-height: 1.555555;

          &-name {
            color: var(--am-oit-c-main-text);
            margin-right: 4px;
          }

          &-badge {
            color: #FFF;
            border-radius: 4px;
            padding: 0 4px;
          }
        }
      }

      &-content {
        font-size: 16px;
        font-weight: 400;
        line-height: 1.5;
        word-break: break-word;
        color: var(--am-oit-c-main-text);
        * {
          color: var(--am-oit-c-main-text);
        }
        & p > * {
          color: var(--am-oit-c-main-text);
        }
      }
    }
  }

  .el-dialog__footer {
    .el-button {
      background-color: var(--am-oit-c-main-btn-color);
      color: var(--am-oit-c-main-btn-color-text)
    }
  }
}
</style>
