export default {
  namespaced: true,

  state: () => ({
    searchFilterText: '',
    categoryFilter: null,
  }),

  getters: {
    getSearchFilterText(state) {
      return state.searchFilterText;
    },

    getCategoryFilter(state) {
      return state.categoryFilter;
    },
  },

  mutations: {
    setSearchFilterText(state, payload) {
      state.searchFilterText = payload;
    },

    setCategoryFilter(state, payload) {
      state.categoryFilter = payload;
    },
  },

  actions: {}
}
