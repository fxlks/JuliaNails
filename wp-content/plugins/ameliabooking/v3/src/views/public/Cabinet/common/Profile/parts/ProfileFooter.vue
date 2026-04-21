<template>
  <div
    class="am-caepf"
    :class="{ 'am-single-btn': props.tabActive !== 'details' || props.passwordActive }"
    :style="cssVars"
  >
    <AmButton
      v-if="props.tabActive === 'details' && !props.passwordActive"
      size="medium"
      category="secondary"
      type="plain"
      @click="emits('changePassword')"
    >
      {{ amLabels.change_password }}
    </AmButton>
    <AmButton
      size="medium"
      @click="saveData"
    >
      {{ props.tabActive !== 'password' ? amLabels.save : amLabels.change_password }}
    </AmButton>
  </div>
</template>

<script setup>
// * Import from Vue
import { inject, computed } from 'vue'

// * Components
import AmButton from '../../../../../_components/button/AmButton.vue'

// * Composables
import { useColorTransparency } from '../../../../../../assets/js/common/colorManipulation'

// * Props
const props = defineProps({
  tabActive: {
    type: String,
    required: true,
  },
  passwordActive: {
    type: Boolean,
    default: false
  }
})

// * Emits
const emits = defineEmits(['changePassword', 'savePassword', 'saveChanges'])

// * Labels
let amLabels = inject('amLabels')

// * Save Changes
function saveData() {
  if (props.tabActive === 'password') {
    emits('savePassword')
  } else {
    emits('saveChanges')
  }
}

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-caepf-text-op15': useColorTransparency(amColors.value.colorMainText, 0.15),
  }
})
</script>

<style lang="scss">
@mixin profile-footer-block {
  // am - amelia
  // caepf - Cabinet Employee Profile Footer
  .am-caepf {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 13px 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0px 0px 2px 3px var(--am-c-caepf-text-op15);

    &.am-single-btn {
      justify-content: flex-end;
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include profile-footer-block;
}
</style>
