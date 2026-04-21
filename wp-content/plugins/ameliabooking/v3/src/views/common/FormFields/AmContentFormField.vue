<template>
  <el-form-item
    ref="formFieldRef"
    class="am-ff__item am-ff__item-content"
  >
    <div class="am-ff__item-content__inner" v-html="`<span class='am-icon-circle-info'></span> ${props.label}`" />
  </el-form-item>
</template>

<script setup>
// * Import from Vue
import {
  ref,
} from "vue";

// * Form Item Props
let props = defineProps({
  label: {
    type: String
  }
})

// * Form Item Reference
let formFieldRef = ref(null)

defineExpose({
  formFieldRef
})
</script>

<script>
export default {
  name: "ContentFormField"
}
</script>

<style lang="scss">
@mixin contentFormItem {
  // am - amelia
  // ff - form field
 .am-ff__item {
   &-content {
     border: 1px solid var(--am-c-inp-border);
     border-radius: 6px;
     background-color: transparent;
     padding: 8px 12px;

     .am-icon-circle-info {
       display: inline-block;
       font-size: 22px;
       position: relative;
       width: 16px;
       height: 16px;

       &:before {
         position: absolute;
         top: -2px;
         left: -3px;
       }
     }
   }
 }
}

.amelia-v2-booking #amelia-container {
  @include contentFormItem;
}
</style>
