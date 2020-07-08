import Repository from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class RevenueService {

    constructor () {
        this.repository = Repository.get('revenue')
        this.revenueTrends = null
        this.ticketsData = null
    }

    async getMiniGridRevenueTrends (miniGridId, startDate, endDate) {
        try {

            let period = {
                startDate: startDate,
                endDate: endDate
            }
            let response = await this.repository.trends(miniGridId, period)

            if (response.status === 200 || response.status === 201) {
                this.revenueTrends = response.data.data

                return this.revenueTrends
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')

        }
    }

    async getTicketsData (miniGridId) {
        try {

            let response = await this.repository.tickets(miniGridId)

            if (response.status === 200 || response.status === 201) {
                this.ticketsData = response.data.data

                return this.ticketsData
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')

        }
    }
}
