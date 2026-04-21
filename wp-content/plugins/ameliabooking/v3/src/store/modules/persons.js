export default {
  namespaced: true,

  state: () => ({
    persons: 1,
    max: 1,
    min: 1,
  }),

  getters: {
    getMaxPersons (state) {
      return state.max
    },

    getMinPersons (state) {
      return state.min
    },

    getPersons (state) {
      return state.persons
    },

    getAllData (state) {
      return {
        persons: state.persons,
        max: state.max,
        min: state.min
      }
    }
  },

  mutations: {
    setMaxPersons (state, payload) {
      state.max = payload
    },

    setMinPersons (state, payload) {
      state.min = payload
    },

    setPersons (state, payload) {
      state.persons = payload
    },

    setAllData (state, payload) {
      state.persons = payload.persons
      state.max = payload.max
      state.min = payload.min
    }
  },

  actions: {
    resetPersons ({ commit }) {
      commit('setPersons', 1)
      commit('setMaxPersons', 1)
      commit('setMinPersons', 1)
    }
  }
}