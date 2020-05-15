const resource = 'api/restrictions'
const resourcePurchase = 'http://127.0.0.1:3000/api/mpm/checkPurchaseCode'
export default {
    sendCode(purchase_PM) {
        return axios.post(`${resourcePurchase}`, purchase_PM)
    },
    check(restriction_PM) {
        return axios.post(`${resource}`, restriction_PM)
    }
}
