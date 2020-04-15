
const resource = {
    'list': '/tickets',
    'detail': '/tickets/',
    'close': '/tickets/api/ticket',
    'create': '/tickets/api/ticket',
    'createMaintenance': '/tickets/api/ticket',
    'getUser': '/tickets/api/tickets/user/',
    'users': '/tickets/api/users/',
    'createUserTicket': '/tickets/api/tickets/users',
    'labels': '/tickets/api/labels',
    'comments': '/tickets/api/tickets/comments',
};

export default  {
     listCategory() {

        return  axios.get(`${resource.labels}` + '/?outsource=1')
    },

    create(maintenanceData){
         return  axios.post(`${resource.create}`,maintenanceData);
    }
}
