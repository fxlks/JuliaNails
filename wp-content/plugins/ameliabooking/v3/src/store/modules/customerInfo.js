import httpClient from "../../plugins/axios";
import {settings} from "../../plugins/settings";

export default {
  namespaced: true,

  state: () => ({
    id: null,
    externalId: '',
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    countryPhoneIso: '',
    loggedUser: false,
    translations: '',
    gender: '',
    birthday: '',
    note: '',
    loading: false,
    customers: [],
    customersIds: [],
    wpUsers: [],
    customFields: null,
    subscribeToMailchimp: settings.mailchimp.checkedByDefault
  }),

  getters: {
    getCustomerId (state) {
      return state.id
    },

    getCustomerExternalId (state) {
      return state.externalId
    },

    getCustomerFirstName (state) {
      return state.firstName
    },

    getCustomerLastName (state) {
      return state.lastName
    },

    getCustomerEmail (state) {
      return state.email
    },

    getCustomerPhone (state) {
      return state.phone
    },

    getCustomerCountryPhoneIso (state) {
      return state.countryPhoneIso
    },

    getCustomerLanguage (state) {
      return state.translations.defaultLanguage
    },

    getCustomerCustomFields (state) {
      return state.customFields
    },

    getCustomerBirthday (state) {
      return state.birthday
    },

    getCustomerGender (state) {
      return state.gender
    },

    getCustomerNote (state) {
      return state.note
    },

    getLoggedUser (state) {
      return state.loggedUser
    },

    getAllData (state) {
      return {
        id: state.id,
        externalId: state.externalId,
        firstName: state.firstName,
        lastName: state.lastName,
        email: state.email,
        phone: state.phone,
        countryPhoneIso : state.countryPhoneIso,
        loggedUser: state.loggedUser,
        customFields: state.customFields
      }
    },

    getLoading (state) {
      return state.loading
    },

    getCustomers (state) {
      return state.customers
    },

    getCustomersIds (state) {
      return state.customersIds
    },

    getWpUsers (state) {
      return state.wpUsers
    },

    getCustomerSubscribe (state) {
      return state.subscribeToMailchimp
    },

    getCustomer (state) {
      return {
        id: state.id,
        externalId: state.externalId,
        firstName: state.firstName,
        lastName: state.lastName,
        email: state.email,
        phone: state.phone,
        countryPhoneIso: state.countryPhoneIso,
        translations: state.translations,
        gender: state.gender,
        birthday: state.birthday,
        note: state.note,
        customFields: state.customFields,
      }
    }
  },

  mutations: {
    setCustomerId (state, payload) {
      state.id = payload
    },

    setCustomerExternalId (state, payload) {
      state.externalId = payload
    },

    setCustomerFirstName (state, payload) {
      state.firstName = payload
    },

    setCustomerLastName (state, payload) {
      state.lastName = payload
    },

    setCustomerEmail (state, payload) {
      state.email = payload
    },

    setCustomerPhone (state, payload) {
      state.phone = payload
    },

    setCustomerCountryPhoneIso (state, payload) {
      state.countryPhoneIso = payload
    },

    setCustomerBirthday (state, payload) {
      state.birthday = payload
    },

    setCustomerLanguage (state, payload) {
      state.translations.defaultLanguage = payload
    },

    setCustomerGender (state, payload) {
      state.gender = payload
    },

    setCustomerNote (state, payload) {
      state.note = payload
    },

    setLoggedUser (state, payload) {
      state.loggedUser = payload
    },

    setCurrentUser (state, payload) {
      state.id = payload.id
      state.externalid = payload.externalid
      state.firstName = payload.firstName
      state.lastName = payload.lastName
      state.email = payload.email
      state.phone = payload.phone || ''
      state.countryPhoneIso = payload.countryPhoneIso || ''
      state.customFields = payload.customFields || ''
    },

    setAllData (state, payload) {
      state.id = payload.id
      state.externalId = payload.externalId
      state.firstName = payload.firstName
      state.lastName = payload.lastName
      state.email = payload.email
      state.phone = payload.phone
      state.countryPhoneIso = payload.countryPhoneIso
      state.loggedUser = payload.loggedUser
      state.customFields = payload.customFields
    },

    setCustomer (state, payload) {
      state.id = payload.id
      state.externalId = payload.externalId
      state.firstName = payload.firstName
      state.lastName = payload.lastName
      state.email = payload.email
      state.phone = payload.phone
      state.countryPhoneIso = payload.countryPhoneIso
      state.translations = payload.translations
      state.gender = payload.gender
      state.birthday = payload.birthday
      state.note = payload.note
      state.customFields = payload.customFields
    },

    setLoading (state, payload) {
      state.loading = payload
    },

    setCustomers (state, payload) {
      state.customers = payload
    },

    setCustomersIds (state, payload) {
      state.customersIds = payload
    },

    setWpUsers (state, payload) {
      state.wpUsers = payload
    },

    setCustomerSubscribe (state, payload) {
      state.subscribeToMailchimp = payload
    },
  },

  actions: {
    requestCurrentUserData ({ commit }) {
      commit('setLoading', true, {root: true})
      if (!('ameliaUser' in window) || !window.ameliaUser) {
        httpClient.get(
          '/users/current'
        ).then((response) => {
          if (response.data.data.user) {
            window.ameliaUser = response.data.data.user ? response.data.data.user : null

            commit('setCurrentUser', window.ameliaUser)
            commit('setLoggedUser', true)
          }
          commit('setLoading', false, {root: true})
        }).catch(() => {
          commit('setLoading', false, {root: true})
        })
      } else {
        let ameliaApiInterval = setInterval(
          () => {
            if ('ameliaUser' in window) {
              clearInterval(ameliaApiInterval)

              commit('setCurrentUser', window.ameliaUser)
              commit('setLoggedUser', true)
            }
            commit('setLoading', false, {root: true})
          },
          1000
        )
      }
    },

    requestWpUsers ({ commit, getters }, payload) {
      let { label } = payload
      commit('setLoading', true)
      httpClient.get(
        'users/wp-users',
        {
          params: {
            id: getters['getCustomerId'],
            role: 'customer'
          }
        }
      ).then((response) => {
        commit('setWpUsers', [{value: 0, label: label}, ...response.data.data.users])

        if (getters['getWpUsers'].map(user => user.value).indexOf(getters['getCustomerExternalId']) === -1) {
          commit('setCustomerExternalId', null)
        }

        commit('setLoading', false)
      }).catch(() => {
        commit('setLoading', false)
      }).finally(() => {
        commit('setLoading', false)
      })
    },

    resetCustomer({commit}) {
      commit('setCustomer', {
        id: null,
        externalId: '',
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        countryPhoneIso: '',
        translations: {defaultLanguage: ''},
        gender: '',
        birthday: '',
        note: '',
        customFields: null
      })
    }
  }
}
