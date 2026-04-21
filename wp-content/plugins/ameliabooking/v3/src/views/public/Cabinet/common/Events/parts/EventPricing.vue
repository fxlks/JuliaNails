<template>
  <el-form
    ref="pricingFormRef"
    :model="pricingFormData"
    :style="cssVars"
    class="am-capei-price"
  >
    <div class="am-capei-price__heading">
      <el-form-item
        :label="`${amLabels.price}:`"
        class="am-capei-price__item am-w-50"
        :class="responsiveClass"
      >
        <AmInput
          v-model="pricingFormData.price"
          :disabled="pricingFormData.customPricing"
          :is-money="true"
        />
      </el-form-item>

      <el-form-item
        :label="`${amLabels.event_max_capacity}:`"
        class="am-capei-price__item am-w-50"
        :class="responsiveClass"
      >
        <AmInputNumber
          v-model="pricingFormData.maxCapacity"
          :disabled="pricingFormData.customPricing"
          :min="1"
        />
      </el-form-item>

      <div
        v-if="allowedMultiplePersons"
        class="am-capei-price__segment"
      >
        <AmCheckbox
          v-model="pricingFormData.bringingAnyone"
          :label="amLabels.event_bringing_anyone"
        >
        </AmCheckbox>

        <AmCheckbox
          v-if="pricingFormData.bringingAnyone"
          v-model="pricingFormData.aggregatedPrice"
          :label="amLabels.event_aggregated_price"
        >
        </AmCheckbox>

        <AmCheckbox
          v-model="pricingFormData.bookMultipleTimes"
          :label="amLabels.event_book_more_than_once"
        >
        </AmCheckbox>
      </div>
    </div>

    <div v-if="amSettings.featuresIntegrations.depositPayment.enabled && !licence.isStarter" class="am-capei-price__block">
      <div class="am-capei-price__block-header">
        <div class="am-capei-price__block-header__text">
          {{ amLabels.deposit_enabled }}
        </div>
        <Am-Switch
          v-model="pricingFormData.depositEnabled"
          :aria-label="amLabels.deposit_enabled"
        />
      </div>

      <template v-if="pricingFormData.depositEnabled">
        <el-form-item
          :label="`${amLabels.deposit_payment}:`"
          class="am-capei-price__item am-w-50"
          :class="responsiveClass"
        >
          <AmSelect v-model="pricingFormData.depositPayment">
            <AmOption value="percentage" :label="amLabels.amount_percentage" />
            <AmOption value="fixed" :label="amLabels.amount_fixed" />
          </AmSelect>
        </el-form-item>

        <el-form-item
          v-if="pricingFormData.depositPayment === 'fixed'"
          :label="`${amLabels.amount} (${getCurrencySymbol()}):`"
          class="am-capei-price__item am-w-50"
          :class="responsiveClass"
        >
          <AmInput
            v-model="pricingFormData.deposit"
            :is-money="true"
          />
        </el-form-item>

        <el-form-item
          v-if="pricingFormData.depositPayment === 'percentage'"
          :label="`${amLabels.amount} (%):`"
          class="am-capei-price__item am-w-50"
          :class="responsiveClass"
        >
          <AmInputNumber
            v-model="pricingFormData.deposit"
            :min="1"
            :max="100"
          />
        </el-form-item>
      </template>

      <div
        v-if="pricingFormData.depositEnabled"
        class="am-capei-price__segment"
      >
        <AmCheckbox
          v-model="pricingFormData.depositPerPerson"
          :label="amLabels.deposit_per_person"
        />

        <AmCheckbox
          v-model="pricingFormData.fullPayment"
          :label="amLabels.allow_total_event_amount"
        />
      </div>
    </div>

    <div
      v-if="(event.id || amSettings.featuresIntegrations.tickets.enabled) && !licence.isStarter"
      class="am-capei-price__block"
    >
      <div class="am-capei-price__block-header">
        <div class="am-capei-price__block-header__text">
          {{ amLabels.custom_pricing_enabled }}
        </div>
        <AmSwitch
          v-model="pricingFormData.customPricing"
          :disabled="event.bookings && event.bookings.length > 0"
          :aria-label="amLabels.custom_pricing_enabled"
          @change="changeCustomPricing"
        />
      </div>

      <template v-if="pricingFormData.customPricing">
        <div
          v-if="props.pageWidth > 545"
          class="am-capei-price__block-heading am-pl-32 am-pr-44"
        >
          <div
            class="am-capei-price__block-heading__item am-custom-pricing am-w-40"
          >
            {{ amLabels.name }}:
          </div>

          <div
            class="am-capei-price__block-heading__item am-custom-pricing am-w-30"
          >
            {{ amLabels.price }}:
          </div>

          <div
            class="am-capei-price__block-heading__item am-custom-pricing am-w-30"
          >
            {{ amLabels.spots }}:
          </div>
        </div>

        <div
          v-for="(ticket, index) in pricingFormData.customTickets"
          :key="index"
          class="am-capei-price__block-segment am-custom-76"
        >
          <div
            class="am-capei-price__block-inner am-ws-16"
            :class="{ 'am-mt-22': props.pageWidth < 546 }"
          >
            <el-form-item>
              <AmCheckbox v-model="ticket.enabled" />
            </el-form-item>
          </div>

          <div
            class="am-capei-price__block-inner"
            :class="[
              { 'am-we-40': props.pageWidth > 545 },
              { 'am-we-100': props.pageWidth < 546 },
            ]"
          >
            <el-form-item
              :label="props.pageWidth < 546 ? `${amLabels.name}:` : ''"
            >
              <AmInput
                v-model="ticket.name"
                :disabled="!ticket.enabled"
                :aria-label="amLabels.name"
              />
            </el-form-item>
          </div>

          <div
            class="am-capei-price__block-inner"
            :class="[
              { 'am-we-30': props.pageWidth > 545 },
              { 'am-w-50 am-order-2': props.pageWidth < 546 },
              { 'am-w-100': props.pageWidth < 401 },
            ]"
          >
            <el-form-item :label="props.pageWidth < 546 ? amLabels.price : ''">
              <AmInput
                v-model="ticket.price"
                :disabled="!ticket.enabled"
                :is-money="true"
                :aria-label="amLabels.price"
              />
            </el-form-item>
          </div>

          <div
            class="am-capei-price__block-inner"
            :class="[
              { 'am-we-30': props.pageWidth > 545 },
              { 'am-w-50 am-order-3': props.pageWidth < 546 },
              { 'am-w-100': props.pageWidth < 401 },
            ]"
          >
            <el-form-item
              :label="props.pageWidth < 546 ? `${amLabels.spots}:` : ''"
            >
              <AmInputNumber
                v-model="ticket.spots"
                :min="1"
                :disabled="!ticket.enabled || pricingFormData.maxCustomCapacityEnabled"
                :aria-label="amLabels.spots"
              />
            </el-form-item>
          </div>

          <div
            v-if="!ticket.booked && pricingFormData.customTickets.length > 1"
            class="am-capei-price__block-delete"
            :class="{ 'am-order-1 am-mt-22': props.pageWidth < 546 }"
          >
            <AmButton
              category="secondary"
              type="plain"
              size="mini"
              :icon-only="true"
              :icon="{
                components: { IconComponent },
                template: `<IconComponent icon='bucket'/>`,
              }"
              @click="removeTicket(index)"
            />
          </div>

          <div v-if="props.pageWidth < 546" class="am-separator am-order-6" />
        </div>

        <AmButton
          :icon="plusIcon"
          prefix="plus"
          size="default"
          category="primary"
          :type="'filled'"
          @click="addTicket"
        >
          {{ amLabels.add_ticket_category }}
        </AmButton>

        <div class="am-separator" />

        <div class="am-capei-price__part">
          <div
            class="am-capei-price__part-item"
            :class="[
              { 'am-w-100': props.pageWidth < 546 },
              { 'am-w-50': props.pageWidth > 545 },
            ]"
          >
            <AmCheckbox
              v-model="pricingFormData.maxCustomCapacityEnabled"
              :label="`${amLabels.event_max_capacity}:`"
            />
          </div>

          <div
            class="am-capei-price__part-item"
            :class="[
              { 'am-w-100': props.pageWidth < 546 },
              { 'am-w-50': props.pageWidth > 545 },
            ]"
          >
            <AmInputNumber
              v-model="pricingFormData.maxCustomCapacity"
              :min="1"
              :disabled="!pricingFormData.maxCustomCapacityEnabled"
            />
          </div>
        </div>

        <div class="am-separator" />

        <div class="am-capei-price__block-header">
          <div class="am-capei-price__block-header__text">
            {{ amLabels.pricing_by_date_enabled }}
          </div>
          <AmSwitch
            v-model="pricingFormData.customTicketsRangesEnabled"
            :aria-label="amLabels.pricing_by_date_enabled"
          />
        </div>

        <div
          v-if="pricingFormData.customTicketsRangesEnabled"
          class="am-capei-price__block-segment"
        >
          <template
            v-for="(ticketRange, rangeIndex) in pricingFormData.customTicketsRanges"
            :key="rangeIndex"
          >
            <el-form-item
              :label="`${amLabels.event_date_range}:`"
              class="am-capei-price__date-range"
            >
              <AmDatePicker
                v-model="ticketRange.range"
                type="daterange"
                :format="momentDateFormat()"
                placeholder="Pick a day"
                :lang="localLanguage"
                :disabled-date="isInBookingRange"
                :start-placeholder="amLabels.start_date"
                :end-placeholder="amLabels.end_date"
              />
              <AmButton
                category="secondary"
                type="plain"
                size="mini"
                :icon-only="true"
                :icon="{
                  components: { IconComponent },
                  template: `<IconComponent icon='bucket'/>`,
                }"
                :aria-label="amLabels.delete"
                @click="removeTicketsRange(rangeIndex)"
              />
            </el-form-item>

            <div
              v-if="props.pageWidth > 545"
              class="am-capei-price__block-heading"
            >
              <div
                class="am-capei-price__block-heading__item am-custom-date am-w-50"
              >
                {{ amLabels.name }}:
              </div>

              <div
                class="am-capei-price__block-heading__item am-custom-date am-w-50"
              >
                {{ amLabels.price }}:
              </div>
            </div>

            <template
              v-for="(ticket, index) in pricingFormData.customTickets"
              :key="index"
            >
              <div
                class="am-capei-price__block-inner"
                :class="[
                  { 'am-w-50': props.pageWidth > 545 },
                  { 'am-w-100': props.pageWidth < 546 },
                ]"
              >
                <el-form-item
                  :label="props.pageWidth < 546 ? `${amLabels.name}:` : ''"
                >
                  <AmInput
                    v-model="ticket.name"
                    :disabled="true"
                    :aria-label="amLabels.name"
                  />
                </el-form-item>
              </div>

              <div
                class="am-capei-price__block-inner"
                :class="[
                  { 'am-w-50': props.pageWidth > 545 },
                  { 'am-w-100': props.pageWidth < 546 },
                ]"
              >
                <el-form-item :label="props.pageWidth < 546 ? `${amLabels.price}:` : ''">
                  <AmInput
                    v-model="ticketRange.tickets[index].price"
                    :is-money="true"
                    :aria-label="amLabels.price"
                  />
                </el-form-item>
              </div>
            </template>

            <div class="am-separator" />
          </template>

          <AmButton
            :icon="plusIcon"
            prefix="plus"
            size="default"
            category="primary"
            :type="'filled'"
            @click="addTicketsRange"
          >
            {{ amLabels.event_add_date_range }}
          </AmButton>
        </div>
      </template>
    </div>

    <div
      v-if="
        pricingFormData.maxCapacity > 1 ||
        pricingFormData.maxCustomCapacity > 1 ||
        ticketsSpotsNumber > 1
      "
      class="am-capei-price__block"
    >
      <div class="am-capei-price__block-header">
        <div class="am-capei-price__block-header__text">
          {{ amLabels.event_close_after_min }}
        </div>
        <AmSwitch
          v-model="pricingFormData.closeAfterMinEnabled"
          :aria-label="amLabels.event_close_after_min"
        />
      </div>

      <div
        v-if="
          pricingFormData.closeAfterMinEnabled &&
          (pricingFormData.maxCapacity > 1 ||
            pricingFormData.maxCustomCapacity > 1 ||
            ticketsSpotsNumber > 1)
        "
        class="am-capei-price__part"
      >
        <AmRadioGroup
          v-model="pricingFormData.closeAfterMinBookings"
          class="am-capei-price__part-item"
          :class="[
            { 'am-w-100': props.pageWidth < 546 },
            { 'am-w-50': props.pageWidth > 545 },
          ]"
        >
          <AmRadio :value="false">{{ amLabels.event_close_min_total }}</AmRadio>
          <AmRadio :value="true">{{ amLabels.event_close_min_bookings }}</AmRadio>
        </AmRadioGroup>

        <el-form-item
          class="am-capei-price__part-item"
          :class="[
            { 'am-w-100': props.pageWidth < 546 },
            { 'am-w-50': props.pageWidth > 545 },
          ]"
        >
          <AmInputNumber
            v-model="pricingFormData.closeAfterMin"
            :min="1"
            :max="maxCapacity"
          />
        </el-form-item>
      </div>
    </div>

    <div
      v-if="
        pricingFormData.maxCapacity > 1 ||
        pricingFormData.maxCustomCapacity > 1 ||
        ticketsSpotsNumber > 1
      "
      class="am-capei-price__block"
    >
      <div class="am-capei-price__block-header">
        <div class="am-capei-price__block-header__text">
          {{ amLabels.limit_extra_people }}
        </div>
        <AmSwitch
          v-model="pricingFormData.maxExtraPeopleEnabled"
          :aria-label="amLabels.limit_extra_people"
        />
      </div>

      <div
        v-if="
          pricingFormData.maxExtraPeopleEnabled &&
          (pricingFormData.maxCapacity > 1 ||
            pricingFormData.maxCustomCapacity > 1 ||
            ticketsSpotsNumber > 1)
        "
        class="am-capei-price__part"
      >
        <el-form-item
          :label="`${amLabels.limit_extra_people_set}:`"
          class="am-capei-price__part-item am-w-100"
        >
          <AmInputNumber
            v-model="pricingFormData.maxExtraPeople"
            :min="0"
            :max="maxCapacity - 1"
          />
        </el-form-item>
      </div>
    </div>
  </el-form>
