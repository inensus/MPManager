<template>
    <div>
        <password-protection></password-protection>
        <widget
            title="Add New Mini-Grid"
            color="green">
            <md-card>
                <md-card-content>
                    <div class="md-layout md-gutter md-size-100">
                        <div class="md-layout-item md-size-70">
                            <md-field :class="{'md-invalid': errors.has('miniGridName')}">
                                <label for="miniGrid_name">MiniGrid Name</label>
                                <md-input
                                    id="miniGridName"
                                    name="miniGridName"
                                    v-model="miniGridName"
                                    v-validate="'required|min:3'"></md-input>
                                <span class="md-error">{{errors.first('miniGridName')}}</span>
                            </md-field>

                        </div>
                        <div class="md-layout-item md-size-30">
                            <md-field :class="{'md-invalid': errors.has('clusterName')}">
                                <label for="clusterName">Cluster</label>
                                <md-select
                                    v-model="selectedClusterId"
                                    name="clusterName"
                                    id="clusterName"
                                    v-validate="'required'"
                                    @md-selected="selectCluster">
                                    <md-option v-for="(cluster,index) in clusters" :value="cluster.id"
                                               :key="cluster.id">
                                        {{cluster.name}}
                                    </md-option>
                                </md-select>
                                <span class="md-error">{{errors.first('clusterName')}}</span>
                            </md-field>
                        </div>
                    </div>
                    <div class="md-layout md-gutter md-size-100">
                        <div class="md-layout md-gutter md-size-60" style="padding-left: 1.5rem !important;">
                            <form class="md-layout md-gutter" @submit.prevent="validatePoints"
                                  data-vv-scope="Points-Form" style="padding-left: 1.5rem !important;"
                            >

                                <div class="md-layout-item md-size-30">
                                    <md-field :class="{'md-invalid': errors.has('Points-Form.latitude')}">
                                        <label for="latitude">Latitude</label>
                                        <md-input
                                            id="latitude"
                                            name="latitude"

                                            maxlength="8"
                                            step="any"
                                            v-model="miniGridLatLng.lat"
                                            v-validate="'required|decimal:5|max:8'"></md-input>
                                        <span class="md-error">{{errors.first('Points-Form.latitude')}}</span>
                                    </md-field>
                                </div>
                                <div class="md-layout-item md-size-30">
                                    <md-field :class="{'md-invalid': errors.has('Points-Form.longitude')}">
                                        <label for="longitude">Longitude</label>
                                        <md-input
                                            id="longitude"
                                            name="longitude"

                                            step="any"
                                            maxlength="8"
                                            v-model="miniGridLatLng.lon"
                                            v-validate="'required|decimal:5|max:8'"></md-input>
                                        <span class="md-error">{{errors.first('Points-Form.longitude')}}</span>
                                    </md-field>
                                </div>
                                <div class="md-layout-item md-size-40">
                                    <md-button type="submit" class="md-primary set-button">Set Points</md-button>

                                </div>
                            </form>

                        </div>

                        <div class="md-layout-item md-size-40">
                            <md-button class="md-primary save-button" @click="saveMiniGrid()">Save</md-button>

                        </div>
                    </div>
                    <div class="md-layout-item md-large-size-100 md-medium-size-100 md-small-size-100 map-area">
                        <Map
                            :geoData="geoData"
                            :clusterName="clusterName"
                            :marker="true"
                            :markerCount="1"
                            :markerLocations="markerLocations"
                            :remove="true"
                            :center="center"
                            :markerUrl="markerUrl"
                        />
                    </div>
                </md-card-content>
                <md-progress-bar md-mode="indeterminate" class="md-progress-bar" v-if="sending"/>

            </md-card>
        </widget>
    </div>
</template>

<script>
    import marker from 'leaflet/dist/images/marker-icon.png'

    import Widget from '../../shared/widget'
    import PasswordProtection from '../PasswordProtection'
    import { ClusterService } from '../../services/ClusterService'
    import Map from '../../shared/Map'
    import { MappingService } from '../../services/MappingService'
    import { EventBus } from '../../shared/eventbus'
    import { MiniGridService } from '../../services/MiniGridService'

    export default {
        name: 'AddMiniGrid',
        components: {
            Widget,
            PasswordProtection,
            Map,
        },
        data () {
            return {
                clusterService: new ClusterService(),
                mappingService: new MappingService(),
                miniGridService: new MiniGridService(),
                clusterId: null,
                geoData: null,
                center: [0, 0],
                marker: true,
                markerUrl: marker,
                miniGridName: '',
                miniGridLatLng: {
                    lat: null,
                    lon: null
                },
                sending: false,
                clusterName: '',
                clusters: [],
                selectedClusterId: '',
                miniGridId: null,
                markerLocations: []

            }
        },
        mounted () {
            this.getClusters()
            EventBus.$on('getDrawedMarker', (geoDataItem) => {
                this.miniGridLatLng.lat = Number(geoDataItem.geojson.coordinates.lat.toFixed(5))
                this.miniGridLatLng.lon = Number(geoDataItem.geojson.coordinates.lng.toFixed(5))
            })
            EventBus.$on('markerError', (message) => {
                this.$swal({
                    type: 'warn',

                    text: message,
                })

            })
        },
        methods: {
            async getClusters () {
                try {
                    this.clusters = await this.clusterService.getClusters()
                    if (this.clusters.length > 0) {
                        this.selectedClusterId = this.clusters[this.clusters.length - 1].id
                    }
                } catch (e) {
                    this.alertNotify('error', e.message)
                }
            },
            async getGeoData (clusterId) {
                try {
                    this.clusterId = clusterId
                    let clusterGeoData = await this.clusterService.getClusterGeoLocation(clusterId)

                    this.center = [clusterGeoData.lat, clusterGeoData.lon]
                    this.geoData = this.mappingService.focusLocation(clusterGeoData)
                } catch (e) {
                    this.alertNotify('error', e.message)
                }
            },
            async validatePoints () {
                let validator = await this.$validator.validateAll('Points-Form')
                if (validator) {
                    this.setMarker()
                }

            },
            async saveMiniGrid () {
                this.sending = true
                let validator = await this.$validator.validateAll()
                if (validator) {
                    try {
                        this.sending = false

                        await this.miniGridService.createMiniGrid(this.miniGridName, this.clusterId, this.miniGridLatLng)
                        this.alertNotify('success', 'The Mini-Grid you add is stored successfully.')

                    } catch (e) {
                        this.alertNotify('error', e.message)
                    }
                }
            },
            selectCluster (cluster) {
                this.miniGridLatLng = {
                    lat: null,
                    lon: null
                }
                this.miniGridName = ''
                this.getGeoData(cluster)
            },
            setMarker () {
                this.markerLocations = []
                this.markerLocations.push([this.miniGridLatLng.lat, this.miniGridLatLng.lon])
            },
            alertNotify (type, message) {
                this.$notify({
                    group: 'notify',
                    type: type,
                    title: type + ' !',
                    text: message
                })
            },
        }
    }
</script>

<style scoped>
    .md-progress-bar {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
    }

    .save-button {
        background-color: #325932 !important;
        color: #fefefe !important;
        top: 0.5rem;
        float: right;
    }

    .set-button {
        background-color: #448aff !important;
        color: #fefefe !important;
        top: 0.5rem;
        float: left;
    }

    .map-area {
        z-index: 1 !important
    }

</style>
