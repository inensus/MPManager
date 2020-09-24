<template>

    <widget
        title="Payment Flow"
        icon="money"
        :show-refresh-button="false"

    >
        <md-card>
            <md-card-header>
                <div class="md-title">
                    Monthly Avg.
                    <span class="txt-color-blue" id="flow_total">{{paymentSum}}</span>
                </div>
            </md-card-header>

            <md-card-content>
                <div class="md-layout" >
                    <div class="md-layout-item md-size-100" >
                        <GChart type="ColumnChart" :data="chartData" :options="chartOptions"/>
                    </div>
                    <div class="md-layout-item md-size-100">
                        Average Period
                        <span class="txt-color-yellow">{{paymentPeriod}}</span>
                    </div>
                    <div class="md-layout-item md-size-100" >
                        Last Payment
                        <span
                            :class=" parseInt(lastPayment) < parseInt(paymentPeriod) ? 'txt-color-green': 'txt-color-red'"
                        >{{lastPayment}}</span>
                    </div>
                    <div class="md-layout-item md-size-100">
                        AccessRate Debt
                        <span
                            :class=" parseInt(accessDebt) == 0 ? 'txt-color-green': 'txt-color-red'"
                        >{{accessDebt}}</span>
                    </div>
                    <div class="md-layout-item md-size-100">
                        Deferred Debt
                        <span
                            :class=" parseInt(deferredDebt) == 0 ? 'txt-color-green': 'txt-color-red'"
                        >{{deferredDebt}}</span>
                    </div>
                </div>
            </md-card-content>
        </md-card>
    </widget>


</template>

<script>
import { resources } from '../../resources'
import { currency } from '../../mixins/currency'
import { GChart } from 'vue-google-charts'
import Widget from '../../shared/widget'

export default {
    name: 'PaymentFlow',
    components: {
        Widget,
        GChart
    },
    mixins: [currency],
    data () {
        return {
            monthNames: [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'June',
                'July',
                'Aug',
                'Sept',
                'Oct',
                'Nov',
                'Dec'
            ],
            chartData: [],
            chartOptions: {
                chart: {
                    title: 'Customer Payment Flow',
                    subtitle: 'Sales, Expenses, and Profit: 2014-2017'
                },
                colors: ['#1b9e77', '#d95f02', '#7570b3']
            },
            lastPayment: null,
            paymentPeriod: 0,
            flow: [],
            loaded: false,

            accessDebt: 0,
            deferredDebt: 0
        }
    },
    computed: {
        paymentSum: function () {
            let cur = this.appConfig.currency
            let currentMonth = new Date().getMonth()
            let pass = true
            let total = 0
            let paidMonths = 0
            for (let i = 0; i < this.flow.length; i++) {
                if (currentMonth < i) break
                let flowVal = this.flow[i]
                if (flowVal > 0) {
                    pass = false
                }
                if (pass) {
                    continue
                }
                paidMonths++
                total += flowVal
            }

            let result = total === 0 ? 0 : Math.round((total / paidMonths), 2)

            return (
                this.readable(result) +
                    cur + ' ' +
                    paidMonths.toString() +
                    'Months'
            )
        }
    },
    created () {
        this.getFlow(this.$store.getters.person.id)
        this.getPeriod(this.$store.getters.person.id)
        this.getDebt(this.$store.getters.person.id)
    },
    methods: {
        getFlow (personId) {
            axios
                .get(resources.paymenthistories + personId + '/flow')
                .then(response => {
                    this.chartData = [['Month', 'Sale']]
                    this.flow = []
                    //  this.flow = response.data;
                    for (let i = 0; i < response.data.length; i++) {
                        this.flow.push(parseInt(response.data[i]))
                        this.chartData.push([
                            this.monthNames[i],
                            parseInt(response.data[i])
                        ])
                    }
                })
        },
        getPeriod (personId) {
            axios
                .get(resources.paymenthistories + personId + '/period')
                .then(response => {
                    this.paymentPeriod = response.data.data.difference
                    this.lastPayment = response.data.data.lastTransaction
                })
        },
        getDebt (personId) {
            axios.get(resources.debt + personId).then(response => {
                this.accessDebt = response.data.data.access_rate
                this.deferredDebt = response.data.data.deferred
            })
        }
    }
}
</script>
<style lang="css" scoped>
    .txt-color-green {
        color: green;
    }

    .txt-color-red {
        color: red;
    }

    .txt-color-yellow {
        color: #cccc05;
    }
</style>
