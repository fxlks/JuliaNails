<template>
  <div
    class="am-cc__cf"
    :class="responsiveClass"
  >
    <template
      v-for="(item, index) in props.data"
      :key="index"
    >
      <div class="am-cc__cf-item">
        <span class="am-cc__cf-name" v-html="item.label"></span>
        <div v-if="'value' in item" class="am-cc__cf-value">
          <span class="am-cc__cf-value__inner">
            {{ customFieldValueDisplay(item.value) }}
          </span>
        </div>
      </div>
    </template>

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
    type: [Array, Object, String]
  }
})

let cWidth = inject('containerWidth')

let responsiveClass = computed(() => {
  return useResponsiveClass(cWidth.value)
})

function customFieldValueDisplay(value) {
  if (!value) return '/';

  if (Array.isArray(value)) {
    if (value.length === 0) return '/';

    return value.map(item => {
      if (typeof item === 'object' && item !== null && !Array.isArray(item)) {
        return item.name || '/';
      }
      return item || '/';
    }).join(', ');
  }

  return value;
}
</script>

<script>
export default {
  name: "TemplateCustomFields"
}
</script>

<style lang="scss">
@mixin popover-template-cf {
  .am-cc__cf {
    width: 340px;

    &.am-w-420 {
      width: 100%;
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

    &-name {
      width: 100%;
      display: flex;
      flex-wrap: wrap;
      word-break: break-word;
      white-space: break-spaces;
      font-size: 15px;
      font-weight: 500;
      line-height: 1.6;
      color: var(--am-c-cc-text-op90);
      margin: 0 0 4px;
    }

    &-value {
      width: 100%;
      display: flex;
      font-size: 14px;
      font-weight: 400;
      line-height: 1.6;
      color: var(--am-c-cc-text);
      margin: 0;

      &__inner {
        word-break: break-word;
        white-space: break-spaces;
      }
    }
  }
}

.el-popover {
  @include popover-template-cf;
}
</style>
