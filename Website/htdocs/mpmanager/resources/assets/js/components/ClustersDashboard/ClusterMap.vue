<template>

    <widget
        :title="$tc('phrases.clusterMap')"
        id="cluster-map">
        <Map
            :geoData="geoData"
            :center="center"
            :parentName="'Top'">

        </Map>


    </widget>
</template>

<script>

import Widget from '../../shared/widget'
import { ClusterService } from '../../services/ClusterService'
import { MappingService } from '../../services/MappingService'
import { MiniGridService } from '../../services/MiniGridService'
import Map from '../../shared/Map'

export default {
    name: 'ClusterMap',
    components: {
        Map,
        Widget,
    },
    data () {
        return {
            clusterService: new ClusterService(),
            mappingService: new MappingService(),
            miniGridService: new MiniGridService(),
            loading: false,
            show: true,
            geoData: null,
            center: this.$store.state.mapSettings.center,
            miniGrids: null,
            clusterLayer: null,
            clusterGeo: {}
        }
    },
    computed: {},
    mounted () {
        this.getGeoData()
        this.getMiniGrids()

    },
    methods: {
        async getGeoData () {
            this.clusterGeo = {}
            let clusters = await this.clusterService.getClusters()
            let geoData = []
            clusters.forEach((e) => {
                this.clusterGeo = e.geo[0]
                this.clusterGeo.clusterId = e.id
                geoData.push(this.clusterGeo)

            })
            this.geoData = geoData

        },

        async getMiniGrids () {
            this.miniGrids = await this.miniGridService.getMiniGrids()
        },

    },

}
</script>

