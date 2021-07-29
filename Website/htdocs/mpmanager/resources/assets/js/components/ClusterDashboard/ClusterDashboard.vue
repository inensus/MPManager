<template>
    <div>
        <md-card style="margin-bottom: 3rem">
            <md-card-header>
                {{ $tc('words.cluster') }} : <span style="font-size: 1.3rem; font-weight: bold"
                                v-if="clusterData"> {{clusterData.name}}</span>
            </md-card-header>
        </md-card>

        <div class="md-layout md-gutter md-size-100">
            <div v-if="clusterData" class="md-layout-item
       md-size-100">
                <box-group
                    :cluster="clusterData"
                />

            </div>
            <div v-if="clusterId" class="md-layout-item
       md-size-100">
                <financial-overview
                    :cluster-id="clusterId"
                    @complete="addRevenue"
                />

            </div>
            <div v-if="clusterData" class="md-layout-item
       md-size-100" style="margin-top: 2vh;">
                <md-card>
                    <md-card-content>
                        <Map
                                :geoData="geoData"
                                :markerLocations="constantLocations"
                                :markerUrl="miniGridIcon"
                                :center="center"
                                :markingInfos="markingInfos"
                                :parentName="'Top-MiniGrid'"
                                :zoom="7"
                        />
                    </md-card-content>

                </md-card>


            </div>
            <div v-if="clusterData && 1===-1" class="md-layout-item
       md-size-100">
                <target-list
                        :target-id="clusterId"
                        target-type="cluster"
                        :base="base"
                        :compared="compared"
                        @complete="addConnections"
                />
            </div>
            <div v-if="clusterData" class="md-layout-item
       md-size-100">
                <revenue-trends :cluster-id="clusterId"/>

            </div>
        </div>

    </div>
</template>

<script>

import '../../shared/TableList'
import Map from '../../shared/Map'
import BoxGroup from './BoxGroup'
import FinancialOverview from './FinancialOverview'
import RevenueTrends from './RevenueTrends'
import TargetList from '../MiniGrid/TargetList'
import { ClusterService } from '../../services/ClusterService'
import { MappingService } from '../../services/MappingService'
import miniGridIcon from '../../../icons/miniGrid.png'
import moment from 'moment'
import { MeterService } from '../../services/MeterService'

export default {
    name: 'ClusterList',

    components: {
        TargetList,
        RevenueTrends,
        FinancialOverview,
        BoxGroup,
        Map
    },
    data () {
        return {
            clusterService: new ClusterService(),
            mappingService: new MappingService(),
            miniGridIcon: miniGridIcon,
            clusterId: null,
            clusterData: null,
            clusters: null,
            geoData: null,
            constantLocations: [],
            meterService: new MeterService(),
            markingInfos: [],
            center: [this.$store.getters['settings/getMapSettings'].latitude,this.$store.getters['settings/getMapSettings'].longitude],
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
    created () {
        this.clusterId = this.$route.params.id
    },

    mounted () {

        this.initDates()
        this.getMiniGridList()

    },

    methods: {
        initDates () {
            this.base = {
                from: moment().startOf('month').format('YYYY-MM-DD hh:mm'),
                to: moment().endOf('month').format('YYYY-MM-DD hh:mm')
            }
            this.compared = {
                from: moment().add(1, 'months').startOf('month').format('YYYY-MM-DD hh:mm'),
                to: moment().add(1, 'months').endOf('month').format('YYYY-MM-DD hh:mm')
            }
        },
        async getMiniGridList () {
            this.clusterData = await this.clusterService.getDetails(this.clusterId)
            let clusterGeoData = await this.clusterService.getClusterGeoLocation(this.clusterId)
            this.center = [clusterGeoData.lat, clusterGeoData.lon]
            this.geoData = this.mappingService.focusLocation(clusterGeoData)
            this.boxData['mini_grids'] = this.clusterData.mini_grids.length
            for (let i in this.clusterData.mini_grids) {
                let miniGrids = this.clusterData.mini_grids
                let points = miniGrids[i].location.points.split(',')
                let lat = points[0]
                let lon = points[1]

                let markingInfo = this.mappingService.createMarkingInformation(
                    miniGrids[i].id,
                    miniGrids[i].name,
                    null,
                    lat,
                    lon,
                    miniGrids[i].data_stream,
                    miniGrids[i].cluster_meta_data
                )

                this.markingInfos.push(markingInfo)
                this.constantLocations.push([lat, lon])

            }
        },

        addRevenue (data) {
            this.boxData['revenue'] = {
                'total': data['sum'],
                'period': data['period']
            }
        },
        addConnections (data) {
            this.boxData['people'] = data
            this.boxData['meters'] = data
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

<style>


</style>

