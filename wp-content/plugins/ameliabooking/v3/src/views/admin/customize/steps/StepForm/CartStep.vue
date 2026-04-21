<template>
  <div
    ref="cartRef"
    class="am-fs__main-content am-fs__cart"
  >
    <div
      class="am-fs__cart-title"
      v-html="labelsDisplay('cart_title', 'cartStep')"
    ></div>

    <div class="am-fs__cart-includes">
      <AmCollapse>
        <AmCollapseItem
          v-for="(item, index) in cart"
          :ref="(el) => setCollapseItem(el, index)"
          :key="index"
          :side="true"
        >
          <template #heading>
            <CartItemHead :data="item"></CartItemHead>
          </template>
          <template #default>
            <CartItemBody :data="item" :index="index" :size="cart.length">
            </CartItemBody>
          </template>
        </AmCollapseItem>
      </AmCollapse>
    </div>
  </div>
</template>

<script setup>
// * Import libraries
import moment from 'moment'

// * _components
import AmCollapseItem from '../../../../_components/collapse/AmCollapseItem'
import AmCollapse from '../../../../_components/collapse/AmCollapse'

// * Dedicated components
import CartItemHead from '../parts/CartItemHead.vue'
import CartItemBody from '../parts/CartItemBody.vue'

// * Import from Vue
import { computed, inject, ref, provide, onMounted, nextTick, watch } from 'vue'

// * Import composables
import { useReactiveCustomize } from '../../../../../assets/js/admin/useReactiveCustomize.js'

// * Features
let features = inject('features')

// * Multi lingual
let langKey = inject('langKey')

// * Component labels
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
const { amCustomize } = useReactiveCustomize()

// * Label computed function
function labelsDisplay(label, stepKey) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value][stepKey].translations &&
      amCustomize.value[pageRenderKey.value][stepKey].translations[label] &&
      amCustomize.value[pageRenderKey.value][stepKey].translations[label][
        langKey.value
      ]
      ? amCustomize.value[pageRenderKey.value][stepKey].translations[label][
          langKey.value
        ]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Component reference
let cartRef = ref(null)

// * Plugin wrapper width
let wrapperWidth = ref()
provide('wrapperWidth', wrapperWidth)

// * window resize listener
window.addEventListener('resize', resize)
// * resize function
function resize() {
  if (cartRef.value) {
    wrapperWidth.value = cartRef.value.offsetWidth
  }
}

onMounted(() => {
  nextTick(() => {
    wrapperWidth.value = cartRef.value.offsetWidth
  })
})

let sidebarCollapsed = inject('sidebarCollapsed', ref(false))

watch(sidebarCollapsed, (current) => {
  if (current) {
    setTimeout(() => {
      wrapperWidth.value = cartRef.value.offsetWidth
    }, 1200)
  } else {
    setTimeout(() => {
      wrapperWidth.value = cartRef.value.offsetWidth
    }, 400)
  }
})

/**************
 * Navigation *
 *************/

let arr1 = [
  {
    service: {
      name: 'Service 1',
      persons: 3,
      price: 100,
      duration: 5400,
      date: moment().format('YYYY-MM-DD'),
      time: moment('09:00', 'hh:mm').format('hh:mm'),
      maxCapacity: 10,
      employee: {
        firstName: 'John',
        lastName: 'Doe',
      },
      location: {
        name: 'Location 1',
      },
      color: '#1788FB',
    },
    extras: [
      {
        name: 'Extra 1',
        duration: 1800,
        quantity: 2,
        price: 10,
      },
      {
        name: 'Extra 2',
        duration: null,
        quantity: 1,
        price: 15,
      },
      {
        name: 'Extra 3',
        duration: 2400,
        quantity: 3,
        price: 20,
      },
    ],
  },
  {
    service: {
      name: 'Service 2',
      persons: 1,
      price: 100,
      duration: 5400,
      date: moment().format('YYYY-MM-DD'),
      time: moment('09:00', 'hh:mm').format('hh:mm'),
      maxCapacity: 10,
      employee: {
        firstName: 'Jane',
        lastName: 'Doe',
      },
      location: {
        name: 'Location 2',
      },
      color: '#FD7E35',
    },
    extras: [
      {
        name: 'Extra 1',
        duration: 1800,
        quantity: 2,
        price: 10,
      },
      {
        name: 'Extra 2',
        duration: null,
        quantity: 1,
        price: 15,
      },
      {
        name: 'Extra 3',
        duration: 2400,
        quantity: 3,
        price: 20,
      },
    ],
  },
  {
    service: {
      name: 'Service 3',
      persons: 2,
      price: 100,
      duration: 5400,
      date: moment().format('YYYY-MM-DD'),
      time: moment('09:00', 'hh:mm').format('hh:mm'),
      maxCapacity: 10,
      employee: {
        firstName: 'John',
        lastName: 'Doe',
      },
      location: {
        name: 'Location 3',
      },
      color: '#774DFB',
    },
    extras: [
      {
        name: 'Extra 1',
        duration: 1800,
        quantity: 2,
        price: 10,
      },
      {
        name: 'Extra 2',
        duration: null,
        quantity: 1,
        price: 15,
      },
      {
        name: 'Extra 3',
        duration: 2400,
        quantity: 3,
        price: 20,
      },
    ],
  },
]

