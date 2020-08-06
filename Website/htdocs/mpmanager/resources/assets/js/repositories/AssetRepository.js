const resource = '/api/assets/types'

export default {

    list () {
        return axios.get(`${resource}`)
    },

    create (asset) {
        return axios.post(`${resource}`, asset)
    },

    update (id, asset) {
        return axios.put(`${resource}/${id}`, asset)
    },

    delete (id) {
        return axios.delete(`${resource}/${id}`)
    },

}
