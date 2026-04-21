<template>
  <div
    role="navigation"
    class="am-cat__sidemenu"
    :style="cssVars"
  >
    <div class="am-cat__sidemenu-item__wrapper">
      <div
        v-for="(item, index) in menuItems"
        :key="index"
        class="am-cat__sidemenu-item"
        :class="{'am-active': props.initSelection === item[props.identifier]}"
        @click="() => selectItem(item)"
      >
        {{ item[props.nameIdentifier] }}
      </div>
    </div>
    <div v-if="props.companyEmail" class="am-cat__sidemenu-footer">
      <span class="am-cat__sidemenu-footer__text">
        {{ props.footerString }}
      </span>
      <a class="am-cat__sidemenu-footer__email" :href="`mailto:${props.companyEmail}`">
        {{ props.companyEmail }}
      </a>
    </div>
  </div>
</template>

<script setup>
import { computed, inject } from "vue";
import { useColorTransparency } from "../../../../assets/js/common/colorManipulation";

let props = defineProps({
  menuItems: {
    type: Array,
    required: true
  },
  initSelection: {
    type: [String, Number]
  },
  identifier: {
    type: String,
    required: true
  },
  nameIdentifier: {
    type: String,
    required: true
  },
  footerString: {
    type: String,
    default: ''
  },
  companyEmail: {
    type: String,
    default: ''
  }
})
let emits = defineEmits(['click'])

function selectItem (item) {
  emits('click', item)
}

// * Colors
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-csm-text-op10': useColorTransparency(amColors.value.colorSbText, 0.1),
    '--am-c-csm-primary-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),
    '--am-c-csm-text-op60': useColorTransparency(amColors.value.colorSbText, 0.6),
    '--am-c-csm-text-op80': useColorTransparency(amColors.value.colorSbText, 0.8),
  }
})
</script>

<script>
export default {
  name: "SideMenu"
}
</script>

<style lang="scss">
@mixin catalog-sidebar {
  // am-    amelia
  // -c-    color
  // -csm-  category side menu
  // -bgr   background
  .am-cat {
    &__sidemenu {
      --am-c-csm-bgr: var(--am-c-sb-bgr);
      --am-c-csm-text: var(--am-c-sb-text);
      --am-c-csm-primary: var(--am-c-primary);

      width: 100%;
      max-width: 208px;
      position: relative;
      display: flex;
      flex-direction: column;
      background-color: var(--am-c-csm-bgr);
      border-radius: 6px;
      margin: 0 12px 0 0;
      padding: 0 0 80px;

      &-item {
        font-size: 14px;
        font-weight: 500;
        line-height: 1.42857;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        color: var(--am-c-csm-text);
        padding: 12px 8px;
        margin: 0 0 2px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;

        &:last-child {
          margin-bottom: 0;
        }

        &:hover {
          background-color: var(--am-c-csm-text-op10);
        }

        &.am-active {
          color: var(--am-c-csm-primary);
          background-color: var(--am-c-csm-primary-op10);
        }

        &__wrapper {
          margin: 8px;
        }
      }

      &-footer {
        width: 100%;
        position: absolute;
        bottom: 0;
        left: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;

        &__text {
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
          text-overflow: ellipsis;
          text-align: center;
          font-size: 14px;
          font-weight: 500;
          line-height: 1.428571;
          color: var(--am-c-csm-text-op60);
        }

        &__email {
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
          text-overflow: ellipsis;
          text-align: center;
          color: var(--am-c-csm-text-op80);
          text-decoration: none;
          font-size: 14px;
          font-weight: 400;
          line-height: 1.428571;

          &:hover {
            color: var(--am-c-csm-text);
          }
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include catalog-sidebar;
}

#amelia-app-backend-new #amelia-container {
  @include catalog-sidebar;
}
</style>