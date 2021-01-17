const resource = 'api/settings'
import Client from './Client/AxiosClient'

export default {
    list(){
        return Client.get(`${resource}/map`)
    },
    update(mapSettings){
        return Client.put(`${resource}/1/map`, mapSettings)
    }
}
