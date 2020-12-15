import Repository from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'
import { Paginator } from '../classes/paginator'
import { resources } from '../resources'
import { EventBus } from '../shared/eventbus'

export class SmsService {
    constructor () {
        this.repository = Repository.get('sms')
        this.sms = {
            id: null,
            number: null,
            date: null,
            message: null,
            total: 0,
            owner: null
        }
        this.numberList = []
        this.list = []
        this.resultList = []
        this.receiverList = []
        this.paginator = new Paginator(resources.sms.list)
    }

    search (term) {
        this.paginator = new Paginator(resources.sms.search)
        EventBus.$emit('loadPage', this.paginator, { 'term': term })
    }

    showAll () {
        this.paginator = new Paginator(resources.sms.list)
        EventBus.$emit('loadPage', this.paginator)
    }

    updateList (smsList) {
        this.numberList = []
        for (let index in smsList) {
            let sms = {
                id: smsList[index].id,
                number: smsList[index].receiver,
                date: smsList[index].created_at,
                message: smsList[index].body,
                owner: '',
                total: 0
            }
            if (smsList[index].address !== null) {
                sms.owner = smsList[index].address.owner
            }
            if ('total' in smsList[index]) {
                sms.total = smsList[index].total
            }
            this.numberList.push(sms)
        }
        return this.numberList
    }

    searchSms (text) {

        if (text.length === 0) {
            return this.numberList
        }
        return this.numberList.filter((n) => {
            return n.number.includes(text) ||
                (n.owner !== null && n.owner !== undefined && n.owner.name !== undefined && n.owner.surname !== undefined) ||
                (n.owner.name.toLowerCase().includes(text.toLowerCase()) || n.owner.surname.toLowerCase().includes(text.toLowerCase()))
        })

    }

    addReceiver (receiver, stored = true) {
        let found = false
        for (let index in this.receiverList) {
            if (this.receiverList[index].stored !== stored) {
                continue
            }
            if (stored && this.receiverList[index].receiver.id === receiver.id) {
                found = true
                break
            }
            if (!stored && this.receiverList[index].receiver === receiver) {
                found = true
                break
            }
            if (!found) {
                this.receiverList.push({
                    receiver: receiver,
                    stored: stored
                })
            }

            return this.receiverList
        }
    }

    removeReceiver (receiver) {
        for (let index in this.receiverList) {
            if (receiver.stored !== this.receiverList[index].stored) {
                continue
            }
            if (receiver.stored && receiver.receiver.id === this.receiverList[index].receiver.id) {
                this.receiverList.splice(index, 1)
                break
            } else if (!receiver.stored && receiver === this.receiverList[index]) {
                this.receiverList.splice(index, 1)
                break
            }
        }
        return this.receiverList
    }

    async getList (personId) {
        try {
            let response = await this.repository.list('list',personId)
            if (response.status === 200) {
                if(personId !== null){
                    return response.data.data
                }
                return this.updateList(response.data.data)
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            return new ErrorHandler(e, 'http')
        }

    }

    async getDetail (phone) {
        try {
            let response = await this.repository.detail(phone)
            if (response.status === 200) {
                this.list = response.data.data
                return this.list
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            return new ErrorHandler(e, 'http')
        }
    }

    async sendMaintenanceSms (maintenanceData) {
        try {
            let sendSmsPM = {
                'person_id': maintenanceData.assigned,
                'message': maintenanceData.description + '/n Amount : '
                    + maintenanceData.amount + '\n Due Date '
                    + maintenanceData.dueDate,
                'senderId': maintenanceData.id,
            }
            let response = await this.repository.send(sendSmsPM, 'single')
            if (response.status === 200 || response.status === 201) {
                return response
            } else {

                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    async sendToNumber (type, message, phone, senderId) {
        let sendSmsPM = {
            'type': type,
            'message': message,
            'phone': phone,
            'senderId': senderId,
        }
        try {
            let response = await this.repository.send(sendSmsPM, 'single')
            if (response.status === 200 || response.status === 201) {
                return response
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            return new ErrorHandler(e, 'http')
        }
    }

    async sendToPerson (message, personId, senderId) {
        let sendSmsPM = {
            'message': message,
            'person_id': personId,
            'senderId': senderId,
        }
        try {
            let response = await this.repository.send(sendSmsPM, 'single')
            if (response.status === 200 || response.status === 201) {
                return response
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            return new ErrorHandler(e, 'http')
        }
    }

    async sendBulk (type, miniGrid, receivers, message, senderId) {
        let sendSmsPM = {
            'type': type,
            'miniGrid': miniGrid,
            'receivers': receivers,
            'message': message,
            'senderId': senderId,
        }
        try {
            let response = await this.repository.send(sendSmsPM, 'bulk')
            if (response.status === 200 || response.status === 201) {
                return response
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            return new ErrorHandler(e, 'http')
        }
    }

    async getGroups () {
        try {
            let response = await this.repository.list('groups')
            if (response.status === 200) {

                this.resultList = response.data.data
                return this.resultList
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            return new ErrorHandler(e, 'http')
        }
    }

    async getTypes () {
        try {
            let response = await this.repository.list('types')
            if (response.status === 200) {

                this.resultList = response.data.data
                return this.resultList
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            return new ErrorHandler(e, 'http')
        }
    }

    async searchPerson (term) {
        try {
            let response = await this.repository.search(term)
            if (response.status === 200) {

                this.resultList = response.data.data
                return this.resultList
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            return new ErrorHandler(e, 'http')
        }
    }

}
