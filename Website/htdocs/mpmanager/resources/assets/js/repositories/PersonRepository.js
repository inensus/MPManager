const resource = '/people'

export default {
    get (page = 1) {
        return axios.get(`${resource}/${page}`)
        //fsda

    }
}
