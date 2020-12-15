import Repository from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class UserTransactionsService {
    constructor () {
        this.repository = Repository.get('userTransactions')
        this.list = []

    }

    async getTransactions (userId, page) {
        try {
            let response = await this.repository.list(userId, page)
            if (response.status === 200) {
                return response
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }
}
