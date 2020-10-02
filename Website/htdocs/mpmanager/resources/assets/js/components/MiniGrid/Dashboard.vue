<template>
    <div>
        <section id="widget-grid">
            <div v-if="expanded === false" class="md-size-100" style="margin-bottom: 1.3vh;">

                <md-toolbar class="md-primary">
                    <div class="md-layout-item md-size-60">
                        <md-tabs class="md-primary" md-alignment="left" md-active-tab="tab-monthly">
                            <md-tab id="tab-weekly" md-label="Weekly" @click="tab = 'weekly'"></md-tab>
                            <md-tab id="tab-monthly" md-label="Monthly" @click="tab = 'monthly'"></md-tab>
                            <md-tab id="tab-anual" md-label="Anual" @click="tab = 'anual'"></md-tab>

                        </md-tabs>
                    </div>

                    <div class="md-toolbar-section-end">
                        <md-button class="md-raised  md-dense" @click="getBatchData"
                                   :disabled="Object.keys(highlighted.base).length===0">Apply
                        </md-button>
                        <md-button class="md-raised md-accent md-dense" @click="closeDatePicker">Close</md-button>
                    </div>

                </md-toolbar>


                <div class="md-layout md-size-90">
                    <div class="text-center md-layout md-gutter" v-if="tab==='weekly'" :key="tab">
                        <div class="md-layout-item md-size-50" style="margin-bottom: 1vh;">
                            <h4>Compared Week</h4>
                            <datepicker :inline="true" @selected="dateSelectedCompared"
                                        :highlighted="highlighted.compared"
                                        :monday-first="true"
                                        :disabledDates="disabled"></datepicker>
                        </div>
                        <div class="md-layout-item md-size-50" style="margin-bottom: 1vh;">
                            <h4>Base Week</h4>
                            <datepicker :inline="true" @selected="dateSelectedBase" :highlighted="highlighted.base"
                                        :monday-first="true"
                                        :disabledDates="disabled"></datepicker>

                        </div>

                    </div>

                    <div class="text-center md-layout md-gutter" v-if="tab==='monthly'" :key="tab">
                        <div class="md-layout-item md-size-50" style="margin-bottom: 1vh;">
                            <h4>Compared Month</h4>
                            <datepicker :inline="true"
                                        :minimum-view="'month'"
                                        :maximum-view="'year'"
                                        @selected="dateSelectedCompared"
                                        :value="highlighted.compared.from"
                                        :disabledDates="disabled"/>
                        </div>
                        <div class="md-layout-item md-size-50" style="margin-bottom: 1vh;">
                            <h4>Base Month</h4>
                            <datepicker :inline="true"
                                        :minimum-view="'month'"
                                        :maximum-view="'year'"
                                        @selected="dateSelectedBase"
                                        :highlighted="highlighted.base"
                                        :disabledDates="disabled"/>
                        </div>


                    </div>


                    <div class="text-center md-layout md-gutter" v-if="tab==='anual'" :key="tab">
                        <div class="md-layout-item md-size-50" style="margin-bottom: 1vh;">
                            <h4>Compared Year</h4>
                            <datepicker :inline="true"
                                        :minimum-view="'year'"
                                        :maximum-view="'year'"
                                        @selected="dateSelectedCompared"
                                        :value="highlighted.tmpCompared.from"
                                        :disabledDates="disabled"/>
                        </div>
                        <div class="md-layout-item md-size-50" style="margin-bottom: 1vh;">
                            <h4>Base Year</h4>
                            <datepicker :inline="true"
                                        :minimum-view="'year'"
                                        :maximum-view="'year'"
                                        @selected="dateSelectedBase"
                                        :highlighted="highlighted.tmpBase"
                                        :disabledDates="disabled"/>
                        </div>

                    </div>
                </div>
                <md-divider style="height: 1vh; background-color: #90CAF9 !important;"></md-divider>

            </div>
            <!-- modal-->
            <div class="md-layout md-gutter">
                <div class="md-layout-item md-medium-size-100  md-xsmall-size-100 md-size-100">
                    <md-toolbar style="margin-bottom: 3rem;">

                    <span class="md-title" v-if="miniGridData">
                    MiniGrid <strong>{{ miniGridData.name}}</strong>
                        <md-icon @click="editMiniGrid">plumbing</md-icon>

                </span>
                        <md-switch v-model="enableDataStream" @change="onDataStreamChange($event)" :disabled="switching"
                                   class="data-stream-switch">
                            <span v-if="!enableDataStream">Activate Data-logger </span>
                            <span v-else> Deactivate Data-logger</span>
                        </md-switch>
                        <div class="md-toolbar-section-end">

                        <span style="float: left">
                    Period : {{this.startDate}} - {{this.endDate}}
                </span>
                            <md-button class="md-raised" @click="openDatePicker">
                                <md-icon>calendar_today</md-icon>
                                Select Period
                            </md-button>

                        </div>
                    </md-toolbar>
                </div>
                <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
                    <box
                        :center-text="true"
                        :color="[ '#ffa726','#fb8c00']"
                        header-text="Sold energy (based on transactions)"
                        :header-text-color="'#dddddd'"
                        :sub-text="soldEnergy.toString() +'kWh'"
                        :sub-text-color="'#e3e3e3'"
                        box-icon="wb_iridescent"
                        :box-icon-color="'#578839'"
                    />
                </div>
                <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
                    <box v-if="currentTransaction"

                         :center-text="true"
                         :color="[ '#ef5350','#e53935']"
                         header-text="Processed Transactions"
                         :header-text-color="'#dddddd'"
                         :sub-text="readable(currentTransaction[0].amount).toString() "
                         :sub-text-color="'#e3e3e3'"
                         box-icon="list"
                         :box-icon-color="'#578839'"
                    />
                </div>
                <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
                    <box v-if="currentTransaction"

                         :center-text="true"
                         :color="[ '#6eaa44','#578839']"
                         header-text="Revenue"
                         :header-text-color="'#dddddd'"
                         :sub-text="readable(currentTransaction[0].revenue).toString() +this.appConfig.currency"
                         :sub-text-color="'#e3e3e3'"
                         box-icon="attach_money"
                         :box-icon-color="'#578839'"
                    />
                </div>
                <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
                    <solar-data-and-weather
                        v-if="this.miniGridData.location!==undefined"
                        :mini_grid_id="this.miniGridId"
                        :mini_grid_coordinates="this.miniGridData.location.points"
                    />
                </div>


                <div style="margin-top:1rem">&nbsp;</div>


                <div class="md-layout-item md-size-100">
                    <energy-chart-box :mini-grid-id="miniGridId"/>
                </div>


                <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-33">
                    <widget
                        :id="'revenue-pie'"
                        :headless="true"
                        :title="'Revenue Per Customer Type'"
                        color="red">

                        <GChart v-if="donutData.length>0"
                                type="PieChart"
                                :options="donutChartOptions"
                                :data="donutData">

                        </GChart>
                        <div v-else class="text-center">
                            <h4>
                                <b>No data for selected period</b> {{Object.keys(donutData).length}}
                            </h4>
                        </div>
                    </widget>
                </div>
                <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-33">
                    <widget
                        :id="'revenue-targets'"
                        :headless="true"
                        :title="'Revenue Targets Per Customer Type'"
                        color="green">

                        <div class="row" v-if="batchRevenues.revenueList !== null && donutData.length>0"
                             style="margin: 2vh;">


                            <div class="md-layout" style="margin-bottom: 0.8vh;"
                                 v-for="(revenue, index) in getPercentileList()" :key="index">
                                <div class="md-layout-item md-size-100">
                                    {{index}}
                                </div>
                                <div class="md-layout-item md-size-100">
                                    <div>
                                        <md-progress-bar class="md-dense" md-mode="determinate"
                                                         :md-value="targetPercentage(batchRevenues.revenueList.revenue[index], revenue.revenue)">

                                        </md-progress-bar>
                                        <md-tooltip md-delay="300" md-direction="bottom">
                                            Targetted Revenue:
                                            {{ targetPercentage(batchRevenues.revenueList.revenue[index],
                                            revenue.revenue, false)}} %
                                            {{readable(batchRevenues.revenueList.revenue[index])}}
                                        </md-tooltip>
                                    </div>

                                </div>
                            </div>
                        </div>


                        <div class="col-sm-12 text-center">
                            <span v-for="i in totalCircles" style="margin:5px" :key="i" class="dot compare-color-bg"
                                  :class="currentSelectedTargetCircle=== i-1 ? '':'period-indicator' "
                                  @click="setCircleIndex(i-1)"> </span>

                        </div>
                        <div class="col-sm-12 text-center">
                            {{currentSelectedTargetCircle+1}} of {{totalCircles}}
                        </div>

                    </widget>
                </div>

                <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
                    <mini-grid-map :mini-grid-id="miniGridId"/>
                </div>
                <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
                    <target-list
                        :target-id="miniGridId"
                        target-type="mini-grid"
                        :base="highlighted.base"
                        :compared="highlighted.compared"
                        @baseDataAvailable=baseDataAvailable
                    />
                </div>
                <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
                    <widget :id="'revenue-trends'" :title="'Revenue Trends'" :subscriber="subscriber.revenue_trends"
                    >
                        <div class="md-layout md-gutter">
                            <div class="md-layout-item">
                                <GChart
                                    type="ColumnChart"
                                    :data="trendChartData.base"
                                    :options="chartOptionsSmall"
                                    :resizeDebounce="500"
                                />
                            </div>
                            <div class="md-layout-item">
                                <GChart
                                    type="ColumnChart"
                                    :data="trendChartData.compare"
                                    :options="chartOptionsSmall"
                                    :resizeDebounce="500"
                                />
                            </div>
                        </div>


                        <GChart
                            type="LineChart"
                            :data="trendChartData.overview"
                            :options="chartOptions"
                            :resizeDebounce="500"
                        />

                    </widget>
                </div>
                <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
                    <widget :id="'ticketing-trends'" :title="'Tickets Overview'">
                        <div class="col-sm-12" style="margin: 2vh;">
                            <h5>Opened Tickets are on the left side and resolved tickets on the right side</h5>
                            <GChart
                                type="ColumnChart"
                                :data="openedTicketChartData"
                                :options="chartOptions"
                                :resizeDebounce="500"
                            />


                        </div>


                    </widget>
                </div>
            </div>

            <transition name="modal" v-if="showModal">
                <div class="modal-mask">
                    <div class="modal-wrapper">
                        <div class="modal-container">
                            <md-card class="md-size-100">
                                <md-card-header>
                                    <h3>Edit MiniGrid {{miniGridData.name}}</h3>
                                </md-card-header>
                                <md-card-content>
                                    <md-field>
                                        <label for="mini-grid-name">Name</label>
                                        <md-input type="text" id="mini-grid-name" class="form-control"
                                                  :value="miniGridData.name"></md-input>
                                    </md-field>

                                    <md-field>
                                        <label for="mini-grid-location">Location (Lat, Lon)</label>
                                        <md-input type="text" id="mini-grid-location"
                                                  class="form-control"
                                                  :value="miniGridData.location!== undefined ? miniGridData.location.points: ''"
                                                  placeholder="Latitude, Longitude"></md-input>


                                    </md-field>
                                </md-card-content>
                                <md-card-actions>
                                    <md-button class="md-raised md-accent" @click="showModal = false">
                                        <md-icon>cancel</md-icon>
                                        Close
                                    </md-button>

                                    <md-button @click="updateMiniGrid" class="md-raised md-primary">
                                        Update
                                    </md-button>
                                </md-card-actions>
                            </md-card>


                        </div>
                    </div>
                </div>
            </transition>
            <!-- purchasing modal-->
            <md-dialog :md-active.sync="ModalVisibility"
            >

                <md-dialog-content>
                    <stepper :watchingMiniGrids="watchingMiniGrids" :purchasingType="'logger'" v-if="ModalVisibility">

                    </stepper>
                </md-dialog-content>
            </md-dialog>
            <!-- purchasing modal-->
        </section>
        <redirection-modal :redirection-url="redirectionUrl" :imperative-item="imperativeItem"
                           :dialog-active="redirectDialogActive"/>
    </div>

