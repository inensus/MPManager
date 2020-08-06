import Repository from '../repositories/RepositoryFactory'
import { Paginator } from '../classes/paginator'

export class AgentTransactionService{

    constructor (agentId) {
        this.repository = Repository.get('agentTransactions')
        this.list = []
        this.agentId = null
        this.transaction = {
            id: null,
            amount: null,
            meter: null,
            customer:null,
            createdAt: null
        }
        this.paginator = new Paginator(resources.agents.transactions + agentId)
    }

    fromJson (data) {
        let transaction = {
            id: data.id,
            amount: data.amount,
            meter: data.message,
            customer:data.meter.meter_parameter.owner.name +' ' + data.meter.meter_parameter.owner.surname,
            createdAt: data.created_at.toString().replace(/T/, ' ').replace(/\..+/, '')
        }
        return transaction
    }

    updateList (data) {
        this.list = []
        for (let a in data) {
            let transaction = this.fromJson(data[a])
            this.list.push(transaction)
        }
        return this.list
    }
}
