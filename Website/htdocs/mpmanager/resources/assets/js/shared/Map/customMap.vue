<template>
    <div>

        <l-map style="height: 630px; " :zoom="zoom" :center="center" :style="cursor">
            <l-tile-layer :url="url" :attribution="attribution"></l-tile-layer>
            <l-control>

                <md-button v-if="drawActivated"
                           class="md-raised" :class="manualDrawing ? 'md-accent':'md-primary'"
                           @click="getLatLng"
                           v-text="manualDrawing ? 'Stop Drawing ' : ' Manual Drawing '">
                    <md-icon>map</md-icon>
                    Manual Drawing
                </md-button>
            </l-control>

            <l-marker v-if="markerActivated" @click="deleteMarker(index)" v-for="(m,index) in marker" :lat-lng="m" :id="index" :key="index">

            </l-marker>
            <l-polygon :latLngs="geoJson" :color="polygon.color" @click="getLatLng"></l-polygon>

        </l-map>

    </div>

</template>

<script>
    import {LMarker, LGeoJson, LMap, LPolygon, LTileLayer, LIcon, LPopup, LControl} from 'vue2-leaflet';
    import widget from "../widget";
    import Vue2LeafletMarkerCluster from 'vue2-leaflet-markercluster';
    import {EventBus} from "../eventbus";
    import {Icon} from 'leaflet';
    delete Icon.Default.prototype._getIconUrl;
    Icon.Default.mergeOptions({
        iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
        iconUrl: require('leaflet/dist/images/marker-icon.png'),
        shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
    });
    export default {
        name: "newMap",
        components: {
            LMap, LPolygon, LMarker, LGeoJson, LTileLayer, widget, LIcon, LPopup, LControl,
            'v-marker-cluster': Vue2LeafletMarkerCluster
        },
        data() {
            return {
                zoom: 8,
                center: [-1.325108, 32.095658],
                url: 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
                attribution: '&copy; MPmanager',
                marker: [],
                geoJson: [],
                cursor: '',
                polygon: {
                    latlngs: this.geoJson,
                    color: 'green'
                },
                markerLatLng: null,
                manualDrawing: false,
            }
        },
        props: {
            geoClusterId: {
                type: Number,
                required: false,
            },
            markerActivated: {
                type: Boolean,
                required: false,
                default: false
            },
            markerLimit: {
                type: Number,
                required: false,
                default: 2
            },
            miniGridId: {
                type: Number,
                required: false,
            },
            userId: {
                type: Number,
                required: false,
            },
            drawActivated: {
                type: Boolean,
                required: false,
                default: false
            }
        },
        methods: {
            deleteMarker(index) {
                if (this.marker.length == null) {
                    alert("There isn't any Marker!!");
                } else {
                    this.marker.splice(index,1);
                    EventBus.$emit("latlng set", this.marker);
                }
            },
            setMarker(markerLatLng) {
                if (this.markerLimit - 1 >= this.marker.length) {
                    if (this.isInsidePolygon(markerLatLng, this.geoJson)) {
                        this.marker.push(markerLatLng);
                        EventBus.$emit("latlng set", this.marker);
                        this.center = this.marker[this.marker.length - 1];
                    } else {
                        alert("Point isn't inside to the Cluster Area!");
                    }
                } else {
                    alert("You can set only " + " " + this.markerLimit + " Marker!");
                }
            },
            isInsidePolygon(marker, poly) {
                let x = marker[0], y = marker[1];
                let inside = false;
                for (let i = 0, j = poly.length - 1; i < poly.length; j = i++) {
                    let xi = poly[i][0], yi = poly[i][1];
                    let xj = poly[j][0], yj = poly[j][1];
                    let intersect = ((yi > y) != (yj > y))
                        && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
                    if (intersect) inside = !inside;
                }
                return inside;
            },
            getGeoJson(clusterId) {
                axios.get(resources.clusters.show_geo + clusterId + '/geo')
                    .then((response) => {
                        if (response == null) {
                            console.log("Cluster doesn't have geoJson")
                        } else {
                            this.geoJson = response.data.data.geo
                            this.center = this.geoJson[0];
                        }
                    })
            },
            stopDrawing() {
            },
            getLatLng(event) {
                this.setMarker([event.latlng.lat, event.latlng.lng]);
            },
        },
        watch: {
            geoClusterId: function () {
                this.getGeoJson(this.geoClusterId);
            },
            markerLatLng: function () {
                if (this.markerLatLng != null) {
                    this.setMarker([this.markerLatLng[0].lat, this.markerLatLng[0].lng])
                }
            }
        },
        mounted() {
            if (this.geoClusterId != null) {
                this.getGeoJson(this.geoClusterId)
            }
        },
        computed: {
            dynamicSize() {
                return [this.iconSize, this.iconSize * 1.15];
            },
        },
        created() {
            EventBus.$on("setMarker", (data) => {
                this.markerLatLng = data;
            })
        }
    }
</script>

<style scoped>
</style>
