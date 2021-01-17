const resource = '/api/settings'
import Client from './Client/AxiosClient'

export default {
    list(){
        return Client.get(`${resource}/ticket`)
    },
    update(ticketSettings){
        return Client.put(`${resource}/1/ticket`, ticketSettings)
    }
}
