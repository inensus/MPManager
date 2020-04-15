const resource = {
    'list': '/api/maintenance',
    'create': '/api/maintenance/user',
}
export default {

    list () {
        return axios.get(`${resource.list}`)
    },
    create (personalData) {
        return axios.post(`${resource.create}`, personalData)
    }

}
