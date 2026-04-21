<template>
  <div
    v-if="!loading"
    class="am-capei"
    :class="props.responsiveClass"
    :style="cssVars"
  >
    <el-tabs v-model="activeTab" class="am-ap__tabs">
      <!-- Details -->
      <el-tab-pane :label="amLabels.details" name="details">
        <EventDetails
          ref="eventDetailsRef"
        />
      </el-tab-pane>
      <!-- /Details -->

      <!-- Periods -->
      <el-tab-pane :label="amLabels.periods" name="periods">
        <EventPeriods
          ref="eventPeriodsRef"
          :page-width="props.pageWidth"
        />
      </el-tab-pane>
      <!-- /Periods -->

      <!-- Recurring -->
      <el-tab-pane v-if="!licence.isStarter" :label="amLabels.recurring" name="recurring">
        <EventRecurring
          ref="eventRecurringRef"
          :page-width="props.pageWidth"
          :recurring-until="recurringUntil"
        />
      </el-tab-pane>
      <!-- /Recurring -->

      <!-- Pricing -->
      <el-tab-pane :label="amLabels.pricing" name="pricing">
        <EventPricing
          ref="eventPricingRef"
          :page-width="props.pageWidth"
        />
      </el-tab-pane>
      <!-- /Pricing -->

      <!-- Customize -->
      <el-tab-pane :label="amLabels.customize" name="customize">
        <EventCustomize/>
      </el-tab-pane>
      <!-- /Customize -->

      <!-- Waiting List -->
      <el-tab-pane
        v-if="amSettings.featuresIntegrations.waitingList.enabled && !licence.isStarter && !licence.isBasic"
        :label="amLabels.waiting_list"
        name="waitingList"
      >
        <EventWaitingList
          :page-width="props.pageWidth"
        />
      </el-tab-pane>
      <!-- /Waiting List -->

      <!-- Settings -->
      <el-tab-pane :label="amLabels.settings" name="settings">
        <EventSettings/>
      </el-tab-pane>
      <!-- /Settings -->
    </el-tabs>

    <div
      class="am-capei__footer"
      :class="[{'am-capei__footer_new': !store.getters['event/getId']}, props.responsiveClass]"
    >
      <AmButton
        v-if="store.getters['event/getId']"
        class="am-capei__footer-duplicate"
        :icon-only="true"
        icon="duplicate"
        type="plain"
        category="secondary"
        @click="duplicateEvent"
      />
      <div class="am-capei__footer-actions">
        <AmButton
          category="secondary"
          :size="'default'"
          :type="'plain'"
          @click="() => {emits('close')}"
        >
          {{ amLabels.cancel }}
        </AmButton>
        <AmButton :size="'default'" @click="validateSave">
          {{ amLabels.save }}
        </AmButton>
      </div>
    </div>
  </div>
  <Skeleton v-else></Skeleton>

  <CancelPopup
    :visibility="followingPopUpVisible && recurringUntil !== null"
    :title="amLabels.confirm"
    :description="amLabels.confirm_save_following"
    :close-btn-text="amLabels.save_single"
    :confirm-btn-text="amLabels.update_following"
    :customized-options="{cancelBtn: {type: 'plain'}, confirmBtn: {type: ''}}"
    @decline="saveEvent(false)"
    @confirm="saveEvent(true)"
    @close="followingPopUpVisible = false"
  >
  </CancelPopup>

  <CancelPopup
    :visibility="conflictPopUpVisible"
    :title="amLabels.confirm"
    :description="amLabels.confirm_save_conflict"
    :close-btn-text="amLabels.cancel"
    :confirm-btn-text="amLabels.confirm"
    :customized-options="{cancelBtn: {buttonType: 'plain'}, confirmBtn: {buttonType: 'filled'}}"
    @decline="conflictPopUpVisible = false"
    @confirm="validateFollowingUpdate"
    @close="conflictPopUpVisible = false"
  >
  </CancelPopup>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  inject,
  computed,
  onMounted,
} from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Import from library
import moment from 'moment/moment'
import httpClient from '../../../../../../plugins/axios'

// * Composables
import { useAuthorizationHeaderObject } from '../../../../../../assets/js/public/panel'
import { useColorTransparency } from '../../../../../../assets/js/common/colorManipulation'
import { useBackEvent, useEventPeriods } from '../../../../../../assets/js/common/events'
import { useUpdateStashEntities } from "../../../../../../assets/js/common/settings";

// * Components
import AmButton from '../../../../../_components/button/AmButton.vue'

// * Dedicated components
import Skeleton from '../../Authentication/parts/Skeleton.vue'
import EventDetails from './EventDetails.vue'
import EventPeriods from './EventPeriods.vue'
import EventRecurring from './EventRecurring.vue'
import EventPricing from './EventPricing.vue'
import EventCustomize from './EventCustomize.vue'
import EventWaitingList from './EventWaitingList.vue'
import EventSettings from './EventSettings.vue'
import CancelPopup from '../../parts/CancelPopup.vue'

// * Component properties
let props = defineProps({
  responsiveClass: {
    type: String,
    default: '',
  },
  pageWidth: {
    type: Number,
  },
})

let store = useStore()

// * Components emits
let emits = defineEmits(['close', 'save', 'duplicate'])

// * Labels
let amLabels = inject('amLabels')

// * Root Settings
const amSettings = inject('settings')

// * Plugin Licence
let licence = inject('licence')

/********
 * Tabs *
 ********/
const activeTab = ref('details')

let eventDetailsRef = ref(null)

let eventPeriodsRef = ref(null)

let eventRecurringRef = ref(null)

