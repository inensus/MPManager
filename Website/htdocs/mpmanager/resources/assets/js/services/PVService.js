import { PV } from '../entities/PV'
import { EventBus } from '../shared/eventbus'

export class PVService {

    constructor () {
        this.usageList = []
        this.chartData = []
        this.subscriber = null
    }

    async getList (miniGridId) {
        if (typeof (miniGridId) === 'undefined') {
            return null
        }
        let list = await axios.get(resources.pv.list + miniGridId)

        console.log('PV USAGE LIST ', list)

        list.data.data.map((l) => (
            this.usageList.push(new PV().fromJson(l))
        ))
        return true
    }
    randomizeFloat(min, max) {
        if(max == null) {
            max = (min == null ? Number.MAX_VALUE : min)
            min = 0.0
        }

        if(min >= max) {
            throw new Error('Incorrect arguments.')
        }

        return min + (max - min) * 3
    }

    prepareChartData () {
        if (this.usageList.length === 0) {
            return null
        }

        let chartData = [['Date', 'PV', 'Generator', 'Battery', 'Total']]
        this.usageList.map((u) => (
            chartData.push([
                new Date(Date.parse(u.created_at)),
                { v: 50+Math.random()*10 },
                { v: 10+Math.random()*10 },
                { v: 70+Math.random()*10 },
                { v: 130+Math.random()*10 }


            ])
        ))
        this.chartData = chartData
        EventBus.$emit('chartLoaded',this.subscriber)
        return chartData
    }

}
