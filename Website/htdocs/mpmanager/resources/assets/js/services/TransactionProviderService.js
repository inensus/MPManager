import Repository from '../repositories/RepositoryFactory'

import { ErrorHandler } from '../Helpers/ErrorHander'

export class TransactionProviderService {
    constructor () {
        this.repository = Repository.get('transactionProviders')
        this.list = []
        this.transactionProvider = {
            name: null,
            value: null
        }
    }

    fromJson (providerData) {
        return {
            name: providerData.split('original')[1],
            value: providerData
        }

    }

    updateList (transactionProviders) {
        this.list = []
        this.list.push({
            name: 'All Network Providers',
            value: '-1'
        })
        this.list = transactionProviders.map(tp => {
            return this.fromJson(tp)
        })
        return this.list
    }

    async getTransactionProviders () {
        try {
            let response = await this.repository.list()
            if (response.status === 200) {
                this.list = this.updateList(response.data.data)
                this.transactionProvider = this.list.filter(x=>x.value==='-1')[0]
                return this.list
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }
}
