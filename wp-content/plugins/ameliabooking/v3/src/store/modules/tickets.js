export default {
  namespaced: true,

  state: () => ({
    tickets: [],
    ticketsData: [],
    maxCustomCapacity: null,
    maxExtraPeople: null,
    globalSpots: 0
  }),

  getters: {
    getMaxCustomCapacity (state) {
      return state.maxCustomCapacity
    },

    getMaxExtraPeople (state) {
      return state.maxExtraPeople
    },

    getTicketNumber: (state) => (id) => {
      return state.ticketsData.find(t => t.id === id).persons
    },

    getTicketsSum (state) {
      let persons = 0
      state.ticketsData.forEach(t => {
        persons += t.persons
      })

      return persons
    },

    getTicketsData (state) {
      return state.ticketsData
    },

    getEventGlobalSpots (state) {
      return state.globalSpots
    },

    getAllData (state) {
      return {
        tickets: state.tickets,
        ticketsData: state.ticketsData,
        maxCustomCapacity: state.maxCustomCapacity,
        maxExtraPeople: state.maxExtraPeople,
        globalSpots: state.globalSpots
      }
    }
  },

  mutations: {
    setTickets (state, payload) {
      state.tickets = payload
      payload.forEach(item => {
        if (item.enabled) {
          let obj = {
            spots: item.spots,
            sold: item.sold,
            persons: 0,
            price: item.dateRangePrice ? item.dateRangePrice : item.price,
            name: item.name,
            id: item.id,
            eventTicketId: item.id,
            waiting: item.waiting,
            waitingListSpots: item.waitingListSpots
          }
          state.ticketsData.push(obj)
        }
      })
    },

    setMaxCustomCapacity (state, payload) {
      state.maxCustomCapacity = payload
    },

    setReduceMaxExtraPeople (state, payload) {
      state.maxExtraPeople = (state.maxExtraPeople + 1) - payload
    },

    setMaxExtraPeople (state, payload) {
      state.maxExtraPeople = payload
    },

    setTicketNumber (state, payload) {
      state.ticketsData.forEach(t => {
        if (t.id === payload.id) t.persons = parseInt(payload.numb)
      })
    },

    setEventGlobalSpots (state, payload) {
      state.globalSpots += payload
    },

    setAllData (state, payload) {
      state.tickets = payload.tickets
      state.ticketsData = payload.ticketsData
      state.maxCustomCapacity = payload.maxCustomCapacity
      state.maxExtraPeople = payload.maxExtraPeople
      state.globalSpots = payload.globalSpots
    }
  },

  actions: {
    resetCustomTickets ({commit}) {
      commit('setAllData', {
        tickets: [],
        ticketsData: [],
        maxCustomCapacity: null,
        maxExtraPeople: null,
        globalSpots: 0
      })
    }
  }
}
