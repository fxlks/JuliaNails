export default {
  namespaced: true,

  state: () => ({
    customFieldsArray: [],
    customFields: {},
  }),

  getters: {
    getFilteredCustomFieldsArray (state) {
      return state.customFieldsArray
    },
    getCustomFields (state) {
      return state.customFields
    },

    getCustomFieldValue: (state) => (field) => {
      return state.customFields[field].value
    },

    getAllData (state) {
      return {
        // customFieldsArray: state.customFieldsArray,
        customFields: state.customFields,
      }
    }
  },

  mutations: {
    setFilteredCustomFieldsArray (state, payload) {
      state.customFieldsArray = payload
    },

    setCustomFields (state, payload) {
      state.customFields = payload
    },

    setCustomField (state, payload) {
      state.customFields[payload.key] = payload
    },

    setCustomFieldValue (state, payload) {
      state.customFields[payload.key].value = payload.value
    },

    setAllData (state, payoad) {
      // state.customFieldsArray = payoad.customFieldsArray
      state.customFields = payoad.customFields
    },

    populateCustomerCustomFields (state, payload) {
      let customerCustomFields = payload.customFields
        ? JSON.parse(payload.customFields) : {}
      let populateAvailableCustomFields = state.customFields

      for (let id in customerCustomFields) {
        if (populateAvailableCustomFields.hasOwnProperty('cf' + id)) {
          populateAvailableCustomFields['cf' + id].value = customerCustomFields[id].value
        }
      }

      state.customFields = populateAvailableCustomFields
    }
  },

  actions: {
    filterEventCustomFields ({ commit, getters, rootGetters }) {
      let eventId = rootGetters['eventBooking/getSelectedEventId']
      let filteredCustomFieldsArr = []
      let customFields = {}
      rootGetters['eventEntities/getCustomFields'].forEach(c => {
        if (c.events.find(event => event.id === parseInt(eventId)) || c.allEvents || c.saveType === 'customer') {
          filteredCustomFieldsArr.push(c)

          customFields[`cf${c.id}`] = {
            id: c.id,
            label: c.label,
            type: c.type,
            position: c.position,
            options: c.options,
            required: c.required,
            width: c.width,
            saveFirstChoice: c.saveFirstChoice,
            saveType: c.saveType
          }

          switch (c.type) {
            case ('checkbox'):
            case ('file'):
              customFields[`cf${c.id}`].value = []

              break

            default:
              customFields[`cf${c.id}`].value = ''
          }

          if (getters['getCustomFields'][`cf${c.id}`]) {
            customFields[`cf${c.id}`].value = getters['getCustomFields'][`cf${c.id}`]['value']
          }
        }
      })

      commit('setFilteredCustomFieldsArray', filteredCustomFieldsArr)
      commit('setCustomFields', customFields)
    }
  }
}