</template>

<script setup>
// * Import form Vue
import { computed, ref, inject } from 'vue'

// * Import store
import { useStore } from 'vuex'

// * Import Components
import AmInput from '../../../../../_components/input/AmInput.vue'
import AmInputNumber from '../../../../../_components/input-number/AmInputNumber.vue'
import AmDatePicker from '../../../../../_components/datePicker/AmDatePicker.vue'
import AmCheckbox from '../../../../../_components/checkbox/AmCheckbox.vue'
import AmSelect from '../../../../../_components/select/AmSelect.vue'
import AmOption from '../../../../../_components/select/AmOption.vue'
import AmButton from '../../../../../_components/button/AmButton.vue'
import IconComponent from '../../../../../_components/icons/IconComponent.vue'
import AmRadio from '../../../../../_components/radio/AmRadio.vue'
import AmRadioGroup from '../../../../../_components/radio/AmRadioGroup.vue'
import AmSwitch from '../../../../../_components/switch/AmSwitch.vue'

// * Import Composables
import { momentDateFormat } from '../../../../../../assets/js/common/date'
import { getCurrencySymbol } from '../../../../../../assets/js/common/formatting'
import { useColorTransparency } from '../../../../../../assets/js/common/colorManipulation'
import { useResponsiveClass } from '../../../../../../assets/js/common/responsive'
import moment from "moment";

