import {resources} from "../resources";

const resource = {

    'list': '/api/sms/',
    'byPhone': '/api/sms/phone',
    'search': '/api/sms/search/',
    'groups': '/api/connection-groups',
    'types': '/api/connection-types',
    'send': '/api/sms/storeandsend',
    'bulk': '/api/sms/bulk'

};

export default {

    list(param) {
        switch (param) {
            case 'list':
                return axios.get(`${resource.list}`);
            case 'groups':
                return axios.get(`${resource.groups}`);
            case 'types':
                return axios.get(`${resource.types}`);
        }

    },
    send(smsSend_PM,type) {
        switch (type) {
            case 'bulk':
                return axios.post(`${resource.bulk}`, smsSend_PM);
            case 'single':
                return axios.post(`${resource.send}`, smsSend_PM);
        }

    },
    detail(phone) {
        return axios.get(`${resource.byPhone}` + '/' + phone)
    },

    search(term){
        return  axios.get(`${resource.search}` + term);
    }

}
