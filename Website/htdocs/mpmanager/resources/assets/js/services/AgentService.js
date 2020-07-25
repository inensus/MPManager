import Repository from '../repositories/RepositoryFactory'
import { Paginator } from '../classes/paginator'
import { EventBus } from '../shared/eventbus'
import { ErrorHandler } from '../Helpers/ErrorHander'
import { resources } from '../resources'

export class AgentService {
    constructor () {
        this.repository = Repository.get('agent')
        this.list = []
        this.agent = {
            id: null,
            personId: null,
            person:null,
            miniGrid: null,
            miniGridId:null,
            password:null,
            deviceId: null,
            name: null,
            email: null,
            balance: null,
        }
        this.paginator = new Paginator(resources.agents.list)
    }

    fromJson (data) {
        this.agent.id = data.id
        this.agent.personId = data.person_id
        this.agent.miniGrid = data.mini_grid.name
        this.agent.deviceId = data.device_id
        this.agent.name = data.name
        this.agent.email = data.email
        this.agent.balance = data.balance
        return this.agent
    }

    updateList (data) {
        this.list = []

        for (let a in data) {

            let agent = {
                id: data[a].id,
                personId: data[a].person_id,
                miniGrid: data[a].mini_grid.name,
                deviceId: data[a].device_id,
                name: data[a].name,
                email: data[a].email,
                balance: data[a].balance,

            }
            this.list.push(agent)
        }

    }

    search (term) {
        this.paginator = new Paginator(resources.agents.search)
        EventBus.$emit('loadPage', this.paginator, { 'term': term })
    }

    showAll () {
        this.paginator = new Paginator(resources.agents.list)
        EventBus.$emit('loadPage', this.paginator)
    }

    async createAgent () {

        try {
            let response = await this.repository.create(this.agent)
            if (response.status === 200 || response.status === 201) {
                this.agent.id = response.data.data.id
                this.agent.name = response.data.data.name

                EventBus.$emit('AgentAdded', this.agent)
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data
            return new ErrorHandler(errorMessage, 'http')
        }

    }

    async updateAgent (agent) {
        try {
            let response = await this.repository.update(agent)
            if (response.status === 200 || response.status === 201) {
                return response
            } else {
                new ErrorHandler(response.error, 'http', response.status)
            }

        } catch (e) {
            let errorMessage = e.response.data.data
            return new ErrorHandler(errorMessage, 'http')
        }

    }

    async deleteAgent (agent) {
        try {
            let response = await this.repository.delete(agent.id)
            if (response.status === 200 || response.status === 201) {
                return response
            } else {
                new ErrorHandler(response.error, 'http', response.status)
            }
            return response
        } catch (e) {
            return new ErrorHandler(e, 'http')
        }

    }
}