// * Props
const props = defineProps({
  pageWidth: {
    type: Number,
    default: 0,
  },
})

let responsiveClass = computed(() => {
  return useResponsiveClass(props.pageWidth)
})

// * Store
let store = useStore()

// * Labels
let amLabels = inject('amLabels')

// * Root Settings
const amSettings = inject('settings')

// * Language
let localLanguage = inject('localLanguage')

// * Plugin Licence
let licence = inject('licence')

let plusIcon = {
  components: { IconComponent },
  template: `<IconComponent icon="plus"/>`,
}

function isInBookingRange(value) {
  let currentDate = moment(value)

  let isSelected = false

  pricingFormData.value.customTicketsRanges.forEach((item) => {
    if (!isSelected && item.range[0] && item.range[1]) {
      isSelected = moment(item.range[0]) <= currentDate && moment(item.range[1]) >= currentDate
    }
  })

  if (isSelected) {
    return true
  }

  let minDate = !store.getters['event/getBookingOpensDisabled']
    ? moment(store.getters['event/getBookingOpensDate'])
    : moment()

  let maxDate = !store.getters['event/getBookingClosesDisabled']
    ? moment(store.getters['event/getBookingClosesDate'])
    : (store.getters['event/getPeriods'][0].startDate ? moment(store.getters['event/getPeriods'][0].startDate, 'YYYY-MM-DD') : moment())

  return currentDate < minDate || currentDate > maxDate
}

