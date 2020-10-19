const resource ={
    list: '/api/admin/users',
    login: '/api/auth/login',
    refresh: '/api/auth/refresh',
}

export default {

    list(paginate){
        return axios.get(`${resource.list}?paginate=${paginate}`)
    }
}
