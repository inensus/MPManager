import Repository from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'
import { Paginator } from '../classes/paginator'

export class AgentSoldApplianceService {
    constructor (agentId) {
        this.repository = Repository.get('assignedAppliance')
        this.list = []
        this.soldAppliance = {
            id: null,
            applianceName: null,
            amount: null,
            customerName: null,
            createdAt: null
        }
        this.paginator = new Paginator(resources.agents.sold_appliances + agentId)
    }

    fromJson (data) {
        let soldAppliance = {
            id: data.id,
            applianceName: data.assigned_appliance.appliance_type.name,
            amount: data.assigned_appliance.cost,
            customerName: data.person.name + ' ' + data.person.surname,
            createdAt: data.created_at.toString().replace(/T/, ' ').replace(/\..+/, '')
        }
        return soldAppliance
    }

    updateList (data) {
        this.list = []
        for (let a in data) {
            let soldAppliance = this.fromJson(data[a])
            this.list.push(soldAppliance)
        }
        return this.list
    }
}
