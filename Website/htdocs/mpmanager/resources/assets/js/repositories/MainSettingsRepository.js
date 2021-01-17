const resource = 'api/settings'
import Client from './Client/AxiosClient'

export default {
    list(){
        return Client.get(`${resource}/main`)
    },
    update(mainSettings){
        return Client.put(`${resource}/1/main`, mainSettings)
    }
}
