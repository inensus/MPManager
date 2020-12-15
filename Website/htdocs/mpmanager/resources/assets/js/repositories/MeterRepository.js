import Client from './Client/AxiosClient'
const resource = '/api/meters'


export default {

    geoList(miniGridId){
        return Client.get(`${resource}/geoList?mini_grid_id=${miniGridId}`)
    },
    get(meterId){
        return Client.get(`${resource}/${meterId}/all`)
    },
    update(meterId,points){
        return Client.put(`${resource}/${meterId}`,points)
    }
}
