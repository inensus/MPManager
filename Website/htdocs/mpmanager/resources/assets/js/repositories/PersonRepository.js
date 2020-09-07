import Client from './Client/AxiosClient'

const resource = '/api/people'

export default {
    get (page = 1) {
        return axios.get(`${resource}/${page}`)

    },
    update(person){
        return axios.put(`${resource}/${person.id}`,person)
    },
    create(agentPm){
        return Client.post(`${resource}`,agentPm)
    },
}
