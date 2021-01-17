const resource = '/api/settings/languagesList'
import Client from './Client/AxiosClient'

export default {
    list(){
        return Client.get(`${resource}`)
    }

}
