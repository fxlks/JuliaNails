<template>
  <el-form
    v-if="!loading"
    ref="employeeFormRef"
    :model="employeeFormData"
    label-position="top"
    class="am-caedo"
    :class="props.responsiveClass"
  >
    <StripeConnect
      v-if="
        amSettings.payments.stripe.enabled &&
        amSettings.payments.stripe.connect.enabled
      "
    />

    <div class="am-caepif__block" :class="props.responsiveClass">
      <template
        v-for="(item, name) in employeeDataFormConstruction"
        :key="name"
      >
        <component
          :is="item.template"
          ref="customerCollectorRef"
          v-model="employeeFormData[name]"
          v-bind="item.props"
          v-on="item.handlers ? item.handlers : {}"
        >
          <template v-if="item.slots && item.slots.default">
            <div v-html="item.slots.default" />
          </template>
        </component>
      </template>
    </div>

    <AmAlert
        v-if="alertVisibility"
        type="error"
        :show-border="true"
        :close-after="5000"
        @close="closeAlert"
        @trigger-close="closeAlert"
    >
      {{ message }}
    </AmAlert>

    <!-- Apple Calendar -->
    <div v-if="amSettings.appleCalendar" class="am-caedo__apple">
      <AmCollapse>
        <AmCollapseItem ref="appleVisibility" :side="true" :delay="500">
          <template #heading>
            {{ amLabels.apple_calendar_personal }}
          </template>
          <template #default>
            <div class="am-caedo__apple__connect">
              <el-form-item
                  :label="amLabels.apple_icloud_id"
                  class="am-caedo__apple__connect__item"
              >
                <AmInput
                    v-model="appleCalendarPersonal.iCloudId"
                    :disabled="isEmployeeConnectedToPersonalAppleCalendar"
                    :placeholder="amLabels.apple_icloud_id"
                />
              </el-form-item>
              <el-form-item
                  :label="amLabels.apple_app_specific_password"
                  class="am-caedo__apple__connect__item"
              >
                <AmInput
                  v-model="appleCalendarPersonal.appSpecificPassword"
                  type="password"
                  :show-password="true"
                  :disabled="isEmployeeConnectedToPersonalAppleCalendar"
                  :placeholder="amLabels.apple_app_specific_password"
                />
              </el-form-item>

              <AmButton
                @click="isEmployeeConnectedToPersonalAppleCalendar ?
                useEmployeeAppleDisconnect(store) :
                useEmployeeAppleConnect(store)"
              >
                {{ isEmployeeConnectedToPersonalAppleCalendar ?
                  amLabels.apple_disconnect :
                  amLabels.apple_connect
                }}
              </AmButton>
            </div>
          </template>
        </AmCollapseItem>
      </AmCollapse>
    </div>
    <!-- /Apple Calendar -->

  </el-form>
  <Skeleton v-else></Skeleton>
</template>

<script setup>
// * Import from Vue
import {computed, ref, inject, onBeforeMount, markRaw, onMounted} from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Import Form Templates
import { formFieldsTemplates } from '../../../../../../assets/js/common/formFieldsTemplates'
import AmButton from '../../../../../_components/button/AmButton.vue'
import StripeConnect from './StripeConnect.vue'

// * Composables
import {
  useGoogleConnect,
  useGoogleDisconnect,
} from '../../../../../../assets/js/common/integrationGoogle'
import {
  useOutlookConnect,
  useOutlookDisconnect,
} from '../../../../../../assets/js/common/integrationOutlook'
import Skeleton from '../../Authentication/parts/Skeleton.vue'
import AmInput from "../../../../../_components/input/AmInput.vue";
import AmCollapse from "../../../../../_components/collapse/AmCollapse.vue";
import AmCollapseItem from "../../../../../_components/collapse/AmCollapseItem.vue";
import {
  isEmployeeConnectedToPersonalAppleCalendar, useAppleSync
} from "../../../../../../assets/js/common/integrationApple";
import httpClient from "../../../../../../plugins/axios";
import AmAlert from "../../../../../_components/alert/AmAlert.vue";

// * Alert block
let message = ref('')
let messageType = ref('error')
let alertVisibility = ref(false)

