import { reactive } from "vue";

const globalLabels = reactive(window.wpAmeliaLabels)

function useStatuses () {
  return [
    {
      value: 'approved',
      label: globalLabels.approved,
      icon: 'check',
      color: 'var(--am-c-success)',
    },
    {
      value: 'pending',
      label: globalLabels.pending,
      icon: 'refresh',
      color: 'var(--am-c-warning)',
    },
    {
      value: 'canceled',
      label: globalLabels.canceled,
      icon: 'close',
      color: 'var(--am-c-error)',
    },
    {
      value: 'rejected',
      label: globalLabels.rejected,
      icon: 'info-reverse',
      color: 'var(--am-c-main-text)',
    },
    {
      value: 'waiting',
      label: globalLabels.waiting,
      icon: 'clock',
      color: 'var(--am-c-warning)',
    },
    {
      value: 'no-show',
      label: globalLabels['no-show'],
      icon: 'no-show',
      color: '#808A90',
    },
  ]
}

export {
  useStatuses,
}
