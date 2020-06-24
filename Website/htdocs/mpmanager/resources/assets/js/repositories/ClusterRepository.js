const resource = 'api/clusters'
export default {

    create(cluster){
        return axios.post(`${resource}`,cluster)
    },
    list(){
        return axios.get(`${resource}/geo`)
    },
    getGeoLocation(clusterId){
        return axios.get(`${resource}/${clusterId}/geo`)
    },
    get(clusterId){
        return axios.get(`${resource}/${clusterId}`)
    }
}
