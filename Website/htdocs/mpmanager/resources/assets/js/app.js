/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import { EventBus } from './shared/eventbus'

require('./bootstrap')
import router from './routes'
import Appp from '../js/Appp'
import Breadcrumb from './shared/Breadcrumb'
import 'leaflet/dist/leaflet.css'
import store from './store/store'
import { Admin } from './classes/admin'
import UserData from './shared/UserData'
import Default from './layouts/Default'

Vue.component('default', Default)

router.beforeEach((to, from, next) => {
    let authToken = store.getters['auth/getToken']

    if (to.name !== 'login' && to.name !== 'forgot-password') {
        if (authToken !== undefined && authToken !== '') {

            store.dispatch('auth/refreshToken', authToken).then((result) => {
                if (result) {
                    next()
                } else {
                    next({ name: 'login' })
                }
            }).catch((e) => {
                next({ name: 'login' })
            })
        } else {
            next({ name: 'login' })
        }

    } else {
        next()
    }
    //if (to.name !== 'Login' && !isAuthenticated) next({ name: 'Login' })
    //else next()
})

const app = new Vue({
    el: '#app',
    components: {
        Breadcrumb,
        UserData,

    },

    mounted () {
        this.$el.addEventListener('click', this.onHtmlClick)
    },
    methods: {},

    router: router,
    store: store,

    render: h => h(Appp)
})
