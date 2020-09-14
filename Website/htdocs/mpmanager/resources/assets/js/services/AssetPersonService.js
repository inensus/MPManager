import Repository from '../repositories/RepositoryFactory'
import {ErrorHandler} from '../Helpers/ErrorHander'

export class AssetPersonService {
    constructor() {
        this.repository = Repository.get('assetPerson')
        this.list = []


    }
    async getPersonAssets(id){
        try {
            let response = await this.repository.list(id)
            if (response.status === 200 || response.status === 201) {
                this.list=response.data.data
                return this.list
            } else {
                new ErrorHandler(response.error, 'http', response.status)
            }

        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    async saveAsset(id,personId,assetPM){
        try {
            let response = await this.repository.create(id,personId,assetPM)
            if (response.status === 200 || response.status === 201) {

                return response
            } else {
                new ErrorHandler(response.error, 'http', response.status)
            }

        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

}
