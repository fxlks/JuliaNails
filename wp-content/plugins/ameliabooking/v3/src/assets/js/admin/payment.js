import { reactive } from "vue";

const globalLabels = reactive(window.wpAmeliaLabels)

function usePaymentGatewayName (payment) {
  if (payment.gateway === 'onSite') {
    return globalLabels.on_site
  }

  if (payment.gateway === 'wc') {
    return payment.gatewayTitle
  }

  if (payment.gateway) {
    return payment.gateway.charAt(0).toUpperCase() + payment.gateway.slice(1)
  }
}

function usePaymentStatusName (status) {
  switch (status) {
    case ('paid'):
      return globalLabels.paid

    case ('pending'):
      return globalLabels.pending

    case ('partiallyPaid'):
      return globalLabels.partially_paid

    case ('refunded'):
      return globalLabels.refunded
  }
}

function usePaymentStatusIcon (status, amColors) {
  switch (status) {
    case 'paid':
      return {
        icon: 'am-icon-checkmark-circle',
        color: amColors.colorSuccess
      }
    case 'pending':
      return {
        icon: 'am-icon-refresh',
        color: amColors.colorWarning
      }
    case 'partiallyPaid':
      return {
        icon:'am-icon-prepaid',
        color: amColors.colorPrimary
      }
    case 'refunded':
      return {
        icon:'am-icon-refund',
        color: amColors.colorError
      }
  }
}

export {
  usePaymentGatewayName,
  usePaymentStatusName,
  usePaymentStatusIcon,
}
