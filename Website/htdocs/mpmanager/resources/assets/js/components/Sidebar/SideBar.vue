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
                <div class="md-layout-item md-size-40" >
                    <md-icon class="md-size-2x c-white">account_box</md-icon>
                </div>

                <div class="md-layout">
                    <div class="md-layout-item md-size-100 c-white">
                        <span class="admin-text">{{adminName}}</span>
                    </div>

                    <div class="md-layout-item md-size-100 c-gray">
                        <small><md-icon>access_alarm</md-icon></small>
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
                        <md-icon v-if="'icon' in menu" class="c-white icon-box">{{menu.icon}}</md-icon>
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
                    icon: 'supervisor_account',
                },
                {
                    name: 'Agents',

                    icon: 'support_agent',
                    children: [
                        {
                            name: 'List',
                            route: '/agents/page/1',
                        },
                        {
                            name: 'Commission Types',
                            route: '/commissions'
                        },
                    ]
                },
                {
                    name: 'Meters',
                    icon: 'bolt',
                    children: [
                        {
                            name: 'List',
                            route: '/meters/page/1',
                        },
                        {
                            name: 'Types',
                            route: '/meters/types'
                        },
                    ]

                },
                {
                    name: 'Transactions',
                    route: '/transactions/page/1',
                    icon: 'account_balance',

                },
                {
                    name: 'Tickets',
                    icon: 'confirmation_number',
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
                    icon: 'widgets',
                },
                {
                    name: 'Targets',
                    route: '/targets',
                    icon: 'gps_fixed',
                },
                {
                    name: 'Reports',
                    route: '/reports',
                    icon: 'text_snippet',
                },
                {
                    name: 'Connection',
                    icon: 'cast',
                    children: [
                        {
                            name: 'Groups',
                            route: '/connection-groups',
                        },
                        {
                            name: 'Types',
                            route: '/connection-types'
                        }
                    ]
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
                    name: 'Asset Types',
                    route: '/assets/types/page/1',
                    icon: 'devices_other',
                },
                {
                    name: 'Maintenance',
                    route: '/maintenance',
                    icon: 'home_repair_service',
                },
                {
                    name: 'Locations',
                    icon: 'add_location_alt',
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

