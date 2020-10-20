<template>

    <md-toolbar md-elevation="0" style="background:#424040">
        <div class="md-toolbar-row">

            <div class="md-toolbar-section-end">
                <div class="nav-buttons">
                    <md-menu
                        class="settings-menu"
                        md-size="medium"
                        :md-offset-x="120"
                        :md-offset-y="-30"
                        :md-active.sync="toggleCard"
                    >
                        <md-button class="btn-menu md-raised" md-menu-trigger>
                            <md-icon style="color:white">settings</md-icon>
                        </md-button>
                        <md-menu-content class="menu-content m-pad">
                            <md-menu-item class="menu-item">
                                <router-link :to="{path: '/profile'}" class="link">
                                    <md-icon>person</md-icon>
                                    Profile
                                </router-link>
                            </md-menu-item>
                            <md-menu-item>
                                <router-link :to="{path: '/profile/management'}" class="link">
                                    <md-icon>people</md-icon>
                                    User Management
                                </router-link>
                            </md-menu-item>
                        </md-menu-content>
                    </md-menu>
                    <button class="btn-close md-raised " @click="logout()">
                        <md-icon style="color:white">exit_to_app</md-icon>
                    </button>
                    <md-button
                        class="md-just-icon md-simple md-toolbar-toggle"
                        :class="{ toggled: $sidebar.showSidebar }"
                        @click="toggleSidebar"
                        style="vertical-align: bottom !important;"
                    >
                        <md-icon>format_align_justify</md-icon>
                    </md-button>
                </div>


            </div>
        </div>
    </md-toolbar>
</template>

<script>
export default {
    data () {
        return {
            open: false,
            toggleCard: false

        }
    },
    methods: {
        logout () {
            this.$store.dispatch('auth/logOut').then(() => {
                this.$router.replace('/login')
            })

        },
        toggle () {
            this.toggleCard = !this.toggleCard
        },
        toggleSidebar () {
            this.$sidebar.displaySidebar(!this.$sidebar.showSidebar)
        }
    }

}
</script>

<style lang="css">
    .btn-close {
        background-color: #fb3c4a;
        border: 1.5px solid #fb3c4a;
        height: 100% !important;
        cursor: pointer;
        vertical-align: bottom !important;
    }

    .btn-menu {
        min-width: 0px !important;
        height: 100% !important;
        background-color: #fb3c4a !important;
        vertical-align: bottom !important;
    }

    .navbar-settings-list > li {
        padding-right: 1rem;
        min-height: 30px;
    }

    .settings-menu {
        height: 30px;
        vertical-align: bottom !important;
    }

    .menu-content .link {
        padding: 5px;
        text-decoration: none;
        color: #3d3b3f;
    }

    .menu-content .link:hover {
        color: #3d3b3f;
    }

    .menu-content .menu-item:nth-child(1) {
        border-bottom: 0.5px solid #75727ae0;
    }

    .menu-content .link .active {
        color: #3d3b3f;
    }

    .nav-buttons {

    }
</style>
