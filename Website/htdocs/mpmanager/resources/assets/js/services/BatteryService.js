import { Battery } from '../entities/Battery'
import { EventBus } from '../shared/eventbus'

export class BatteryService {
    constructor () {
        this.batteryData = []
        this.stateChartData = []
        this.energyChartData = []
        this.subscriber = null

    }

    async getBatteryUsageList (miniGridId) {
        if (typeof (miniGridId) === 'undefined') {
            return null
        }

        let list = await axios.get(resources.batteries.detail + miniGridId + '/batteries?start_date=2020-10-01&limit=100')

        list.data.data.map((b) => (
            this.batteryData.push(new Battery().fromJson(b))
        ))

        return true
    }

    chartDataDistributor (data) {

        this.prepareStateChartData(data)
        EventBus.$emit('chartLoaded',this.subscriber)
    }

    prepareStateChartData (b) {
        if (this.stateChartData.length === 0) {
            this.stateChartData.push(['Date', 'SoC'])
        }
        let chartData = []
        let date = new Date(Date.parse(b.read_out))

        chartData.push(
            date,
            {
                v: b.soc_average,
                f: 'Date:' + b.read_out.split(' ')[0] + '\nMax:' + b.soc_max + '\nAvg:' + b.soc_average + '\nMin:' + b.soc_min
            }

        )
        this.stateChartData.push(chartData)
    }

    prepareChartData () {
        if (this.batteryData.length === 0) {
            return null
        }

        this.batteryData.map((b) => (
            this.chartDataDistributor(b)
        ))

    }

}
