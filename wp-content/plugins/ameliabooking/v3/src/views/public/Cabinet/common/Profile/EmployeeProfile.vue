<template>
  <div
    ref="pageContainer"
    class="am-caep"
    :style="cssVars"
  >
    <div
      v-if="ready && !loading && !integrationsLoading"
      class="am-caep__inner"
      :class="responsiveClass"
    >
      <AmAlert
        v-if="alertVisibility"
        :type="messageType"
        :show-border="true"
        :close-after="5000"
        custom-class="am-capi__alert"
        @close="closeAlert"
        @trigger-close="closeAlert"
      >
        <template #title>
          <span class="am-icon-checkmark-circle-full"></span> {{ message }}
        </template>
      </AmAlert>

      <el-tabs
        v-model="activeTab"
      >
        <el-tab-pane
          :label="amLabels.details"
          name="details"
          @click="tabClick"
        >
          <Details
            ref="detailsRef"
            :responsive-class="responsiveClass"
          />
        </el-tab-pane>

        <el-tab-pane
          v-if="amSettings.roles.allowConfigureServices"
          :label="amLabels.heading_services"
          name="services"
        >
          <Services
            :responsive-class="responsiveClass"
          />
        </el-tab-pane>

        <el-tab-pane
          v-if="amSettings.roles.allowConfigureSchedule"
          :label="amLabels.working_hours"
          name="workingHours"
        >
          <WeekDays
            :responsive-class="responsiveClass"
          />
        </el-tab-pane>

        <el-tab-pane
          v-if="amSettings.roles.allowConfigureSpecialDays"
          :label="amLabels.special_days"
          name="specialDays"
        >
          <SpecialDays
            ref="specialDaysRef"
            :responsive-class="responsiveClass"
          />
        </el-tab-pane>

        <el-tab-pane
          v-if="amSettings.roles.allowConfigureDaysOff"
          :label="amLabels.days_off"
          name="daysOff"
        >
          <DaysOff
            ref="daysOffRef"
            :responsive-class="responsiveClass"
          />
        </el-tab-pane>

        <el-tab-pane
          v-if="changePasswordEnabled"
          :label="amLabels.password"
          name="password"
        >
          <ChangePassword
            v-if="!loading"
            ref="changePassRef"
          />
          <ProfileSkeleton
            v-else
            :item-direction="'column'"
            :count="2"
          ></ProfileSkeleton>
        </el-tab-pane>

        <el-tab-pane
          v-if="amSettings.zoom.enabled ||
          amSettings.googleCalendar.enabled ||
          amSettings.outlookCalendar.enabled ||
          amSettings.appleCalendar ||
          (amSettings.payments.stripe.enabled && amSettings.payments.stripe.connect.enabled)"
          :label="amLabels.integrations_settings"
          name="integrations"
        >
          <Integrations
            :responsive-class="responsiveClass"
          />
        </el-tab-pane>
      </el-tabs>

      <!-- Profile Footer -->
      <ProfileFooter
        :tab-active="activeTab"
        :password-active="changePasswordEnabled"
        @change-password="changePassword"
        @save-password="savePassword"
        @save-changes="saveEmployee"
      />
      <!-- /Profile Footer -->
    </div>
    <Skeleton v-else></Skeleton>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  inject,
  computed,
  provide,
} from "vue";

import { useElementSize } from "@vueuse/core";

// * Import from Vuex
import { useStore } from "vuex";

// * Import for axios
import httpClient from "../../../../../plugins/axios";

// * Composables
import {
  useAuthorizationHeaderObject,
} from "../../../../../assets/js/public/panel";
import {
  useBackendEmployee,
} from "../../../../../assets/js/common/employee";
import {
  useResponsiveClass,
} from "../../../../../assets/js/common/responsive";
import {
  useColorTransparency
} from "../../../../../assets/js/common/colorManipulation";

// * Dedicated components
import Skeleton from "../Authentication/parts/Skeleton.vue";
import Details from "./parts/Details.vue";
import Services from "./parts/Services.vue";
import WeekDays from "./parts/WeekDays.vue";
import SpecialDays from "./parts/SpecialDays.vue";
import DaysOff from "./parts/DaysOff.vue";
import Integrations from "./parts/Integrations.vue";
import ProfileFooter from "./parts/ProfileFooter.vue";
import ChangePassword from "./parts/ChangePassword.vue";
import AmAlert from "../../../../_components/alert/AmAlert.vue";
import ProfileSkeleton from "./parts/ProfileSkeleton.vue";

// * Store
let store = useStore()

