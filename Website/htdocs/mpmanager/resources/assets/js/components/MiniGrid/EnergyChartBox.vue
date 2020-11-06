<template>
    <div class="md-layout">
        <md-toolbar class="md-dense">
            <div class="md-toolbar-section-start">
                <h3 class="md-title" > Battery & PV Charts </h3>
                <div class="md-subheader" ><span><b> Resolution:</b> 3 Days</span>&nbsp; | &nbsp;<span><b> Period:</b> {{  }} - {{ todayDate }}</span>  </div>
            </div>
            <div class="md-toolbar-section-end">
                <md-button class="md-icon-button">
                    <md-icon>keyboard_arrow_left</md-icon>
                    <md-tooltip md-direction="top">Previous 3 Days</md-tooltip>
                </md-button>
                <md-button class="md-icon-button">
                    <md-icon>keyboard_arrow_right</md-icon>
                    <md-tooltip md-direction="top">Next 3 Days</md-tooltip>
                </md-button>
                <md-button class="md-icon-button">
                    <md-tooltip md-direction="top">Increase Period</md-tooltip>
                    <md-icon>add</md-icon>
                </md-button>
                <md-button class="md-icon-button">
                    <md-tooltip md-direction="top">Decrease Period</md-tooltip>
                    <md-icon>remove</md-icon>
                </md-button>

            </div>
        </md-toolbar>
        <!-- <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-33">
             <chart-box
                 v-if="batteryService.energyChartData.length>0"
                 title="Battery Discharge"
                 chart-type="LineChart"
                 gradient-end="#033d05"
                 gradient-start="#035c08"
                 :data="batteryService.energyChartData"/>
         </div>-->
        <div class="md-layout-item md-size-100">
            <custom-chart
                :data="pvService.chartData"
                :chart-type="'line'"
                :title="'Energy'"
                :subscriber="subscriber.energy"
            ></custom-chart>
        </div>
        <div class="md-layout-item md-size-100">
            <custom-chart
                :data="batteryService.stateChartData"
                :chart-type="'line'"
                :title="'Battery Charge Status'"
                :subscriber="subscriber.battery"
            ></custom-chart>

        </div>
        <div class="md-layout-item md-size-100">
            <custom-chart
                :chart-type="'bar'"
                :title="'Energy Status'"
                :subscriber="subscriber.energyStatus"
            ></custom-chart>

        </div>


    </div>


</template>

<script>
import { BatteryService } from '../../services/BatteryService'
import { PVService } from '../../services/PVService'
import CustomChart from '../../shared/CustomChart'
import { EventBus } from '../../shared/eventbus'

export default {
    name: 'EnergyChartBox',
    components: { CustomChart },
    props: {
        miniGridId: {
            required: true
        }
    },
    created () {
        this.initBatteryChart()
        this.initPVChart()

    },
    mounted () {
        EventBus.$emit('chartLoaded',this.subscriber.energyStatus)
        EventBus.$emit('chartLoaded',this.subscriber.energy)
        this.batteryService.subscriber = this.subscriber.battery
        this.pvService.subcsriber = this.subscriber.energy
    },
    data: () => (
        {
            todayDate : new Date().toJSON().slice(0,10).replace(/-/g,'/'),
            batteryService: new BatteryService(),
            pvService: new PVService(),
            subscriber: {
                energy:'energy',
                energyStatus:'energyStatus',
                battery:'batteryCharge'

            }
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
