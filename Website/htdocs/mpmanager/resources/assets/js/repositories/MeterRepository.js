
const resource = '/api/meters'


export default {

    geoList(miniGridId){
        return axios.get(`${resource}/geoList?mini_grid_id=${miniGridId}`)
    },
    get(meterId){
        return axios.get(`${resource}/${meterId}/all`)
    },
    update(meterId,points){
        return axios.put(`${resource}/${meterId}`,points)
    }
}
