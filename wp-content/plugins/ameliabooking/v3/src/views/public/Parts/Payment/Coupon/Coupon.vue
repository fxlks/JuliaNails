<template>
  <el-form
    ref="couponFormRef"
    :rules="rules"
    :model="couponFormData"
  >
    <div
      class="am-fs__coupon"
      :class="{'am-fs__coupon-mobile-s': mobileS}"
      :style="cssVars"
    >
      <el-form-item
        :prop="'coupon'"
        class="am-fs__coupon-form-item"
      >
        <template #label>
          <span>{{ `${amLabels.coupon}:` }}</span>
        </template>
        <AmInput
          v-model="couponFormData.coupon"
          size="small"
          :class="{'am-fs__coupon-invalid': coupon.limit === 0}"
          :prefix-icon="couponIcon"
          :suffix-icon="validateIcon"
          @input="clearValidation"
          @keydown.enter="(e) => {e.preventDefault()}"
        />
      </el-form-item>
      <AmButton
        size="small"
        @click="validate"
      >
        {{ amLabels.add_coupon_btn }}
      </AmButton>
    </div>
  </el-form>

  <!-- Coupons Used Message -->
  <el-row
    v-if="props.bookableType !== 'event'"
    class="am-coupon-limit"
    :style="cssVars"
  >
    <el-col :sm="2" :xs="4">
      <div style="display: inline-block;">
        <span class="am-icon-coupon-limit"></span>
      </div>
    </el-col>

    <el-col class="am-coupon-limit-col" :sm="22" :xs="20">
      <div class="am-coupon-limit-text">
        <strong>
          {{ amLabels.coupons_used }}
        </strong>
        <p>
          {{ `${amLabels.coupons_used_description} ${coupon.limit}` }}
        </p>
      </div>
    </el-col>
  </el-row>
  <!-- /Coupons Used Message -->
</template>

<script setup>
import AmInput from '../../../../_components/input/AmInput.vue'
import AmButton from '../../../../_components/button/AmButton.vue'
import IconComponent from "../../../../_components/icons/IconComponent.vue";

// * Import from Vue
import {
  inject,
  computed,
  onMounted, ref
} from 'vue'

// * Composables
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation.js'

// * Import from Store
import { useStore } from 'vuex'
import {defaultCustomizeSettings} from "../../../../../assets/js/common/defaultCustomize";
import {validateCoupon} from "../../../../../assets/js/public/coupon";
const store = useStore()

let props = defineProps({
  entityId: {
    type: [Number, String],
    required: true
  },
  bookableType: {
    type: String,
    required: true,
  },
  bookingsCount: {
    type: Number,
    required: true
  }
})

let coupon = computed(() => store.getters['coupon/getCoupon'])

// * Coupon icon
let couponIcon = {
  components: {IconComponent},
  template: `<IconComponent icon="coupon" />`
}

// * Computed labels
let amLabels = inject('amLabels')

// * Event form customization
let customizedDataForm = inject('customizedDataForm')

let couponFormRef = ref(null)

let couponFormData = ref({
  coupon: computed({
    get: () => store.getters['coupon/getCoupon'].code,
    set: (val) => {
      store.commit('coupon/setCode', val)
    }
  }),
})

let couponRequired = computed(() => {
  if ('coupon' in customizedDataForm.value.payment.options) {
    return customizedDataForm.value.payment.options.coupon.required
  }
  return defaultCustomizeSettings.elf.payment.options.coupon.required
})

// * Form validation rules
let rules = ref({
  coupon: [
    {
      required: couponRequired.value,
      trigger: 'submit',
    }
  ]
})

onMounted(() => {
  store.commit('coupon/setCouponRequired', couponRequired.value)
  if (coupon.value.code) {
    validate()
  }
})

const emits = defineEmits(['couponApplied'])

let validateIcon = ref('')

function clearValidation () {
  let err = store.getters['coupon/getError']
  if (err) {
    store.commit('coupon/setError', '')
  }
  validateIcon.value = ''
  couponFormRef.value.clearValidate()
}

function validate () {
  validateCoupon(store, () => {
    let couponNew = store.getters['coupon/getCoupon']
    if (couponNew.code && (couponNew.discount || couponNew.deduction)) {
      if (couponNew.limit === 0) {
        validateIcon.value = 'info-reverse'
      } else {
        validateIcon.value = 'check'
      }
    } else {
      validateIcon.value = ''
    }

    emits('couponApplied')
  })
}

let cWidth = inject('containerWidth', 0)
let mobileS = computed(() => cWidth.value < 340)

// * Global colors
let amColors = inject('amColors');
let cssVars = computed(() => {
  return {
    '--am-c-coupon-primary': amColors.value.colorPrimary,
    '--am-c-coupon-primary-op10': useColorTransparency(amColors.value.colorPrimary, 0.10),
    '--am-c-coupon-primary-op40': useColorTransparency(amColors.value.colorPrimary, 0.40),
  }
})
</script>

<script>
export default {
  name: 'CouponCode'
}
</script>

<style lang="scss">
.amelia-v2-booking {
  #amelia-container {
    .am-coupon-limit {
      background-color: var(--am-c-coupon-primary-op10);
      border: 1px solid var(--am-c-coupon-primary-op40);
      border-radius: 8px;
      padding: 10px;
      margin-top: 5px;

      &-col {
        display: flex;
        justify-content: center;
      }
      &-text {
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        font-size: 13px;

        div {
          display: inline-block;
          padding-top: 6px;
          padding-bottom: 6px;
        }

        p, strong {
          color: var(--am-c-ps-text);
        }

        p {
          font-weight: 300;
        }
      }

      .am-icon-coupon-limit {
        font-size: 30px;
        display: inline-block;
        margin-right: 2px;
        vertical-align: middle;
        margin-bottom: 6px;
        margin-top: 6px;
        color: white;
        background-color: var(--am-c-coupon-primary);
        border-radius: 50%;
      }
    }

    .am-fs__coupon {
      width: 100%;
      display: flex;
      padding: 0;
      margin: 16px 0 0;
      font-size: 14px;
      font-weight: 400;
      line-height: 1.42857;
      gap: 4px;
      color: var(--am-c-ps-text);
      white-space: nowrap;
      align-items: center;

      &-invalid {
        .am-input .el-input__wrapper {
          box-shadow: 0 0 0 1px var(--am-c-error);
        }
      }

      .am-input .el-input__wrapper {
        .am-icon-info-reverse, .am-icon-check {
          font-size: 18px;
        }

        .am-icon-info-reverse {
          color: var(--am-c-error);
        }

        .am-icon-check {
          display: flex;
          align-items: center;
          justify-content: center;
          width: 20px;
          height: 20px;
          background-color: var(--am-c-success);
          border-radius: 50%;
          color: var(--am-c-inp-bgr);
        }
      }

      .el-form-item {
        display: flex;
        gap: 5px;
        align-items: center;
        margin-bottom: 0;
        width: 100%;
        .el-form-item__error {
          width: 100%;
          text-align: center;
        }
      }

      &-mobile-s {
        display: flex;
        flex-direction: column;

        .am-button {
          width: 100%;
        }
      }
    }

    .am-fs__coupon-discount {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      font-weight: 400;
      line-height: 1.42857;
      color: var(--am-c-ps-text);
      margin-bottom: 12px;

      & span:nth-child(2) {
        font-weight: 500;
        color: var(--am-c-success);
      }
    }
  }
}
</style>
