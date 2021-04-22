import RepositoryFactory from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class BatchRevenueService {
    constructor () {
        this.revenueList = null
        this.repository = RepositoryFactory.get('batchRevenue')
        this.batchRevenues = {}
        this.comparedRevenues = {}
    }

    async getRevenueForPeriod(targetId, targetType, startDate, endDate){
        let data = {}
        let targetPeriod = {
            'target_type_id': targetId,
            'target_type': targetType,
            'start_date': startDate,
            'end_date': endDate
        }
        try {
            let response  = await this.repository.getRevenueForPeriod(targetPeriod)
            if(response.status === 200){
                data.revenueList = response.data.data
                data.revenueList.averages = this.calculateAverages(data.revenueList)
                return data
            }else{
                return new ErrorHandler(response.error, 'http', response.status)
            }
        }catch (e) {
            let erorMessage = e.response.data.data.message
            return new ErrorHandler(erorMessage, 'http')
        }

    }
    calculateAverages (list) {
        let data = {}
        for (let connection in list.target.targets) {
            let result = '-'
            if (list.revenue[connection] > 0) {
                result = parseInt(list.revenue[connection]) / list.total_connections[connection]
            }
            data[connection] = result
        }
        return data
    }
    initializeDonutCharts (initValue, batchRevenues) {
        let donutData = [initValue]
        //donut chart for given period
        let data = batchRevenues.revenueList.revenue
        for (let con in data) {
            let connectionRev = data[con]
            donutData.push([
                con, parseInt(connectionRev)
            ])
        }
        return donutData
    }
}
