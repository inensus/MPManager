const resource = 'api/restrictions'
const resourcePurchase = 'https://stripe.micropowermanager.com/api/mpm/checkPurchaseCode'
export default {
    sendCode (purchase_PM) {
        return axios.post(`${resourcePurchase}`, purchase_PM)
    },
    check (restriction_PM) {
        return axios.post(`${resource}`, restriction_PM)
    }
}
