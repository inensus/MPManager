const resource = '/api/tariffs';

export default {

    list() {
        return axios.get(`${resource}`)
    },
    create(tariff) {
        return axios.post(`${resource}`, tariff)
    }

}
