<template>
  <AmSelect
    v-if="!activeBooking"
    v-model="timeZoneSelection"
    :filterable="true"
    :placeholder="useCurrentTimeZone()"
    :clearable="true"
    :size="props.size"
    custom-class="am-ctz"
    prefix-icon="globe-watch"
    :filter-method="filterTimeZone"
    @clear="returnToDefault"
    @change="timeZoneChanged"
  >
    <AmOption
      v-for="(timeZone, index) in filteredTimeZones"
      :key="index"
      :label="timeZone"
      :value="timeZone"
    />
  </AmSelect>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  computed,
  inject
} from 'vue'

// * Component properties
let props = defineProps({
  size: {
    type: String,
    default: 'default'
  }
})

// * Import from Libraries
import { useCookies } from 'vue3-cookies'

// * Vars
const vueCookies = useCookies()['cookies']

// * Composables
import { useCurrentTimeZone } from "../../../../../assets/js/common/helper";

// * _components
import AmOption from "../../../../_components/select/AmOption.vue";
import AmSelect from "../../../../_components/select/AmSelect.vue";

// * Root Settings
const amSettings = inject('settings')

let cabinetType = inject('cabinetType')

let { bookingsCounterChanger } = inject('bookingsCounterChanger', {
  bookingsCounterChanger: () => {}
})

// * import from Vuex
import { useStore } from 'vuex'

// * Define store
const store = useStore()

let timeZoneSelection = computed({
  get: () => store.getters['cabinet/getTimeZone'],
  set: (val) => {
    store.commit('cabinet/setTimeZone', val ? val : '')
  }
})

let activeBooking = computed(() => store.getters['appointment/getActive'] || store.getters['event/getActive'] || store.getters['attendee/getActive'])

const adminTimeZone = inject('timeZone')

const timeZones = inject('timeZones')

let queryLower = ref('')
function filterTimeZone (query) {
  queryLower.value = query.toLowerCase()
}

let filteredTimeZones = computed(() => {
  if (queryLower.value) {
    return timeZones.filter(item => {
      return item.toLowerCase().includes(queryLower.value)
    })
  }
  return timeZones
})

function returnToDefault () {
  let initialTimeZone
  if (!amSettings.general.showClientTimeZone) {
    initialTimeZone = adminTimeZone.value
  } else {
    initialTimeZone = useCurrentTimeZone()
  }

  vueCookies.set(
    'ameliaUserTimeZone',
    initialTimeZone,
    amSettings.roles[cabinetType.value + 'Cabinet']['tokenValidTime']
  )

  bookingsCounterChanger()

  store.commit('cabinet/setTimeZone', initialTimeZone)
}

function timeZoneChanged (selection) {
  vueCookies.set(
    'ameliaUserTimeZone',
    selection,
    amSettings.roles[cabinetType.value + 'Cabinet']['tokenValidTime']
  )

  bookingsCounterChanger()
}
</script>

<style lang="scss">
@mixin am-cabinet-time-zone {
  // ctz - customer time zone
  .am-ctz {
    .el-input {
      &__inner {
        padding: 8px 24px 8px 36px !important;
      }

      &__prefix {
        left: 4px;
        font-size: 30px;
        color: var(--am-c-select-text);
      }
    }
  }
}


// public
.amelia-v2-booking #amelia-container {
  @include am-cabinet-time-zone;
}
</style>