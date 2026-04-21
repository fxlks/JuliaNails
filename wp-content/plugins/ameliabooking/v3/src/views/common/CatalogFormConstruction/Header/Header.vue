<template>
  <div class="am-cat__header-inner">
    <div class="am-cat__back-btn">
      <AmButton
        v-if="props.backBtnVisibility"
        :prefix="arrowLeft"
        category="secondary"
        :type="btnType"
        :size="btnSize"
        @click="goBack"
        @keydown.enter="goBack"
      >
        {{props.btnString}}
      </AmButton>
    </div>
    <div
      v-if="props.cartBtnVisibility"
      class="am-cat__cart-btn"
    >
      <span class="am-cat__cart-number">
        {{props.cartItemsNumber}}
      </span>
      <AmButton
        aria-label="Go to cart button"
        category="secondary"
        :type="btnType"
        :size="btnSize"
        :round="true"
        :icon-only="true"
        :icon="iconCart"
        @click="goToCart"
      >
      </AmButton>
    </div>
  </div>
</template>

<script setup>
import AmButton from '../../../_components/button/AmButton.vue'
import IconComponent from '../../../_components/icons/IconComponent.vue'

import {
  defineComponent
} from "vue"
import {useStore} from "vuex";
// * import composable
import {
  useRemoveLastCartItem,
} from '../../../../assets/js/public/cart'

let props = defineProps({
  btnString: {
    type: String,
    default: ''
  },
  btnType: {
    type: String,
    default: 'plain'
  },
  btnSize: {
    type: String,
    default: 'mini'
  },
  backBtnVisibility: {
    type: Boolean,
    default: true
  },
  cartBtnVisibility: {
    type: Boolean,
    default: false
  },
  cartItemsNumber: {
    type: Number,
    default: 0
  }
})

let arrowLeft = defineComponent({
  components: {IconComponent},
  template: `<IconComponent icon="arrow-left"></IconComponent>`
})

let iconCart = defineComponent({
  components: {IconComponent},
  template: `<IconComponent icon="cart"/>`
})

let emits = defineEmits(['goBack', 'goToCart'])

function goBack() {
  emits('goBack')
}

const store = useStore()

function goToCart() {
  useRemoveLastCartItem(store)

  emits('goToCart')
}
</script>

<script>
export default {
  name: "MainHeader"
}
</script>

<style lang="scss">
@mixin catalog-header {
  .am-cat {
    &__header {
      &-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
      }
    }

    &__back-btn {
      width: 100%;
    }

    &__cart {
      &-btn {
        position: relative;

        .am-icon-cart {
          font-size: 24px;
        }
      }

      &-number {
        position: absolute;
        top: -6px;
        left: -6px;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        line-height: 1;
        border-radius: 50%;
        background-color: var(--am-c-error);
        color: var(--am-c-cat-main-bgr);
      }
    }
  }
}

// Public
.amelia-v2-booking #amelia-container {
  @include catalog-header;
}

// Admin
#amelia-app-backend-new #amelia-container {
  @include catalog-header;
}
</style>
