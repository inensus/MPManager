import Client from './Client/AxiosClient'
const resource = '/api/elastic-usage-times'
export default {

    delete(id){
        return Client.delete(`${resource}/${id}`)
    }
}
