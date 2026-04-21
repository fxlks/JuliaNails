// * Import from Vue
import {
  markRaw,
  reactive
} from "vue";

// * Form Field templates
import AmInputFormField from "../../../views/common/FormFields/AmInputFormField.vue";
import AmPhoneFormField from "../../../views/common/FormFields/AmPhoneFormField.vue";
import AmSelectFormField from "../../../views/common/FormFields/AmSelectFormField.vue";
import AmRadioFormField from "../../../views/common/FormFields/AmRadioFormField.vue";
import AmCheckboxFormField from "../../../views/common/FormFields/AmCheckboxFormField.vue";
import AmAttachmentFormField from "../../../views/common/FormFields/AmAttachmentFormField.vue";
import AmDatePickerFormField from "../../../views/common/FormFields/AmDatePickerFormField.vue";
import AmContentFormField from "../../../views/common/FormFields/AmContentFormField.vue";
import AmAddressFormField from "../../../views/common/FormFields/AmAddressFormField.vue";

// * Form Fields Object
let formFieldsTemplates = reactive({
  text: markRaw(AmInputFormField),
  'text-area': markRaw(AmInputFormField),
  phone: markRaw(AmPhoneFormField),
  select: markRaw(AmSelectFormField),
  radio: markRaw(AmRadioFormField),
  checkbox: markRaw(AmCheckboxFormField),
  file: markRaw(AmAttachmentFormField),
  datepicker: markRaw(AmDatePickerFormField),
  content: markRaw(AmContentFormField),
  address: markRaw(AmAddressFormField)
})

export {
  formFieldsTemplates
}