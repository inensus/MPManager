import RepositoryFactory from '../repositories/RepositoryFactory'
import {ErrorHandler} from '../Helpers/ErrorHander'


export class UserService {
    constructor() {
        this.repository = RepositoryFactory.get('user')
        this.users = []
        this.selectedUser = null
    }

    async getUsers() {
        try {
            let response = await this.repository.list()

            if (response.status === 200) {
                this.users = response.data.data
                return this.users
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            return new ErrorHandler(e, 'http')
        }
    }
}
