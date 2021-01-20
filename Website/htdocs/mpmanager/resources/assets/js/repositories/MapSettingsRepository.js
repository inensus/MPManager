const resource = 'api/settings'
import Client from './Client/AxiosClient'

export default {
    list(){
        return Client.get(`${resource}/map`)
    },
    update(id, mapSettings){
        return Client.put(`${resource}/${id}/map`, mapSettings)
    }
}
