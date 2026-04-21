<template>
  <el-popover
    v-if="stripeProvider && stripeProvider.id && stripeProvider.completed"
    ref="stripeConnectRef"
    :visible="stripeConnectPopVisible"
    :persistent="false"
    :show-arrow="false"
    :width="'auto'"
    :popper-class="'am-cc__popper'"
    :popper-style="cssVars"
    trigger="click"
  >
    <template #reference>
      <AmButton
        @click="clickItem"
      >
        {{ amLabels.stripe_connect }} {{ '(' + (stripeProvider.email ? stripeProvider.email : stripeProvider.id) + ')' }}
      </AmButton>
    </template>
    <div
      v-click-outside="close"
      class="am-cc__edit"
    >
      <div
        class="am-cc__edit-item"
        @click="stripePreview()"
      >
        <span class="am-cc__edit-text">
          {{ amLabels.stripe_preview }}
        </span>
      </div>

      <div
        class="am-cc__edit-item"
        @click="stripeDisconnect()"
      >
        <span class="am-cc__edit-text">
          {{ amLabels.disconnect }}
        </span>
      </div>
    </div>
  </el-popover>
  <el-popover
    v-else-if="stripeProvider && stripeProvider.id && !stripeProvider.completed"
    ref="stripeConnectRef"
    :visible="stripeConnectPopVisible"
    :persistent="false"
    :show-arrow="false"
    :width="'auto'"
    :popper-class="'am-cc__popper'"
    :popper-style="cssVars"
    trigger="click"
  >
    <template #reference>
      <AmButton
        @click="clickItem"
      >
        {{ amLabels.stripe_connect }} {{ '(' + (stripeProvider.email ? stripeProvider.email : stripeProvider.id) + ')' }}
      </AmButton>
    </template>
    <div
      v-click-outside="close"
      class="am-cc__edit"
    >
      <div
        class="am-cc__edit-item"
        @click="stripeConnect(stripeProvider.type)"
      >
        <span class="am-cc__edit-text">
          {{ amLabels.stripe_onboard }}
        </span>
      </div>

      <div
        class="am-cc__edit-item"
        @click="stripeDisconnect()"
      >
        <span class="am-cc__edit-text">
          {{ amLabels.disconnect }}
        </span>
      </div>
    </div>
  </el-popover>
  <el-popover
    v-else
    ref="stripeConnectRef"
    :visible="stripeConnectPopVisible"
    :persistent="false"
    :show-arrow="false"
    :width="'auto'"
    :popper-class="'am-cc__popper'"
    :popper-style="cssVars"
    trigger="click"
  >
    <template #reference>
      <AmButton
        @click="clickItem"
      >
        {{ amLabels.stripe_connect }}
      </AmButton>
    </template>
    <div
      v-click-outside="close"
      class="am-cc__edit"
    >
      <div
        class="am-cc__edit-item"
        @click="stripeConnect('standard')"
      >
        <span class="am-cc__edit-text">
          {{ amLabels.stripe_account_standard }}
        </span>
      </div>

      <div
        class="am-cc__edit-item"
        @click="stripeConnect('express')"
      >
        <span class="am-cc__edit-text">
          {{ amLabels.stripe_account_express }}
        </span>
      </div>
    </div>
  </el-popover>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  inject,
  computed,
} from "vue";

// * Import from Vuex
import { useStore } from "vuex";

// * Import from Libraries
import { ClickOutside as vClickOutside } from "element-plus";

// * Dedicated components
import AmButton from "../../../../../_components/button/AmButton.vue";


// * Composables
import { useColorTransparency } from "../../../../../../assets/js/common/colorManipulation";
import {
  useStripeConnect,
  useStripeDisconnect,
  useStripePreview,
} from "../../../../../../assets/js/common/integrationStripe";

// * Store
let store = useStore()

// * Labels
let amLabels = inject('amLabels')

let stripeProvider = computed(() => store.getters['auth/getStripeProvider'])

let stripeConnectPopVisible = ref(false)

function clickItem (e) {
  e.stopPropagation()

  stripeConnectPopVisible.value= !stripeConnectPopVisible.value
}

function stripeConnect (accountType) {
  useStripeConnect(store, accountType)

  close()
}

function stripePreview () {
  useStripePreview(store)

  close()
}

function stripeDisconnect () {
  useStripeDisconnect(store)

  close()
}

function close () {
  stripeConnectPopVisible.value= false
}

/*************
 * Customize *
 *************/
// * Fonts
let amFonts = inject('amFonts')

// * Colors block
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-cc-error': amColors.value.colorError,
    '--am-c-cc-error-op15': useColorTransparency(amColors.value.colorError, 0.15),
    '--am-c-cc-bgr': amColors.value.colorMainBgr,
    '--am-c-cc-text': amColors.value.colorMainText,
    '--am-c-cc-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
    '--am-c-cc-text-op15': useColorTransparency(amColors.value.colorMainText, 0.15),
    '--am-font-family': amFonts.value.fontFamily,
  }
})
</script>

<script>
export default {
  name: 'StripeConnect'
}
</script>

<style lang="scss">
@mixin am-stripe-connect {
  .am-cc {
    &__edit-btn {
      cursor: pointer;
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-stripe-connect;
}
</style>
