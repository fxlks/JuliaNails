<template>
  <div
    class="am-cappf"
    :style="cssVars"
  >
    <div
      class="am-cappf__options"
      :class="{'am-only-one': display === 'second' || !amSettings.roles.allowCustomerDeleteProfile}"
    >
      <div v-if="display === 'first' && amSettings.roles.allowCustomerDeleteProfile">
        <AmButton
          category="danger"
          :size="props.parentWidth <= 360 ? 'small' : 'default'"
          :type="props.deleteFooterType"
          class="am-cappf__delete"
          :disabled="loading"
          @click="deleteProfileDialog = true"
        >
          {{displayLabels('delete_profile')}}
        </AmButton>
      </div>
      <div v-if="display === 'first' || display === 'third'">
        <AmButton
          :type="props.saveFooterButton"
          :size="props.parentWidth <= 360 ? 'small' : 'default'"
          :disabled="loading"
          @click="display === 'first' ? saveProfileChanges() : saveCustomerCustomFields()"
        >
          {{displayLabels('save_changes')}}
        </AmButton>
      </div>
      <div v-if="display === 'second'">
        <AmButton
          :type="props.passFooterButton"
          :size="props.parentWidth <= 360 ? 'small' : 'default'"
          :disabled="loading"
          @click="changeProfilePassword()"
        >
          {{displayLabels('change_password')}}
        </AmButton>
      </div>
    </div>

  </div>
</template>

<script setup>
// * _components
import AmButton from "../../../../_components/button/AmButton.vue";

// * Import from Vue
import {
  inject,
  computed,
  ref
} from "vue";

// * Composables
import { useColorTransparency } from "../../../../../assets/js/common/colorManipulation";

// * Components properties
let props = defineProps({
  parentWidth: {
    type: Number,
    default: 1200
  },
  display: {
    type: String,
    default: 'first'
  },
  loading: {
    type: Boolean,
    default: false
  },
  customizedLabels: {
    type: Object,
    default: () => {
      return {}
    }
  },
  deleteFooterType: {
    type: String,
    default: 'plain'
  },
  saveFooterButton: {
    type: String,
    default: 'filled'
  },
  passFooterButton: {
    type: String,
    default: 'filled'
  }
})

// * Labels
const amLabels = inject('labels')
function displayLabels (label) {
  return Object.keys(props.customizedLabels).length && props.customizedLabels[label] ? props.customizedLabels[label] : amLabels[label]
}

// * Settings
const amSettings = inject('settings')

// * Injected Functions
let deleteProfileDialog = inject('deleteProfileDialog', ref(false))
let saveProfileChanges = inject('saveProfileChanges', () => {})
let saveCustomerCustomFields = inject('saveCustomerCustomFields', () => {})
let changeProfilePassword = inject('changeProfilePassword', () => {})

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    // am - amelia
    // cappf - cabinet panel profile footer
    '--am-c-cappf-bgr': amColors.value.colorMainBgr,
    '--am-c-cappf-text': amColors.value.colorMainText,
    '--am-c-cappf-text-op15': useColorTransparency(amColors.value.colorMainText, 0.15),
  }
})
</script>

<script>
export default {
  name: "MainProfileFooter"
}
</script>


<style lang="scss">

@mixin profile-footer-block {
  // am - amelia
  // cappf - cabinet panel profile footer
  .am-cappf {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 12px;
    background-color: var(--am-c-cappf-bgr);
    box-shadow: 0 -2px 3px var(--am-c-cappf-text-op15);

    &__options {
      display: flex;
      justify-content: space-between;

      &.am-only-one {
        justify-content: flex-end;
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include profile-footer-block;
}

#amelia-app-backend-new #amelia-container {
  @include profile-footer-block;
}
</style>
