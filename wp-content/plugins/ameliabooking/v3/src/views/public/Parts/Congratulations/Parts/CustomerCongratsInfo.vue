<template>
  <div
    class="am-congrats__info-item am-fs__congrats-info-customer-name"
    :class="responsiveClass"
  >
    <span class="am-congrats__info-item__label">
      {{ props.labels.your_name_colon }}:
    </span>
    <span class="am-congrats__info-item__value">
      {{ `${props.customer.firstName} ${props.customer.lastName}` }}
    </span>
  </div>
  <div
    v-if="props.customer.email"
    class="am-congrats__info-item am-fs__congrats-info-customer-email"
    :class="responsiveClass"
  >
    <span class="am-congrats__info-item__label">
      {{ props.labels.email_address_colon }}:
    </span>
    <span class="am-congrats__info-item__value">
      {{ props.customer.email }}
    </span>
  </div>
  <div
    v-if="props.customer.phone"
    class="am-congrats__info-item am-fs__congrats-info-customer-phone"
    :class="responsiveClass"
  >
    <span class="am-congrats__info-item__label">
      {{ props.labels.phone_number_colon }}:
    </span>
    <span class="am-congrats__info-item__value">
      {{ props.customer.phone }}
    </span>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  inject,
  computed
} from "vue";

// * Composables
import { useResponsiveClass } from "../../../../../assets/js/common/responsive";

// * Component props
let props = defineProps({
  customer: {
    type: Object,
    required: true
  },
  labels: {
    type: Object,
    required: true
  },
  inDialog: {
    type: Boolean,
    default: false
  }
})

// * Responsive - Container Width
let cWidth = inject('containerWidth')
let dWidth = inject('dialogWidth')

let componentWidth = computed(() => {
  return props.inDialog ? dWidth.value : cWidth.value
})

let responsiveClass = computed(() => useResponsiveClass(componentWidth.value))
</script>

<script>
export default {
  name: "CustomerCongratsInfo"
}
</script>

<style lang="scss">
@mixin congrats-info-item-block {
  .am-congrats__info-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;

    * {
      font-size: 14px;
      line-height: 1.42857;
      word-break: break-word;
    }

    &.am-rw-480 {
      flex-direction: column;
      align-items: flex-start;

      * {
        font-size: 16px;
      }
    }

    &__label {
      color: var(--am-c-atc-text-op40);
    }

    &__value {
      color: var(--am-c-atc-text);
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include congrats-info-item-block;
}
</style>