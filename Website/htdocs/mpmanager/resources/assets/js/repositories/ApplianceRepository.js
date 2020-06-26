const resource = '/api/appliances/types';

export default {


    create (appliance) {
        return axios.post(`${resource}`, appliance)
    },

    update (id, appliance) {
        return axios.put(`${resource}/${id}`, appliance)
    },

    delete (id) {
        return axios.delete(`${resource}/${id}`)
    },

}
