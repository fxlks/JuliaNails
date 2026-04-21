<template>
  <el-form
    ref="customizeFormRef"
    :model="eventCustomizeForm"
    class="am-capei-custef"
  >
    <div class="am-capei-custef__heading">
      {{ amLabels.event_colors }}
    </div>

    <AmRadioGroup v-model="colorType">
      <el-form-item class="am-capei-custef__item">
        <AmRadio :value="1">
          {{ amLabels.event_colors_preset }}:
        </AmRadio>
        <div class="am-capei-custef__colors">
          <div
            v-for="color in colorPalette"
            :key="color"
            class="am-capei-custef__colors-item"
            :class="{'am-disabled': colorType === 2}"
            @click="store.commit('event/setColor', color)"
          >
            <div
              class="am-capei-custef__colors-item__inner"
              :style="{backgroundColor: color}"
            >
              <span
                v-if="color === eventCustomizeForm.color"
                class="am-icon-check"
              />
            </div>
          </div>
        </div>
      </el-form-item>
      <el-form-item class="am-capei-custef__item">
        <AmRadio :value="2">
          {{ amLabels.event_colors_custom }}:
        </AmRadio>
        <el-color-picker
          v-model="eventCustomizeForm.color"
          :disabled="colorType === 1"
          :aria-label="amLabels.event_colors_custom"
        />
      </el-form-item>
      <el-form-item class="am-capei-custef__item">
        <AmCheckbox
          v-model="eventCustomizeForm.show"
          :label="amLabels.event_show_on_site"
        />
      </el-form-item>
    </AmRadioGroup>
  </el-form>
</template>

<script setup>
// * Import form Vue
import {
  ref,
  computed,
  inject,
  onMounted
} from 'vue'

// * Import store
import { useStore } from 'vuex'

// * Import Components
import AmCheckbox from '../../../../../_components/checkbox/AmCheckbox.vue'
import AmRadioGroup from '../../../../../_components/radio/AmRadioGroup.vue'
import AmRadio from '../../../../../_components/radio/AmRadio.vue'

// * Store
let store = useStore()

// * Labels
let amLabels = inject('amLabels')

// * Form Reference
const customizeFormRef = ref(null)

let colorType = ref(1)

let colorPalette = ref([
  '#1788FB',
  '#4BBEC6',
  '#FBC22D',
  '#FA3C52',
  '#D696B8',
  '#689BCA',
  '#26CC2B',
  '#FD7E35',
  '#E38587',
  '#774DFB'
])

// * Form data
let eventCustomizeForm = ref({
  color: computed({
    get: () => store.getters['event/getColor'],
    set: (val) => {
      store.commit('event/setColor', val)
    },
  }),
  show: computed({
    get: () => store.getters['event/getShow'],
    set: (val) => {
      store.commit('event/setShow', val)
    },
  }),
})

// * Lifecycle Hooks
onMounted(() => {
  let selectedColor = store.getters['event/getColor']
  let colorTypeRecognition = colorPalette.value.filter((color) => color === selectedColor).length > 0
  if (colorTypeRecognition) {
    colorType.value = 1
  } else {
    colorType.value = 2
  }
})
</script>

<style lang="scss">
@mixin cabinet-event-customize-block {
  // am     - amelia
  // capei  - cabinet panel event item
  // custef - customize event form
  .am-capei-custef {
    display: flex;
    flex-direction: column;
    gap: 24px;

    &__heading {
      font-size: 15px;
      font-weight: 400;
      line-height: 1.6;
      color: var(--am-c-main-text);
    }

    &__colors {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 8px;

      &-item {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        cursor: pointer;
        padding: 4px;
        border: 1px solid var(--am-c-inp-border);

        &.am-disabled {
          cursor: not-allowed;
          pointer-events: none;
          position: relative;

          &:after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 4px;
          }
        }

        &__inner {
          width: 100%;
          height: 100%;
          display: flex;
          justify-content: center;
          align-items: center;
          position: relative;

          .am-icon-check {
            color: #ffffff;
            font-size: 18px;
          }
        }
      }
    }

    &__item {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: flex-start;
    }

    .el-form-item {
      margin: 0;

      &__content {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
      }
    }

    .el-color-picker {
      &__trigger {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        box-sizing: border-box;
        height: 32px;
        width: 32px;
        padding: 4px;
        border: 1px solid var(--am-c-inp-border);
        border-radius: 4px;
        font-size: 0;
        position: relative;
        cursor: pointer;
      }

      &__mask {
        width: 31px;
        height: 31px;
      }

      &__icon {
        font-size: 12px;
      }
    }

    .am-radio-group {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include cabinet-event-customize-block;
}
</style>