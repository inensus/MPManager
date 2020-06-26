<template>

    <widget :id="'revenue-types'" :title="'Revenue Analysis'" v-if="batchRevenues.revenueList">

        <md-table>
            <md-table-row>
                <md-table-head>Type</md-table-head>
                <md-table-head colspan="2">Connections</md-table-head>
                <md-table-head :colspan="compareAnalysisAvailable ? 3 : 2">Revenues</md-table-head>
                <md-table-head class="md-xsmall-hide">Avg Revenue Per Connection</md-table-head>
            </md-table-row>


            <md-table-row>
                <md-table-head class="md-xsmall-hide">Status now</md-table-head>
                <md-table-head class="md-xsmall-hide">New Connections/Target</md-table-head>
                <md-table-head>
                    This Period <small>{{base.from +'-'+ base.to}}</small>
                </md-table-head>
                <md-table-head v-if="compareAnalysisAvailable">
                    Compared Period <small>{{compared.from +'-'+ compared.to}}</small>
                </md-table-head>
                <md-table-head>Target</md-table-head>
                <md-table-head class="md-xsmall-hide">This period</md-table-head>
                <md-table-head class="md-xsmall-hide" v-if="compareAnalysisAvailable">Compared Period</md-table-head>
            </md-table-row>

            <md-table-row v-if="batchRevenues.revenueList !== null"
                          v-for="(revenue, index) in batchRevenues.revenueList.target.targets"
                          :key="index">
                <md-table-cell>{{index}}</md-table-cell>
                <md-table-cell>{{batchRevenues.revenueList.total_connections[index]}}</md-table-cell>
                <md-table-cell>
                    {{batchRevenues.revenueList.new_connections[index]}} / {{revenue.new_connections}}
                </md-table-cell>
                <md-table-cell v-if="batchRevenues.revenueList.revenue">
                    {{readable(batchRevenues.revenueList.revenue[index])}}
                </md-table-cell>
                <md-table-cell v-if="comparedRevenues.revenueList!== null && 'revenue' in comparedRevenues.revenueList">
                    {{readable(comparedRevenues.revenueList.revenue[index])}}
                </md-table-cell>
                <md-table-cell>{{readable(revenue.revenue)}}</md-table-cell>
                <md-table-cell v-if="batchRevenues.revenueList !== null">
                    {{readable(batchRevenues.revenueList.averages[index])}}
                </md-table-cell>
                <md-table-cell v-else>-</md-table-cell>
                <md-table-cell v-if="compareAnalysisAvailable">
                    {{readable(comparedRevenues.revenueList.averages[index])}}
                </md-table-cell>
            </md-table-row>

            <md-table-row v-else>
                <md-table-cell colspan="4">No Data available</md-table-cell>
            </md-table-row>

            <md-table-row style="margin-top: 2rem;">
                <md-table-cell>Total</md-table-cell>
                <md-table-cell>{{totalRevenues.totalConnections}}</md-table-cell>
                <md-table-cell>{{totalRevenues.newConnections}} / {{totalRevenues.targetConnections}}</md-table-cell>
                <md-table-cell>{{readable(totalRevenues.revenue)}}</md-table-cell>
                <md-table-cell v-if="compareAnalysisAvailable">{{readable(totalRevenues.comparedRevenue)}}
                </md-table-cell>
                <md-table-cell>{{readable(totalRevenues.totalTargetRevenue)}}</md-table-cell>
                <md-table-cell>{{readable(totalRevenues.totalAverage)}}</md-table-cell>
                <md-table-cell v-if="compareAnalysisAvailable">{{(totalRevenues.comparedTotalAverage)}}</md-table-cell>
            </md-table-row>


        </md-table>


    </widget>
</template>

