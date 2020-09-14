import Repository from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class AssetRateService {
    constructor () {
        this.repository = Repository.get('assetRate')

    }

    async   editAssetRate (id, remaining, adminId) {
        try {
            let terms = {
                remaining: remaining,
                admin_id: adminId
            }

            let response = await this.repository.update(id, terms)

            if (response.status === 200 || response.status === 201) {
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
