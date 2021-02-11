import RepositoryFactory from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class ClusterService {
    constructor () {
        this.repository = RepositoryFactory.get('cluster')
        this.clusters = []
    }

    async createCluster (geoType, location, name, managerId) {
        const cluster_PM = {
            geo_type: geoType,
            geo_data: location,
            name: name,
            manager_id: managerId,
        }
        try {
            const response = await this.repository.create(cluster_PM)
            return this.responseValidator(response, [200,201])
        } catch (e) {
            return new ErrorHandler(e.response.data.data.message, 'http')
        }
    }

    async getClusters () {
        try {
            const response = await this.repository.list()
            return this.responseValidator(response)
        } catch (e) {
            return new ErrorHandler(e.response.data.data.message, 'http')
        }
    }

    async getClusterGeoLocation (clusterId) {
        try {
            const response = await this.repository.getGeoLocation(clusterId)
            return this.responseValidator(response)
        } catch (e) {
            return new ErrorHandler(e.response.data.data.message, 'http')
        }
    }

    async getDetails (clusterId) {
        try {
            const response = await this.repository.get(clusterId)
            return this.responseValidator(response)
        } catch (e) {
            return new ErrorHandler(e.response.data.data.message, 'http')
        }
    }

    async getClusterCitiesRevenue (clusterId, period, startDate, endDate) {
        const queryString = `?period=${period}&startDate=${startDate ??
        ''}&endDate=${endDate ?? ''}`
        try {
            const response = await this.repository.getClusterCitiesRevenue(
                clusterId,
                queryString)
            return this.responseValidator(response)
        } catch (e) {
            return new ErrorHandler(e.response.data.data.message, 'http')
        }
    }

    async getClusterRevenues (clusterId) {
        try {
            const response = await this.repository.getClusterRevenues(clusterId)
            return this.responseValidator(response)
        } catch (e) {
            return new ErrorHandler(e.response.data.data.message, 'http')
        }
    }

    async getAllRevenues (period, startDate, endDate) {
        const queryString = `?period=${period}&startDate=${startDate ??
        ''}&endDate=${endDate ?? ''}`
        try {
            const response = await this.repository.getAllRevenues(queryString)
            return this.responseValidator(response, [200,201])
        } catch (e) {
            return new ErrorHandler(e.response.data.data.message, 'http')
        }
    }

    responseValidator (response, expectedStatus = [200]) {
        return expectedStatus.includes(response.status)
            ? response.data.data :
            new ErrorHandler(response.error, 'http', response.status)
    }
}
