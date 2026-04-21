<template>
  <div class="am-cc__qr-codes">
    <div
      v-if="errorMessage"
      class="am-cc__qr-codes__message"
    >
      <span class="am-icon-info-reverse"></span>
      {{errorMessage}}
    </div>
    <div
      v-for="(item, index) in props.data"
      :key="index"
      class="am-cc__qr-codes__item"
      @click="showQrCodes(item)"
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
// * Import from Vue
import { ref, onMounted } from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Composables
import { useAuthorizationHeaderObject } from '../../../../../../../../assets/js/public/panel'
import httpClient from '../../../../../../../../plugins/axios'

// * Store
const store = useStore()

// * Props
let props = defineProps({
  data: {
    type: [Array],
  },
})

let errorMessage = ref('')

function showQrCodes(ticket) {
  httpClient
    .get(
      '/etickets',
      Object.assign(
        {
          params: {
            source: 'cabinet-customer',
            timeZone: store.getters['cabinet/getTimeZone'],
            eventId: ticket.eventId,
            bookingId: ticket.bookingId,
            ticketManualCode: ticket.ticketManualCode,
          },
        },
        useAuthorizationHeaderObject(store)
      )
    )
    .then((response) => {
      errorMessage.value = ''
      window.open(createFileUrlFromResponse(response.data))
    })
    .catch((error) => {
      if (error.response && error.response.data && error.response.data.message) {
        errorMessage.value = error.response.data.message
      } else {
        errorMessage.value = 'An error occurred while fetching the QR code.'
      }
    })
}

function createFileUrlFromResponse(pdfData) {
  const byteCharacters = atob(pdfData)
  const byteNumbers = new Array(byteCharacters.length)
  for (let i = 0; i < byteCharacters.length; i++) {
    byteNumbers[i] = byteCharacters.charCodeAt(i)
  }
  const byteArray = new Uint8Array(byteNumbers)
  const file = new Blob([byteArray], { type: 'application/pdf;base64' })
  return URL.createObjectURL(file)
}

onMounted(() => {
  errorMessage.value = ''
})
</script>

<style lang="scss">
@mixin popover-template-qr-code {
  .am-cc__qr-codes {
    display: flex;
    flex-direction: column;
    max-width: 480px;
    gap: 6px;

    &__message {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 4px 8px;
      border-radius: 4px;
      background-color: var(--am-c-cc-error-op15);
      color: var(--am-c-cc-error);
      text-align: center;
      font-size: 14px;
      line-height: 20px;

      span {
        font-size: 18px;
        line-height: 20px;
      }
    }

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
