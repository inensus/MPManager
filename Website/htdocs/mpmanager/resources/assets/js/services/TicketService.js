import Repository from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class TicketService {
    constructor () {
        this.repository = Repository.get('ticket')
        this.categories = []
    }

    async getCategories () {
        try {
            let response = await this.repository.listCategory()
            if (response.status === 200 || response.status === 201) {
                this.categories = response.data.data
                return this.categories
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            return new ErrorHandler(e, 'http')
        }
    }

    async createMaintenanceTicket (maintenanceData) {
        let maintenanceData_PM =
            {
                creator: maintenanceData.creator,
                dueDate: maintenanceData.dueDate,
                label: maintenanceData.category,
                outsourcing: maintenanceData.amount,
                description: maintenanceData.description,
                title: maintenanceData.title,
                owner_id: maintenanceData.assigned,
                owner_type: 'person',
            }

        try {
            let response = await this.repository.create(maintenanceData_PM)
            if (response.status === 200 || response.status === 201) {
                return response
            } else {

                return new ErrorHandler(response.error, 'http', response.status)
            }

        } catch (e) {
            return new ErrorHandler(e, 'http')
        }
    }
}
