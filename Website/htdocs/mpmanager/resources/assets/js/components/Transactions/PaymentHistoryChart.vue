<template>
    <donut-chart id="donut" :data="_donutData" colors='[ "#FF6384", "#36A2EB", "#FFCE56" ]'
                 resize="true"></donut-chart>


</template>

<script>
  import { DonutChart } from 'vue-morris'

  export default {
    name: 'PaymentHistoryChart',
    components: {DonutChart},
    props: ['paymentdata'],
    computed: {
      _donutData: function () {
        for (let i in this.paymentdata) {
          this.donutData.push({
            label: this.paymentdata[i].payment_type,
            value: this.paymentdata[i].amount

          })
        }
        let data = {}

        return this.donutData
      }
    },

    data () {
      return {
        hebele: this.paymentdata,

        donutData: []
      }
    }
  }
</script>

<style scoped>

</style>
