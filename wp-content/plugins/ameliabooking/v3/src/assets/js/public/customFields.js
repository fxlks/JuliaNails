import {useCart} from './cart'

function useCustomFields (store) {
  let serviceCustomFields = {}

  let servicesIds = []

  let cart = useCart(store)

  cart.forEach((cartItem) => {
    for (let serviceId in cartItem.services) {
      if (cartItem.services[serviceId].list.length) {
        servicesIds = servicesIds.concat([parseInt(serviceId)])
      }
    }
  })

  Object.values(store.getters['entities/getCustomFields']).forEach(customField => {
    if (customField.services.map(service => service.id).filter(serviceId => servicesIds.includes(parseInt(serviceId))).length ||
        customField.allServices ||
        customField.saveType === 'customer'
    ) {
      serviceCustomFields[customField.id] = {
        label: customField.label,
        type: customField.type
      }

      switch (customField.type) {
        case ('checkbox'):
        case ('file'):
          serviceCustomFields[customField.id].value = []

          break

        default:
          serviceCustomFields[customField.id].value = ''
      }

      if (store.getters['booking/getAvailableCustomFields'][customField.id]) {
        serviceCustomFields[customField.id].value = store.getters['booking/getAvailableCustomFields'][customField.id]['value']
      }
    }
  })

  store.commit('booking/setAvailableCustomFields', serviceCustomFields)
}

export {
  useCustomFields,
}
