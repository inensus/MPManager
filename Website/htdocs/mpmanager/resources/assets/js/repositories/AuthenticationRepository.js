const resource = '/api/auth'

export default {
    login (user) {
        return axios.post(`${resource}/login`, user)
    },
    refresh (token) {
        return axios.post(`${resource}/refresh`, null, { headers: { Authorization: 'Bearer' + token } })
    }
}
