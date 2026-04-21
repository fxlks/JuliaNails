<template>
  <AmButton
    :size="props.btnSize"
    :suffix="arrowDown"
    class="am-button-single"
    :class="props.class"
  >
    {{ labelsDisplay('pay_now_btn') }}
  </AmButton>
</template>

<script setup>
// * Import from Vue
import { computed, inject, defineComponent } from 'vue'

// * Components
import AmButton from '../../../../../../_components/button/AmButton.vue'
import IconComponent from '../../../../../../_components/icons/IconComponent.vue'

// * Composables
import { useReactiveCustomize } from '../../../../../../../assets/js/admin/useReactiveCustomize.js'

let props = defineProps({
  reservation: {
    type: Object,
    default: () => {},
  },
  bookable: {
    type: Object,
    default: () => {},
  },
  btnSize: {
    type: String,
    default: 'mini',
  },
  class: {
    type: String,
    default: '',
  },
})

let arrowDown = defineComponent({
  components: { IconComponent },
  template: `<IconComponent icon="arrow-down"></IconComponent>`,
})

/*************
 * Customize *
 *************/
// * Customize
const { amCustomize } = useReactiveCustomize()

// * Labels
let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
let stepName = inject('stepName')

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value][stepName.value]
      .translations &&
      amCustomize.value[pageRenderKey.value][stepName.value].translations[
        label
      ] &&
      amCustomize.value[pageRenderKey.value][stepName.value].translations[
        label
      ][langKey.value]
      ? amCustomize.value[pageRenderKey.value][stepName.value].translations[
          label
        ][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}
</script>

<script>
export default {
  name: 'PaymentButton',
}
</script>

<style lang="scss"></style>
