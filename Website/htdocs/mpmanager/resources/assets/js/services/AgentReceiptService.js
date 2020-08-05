import { Paginator } from '../classes/paginator'
import { ErrorHandler } from '../Helpers/ErrorHander'
import Repository from '../repositories/RepositoryFactory'
export class AgentReceiptService {
    constructor (agentId) {
        this.repository= Repository.get('agentReceipt')
        this.list = []
        this.receipt = {
            id: null,
            amount: null,
            receiver: null,
            createdAt: null
        }
        this.newReceipt={
            agentId:null,
            amount:null
        }
        this.paginator = new Paginator(resources.agents.receipts + '/' + agentId)
    }

    fromJson (data) {
        let receipt = {
            id: data.id,
            amount: data.amount,
            receiver: data.user.name,
            createdAt: data.created_at.toString().replace(/T/, ' ').replace(/\..+/, '')
        }
        return receipt
    }

    updateList (data) {
        this.list = []
        for (let a in data) {
            let receipt = this.fromJson(data[a])
            this.list.push(receipt)
        }
        return this.list
    }


    async addNewReceipt () {
        try {

            let response = await this.repository.create(this.newReceipt)
            if (response.status === 200 || response.status === 201) {
                this.resetNewBalance()
                return response
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data
            return new ErrorHandler(errorMessage, 'http')
        }

    }
}
