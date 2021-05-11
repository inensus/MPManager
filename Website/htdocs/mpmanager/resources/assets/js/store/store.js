import Vue from 'vue'
import Vuex from 'vuex'
import { Person } from '../classes/person'
import { Meters } from '../classes/person/meters'
import { Admin } from '../classes/admin'
import * as auth from '../store/modules/authentication'
import * as settings from '../store/modules/settings'
import * as resolution from '../store/modules/resolution'
import VuexPersist from 'vuex-persist'

Vue.use(Vuex)
const vuexLocalStorage = new VuexPersist({
    reducer: (state) => ({
        auth: {
            authenticateUser: state.auth.authenticateUser,
        },
        settings:{
            mainSettings: state.settings.mainSettings,
            ticketSettings: state.settings.ticketSettings,
            mapSettings:state.settings.mapSettings,
        },
        resolution:{
            width: state.resolution.width,
            height: state.resolution.height,
            isMobile: state.resolution.isMobile
        }
    }),
    key: 'vuex',
    storage: window.localStorage,
})
export default new Vuex.Store({
    modules: {
        auth,
        settings,
        resolution
    },
    plugins: [vuexLocalStorage.plugin],
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
        resolution: state => state.resolution
    }
})