let event = computed(() => store.getters['event/getEvent'])

let allowedMultiplePersons = computed(() => {
  return pricingFormData.value.customPricing
    ? pricingFormData.value.customTickets.reduce(
      (sum, ticket) => sum + ticket.spots,
      0
    ) > 1
    : pricingFormData.value.maxCapacity > 1
})

let ticketsSpotsNumber = computed(() => {
  return pricingFormData.value.customPricing
    ? pricingFormData.value.customTickets.reduce(
        (sum, ticket) => sum + ticket.spots,
        0
      )
    : 0
})

let maxCapacity = computed(() => {
  if (
    pricingFormData.value.customPricing &&
    pricingFormData.value.maxCustomCapacityEnabled
  ) {
    return pricingFormData.value.maxCustomCapacity
  } else if (pricingFormData.value.customPricing) {
    return ticketsSpotsNumber.value
  }

  return pricingFormData.value.maxCapacity
})

function changeCustomPricing() {
  if (
    pricingFormData.value.customPricing &&
    pricingFormData.value.customTickets.length === 0
  ) {
    addTicket()
  }
}

function removeTicket(index) {
  pricingFormData.value.customTickets.splice(index, 1)
}

function addTicket() {
  pricingFormData.value.customTickets.push({
    enabled: true,
    name: '',
    spots: 1,
    waitingListSpots: 0,
    price: 0,
  })

  pricingFormData.value.customTicketsRanges.forEach((i) => {
    i.tickets.push(new Object({ price: 0 }))
  })
}

