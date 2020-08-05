import Client from './Client/AxiosClient'
const resource = '/api/agents/commissions'

export default {
    list(){
        return Client.get(`${resource}`)
    }
}
