import RepositoryFactory from '../repositories/RepositoryFactory'
import {ErrorHandler} from '../Helpers/ErrorHander'
import {Paginator} from '../classes/paginator'
import {resources} from '../resources'

export class ConnectionService {
    constructor(_type) {
        this.type = _type
        this.repository = RepositoryFactory.get('connections')
        this.connections = []
        this.target = {
            newConnection: 0,
            totalRevenue: 0,
            connectedPower: 0,
            energyPerMonth: 0,
            averageRevenuePerMonth: 0
        }
        this.connection = {
            id: null,
            name: null,
            target: this.target
        }
        if (_type === 'types') {
            this.paginator = new Paginator(resources.connections.store)
        } else {
            this.paginator = new Paginator(resources.connections.list)
        }

    }

    updateList(data) {
        this.connections = []

        for (let a in data) {

            let connection = {
                id: data[a].id,
                name: data[a].name,
                updated_at: data[a].updated_at,
                edit: false,
            }
            this.connections.push(connection)
            return this.connections
        }

    }

    async getConnectionTypes() {
        try {
            let response = await this.repository.list(this.type)

            if (response.status === 200) {
                this.connections = response.data.data
                return this.connections
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let erorMessage = e.response.data.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }

    async createConnectionType(name) {
        try {
            let connectionType_PM = {
                name: name
            }
            let response = await this.repository.create(this.type, connectionType_PM)
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
