import RepositoryFactory from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class ClusterService {
    constructor () {
        this.repository = RepositoryFactory.get('cluster')
        this.clusters = []

    }

    async createCluster (geoType, location, name, managerId) {
        try {
            let cluster_PM = {
                geo_type: geoType,
                geo_data: location,
                name: name,
                manager_id: managerId
            }

            let response = await this.repository.create(cluster_PM)

            if (response.status === 201 || response.status === 200) {
                return response
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {

            let erorMessage = e.response.data.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }

    async getClusters () {
        try {

            let response = await this.repository.list()
            if (response.status === 201 || response.status === 200) {
                this.clusters = response.data.data
                return this.clusters
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let erorMessage = e.response.data.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }

    async getClusterGeoLocation (clusterId) {
        try {

            let response = await this.repository.getGeoLocation(clusterId)
            if (response.status === 201 || response.status === 200) {
                return response.data.data.geo
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let erorMessage = e.response.data.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }

    async getDetails (clusterId) {
        try {

            let response = await this.repository.get(clusterId)
            if (response.status === 201 || response.status === 200) {
                return response.data.data
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let erorMessage = e.response.data.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }

    async getClusterRevenues (clusterId, period, startDate, endDate) {
        try {
            let revenuePM = {
                'period': period,
                'startDate': startDate,
                'endDate': endDate
            }

            let response = await this.repository.getClusterRevenues(clusterId, revenuePM)
            if (response.status === 201 || response.status === 200) {
                return response.data.data
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let erorMessage = e.response.data.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }

    async getAllRevenues (period, startDate, endDate) {
        try {
            let revenuePM = {
                'period': period,
                'startDate': startDate,
                'endDate': endDate
            }

            let response = await this.repository.getAllRevenues(revenuePM)
            if (response.status === 201 || response.status === 200) {
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
