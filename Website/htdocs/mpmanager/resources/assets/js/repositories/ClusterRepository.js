const resource = 'api/clusters'
import Client from './Client/AxiosClient'
export default {

    create(cluster){
        return Client.post(`${resource}`,cluster)
    },
    list () {
        return Client.get(`${resource}/geo`)
    },
    getGeoLocation (clusterId) {
        return Client.get(`${resource}/${clusterId}/geo`)
    },
    get (clusterId) {
        return Client.get(`${resource}/${clusterId}`)
    },
    getClusterRevenues (clusterId) {
        return Client.get(`${resource}/${clusterId}/revenue`)
    },
    getClusterCitiesRevenue (clusterId, terms) {
        return Client.get(`${resource}/${clusterId}/cities-revenue/${terms}`)
    },
    getAllRevenues (terms) {
        return Client.get(`${resource}/revenue/${terms}`)
    },

}
