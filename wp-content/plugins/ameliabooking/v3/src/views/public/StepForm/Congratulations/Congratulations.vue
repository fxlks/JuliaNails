<template>
  <div
    v-if="ready && booked && !loading"
    :style="cssVars"
    class="am-fs__main-content am-fs__congrats"
    :class="[{'am-fs-sb-atc' : checkScreen}, props.globalClass]"
    tabindex="0"
  >
    <div class="am-fs__congrats-main">
      <img
        :src="baseUrls.wpAmeliaPluginURL+'/v3/src/assets/img/congratulations/congratulations.svg'"
        :alt="amLabels.congratulations"
      >
      <div v-if="waitingListOptions.enabled && waitingListOptions.isWaitingListSlot" class="am-fs__congrats-main-waiting-list-block">
        <p class="am-fs__congrats-main-heading">
          {{amLabels.your_position_on_waiting_list}} {{ '#' + (waitingListOptions.peopleWaiting + 1)}}
        </p>
        <span>{{amLabels.appointment_waiting_list_notify_message}}</span>
      </div>
      <p v-else class="am-fs__congrats-main-heading">
        {{ amLabels.congratulations }}
      </p>
      <span v-if="booked && booked.data.length && bookedType === 'appointment'">{{amLabels.appointment_id}} #{{ booked.data[0].appointmentId  }}</span>

      <AddToCalendar v-if="checkScreen" class="am-fs__congrats-main-atc"></AddToCalendar>
    </div>

    <div
      class="am-fs__congrats-info"
      :class="[
        {'am-fs__congrats-info-mobile' : checkScreen},
        {'am-fs__congrats-info-mobile-s' : mobileS},
      ]"
    >
      <div class="am-fs__congrats-info-customer">
        <component :is="componentTypes[bookedType]"></component>
        <div v-if="booked.price > 0 || (booked.price <= 0 && coupon.code)" class="am-fs__congrats-info-payment">
          <span v-if="booked && booked.paymentAmount && getPayment(booked.payments).gateway === 'onSite'">
            {{ amLabels.congrats_total_amount }}:
          </span>
          <span v-else>{{ amLabels.congrats_payment }}:</span>
          <span v-if="booked && booked.paymentAmount &&  getPayment(booked.payments).gateway">
            {{ `${useFormattedPrice(booked.paymentAmount)} - ${ getPayment(booked.payments).gatewayTitle ?  getPayment(booked.payments).gatewayTitle : getPaymentGatewayNiceName( getPayment(booked.payments))}` }}
          </span>
          <span v-else-if="booked">
            {{ (  getPayment(booked.payments).gateway !== 'onSite' ? useFormattedPrice(booked.paymentAmount) : useFormattedPrice(booked.price < 0 ? 0 : booked.price) ) + ( getPayment(booked.payments).status !== 'paid' ?  (' - ' + amLabels.on_site) : '') }}
          </span>
          <span v-else></span>
        </div>
        <div class="am-fs__congrats-info-customer-border am-fs__congrats-info-customer-name">
          <span>{{ amLabels.your_name_colon }}:</span>
          <span>{{ customer.firstName + ' ' + customer.lastName }}</span>
        </div>
        <div v-if="customer.email" class="am-fs__congrats-info-customer-email">
          <span>{{ amLabels.email_address_colon }}:</span>
          <span>{{ customer.email }}</span>
        </div>
        <div v-if="customer.phone" class="am-fs__congrats-info-customer-phone">
          <span>{{ amLabels.phone_number_colon }}:</span>
          <span>{{ customer.phone }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Skeleton -->
  <el-skeleton v-else animated class="am-skeleton-congratz">
    <template #template>
      <div class="am-skeleton-congratz-heading">
        <el-skeleton-item variant="circle" />
        <el-skeleton-item variant="h3" />
        <el-skeleton-item variant="text" />
      </div>
      <div class="am-skeleton-congratz-booking-info">
        <div v-for="(item, i) in new Array(10)" :key="item" :class="{'am-customer-info': (i === 6)}">
          <el-skeleton-item variant="text" :style="{width: `${useRandomIntFromInterval(14, 36)}%`}"/>
          <el-skeleton-item variant="text" :style="{width: `${useRandomIntFromInterval(14, 36)}%`}"/>
        </div>
      </div>
    </template>
  </el-skeleton>
  <!-- /Skeleton -->
</template>

<script setup>
import AddToCalendar from './AddToCalendar'
import CartInfoService from './parts/CartInfoService'
import AppointmentInfoService from './parts/AppointmentInfoService'
import PackageInfoService from './parts/PackageInfoService'
import { computed, inject, provide, markRaw, watchEffect, onMounted } from 'vue'
import { useColorTransparency } from '../../../../assets/js/common/colorManipulation'
import { useRandomIntFromInterval, useFormattedPrice } from '../../../../assets/js/common/formatting'
import { useStore } from 'vuex'
import { useCart } from '../../../../assets/js/public/cart'
import { useRenderAction } from "../../../../assets/js/public/renderActions";

let props = defineProps({
  globalClass: {
    type: String,
    default: ''
  }
})

// * Root Settings
const amSettings = inject('settings')

const store = useStore()

// * Labels
const amLabels = inject('amLabels')

/**************
 * Computed *
 *************/

let ready = computed(() => store.getters['entities/getReady'])

let booked = computed(() => store.getters['booking/getBooked'])

provide('booked', booked)

let bookedType = computed(() => {
  return useCart(store).filter(i => i.serviceId && i.serviceId in i.services).length > 1 ? 'cart' : booked.value.type
})

// * Coupon
let coupon = computed(() => store.getters['booking/getCoupon'])

let loading = computed(() => store.getters['booking/getLoading'])

let customer = computed(() => {
  return {
    firstName: store.getters['booking/getCustomerFirstName'],
    lastName: store.getters['booking/getCustomerLastName'],
    email: store.getters['booking/getCustomerEmail'],
    phone: store.getters['booking/getCustomerPhone'],
  }
})

let waitingListOptions = computed(() => store.getters['appointmentWaitingListOptions/getOptions'])

/**************
 * Navigation *
 *************/

const { footerButtonClicked } = inject('changingStepsFunctions', {
  footerButtonClicked: {
    value: false
  }
})

function getPayment (payments) {
  let onlinePayments = payments.filter(i => i.gateway !== 'onSite')

  return onlinePayments.length ? onlinePayments[0] : payments[0]
}

function getPaymentGatewayNiceName (payment) {
  if (payment.gateway === 'onSite') {
    return amLabels.value['on_site']
  }

  if (payment.gateway === 'wc') {
    return payment.gatewayTitle
  }

  if (payment.gateway) {
    return payment.gateway.charAt(0).toUpperCase() + payment.gateway.slice(1)
  }
}

// * Watching when footer button was clicked
watchEffect(() => {
  if (footerButtonClicked.value) {
    finish()
  }
})

const componentTypes = {
  cart: markRaw(CartInfoService),
  appointment: markRaw(AppointmentInfoService),
  package: markRaw(PackageInfoService),
}

const globalSettings = inject('settings')
const baseUrls = inject('baseUrls')

let cWidth = inject('containerWidth', 0)
let checkScreen = computed(() => cWidth.value < 540)
let mobileS = computed(() => cWidth.value < 340)

function finish () {
  let entity = store.getters['entities/getBookableFromBookableEntities'](
    store.getters['booking/getSelection']
  )

  let entitySettings = entity.settings ? JSON.parse(entity.settings) : globalSettings

  if ('general' in entitySettings && 'redirectUrlAfterAppointment' in entitySettings.general && entitySettings.general.redirectUrlAfterAppointment) {
    window.location.href = entitySettings.general.redirectUrlAfterAppointment
  } else if (globalSettings.general.redirectUrlAfterAppointment) {
    window.location.href = globalSettings.general.redirectUrlAfterAppointment
  } else {
    window.location.reload()
  }
}


let amColors = inject('amColors')

const cssVars = computed(() => {
  if (checkScreen.value) {
    return  {
      '--am-c-atc-text-op40': useColorTransparency(amColors.value.colorMainText, 0.4),
      '--am-c-atc-heading-text-op40': useColorTransparency(amColors.value.colorSbText, 0.4),
      '--am-c-atc-text-op30': useColorTransparency(amColors.value.colorMainText, 0.3),
      '--am-c-atc-text': amColors.value.colorMainText,
      '--am-c-atc-heading-text': amColors.value.colorSbText,
      '--am-c-atc-bgr-coverage': booked.value.type === 'package' && !(amSettings.general.addToCalendar && booked.value && booked.value.data.length) ? '50%' : '80%'
    }
  } else {
    return  {
      '--am-c-atc-text-op40': useColorTransparency(amColors.value.colorMainText, 0.4),
      '--am-c-atc-heading-text-op40': useColorTransparency(amColors.value.colorMainHeadingText, 0.4),
      '--am-c-atc-text-op30': useColorTransparency(amColors.value.colorMainText, 0.3),
      '--am-c-atc-text': amColors.value.colorMainText,
      '--am-c-atc-heading-text': amColors.value.colorMainHeadingText
    }
  }
})

onMounted(() => {
  useRenderAction('congratulationsLoaded')
})
</script>

<script>
export default {
  name: 'CongratulationsStep',
  key: 'congratulations',
  sidebarData: {
    label: 'congratulations',
    icon: 'pennant',
    selected: true,
    finished: true
  }
}

</script>

<style lang="scss">
.amelia-v2-booking #amelia-container {
  .am-fs__main-content.am-fs__congrats {
    padding: 16px 16px 16px;
    height: calc(100% - 56px);
    margin-top:0
  }

  .am-fs-sb-atc {
    --am-c-sb-bgr-atc: var(--am-c-sb-bgr);
    background-image: linear-gradient(var(--am-c-sb-bgr-atc) var(--am-c-atc-bgr-coverage), transparent 20%);
  }

  .am-fs__congrats {
    &-main {
      margin-top: 22px;
      display: flex;
      flex-direction: column;
      align-items: center;
      font-family: var(--am-font-family);
      margin-bottom: 16px;

      & > * {
        $count: 5;
        @for $i from 0 through $count {
          &:nth-child(#{$i + 1}) {
            animation: 600ms cubic-bezier(.45,1,.4,1.2) #{$i*100}ms am-animation-slide-up;
            animation-fill-mode: both;
          }
        }
      }

      & img {
        width:54px;
        margin-bottom: 8px;
      }
      &-heading {
        margin-bottom: 2px;
        font-weight: 500;
        font-size: 18px;
        line-height: 28px;
        color: var(--am-c-atc-heading-text);
      }
      &-atc {
        margin-top: 16px
      }
      & span {
        color: var(--am-c-atc-heading-text-op40);
        font-weight: 400;
        font-size: 13px;
        line-height: 18px;
      }

      &-waiting-list-block {
        display: flex;
        flex-direction: column;
        align-items: center;
      }
    }
    &-info-mobile {
      padding: 16px;
      margin-top: 24px;
      border-radius: 6px;
      box-shadow: 0 2px 3px 2px rgba(26, 44, 55, 0.1);
    }
    &-info {
      --am-c-atc-main-bgr: var(--am-c-main-bgr);
      background-color: var(--am-c-atc-main-bgr);
      &-customer {
        &-border {
          border-top: 1px solid var(--am-c-atc-text-op30);
          padding-top: 16px;
        }

        & > div {
          $count: 20;
          @for $i from 0 through $count {
            &:nth-child(#{$i + 1}) {
              animation: 600ms cubic-bezier(.45,1,.4,1.2) #{$i*100}ms am-animation-slide-up;
              animation-fill-mode: both;
            }
          }
        }

        & div {
          display: flex;
          justify-content: space-between;
          font-weight: 400;
          font-size: 14px;
          margin-bottom: 12px;
          line-height: 20px;
          & span {
            color: var(--am-c-atc-text-op40);
          }
          & span:nth-child(2) {
            color: var(--am-c-atc-text);
          }
        }
      }
    }

    &-info-mobile-s {
      .am-fs__congrats-info-customer > div {
        flex-direction: column;
      }
    }
  }

  .am-skeleton-congratz {

    &-heading {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 13px;

      :first-child {
        width: 52px;
        height: 52px;
        margin-bottom: 9px;
      }

      :nth-child(2) {
        height: 28px;
        max-width: 132px;
        margin-bottom: 4px;
      }

      :last-child {
        max-width: 148px;
        height: 18px;
      }
    }

    &-booking-info {
      & > div {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;

        .el-skeleton__item {
          height: 20px;
        }

      }

      .am-customer-info {
        border-bottom: 1px solid var(--am-c-skeleton-op20);
        padding-bottom: 12px;
      }
    }
  }
}
</style>
