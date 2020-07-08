const resource = '/api/revenue'

export default {
    trends(miniGridId,period){
        return  axios.post(`${resource}/trends/${miniGridId}`,period)
    },
    tickets(miniGridId){
        return  axios.get(`${resource}/tickets/${miniGridId}`)
    }

}
