<template>
  <div
    class="am-settings-card"
    :class="{ 'am-settings-card--draggable': props.draggable }"
    @click="(evt) => emits('click', evt)"
  >
    <div v-if="props.draggable" class="am-settings-card__drag-handle">
      <span class="am-icon-drag"></span>
    </div>
    <div class="am-settings-card__inner am-settings-card__header">
      <span v-if="$slots.header">
        <slot name="header"></slot>
      </span>
      <span v-if="props.header">
        {{ props.header }}
      </span>
    </div>
    <div class="am-settings-card__inner am-settings-card__content">
      <span v-if="!props.content">
        <slot name="default"></slot>
      </span>
      <span v-if="!$slots.default">
        {{ props.content }}
      </span>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  header: {
    type: String,
    default: ''
  },
  content: {
    type: String,
    default: ''
  },
  draggable: {
    type: Boolean,
    default: false
  }
})
const emits = defineEmits(['click'])
</script>

<script>
export default {
  name: "AmSettingsCard"
}
</script>

<style lang="scss">
@mixin am-settings-card-block {
  .am-settings-card {
    padding: 8px 12px;
    border-radius: 9px;
    border: 1px solid $shade-250;
    box-shadow: 0 1px 3px fade-out($shade-250, 0.3);
    font-family: var(--am-font-family);
    box-sizing: border-box;
    cursor: pointer;

    &--draggable {
      position: relative;
      padding-left: 32px;
    }

    * {
      font-family: var(--am-font-family);
      box-sizing: border-box;
    }

    &__drag-handle {
      position: absolute;
      top: 50%;
      left: 6px;
      transform: translateY(-50%);
      cursor: move;

      .am-icon-drag {
        font-size: 24px;
        color: $shade-900;
      }
    }

    &__inner {
      margin-bottom: 4px;

      &:last-child {
        margin-bottom: 0;
      }
    }

    &__header {
      font-size: 14px;
      font-weight: 500;
      font-style: normal;
      line-height: 1.42857;
      color: $shade-900;
    }

    &__content {
      font-size: 13px;
      font-weight: 400;
      font-style: normal;
      line-height: 1.3846;
      color: $shade-700;
    }
  }
}

// admin
#amelia-app-backend-new {
  @include am-settings-card-block;
}
</style>