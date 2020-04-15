<template>
    <widget
        :title="'Finance Overview Period (' + finance_period+')'"
        :id="'clusters-finance-overview'"
        button
        button-text="Set Period"
        button-color="red"
        :callback="showPeriod"

    >
        <div v-if="setPeriod"
             style="position: absolute; top: 0; right: 0; z-index: 9999; padding: 15px; background-color: white; border:1px solid #ccc;">
            <p>Select a period for income data</p>
            <h5>From</h5>
            <datepicker
                class="datepicker-right"
                monday-first
                minimum-view="day"
                :disabledDates="disabled"
                @selected="dateSelectedFrom"
                maximum-view="year"/>
            <h5> To</h5>
            <datepicker
                class="datepicker-right"
                monday-first
                minimum-view="day"
                @selected="dateSelectedTo"
                :disabledDates="disabled"
                maximum-view="year"/>
            <div style="margin-top: 5px;">
                <button style="width:100%;" class="btn btn-primary" @click="getClusterFinancialData">Send</button>
            </div>
        </div>

        <div v-if="loaded">
            <div class="md-layout md-gutter" style="padding: 10px">
                <!-- donut chart-->
                <div class="md-layout-item "
                     :class="lineChartFullScreen? 'md-size-100' : 'md-size-35' "
                     v-if="financialData">
                    <md-card>
                        <md-card-header>
                            <md-card-header-text>
                                Revenue Line
                            </md-card-header-text>
                            <md-menu md-size="big" md-direction="bottom-end">
                                <md-button class="md-icon-button" md-menu-trigger
                                           @click="maximize('lineChartFullScreen')">
                                    <md-icon>fullscreen</md-icon>
                                </md-button>

                            </md-menu>
                        </md-card-header>
                        <md-card-content>
                            <GChart
                                type="LineChart"
                                :data="financialDataChart('line', true)"
                                :options="chartOptions"
                                :resizeDebounce="500"
                                ref="gChart"
                                :events="chartEvents"
                            />
                        </md-card-content>
                    </md-card>
                </div>

                <div class="md-layout-item "
                     :class="barChartFullScreen? 'md-size-100' : 'md-size-35' "
                     v-if="financialData">
                    <md-card>
                        <md-card-header>
                            <md-card-header-text>
                                Revenue Columns
                            </md-card-header-text>
                            <md-menu md-size="big" md-direction="bottom-end">
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

                <div class="md-layout-item "
                     :class="donutChartFullScreen? 'md-size-100' : 'md-size-30' "
                     v-if="financialData">
                    <md-card>
                        <md-card-header>
                            <md-card-header-text>
                                Revenue Percentiles
                            </md-card-header-text>
                            <md-menu md-size="big" md-direction="bottom-end">
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
        </div>
        <div v-else>
            <h2 class="text-center"> Loading Data</h2>
        </div>


    </widget>


</template>

<script>
    import Widget from '../../shared/widget'
    import { resources } from '../../resources'
    import Datepicker from 'vuejs-datepicker'
    import moment from 'moment'

    export default {
        name: 'FinancialOverview',
        components: { Datepicker, Widget },
        props: {
            clusterId: {
                type: Number,
                default: 1,
            }
        },
        data () {
            return {
                lineChartFullScreen: false,
                barChartFullScreen: false,
                donutChartFullScreen: false,
                period: {},
                loaded: false,
                setPeriod: false,
                clicks: 0, //to detect a double click on a chart
                financialData: [],
                finance_period: 'From 2019.01.01 - Today',
                chartOptions: {
                    chart: {
                        title: 'Customer Payment Flow',
                        subtitle: 'Sales, Expenses, and Profit: 2014-2017',
                    },
                    //colors: ['#FF6384', '#CC6384', '#36A2EB']
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
                this.setPeriod = true
            },
            getClusterFinancialData () {
                axios.post(resources.clusters.revenue.overview, {
                    'period': 'monthly',
                    'startDate': this.period.from,
                    'endDate': this.period.to
                }).then((response) => {
                    this.financialData = response.data.data
                    this.loaded = true
                    if (this.period.from)
                        this.finance_period = this.period.from + ' - ' + this.period.to
                    this.setPeriod = false
                })
            },
            financialDataChart (type, summary = false) {
                let data = []
                if (type === 'column') {
                    return this.columnChartData(summary)
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

                let clustersCount = this.financialData.length
                if (clustersCount === 0) {
                    return
                }

                data[0] = this.insertCityNames(clustersCount, data[0])
                if (summary) {
                    data[0].push('Total')
                }

                let periods = this.financialData[0].period
                for (let p in periods) {
                    data.push(this.getPeriodicData(clustersCount, p, summary))
                }
                return data
            },
            /**
             * Generates data array for column and donut chart
             */
            columnChartData (summary) {
                let data = []
                let summaryRevenue = 0
                data.push(['Cluster Name', 'Revenue'])
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
            insertCityNames (count, data) {
                for (let i = 0; i < count; i++) {
                    data.push(this.financialData[i].name)
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
        }
    }
</script>

<style>
    .datepicker-right .vdp-datepicker__calendar {
        right: 0;
    }
</style>
