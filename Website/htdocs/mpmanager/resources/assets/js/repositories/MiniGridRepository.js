const resource = '/api/mini-grids';


export default {
    list() {
        return axios.get(`${resource}`);
    }
}
