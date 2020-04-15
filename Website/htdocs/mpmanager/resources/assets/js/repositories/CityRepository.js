const resource = '/api/cities';

export default {


    list() {
        return axios.get(`${resource}`)
    }
}
