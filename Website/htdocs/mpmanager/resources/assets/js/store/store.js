import Vue from 'vue'
import Vuex from 'vuex'
import { Person } from '../classes/person'
import { Meters } from '../classes/person/meters'
import { Admin } from '../classes/admin'
import { MainSettingsService} from '../services/MainSettingsService'
import * as auth from '../store/modules/authentication'
import VuexPersist from 'vuex-persist'
import { MapSettingsService } from '../services/MapSettingsService'

Vue.use(Vuex)
const vuexLocalStorage = new VuexPersist({
    reducer: (state) => ({
        auth: {
            authenticateUser: state.auth.authenticateUser,

        }
    }),

    key: 'vuex',
    storage: window.localStorage,
})
export default new Vuex.Store({
    modules: {
        auth
    },
    plugins: [vuexLocalStorage.plugin],
    state: {

        person: new Person(),
        meters: new Meters(),
        admin: new Admin(),
        mainSettings: new MainSettingsService(),
        mapSettingsService: new MapSettingsService(),
        mSettings:{
            site_title:null,
            company_name:null,
            currency:null,
            country:null,
            language:null
        },
        mapSettings:{
            zoom:null,
            center:[]
        },
        search: {},
    },
    mutations: {
        FETCH_SETTINGS(state,payload){
            state.mSettings = payload
        }
    },
    actions: {
        getMainSettings({ commit } ){
            let mainSettings = new MainSettingsService()
            mainSettings.list().then(res=>{
                commit('FETCH_SETTINGS',res)
                return res
            })
        }
    },
    getters: {

        person: state => state.person,
        meters: state => state.meters,
        admin: state => state.admin,
        search: state => state.search,
        mSettings: state => state.mSettings,
        mainSettings: state => state.mainSettings,
        mapSettingsService: state => state.mapSettingsService

    }

})
