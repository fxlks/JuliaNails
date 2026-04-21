export default {
  namespaced: true,

  state: () => ({
    show: 1,
    page: 1,
    count: 0
  }),

  getters: {
    getShow (state) {
      return state.show
    },

    getPage (state) {
      return state.page
    },

    getCount (state) {
      return state.count
    },

    getAllData (state) {
      return {
        show: state.show,
        page: state.page,
        count: state.count
      }
    }
  },

  mutations: {
    setShow (state, payload) {
      state.show = payload
    },

    setPage (state, payload) {
      state.page = payload
    },

    setCount (state, payload) {
      state.count = payload
    },

    setAllData (state, payload) {
      state.show = parseInt(payload.show)
      state.page = parseInt(payload.page)
      state.count = parseInt(payload.count)
    }
  },

  actions: {}
}