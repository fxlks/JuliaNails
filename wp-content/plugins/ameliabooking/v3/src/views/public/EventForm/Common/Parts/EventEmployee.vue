<template>
  <div
    v-if="Object.keys(props.employee).length"
    class="am-eli__organizer"
  >
    <el-divider v-if="props.divider" />
    <div class="am-eli__organizer-item">
      <span
        class="am-eli__organizer-img"
        :style="{backgroundImage: `url(${props.employee.pictureThumbPath})`}"
      >
        {{ employeeInitials }}
      </span>
      <div class="am-eli__organizer-inner">
        <p class="am-eli__organizer-name">
          {{`${props.employee.firstName} ${props.employee.lastName}`}}
          <span
            v-if="props.employee.badge"
            :style="{background: props.employee.badge.color}"
            class="am-eli__organizer-badge"
          >
            {{ props.employee.badge.content }}
          </span>
        </p>
        <p v-if="props.rank" class="am-eli__organizer-rank">
          {{props.rank}}
        </p>
      </div>
    </div>

    <DescriptionItem
      :title="props.customizedLabels.about"
      :description="props.employee.description"
      :customized-labels="props.customizedLabels"
      :limit="250"
    ></DescriptionItem>
  </div>
</template>

<script setup>
import DescriptionItem from "./DescriptionItem.vue";

import {
  computed
} from "vue";

let props = defineProps({
  customizedLabels: {
    type: Object,
    required: true
  },
  employee: {
    type: Object,
    required: true
  },
  rank: {
    type: String,
    default: ''
  },
  divider: {
    type: Boolean,
    default: false
  }
})

let employeeInitials = computed(() => {
  let initials = ''
  if (props.employee && !props.employee.pictureThumbPath) {
    let fullName = `${props.employee.firstName} ${props.employee.lastName}`
    fullName.split(' ').forEach(item => {
      initials += item.charAt(0).toUpperCase()
    })
  }
  return initials
})
</script>

<script>
export default {
  name: "EventEmployee"
}
</script>

<style lang="scss">
@mixin event-employee {
  // eli - event list info
  .am-eli {
    &__organizer {
      .el-divider {
        margin-bottom: 16px;
        border-top: 1px solid var(--am-c-card-border);
      }

      &-item {
        display: flex;
        flex-direction: row;
        align-items: center;
        margin-bottom: 16px;
      }

      &-img {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--am-c-eli-primary);
        color: var(--am-c-eli-bgr);
        width: 48px;
        height: 48px;
        border-radius: 50%;
        margin-right: 16px;
        font-size: 14px;
        font-weight: 500;
        overflow: hidden;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
      }

      &-name {
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        color: var(--am-c-eli-text-op90);
        margin: 0;
      }

      &-badge {
        color: #FFF;
        border-radius: 4px;
        padding: 0 4px;
        font-size: 13px;
        line-height: 19px;
      }

      &-rank {
        font-size: 15px;
        font-weight: 400;
        line-height: 1.6;
        color: var(--am-c-eli-text-op60);
        margin: 0;
      }
    }
  }
}

// Public
.amelia-v2-booking #amelia-container {
  @include event-employee;
}

// Admin
#amelia-app-backend-new {
  @include event-employee;
}
</style>
