const resource = '/api/mini-grids'

export default {
    list () {
        return axios.get(`${resource}`)
    },
    create (miniGridPM) {

        return axios.post(`${resource}`, miniGridPM)
    },
    get (miniGridId) {
        return axios.get(`${resource}/${miniGridId}`)
    },
    geoData (miniGridId) {
        return axios.get(`${resource}/${miniGridId}?relation=1`)
    },
    watch(Id,miniGridPM) {

        return axios.put(`${resource}` + '/' + Id,miniGridPM)
    },
    listDataStream(dataStream) {
        return axios.get(`${resource}`+'?data_stream='+dataStream)
    },
    transactions(miniGridId,period){
        return axios.post(`${resource}/${miniGridId}/transactions`,period)
    },
    soldEnergy(miniGridId,period){
        return axios.post(`${resource}/${miniGridId}/energy`,period)
    }
}
