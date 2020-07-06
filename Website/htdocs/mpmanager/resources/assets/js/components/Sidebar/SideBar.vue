<template>
    <div
        class="sidebar"
        :data-color="sidebarItemColor"
        :style="sidebarStyle"
    >
        <div class="logo">
            <div class="brand-column">

                <img class="logo" alt="logo" :src="imgLogo"/>

                <div class="company-header">MicroPowerManager<br>Open Source</div>
            </div>


        </div>

        <div class="ramaining-content" style="display: inline-flex">
            <div class="md-layout md-gutter md-alignment-center-left p-15">
                <div class="md-layout-item md-size-40" style="margin-right: 1vh">
                    <font-awesome-icon icon="user-circle" class="fa-4x c-white"/>
                </div>

                <div class="md-layout">
                    <div class="md-layout-item md-size-100 c-white">
                        <span class="admin-text">{{adminName}}</span>
                    </div>

                    <div class="md-layout-item md-size-100 c-gray">
                        <font-awesome-icon icon="clock" swap-opacity/>
                        <small>{{remaining}}</small>
                    </div>
                </div>


            </div>

        </div>
        <div class="sidebar-wrapper">
            <slot name="content"></slot>
            <md-list class="no-bg p-15" md-expand-single>
                <component :is="'route' in menu ? 'router-link' : 'div'" v-for="(menu,index) in menus" :key="index"
                           :md-expand="'children' in menu"
                           :to="menu.route">
                    <md-list-item :md-expand="'children' in menu">
                        <!-- add icon if icon is defined -->
                        <font-awesome-icon :icon="menu.icon" v-if="'icon' in menu" class="c-white icon-box"
                        />

                        <span class="md-list-item-text c-white">{{menu.name}}</span>
                        <md-list slot="md-expand" v-if="'children' in menu" class="no-bg">
                            <md-list-item v-for="(sub,index) in menu.children"
                                          :key="index"

                            >
                                <router-link :to="sub.route" class="sub-menu">
                                    <md-list-item class="md-inset c-white">
                                        <span class="md-list-item-text c-white"> {{sub.name}}</span>
                                    </md-list-item>
                                </router-link>
                            </md-list-item>
                        </md-list>
                    </md-list-item>
                </component>
            </md-list>
        </div>
    </div>
</template>
<script>

    import store from '../../store/store'
    export default {

        data: () => (
            {
                show_extender: false,
                admin: null,
                menus: [
                    {
                        name: 'Dashboard',
                        icon: 'home',
                        children: [
                            {
                                name: 'Clusters',
                                route: '/'
                            },
                            {
                                name: 'Mini-Grid',
                                route: '/dashboards/mini-grid'
                            },
                        ]
                    },
                    {
                        name: 'Customers',
                        route: '/people/page/1',
                        icon: 'user-friends',
                    },
                    {
                        name: 'Meters',
                        route: '/meters/page/1',
                        icon: 'bolt',
                    },
                    {
                        name: 'Transactions',
                        route: '/transactions/page/1',
                        icon: 'university',
                    },
                    {
                        name: 'Tickets',
                        icon: 'ticket-alt',
                        children: [
                            {
                                name: 'List',
                                route: '/tickets'
                            },
                            {
                                name: 'Users',
                                route: '/tickets/settings/users'
                            },
                            {
                                name: 'Categories',
                                route: '/tickets/settings/categories'
                            },
                        ]
                    },
                    {
                        name: 'Tariffs',
                        route: '/tariffs',
                        icon: 'charging-station',
                    },
                    {
                        name: 'Targets',
                        route: '/targets',
                        icon: 'bullseye',
                    },
                    {
                        name: 'Reports',
                        route: '/reports',
                        icon: 'file-excel',
                    },
                    {
                        name: 'Sms',
                        icon: 'sms',
                        children: [
                            {
                                name: 'Sms List',
                                route: '/sms/list'
                            },
                            {
                                name: 'New Sms',
                                route: '/sms/newsms'
                            },

                        ]
                    },
                    {
                        name: 'Appliance Types',
                        route: '/appliance/types/page/1',
                        icon: 'toolbox',
                    },
                    {
                        name: 'Maintenance',
                        route: '/maintenance',
                        icon: 'wrench',
                    },
                    {
                        name: 'Locations',
                        icon: 'map-marker',
                        children: [
                            {
                                name: 'Add Cluster',
                                route: '/locations/add-cluster',
                            },
                            {
                                name: 'Add MiniGrid',
                                route: '/locations/add-mini-grid',
                            },
                            {
                                name: 'Add Village',
                                route: '/locations/add-village',
                            }
                        ]
                    }
                ]
            }
        ),
        props: {
            title: {
                type: String,
                default: 'MicroPowerManager Open Source'
            },
            sidebarBackgroundImage: {
                type: String,
                default: null
            },
            imgLogo: {
                type: String,
                default: require('../../../images/Logo1.png')
            },
            sidebarItemColor: {
                type: String,
                default: 'green',

            },

            autoClose: {
                type: Boolean,
                default: true
            }
        },
        provide () {
            return {
                autoClose: this.autoClose
            }
        },
        computed: {
            adminName () {
                return this.$store.getters['auth/getAuthenticateUser'].name
            },
            remaining () {
                let remaining_time = this.$store.getters['auth/getAuthenticateUser'].remaining_time
                let remaining_seconds = (remaining_time % 60).toString()
                return Math.floor(remaining_time / 60).toString() + ':' + ('0' + remaining_seconds).slice(-2)
            },
            sidebarStyle () {
                return {

                    background: '#2b2b2b !important'
                }
            }
        }
    }
</script>
<style>
    .brand-column {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        overflow: visible;
        margin-top: 0px;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center;
        grid-auto-columns: 1fr;
        grid-column-gap: 16px;
        grid-row-gap: 16px;
        -ms-grid-columns: 1fr 1fr;
        grid-template-columns: 1fr 1fr;
        -ms-grid-rows: auto auto;
        grid-template-rows: auto auto;
        -o-object-fit: fill;
        object-fit: fill;
    }

    .brand-column {
        text-align: center;
        padding-left: 2rem !important
    }

    @media screen and (min-width: 991px) {
        .nav-mobile-menu {
            display: none;
        }
    }

    .company-header {
        color: white;
        font-weight: bold;
    }

    .active-link {
        background-color: rgba(32, 66, 32, 0.74);

    }

    .exact-active {
        background: #6b6a6a !important;
        position: relative;
        left: -15px;
        width: calc(100% + 30px) !important;
    }

    /*  .md-list-item-text {
          color: #f5e8e8 !important;

      }*/

    .no-bg {
        background-color: transparent !important;
    }

    .c-white {
        color: #f5e8e8 !important;
    }

    .sidebar-layout {
        position: absolute;
        height: 100%;
        width: 100%;

    }

    .icon-box {
        margin-right: 15px;
        width: 25px !important;
        height: 25px !important;

    }

    .sub-menu {
        width: 100% !important;
    }

    .c-gray {
        color: gray;
    }

    .admin-text {
        font-size: 1.2rem;
    }

    .app-style {
        width: calc(100% / 12 * 2);
        position: fixed;
    }

    .drawer-style {
        background-color: #2b2b2b !important;
        height: 100vh;
    }

    .p-15 {
        padding: 15px;
    }

    .ramaining-content {
        display: inline-flex;
        padding-left: 0.7rem;
    }
</style>
