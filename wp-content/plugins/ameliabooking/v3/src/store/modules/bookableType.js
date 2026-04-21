export default {
  namespaced: true,

  state: () => ({
    type: ''
  }),

  getters: {
    getType (state) {
      return state.type
    },
  },

  mutations: {
    setType (state, payload) {
      state.type = payload
    },
  },

  actions: {}
}