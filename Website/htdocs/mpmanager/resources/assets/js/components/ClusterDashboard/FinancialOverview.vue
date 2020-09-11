<template>
    <widget
        :title="'Finance Overview  (Period : ' +periodText + ')'"
        :id="'clusters-finance-overview'"
        button
        button-text="Set Period"
        button-color="red"
        :callback="showPeriod"

    >
        <div v-if="setPeriod" class="period-selector">
            <p>Select a period for income data</p>
            <div class="md-layout md-gutter">
                <div class="md-layout-item md-size-100">
                    <md-datepicker v-model="period.from" md-immediately>
                        <label>From Date</label>
                    </md-datepicker>
                </div>
                <div class="md-layout-item md-size-100">
                    <md-datepicker v-model="period.to" md-immediately>
                        <label>To Date</label>
                    </md-datepicker>
                </div>
            </div>


            <div style="margin-top: 5px;">
                <md-progress-bar md-mode="indeterminate" v-if="loading"/>
                <button style="width:100%;" class="btn btn-primary" v-if="!loading" @click="getClusterFinancialData">
                    Send
                </button>
            </div>
        </div>

        <div class="md-layout md-gutter" style="padding: 10px">
            <!-- donut chart-->
            <div class="md-layout-item md-size-35 md-medium-size-100"
                 :class="lineChartFullScreen? 'md-size-100' : 'md-size-35' "
                 v-if="financialData">
                <md-card class="chart-card">
                    <md-card-header>
                        <md-card-header-text>
                            Revenue Line
                        </md-card-header-text>
                        <md-menu class="md-medium-hide" md-size="big" md-direction="bottom-end">
                            <md-button class="md-icon-button" md-menu-trigger
                                       @click="maximize('lineChartFullScreen')">
                                <md-icon>fullscreen</md-icon>
                            </md-button>

                        </md-menu>
                    </md-card-header>
                    <md-card-content>
                        <GChart
                            type="LineChart"
                            :data="financialDataChart('line', false)"
                            :options="chartOptions"
                            :resizeDebounce="500"
                            ref="gChart"
                            :events="chartEvents"
                        />
                    </md-card-content>
                </md-card>
            </div>
            <!-- Column Chart -->
            <div class="md-layout-item md-size-35 md-medium-size-100"
                 v-if="financialData"
                 :class="barChartFullScreen? 'md-size-100' : 'md-size-35' ">
                <md-card class="chart-card">
                    <md-card-header>
                        <md-card-header-text>
                            Bar Chart
                        </md-card-header-text>
                        <md-menu class="md-medium-hide" md-size="big" md-direction="bottom-end">
                            <md-button class="md-icon-button" md-menu-trigger
                                       @click="maximize('barChartFullScreen')">
                                <md-icon>fullscreen</md-icon>
                            </md-button>

                        </md-menu>
                    </md-card-header>
                    <md-card-content>
                        <GChart
                            type="ColumnChart"
                            :data="financialDataChart('column')"
                            :options="chartOptions"
                            :resizeDebounce="500"
                            ref="gChart"
                            :events="chartEvents"
                        />
                    </md-card-content>
                </md-card>
            </div>

            <div class="md-layout-item md-size-30 md-medium-size-100"
                 v-if="financialData"
                 :class="donutChartFullScreen? 'md-size-100' : 'md-size-30' ">
                <md-card class="chart-card">
                    <md-card-header>
                        <md-card-header-text>
                            Percentiles of the Revenue
                        </md-card-header-text>
                        <md-menu class="md-medium-hide" md-size="big" md-direction="bottom-end">
                            <md-button class="md-icon-button" md-menu-trigger
                                       @click="maximize('donutChartFullScreen')">
                                <md-icon>fullscreen</md-icon>
                            </md-button>

                        </md-menu>
                    </md-card-header>
                    <md-card-content>
                        <GChart
                            type="PieChart"
                            :data="financialDataChart('column')"
                            :options="chartOptions"
                            :resizeDebounce="500"
                            ref="gChart"
                            :events="chartEvents"
                        />
                    </md-card-content>
                </md-card>

            </div>
        </div>


    </widget>


</template>

<script>
import Widget from '../../shared/widget'
import moment from 'moment'
import { ClusterService } from '../../services/ClusterService'