function removeTicketsRange(index) {
  pricingFormData.value.customTicketsRanges.splice(index, 1)
}

function addTicketsRange() {
  pricingFormData.value.customTicketsRanges.push({
    range: ['', ''],
    tickets: pricingFormData.value.customTickets.map(
      (i) => new Object({ price: i.price })
    ),
  })
}

// * Form Reference
const pricingFormRef = ref(null)

// * Form data
let pricingFormData = ref({
  price: computed({
    get: () => store.getters['event/getPrice'],
    set: (val) => {
      store.commit('event/setPrice', val)
    },
  }),
  maxCapacity: computed({
    get: () => store.getters['event/getMaxCapacity'],
    set: (val) => {
      store.commit('event/setMaxCapacity', val)
    },
  }),
  bringingAnyone: computed({
    get: () => store.getters['event/getBringingAnyone'],
    set: (val) => {
      store.commit('event/setBringingAnyone', val)
    },
  }),
  aggregatedPrice: computed({
    get: () => store.getters['event/getAggregatedPrice'],
    set: (val) => {
      store.commit('event/setAggregatedPrice', val)
    },
  }),
  bookMultipleTimes: computed({
    get: () => store.getters['event/getBookMultipleTimes'],
    set: (val) => {
      store.commit('event/setBookMultipleTimes', val)
    },
  }),
  depositEnabled: computed({
    get: () => store.getters['event/getDepositEnabled'],
    set: (val) => {
      store.commit('event/setDepositEnabled', val)
    },
  }),
  deposit: computed({
    get: () => store.getters['event/getDeposit'],
    set: (val) => {
      store.commit('event/setDeposit', val)
    },
  }),
  depositPayment: computed({
    get: () => store.getters['event/getDepositPayment'],
    set: (val) => {
      store.commit('event/setDepositPayment', val)
    },
  }),
  depositPerPerson: computed({
    get: () => store.getters['event/getDepositPerPerson'],
    set: (val) => {
      store.commit('event/setDepositPerPerson', val)
    },
  }),
  fullPayment: computed({
    get: () => store.getters['event/getFullPayment'],
    set: (val) => {
      store.commit('event/setFullPayment', val)
    },
  }),
  customPricing: computed({
    get: () => store.getters['event/getCustomPricing'],
    set: (val) => {
      store.commit('event/setCustomPricing', val)
    },
  }),
  customTickets: computed({
    get: () => store.getters['event/getCustomTickets'],
    set: (val) => {
      store.commit('event/setCustomTickets', val)
    },
  }),
  customTicketsRangesEnabled: computed({
    get: () => store.getters['event/getCustomTicketsRangesEnabled'],
    set: (val) => {
      store.commit('event/setCustomTicketsRangesEnabled', val)
    },
  }),
  customTicketsRanges: computed({
    get: () => store.getters['event/getCustomTicketsRanges'],
    set: (val) => {
      store.commit('event/setCustomTicketsRanges', val)
    },
  }),
  maxCustomCapacityEnabled: computed({
    get: () => store.getters['event/getMaxCustomCapacityEnabled'],
    set: (val) => {
      store.commit('event/setMaxCustomCapacityEnabled', val)
    },
  }),
  maxCustomCapacity: computed({
    get: () => store.getters['event/getMaxCustomCapacity'],
    set: (val) => {
      store.commit('event/setMaxCustomCapacity', val)
    },
  }),
  closeAfterMinEnabled: computed({
    get: () => store.getters['event/getCloseAfterMinEnabled'],
    set: (val) => {
      store.commit('event/setCloseAfterMinEnabled', val)
    },
  }),
  closeAfterMin: computed({
    get: () => store.getters['event/getCloseAfterMin'],
    set: (val) => {
      store.commit('event/setCloseAfterMin', val)
    },
  }),
  closeAfterMinBookings: computed({
    get: () => store.getters['event/getCloseAfterMinBookings'],
    set: (val) => {
      store.commit('event/setCloseAfterMinBookings', val)
    },
  }),
  maxExtraPeopleEnabled: computed({
    get: () => store.getters['event/getMaxExtraPeopleEnabled'],
    set: (val) => {
      store.commit('event/setMaxExtraPeopleEnabled', val)
    },
  }),
  maxExtraPeople: computed({
    get: () => store.getters['event/getMaxExtraPeople'],
    set: (val) => {
      store.commit('event/setMaxExtraPeople', val)
    },
  }),
})

