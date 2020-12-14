const resource ={
    list: '/api/admin/users',
    login: '/api/auth/login',
    refresh: '/api/auth/refresh',
}
import Client from './Client/AxiosClient'

export default {

    list(paginate){
        return Client.get(`${resource.list}?paginate=${paginate}`)
    }
}
