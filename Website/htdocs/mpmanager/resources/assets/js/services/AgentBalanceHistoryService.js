import Repository from '../repositories/RepositoryFactory'
import { Paginator } from '../classes/paginator'

export class AgentBalanceHistoryService {

    constructor (agentId) {
        this.repository = Repository.get('balanceHistory')
        this.list = []
        this.agentId = null
        this.agentBalanceHistory = {
            id: null,
            type: null,
            amount: false,
            createdAt: null
        }
        this.paginator = new Paginator(resources.agents.balance_histories + agentId)
    }

    fromJson (data) {
        let balanceHistory = {
            id: data.id,
            type: data.trigger_type,
            amount: data.amount,
            createdAt: data.created_at.toString().replace(/T/, ' ').replace(/\..+/, '')
        }
        return balanceHistory
    }

    updateList (data) {
        this.list = []
        for (let a in data) {
            let balanceHistory = this.fromJson(data[a])
            this.list.push(balanceHistory)
        }
        return this.list
    }

}
