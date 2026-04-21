export default {
  namespaced: true,

  state: () => ({
    amount: 0,
    gateway: '',
    deposit: false,
    depositAmount: 0,
    depositType: '',
    data: {},
    error: '',
    onSitePayment: false
  }),

  getters: {
    getError (state) {
      return state.error
    },
    getPaymentGateway (state) {
      return state.gateway
    },
    getPaymentDeposit (state) {
      return state.deposit
    },
    getAllData (state) {
      return {
        amount: state.amount,
        gateway: state.gateway,
        deposit: state.deposit,
        depositAmount: state.depositAmount,
        depositType: state.depositType,
        data: state.data,
      }
    },
    getOnSitePayment (state) {
      return state.onSitePayment
    }
  },

  mutations: {
    setError (state, payload) {
      state.error = payload
    },
    setPaymentGateway (state, payload) {
      state.gateway = payload
    },
    setPaymentDeposit (state, payload) {
      state.deposit = payload
    },
    setPaymentDepositAmount (state, payload) {
      state.depositAmount = payload
    },
    setPaymentDepositType (state, payload) {
      state.depositType = payload
    },
    setAllData (state, payload) {
      state.amount = payload.amount
      state.gateway = payload.gateway
      state.deposit = payload.deposit
      state.depositAmount = payload.depositAmount
      state.depositType = payload.depositType
      state.data = payload.data
    },
    setOnSitePayment (state, payload) {
      state.onSitePayment = payload
    }
  },

  actions: {
    resetPaymentData ({commit}) {
      commit('setAllData', {
        amount: 0,
        gateway: '',
        deposit: false,
        depositAmount: 0,
        depositType: '',
        data: {},
      })
    }
  }
}