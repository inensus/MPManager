import { PV } from '../entities/PV'

export class PVService {

    constructor (props) {
        this.usageList = []
        this.chartData = []
    }

    async getList (miniGridId) {
        if (typeof (miniGridId) === 'undefined') {
            return null
        }
        let list = await axios.get(resources.pv.list + miniGridId)

        list.data.data.map((l) => (
            this.usageList.push(new PV().fromJson(l))
        ))
        return true
    }

    prepareChartData () {
        if (this.usageList.length === 0) {
            return null
        }

        let chartData = [['Date', 'New Generated', 'Daily Generated']]

        this.usageList.map((u) => (
            chartData.push([
                u.created_at.split(' ')[1],
                { v: u.new_generated_energy, f: u.new_generated_energy + 'kWh' },
                { v: u.daily, f: u.daily + 'kWh' + '\n Date:' + u.created_at.split(' ')[0] },

            ])
        ))
        this.chartData = chartData
        return chartData
    }

}
