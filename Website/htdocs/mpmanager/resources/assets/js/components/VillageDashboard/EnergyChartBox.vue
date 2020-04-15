<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-size-33">
            <chart-box
                v-if="batteryService.energyChartData.length>0"
                title="Battery Discharge"
                chart-type="LineChart"
                gradient-end="#033d05"
                gradient-start="#035c08"
                :data="batteryService.energyChartData"/>
        </div>
        <div class="md-layout-item md-size-33">
            <chart-box
                v-if="batteryService.stateChartData.length>0"
                title="Battery State"
                chart-type="LineChart"
                gradient-start="#47100f"
                gradient-end="#7b1a18"
                :data="batteryService.stateChartData"/>
        </div>
        <div class="md-layout-item md-size-33">
            <chart-box
                v-if="pvService.chartData"
                title="Pv Generation"
                :data="pvService.chartData"
                chart-type="LineChart"
                gradient-start="#012b30"
                gradient-end="#035a65"
            />
        </div>

    </div>


</template>

<script>
    import { BatteryService } from '../../services/BatteryService'
    import ChartBox from '../ChartBox'
    import { PVService } from '../../services/PVService'

    export default {
        name: 'EnergyChartBox',
        components: { ChartBox },
        props: {
            miniGridId: {
                type: String,
                required: true
            }
        },
        created () {
            this.initBatteryChart()
            this.initPVChart()
        },
        data: () => (
            {
                batteryService: new BatteryService(),
                pvService: new PVService(),
            }
        ),
        methods: {
            initBatteryChart () {
                this.batteryService.getBatteryUsageList(this.miniGridId).then((result) => {
                    if (!result) {
                        console.log('Battery chart data failed to load')
                        return
                    }
                    this.batteryService.prepareChartData()
                })
            },
            initPVChart () {
                this.pvService.getList(this.miniGridId).then((result) => {
                    if (!result) {
                        console.log('PV chart data failed to load')
                        return
                    }
                    this.pvService.prepareChartData()
                })
            }
        }
    }
</script>

<style scoped>

</style>
