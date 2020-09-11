<template>

    <widget :id="'revenue-trends'" :title="'Revenue Trends'">
        <div class="col-sm-12">
            <GChart
                    type="ColumnChart"
                    :data="trendChartData.base"
                    :options="chartOptions"
                    :resizeDebounce="500"
            />
        </div>


        <div class="col-sm-12">
            <GChart
                    type="LineChart"
                    :data="trendChartData.overview"
                    :options="chartOptions"
                    :resizeDebounce="500"
            />
        </div>
    </widget>
</template>

<script>
import Widget from '../../shared/widget'

export default {
    name: 'RevenueTrends',
    components: {Widget},
    props: {
        clusterId: {
            type: String,
            required: true,
        }
    },
    mounted () {
        this.getTrends(this.clusterId)
    },
    data () {
        return {
            clusterTrends: null,
            period: {},
            trendChartData: {base: null, overview: null},
            chartOptions: {
                chart: {
                    legend: {
                        position: 'top'
                    },
                },
                hAxis: {
                    textPosition: 'out',
                    slantedText: true
                },
                vAxis: {

                    //scaleType: 'mirrorLog',
                },
                colors: ['#739e73', '#3276b1', '#78002e', '#dce775',],
                height: 550,
            },
            chartOptionsSmall: {
                chart: {

                    legend: {
                        position: 'top'
                    },
                },
                hAxis: {
                    textPosition: 'out',
                    slantedText: true
                },
                vAxis: {

                    //scaleType: 'mirrorLog',
                },
                colors: ['#739e73', '#3276b1', '#78002e', '#dce775',],
                height: 220,
            },
        }
    },
    methods: {
        fillTrends () {
            let trendKeys = (Object.keys(this.clusterTrends))
            this.trendChartData.base = [(Object.keys(this.clusterTrends))]
            this.trendChartData.base[0].unshift('Date')
            for (let i in this.clusterTrends[trendKeys[0]]) { // iterate over periods
                let tmpData = []
                for (let j in trendKeys) { //iterate over connection names
                    tmpData.push(
                        this.clusterTrends[trendKeys[j]][i]
                    )
                }
                tmpData.unshift(i)
                this.trendChartData.base.push(tmpData)
            }
        },
        getTrends () {
            axios.post(resources.clusters.revenue.trends + this.clusterId + '/revenue/analysis',
                {
                    'period': 'monthly',
                    'startDate': this.period.from,
                    'endDate': this.period.to
                })
                .then((response) => {
                    this.clusterTrends = response.data.data
                    this.fillTrends()
                })
        }

    },
}
</script>

<style scoped>

</style>
