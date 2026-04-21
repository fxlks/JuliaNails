<template>
  <el-button
    class="am-adv-button"
    :class="[`am-adv-button__${btnSize}`, isIconClass, customClass]"
    :size="size"
    :type="type"
    :plain="plain"
    :round="round"
    :circle="circle"
    :loading="loading"
    :disabled="disabled"
    :autofocus="autofocus"
    :native-type="nativeType"
    :auto-insert-space="autoInsertSpace"
    @click="handleClick"
  >
    <slot v-if="(prefixIcon || Object.keys(prefixIcon).length) && !iconOnly" name="prefixIcon">
      <component :is="prefixIcon" v-if="typeof prefixIcon === 'object'"></component>
      <span v-if="typeof prefixIcon === 'string'" :class="`am-icon-${prefixIcon}`"></span>
    </slot>
    <slot v-if="(icon || Object.keys(icon).length) && iconOnly" name="icon">
      <component :is="icon" v-if="typeof icon === 'object'"></component>
      <span v-if="typeof icon === 'string'" :class="`am-icon-${icon}`"></span>
    </slot>
    <slot v-if="loadingIcon || Object.keys(loadingIcon).length" name="icon">
      <component :is="loadingIcon" v-if="typeof loadingIcon === 'object'"></component>
      <span v-if="typeof loadingIcon === 'string'" :class="`am-icon-${loadingIcon}`"></span>
    </slot>
    <slot v-if="$slots.default && !iconOnly"></slot>
    <slot v-if="(suffixIcon || Object.keys(suffixIcon).length) && !iconOnly" name="suffixIcon">
      <component :is="suffixIcon" v-if="typeof suffixIcon === 'object'"></component>
      <span v-if="typeof suffixIcon === 'string'" :class="`am-icon-${suffixIcon}`"></span>
    </slot>
  </el-button>
</template>

<script setup>
import {computed} from "vue";

/**
 * Component Props
 */
const props = defineProps({
  customClass: {
    type: String,
    default: ''
  },
  iconOnly: {
    type: Boolean,
    default: false
  },
  // default / medium / small / mini / micro
  btnSize: {
    type: String,
    default: 'default',
    validator(value) {
      return ['default', 'medium', 'small', 'mini', 'micro'].includes(value)
    }
  },
  size: {
    // large / default / medium /small
    type: String,
    default: 'default'
  },
  type: {
    // primary / success / warning / danger / info / text
    type: String,
    default: ''
  },
  nativeType: {
    // button / submit / reset
    type: String,
    default: 'button'
  },
  plain: {
    type: Boolean,
    default: false
  },
  round: {
    type: Boolean,
    default: false
  },
  circle: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  autofocus: {
    type: Boolean,
    default: false
  },
  autoInsertSpace: {
    type: Boolean
  },
  icon: {
    type: [String, Object, Function],
    default: ''
  },
  loadingIcon: {
    type: [String, Object, Function],
    default: ''
  },
  prefixIcon: {
    type: [String, Object, Function],
    default: ''
  },
  suffixIcon: {
    type: [String, Object, Function],
    default: ''
  }
})

const isIconClass = computed(() => props.iconOnly ? `am-adv-button__${props.btnSize}-icon` : '')

/**
 * Component Emits
 * */
const emits = defineEmits(['click'])

/**
 * Component Event Handlers
 */
const handleClick = (event) => {
  emits('click', event)
}
</script>

<style lang="scss">
$button: 'am-adv-button__';

// Colors
$defaultButtonBackgroundColor: #FFFFFF;
$defaultButtonTextColor: #1A2C37;
$defaultButtonBorderColor: #E0E2E4;


#amelia-container {
  .am-adv-button {

    // Button sizes
    // Default
    &__default {}
    // Medium
    &__medium {}
    // Mini
    &__mini {}
    // Micro
    &__micro {
      height: 24px;
      font-size: 16px;
      transition: all .3s ease-in-out;

      // Only contains icon
      &-icon {
        width: 24px;
        padding: 4px;

        &.is-loading {
          width: 48px;
        }
      }
    }
  }
}
</style>