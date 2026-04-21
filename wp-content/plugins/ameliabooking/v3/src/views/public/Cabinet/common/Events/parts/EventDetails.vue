<template>
  <el-form
    ref="detailsFormRef"
    :model="formData"
    :rules="rules"
    class="am-capei-def"
  >
    <!-- Event Name -->
    <el-form-item
      :prop="'name'"
      :label="`${amLabels.name}:`"
      class="am-capei-def__item"
    >
      <AmInput
        v-model="formData.name"
        :placeholder="amLabels.enter_event_name"
      />
    </el-form-item>
    <!-- /Event Name -->

    <!-- Event Location -->
    <el-form-item
      v-if="!licence.isStarter && locations.length > 1"
      :label="`${amLabels.event_select_address}:`"
      class="am-capei-def__item"
    >
      <AmSelect
        v-model="formData.locationId"
        :placeholder="amLabels.select"
      >
        <AmOption
          :key="0"
          :value="0"
          :label="amLabels.event_custom_address"
        />
        <AmOption
          v-for="entity in locations"
          :key="entity.id"
          :value="entity.id"
          :label="entity.name"
        />
      </AmSelect>
    </el-form-item>
    <!-- /Event Location -->

    <!-- Custom Location -->
    <el-form-item
      v-if="!formData.locationId"
      :label="`${amLabels.event_custom_address}:`"
      class="am-capei-def__item"
    >
      <AmInput
        v-model="formData.customLocation"
      />
    </el-form-item>
    <!-- /Custom Location -->

    <!-- Event Organizer -->
    <el-form-item
      v-if="amSettings.zoom.enabled"
      :label="`${amLabels.zoom_user}:`"
      class="am-capei-def__item"
    >
      <AmSelect
        v-model="formData.zoomUserId"
        clearable
        :placeholder="amLabels.zoom_user_placeholder"
      >
        <AmOption
          v-for="entity in zoomOptions"
          :key="entity.value"
          :value="entity.value"
          :label="entity.label"
        />
      </AmSelect>
    </el-form-item>
    <!-- /Event Organizer -->

    <!-- Event Organizer -->
    <el-form-item
      v-if="!licence.isStarter &&
        (amSettings.googleCalendar.enabled || amSettings.outlookCalendar.enabled || amSettings.appleCalendar) &&
        (store.getters['employee/getGoogleCalendarId'] || store.getters['employee/getOutlookCalendarId'] || store.getters['employee/getAppleCalendarId'])"
      class="am-capei-def__item am-capei-def__item-tool"
    >
      <AmSwitch
        v-model="isOrganizer"
        :disabled="formData.organizerId && formData.organizerId !== store.getters['auth/getProfile'].id"
        :active-text="amLabels.event_organizer"
        :aria-label="amLabels.event_organizer"
        @change="toggleOrganizer"
      />
      <el-tooltip
        effect="dark"
        placement="top"
      >
        <template #content>
          <div v-html="amLabels.event_organizer_tooltip"></div>
        </template>
        <IconComponent class="am-tooltip__trigger" icon="info-reverse" />
      </el-tooltip>
    </el-form-item>
    <!-- /Event Organizer -->

    <!-- Event Tags -->
    <el-form-item
      :label="`${amLabels.event_tags}:`"
      class="am-capei-def__item"
    >
      <AmSelect
        v-model="formData.tags"
        clearable
        filterable
        multiple
        :allow-create="true"
        :placeholder="amLabels.event_tags_select_or_create"
        :filter-method="filterTags"
      >
        <AmOption
          v-for="(entity, index) in filteredTags"
          :key="index"
          :value="entity.name"
          :label="entity.name"
        />
      </AmSelect>
    </el-form-item>
    <!-- /Event Tags -->

    <!-- Notify Participants -->
    <el-form-item class="am-capei-def__item am-capei-def__item-tool">
      <AmSwitch
        v-model="formData.notifyParticipants"
        :active-text="amLabels.notify_attendees"
        :aria-label="amLabels.notify_attendees"
      />
      <el-tooltip
        effect="dark"
        placement="top"
      >
        <template #content>
          <div v-html="amLabels.notify_attendees_tooltip"></div>
        </template>
        <IconComponent class="am-tooltip__trigger" icon="info-reverse" />
      </el-tooltip>

    </el-form-item>

    <!-- Event Description -->
    <el-form-item
      :prop="'description'"
      :label="amLabels.description_colon"
      class="am-capei-def__item"
    >
      <DescriptionEditor
        v-model="formData.description"
        :mode="store.getters['event/getDescriptionMode']"
        @set-mode="(val) => store.commit('event/setDescriptionMode', val)"
      />
    </el-form-item>
  </el-form>
