import moment from "moment";
import { useCookies } from "vue3-cookies";
import httpClient from "../../plugins/axios";

export default {
  namespaced: true,

  state: () => ({
    email: '',
    password: '',
    newPassword: '',
    confirmPassword: '',
    authenticated: false,
    token: null,
    profile: null,
    profileDeleted: false,
    loggedOut: false,
    googleLoading: false,
    outlookLoading: false,
    appleLoading: false,
    stripeLoading: false,
    zoomLoading: false,
    spacesLoading: false,
    zoomUsers: [],
    googleCalendars: [],
    outlookCalendars: [],
    appleCalendars: [],
    stripeProvider: {
      id: '',
      type: '',
      email: '',
      completed: false,
    },
    spaces: [],
    loadingAppointmentsCounter: 0,
    loadingEventsCounter: 0,
    preloaded: {
      customers: [],
      events: [],
    },
  }),

  getters: {
    getEmail (state) {
      return state.email
    },

    getPassword (state) {
      return state.password
    },

    getNewPassword (state) {
      return state.newPassword
    },

    getConfirmPassword (state) {
      return state.confirmPassword
    },

    getAuthenticated (state) {
      return state.authenticated
    },

    getToken (state) {
      return state.token
    },

    getProfile (state) {
      return state.profile
    },

    getProfileDeleted (state) {
      return state.profileDeleted
    },

    getLoggedOut (state) {
      return state.loggedOut
    },

    getGoogleLoading (state) {
      return state.googleLoading
    },

    getOutlookLoading (state) {
      return state.outlookLoading
    },

    getAppleLoading (state) {
      return state.appleLoading
    },

    getStripeLoading (state) {
      return state.stripeLoading
    },

    getZoomLoading (state) {
      return state.zoomLoading
    },

    getSpacesLoading (state) {
      return state.spacesLoading
    },

    getZoomUsers (state) {
      return state.zoomUsers
    },

    getSpaces (state) {
      return state.spaces
    },

    getGoogleCalendars (state) {
      return state.googleCalendars
    },

    getOutlookCalendars (state) {
      return state.outlookCalendars
    },

    getAppleCalendars (state) {
      return state.appleCalendars
    },

    getStripeProvider (state) {
      return state.stripeProvider
    },

    getLoadingAppointmentsCounter (state) {
      return state.loadingAppointmentsCounter
    },

    getLoadingEventsCounter (state) {
      return state.loadingEventsCounter
    },

    getPreloadedEvents (state) {
      return state.preloaded.events
    },

    getPreloadedCustomers (state) {
      return state.preloaded.customers
    },
  },

  mutations: {
    setEmail (state, payload) {
      state.email = payload
    },

    setPassword (state, payload) {
      state.password = payload
    },

    setNewPassword (state, payload) {
      state.newPassword = payload
    },

    setConfirmPassword (state, payload) {
      state.confirmPassword = payload
    },

    setAuthenticated (state, payload) {
      state.authenticated = payload
    },

    setToken (state, payload) {
      state.token = payload
    },

    setProfile (state, payload) {
      state.profile = payload

      if (state.profile.phone === null) {
        state.profile.phone = ''
      }

      if (state.profile.birthday) {
        state.profile.birthday = moment(payload.birthday.date).format('YYYY-MM-DD')
      }
    },

    setProfileFirstName (state, payload) {
      state.profile.firstName = payload
    },

    setProfileLastName (state, payload) {
      state.profile.lastName = payload
    },

    setProfileEmail (state, payload) {
      state.profile.email = payload
    },

    setProfilePhone (state, payload) {
      state.profile.phone = payload
    },

    setProfileCountryPhoneIso (state, payload) {
      state.profile.countryPhoneIso = payload
    },

    setProfileBirthday (state, payload) {
      state.profile.birthday = payload
    },

    setProfileDeleted (state, payload) {
      state.profileDeleted = payload
    },

    setLoggedOut (state, payload) {
      state.loggedOut = payload
    },

    setGoogleLoading (state, payload) {
      state.googleLoading = payload
    },

    setOutlookLoading (state, payload) {
      state.outlookLoading = payload
    },

    setAppleLoading (state, payload) {
      state.appleLoading = payload
    },

    setStripeLoading (state, payload) {
      state.stripeLoading = payload
    },

    setZoomLoading (state, payload) {
      state.zoomLoading = payload
    },

    setSpacesLoading (state, payload) {
      state.spacesLoading = payload
    },

    setZoomUsers (state, payload) {
      state.zoomUsers = payload
    },

    setSpaces (state, payload) {
      state.spaces = payload
    },

    setGoogleCalendars (state, payload) {
      state.googleCalendars = payload
    },

    setOutlookCalendars (state, payload) {
      state.outlookCalendars = payload
    },

    setAppleCalendars (state, payload) {
      state.appleCalendars = payload
    },

    setStripeProvider (state, payload) {
      state.stripeProvider = payload
    },

    setLoadingAppointmentsCounter (state, payload) {
      state.loadingAppointmentsCounter = payload
    },

    setLoadingEventsCounter (state, payload) {
      state.loadingEventsCounter = payload
    },

    setPreloadedEvents (state, payload) {
      state.preloaded.events = payload
    },

    setPreloadedCustomers (state, payload) {
      state.preloaded.customers = payload
    },
  },

  actions: {
    logout ({commit}) {
      const vueCookies = useCookies()['cookies']
      commit('setToken', null)
      commit('setPassword', '')
      vueCookies.remove('ameliaToken')
      commit('setAuthenticated', false)
      commit('setLoggedOut', true)
      localStorage.removeItem('vue-authenticate.vueauth_token')

      try {
        httpClient.post(
            '/users/logout',
            {},
            {}
        )

        if (window?.ameliaBooking?.cabinet?.userLoggedOut) {
          window.ameliaBooking.cabinet.userLoggedOut()
        }
      } catch (error) {
        console.log(error)
      }
    }
  }
}
