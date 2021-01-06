import Repository from '../repositories/RepositoryFactory'
import { Paginator } from '../classes/paginator'
import { ErrorHandler } from '../Helpers/ErrorHander'
import { resources } from '../resources'
import { EventBus } from '../shared/eventbus'

export class TransactionService {
    constructor () {
        this.repository = Repository.get('transaction')
        this.list = []
        this.transaction = {
            id: null,
            service: null,
            sender: null,
            amount: null,
            type: null,
            message: null,
            sentDate: null,
            lastUpdate: null,
            status: null,
        }
        this.paginator = new Paginator(resources.transactions.list.all)
        this.analyticsData = null
        this.transactionJson = null
    }

    async getTransactions () {
        try {
            let response = await this.repository.list()
            if (response.status === 200) {
                this.list = this.updateList(response.data.data)
                return this.list
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    async getAnalytics (period) {
        try {
            this.analyticsData = null
            let response = await this.repository.analytics(period)
            if (response.status === 200) {
                this.analyticsData = response.data
                return this.analyticsData
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    async getFilteredTransactions (term) {
        try {
            let response = await this.repository.filteredList(term)
            if (response.status === 200) {

                this.list = this.updateList(response.data.data)
                return this.list
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    async getTransaction (id) {
        try {

            let response = await this.repository.get(id)

            if (response.status === 200 || response.status === 201) {
                this.transactionJson = response.data.data

                return this.transactionJson
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    searchAdvanced (data) {
        this.paginator.url = resources.transactions.searchAdvanced
        this.paginator.method = 'POST'
        this.paginator.setPostData({ terms: data })
        EventBus.$emit('loadPage', this.paginator, { 'term': data })
    }

    updateList (transactionList) {
        this.list = []
        for (let transaction in transactionList) {
            this.list.push(this.fromJson(transactionList[transaction]))
        }

        return this.list
    }

    fromJson (transactionData) {
        return {
            id: transactionData.id,
            service: transactionData.original_transaction_type,
            sender: transactionData.sender,
            amount: transactionData.amount,
            type: transactionData.type,
            message: transactionData.message,
            sentDate: transactionData.created_at,
            lastUpdate: transactionData.updated_at,
            status: this.getOriginalData(transactionData).status,
        }

    }

    getOriginalData (transactionData) {
        if (transactionData.original_transaction !== undefined) {
            return transactionData.original_transaction
        } else if (transactionData.original_transaction_type === 'airtel_transaction') {
            return transactionData.original_airtel
        } else if (transactionData.original_transaction_type === 'vodacom_transaction') {
            return transactionData.original_vodacom
        } else if (transactionData.original_transaction_type === 'third_party_transaction') {
            return transactionData.third_party
        }
    }
}
