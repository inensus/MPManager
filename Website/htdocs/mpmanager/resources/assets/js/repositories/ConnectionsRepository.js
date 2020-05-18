const resource = {
    'types': '/api/connection-types',
    'groups': '/api/connection-groups',
}

export default {
    list(type) {
        switch (type) {
            case 'types':
                return axios.get(`${resource.types}`)
            case 'groups':
                return axios.get(`${resource.groups}`)


        }

    },
    create(type, params) {
        switch (type) {
            case 'types':

                return axios.post(`${resource.types}`, params)
            case 'groups':
                return axios.post(`${resource.groups}`, params)


        }

    }
}
