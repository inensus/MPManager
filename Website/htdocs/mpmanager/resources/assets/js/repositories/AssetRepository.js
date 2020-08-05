const resource = '/api/assets/types';

export default {


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
