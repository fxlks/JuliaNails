export default {
  namespaced: true,

  state: () => ({
    repeatType: 'daily',
    repeatInterval: 1,
    occurrenceType: 'After',
    occurrenceDate: null,
    occurrenceCount: 1,
    days: [
      {value: 'monday', selected: false},
      {value: 'tuesday', selected: false},
      {value: 'wednesday', selected: false},
      {value: 'thursday', selected: false},
      {value: 'friday', selected: false},
      {value: 'saturday', selected: false},
      {value: 'sunday', selected: false}
    ],
    monthly: 0,
  }),

  getters: {
    getRepeatType (state) {
      return state.repeatType
    },
    getRepeatInterval (state) {
      return state.repeatInterval
    },
    getOccurrenceType (state) {
      return state.occurrenceType
    },
    getOccurrenceDate (state) {
      return state.occurrenceDate
    },
    getOccurrenceCount (state) {
      return state.occurrenceCount
    },
    getMonthly (state) {
      return state.monthly
    },
    getDays (state) {
      return state.days
    },
  },

  mutations: {
    setRepeatType (state, payload) {
      state.repeatType = payload
    },
    setRepeatInterval (state, payload) {
      state.repeatInterval = payload
    },
    setOccurrenceType (state, payload) {
      state.occurrenceType = payload
    },
    setOccurrenceDate (state, payload) {
      state.occurrenceDate = payload
    },
    setOccurrenceCount (state, payload) {
      state.occurrenceCount = payload
    },
    setMonthly (state, payload) {
      state.monthly = payload
    },
    setDays (state, payload) {
      state.days.find(day => day.value === payload.value).selected = payload.selected
    },
  },

  actions: {}
}