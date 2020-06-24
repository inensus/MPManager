<template>

    <widget
        title="Cluster Map"
        id="cluster-map">
        <Map
            :geoData="geoData"
            :center="center"
        />

    </widget>
</template>

<script>
    import { LMap, LTileLayer, LMarker, LGeoJson, LPolygon } from 'vue2-leaflet'
    import { layerGroup } from 'leaflet'

    import Widget from '../../shared/widget'
    import { ClusterService } from '../../services/ClusterService'
    import { MappingService } from '../../services/MappingService'
    import Map from '../../shared/Map'
    import { MiniGridService } from '../../services/MiniGridService'

    export default {
        name: 'ClusterMap',
        components: {
            Widget,
            Map,
        },
        data () {
            return {
                clusterService: new ClusterService(),
                mappingService: new MappingService(),
                miniGridService: new MiniGridService(),
                loading: false,
                show: true,
                geoData: null,
                center: this.appConfig.mapStartingPoint,
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

