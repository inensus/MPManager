const resource ={
        list: '/api/clusterlist',
        geo: '/api/clusters/geo/',
        show_geo: '/api/clusters/',
        revenue: {
             overview: '/api/clusters/revenue',
             trends: '/api/clusters/', //{id}/revenue/analysis
         },
        save: '/api/clusters',
        detail: '/api/clusters/',
}

export default {

    create(cluster){
        return axios.post(`${resource.save}`,cluster)
    },
    list(){
        return axios.get(`${resource.geo}`)
    },
    getGeoLocation(clusterId){
        return axios.get(`${resource.show_geo}${clusterId}/geo`)
    },
    get(clusterId){
        return axios.get(`${resource.detail}${clusterId}`)
    }
}