</template>

<script>
import format from 'date-fns/format'
import Widget from '../../shared/widget'
import moment from 'moment'
import { currency } from '../../mixins/currency'
import Datepicker from 'vuejs-datepicker'
import { BatchRevenue } from '../../classes/revenue/batch'
import TargetList from './TargetList'
import SolarDataAndWeather from './SolarDataAndWeather'
import Box from '../Box'
import EnergyChartBox from './EnergyChartBox'
import { MiniGridService } from '../../services/MiniGridService'
import Stepper from '../../shared/stepper'
import { EventBus } from '../../shared/eventbus'
import MiniGridMap from './MiniGridMap'
import { RevenueService } from '../../services/RevenueService'
import RedirectionModal from '../../shared/RedirectionModal'

export default {
    name: 'Dashboard',
    components: {
        EnergyChartBox,
        Box,
        SolarDataAndWeather,
        MiniGridMap,
        TargetList,
        Widget,
        Datepicker,
        Stepper,
        RedirectionModal
    },
    mixins: [currency],
    created () {
        this.miniGridId = this.$route.params.id
        this.redirectionUrl += '/' + this.miniGridId
        this.getMiniGridData(this.miniGridId)
    },
    mounted () {
        //set initial dates for periods
        this.initializePeriods()
        this.fillRevenueTrendsOverview()
        this.getBatchData()

        EventBus.$on('closeModal', this.closeModal)
    },
    watch: {
        compareData: function () {
            if (this.compareData.length === 0) {
                return
            }
            this.compareTotal = 0
            for (let i = 0; i < this.actualData.length; i++) {
                this.compareTotal += parseInt(this.compareData[i].amount)
                let old = this.chartTmpData[i + 1]
                old.push(parseInt(this.compareData[i].amount))
                let parsedIndex = old[0].split(' - ')
                if (parsedIndex.length > 1) {
                    old[0] = parsedIndex[0] + ' - ' + this.compareData[i].date
                } else {
                    old[0] += ' - ' + this.compareData[i].date
                }
                this.chartTmpData[i + 1] = old
            }
            this.refreshChart()
        },
        actualData: function () {
            if (this.actualData.length === 0) {
                return
            }
            this.actualTotal = 0
            for (let i = 0; i < this.actualData.length; i++) {
                this.actualTotal += parseInt(this.actualData[i].amount)
                this.chartTmpData.push(
                    [this.actualData[i].date, parseInt(this.actualData[i].amount),],
                )
            }
        },
    },
    computed: {
        compareAnalysisAvailable () {
            return this.comparedRevenues.revenueList !== null
        },
        totalCircles () {
            if (this.batchRevenues.revenueList === null) {
                return 0
            }
            return Math.ceil(Object.keys(this.batchRevenues.revenueList.target.targets).length / 4)
        },
        //the summary of total revenues of both periods
        totalRevenues () {
            if (this.revenues.length === 0) {
                return 0
            }
            let sum = {
                revenue: 0,
                compareRevenue: 0,
                connections: 0,
                compareNewConnections: 0,
                totalConnections: 0,
                revenuePerConnection: 0,
                compareRevenuePerConnection: 0
            }
            for (let i in this.revenues) {
                let revenues = this.revenues[i]
                sum['revenue'] += revenues.revenue
                sum['compareRevenue'] += revenues.compareRevenue
                sum['connections'] += revenues.newConnections
                sum['compareNewConnections'] += revenues.compareNewConnections
                sum['totalConnections'] += revenues.totalConnections
                sum['revenuePerConnection'] += revenues.revenuePerConnection
                sum['compareRevenuePerConnection'] += revenues.compareRevenuePerConnection
            }
            return sum
        },
        currentTarget () {
            if (this.donutData.length === 1) {
                return this.donutData[0].targets
            }
            return this.donutData[this.currentDonutIndex].targets
        },
        currentDonutIndex () {
            return this.periodMapIterator % this.periodMap.length
        }
    },
    data () {
        return {
            subscriber:{
                revenue_trends:'mini-grid-revenue-trends'
            },
            miniGridService: new MiniGridService(),
            revenueService: new RevenueService(),
            revenueTrends: null,
            ticketsData: null,
            enableDataStream: false,
            isLoggerActive: false,
            ModalVisibility: false,
            switching: false,
            watchingMiniGrids: [],
            activeStep: 'firstStep',
            firstStep: false,
            secondStep: false,
            thirdStep: false,
            purchaseCode: '',
            showModal: false,
            currentSelectedTargetCircle: 0,
            displayedTargetPercetinles: [0, 5],
            miniGridData: {},
            miniGridId: null,
            activeDateTab: 'tab-monthly',
            startDate: null,
            endDate: null,
            chartEvents:
                    {
                        select: () => {
                            const table = this.$refs.gChart.chartObject
                            const selection = table.getSelection()
                            const onSelectionMessage = selection.length !== 0 ? 'row was selected' : 'row was diselected'
                            console.log(onSelectionMessage)
                        }
                    },
            chartOptions: {
                isStacked: true,
                chart: {
                    legend: {
                        position: 'top'
                    }
                    ,
                },
                hAxis: {
                    textPosition: 'out',
                    slantedText:
                            true
                },
                vAxis: {
                    //scaleType: 'mirrorLog',
                },
                height: '600',
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
                colors: ['#739e73', '#448aff', '#78002e', '#dce775',],
                height: 220,
            },
            donutChartOptions: { // options for donut chart
                pieHole: 1,
                legend: 'bottom',
                height: 300,
            },
            closedTicketChartData: [],
            openedTicketChartData: [],
            periodMap: [],
            soldEnergy: 0,
            //bach revenue controller instance
            batchRevenues: new BatchRevenue(),
            comparedRevenues: new BatchRevenue(),
            //hold the target(s) data
            targets: [],
            //holds the data for the donut chart
            donutData: [],
            //holds the donut data which should displayed
            selectedDonutData: null,
            //is the counter which is used to determine which data should displayed
            periodMapIterator: 0,
            //selected tartget
            selectedTargetData: [],
            //holds the current transaction data for the given period
            currentTransaction: null,
            //holds the ticket data for selected period
            currentTickets: {},
            disabled: {
                days: [0, 2, 3, 4, 5, 6], // Disable all days except monday
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
            highlighted: {
                base: {},
                compared: {},
                tmpBase: {},
                tmpCompared: {},
            },
            tab: 'monthly',
            expanded: true, // is the determinator whether the period picker should be displayed or not

            revenues: [],
            tmpRevenues: [],
            init: true,
            dateFormat: 'DD MMM YYYY',
            trendChartData:
                    {
                        base: [],
                        compare: [],
                        overview: []
                    },
            labels: ['Base', 'Comparision'],
            chartData: [],
            chartTmpData: [],
            redirectionUrl: '/locations/add-village',
            imperativeItem: 'City',
            redirectDialogActive: false
        }
    },
    methods: {
        closeModal(){
            this.ModalVisibility = false
        },
        closeDatePicker () {
            this.expanded = true
        },
        openDatePicker () {
            this.expanded = false
            this.tab = 'monthly'

        },
        editMiniGrid () {
            this.showModal = true
        },
        async getMiniGridData (miniGridId) {
            try {
                this.miniGridData = await this.miniGridService.getMiniGridData(miniGridId)
                this.enableDataStream = this.miniGridData.data_stream === 1 ? true : false
                this.isLoggerActive = this.enableDataStream
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async getTransactionsOverview () {
            try {
                this.currentTransaction = await this.miniGridService.getTransactionsOverview(this.miniGridId, this.startDate, this.endDate)
            } catch (e) {

                this.alertNotify('error', e.message)
            }

        },
        async getSoldEnergy () {
            try {
                this.soldEnergy = await this.miniGridService.getTSoldEnergy(this.miniGridId, this.startDate, this.endDate)
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async fillTicketChart () {

            let openedTicketChartData = []
            let closedTicketChartData = []
            try {
                this.ticketsData = await this.revenueService.getTicketsData(this.miniGridId)

                openedTicketChartData.push(['Period'])
                closedTicketChartData.push(['Period'])
                for (let category in this.ticketsData.categories) {
                    openedTicketChartData[0].push(this.ticketsData.categories[category].label_name)
                    closedTicketChartData[0].push(this.ticketsData.categories[category].label_name)
                }

                for (let oT in this.ticketsData) {
                    if (oT === 'categories') {
                        continue
                    }
                    let ticketCategoryData = this.ticketsData[oT]

                    let ticketChartDataOpened = [oT]
                    let ticketChartDataClosed = [oT]
                    for (let tD in ticketCategoryData) {
                        let ticketData = ticketCategoryData[tD]
                        ticketChartDataOpened.push(ticketData.opened)
                        ticketChartDataClosed.push(ticketData.closed)
                    }

                    openedTicketChartData.push(ticketChartDataOpened)
                    openedTicketChartData.push(ticketChartDataClosed)
                    closedTicketChartData.push(ticketChartDataClosed)

                }

                this.openedTicketChartData = openedTicketChartData
                this.closedTicketChartData = closedTicketChartData

            } catch (e) {

                this.alertNotify('error', e.message)
            }

        },
        async fillRevenueTrends () {
            this.trendChartData.base = [['Date']]
            this.trendChartData.compare = [['Date']]

            try {
                this.revenueTrends = await this.revenueService.getMiniGridRevenueTrends(this.miniGridId, this.startDate, this.endDate)

                for (let dt in this.revenueTrends) {
                    for (let tariffNames in this.revenueTrends[dt]) {
                        this.trendChartData.base[0].push(tariffNames)
                        this.trendChartData.compare[0].push(tariffNames)
                    }
                    this.trendChartData.base[0].push('Total')
                    this.trendChartData.compare[0].push('Total')
                    if (this.tab !== 'weekly') {
                        break
                    }
                }

                for (let x in this.revenueTrends) {

                    let tmpChartData = [x]
                    let totalRev = 0
                    for (let d in this.revenueTrends[x]) {
                        tmpChartData.push(this.revenueTrends[x][d].revenue)
                        totalRev += this.revenueTrends[x][d].revenue
                    }
                    tmpChartData.push(totalRev)
                    this.trendChartData.base.push(tmpChartData)

                }

                if (Object.keys(this.highlighted.compared).length > 0) { //compare data is also available.
                    let compareData = await this.revenueService.getMiniGridRevenueTrends(this.miniGridId, this.startDate, this.endDate)

                    for (let x in compareData) {
                        let tmpChartData = [x]
                        let totalRev = 0
                        for (let d in compareData[x]) {
                            tmpChartData.push(compareData[x][d].revenue)
                            totalRev += compareData[x][d].revenue
                        }
                        tmpChartData.push(totalRev)
                        this.trendChartData.compare.push(tmpChartData)

                    }

                }
                EventBus.$emit('widgetContentLoaded',this.subscriber.revenue_trends,this.trendChartData.base.length)

            } catch (e) {
                this.redirectDialogActive = true
            }

        },
        async onDataStreamChange (value) {
            try {
                this.switching = true
                let data_stream = this.enableDataStream === true ? 1 : 0
                await this.miniGridService.setMiniGridDataStream(this.miniGridId, data_stream)
                let message = value === true ? 'Data Logger is activated.' : 'Data Logger is deactivated.'
                this.alertNotify('success', message)
                this.isLoggerActive = value
                this.enableDataStream = value
                this.switching = false
            } catch (e) {
                this.switching = false
                this.alertNotify('warn', e.message)
                this.isLoggerActive = !value
                this.enableDataStream = !value
                try {
                    this.watchingMiniGrids = await this.miniGridService.getMiniGridDataStreams(1)
                    this.ModalVisibility = true
                } catch (e) {
                    this.alertNotify('error', e.message)
                }
            }
        },
        //get all data from the beginning of the time
        async fillRevenueTrendsOverview () {
            this.trendChartData.overview = [['Date']]
            try {
                this.revenueTrends = await this.revenueService.getMiniGridRevenueTrends(this.miniGridId, '2018-08-01', this.highlighted.base.to)
                for (let dt in this.revenueTrends) {
                    for (let tariffNames in this.revenueTrends[dt]) {
                        this.trendChartData.overview[0].push(tariffNames)
                    }
                    this.trendChartData.overview[0].push('Total')
                    break
                }
                for (let x in this.revenueTrends) {
                    let tmpChartData = [x]
                    let totalRev = 0
                    for (let d in this.revenueTrends[x]) {
                        tmpChartData.push(this.revenueTrends[x][d].revenue)
                        totalRev += this.revenueTrends[x][d].revenue
                    }
                    tmpChartData.push(totalRev)
                    this.trendChartData.overview.push(tmpChartData)
                }

            } catch (e) {
                this.alertNotify('error', e.message)
            }

        },
        initializePeriods () {
            this.datesInitialized = true
            let startingPeriod = moment().weekday(-6).format('YYYY-MM-DD') // last Monday
            let startingPeriodDateObj = moment(startingPeriod).toDate()
            this.dateSelected(startingPeriodDateObj)
        },
        getBatchData () {
            this.expanded = true
            this.highlighted.base = this.highlighted.tmpBase
            this.highlighted.compared = this.highlighted.tmpCompared
            this.checkBatchData()
            this.getSoldEnergy()
            this.getTransactionsOverview()
            this.fillRevenueTrends()
            this.fillTicketChart()

        },
        checkBatchData () {
            if (this.highlighted.base.to <= this.highlighted.compared.to) {
                this.startDate = this.highlighted.base.from
                this.endDate = this.highlighted.compared.to
            } else {
                this.startDate = this.highlighted.compared.from
                this.endDate = this.highlighted.base.to
            }

        },

        setCircleIndex (index) {
            this.displayedTargetPercetinles[0] = index * 5
            this.displayedTargetPercetinles[1] = this.displayedTargetPercetinles[0] + 5
            this.currentSelectedTargetCircle = index
        },
        getPercentileList () {
            let tmpList = {}
            let counter = 0
            for (let t in this.batchRevenues.revenueList.target.targets) {
                if (counter < this.displayedTargetPercetinles[0]) {
                    counter++
                    continue
                }
                if (counter >= this.displayedTargetPercetinles[1]) {
                    break
                }
                tmpList[t] = this.batchRevenues.revenueList.target.targets[t]
                counter++
            }
            return tmpList
        },
        totalConnectionsByTarget () {
            let totalConnections = 0
            if (this.periodMap.length === 0) return totalConnections
            this.periodMap[0].targets.map(item => {
                totalConnections += item.new_connections
                return item
            })
            return totalConnections
        },
        calculateRevenueTargetPercentage (revenue, targetRevenue) {
            if (revenue === 0 || targetRevenue === 0) return 0
            return Math.round(
                parseInt(revenue) * 100
                    / parseInt(targetRevenue)
                    * 100
            ) / 100
        },
        baseTargetData (connectionType, type) {
            if (this.periodMap.length === 0) return 'Target not available'
            let matchTarget = this.periodMap[0].targets.filter(target => {
                return target.connection === connectionType
            })
            if (matchTarget.length === 0) {
                return 'no data for ' + connectionType
            }
            if (type === 'connections') {
                return matchTarget[0].new_connections
            } else if (type === 'revenue') {
                return matchTarget[0].revenue
            }
        },
        //returns a readable string based on [seconds]
        calcutateDuration (seconds) {
            if (seconds === null) return '0'
            seconds = parseInt(seconds)
            return Math.floor(moment.duration(seconds, 'seconds').asHours()) + ':' + moment.duration(seconds, 'seconds').minutes() + ':' + moment.duration(seconds, 'seconds').seconds()
        },
        //re-formats the date
        formatPeriodText (date) {
            return moment(date, 'Y-W').format('YYYY MMM Do')
        },

        //calculates the reached target percentage
        targetPercentage (actualRevenue, targetRevenue, makeHundred = true) {
            if (typeof (targetRevenue) === 'undefined') return 0
            if (targetRevenue === 0) return 100
            let result = parseInt(parseInt(actualRevenue) * 100 / parseInt(targetRevenue))
            if (Number.isNaN(result))
                return 0
            return makeHundred === true ? (result > 100 ? 100 : result) : result
        },

        revenueData (from, to, batchRevenues) {
            return batchRevenues.revenueForPeriod(
                this.miniGridId,
                'mini-grid',
                from,
                to,
            ).then((data) => {
                return data
            })
        },
        dateSelected (val, base = true) {
            if (this.tab === 'monthly') {
                let date = moment(val)
                if (base) {
                    this.highlighted.tmpBase = {
                        from: date.format('YYYY-MM-01'),
                        to: date.add(1, 'months').date(0).format('YYYY-MM-DD'),
                        includeDisabled: true // Highlight disabled dates
                    }
                    this.highlighted.tmpCompared = {
                        to: date.date(0).format('YYYY-MM-DD'),
                        from: date.format('YYYY-MM-01'),
                        includeDisabled: true // Highlight disabled dates
                    }
                } else {
                    this.highlighted.tmpCompared = {
                        from: date.format('YYYY-MM-01'),
                        to: date.add(1, 'months').date(0).format('YYYY-MM-DD'),
                        includeDisabled: true // Highlight disabled dates
                    }
                }
            } else if (this.tab === 'weekly') {
                let starting = moment(val).format('YYYY-MM-DD')
                let nextSunday = moment(new Date(val.getFullYear(), val.getMonth(), val.getDate() + 6, val.getHours(), val.getMinutes())).format('YYYY-MM-DD')
                if (base) {
                    let date = moment(nextSunday)
                    this.highlighted.tmpBase = {
                        from: starting,
                        to: nextSunday,
                        includeDisabled: true // Highlight disabled dates
                    }
                    this.highlighted.tmpCompared = {
                        from: date.add(-13, 'days').format('YYYY-MM-DD'),
                        to: date.add(6, 'days').format('YYYY-MM-DD'),
                        includeDisabled: true // Highlight disabled dates
                    }
                } else {
                    this.highlighted.tmpCompared = {
                        from: starting,
                        to: nextSunday,
                        includeDisabled: true // Highlight disabled dates
                    }
                }
            } else if (this.tab === 'anual') {
                let date = moment(val)
                if (base) {
                    this.highlighted.tmpBase = {
                        from: date.format('YYYY-01-01'),
                        to: date.format('YYYY-12-31'),
                        includeDisabled: true // Highlight disabled dates
                    }
                    this.highlighted.tmpCompared = {
                        from: date.add(-1, 'years').format('YYYY-01-01'),
                        to: date.format('YYYY-12-31'),
                        includeDisabled: true // Highlight disabled dates
                    }
                } else {
                    this.highlighted.tmpCompared = {
                        from: date.format('YYYY-01-01'),
                        to: date.format('YYYY-12-31'),
                        includeDisabled: true // Highlight disabled dates
                    }
                }
            }
        },

        baseDataAvailable (data) {
            this.batchRevenues = data
            this.initDonutData()
        },
        // get all periods from donut data and maps them into one array
        initDonutData () {
            this.donutData = this.initializeCharts(['Connection Name', 'Revenue'])
        },
        initializeCharts (initValue) {
            let donutData = [initValue]
            //donut chart for given period
            let data = this.batchRevenues.revenueList.revenue
            for (let con in data) {
                let connectionRev = data[con]
                donutData.push([
                    con, parseInt(connectionRev)
                ])
            }
            return donutData

        },
        dateSelectedBase (val) {
            this.dateSelected(val)
        },

        dateSelectedCompared (val) {
            this.dateSelected(val, false)
        },

        formatDates (dateOne, dateTwo) {
            let formattedDates = ''
            if (dateOne) {
                formattedDates = format(dateOne, this.dateFormat)
            }
            if (dateTwo) {
                formattedDates += ' - ' + format(dateTwo, this.dateFormat)
            }
            return formattedDates !== '' ? formattedDates : 'Select Dates'
        },
        refreshChart () {
            this.chartData = this.chartTmpData
        },
        calculateRevenuePercent (current, compared) {
            if (current + compared === 0) return -1
            return Math.round(current * 100 / compared)
        },

        alertNotify (type, message) {
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message,
                speed: 0
            })
        },
    }
}
</script>

<style>
    .asd__inner-wrapper {
        margin-left: 0 !important;
    }

    .date-button {
        overflow: hidden;
        max-width: 100%;
    }

    .base-color, .green {
        color: #739e73
    }

    .compare-color {
        color: #448aff;
    }

    .red {
        color: #ba0f0d;
    }

    .base-color-bg {
        background-color: #739e73 !important;
        color: whitesmoke !important;
    }

    .compare-color-bg {
        background-color: #448aff !important;
        color: whitesmoke !important;
    }

    .progress {
        margin-bottom: 6px !important;
    }

    .close-period {
        transition: all 600ms;
        -webkit-transition: all 0.5s; /* Safari */
        cursor: pointer;
        background-color: #1b1e21;
        border-top-left-radius: 15px;
        border-bottom-left-radius: 15px;
        color: whitesmoke;
        padding: 10px;
        border: 1px solid;
        font-size: 1.2rem;
        position: absolute;
        left: -12%;
        top: 0;
    }

    .close-period:hover {
        left: 0;
        padding-left: 48px;
        margin-left: -50px;
    }

    .close-period > button {
        font-size: 2rem;
        background-color: #7f9919;
        color: whitesmoke;
    }

    .open-period {
        cursor: pointer;
        background-color: #1b1e21;
        border-top-left-radius: 15px;
        border-bottom-left-radius: 15px;
        color: whitesmoke;
        padding: 10px;
        border: 1px solid;
        font-size: 1.2rem;
        position: absolute;
        top: 1rem;
        -webkit-transition: padding-right 0.5s, color 0.5s, background-color 0.5s; /* Safari */
        transition: padding-right 0.5s, color 0.5s, background-color 0.5s;
        right: 0;
    }

    .open-period:hover {
        padding-right: 250px;
        background-color: #c7cfdc;
        border: #cccccc;
        color: #1b1e21;
    }

    div {
        transition: all 600ms;
        -webkit-transition: all 0.5s; /* Safari */
    }

    .period-indicator {
        opacity: 0.8;
        cursor: pointer;
    }

    .pull-left.navigation-padding {
        padding-left: 15px;
    }

    .pull-right.navigation-padding {
        padding-right: 15px;
    }

    .period-navigation {
        background-color: #448aff;
        padding: 5px;
        color: white;
        border: 1px;
        font-weight: 600;
        letter-spacing: 4.2px;
        border-radius: 11px;
        font-size: 1.5rem;
        margin-bottom: 2rem;
    }

    .period-navigation > .arrows {
        position: absolute;
        top: 1.5rem;
    }

    .arrows.right {
        right: 2rem;
    }

    .arrows.left {
        left: 2rem;
    }

    .progress-title {
        font-size: 16px;
        font-weight: 700;
        color: #333;
        margin: 0 0 20px;
    }

    .progress {
        height: 10px;
        background: #333;
        border-radius: 0;
        box-shadow: none;
        margin-bottom: 30px;
        overflow: visible;
    }

    .progress .progress-bar {
        position: relative;
        -webkit-animation: animate-positive 2s;
        animation: animate-positive 2s;
    }

    .progress .progress-bar:after {
        content: "";
        display: inline-block;
        width: 9px;
        background: #fff;
        position: absolute;
        top: -10px;
        bottom: -10px;
        right: -1px;
        z-index: 1;
        transform: rotate(35deg);
    }

    .progress .progress-value {
        display: block;
        font-size: 16px;
        font-weight: 600;
        color: #333;
        position: absolute;
        top: -30px;
        right: -25px;
    }

    @-webkit-keyframes animate-positive {
        0% {
            width: 0;
        }
    }

    @keyframes animate-positive {
        0% {
            width: 0;
        }
    }

    .tooltip {
        display: block !important;
        z-index: 10000;
    }

    .tooltip .tooltip-inner {
        background: black;
        color: white;
        border-radius: 16px;
        padding: 5px 10px 4px;
    }

    .tooltip .tooltip-arrow {
        width: 0;
        height: 0;
        border-style: solid;
        position: absolute;
        margin: 5px;
        border-color: black;
        z-index: 1;
    }

    .tooltip[x-placement^="top"] {
        margin-bottom: 5px;
    }

    .tooltip[x-placement^="top"] .tooltip-arrow {
        border-width: 5px 5px 0 5px;
        border-left-color: transparent !important;
        border-right-color: transparent !important;
        border-bottom-color: transparent !important;
        bottom: -5px;
        left: calc(50% - 5px);
        margin-top: 0;
        margin-bottom: 0;
    }

    .tooltip[x-placement^="bottom"] {
        margin-top: 5px;
    }

    .tooltip[x-placement^="bottom"] .tooltip-arrow {
        border-width: 0 5px 5px 5px;
        border-left-color: transparent !important;
        border-right-color: transparent !important;
        border-top-color: transparent !important;
        top: -5px;
        left: calc(50% - 5px);
        margin-top: 0;
        margin-bottom: 0;
    }

    .tooltip[x-placement^="right"] {
        margin-left: 5px;
    }

    .tooltip[x-placement^="right"] .tooltip-arrow {
        border-width: 5px 5px 5px 0;
        border-left-color: transparent !important;
        border-top-color: transparent !important;
        border-bottom-color: transparent !important;
        left: -5px;
        top: calc(50% - 5px);
        margin-left: 0;
        margin-right: 0;
    }

    .tooltip[x-placement^="left"] {
        margin-right: 5px;
    }

    .tooltip[x-placement^="left"] .tooltip-arrow {
        border-width: 5px 0 5px 5px;
        border-top-color: transparent !important;
        border-right-color: transparent !important;
        border-bottom-color: transparent !important;
        right: -5px;
        top: calc(50% - 5px);
        margin-left: 0;
        margin-right: 0;
    }

    .tooltip.popover .popover-inner {
        background: #f9f9f9;
        color: black;
        padding: 24px;
        border-radius: 5px;
        box-shadow: 0 5px 30px rgba(black, .1);
    }

    .tooltip.popover .popover-arrow {
        border-color: #f9f9f9;
    }

    .tooltip[aria-hidden='true'] {
        visibility: hidden;
        opacity: 0;
        transition: opacity .15s, visibility .15s;
    }

    .tooltip[aria-hidden='false'] {
        visibility: visible;
        opacity: 1;
        transition: opacity .15s;
    }

    .vdp-datepicker__calendar {
        width: 100% !important;
    }

    .dot {
        height: 1.5rem;
        width: 1.5rem;
        border-radius: 50%;
        display: inline-block;
    }

    .dot.revenue {
        background-color: #739e73;
    }

    .dot.new-connection {
        background-color: #c79121;
    }

    .modal-mask {
        position: fixed;
        z-index: 1001;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        display: table;
        transition: opacity .3s ease;
    }

    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }

    .modal-container {
        margin: 0px auto;
        padding: 20px 30px;
        background-color: #fff;
        border-radius: 2px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
        transition: all .3s ease;
        font-family: Helvetica, Arial, sans-serif;
        max-height: 85%;
        overflow-y: scroll;
    }

    @media only screen and (max-width: 1024px) {
        .modal-container {
            width: 99% !important;
        }
    }

    @media only screen and (min-width: 1024px) {
        .modal-container {
            width: 55% !important;
        }
    }

    .modal-header h3 {
        margin-top: 0;
        color: #42b983;
    }

    .modal-body {
        margin: 20px 0;
    }

    .modal-default-button {
        float: right;
    }

    /*
     * The following styles are auto-applied to elements with
     * transition="modal" when their visibility is toggled
     * by Vue.js.
     *
     * You can easily play with the modal transition by editing
     * these styles.
     */
    .modal-enter {
        opacity: 0;
    }

    .modal-leave-active {
        opacity: 0;
    }

    .modal-enter .modal-container,
    .modal-leave-active .modal-container {
        -webkit-transform: scale(1.1);
        transform: scale(1.1);
    }

    .exclamation {
        margin: auto;
        align-items: center;
        display: inline-grid;
        text-align: center;
    }

    .watched-miniGrid-List {
        font-size: 11px;
        width: 15%;
        margin: auto;
        font-weight: bold;
    }

    .exclamation-div {
        margin-top: 2% !important;
    }

    .data-stream-switch {
        margin-left: 3rem !important;
    }

    .vdp-datepicker__calendar .cell.selected {
        background: #90CAF9 !important;
    }
</style>


