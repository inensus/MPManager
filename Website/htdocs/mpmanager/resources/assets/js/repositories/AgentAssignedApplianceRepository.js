import Client from './Client/AxiosClient'

const resource = 'api/agents/assigned'
export default {

    list (agent) {
        return Client.get(`${resource}/${agent.id}`)
    },
    create(assignAppliancePm){
        return Client.post(`${resource}`,assignAppliancePm)
    }
}
