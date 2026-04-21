import moment from 'moment';
import {useUrlQueryParams} from "../../assets/js/common/helper";

function getDateString (date) {
  return moment(date).format('YYYY-MM-DD')
}

function formatDateRange (dateArray) {
  if (!dateArray.length) return []

  // Handle single date case
  if (!dateArray[1]) {
    return [getDateString(dateArray[0])]
  }

  // Handle date range case
  const startDate = getDateString(dateArray[0])
  const endDate = getDateString(dateArray[1])

  return [startDate, endDate]
}

export default {
  namespaced: true,

  state: () => ({
    params: {
      id: null,
      tag: null,
      search: null,
      recurring: null,
      dates: [moment().toDate()],
      upcomingDates: [moment().toDate()],
      locationId: null,
      locations: null,
      providers: null,
      range: null,
    },
    shortcodeParams: {
      ids: null,
      tags: null,
      locations: null
    }
  }),

  getters: {
    getTag (state) {
      return state.params.tag
    },

    getSearch (state) {
      return state.params.search
    },

    getLocationIdParam (state) {
      return state.params.locationId
    },

    getLocations (state) {
      return state.params.locations
    },

    getEmployees (state) {
      return state.params.providers
    },

    getDates (state) {
      return state.params.dates
    },

    getUpcomingDates (state) {
      return state.params.upcomingDates
    },

    getEventParams (state) {
      let locations = state.params.locationId && !state.params.locations
        ? { locationId: state.params.locationId }
        : state.params.locations && !state.params.locationId
          ? { locations: state.params.locations }
          : { locations: state.shortcodeParams.locations };

      return Object.assign({
          dates: formatDateRange(state.params.dates),
          id: state.params.id ? state.params.id : state.shortcodeParams.ids,
          search: state.params.search,
          tag: state.params.tag ? state.params.tag : state.shortcodeParams.tags,
          recurring: state.params.recurring,
          providers: state.params.providers,
          range: state.params.range
        },
        locations
      )
    },

    getUpcomingEventParams (state) {
      let locations = state.params.locationId && !state.params.locations
        ? { locationId: state.params.locationId }
        : state.params.locations && !state.params.locationId
          ? { locations: state.params.locations }
          : { locations: state.shortcodeParams.locations };

      return Object.assign({
          dates: state.params.upcomingDates[1] ? [getDateString(state.params.upcomingDates[0]), getDateString(state.params.upcomingDates[1])] : [getDateString(state.params.upcomingDates[0])],
          id: state.params.id ? state.params.id : state.shortcodeParams.ids,
          search: state.params.search,
          tag: state.params.tag ? state.params.tag : state.shortcodeParams.tags,
          recurring: state.params.recurring,
          providers: state.params.providers
        },
        locations
      )
    },

    getShortcodeParams (state) {
      return {
        ids: state.shortcodeParams.ids,
        tags: state.shortcodeParams.tags,
        locations: state.shortcodeParams.locations
      }
    },

    getAllData (state) {
      return {
        dates: state.params.dates,
        upcomingDates: state.params.upcomingDates,
        id: state.params.id,
        search: state.params.search,
        locationId: state.params.locationId,
        tag: state.params.tag,
        recurring: state.params.recurring,
        providers: state.params.providers
      }
    }
  },

  mutations: {
    setTag (state, payload) {
      state.params.tag = payload ? payload : null
    },

    setLocationIdParam (state, payload) {
      state.params.locationId = payload ? payload : null
    },

    setLocations (state, payload) {
      state.params.locations = payload ? payload : null
    },

    setEmployees (state, payload) {
      state.params.providers = payload ? payload : null
    },

    setId (state, payload) {
      state.params.id = payload
    },

    // * Params for Events
    // ! needs to change name of function
    setParams (state, payload) {
      let urlParameters = useUrlQueryParams(window.location.href)

      if (payload.eventId) {
        state.shortcodeParams.ids = payload.eventId.split(',')
      }
      if (urlParameters && urlParameters.ameliaEventId) {
        state.shortcodeParams.ids = urlParameters.ameliaEventId.split(',')
      }

      if (payload.eventTag) {
        state.shortcodeParams.tags = payload.eventTag.split("{").map(e => e.replace('},', '').replace('}', '')).filter(e => e !== '')
      }
      if (urlParameters && urlParameters.ameliaEventTag) {
        state.shortcodeParams.tags = urlParameters.ameliaEventTag.split(',')
      }

      if (payload.locationId) {
        state.shortcodeParams.locations = payload.locationId.split(',')
      }
      if (urlParameters && urlParameters.ameliaLocationId) {
        state.shortcodeParams.locations = urlParameters.ameliaLocationId.split(',')
      }

      if (payload.eventRecurring) {
        state.params.recurring = payload.eventRecurring
      }

      if (payload.range) {
        state.params.dates = []
        if (payload.range === 'past' || payload.range === 'future') {
          state.params.range = payload.range
        }

        if (payload.range.includes(' - ')) {
          state.params.range = payload.range.split(' - ').map(date => moment(date.trim()).format('YYYY-MM-DD'))
        }
      }
    },

    setSearch (state, payload) {
      state.params.search = payload ? payload : null
    },

    setDates (state, payload) {
      state.params.dates = payload
    },

    setUpcomingDates (state, payload) {
      state.params.upcomingDates = payload
    },

    setAllData (state, payload) {
      state.params = {
        dates: payload.dates,
        upcomingDates: payload.upcomingDates,
        id: payload.id ? parseInt(payload.id) : null,
        search: payload.search,
        locationId: payload.locationId ? parseInt(payload.locationId) : null,
        tag: payload.tag,
        recurring: payload.recurring,
        providers: payload.providers
      }
    }
  },

  actions: {}
}