function closeAlert () {
  alertVisibility.value = false
  message.value = ''
}

// * Props
const props = defineProps({
  responsiveClass: {
    type: String,
    default: '',
  },
})

// * Store
const store = useStore()

// * Root Urls
const baseUrls = inject('baseUrls')

// * Settings
const amSettings = inject('settings')

// * Labels
let amLabels = inject('amLabels')

let loading = computed(
  () =>
    store.getters['auth/getGoogleLoading'] ||
    store.getters['auth/getOutlookLoading'] ||
    store.getters['auth/getAppleLoading'] ||
    store.getters['auth/getStripeLoading'] ||
    store.getters['auth/getZoomLoading']
)

// * Zoom Users Options
let zoomOptions = computed(() => {
  let users = store.getters['auth/getZoomUsers']

  if (users.length) {
    return users.map((user) => {
      return {
        value: user.id,
        label: `${user.first_name} ${user.last_name} (${user.email})`,
      }
    })
  }

  return []
})

// * Google Calendar Options
let googleCalendarOptions = computed(() => {
  let calendars = store.getters['auth/getGoogleCalendars']

  if (calendars.length) {
    return calendars.map((calendar) => {
      return {
        value: calendar.calendarId ? calendar.calendarId : calendar.id,
        label: calendar.summary,
      }
    })
  }

  return []
})

// * Outlook Calendar Options
let outlookCalendarOptions = computed(() => {
  let calendars = store.getters['auth/getOutlookCalendars']

  if (calendars.length) {
    return calendars.map((calendar) => {
      return {
        value: calendar.id,
        label: calendar.name,
      }
    })
  }

  return []
})

// * Apple Calendar Options
let appleCalendarOptions = computed(() => {
  let calendars = store.getters['auth/getAppleCalendars']
  if (calendars.length) {
    return calendars.map((calendar) => {
      return {
        value: calendar.id,
        label: calendar.name,
      }
    })
  }
  return []
})

let appleVisibility = ref(null)

// * Apple Calendar iCloudId and App-specific Password
let appleCalendarPersonal = ref({
  iCloudId: computed({
    get: () => store.getters['employee/getEmployeeAppleCalendarICloudId'],
    set: (val) => {
      store.commit('employee/setEmployeeAppleCalendarICloudId', val ? val : '')
    }
  }),
  appSpecificPassword: computed({
    get: () => store.getters['employee/getEmployeeAppleCalendarAppSpecificPassword'],
    set: (val) => {
      store.commit('employee/setEmployeeAppleCalendarAppSpecificPassword', val ? val : null)
    }
  })
})

// * Google Button Text
let googleBtnText = computed(() => {
  return store.getters['employee/getGoogleToken']
    ? amLabels.google_sign_out
    : amLabels.google_sign_in
})

// * Outlook Button Text
let outlookBtnText = computed(() => {
  return store.getters['employee/getOutlookToken']
    ? amLabels.outlook_sign_out
    : amLabels.outlook_sign_in
})

// * Form Data
let employeeFormData = ref({
  googleId: computed({
    get: () => {
      return store.getters['employee/getGoogleToken']
        ? store.getters['employee/getGoogleCalendarId']
        : store.getters['employee/getUserTableGoogleCalendarId']
    },
    set: (val) => {
      if (store.getters['employee/getGoogleToken']) {
        store.commit('employee/setGoogleCalendarId', val || '')
      } else {
        store.commit('employee/setEmployeeGoogleCalendarId', val || '')
      }
    },
  }),
  googleBtn: '',
  outlookId: computed({
    get: () => {
      return store.getters['employee/getOutlookToken']
        ? store.getters['employee/getOutlookCalendarId']
        : store.getters['employee/getUserTableOutlookCalendarId']
    },
    set: (val) => {
      if (store.getters['employee/getOutlookToken']) {
        store.commit('employee/setOutlookCalendarId', val || '')
      } else {
        store.commit('employee/setEmployeeOutlookCalendarId', val || '')
      }
    },
  }),
  outlookBtn: '',
  zoomUserId: computed({
    get: () => store.getters['employee/getZoomUserId'],
    set: (val) => {
      store.commit('employee/setZoomUserId', val ? val : '')
    },
  }),
  appleId: computed({
    get: () => store.getters['employee/getAppleCalendarId'],
    set: (val) => {
      store.commit('employee/setAppleCalendarId', val ? val : '')
    },
  }),
})

