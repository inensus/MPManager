<template>

    <widget
        title="Cluster Map"
        id="cluster-map">
        <div style="padding: 10px">
            <div id="map" class="map" style="height: 500px;"></div>
        </div>

    </widget>
</template>

<script>
    import { LMap, LTileLayer, LMarker, LGeoJson, LPolygon } from 'vue2-leaflet'
    import { layerGroup } from 'leaflet'

    import Widget from '../../shared/widget'

    export default {
        name: 'ClusterMap',
        components: {
            Widget,
            LMap,
            LTileLayer,
            LGeoJson,
            LMarker
        },
        data () {
            return {
                loading: false,
                show: true,
                enableTooltip: true,
                zoom: 6,
                center: [48, -1.219482],
                geojson: null,
                fillColor: '#e4ce7f',
                url: 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
                map: null,
                miniGrids: null,
                clusterLayer: null,
            }
        },
        computed: {
            options () {
                return {
                    onEachFeature: this.onEachFeatureFunction
                }
            },
            onEachFeatureFunction () {
                if (!this.enableTooltip) {
                    return () => {}
                }
                return (feature, layer) => {
                    layer.bindTooltip('<div>code:' + feature.properties.code + '</div><div>nom: ' + feature.properties.nom + '</div>', {
                        permanent: false,
                        sticky: true
                    })
                }
            },

        },
        mounted () {
            this.initMap()
            this.getMiniGrids()

        },
        methods: {
            initMap () {
                //create map
                this.map = L.map('map')
                    .setView([38.63, -90.23], 6)
                //set tile
                this.tileLayer = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                        maxZoom: 18,
                        attribution: ' <span style="cursor:pointer">&copy; MpManager</span>',
                    }
                )

                this.tileLayer.addTo(this.map)
                this.getLocation()

            },
            getMiniGrids () {
                axios.get(resources.miniGrids.list).then((response) => {
                    this.miniGrids = response.data
                })
            },

            strToHex (str) {
                str += 'z4795dfjkldfnjk4lnjkl'
                let hash = 0
                for (let i = 0; i < str.length; i++) {
                    hash = str.charCodeAt(i) + ((hash << 5) - hash)
                }
                let colour = '#'
                for (let i = 0; i < 3; i++) {
                    let value = (hash >> (i * 8)) & 0xFF
                    colour += ('00' + value.toString(16)).substr(-2)
                }
                return colour
            },
            getLocation () {
                let clusterLayer = layerGroup().addTo(this.map)
                axios.get(resources.clusters.geo)
                    .then((response) => {
                        let data = response.data.data
                        for (let i = 0; i < data.length; i++) {

                            let cluster = data[i]
                            this.map.setView(cluster.geo[0][0], 8)
                            let polygon = new L.polygon(cluster.geo, {
                                color: this.strToHex(cluster.name)
                            })
                            let parent = this
                            polygon.on('click', function (e) {
                                parent.clusterClick(cluster)
                            })
                            polygon.bindTooltip(cluster.name)
                            polygon.addTo(clusterLayer)
                            polygon.bringToFront()

                        }

                    })

            },
            clusterClick (cluster) {
                this.$router.push({ path: '/clusters/' + cluster.id })
            },
        },

    }
</script>
