<template>
  <div class="am-cc__qr-codes">
    <div
      v-for="(item, index) in props.data"
      :key="index"
      class="am-cc__qr-codes__item"
    >
      {{
        `${item.eventName} - ${
          item.type === 'ticket' ? 'Single Ticket.pdf' : 'Group Ticket.pdf'
        }`
      }}
    </div>
  </div>
</template>

<script setup>
// * Props
let props = defineProps({
  data: {
    type: [Array],
  },
})
</script>

<style lang="scss">
@mixin popover-template-qr-code {
  .am-cc__qr-codes {
    display: flex;
    flex-direction: column;
    max-width: 480px;
    gap: 6px;

    &__item {
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
      text-align: left;
      font-size: 14px;
      color: var(--am-c-cc-text);
      transition: color 0.3s;

      &:hover {
        color: var(--am-c-cc-primary);
      }
    }
  }
}

.el-popover {
  @include popover-template-qr-code;
}
</style>
