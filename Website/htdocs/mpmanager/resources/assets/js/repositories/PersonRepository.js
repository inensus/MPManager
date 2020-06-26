const resource = '/api/people'

export default {
    get (page = 1) {
        return axios.get(`${resource}/${page}`)

    },
    update(person){
        return axios.put(`${resource}/${person.id}`,person)
    }
}
