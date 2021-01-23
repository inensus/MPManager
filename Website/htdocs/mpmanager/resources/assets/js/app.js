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
    let authToken = store.getters['auth/getToken']

    if (to.name !== 'login'
    && !store.getters['auth/authenticationService'].authenticateUser.token
    && to.name !== 'forgot-password') {

        if (authToken !== undefined && authToken !== '') {
            store.dispatch('auth/refreshToken', authToken).then((result) => {
                if (result) {
                    next()
                } else {
                    next({ name: 'login' })
                }
            }).catch(() => {
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
  created () {
    this.setSettingsState()
  },
  mounted () {
    this.$el.addEventListener('click', this.onHtmlClick)

  },
  methods: {
    async setSettingsState () {

      let statedMainSettings = this.$store.getters['settings/getMainSettings']
      if (Object.keys(statedMainSettings).length < 1) {
        let mainSettings = await this.mainSettingsService.list()
        document.title = mainSettings.siteTitle
        this.$i18n.locale = mainSettings.language
        this.$store.dispatch('settings/setMainSettings', mainSettings).then(() => {
        }).catch((err) => {
          console.log(err)
        })
      } else {
        document.title = statedMainSettings.siteTitle
        this.$i18n.locale = statedMainSettings.language
      }

      let mapSettings = this.$store.getters['settings/getMapSettings']

      if (Object.keys(mapSettings).length < 1) {

        mapSettings = await this.mapSettingService.list()
        store.dispatch('settings/setMapSettings', mapSettings).then(() => {

        }).catch((err) => {
          console.log(err)
        })
      }

      let ticketSettings = this.$store.getters['settings/getTicketSettings']

      if (Object.keys(ticketSettings).length < 1) {
        ticketSettings = await this.ticketSettingsService.list()
        this.$store.dispatch('settings/setTicketSettings', this.ticketSettings).then(() => {

        }).catch((err) => {
          console.log(err)
        })
      }

    }
  },
  router: router,
  store: store,
  i18n,
  render: h => h(Appp)
})
