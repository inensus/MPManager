<template>
    <div>
        <div class="md-layout md-gutter">
            <div class="md-layout-item md-medium-size-100  md-xsmall-size-100 md-size-100">
                <md-card style="margin-bottom: 3rem">
                    <md-card-header>
                        Cluster : <span style="font-size: 1.3rem; font-weight: bold"
                                        v-if="clusterData"> {{clusterData.name}}</span>
                    </md-card-header>
                </md-card>
            </div>
            <div class="md-layout-item md-medium-size-100  md-xsmall-size-100 md-size-100" v-if="clusterData">
                <box-group
                    :mini-grids="clusterData"
                    :box-data="boxData"
                />
            </div>

            <div class="md-layout-item md-medium-size-100  md-xsmall-size-100 md-size-100" v-if="clusterId">
                <financial-overview
                    :cluster-id="this.clusterId"
                    @complete="addRevenue"
                />
            </div>
            <div class="md-layout-item md-medium-size-100  md-xsmall-size-100 md-size-100" v-if="clusterData">
                <cluster-map :miniGrids="clusterData.mini_grids"/>
            </div>
            <div class="md-layout-item md-medium-size-100  md-xsmall-size-100 md-size-100">
                <target-list
                    :target-id="clusterId"
                    target-type="cluster"
                    :base="base"
                    :compared="compared"
                    @complete="addConnections"
                />
            </div>
            <div class="md-layout-item md-medium-size-100  md-xsmall-size-100 md-size-100" v-if="clusterData">
                <revenue-trends :cluster-id="clusterId"/>
            </div>

        </div>
    </div>
</template>

<script>
    import {EventBus} from '../../shared/eventbus'
    import '../../shared/TableList'

    import Widget from '../../shared/widget'
    import BoxGroup from './BoxGroup'
    import FinancialOverview from './FinancialOverview'
    import {resources} from '../../resources'
    import ClusterMap from './ClusterMap'
    import RevenueAnalysis from './RevenueAnalysis'
    import RevenueTrends from './RevenueTrends'
    import TargetList from '../VillageDashboard/TargetList'

    export default {
        name: 'ClusterList',

        components: {TargetList, RevenueTrends, RevenueAnalysis, ClusterMap, FinancialOverview, BoxGroup, Widget},

        created() {
            this.clusterId = this.$route.params.id
        },

        mounted() {
            EventBus.$emit('bread', this.bcd)
            this.initDates()
            this.getMiniGridList()

        },

        data() {
            return {
                clusterId: null, // the cluster id which passed via url
                clusterData: null, //contains simple cluster data like name and assigned minigrids
                bcd: {
                    'Home': {
                        'href': '/'
                    },
                    'Clusters': {
                        'href': null,
                    },
                },
                clusters: null,
                base: {},
                compared: {},
                boxData: {
                    'revenue': {
                        'period': '-',
                        'total': '-',
                    },
                    'people': '-',
                    'meters': '-',
                },

            }
        },
        methods: {
            initDates() {
                this.base = {
                    from: moment().startOf('month').format('YYYY-MM-DD hh:mm'),
                    to: moment().endOf('month').format('YYYY-MM-DD hh:mm')
                }
                this.compared = {
                    from: moment().add(1, 'months').startOf('month').format('YYYY-MM-DD hh:mm'),
                    to: moment().add(1, 'months').endOf('month').format('YYYY-MM-DD hh:mm')
                }
            },

            //gets the cluster list form the api and assigns it to clusterData
            getMiniGridList() {
                axios.get(resources.clusters.detail + this.clusterId).then((response) => {
                    this.clusterData = response.data.data
                    this.boxData['mini_grids'] = this.clusterData.mini_grids.length
                })
            },

            addRevenue(data) {
                this.boxData['revenue'] = {
                    'total': data['sum'],
                    'period': data['period']
                }
            },
            addConnections(data) {
                this.boxData['people'] = data
                this.boxData['meters'] = data
            }
        },

    }
</script>

<style>

</style>
