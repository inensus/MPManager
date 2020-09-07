import RepositoryFactory from '../repositories/RepositoryFactory'
import {ErrorHandler} from '../Helpers/ErrorHander'
import {Paginator} from '../classes/paginator'
import {resources} from '../resources'

export class ConnectionTypeService {
    constructor() {
        this.repository = RepositoryFactory.get('connectionTypes')
        this.connectionTypes = []
        this.target = {
            newConnection: 0,
            totalRevenue: 0,
            connectedPower: 0,
            energyPerMonth: 0,
            averageRevenuePerMonth: 0
        }
        this.connectionType = {
            id: null,
            name: null,
            target: this.target
        }
        this.paginator = new Paginator(resources.connections.store)
    }

    updateList(data) {
        this.connectionTypes = []

        for (let a in data) {

            let connectionType = {
                id: data[a].id,
                name: data[a].name,
                updated_at: data[a].updated_at,
                edit: false,
            }
            this.connectionTypes.push(connectionType)
            return this.connectionTypes
        }

    }
    async updateConnectionType(connectionType){
        try {
            let response = await this.repository.update(connectionType)
            if(response.status === 200 || response.status === 201){

            }else{
                return new ErrorHandler(response.error, 'http', response.status)
            }
        }catch (e) {
            let erorMessage = e.response.data.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }

    async getConnectionTypes() {
        try {
            let response = await this.repository.list()

            if (response.status === 200) {
                this.connectionTypes = response.data.data
                return this.connectionTypes
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let erorMessage = e.response.data.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }

    async getConnectionTypeDetail(connectionTypeId){
        try {
            let response = await this.repository.show(connectionTypeId)

            if (response.status === 200) {
                this.connectionTypes = response.data.data[0]
                return this.connectionTypes
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
            let response = await this.repository.create(connectionType_PM)
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

