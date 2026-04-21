<template>
  <div
    class="am-cc__employee"
    :class="responsiveClass"
  >
    <template v-if="isObjType">
      <div class="am-cc__employee-heading">
        <span class="am-cc__employee-img" :style="imgStyles(props.data)">
          <template v-if="!props.data.pictureThumbPath">
            {{ imagePlaceholder(props.data) }}
          </template>
        </span>
        <div class="am-cc__employee-heading__inner">
          <span class="am-cc__employee-name">
            {{ `${props.data.firstName} ${props.data.lastName}` }}
          </span>
          <span v-if="props.data.badge" class="am-cc__employee-badge" :style="{background: props.data.badge.color}">
            {{ props.data.badge.content }}
          </span>
        </div>
      </div>
      <div
        v-if="props.data.description"
        class="am-cc__employee-desc ql-description"
        v-html="props.data.description"
      ></div>
    </template>

    <div v-else class="am-cc__employee-wrapper">
      <template
        v-for="empl in props.data"
        :key="empl.id"
      >
        <div class="am-cc__employee-heading">
          <span class="am-cc__employee-img" :style="imgStyles(empl)">
            <template v-if="!empl.pictureThumbPath">
              {{ imagePlaceholder(empl) }}
            </template>
          </span>
          <div class="am-cc__employee-heading__inner">
            <span class="am-cc__employee-name">
              {{ `${empl.firstName} ${empl.lastName}` }}
            </span>
            <span v-if="empl.badge" class="am-cc__employee-badge" :style="{background: empl.badge.color}">
              {{ empl.badge.content }}
            </span>
          </div>
        </div>
        <div
          v-if="descVisibility(empl.description)"
          class="am-cc__employee-desc ql-description"
          v-html="empl.description"
        ></div>
      </template>
    </div>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  computed,
  inject
} from "vue";

// * Composables
import { useResponsiveClass } from "../../../../../../../../assets/js/common/responsive.js";

let props = defineProps({
  data: {
    type: [Object, Array]
  }
})

let isObjType = computed(() => !Array.isArray(props.data))

let cWidth = inject('containerWidth')

let responsiveClass = computed(() => {
  return useResponsiveClass(cWidth.value)
})

function imagePlaceholder(empl) {
  let fName = empl.firstName.charAt(0).toUpperCase()
  let lName = empl.lastName.charAt(0).toUpperCase()
  return `${fName}${lName}`
}

function imgStyles(empl) {
  if (empl.pictureThumbPath) {
    return {backgroundImage: `url(${empl.pictureThumbPath})`}
  }

  return {}
}

function descVisibility(desc) {
  if (desc && desc.includes('<!-- Content -->')) {
    return desc.slice(16).length
  }

  return desc ? desc.length : false
}

</script>

<script>
export default {
  name: "TemplateEmployee"
}
</script>

<style lang="scss">
@import '../../../../../../../../../src/assets/scss/common/quill/_quill-mixin.scss';

@mixin popover-template-employee {
  .am-cc__employee {
    width: 340px;

    &.am-w-420 {
      width: 100%;
    }

    &-wrapper {
      max-height: 350px;
      overflow-x: hidden;

      .am-cc__employee-desc {
        margin-bottom: 16px;
      }

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

    &-item {
      width: 100%;
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      margin: 0 0 16px;

      &:last-child {
        margin: 0;
      }
    }

    &-heading {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      margin: 0 0 16px;

      &__inner {
        display: flex;
        flex-wrap: wrap;
      }
    }

    &-img {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background-color: var(--am-c-cc-text);
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      margin-right: 8px;
    }

    &-name {
      display: inline-flex;
      flex-wrap: wrap;
      word-break: break-word;
      font-size: 15px;
      font-weight: 500;
      line-height: 1.6;
      color: var(--am-c-cc-text-op90);
      margin: 0 6px 0 0;
    }

    &-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      border-radius: 4px;
      padding: 0 4px;
      font-size: 13px;
      line-height: 19px;
    }

    &-desc {
      font-size: 13px;
      line-height: 2;
      word-break: break-word;
      white-space: break-spaces;
      color: var(--am-c-cc-text-op80);
      margin: 0 0 16px;

      * {
        font-size: 13px;
        line-height: 2;
        word-break: break-word;
        white-space: break-spaces;
        color: var(--am-c-fcis-text-op80);
      }

      a {
        color: var(--am-c-fcis-primary);
      }

      &.ql-description {
        @include quill-styles;
      }
    }
  }
}

.el-popover {
  @include popover-template-employee;
}
</style>
