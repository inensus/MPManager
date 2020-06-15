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
    }
}
