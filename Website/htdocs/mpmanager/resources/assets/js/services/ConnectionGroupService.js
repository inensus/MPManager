import RepositoryFactory from '../repositories/RepositoryFactory'
import {ErrorHandler} from '../Helpers/ErrorHander'
import {Paginator} from '../classes/paginator'
import {resources} from '../resources'

export class connectionGroupService {
    constructor() {
        this.repository = RepositoryFactory.get('connectionGroups')
        this.connectionGroups = []
        this.target = {
            newConnection: 0,
            totalRevenue: 0,
            connectedPower: 0,
            energyPerMonth: 0,
            averageRevenuePerMonth: 0
        }
        this.connectionGroup = {
            id: null,
            name: null,
            target: this.target
        }
        this.paginator = new Paginator(resources.connections.list)
    }

    updateList(data) {
        this.connectionGroups = []

        for (let a in data) {

            let connectionGroup = {
                id: data[a].id,
                name: data[a].name,
                updated_at: data[a].updated_at,
                edit: false,
            }
            this.connectionGroups.push(connectionGroup)
            return this.connectionGroups
        }

    }

    async getConnectionGroups() {
        try {
            let response = await this.repository.list()

            if (response.status === 200) {
                this.connectionGroups = response.data.data
                return this.connectionGroups
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let erorMessage = e.response.data.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }

    async createConnectionGoup(name) {
        try {
            let connectionGroup_PM = {
                name: name
            }
            let response = await this.repository.create(connectionGroup_PM)
            if (response.status === 200 || response.status === 201) {
                return response.data.data
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let erorMessage = e.response.data.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }
}
