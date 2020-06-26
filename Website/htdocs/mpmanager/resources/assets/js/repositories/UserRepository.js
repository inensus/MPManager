const resource ={
    list: '/api/admin/users',
    login: '/api/auth/login',
    refresh: '/api/auth/refresh',
}

export default {

    list(){
        return axios.get(`${resource.list}`)
    }
}
