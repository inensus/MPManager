const resource = '/api/settings'
import Client from './Client/AxiosClient'

export default {
    list(){
        return Client.get(`${resource}/ticket`)
    },
    update(id, ticketSettings){
        return Client.put(`${resource}/${id}/ticket`, ticketSettings)
    }
}
