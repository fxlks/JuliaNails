import httpClient from "../../../plugins/axios.js";

function useCurrentUser (store) {
  if (!('ameliaUser' in window)) {
    httpClient.get(
      '/users/current'
    ).then((response) => {
      if (response.data.data.user) {
        window.ameliaUser = response.data.data.user ? response.data.data.user : null

        setCurrentUser(store, window.ameliaUser)
      }
    }).catch(() => {
    })
  } else {
    let ameliaApiInterval = setInterval(
      () => {
        if ('ameliaUser' in window) {
          clearInterval(ameliaApiInterval)

          setCurrentUser(store, window.ameliaUser)
        }
      },
      1000
    )
  }
}

function setCurrentUser (store, user) {
  store.commit('booking/setCustomerFirstName', user.firstName)
  store.commit('booking/setCustomerLastName', user.lastName)
  store.commit('booking/setCustomerEmail', user.email)
  store.commit('booking/setCustomerPhone', user.phone)
  store.commit('booking/setCustomerCountryPhoneIso', user.countryPhoneIso)
  store.commit('booking/setCustomerId', user.id)
  store.commit('booking/setCustomerExternalId', user.externalid)
  store.commit('booking/setCustomerTranslations', user.translations)
  store.commit('booking/setCustomerCustomFields', user.customFields)
}

export {
  useCurrentUser,
}