export default {
    name: 'FinancialOverview',
    components: {  Widget },
    props: {
        clusterId: {
            type: String,
            default: '1'
        }
    },
    data () {
        return {
            clusterService: new ClusterService(),
            lineChartFullScreen: false,
            barChartFullScreen: false,
            donutChartFullScreen: false,
            loading: false,
            periodText: '2019.01.01 - Today',
            period: {
                from: null,
                to: null,
            },
            setPeriod: false,
            clicks: 0, //to detect a double click on a chart
            financialData: null,
            chartOptions: {
                chart: {
                    title: 'Customer Payment Flow',
                    subtitle: 'Sales, Expenses, and Profit: 2014-2017',
                },
            },
            chartEvents: {
                select: () => {
                },
                click: () => {
                    this.clicks++
                    let parent = this
                    setTimeout(function () {
                        if (parent.clicks >= 2) {
                            parent.chartType = parent.toggleChartType()

                        }
                        parent.clicks = 0
                    }, 250)
                },
            },
            disabled: {
                customPredictor:
                        function (date) {
                            let today = new Date()
                            let minDate = new Date('2018-01-01')
                            // disables the date if it is a multiple of 5
                            if (date > today || date < minDate) {
                                return true
                            }
                        }
            },
        }
    },
    mounted () {
        this.getClusterFinancialData()
    },
    methods: {
        showPeriod () {
            this.setPeriod = !this.setPeriod
        },
        async getClusterFinancialData () {
            try {
                this.loading = true
                let from = this.period.from !== null ? moment(this.period.from).format('YYYY-MM-DD') : null
                let to = this.period.to !== null ? moment(this.period.to).format('YYYY-MM-DD') : null


                this.financialData = await this.clusterService.getClusterCitiesRevenue(this.clusterId, 'monthly',
                    from, to)

                this.loading = false


                if (from !== null) {

                    this.periodText = from + ' - ' + to
                }

            } catch (e) {
                this.alertNotify('error', e.message)

            }
            this.setPeriod = false
            this.loading = false
        },
        financialDataChart (type, summary = false) {
            let data = []
            if (type === 'column') {
                return this.columnChartData()
            } else if (type === 'line') {
                return this.lineChartData(summary)
            }
            return data
        },
        /**
             * Generates data array for line chart
             */
        lineChartData (summary) {
            let data = []
            data.push(['Period'])

            let miniGridsCount = this.financialData.length
            if (miniGridsCount === 0) {
                return
            }

            data[0] = data[0].concat(this.financialData.map(f => {return f.name}))
            if (summary) {
                data[0].push('Total')
            }
            let periods = this.financialData[0].period
            for (let p in periods) {

                data.push(this.getPeriodicData(miniGridsCount, p, summary))
            }
            return data
        },
        /**
             * Generates data array for column and donut chart
             */


        columnChartData (summary) {
            let data = []
            let summaryRevenue = 0
            data.push(['MiniGrid Name', 'Revenue'])
            for (let i in this.financialData) {
                let cD = this.financialData[i]
                if (summary) {
                    summaryRevenue += cD.totalRevenue
                }
                data.push([cD.name, cD.totalRevenue])
            }
            if (summary) {
                data.push(['Sum', summaryRevenue])
            }
            return data
        },

        /**
             * Inserts the cluster names to the given data array and returns it
             * @param count
             * @param data
             */
        insertCityNames
        (count, data) {
            for (let i = 0; i < count; i++) {
                data.push(this.financialData.cities[i].name)
            }
            return data
        },
        /**
             *
             * @param count the length of clusters
             * @param periodName current selected Period
             * @returns array
             */
        getPeriodicData (count, periodName, summary) {
            let data = []
            let sum = 0
            data.push(periodName)
            for (let i = 0; i < count; i++) {
                if (summary) {
                    sum += this.financialData[i].period[periodName].revenue
                }
                data.push(this.financialData[i].period[periodName].revenue)
                //data.push(this.financialData[i].period[periodName].revenue)
            }
            if (summary) {
                data.push(sum)
            }
            return data
        },

        dateSelectedFrom (date) {
            this.setDate(date, 'from')
        },
        dateSelectedTo (date) {
            this.setDate(date, 'to')
        },
        setDate (dateData, target) {
            let date = moment(dateData)
            if (target === 'from') {
                this.period.from = date.format('YYYY-MM-DD')
            } else {
                this.period.to = date.format('YYYY-MM-DD')
            }
        },
        maximize (data) {
            //eval('this.data = !this.data')
            if (data === 'lineChartFullScreen') {
                this.lineChartFullScreen = !this.lineChartFullScreen
            } else if (data === 'barChartFullScreen') {
                this.barChartFullScreen = !this.barChartFullScreen
            } else if (data === 'donutChartFullScreen') {
                this.donutChartFullScreen = !this.donutChartFullScreen
            }
            window.dispatchEvent(new Event('resize'))
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

<style lang="scss">
    .datepicker-right .vdp-datepicker__calendar {
        right: 0;
    }

    .period-selector {
        position: absolute;
        top: 0;
        right: 0;
        z-index: 9999;
        padding: 15px;
        background-color: white;
        border: 1px solid #ccc;
    }

    .md-datepicker-dialog {
        z-index: 1000 !important;
    }

    .chart-card {
        margin-bottom: 1vh;
    }


</style>
