<template>

    <md-toolbar md-elevation="0" style="background:#424040">
        <div class="md-toolbar-row">

            <div class="md-toolbar-section-end">
                <div>
                    <md-menu
                        md-direction="bottom-end"
                        md-size="big"
                        class="menu-item">
                        <md-button  class=" md-dense nav-button md-raised" md-menu-trigger>
                            <md-tooltip md-direction="bottom">{{$tc('words.location',2)}}</md-tooltip>
                             <md-icon style="color:white">add_location_alt</md-icon>
                            <small>Locations</small>
                        </md-button>
                        <md-menu-content>
                            <md-menu-item disabled>
                                <span>{{$tc('words.location',2)}}</span>
                                <md-icon>add_location_alt</md-icon>
                                </md-menu-item>
                            <md-menu-item @click="replaceRoute('/locations/add-cluster')">
                                          {{$tc('menu.subMenu.Add Cluster')}}
                            </md-menu-item>
                            <md-menu-item @click="replaceRoute('/locations/add-mini-grid')">
                                {{$tc('menu.subMenu.Add MiniGrid')}}
                            </md-menu-item>
                            <md-menu-item @click="replaceRoute('/locations/add-village')">
                                {{$tc('menu.subMenu.Add Village')}}
                            </md-menu-item>
                        </md-menu-content>
                    </md-menu>
                    <md-menu
                        class="menu-item"
                        md-direction="bottom-end"
                        md-size="big">
                        <md-button class="nav-button md-raised md-dense " md-menu-trigger>
                            <md-tooltip md-direction="bottom">{{$tc('words.profile')}}</md-tooltip>
                            <md-icon style="color:white">person</md-icon>
                            <small>Profile</small>
                        </md-button>
                        <md-menu-content>
                            <md-menu-item disabled="">
                                <span>{{$tc('words.profile')}}</span>
                                <md-icon>person</md-icon>
                                </md-menu-item>
                            <md-menu-item  @click="replaceRoute('/profile')">
                                    {{$tc('words.profile')}}
                            </md-menu-item>
                            <md-menu-item @click="replaceRoute('/profile/management')">
                                    {{$tc('phrases.userManagement')}}
                            </md-menu-item>
                        </md-menu-content>
                    </md-menu>
                    <md-menu
                        class="menu-item"
                        md-direction="bottom-end"
                        md-size="big">
                        <md-button class=" md-dense nav-button md-raised" md-menu-trigger>
                            <md-tooltip md-direction="bottom">Settings</md-tooltip>
                            <md-icon style="color:white">settings</md-icon>
                            <small>Settings</small>
                        </md-button>
                        <md-menu-content>
                            <md-menu-item @click="replaceRoute('/settings')">
                                <span>Settings</span>
                                <md-icon>settings</md-icon>
                            </md-menu-item>
                            <md-divider></md-divider>
                            <md-menu-item disabled="">
                                <span>{{$tc('words.connection')}}</span>
                                <md-icon>cast</md-icon>
                            </md-menu-item>
                            <md-menu-item @click="replaceRoute('/connection-groups')">
                                {{$tc('words.group',2)}}</md-menu-item>
                            <md-menu-item @click="replaceRoute('/connection-types')">
                                {{$tc('words.type',2)}}
                            </md-menu-item>
                        </md-menu-content>
                    </md-menu>
                    <md-menu class="menu-item">
                        <md-button class="md-dense md-raised md-accent md-icon-button" @click="logout()">
                            <md-tooltip md-direction="bottom">Log Out</md-tooltip>
                            <md-icon style="color:white">exit_to_app</md-icon>

                        </md-button>
                    </md-menu>

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
        },
        replaceRoute(route){
            this.$router.replace(route)
        }

    }

}
</script>

<style lang="css">

.nav-button{
    background-color: #3d3d3d!important;
    color: white!important;
}
.menu-item{
    padding-right: 1vh
}

</style>
