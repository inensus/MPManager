<template>

    <widget :id="'revenue-trends'" :title="'Revenue Trends'">
        <div class="col-sm-12">
            <GChart
                    type="ColumnChart"
                    :data="clusterService.trendChartData.base"
                    :options="chartOptions"
                    :resizeDebounce="500"
            />
        </div>


        <div class="col-sm-12">
            <GChart
                    type="LineChart"
                    :data="clusterService.trendChartData.overview"
                    :options="chartOptions"
                    :resizeDebounce="500"
            />
        </div>
    </widget>
</template>

<script>
import Widget from '../../shared/widget'
import { ClusterService } from '../../services/ClusterService'

export default {
    name: 'RevenueTrends',
    components: {Widget},
    props: {
        clusterId: {
            type: String,
            required: true,
        }
    },
    created () {
        this.getTrends()
    },
    data () {
        return {
            clusterService : new ClusterService(),
            period: {},
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
        async getTrends () {
            try {
                await this.clusterService.getClusterTrends(this.clusterId, this.period.from, this.period.to)
            }catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        alertNotify (type, message) {
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message
            })
        },

    },
}
</script>

<style scoped>

</style>
