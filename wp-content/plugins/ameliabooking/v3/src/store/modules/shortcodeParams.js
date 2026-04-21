export default {
  namespaced: true,

  state: () => ({
    form: null,
    preselected: {},
  }),

  getters: {
    getForm (state) {
      return state.form
    },

    getShortcodeParams (state) {
      return state.preselected
    }
  },

  mutations: {
    setForm (state, payload) {
      state.form = payload
    },

    setShortcodeParams (state, payload) {
      state.preselected = payload
    },
  },

  actions: {}
}
