<template>
  <div
    v-if="useDescriptionVisibility(props.description)"
    class="am-eli__description"
  >
    <p
      v-if="props.title"
      class="am-eli__description-title"
    >
      {{ props.title }}
    </p>
    <p
      v-if="showMore && props.description.replace(/<[^>]*>?/gm, '').length > props.limit"
      class="am-eli__description-text ql-description"
      v-html="props.description.slice(0, props.limit) + '...'"
    >
    </p>
    <p
      v-else
      class="am-eli__description-text ql-description"
      v-html="props.description"
    >
    </p>
    <span
      v-if="props.description.replace(/<[^>]*>?/gm, '').length > props.limit"
      class="am-eli__description-btn"
      @click="showMore = !showMore"
    >
      {{ showMore ? displayLabels('show_more') : displayLabels('show_less') }}
    </span>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  inject,
  onMounted
} from "vue";

// * Composables
import { useDescriptionVisibility } from "../../../../../assets/js/common/helper";

// * Component props
let props = defineProps({
  customizedLabels: {
    type: Object,
    default: () => {
      return {}
    }
  },
  title: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  limit: {
    type: Number,
    default: 100
  },
  initState: {
    type: Boolean,
    default: true
  }
})

let showMore = ref(false)

let amLabels = inject('labels')

function displayLabels (label) {
  return Object.keys(props.customizedLabels).length && props.customizedLabels[label] ? props.customizedLabels[label] : amLabels[label]
}

onMounted(() => {
  showMore.value = props.initState
})
</script>

<script>
export default {
  name: "TextItem"
}
</script>

<style lang="scss">
@import '../../../../../assets/scss/common/quill/_quill-mixin.scss';

@mixin event-description {
  .am-eli {
    &__description {

      &-title {
        font-size: 14px;
        font-weight: 500;
        line-height: 1.42857;
        color: var(--am-c-eli-text-op70);
        margin: 0 0 8px;
      }

      &-text {
        font-size: 15px;
        font-weight: 400;
        line-height: 1.6;
        color: var(--am-c-eli-text-op80);
        margin: 0;

        &.ql-description {
          @include quill-styles;
        }
      }

      &-btn {
        display: inline-block;
        font-size: 15px;
        font-weight: 400;
        line-height: 1.6;
        color: var(--am-c-primary);
        cursor: pointer;
      }
    }
  }
}

// Public
.amelia-v2-booking #amelia-container {
  @include event-description;
}

// Admin
#amelia-app-backend-new {
  @include event-description;
}
</style>
