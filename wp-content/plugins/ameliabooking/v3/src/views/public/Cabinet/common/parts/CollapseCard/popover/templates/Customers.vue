<template>
  <div
    class="am-cc__customers"
    :class="responsiveClass"
  >
    <template
      v-for="(item, index) in props.data"
      :key="index"
    >
      <div class="am-cc__customers-item">
        <div class="am-cc__customers-item_inner">
          <span class="am-cc__customers-name">
            <span
              v-if="item.bookingStatus === 'waiting'"
              class="am-icon-clock am-cc__customers-waiting"
            ></span>
            {{ item.firstName }} {{ item.lastName }}
          </span>
          <span v-if="!customizedOptions || customizedOptions.appointments.options.customerEmail.visibility" class="am-cc__customers-info">
            {{ item.email }}
          </span>
          <span v-if="!customizedOptions || customizedOptions.appointments.options.customerPhone.visibility" class="am-cc__customers-info">
            {{ item.phone }}
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
  },
  customizedOptions: {
    type: Object,
    required: true
  }
})

let cWidth = inject('containerWidth')

let responsiveClass = computed(() => {
  return useResponsiveClass(cWidth.value)
})
</script>

<script>
export default {
  name: "TemplateCustomers"
}
</script>

<style lang="scss">
@mixin popover-template-customers {
  .am-cc__customers {
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

    &-waiting {
      margin-right: 2px;
      color: var(--am-c-cc-warning, #ffa500);
      font-size: 18px;
    }

    &-info {
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
  @include popover-template-customers;
}
</style>
