function useDisableAuthorizationHeader () {
  return 'ameliaBooking' in window &&
    'cabinet' in window['ameliaBooking'] &&
    'disableAuthorizationHeader' in window['ameliaBooking']['cabinet'] &&
    window['ameliaBooking']['cabinet']['disableAuthorizationHeader']
}

function useAuthorizationHeaderObject (store) {
  let token = store.getters['auth/getToken']

  return token && !useDisableAuthorizationHeader() ? {headers: {Authorization: 'Bearer ' + token}} : {}
}

export {
  useDisableAuthorizationHeader,
  useAuthorizationHeaderObject,
}
