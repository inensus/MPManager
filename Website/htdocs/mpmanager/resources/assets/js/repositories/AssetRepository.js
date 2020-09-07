const resource = '/api/assets/types';
import Client from './Client/AxiosClient'

export default {
    list(){
        return Client.get(`${resource}`)
    },
    create (asset) {
        return Client.post(`${resource}`, asset)
    },

    update (id, asset) {
        return Client.put(`${resource}/${id}`, asset)
    },

    delete (id) {
        return Client.delete(`${resource}/${id}`)
    }
}
