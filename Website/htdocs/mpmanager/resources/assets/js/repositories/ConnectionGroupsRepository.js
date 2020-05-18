const resource =  '/api/connection-groups';

export default {
    list(){
        return axios.get(`${resource}`)
    },
    create(name){
        return axios.post(`${resource}`,name)
    }
}

