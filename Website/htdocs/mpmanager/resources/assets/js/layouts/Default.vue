<template>
    <div>
        <div class="wrapper" :class="{ 'nav-open': $sidebar.showSidebar }">
            <side-bar/>
            <div class="main-panel">
                <top-navbar></top-navbar>
                <div class="content">
                    <slot/>
                </div>
                <footer-bar/>

            </div>
        </div>

        <md-dialog
            :md-active.sync="active"
            :md-close-on-esc="false"
            :md-click-outside-to-close="false"
        >
            <md-dialog-title>Session is about to expire</md-dialog-title>
            <md-dialog-content>
                Your session expires in <strong>{{expires_in}}</strong> seconds.<br>If you want to extend your session
                for the next 60(sixty) minutes, please confirm this window.
            </md-dialog-content>

            <md-dialog-actions>
                <md-button class="md-primary md-raised" @click="extendToken" :disabled="confirmed">Confirm &amp;
                    Extend
                </md-button>
            </md-dialog-actions>
        </md-dialog>
    </div>
</template>


<script>
import FooterBar from '../layouts/FooterBar.vue'
import { EventBus } from '../shared/eventbus'
import TopNavbar from './TopNavbar.vue'
import SideBar from '../components/Sidebar/SideBar'

export default {
    name: 'default',
    components: {
        TopNavbar,
        FooterBar,
        SideBar
    },
    mounted () {
        //register the time extender
        EventBus.$on('ask.for.extend', this.showExtender)
        EventBus.$on('session.end', this.logout)
    },

    data: () => ({

        active: false,
        showed: false,
        confirmed: false,
        expires_in: null,
        sidebarBackground: 'green',
        sidebarBackgroundImage: null
    }),
    methods: {
        showExtender (val) {
            this.expires_in = val
            if (this.showed === true) {
                return
            }
            this.showed = true
            this.active = true
        },
        extendToken () {
            this.confirmed = true
            location.reload()
        },
        logout () {
            this.$store.dispatch('auth/logOut').then(() => {
                this.$router.replace('/login')
            })

        }
    }
}
</script>


<style lang="css" scoped>
    .container {
        padding: 1rem;
    }

    @media screen and (min-width: 991px) {
        .sidebar {
            width: 15%;
            min-width: 260px;

        }

        .main-panel {
            width: 85%;
            max-width: calc(100% - 260px);
        }
    }


</style>
