export let resources = {
    bookKeeping: {
        list: '/tickets/api/export',
        download: '/tickets/api/export/download/', //{id}/book-keeping
    },
    sms: {
        list: '/api/sms/',
        byPhone: '/api/sms/phone',
        search: '/api/sms/search/',
        groups: '/api/connection-groups',
        types: '/api/connection-types',
        send: '/api/sms/storeandsend',
        bulk: '/api/sms/bulk'
    },
    reports: {
        weekly: {
            list: '/api/reports?type=weekly'
        },
        monthly: {
            list: '/api/reports?type=monthly'
        },
        download: '/api/reports/', //{id}/download
    },
    user: {
        authData: '/user-data',
    },
    address: {
        list: '/api/addresses/',
        create: '/api/addresses/',
        update: '/api/addresses',
        delete: '/api/addresses',
    },
    city: {
        'list': '/api/cities',
        'create': '/api/cities',
    },

    admin: {
        list: '/api/admin/users',
        login: '/api/auth/login',
        refresh: '/api/auth/refresh',
    },
    maintenance: {
        list: '/api/maintenance',
        person: '/api/maintenance/user'
    },
    person: {
        'create': '/api/people',
        'update': '/api/people/',
        'detail': '/api/people/',
        'delete': '/api/people/',
        'search': '/api/people/search',
        'list': '/api/people',
        'addresses': '/api/people/',
        'maintenance_list': '/api/people/?is_customer=0'

    },
    tariff: {
        'list': '/api/tariffs',
        'create': '/api/tariffs',
    },
    ticket: {
        'list': '/tickets',
        'detail': '/tickets/',
        'close': '/tickets/api/ticket',
        'create': '/tickets/api/ticket',
        'createMaintenance': '/tickets/api/ticket',
        'getUser': '/tickets/api/tickets/user/',
        'users': '/tickets/api/users/',
        'createUserTicket': '/tickets/api/tickets/users',
        'labels': '/tickets/api/labels',
        'comments': '/tickets/api/tickets/comments',
    },

    transactions: {
        'list': {
            'all': '/api/transactions',
            'confirmed': '/api/transactions/confirmed',
            'cancelled': '/api/transactions/cancelled',
        },
        'analytics': '/api/transactions/analytics/',
        'detail': '/api/transactions/',
        'search': '/api/transactions/search',
        'searchAdvanced': '/api/transactions/advanced',
    },
    paymenthistories: '/api/paymenthistories/',
    debt: '/api/paymenthistories/debt/',
    assets: {
        list: '/api/assets/types',
        type: {
            list: '/api/assets/types',
            store: '/api/assets/types',
            update: '/api/assets/types',
            delete: '/api/assets/types',
            sell: '/api/assets/types/',
            person: '/api/assets/types/people/',
        },
        rate: {
            'update': '/api/assets/rates/'
        }

    },
    meters: {
        'list': '/api/meters',
        'search': '/api/meters/search',
        'getMeters': '/api/meters/',
        'delete': '/api/meters/',
        'revenue': '/api/meters/',
        'transactions': 'api/meters/',
        'consumptions': 'api/meters/',
        'geo': '/api/meters/geoList'
    },
    meterparameters: {
        'update': '/api/meters/',
    },
    manufacturer: {
        'detail':
            '/api/manufacturers/',
        'list':
            '/api/manufacturers/',
    },
    revenues: {
        'analysis': '/api/revenue/analysis',
        'trends': '/api/revenue/trends',
        'batch': '/api/revenue',
        'tickets': '/api/revenue/tickets'
    },
    target: {
        'list': '/api/targets',
        'store': '/api/targets',
        'available_slots': '/api/targets/slots',
    },
    connections: {
        'list': '/api/connection-groups',
        'number_of_customers': '/api/meters/parameters/connection-types',
        'store': '/api/connection-types',
    },
    clusters: {
        'list': '/api/clusterlist',
        'geo': '/api/clusters/geo/',
        'show_geo': '/api/clusters/',
        'revenue': {
            'overview': '/api/clusters/revenue',
            'trends': '/api/clusters/', //{id}/revenue/analysis
        },

        'save': '/api/clusters',
        'detail': '/api/clusters/',
    },

    miniGrids: {
        list: '/api/mini-grids',
    },
    batteries: {
        detail: '/api/mini-grids/',
    },
    solar: {
        detail: '/api/mini-grids/',
    },
    pv: {
        list: '/api/pv/'
    }
}
