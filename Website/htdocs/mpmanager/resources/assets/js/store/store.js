import Vue from 'vue'
import Vuex from 'vuex'
import {Person} from '../classes/person'
import {Meters} from '../classes/person/meters'
import {Admin} from '../classes/admin'
import * as auth from '../store/modules/authentication'
import VuexPersist from 'vuex-persist';
Vue.use(Vuex);
const vuexLocalStorage = new VuexPersist({
    key: 'vuex',
    storage: window.localStorage,
});
export default new Vuex.Store({
    modules: {
        auth
    },
    plugins: [vuexLocalStorage.plugin],
    state: {

        person: new Person(),
        meters: new Meters(),
        admin: new Admin(),
        search: {},
    },
    mutations: {},
    actions: {},
    getters: {

        person: state => state.person,
        meters: state => state.meters,
        admin: state => state.admin,
        search: state => state.search,
    }

})
