import httpClient from "../../../plugins/axios";
import {useRemoveUrlParameter, useUrlQueryParams} from "./helper";
import {useAuthorizationHeaderObject} from "../public/panel";

function useOutlookSync (code, successCallback) {
  let redirectURL = useRemoveUrlParameter(
    useRemoveUrlParameter(
      useRemoveUrlParameter(
        window.location.href,
        'code'
      ),
      'state'
    ),
    'type'
  )

  httpClient.post(
    '/outlook/authorization/token',
    {
      authCode: code,
      userId: useUrlQueryParams(window.location.href)['state'].split('amelia-outlook-calendar-auth-')[1],
      redirectUri: window.location.href.split('?')[0]
    }
  ).then(() => {
    history.pushState({}, null, redirectURL)
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    successCallback()
  })
}

function useOutlookConnect (store) {
  const amSettings = store.getters['getSettings']
  if (!store.getters['auth/getOutlookLoading']) {
    store.commit('auth/setOutlookLoading', true)

    if (!amSettings.outlookCalendar.accessToken) {
      httpClient
        .get(
          '/outlook/authorization/url/' + store.getters['employee/getId'],
          Object.assign(
            {
              redirectUri: window.location.href.split('?')[0],
            },
            useAuthorizationHeaderObject(store)
          )
        )
        .then((response) => {
          window.location.href = response.data.data.authUrl.replace(
            /redirect_uri=.+?&/,
            'redirect_uri=' + window.location.href + '&'
          )
        })
        .catch((error) => {
          console.log(error)

          store.commit('auth/setOutlookLoading', false)
        })
    }

    if (amSettings.outlookCalendar.accessToken) {
      httpClient
        .get(
          '/outlook-calendar/authorization/url/' +
            store.getters['employee/getId'],
          {
            params: {
              redirectUri: window.location.href,
            },
          }
        )
        .then((response) => {
          window.location.href = response.data.data.authUrl
        })
        .catch((error) => {
          console.log(error)

          store.commit('auth/setOutlookLoading', false)
        })
    }
  }
}

function useOutlookDisconnect (store) {
  store.commit('auth/setOutlookLoading', true)

  httpClient.post(
    '/outlook/disconnect/' + store.getters['employee/getId'],
  ).then(() => {
    store.commit('employee/setOutlookId', '')
    store.commit('employee/setOutlookCalendarId', '')
    store.commit('employee/setOutlookToken', null)
    store.commit('auth/setOutlookCalendars', [])
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    store.commit('auth/setOutlookLoading', false)
  })
}

export {
  useOutlookSync,
  useOutlookConnect,
  useOutlookDisconnect,
}
