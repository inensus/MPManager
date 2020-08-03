import Repository from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class AgentCommissionService {
    constructor () {
        this.repository = Repository.get('commission')
        this.list = []
        this.agentCommission = {
            id: null,
            name: null,
            energyCommission: null,
            applianceCommission: null

        }
    }

    fromJson (data) {
        this.agentCommission.id = data.id
        this.agentCommission.name = data.name
        this.agentCommission.energyCommission = data.energy_commission
        this.agentCommission.applianceCommission = data.appliance_commission
        return this.agentCommission

    }

    updateList (data) {
        this.list = []
        for (let a in data) {
            let agentCommission = this.fromJson(data[a])
            this.list.push(agentCommission)
        }
        return this.list
    }

    async getAgentCommissions () {
        try {
            let response = await this.repository.list()
            if (response.status === 200) {
                let list = response.data.data
                this.list = this.updateList(list)
                return this.list
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data
            return new ErrorHandler(errorMessage, 'http')
        }
    }
}
