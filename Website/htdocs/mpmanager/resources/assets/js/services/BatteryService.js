import { Battery } from '../entities/Battery'

export class BatteryService {
    constructor (props) {
        this.batteryData = []

        this.stateChartData = []
        this.energyChartData = []

    }

    async getBatteryUsageList (miniGridId) {
        if (typeof (miniGridId) === 'undefined') {
            return null
        }

        let list = await axios.get(resources.batteries.detail + miniGridId + '/batteries')

        list.data.data.map((b) => (
            this.batteryData.push(new Battery().fromJson(b))
        ))
        console.log('BATTERIES TaMAM', this.batteryData)

        return true
    }

    chartDataDistributor (data) {
        console.log('BATTERIES DISTR Basladi')
        this.prepareStateChartData(data)
        console.log('BATTERIES state bitti')
        this.prepareEnergyChartData(data)

        console.log('BATTERIES DIST TAMAM', this.stateChartData, this.energyChartData)
    }

    prepareStateChartData (b) {
        if (this.stateChartData.length === 0) {
            this.stateChartData.push(['Date', 'SoC', 'SoH'])
        }
        let chartData = []

        chartData.push(
            b.read_out.split(' ')[1],
            {
                v: b.soc_average,
                f: 'Date:' + b.read_out.split(' ')[0] + '\nMax:' + b.soc_max + '\nAvg:' + b.soc_average + '\nMin:' + b.soc_min
            },
            {
                v: b.soh_average,
                f: 'Date:' + b.read_out.split(' ')[0] + '\nMax:' + b.soh_max + '\nAvg:' + b.soh_average + '\nMin:' + b.soh_min
            },
        )
        this.stateChartData.push(chartData)
        console.log('STATE CHART', chartData)
    }

    prepareEnergyChartData (d) {
        if (this.energyChartData.length === 0) {
            this.energyChartData.push(['Date', 'Total', 'New'])
        }
        let chartData = []

        chartData.push(
            d.read_out.split(' ')[1],
            {
                v: d.d_total,
                f: 'Date:' + d.read_out.split(' ')[0] + '\n' + d.d_total + ' ' + d.d_total_unit,
            },
            {
                v: d.d_newly_energy,
                f: 'Date:' + d.read_out.split(' ')[0] + '\n' + d.d_newly_energy + ' ' + d.d_newly_energy_unit,
            },
        )
        this.energyChartData.push(chartData)

    }

    prepareChartData () {
        console.log('BATTERIES prepare basladi')
        if (this.batteryData.length === 0) {
            return null
        }

        this.batteryData.map((b) => (
            this.chartDataDistributor(b)
        ))

    }

}