let employeeDataFormConstruction = ref({
  googleId: {
    template: formFieldsTemplates.select,
    props: {
      itemName: 'googleId',
      label: amLabels.google_calendar,
      placeholder: '',
      class: computed(() => `am-caepif__item ${props.responsiveClass}`),
      disabled: computed(() => !store.getters['employee/getGoogleToken']),
      clearable: false,
      options: googleCalendarOptions.value,
      loading: computed(() => store.getters['auth/getGoogleLoading']),
      loadingIcon: 'loading',
    },
  },
  googleBtn: {
    template: markRaw(AmButton),
    props: {
      class: computed(
        () =>
          `am-caepif__item am-google-calendar-button ${props.responsiveClass}`
      ),
      category: computed(() => 'primary'),
      disabled: computed(
        () => !!store.getters['employee/getUserTableGoogleCalendarId']
      ),
    },
    slots: {
      default: computed(() => {
        return `<img src="${baseUrls.wpAmeliaPluginURL}/v3/src/assets/img/cabinet/google-button.svg" alt="Google" />${googleBtnText.value}`
      }),
    },
    handlers: {
      click: () => {
        store.getters['employee/getGoogleToken']
          ? useGoogleDisconnect(store)
          : useGoogleConnect(store)
      },
    },
  },
  outlookId: {
    template: formFieldsTemplates.select,
    props: {
      itemName: 'outlookId',
      label: amLabels.outlook_calendar,
      placeholder: '',
      class: computed(() => `am-caepif__item ${props.responsiveClass}`),
      disabled: computed(() => !store.getters['employee/getOutlookToken']),
      clearable: false,
      options: outlookCalendarOptions.value,
      loading: computed(() => store.getters['auth/getOutlookLoading']),
      loadingIcon: 'loading',
    },
  },
  outlookBtn: {
    template: markRaw(AmButton),
    props: {
      class: computed(
        () => `am-caepif__item am-outlook-button ${props.responsiveClass}`
      ),
      category: computed(() => 'primary'),
      disabled: computed(
        () => !!store.getters['employee/getUserTableOutlookCalendarId']
      ),
    },
    slots: {
      default: computed(() => {
        return `<div class="am-outlook-img"><img src="${baseUrls.wpAmeliaPluginURL}/v3/src/assets/img/cabinet/outlook-calendar.png" alt="Outlook"/></div>${outlookBtnText.value}`
      }),
    },
    handlers: {
      click: () => {
        store.getters['employee/getOutlookToken']
          ? useOutlookDisconnect(store)
          : useOutlookConnect(store)
      },
    },
  },
  zoomUserId: {
    template: formFieldsTemplates.select,
    props: {
      itemName: 'zoomUserId',
      label: amLabels.zoom_user,
      placeholder: amLabels.zoom_user_placeholder,
      class: computed(() => `am-caepif__item ${props.responsiveClass}`),
      options: zoomOptions.value,
    },
  },
  appleId: {
    template: formFieldsTemplates.select,
    props: {
      itemName: 'appleId',
      label: amLabels.apple_calendar,
      placeholder: amLabels.apple_calendar_placeholder,
      class: computed(() => `am-caepif__item ${props.responsiveClass}`),
      disabled: false,
      clearable: computed(() => !isEmployeeConnectedToPersonalAppleCalendar.value),
      options: computed(() => appleCalendarOptions.value),
      loading: computed(() => store.getters['auth/getAppleLoading']),
      loadingText: 'Loading...',
    },
  },
})

onMounted(() => {
  if (amSettings.appleCalendar && appleVisibility.value) {
    appleVisibility.value.contentVisibility = isEmployeeConnectedToPersonalAppleCalendar.value
  }
})