</template>

<script setup>
// * Import form Vue
import {
  computed,
  ref,
  inject,
} from "vue";

// * Import Vuex
import { useStore } from "vuex";

// * Import Components
import AmInput from "../../../../../_components/input/AmInput.vue";
import AmSelect from "../../../../../_components/select/AmSelect.vue";
import AmOption from "../../../../../_components/select/AmOption.vue";
import AmSwitch from "../../../../../_components/switch/AmSwitch.vue";
import IconComponent from "../../../../../_components/icons/IconComponent.vue";
import DescriptionEditor from "../../parts/DescriptionEditor.vue";

// * Store
let store = useStore();

// * Labels
let amLabels = inject('amLabels')

// * Plugin Licence
let licence = inject('licence')

// * Settings
const amSettings = inject('settings')

let locations = ref(store.getters['entities/getLocations'])

let tags = ref(store.getters['entities/getTags'])

let queryTags = ref('')
function filterTags (query) {
  queryTags.value = query.toLowerCase()
}

let filteredTags = computed(() => {
  if (queryTags.value) {
    return tags.value.filter((tag) => {
      return tag.name.toLowerCase().includes(queryTags.value)
    })
  }

  return tags.value
})

// * Form Reference
let detailsFormRef = ref(null)

// * Form data
let formData = ref({
  name: computed({
    get: () => store.getters['event/getName'],
    set: (val) => {
      store.commit('event/setName', val)
    }
  }),
  locationId: computed({
    get: () => store.getters['event/getLocationId'],
    set: (val) => {
      store.commit('event/setLocationId', val)
    }
  }),
  customLocation: computed({
    get: () => store.getters['event/getCustomLocation'],
    set: (val) => {
      store.commit('event/setCustomLocation', val)
    }
  }),
  organizerId: computed({
    get: () => store.getters['event/getOrganizerId'],
    set: (val) => {
      store.commit('event/setOrganizerId', val)
    }
  }),
  zoomUserId: computed({
    get: () => store.getters['event/getZoomUserId'],
    set: (val) => {
      store.commit('event/setZoomUserId', val)
    }
  }),
  tags: computed({
    get: () => store.getters['event/getTags'],
    set: (val) => {
      store.commit('event/setTags', val)
    }
  }),
  notifyParticipants: computed({
    get: () => store.getters['event/getNotifyParticipants'],
    set: (val) => {
      store.commit('event/setNotifyParticipants', val)
    }
  }),
  description: computed({
    get: () => store.getters['event/getDescription'],
    set: (val) => {
      store.commit('event/setDescription', val ? val : '')
    }
  }),
})

let isOrganizer = computed(() => formData.value.organizerId !== null)

function toggleOrganizer (val) {
  store.commit('event/setOrganizerId', val ? store.getters['auth/getProfile'].id : null)
}

// * Form rules
let rules = computed(() => {
  return {
    name: [
      {
        required: true,
        message: amLabels.value.please_enter_name,
        trigger: ['submit', 'change'],
      }
    ],
  }
})

// * Zoom Users Options
let zoomOptions = computed(() => {
  let users = store.getters['auth/getZoomUsers']

  if (users.length){
    return users.map((user) => {
      return {
        value: user.id,
        label: `${user.first_name} ${user.last_name} (${user.email})`,
      }
    })
  }

  return []
})

// * Expose
defineExpose({
  detailsFormRef
})
</script>

<style lang="scss">
@mixin cabinet-event-details-block {
  // am    - amelia
  // capei - cabinet panel event item
  // def   - details form
  .am-capei-def {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;

    &__item {
      width: 100%;

      &.el-form-item {
        margin-bottom: 0;
      }

      .el-form-item {
        &__error {
          color: var(--am-c-error);
          padding: 0;
        }
      }

      &-tool {
        .el-form-item {
          &__content{
            gap: 8px;
          }
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include cabinet-event-details-block;
}
</style>
