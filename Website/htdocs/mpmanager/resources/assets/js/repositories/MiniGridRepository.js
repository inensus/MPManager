const resource = '/api/mini-grids'

export default {
    list () {
        return axios.get(`${resource}`)
    },
    create (miniGrid_PM) {

        return axios.post(`${resource}`, miniGrid_PM)
    },
    get (miniGrid_Id) {
        return axios.get(`${resource}/${miniGrid_Id}`)
    },
    geoData (miniGrid_Id) {
        return axios.get(`${resource}/${miniGrid_Id}?relation=1`)
    },
    watch(Id,miniGrid_PM) {

        return axios.put(`${resource}` + '/' + Id,miniGrid_PM)
    },
    listDataStream(data_stream) {
        return axios.get(`${resource}`+'?data_stream='+data_stream)
    },
}