// * Settings
const amSettings = inject('settings')

// * Labels
let amLabels = inject('amLabels')

// * Page Content width
let pageContainer = ref(null)
const { width: pageWidth } = useElementSize(pageContainer)

let responsiveClass = computed(() => {
  return useResponsiveClass(pageWidth.value)
})

provide('pageWidth', pageWidth)

let ready = computed(() => store.getters['entities/getReady'])

/********
 * Tabs *
 ********/
const activeTab = ref('details')

// * Success message
let message = ref('')

let messageType = ref('success')

// * Change Password Alert visibility
let alertVisibility = ref(false)

function tabClick () {
  alertVisibility.value = false
}

function closeAlert () {
  alertVisibility.value = false
  message.value = ''
}

/***********
 * Details *
 ***********/
let detailsRef = ref(null)
let daysOffRef = ref(null)
let specialDaysRef = ref(null)

let employee = computed(() => {
  return store.getters['employee/getEmployee']
})

let integrationsLoading = computed(
  () => store.getters['auth/getGoogleLoading'] ||
    store.getters['auth/getOutlookLoading'] ||
    store.getters['auth/getAppleLoading'] ||
    store.getters['auth/getStripeLoading'] ||
    store.getters['auth/getZoomLoading']
)

let loading = ref(false)

let timeZone = inject('timeZone')

function saveEmployee () {
  Promise.all([
    detailsRef.value?.employeeFormRef ? detailsRef.value.employeeFormRef.validate().then(() => true).catch(() => false) : Promise.resolve(true),
    daysOffRef.value ? daysOffRef.value.validate() : Promise.resolve(true),
    specialDaysRef.value ? specialDaysRef.value.validate() : Promise.resolve(true),
  ]).then(([detailsValid, daysOffValid, specialDaysValid]) => {
    if (!detailsValid) {
      activeTab.value = 'details'
      return
    }

    if (!daysOffValid) {
      activeTab.value = 'daysOff'
      return
    }

    if (!specialDaysValid) {
      activeTab.value = 'specialDays'
      return
    }

    loading.value = true

    httpClient.post(
      '/users/providers/' + employee.value.id,
      useBackendEmployee(store, timeZone.value),
      Object.assign(useAuthorizationHeaderObject(store), {params: {source: 'cabinet-provider'}})
    ).then((response) => {
      store.commit('employee/setSavedSpecialDayList', JSON.parse(JSON.stringify(response.data.data.user.specialDayList)))
      store.commit('employee/setSavedDayOffList', JSON.parse(JSON.stringify(response.data.data.user.dayOffList)))

      if (daysOffRef.value) daysOffRef.value.commitEditedState()
      if (specialDaysRef.value) specialDaysRef.value.commitEditedState()

      message.value = amLabels.profile_data_success
      messageType.value = 'success'
    }).catch((error) => {
      message.value = error.response.data.message
      messageType.value = 'error'
    }).finally(() => {
      alertVisibility.value = true
      loading.value = false
    })
  })
}

/***********
 * Password *
 ***********/
let changePassRef = ref(null)
let changePasswordEnabled = ref(false)

function changePassword () {
  changePasswordEnabled.value = true
  activeTab.value = 'password'
}

function savePassword () {
  changePassRef.value.passFormRef.validate((valid) => {
    if (valid) {
      let user = store.getters['auth/getProfile']

      loading.value = true

      httpClient.post(
        '/users/providers/' + user.id,
        {password: store.getters['auth/getNewPassword']},
        useAuthorizationHeaderObject(store)
      ).then(() => {
        message.value = amLabels.password_success

        changePasswordEnabled.value = false

        activeTab.value = 'details'

        store.commit('auth/setNewPassword', '')
        store.commit('auth/setConfirmPassword', '')
      }).catch(() => {
        alertVisibility.value = true
      }).finally(() => {
        loading.value = false
      })
    }
  })
}

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-caep-primary': amColors.value.colorPrimary,
    '--am-c-caep-text': amColors.value.colorMainText,
    '--am-c-caep-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1)
  }
})
</script>

<script>
export default {
  name: 'CabinetEmployeeProfile'
}
</script>

<style lang="scss">
@mixin am-cabinet-profile {
  // am    - amelia
  // caep - cabinet employee profile
  .am-caep {
    .el-skeleton {
      padding: 32px;
    }

    &__inner {
      display: block;
      padding: 16px 32px;

      .el-skeleton {
        padding-right: 32px;
      }

      &.am-rw- {
        &480 {
          padding: 16px;
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-profile;
}
</style>
