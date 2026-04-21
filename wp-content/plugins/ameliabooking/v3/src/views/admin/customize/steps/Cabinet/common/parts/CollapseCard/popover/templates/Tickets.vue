<template>
  <div
    class="am-cc__tickets"
    :class="responsiveClass"
  >
    <template
      v-for="(item, index) in props.data"
      :key="index"
    >
      <div class="am-cc__tickets-item">
        <div class="am-cc__tickets-item_inner">
          <span class="am-cc__tickets-name">
            {{ item.name }}
          </span>
          <span class="am-cc__tickets-calc">
            {{ `${item.persons} ${item.persons > 1 ? amLabels.event_tickets : amLabels.event_ticket} x ${useFormattedPrice(item.price)}` }}
          </span>
        </div>

        <div class="am-cc__tickets-price">
          {{ useFormattedPrice(item.persons * item.price) }}
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
import { useResponsiveClass } from "../../../../../../../../../../assets/js/common/responsive.js";
import { useFormattedPrice } from "../../../../../../../../../../assets/js/common/formatting";

// * Vars
const amLabels = inject('labels')

let props = defineProps({
  data: {
    type: [Array, Object, String]
  }
})

let cWidth = inject('containerWidth')

let responsiveClass = computed(() => {
  return useResponsiveClass(cWidth.value)
})
</script>

<script>
export default {
  name: "TemplateTickets"
}
</script>

<style lang="scss">
@mixin popover-template-tickets {
  .am-cc__tickets {
    width: 340px;

    &.am-w-420 {
      width: 100%;
    }

    &-item {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      margin: 0 0 16px;

      &:last-child {
        margin: 0;
      }
    }

    &-name {
      width: 100%;
      display: flex;
      font-size: 14px;
      font-weight: 500;
      line-height: 1.428571429;
      color: var(--am-c-cc-text);
      margin: 0 0 4px;
    }

    &-calc {
      width: 100%;
      display: flex;
      font-size: 13px;
      font-weight: 500;
      line-height: 1.384615385;
      color: var(--am-c-cc-text-op70);
      margin: 0 0 4px;
    }

    &-price {
      display: flex;
      font-size: 15px;
      font-weight: 500;
      line-height: 1.428571429;
      color: var(--am-c-cc-primary);
    }
  }
}

.el-popover {
  @include popover-template-tickets;
}
</style>