let arr2 = [
  {
    service: {
      name: 'Service 1',
      persons: 3,
      price: 100,
      duration: 5400,
      date: moment().format('YYYY-MM-DD'),
      time: moment('09:00', 'hh:mm').format('hh:mm'),
      maxCapacity: 10,
      employee: {
        firstName: 'John',
        lastName: 'Doe',
      },
      location: {
        name: 'Location 1',
      },
      color: '#1788FB',
    },
  },
  {
    service: {
      name: 'Service 2',
      persons: 1,
      price: 100,
      duration: 5400,
      date: moment().format('YYYY-MM-DD'),
      time: moment('09:00', 'hh:mm').format('hh:mm'),
      maxCapacity: 10,
      employee: {
        firstName: 'Jane',
        lastName: 'Doe',
      },
      location: {
        name: 'Location 2',
      },
      color: '#FD7E35',
    },
  },
  {
    service: {
      name: 'Service 3',
      persons: 2,
      price: 100,
      duration: 5400,
      date: moment().format('YYYY-MM-DD'),
      time: moment('09:00', 'hh:mm').format('hh:mm'),
      maxCapacity: 10,
      employee: {
        firstName: 'John',
        lastName: 'Doe',
      },
      location: {
        name: 'Location 3',
      },
      color: '#774DFB',
    },
  },
]

let cart = ref(!features.value.extras ? arr2 : arr1)

let appointmentsList = ref([])

let initCollapseLoadCompleted = ref(false)

function setCollapseItem(el, index) {
  appointmentsList.value[index] = el

  if (index === 0 && !initCollapseLoadCompleted.value) {
    initCollapseLoadCompleted.value = true
  }
}
</script>

<script>
export default {
  name: 'CartStep',
  key: 'cartStep',
  inheritAttrs: false,
  sidebarData: {
    label: 'cart_step',
    icon: 'cart',
    stepSelectedData: [],
    finished: false,
    selected: false,
  },
}
</script>

<style lang="scss">
#amelia-app-backend-new {
  #amelia-container {
    .am-fs__cart {
      // am - amelia
      // c  - color
      // cart - cart
      --am-c-cart-text: var(--am-c-main-text);
      --am-c-cart-bgr: var(--am-c-main-bgr);

      & > * {
        $count: 2;
        @for $i from 0 through $count {
          &:nth-child(#{$i + 1}) {
            animation: 600ms
              cubic-bezier(0.45, 1, 0.4, 1.2)
              #{$i *
              100}ms
              am-animation-slide-up;
            animation-fill-mode: both;
          }
        }
      }

      &-title {
        font-size: 14px;
        font-weight: 400;
        line-height: 1.42857;
        color: var(--am-c-cart-text);
        margin: 0;
      }

      &-includes {
        margin: 16px 0 0;

        .am-collapse-item {
          $count: 100;
          @for $i from 0 through $count {
            &:nth-child(#{$i + 1}) {
              animation: 600ms
                cubic-bezier(0.45, 1, 0.4, 1.2)
                #{$i *
                100}ms
                am-animation-slide-up;
              animation-fill-mode: both;
            }
          }

          &__heading {
            padding: 12px;
            transition-delay: 0.5s;

            &-side {
              transition-delay: 0s;
            }
          }

          &__trigger {
            &-side {
              padding: 0 4px 0 10px;
            }
          }
        }
      }
    }
  }
}
</style>
