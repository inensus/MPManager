import Vue from 'vue'
import Vuex from 'vuex'

import { Person } from './classes/person'
import { Meters } from './classes/person/meters'
import { Admin } from './classes/admin'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        person: new Person(),
        meters: new Meters(),
        admin: new Admin(),
        search: {},
    },
    getters: {
        person: state => state.person,
        meters: state => state.meters,
        admin: state => state.admin,
        search: state => state.search,
    }

})
