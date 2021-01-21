const resource = '/api/settings/countryList'
import Client from './Client/AxiosClient'

export default {
    list(){
        return Client.get(`${resource}`)
    }
}
