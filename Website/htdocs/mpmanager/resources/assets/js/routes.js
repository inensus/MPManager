import VueRouter from 'vue-router'
import LoginFooter from './components/Login/LoginFooter'
import LoginHeader from './components/Login/LoginHeader'
import Login from './components/Login/Login'
import ForgotPassword from './components/Login/ForgotPassword'

let routes = [
    {
        path: '/login',
        name: 'login',
        components: { default: Login, header: LoginHeader, footer: LoginFooter },
        props: {
            header: { colorOnScroll: 400 }
        },
        meta: { requireAuth: false }
    },
    {

        path: '/forgot-password',
        name: 'forgot-password',
        components: { default: ForgotPassword, header: LoginHeader, footer: LoginFooter },

        meta: { requireAuth: false }
    },
    {
        path: '/',
        component: require('./components/ClustersDashboard/ClusterList').default,
        name: 'cluster-list-dashboard',
        meta: { layout: 'default' },
    },
    {
        path: '/dashboards/mini-grid/:id',
        component: require('./components/MiniGrid/Dashboard').default,
        meta: { layout: 'default' },
    },
    {
        path: '/dashboards/mini-grid/',
        component: require('./components/MiniGrid/Selector').default,
        meta: { layout: 'default' },
    },
    {
        path: '/reports',
        component: require('./components/ExportedReports/ReportsList').default,
        meta: { layout: 'default' },
    },
    {
        path: '/reports/page/:page_number',
        component: require('./components/ExportedReports/ReportsList').default,
        meta: { layout: 'default' },
    },

    {
        path: '/people',
        component: require('./components/Client/ClientList').default,
        meta: { layout: 'default' },
    },
    {
        path: '/people/page/:page_number',
        component: require('./components/Client/ClientList').default,
        meta: { layout: 'default' },
    },
    {
        path: '/people/:id',
        component: require('./components/Client/ClientDetail').default,
        meta: { layout: 'default' },
    },
    {
        //transaction list
        path: '/transactions',
        component: require('./components/Transactions/TransactionList').default,
        meta: { layout: 'default' },
    },
    {
        //transaction list
        path: '/transactions/page/:page',
        component: require('./components/Transactions/TransactionList').default,
        meta: { layout: 'default' },
    },
    {
        //transaction details
        path: '/transactions/:id',
        component: require('./components/Transactions/TransactionDetail').default,
        meta: { layout: 'default' },
    },
    {
        path: '/tickets',
        component: require('./components/Ticket/TicketList').default,
        meta: { layout: 'default' },
    },
    {
        path: '/tickets/settings/users',
        component: require('./components/Ticket/UserManagement').default,
        meta: { layout: 'default' },
    },
    {
        path: '/tickets/settings/categories',
        component: require('./components/Ticket/LabelManagement').default,
        meta: { layout: 'default' },
    },
    {
        path: '/tariffs',
        component: require('./components/Tariff/TariffList').default,
        meta: { layout: 'default' },
    },
    {
        path: '/tariffs/:id',
        component: require('./components/Tariff/TariffDetail').default,
        meta: { layout: 'default' },
    },
    {
        path: '/meters/list',
        component: require('./components/Meter/MeterList').default,
        meta: { layout: 'default' },

    },
    {
        path: '/meters/types',
        component: require('./components/Meter/Types').default,
        meta: { layout: 'default' },

    },
    {
        path: '/meters/page/:page_number',
        component: require('./components/Meter/MeterList').default,
        meta: { layout: 'default' },

    },
    {
        path: '/meters/:id',
        component: require('./components/Meter/MeterDetail').default,
        meta: { layout: 'default' },

    },
    {
        path: '/user-management',
        component: require('./components/UserManagement/AddNewUser').default,
        meta: { layout: 'default' },

    },
    {
        path: '/clusters',
        component: require('./components/ClustersDashboard/ClusterList').default,
        name: 'cluster-list',
        meta: { layout: 'default' },
    },

    {
        path: '/locations/add-cluster',
        component: require('./components/ClustersDashboard/AddCluster').default,
        name: 'cluster-new',
        meta: { layout: 'default' },
    },

    {
        path: '/clusters/:id',
        component: require('./components/ClusterDashboard/ClusterDashboard').default,
        name: 'cluster-detail',
        meta: { layout: 'default' },
    },
    //targets
    {
        path: '/targets',
        component: require('./components/Target/TargetList').default,
        name: 'target-list',
        meta: { layout: 'default' },
    },
    {
        path: '/targets/new',
        component: require('./components/Target/NewTarget').default,
        name: 'new-target',
        meta: { layout: 'default' },
    },

    // connection-types
    {
        path: '/connection-types',
        component: require('./components/ConnectionTypes/ConnectionTypesList').default,
        name: 'connection-types',
        meta: { layout: 'default' },
    },
    // connection-types
    {
        path: '/connection-types/:id',
        component: require('./components/ConnectionTypes/ConnectionTypeDetail').default,
        name: 'connection-type-detail',
        meta: { layout: 'default' }
    },
    {
        path: '/connection-types/new',
        component: require('./components/ConnectionTypes/NewConnectionType').default,
        name: 'new-connection-types',
        meta: { layout: 'default' },
    },
    // connection-types
    {
        path: '/connection-groups',
        component: require('./components/ConnectionGroups/ConnectionGroupsList').default,
        name: 'connection-groups',
        meta: { layout: 'default' },
    },
    {
        path: '/connection-types/new',
        component: require('./components/ConnectionGroups/NewConnectionGroup').default,
        name: 'new-connection-group',
        meta: { layout: 'default' },
    },
    {
        path: '/sms/list',
        component: require('./components/Sms/List').default,
        name: 'sms-list',
        meta: { layout: 'default' },
    },
    {
        path: '/sms/list/page/:page_number',
        component: require('./components/Sms/List').default,
        name: 'sms-list',
        meta: { layout: 'default' },
    },
    {
        path: '/sms/newsms',
        component: require('./components/Sms/NewSms').default,
        name: 'new-sms',
        meta: { layout: 'default' },
    },
    {
        path: '/maintenance',
        component: require('./components/Maintenance/Maintenance').default,
        name: 'maintenance',
        meta: { layout: 'default' },
    },
    {
        path: '/locations/add-village',
        component: require('./components/Village/AddVillage').default,
        name: 'add-village',
        meta: { layout: 'default' },
    },
    {
        path: '/locations/add-village/:id',
        component: require('./components/Village/AddVillage').default,
        name: 'add-village',
        meta: { layout: 'default' },
    },
    {
        path: '/locations/add-mini-grid',
        component: require('./components/MiniGrid/AddMiniGrid').default,
        name: 'add-mini-grid',
        meta: { layout: 'default' }

    },
    {
        path: '/assets/types',
        component: require('./components/Assets/AssetTypeList').default,
        name: 'asset-types',
        meta: { layout: 'default' },
    },
    {
        path: '/assets/types/page/:page_number',
        component: require('./components/Assets/AssetTypeList').default,
        meta: { layout: 'default' },
    },

    {
        path: '/profile',
        component: require('./components/Profile/User').default,
        meta: { layout: 'default' },
    },
    {
        path: '/profile/management',
        component: require('./components/Profile/UserManagement').default,
        meta: { layout: 'default' },
    },
    {
        path: '/agents/page/:page_number',
        component: require('./components/Agent/AgentList').default,
        meta: { layout: 'default' },
    },
    {
        path: '/agents/:id',
        component: require('./components/Agent/Agent').default,
        meta: { layout: 'default' },
    },
    {
        path: '/commissions',
        component: require('./components/Agent/Commission/AgentCommissionList').default,
        meta: { layout: 'default' },
    },

]

export default new VueRouter({
    routes,
    linkActiveClass: 'active',
    linkExactActiveClass: 'exact-active',
})

