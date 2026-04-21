<template>
  <div
    class="am-button-group"
    :class="{'am-rtl': isRtl}"
  >
    <slot></slot>
  </div>
</template>

<script setup>
// * import from Vue
import {
  computed,
} from "vue"

let isRtl = computed(() => {
  if (document) {
    return document.documentElement.dir === 'rtl'
  }

  return false
})
</script>

<script>
export default {
  name: "AmButtonGroup"
}
</script>

<style lang="scss">
@mixin am-button-group-block {
  .am-button-group {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;

    &.am-rtl {
      flex-direction: row-reverse;
    }

    & > .am-button {
      box-shadow: none;

      &:first-child {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
      }

      &:last-child {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0
      }

      &:not(:last-child) {
        margin-right: -1px;
      }

      &:first-child:last-child {
        border-radius: var(--am-rad-inp);
      }

      &:not(:first-child):not(:last-child) {
        border-radius: 0;
      }

      // Type - filled / plain / text
      &--filled {
        &:not(.is-disabled) {
          &:focus:not(:active) {
            border-color: var(--am-c-btn-first-darken30);
            box-shadow: none;
          }
        }
      }

      &--plain {
        &:not(.is-disabled) {
          &:focus:not(:active) {
            border-color: var(--am-c-btn-first);
            box-shadow: none;
          }
        }
      }

      &--text {
        &:not(.is-disabled) {
          &:focus:not(:active) {
            box-shadow: none;
          }
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-button-group-block;
}

#amelia-app-backend-new {
  @include am-button-group-block;
}
</style>