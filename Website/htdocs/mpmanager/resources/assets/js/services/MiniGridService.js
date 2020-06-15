import Repository from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class MiniGridService {
    constructor () {
        this.repository = Repository.get('minigrid')
        this.miniGrids = []
        this.miniGrid={}
    }

    async getMiniGrids () {
        try {

            let response = await this.repository.list()

            if (response.status === 200 || response.status === 201) {
                this.miniGrids = response.data.data

                return this.miniGrids
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')

        }
    }

    async createMiniGrid (name, cluster_Id, geo_data) {
        try {
            let miniGrid_PM = {
                cluster_id: cluster_Id,
                geo_data: geo_data,
                name: name,

            }

            let response = await this.repository.create(miniGrid_PM)

            if (response.status === 201 || response.status === 200) {
                return response
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {

            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    async getMiniGrid (miniGrid_Id) {
        try {

            let response = await this.repository.get(miniGrid_Id)

            if (response.status === 200 || response.status === 201) {
                this.miniGrid = response.data.data

                return this.miniGrid
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }
    async getMiniGridGeoData (miniGrid_Id) {
        try {

            let response = await this.repository.geoData(miniGrid_Id)

            if (response.status === 200 || response.status === 201) {
                this.miniGrid = response.data.data

                return this.miniGrid
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }
}
