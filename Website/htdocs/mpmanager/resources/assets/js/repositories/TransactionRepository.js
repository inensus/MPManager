const resource = '/api/transactions'

export default {

    list () {
        return axios.get(`${resource}`)

    },
    analytics (period) {
        return axios.get(`${resource}/analytics/${period}`)
    },
    filteredList (term) {
        return axios.post(`${resource}/advanced`, term)
    },
    get (id) {
        return axios.get(`${resource}/${id}`)
    },

}
