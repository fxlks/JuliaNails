import httpClient from "../../../plugins/axios";
import { settings } from "../../../plugins/settings";
import { useUrlParams } from "../common/helper";

function useCustomers (params, callback) {
  httpClient.get(
    '/users/customers',
    {params: useUrlParams(params)}
  ).then(response => {
    callback(response.data.data.users)
  }).catch(() => {
    callback([])
  })
}

function useSearchCustomers (store, value = '', params = {}, callback = () => {}) {
  store.commit('customerInfo/setLoading', true)

  setTimeout(
    () => {
      useCustomers(
        Object.assign(
          {
            search: value,
            page: 1,
            limit: settings.general.customersFilterLimit,
            skipCount: 1,
          },
          params
        ),
        (result) => {
          store.commit('customerInfo/setCustomers', result)

          store.commit('customerInfo/setLoading', false)

          callback()
        }
      )
    },
    500
  )
}

function useFocusCustomers(store) {
  if (!store.getters['customerInfo/getCustomers'].length) {
    useSearchCustomers(store)
  }
}

export {
  useCustomers,
  useSearchCustomers,
  useFocusCustomers,
}