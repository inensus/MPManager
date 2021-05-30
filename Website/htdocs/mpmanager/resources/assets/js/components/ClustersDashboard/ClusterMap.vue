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
            geoData: null,
            center: [
                this.$store.getters['settings/getMapSettings'].latitude,
                this.$store.getters['settings/getMapSettings'].longitude],
            clusterGeo: {},
        }
    },
    mounted () {
        this.getGeoData()
    },
    methods: {
        async getGeoData () {
            let clusters = await this.clusterService.getClusters()
            let geoData = []
            clusters.forEach((e) => {
                if(e.geo_data !== null ){
                    this.clusterGeo = e.geo_data
                    this.clusterGeo.clusterId = e.id
                    geoData.push(this.clusterGeo)
                }
            })
            this.geoData = geoData
        },
    },
}
</script>
