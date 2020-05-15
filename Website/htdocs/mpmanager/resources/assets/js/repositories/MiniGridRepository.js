const resource = '/api/mini-grids'


export default {
    list() {
        return axios.get(`${resource}`)
    },
    get(Id) {

        return axios.get(`${resource}` + '/' + Id + '/?relation=1')
    },
    watch(Id,miniGrid_PM) {

        return axios.put(`${resource}` + '/' + Id,miniGrid_PM)
    },
    listDataStream(data_stream) {
        return axios.get(`${resource}`+'?data_stream='+data_stream)
    },


}
