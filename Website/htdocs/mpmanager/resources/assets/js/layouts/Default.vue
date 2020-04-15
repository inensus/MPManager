<template>
    <div>
        <vue-grid align="stretch" justify="start">
            <vue-cell width="12of12" style="min-height: 51px">
                <nav-bar/>
            </vue-cell>
            <vue-cell width="2of12">
                <side-bar/>
            </vue-cell>

            <vue-cell width="10of12">
                <div class="container">
                    <slot/>
                </div>
            </vue-cell>
        <!--    <vue-cell width="12of12">
                <footer-bar/>
            </vue-cell>-->
        </vue-grid>
        <!-- extend token timer -->
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
    import NavBar from '../layouts/NavBar.vue'
    import SideBar from '../layouts/SideBar.vue'
    import FooterBar from '../layouts/FooterBar.vue'
    import { EventBus } from '../shared/eventbus'

    export default {
        name: 'default',
        components: {
            NavBar,
            SideBar,
            FooterBar
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

                this.$router.replace('/login')
            }
        }
    }
</script>


<style lang="css" scoped>
    .container {
        padding: 1rem;
    }
</style>
