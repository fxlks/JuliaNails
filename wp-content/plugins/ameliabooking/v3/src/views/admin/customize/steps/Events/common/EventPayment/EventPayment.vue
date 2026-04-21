<template>
  <div :class="props.globalClass">
    <Payment
      v-if="selectedEvent"
      :selected-item="selectedEvent"
      :customized-labels="customLabels"
    ></Payment>
  </div>
</template>

<script setup>
// * Common Components
import Payment from '../../../common/Payment.vue'

// * Import from Vue
import { ref, reactive, computed, inject } from 'vue'

// * Import Libraries
import moment from 'moment'

// * Composables
import { useReactiveCustomize } from '../../../../../../../assets/js/admin/useReactiveCustomize.js'

// * Components Properties
let props = defineProps({
  globalClass: {
    type: String,
    default: '',
  },
  componentClass: {
    type: String,
    default: '',
  },
  inDialog: {
    type: Boolean,
    default: false,
  },
})

// * Base Urls
const baseUrls = inject('baseUrls')

// * Selected Event
let selectedEvent = ref({
  id: 3,
  bookable: true,
  color: '#1788FB',
  opened: true,
  full: false,
  closed: false,
  description:
    "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
  fullPayment: false,
  gallery: [
    {
      pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
    },
    {
      pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
    },
    {
      pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
    },
    {
      pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
    },
    {
      pictureFullPath: `${baseUrls.value.wpAmeliaPluginURL}v3/src/assets/img/admin/customize/img_holder1.svg`,
    },
  ],
  locationId: 1,
  customPricing: true,
  customTickets: [
    {
      enabled: true,
      id: 1,
      eventId: 1,
      name: 'Ticket one',
      price: 10,
      sold: 0,
      spots: 20,
    },
    {
      enabled: true,
      id: 2,
      eventId: 1,
      name: 'Ticket two',
      price: 50,
      sold: 0,
      spots: 20,
    },
    {
      enabled: true,
      id: 2,
      eventId: 1,
      name: 'Ticket three',
      price: 100,
      sold: 0,
      spots: 20,
    },
  ],
  depositPerPerson: true,
  depositPayment: 'percentage',
  deposit: 10,
  maxCapacity: 9,
  maxCustomCapacity: null,
  maxExtraPeople: 3,
  name: 'Even 3',
  organizerId: 1,
  periods: [
    {
      id: 1,
      eventId: 1,
      periodStart: moment()
        .add(2, 'days')
        .add(5, 'hours')
        .format('YYYY-MM-DD hh:mm:ss'),
      periodEnd: moment()
        .add(4, 'days')
        .add(10, 'hours')
        .format('YYYY-MM-DD hh:mm:ss'),
    },
  ],
  pictureFullPath: null,
  pictureThumbPath: null,
  places: 30,
  price: 10,
  providers: [
    {
      id: 1,
      firstName: 'Jane',
      lastName: 'Doe',
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
    },
    {
      id: 2,
      firstName: 'Sahar',
      lastName: 'Potter',
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
    },
    {
      id: 3,
      firstName: 'Miles',
      lastName: 'Shannon',
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
    },
  ],
  show: true,
  status: 'approved',
  tags: [{}],
})

// * Customize
const { amCustomize } = useReactiveCustomize()

// * Form string recognition
let pageRenderKey = inject('pageRenderKey')

// * Labels
let langKey = inject('langKey')
let amLabels = inject('labels')

// * Computed labels
let customLabels = computed(() => {
  let computedLabels = reactive({ ...amLabels })

  if (amCustomize.value[pageRenderKey.value].payment.translations) {
    let customizedLabels =
      amCustomize.value[pageRenderKey.value].payment.translations
    Object.keys(customizedLabels).forEach((labelKey) => {
      if (customizedLabels[labelKey][langKey.value]) {
        computedLabels[labelKey] = customizedLabels[labelKey][langKey.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
})
</script>

<script>
export default {
  name: 'EventPayment',
  key: 'payment',
  label: 'event_payment',
}
</script>

<style lang="scss"></style>
