const resource =  '/api/connection-types';

export default {
    list(){
        return axios.get(`${resource}`)
    },
    create(name){
        return axios.post(`${resource}`,name)
    }
}

