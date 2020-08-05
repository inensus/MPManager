const resource = '/tickets/api/labels'
import Client from './Client/AxiosClient'

export default {
    list () {
        return Client.get(`${resource}` + '/?outsource=1')
    },

    create (labelPM) {
        return Client.post(`${resource}`, labelPM)
    },

}