<script>
    import Widget from '../../shared/widget'
    import { BatchRevenue } from '../../classes/revenue/batch'
    import moment from 'moment'
    import TableList from '../../shared/TableList'
    import { currency } from '../../mixins/currency'

    export default {
        name: 'TargetList',
        components: { TableList, Widget },
        mixins: [currency],
        props: {
            targetId: {
                type: String,
                required: true,
            },
            targetType: {
                type: String,
                required: true,
            },
            base: {
                type: Object,
                required: true,
            },
            compared: {
                type: Object,
                default: () => {return {}},
            },
        },
        data () {
            return {
                //bach revenue controller instance
                batchRevenues: new BatchRevenue(),
                comparedRevenues: new BatchRevenue(),
                datesSet: 0, //when 2 fire batchData
                totalRevenues: {
                    comparedRevenue: 0,
                    totalAverage: 0,

                    revenue: 0,
                    compareRevenue: 0,
                    connections: 0,
                    newConnections: 0,
                    compareNewConnections: 0,
                    totalConnections: 0,
                    targetConnections: 0,
                    revenuePerConnection: 0,
                    compareRevenuePerConnection: 0,
                    totalTargetRevenue: 0,
                }
            }
        },
        computed: {
            compareAnalysisAvailable () {
                return this.comparedRevenues.revenueList !== null
            },
        },
        watch: {
            base: function (newVal, oldVal) {
                this.dataSet = 1
                setTimeout(() => {
                    if (this.dataSet === 1) {
                        this.getBatchData()
                    }
                }, 1500)
            },
            compared: function (newVal, oldVal) {
                this.dataSet = 2
                setTimeout(() => {
                    if (this.dataSet === 2) {
                        this.getBatchData()
                    }
                }, 1500)
            },
        },
        methods: {
            getBatchData () {
                this.expanded = true
                this.targets = []
                //if(this.highlighted.from)
                if (Object.keys(this.base).length > 0) { //base data is there
                    this.revenueData(
                        moment(this.base.from).format('YYYY-MM-DD'),
                        moment(this.base.to).format('YYYY-MM-DD'),
                        this.batchRevenues
                    )
                        .then((response) => {
                            this.batchRevenues.revenueList = response
                            this.batchRevenues.revenueList.averages = this.calculateAverages(this.batchRevenues.revenueList)
                            this.$emit('baseDataAvailable', this.batchRevenues)
                            if (Object.keys(this.compared).length === 0) this.totals()

                            //this.donutData = this.initializeCharts(['Connection Name', 'Revenue'])
                            if (Object.keys(this.compared).length > 0) { //compare data is also available.
                                this.revenueData(
                                    moment(this.compared.from).format('YYYY-MM-DD'),
                                    moment(this.compared.to).format('YYYY-MM-DD'),
                                    this.comparedRevenues
                                )
                                    .then((response) => {
                                        this.comparedRevenues.revenueList = response
                                        this.comparedRevenues.revenueList.averages = this.calculateAverages(this.comparedRevenues.revenueList)
                                        this.totals()
                                    })
                            }
                        })
                }
            },
            calculateAverages (list) {
                let data = {}
                for (let connection in list.target.targets) {
                    let result = '-'
                    if (list.revenue[connection] > 0) {
                        result = parseInt(list.revenue[connection]) / list.total_connections[connection]
                    }
                    data[connection] = result
                }
                return data
            },
            revenueData (from, to, batchRevenues) {
                return batchRevenues.revenueForPeriod(
                    this.targetId,
                    this.targetType,
                    from,
                    to,
                ).then((data) => {
                    return data
                })
            },
            totals () {
                let newConnections = 0
                let totalConnections = 0
                let totalRevenue = 0
                let totalTargetNewConnections = 0
                let totalTargetRevenue = 0

                let comparedRevenue = 0
                let totalAverage = 0
                let affectedAverageConnections = 0

                let comparedTotalAverage = 0
                let comparedAffectedAverageConnections = 0

                for (let connection in this.batchRevenues.revenueList.target.targets) {
                    newConnections += this.batchRevenues.revenueList.new_connections[connection]
                    totalTargetNewConnections += this.batchRevenues.revenueList.target.targets[connection].new_connections === '-' ? 0 : this.batchRevenues.revenueList.target.targets[connection].new_connections

                    totalConnections += this.batchRevenues.revenueList.total_connections[connection] === '-' ? 0 : this.batchRevenues.revenueList.total_connections[connection]
                    totalRevenue += parseInt(this.batchRevenues.revenueList.revenue[connection])
                    totalTargetRevenue += this.batchRevenues.revenueList.target.targets[connection].revenue === '-' ? 0 : this.batchRevenues.revenueList.target.targets[connection].revenue

                    if (this.batchRevenues.revenueList.total_connections[connection] > 0
                        && this.batchRevenues.revenueList.averages[connection] !== '-') {
                        totalAverage += this.batchRevenues.revenueList.averages[connection]
                        affectedAverageConnections++
                    }

                }
                if (this.comparedRevenues.revenueList !== null
                ) {
                    for (let connection in this.comparedRevenues.revenueList.target.targets) {
                        if (this.comparedRevenues.revenueList !== 'null' && 'revenue' in this.comparedRevenues.revenueList)
                            comparedRevenue += parseInt(this.comparedRevenues.revenueList.revenue[connection])

                        if (this.comparedRevenues.revenueList.total_connections[connection] > 0
                            && this.comparedRevenues.revenueList.averages[connection] !== '-') {
                            comparedTotalAverage += this.comparedRevenues.revenueList.averages[connection]
                            comparedAffectedAverageConnections++
                        }

                    }
                }
                this.totalRevenues.totalConnections = totalConnections

                this.totalRevenues.connections = totalConnections
                this.totalRevenues.newConnections = newConnections
                this.totalRevenues.targetConnections = totalTargetNewConnections

                this.totalRevenues.revenue = totalRevenue
                this.totalRevenues.totalTargetRevenue = totalTargetRevenue
                this.totalRevenues.comparedRevenue = comparedRevenue
                this.totalRevenues.totalAverage = totalAverage / (affectedAverageConnections > 0 ? affectedAverageConnections : 1)
                this.totalRevenues.comparedTotalAverage = comparedTotalAverage / (comparedAffectedAverageConnections > 0 ? comparedAffectedAverageConnections : 1)

                this.$emit('complete', totalConnections)
            }
        }
    }
</script>

<style scoped>

</style>
