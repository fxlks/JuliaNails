import { settings } from '../../../plugins/settings'

function useCart (store) {
  return store.getters['booking/getAllMultipleAppointments']
}

function useCartItem (store) {
  return store.getters['booking/getAllMultipleAppointments'][store.getters['booking/getCartItemIndex']]
}

function useCartHasItems (store) {
  let cart = useCart(store)

  let hasItems = 0

  cart.forEach((item) => {
    if (!item.packageId) {
      Object.keys(item.services).forEach(((serviceId) => {
        if (item.services[serviceId].list.filter(i => i.date && i.time && i.providerId).length) {
          hasItems++
        }
      }))
    }
  })

  return hasItems
}

function useCartStep (store) {
  let preselected = store.getters['entities/getPreselected']

  return settings.payments.cart &&
    !(preselected.show === 'packages' || (Array.isArray(preselected.package) ? preselected.package.length : preselected.package))
}

function useAddToCart (store) {
  let index = store.getters['booking/getCartItemIndex']

  let items = store.getters['booking/getAllMultipleAppointments']

  index++

  items[index] = {
    packageId: null,
    serviceId: null,
    index: 0,
    services: {},
  }

  store.commit('booking/setCartItemIndex', index)
}

function useGoToCartStep (stepsArray, stepIndex) {
  stepIndex.value = stepsArray.value.findIndex(i => i.name === 'CartStep' )
}

function useInitSelection (store, deselectService) {
  let preselected = store.getters['entities/getPreselected']

  if (deselectService && !preselected.service.length) {
    store.commit('booking/setServiceId', null)
    store.commit('booking/setCategoryId', null)
  }

  if (!preselected.employee.length) {
    store.commit('booking/setEmployeeId', null)
  }

  if (!preselected.location.length) {
    store.commit('booking/setLocationId', null)
  }

  if (!preselected.package.length) {
    store.commit('booking/setPackageId', null)

    store.commit('booking/setBookableType', 'appointment')
  }
}

function useResetCart (store) {
  let items = store.getters['booking/getAllMultipleAppointments']

  if (items[0].packageId) {
    store.commit(
      'booking/setMultipleAppointments',
      [
        {
          packageId: null,
          serviceId: null,
          index: 0,
          services: {},
        }
      ]
    )
  }
}

function useInitCartItem (store) {
  let index = store.getters['booking/getCartItemIndex']

  let items = store.getters['booking/getAllMultipleAppointments']

  let serviceId = store.getters['booking/getServiceId']

  let appointmentInstantiated = index in items && serviceId in items[index].services

  if (serviceId && items[index] && ('services' in items[index]) && !(serviceId in items[index].services)) {
    items[index].index = 0

    items[index].packageId = null

    items[index].serviceId = serviceId

    items[index].services = {}

    items[index].services[serviceId] = {
      fetched: false,
      slots: [],
      providerId: store.getters['booking/getEmployeeId'],
      locationId: store.getters['booking/getLocationId'],
      list: [
        {
          providerId: store.getters['booking/getEmployeeId'],
          locationId: store.getters['booking/getLocationId'],
          date: appointmentInstantiated ? store.getters['booking/getMultipleAppointmentsDate'] : null,
          time: appointmentInstantiated ? store.getters['booking/getMultipleAppointmentsTime'] : null,
          range: appointmentInstantiated ? store.getters['booking/getMultipleAppointmentsRange'] : {start: null, end: null},
          persons: 1,
          extras: [],
          duration: null,
          price: null,
        }
      ],
    }
  } else if (!serviceId) {
    items[index].index = ''

    items[index].services = {}

    items[index].packageId = null

    items[index].serviceId = null
  }
}

function useRemoveLastCartItem (store) {
  let items = store.getters['booking/getAllMultipleAppointments']

  if (!items[items.length - 1].services[items[items.length - 1].serviceId].list[0].date &&
    !items[items.length - 1].services[items[items.length - 1].serviceId].list[0].time
  ) {
    items.pop()

    store.commit('booking/setCartItemIndex', items.length - 1)
  }
}

export {
  useInitSelection,
  useAddToCart,
  useGoToCartStep,
  useCartHasItems,
  useInitCartItem,
  useCartStep,
  useCart,
  useResetCart,
  useCartItem,
  useRemoveLastCartItem,
}
