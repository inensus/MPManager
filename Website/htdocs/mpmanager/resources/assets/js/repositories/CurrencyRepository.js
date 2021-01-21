const resource = '/api/settings/currencyList'
import Client from './Client/AxiosClient'

export default {
    list(){
        return Client.get(`${resource}`)
    }
}
