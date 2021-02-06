/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')
import router from './routes'
import Appp from '../js/Appp'
import Breadcrumb from './shared/Breadcrumb'
import 'leaflet/dist/leaflet.css'
import store from './store/store'
import UserData from './shared/UserData'
import Default from './layouts/Default'
import i18n from './i18n'
import { MapSettingsService } from './services/MapSettingsService'
import { TicketSettingsService } from './services/TicketSettingsService'
import { MainSettingsService } from './services/MainSettingsService'

Vue.component('default', Default)

router.beforeEach((to, from, next) => {
    const authToken = store.getters['auth/getToken']

    if (authToken === undefined || authToken === '') {
        return next({ name: 'login' })
    }
    if (['login', 'forgot_password'].includes(to.name)) {
        return next()
    }
    store.dispatch('auth/refreshToken', authToken).then((result) => {
        return result ? next() : next({ name: 'login' })
    }).catch(() => {
        return next({ name: 'login' })
    })
    return next()
})

/*eslint-disable */
const app = new Vue({
    el: '#app',
    components: {
        Breadcrumb,
        UserData,

    },

    data () {
        return {
            mainSettingsService: new MainSettingsService(),
            mapSettingService: new MapSettingsService(),
            ticketSettingsService: new TicketSettingsService(),
        }
    },
    mounted () {
        this.$el.addEventListener('click', this.onHtmlClick)
    },
    router: router,
    store: store,
    i18n,
    render: h => h(Appp),
})
