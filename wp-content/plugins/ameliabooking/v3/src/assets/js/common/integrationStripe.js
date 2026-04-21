import httpClient from "../../../plugins/axios";
import {useAuthorizationHeaderObject} from "../public/panel";

function useStripeSync (store) {
  store.commit('auth/setStripeLoading', true)

  httpClient.get(
    '/stripe/accounts/' + store.getters['employee/getId'],
    Object.assign(
      {
      },
      useAuthorizationHeaderObject(store)
    )
  ).then((response) => {
    if (response.data.data.account) {
      store.commit('auth/setStripeProvider', response.data.data.account)

      if (!store.getters['employee/getStripeConnect']) {
        store.commit('employee/setStripeConnect', {
          id: response.data.data.account.id,
          amount: null,
        })
      }
    }
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    store.commit('auth/setStripeLoading', false)
  })
}

function useStripeConnect (store, accountType) {
  store.commit('auth/setStripeLoading', true)

  httpClient.post(
    '/stripe/onboard/' + store.getters['auth/getProfile'].id,
    Object.assign(
      {
        returnUrl: window.location.href,
        accountType: accountType,
      },
      useAuthorizationHeaderObject(store)
    )
  ).then((response) => {
    window.location.href = response.data.data.url
  }).catch((error) => {
    store.commit('auth/setStripeLoading', false)
    console.log('response' in error && 'data' in error.response && 'message' in error.response.data ? error.response.data.message : error.message)
  })
}

function useStripeDisconnect (store) {
  store.commit('auth/setStripeLoading', true)

  httpClient.post(
    '/stripe/disconnect/' + store.getters['auth/getProfile'].id,
    useAuthorizationHeaderObject(store)
  ).then(() => {
    store.commit(
      'auth/setStripeProvider',
      {
        id: '',
        email: '',
        type: '',
        completed: false,
      }
    )

    store.commit('employee/setStripeConnect', null)
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    store.commit('auth/setStripeLoading', false)
  })
}

function useStripePreview (store) {
  let stripeProvider = store.getters['auth/getStripeProvider']

  store.commit('auth/setStripeLoading', true)

  if (stripeProvider.type === 'standard') {
    window.open('https://dashboard.stripe.com/' + stripeProvider.id, '_blank')

    store.commit('auth/setStripeLoading', false)
  } else if (stripeProvider.type === 'express') {
    httpClient.post(
      '/stripe/dashboard/' + store.getters['auth/getProfile'].id,
      useAuthorizationHeaderObject(store)
    ).then((response) => {
      if (response.data.data.url) {
        window.open(response.data.data.url, '_blank')
      }
    }).catch((error) => {
      console.log(error)
    }).finally(() => {
      store.commit('auth/setStripeLoading', false)
    })
  }
}

export {
  useStripeSync,
  useStripeConnect,
  useStripeDisconnect,
  useStripePreview,
}