onBeforeMount(() => {
  if (!amSettings.zoom.enabled) {
    delete employeeDataFormConstruction.value.zoomUserId
  }

  if (!amSettings.googleCalendar.enabled) {
    delete employeeDataFormConstruction.value.googleId
  }

  if (!amSettings.googleCalendar.enabled) {
    delete employeeDataFormConstruction.value.googleBtn
  }

  if (!amSettings.outlookCalendar.enabled) {
    delete employeeDataFormConstruction.value.outlookId
  }

  if (!amSettings.outlookCalendar.enabled) {
    delete employeeDataFormConstruction.value.outlookBtn
  }

  if (!amSettings.appleCalendar) {
    delete employeeDataFormConstruction.value.appleId
  }
})

function useEmployeeAppleConnect (store) {
  const data = {
    iCloudId: store.getters['employee/getEmployeeAppleCalendarICloudId'],
    appSpecificPassword: store.getters['employee/getEmployeeAppleCalendarAppSpecificPassword']
  }

  httpClient.post(
      '/apple/connect/' + store.getters['employee/getId'],
      {
        employeeAppleCalendar: data
      }
  ).then(() => {
    store.commit('employee/setEmployeeAppleCalendarICloudId', data.iCloudId)
    store.commit('employee/setEmployeeAppleCalendarAppSpecificPassword', data.appSpecificPassword)
    useAppleSync(store)
  }).catch((error) => {
    alertVisibility.value = true
    message.value = error.response.data.message
    messageType.value = 'error'
  }).finally(() => {
    alertVisibility.value = true
  })
}

function useEmployeeAppleDisconnect (store) {
  store.commit('auth/setAppleLoading', true)
  httpClient.post(
      '/apple/disconnect-employee/' + store.getters['employee/getId']
  ).then(() => {
    store.commit('employee/setEmployeeAppleCalendarICloudId', null)
    store.commit('employee/setEmployeeAppleCalendarAppSpecificPassword', null)
     isEmployeeConnectedToPersonalAppleCalendar.value = false
    useAppleSync(store)
    appleCalendarOptions.value = store.getters['auth/getAppleCalendars']
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    store.commit('auth/setAppleLoading', false)
  })
}
</script>

<style lang="scss">
@mixin am-cabinet-profile {
  .am-caedo {

    &__apple {
      &__connect {
        display: flex;
        flex-direction: column;
        width: 100%;
        padding: 16px;
        border-radius: 8px;
        background-color: rgba(26, 44, 55, 0.03);

        &__item {
          .el-form-item__label {
            padding-bottom: 4px;
          }
        }
      }
      .am-collapse-item__heading {
        height: 44px;
      }
    }

    .am-google-calendar-button {
      padding: 0 2px 0 30px;
      height: 40px;
      border: 1px solid #747775 !important;
      background-color: #ffffff !important;
      -webkit-border-radius: 1px;
      border-radius: 1px;
      -webkit-box-sizing: border-box;
      box-sizing: border-box;
      -webkit-transition: background-color 0.218s, border-color 0.218s,
        box-shadow 0.218s;
      transition: background-color 0.218s, border-color 0.218s,
        box-shadow 0.218s;
      -webkit-user-select: none;
      -webkit-appearance: none;
      background-image: none;
      cursor: pointer;
      outline: none;
      overflow: hidden;
      position: relative;
      text-align: center;
      vertical-align: middle;
      white-space: nowrap;
      margin: 0;

      &:hover {
        box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3),
          0 1px 3px 1px rgba(60, 64, 67, 0.15) !important;
        background-color: rgba(48, 48, 48, 0.08) !important;
      }

      .am-button__inner {
        color: #131314;

        img {
          position: absolute;
          left: 10px;
          top: 50%;
          transform: translateY(-50%);
          width: 20px;
          height: 20px;
        }
      }
    }

    .am-outlook-button {
      position: relative;
      padding-left: 30px;
      padding-right: 2px;

      .am-outlook-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 40px;
        height: 100%;
        border-radius: 4px 0 0 4px;
        background-color: #fff;

        img {
          position: absolute;
          top: 50%;
          transform: translateY(-50%);
          left: 10px;
          width: 20px;
          height: 20px;
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-profile;
}
</style>
