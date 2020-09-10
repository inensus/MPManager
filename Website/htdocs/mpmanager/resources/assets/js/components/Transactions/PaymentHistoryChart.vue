<template>
    <donut-chart id="donut" :data="prepareChartData()" colors='[ "#FF6384", "#36A2EB", "#FFCE56" ]'
                 resize="true"></donut-chart>


</template>

<script>
import { DonutChart } from 'vue-morris'

export default {
    name: 'PaymentHistoryChart',
    components: {DonutChart},
    props: ['paymentdata'],
    data () {
        return {
            hebele: this.paymentdata,

            donutData: []
        }
    },
    methods:{
        prepareChartData(){
            for (let i in this.paymentdata) {
                this.donutData.push({
                    label: this.paymentdata[i].payment_type,
                    value: this.paymentdata[i].amount

                })
            }
            return this.donutData
        }
    }
}
</script>

<style scoped>

</style>
