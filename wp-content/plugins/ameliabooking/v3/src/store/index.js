import { createStore } from 'vuex'
import entities from './modules/entities'
import booking from './modules/booking'
import eventEntities from './modules/eventEntities'
import eventBooking from './modules/eventBooking'

const store = createStore({
  modules: {
    entities,
    booking,
    eventEntities,
    eventBooking
  },
})

export default store
