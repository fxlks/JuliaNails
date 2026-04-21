import httpClient from "../../../plugins/axios";
import {useAuthorizationHeaderObject} from "../public/panel";

function useZoomUsers (store) {
  store.commit('auth/setZoomLoading', true)

  httpClient.get(
    '/zoom/users',
    Object.assign(
      useAuthorizationHeaderObject(store),
      {
        params: {
          source: 'cabinet-provider',
        },
      }
    )
  ).then((response) => {
    if ('data' in response.data && 'users' in response.data.data) {
      store.commit('auth/setZoomUsers', response.data.data?.users ? response.data.data.users : [])
    }
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    store.commit('auth/setZoomLoading', false)
  })
}

export {
  useZoomUsers,
}
