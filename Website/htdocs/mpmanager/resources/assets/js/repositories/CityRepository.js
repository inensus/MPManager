const resource = '/api/cities'

export default {


    list() {
        return axios.get(`${resource}`)
    },
    create(city){
        return axios.post(`${resource}`, city)
    }
}