let eventPricingRef = ref(null)

let recurringUntil = ref(null)

/********
 * Form *
 ********/
let loading = ref(true)

let followingPopUpVisible = ref(false)

let conflictPopUpVisible = ref(false)

function duplicateEvent () {
  emits(
    'duplicate',
    Object.assign(JSON.parse(JSON.stringify(store.getters['event/getEvent'])), {id: null, bookings: []})
  )
}

async function validateSave() {
  let validData = true

  // Validate details form
  await new Promise((resolve) => {
    eventDetailsRef.value?.detailsFormRef.validate((valid) => {
      if (!valid) {
        activeTab.value = 'details'
        validData = false
      }
      resolve()
    })
  })

  if (!validData) return

  // Validate periods form
  await new Promise((resolve) => {
    eventPeriodsRef.value?.periodsFormRef.validate((valid) => {
      if (!valid) {
        activeTab.value = 'periods'
        validData = false
      }
      resolve()
    })
  })

  if (!validData) return

  // Validate recurring form if enabled
  if (store.getters['event/getRecurringEnabled']) {
    await new Promise((resolve) => {
      eventRecurringRef.value?.recurringFormRef.validate((valid) => {
        if (!valid) {
          activeTab.value = 'recurring'
          validData = false
        }
        resolve()
      })
    })

    if (!validData) return
  }

  // All validations passed, now check calendar conflicts
  validateCalendarConflict((haveConflict) => {
    if (haveConflict) {
      conflictPopUpVisible.value = true
    } else {
      validateFollowingUpdate()
    }
  })
}

function saveEvent(updateFollowing) {
  loading.value = true

  httpClient
    .post(
      '/events' + (store.getters['event/getId'] ? '/' + store.getters['event/getId'] : ''),
      Object.assign(
        useBackEvent(store),
        {
          applyGlobally: updateFollowing,
        }
      ),
      Object.assign(useAuthorizationHeaderObject(store), {
        params: { source: 'cabinet-provider' },
      })
    )
    .then(() => {
      useUpdateStashEntities(store)

      close('save')
    })
    .catch((e) => {
      console.log(e)
    })
    .finally(() => {
      loading.value = false
    })
}

function validateFollowingUpdate() {
  if (recurringUntil.value) {
    loading.value = false
    conflictPopUpVisible.value = false
    followingPopUpVisible.value = true
  } else {
    saveEvent(false)
  }
}

function validateCalendarConflict(callback) {
  let organizer = store.getters['event/getOrganizerId'] ? store.getters['employee/getEmployee'] : null

  if (organizer && (organizer.googleCalendar || organizer.outlookCalendar)) {
    loading.value = true

    let providers = store.getters['event/getProviders'].map(i => new Object({id: i.id}))

    httpClient
      .post(
        '/events/calendar',
        {
          providers: store.getters['event/getProviders'].map(i => i.id).indexOf(organizer.id) === -1
            ? providers.concat([{id: organizer.id}])
            : providers,
          periods: useEventPeriods(store),
          eventIds: [store.getters['event/getId'], store.getters['event/getParentId']],
          recurring: store.getters['event/getRecurring'],
        },
        Object.assign(
          useAuthorizationHeaderObject(store),
          {
            params: { source: 'cabinet-provider' },
          }
        )
      )
      .then(() => {
        callback(false)
      })
      .catch(() => {
        callback(true)

        loading.value = false
      })
  } else {
    callback(false)
  }
}

function close(type) {
  store.commit('event/setActive', false)

  emits(type)
}

onMounted(() => {
  if (store.getters['event/getId'] && store.getters['event/getRecurringUntil']) {
    recurringUntil.value = moment(store.getters['event/getRecurringUntil'].toISOString().split('T')[0])
  }

  store.commit('event/setActive', true)

  loading.value = false
})

// * Colors
let amColors = inject('amColors')
// * CSS Variables
let cssVars = computed(() => {
  return {
    // * Event item css variables
    '--am-c-capei-text': amColors.value.colorMainText,
    '--am-c-capei-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
    '--am-c-capei-primary': amColors.value.colorPrimary,

    // * Customer css variables
    '--am-c-cust-no1': amColors.value.colorMainText,
    '--am-c-cust-no1-bgr': useColorTransparency(
      amColors.value.colorMainText,
      0.1
    ),
    '--am-c-cust-no2': amColors.value.colorWarning,
    '--am-c-cust-no2-bgr': useColorTransparency(
      amColors.value.colorWarning,
      0.1
    ),
    '--am-c-cust-no3': amColors.value.colorError,
    '--am-c-cust-no3-bgr': useColorTransparency(amColors.value.colorError, 0.1),
    '--am-c-cust-link': useColorTransparency(amColors.value.colorMainText, 0.5),
    '--am-c-cust-text': amColors.value.colorMainText,
  }
})
</script>

<script>
export default {
  name: 'CabinetEvent',
}
</script>

<style lang="scss">
@mixin am-cabinet-event {
  // am    - amelia
  // capei - cabinet-panel-event-item
  .am-capei {

    // Divider
    .am-divider {
      width: 100%;
      height: 1px;
      background-color: var(--am-c-inp-border);
      margin: 6px 0;
    }

    &__footer {
      width: calc(100% - 64px);
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: absolute;
      bottom: 16px;
      right: 32px;

      &.am-rw- {
        &480 {
          width: calc(100% - 32px);
          right: 16px;
        }
      }

      &-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 20px;
      }

      &-duplicate {
        font-size: 24px !important;
      }
    }

    &__footer_new {
      justify-content: flex-end;
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-event;
}
</style>