// * Expose
defineExpose({
  pricingFormRef,
})

// * Colors
let amColors = inject('amColors')

// * CSS Variables
let cssVars = computed(() => {
  return {
    '--am-c-capei-price-text': amColors.value.colorMainText,
    '--am-c-capei-price-text-op03': useColorTransparency(
      amColors.value.colorMainText,
      0.03
    ),
    '--am-c-capei-price-inp-bgr-op03': useColorTransparency(
      amColors.value.colorInpBgr,
      0.03
    ),
  }
})
</script>

<style lang="scss">
@mixin cabinet-event-price-block {
  // am - amelia
  // capei - cabinet-panel-event-item
  .am-capei-price {
    display: flex;
    flex-direction: column;
    gap: 16px;
    width: 100%;

    &__heading {
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      align-items: center;
      gap: 16px;
      width: 100%;
    }

    &__segment {
      display: flex;
      flex-direction: column;
      gap: 8px;
      width: 100%;
    }

    &__block {
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      gap: 16px;
      width: 100%;
      background-color: var(--am-c-capei-price-text-op03);
      padding: 16px;
      border-radius: 8px;

      &-header {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        width: 100%;

        &__text {
          font-size: 15px;
          font-weight: 500;
          line-height: 1.6;
          color: var(--am-c-capei-price-text);
        }
      }

      &-heading {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
        gap: 16px;
        width: 100%;

        &.am-pl-32 {
          padding-left: 32px;
        }

        &.am-pr-44 {
          padding-right: 44px;
        }

        &__item {
          display: flex;
          align-items: center;
          width: 100%;
          font-size: 15px;
          font-style: normal;
          font-weight: 500;
          line-height: 20px;
          color: var(--am-c-capei-price-text);

          &.am-custom-pricing {
            @include set-item-width(3, 16px * 2);
          }

          &.am-custom-date {
            @include set-item-width(2, 16px);
          }
        }
      }

      $excludeWidth: 0px;

      &-segment {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
        gap: 16px;
        width: 100%;

        &.am-custom-76 {
          $excludeWidth: 76px;
        }

        .el-form-item {
          width: 100%;
          margin-bottom: 0;
        }

        .am-date-picker {
          width: 100%;
        }
      }

      &-inner {
        display: flex;
        flex-direction: row;
        align-items: center;
        width: 100%;

        // ws - width static
        &.am-ws-16 {
          width: 16px;
          max-width: 16px;
        }

        // we - width exclude
        &.am-we-100 {
          width: calc(100% - $excludeWidth);
        }

        &.am-we-40 {
          width: calc(((100% - $excludeWidth) / 100) * 40 - 16px);
        }

        &.am-we-30 {
          width: calc(((100% - $excludeWidth) / 100) * 30 - 16px);
        }

        &.am-w-50 {
          width: calc(50% - 8px);
        }

        &.am-w-100 {
          width: 100%;
        }

        &.am-order {
          &-1 {
            order: 1;
          }
          &-2 {
            order: 2;
          }
          &-3 {
            order: 3;
          }
        }

        &.am-mt-22 {
          margin-top: 22px;
        }

        .el-form-item {
          margin: 0;
          width: 100%;
        }
      }

      &-delete {
        display: flex;
        align-items: center;

        &.am-mt-22 {
          margin-top: 22px;
        }

        .am-icon-bucket {
          font-size: 26px;
          line-height: 1;
        }
      }
    }

    &__item {
      width: 100%;

      &.am-w-50 {
        width: calc(50% - 8px);

        &.am-rw-460 {
          width: 100%;
        }
      }

      &.el-form-item {
        margin-bottom: 0;

        &.is-required:not(.is-no-asterisk) > .el-form-item__label:before {
          color: var(--am-c-error);
        }
      }

      .el-form-item {
        &__error {
          color: var(--am-c-error);
          padding: 0;
          line-height: 1.6;
        }
      }
    }

    &__delete {
      width: 100%;
      border-bottom: 1px solid var(--am-c-inp-border);
      padding: 0 0 16px;
    }

    &__part {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 8px 16px;
      width: 100%;

      &-item {
        width: 100%;

        &.am-w-50 {
          width: calc(50% - 8px);
        }

        &.am-w-100 {
          width: 100%;
        }
      }
    }

    &__date-range {
      width: 100%;

      .el-form-item__content {
        display: flex;
        align-items: center;
        gap: 16px;
      }

      .am-icon-bucket {
        font-size: 26px;
        line-height: 1;
      }
    }

    .el-input-number {
      width: 100%;
    }

    .am-checkbox {
      min-height: auto;

      .el-checkbox {
        &__input {
          padding: 0;
          align-self: center;
        }
      }
    }

    .am-separator {
      width: 100%;
      border-bottom: 1px solid var(--am-c-inp-border);

      &.am-order-6 {
        order: 6;
      }
    }
  }
}

@mixin set-item-width($itemsNumber, $gapWidth) {
  &.am-w-50 {
    width: calc(50% - (#{$gapWidth} / #{$itemsNumber}));
  }

  &.am-w-40 {
    width: calc(40% - (#{$gapWidth} / #{$itemsNumber}));
  }

  &.am-w-30 {
    width: calc(30% - (#{$gapWidth} / #{$itemsNumber}));
  }
}

.amelia-v2-booking #amelia-container {
  @include cabinet-event-price-block;
}
</style>