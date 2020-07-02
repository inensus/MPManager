const resource = '/api/tariffs'
import Client from './Client/AxiosClient'

export default {

    list () {
        return Client.get(`${resource}`)

    },
    create (tariff) {
        return Client.post(`${resource}`, tariff)
    }

}
