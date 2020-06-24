<template>
    <div>
        <password-protection></password-protection>
        <widget title="Add New Village" color="green">

            <md-card class="md-layout-item md-size-100">

                <md-card-content>

                    <div class="md-layout md-gutter md-size-100">
                        <div class="md-layout-item md-size-70">
                            <md-field :class="{'md-invalid': errors.has('cityName')}">
                                <label for="city_name"> Village Name </label>
                                <md-input
                                    id="cityName"
                                    name="cityName"
                                    v-model="cityName"
                                    v-validate="'required|min:3'"/>
                                <span class="md-error">{{ errors.first('cityName') }}</span>
                            </md-field>
                        </div>
                        <div class="md-layout-item md-size-30">
                            <md-field :class="{'md-invalid': errors.has('miniGridName')}">
                                <label>Mini Grid</label>
                                <md-select
                                    v-model="selectedMiniGridId"
                                    name="miniGridName"
                                    id="miniGridName"
                                    v-validate="'required'"
                                    @md-selected="selectMiniGrid"
                                >
                                    <md-option v-for="(mg,index) in miniGrids" :value="mg.id"
                                               :key="mg.id">
                                        {{mg.name}}
                                    </md-option>
                                </md-select>
                                <span class="md-error">{{ errors.first('city_miniGrid') }}</span>

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
                                            v-model="cityLatLng.lat"
                                            step="any"
                                            maxlength="8"
                                            v-validate="'required|decimal:5|max:8'"/>
                                        <span class="md-error">{{ errors.first('Points-Form.latitude') }}</span>

                                    </md-field>
                                </div>
                                <div class="md-layout-item md-size-30">
                                    <md-field :class="{'md-invalid': errors.has('Points-Form.longitude')}">
                                        <label for="longitude">Longitude</label>
                                        <md-input
                                            id="longitude"
                                            name="longitude"
                                            v-model="cityLatLng.lon"
                                            step="any"
                                            maxlength="8"
                                            v-validate="'required|decimal:5|max:8'"/>
                                        <span class="md-error">{{ errors.first('Points-Form.longitude') }}</span>
                                    </md-field>
                                </div>
                                <div class="md-layout-item md-size-40">
                                    <md-button type="submit" class="md-primary set-button">Set Points</md-button>

                                </div>
                            </form>

                        </div>

                        <div class="md-layout-item md-size-40">
                            <md-button class="md-primary save-button" @click="getValidateVillage()">Save</md-button>

                        </div>
                    </div>

                    <div class="md-layout-item md-large-size-100 md-medium-size-100 md-small-size-100 map-area">
                        <Map
                            :geoData="geoData"
                            :marker="true"
                            :markerCount="1"
                            :remove="true"
                            :center="center"
                            :markerLocations="markerLocations"
                            :constantLocations="constantLocations"
                            :constantMarkerUrl="miniGridIcon"
                            :markerUrl="villageIcon"
                            :markingInfos="markingInfos"
                        />
                    </div>


                </md-card-content>

                <md-progress-bar md-mode="indeterminate" class="md-progress-bar" v-if="sending"/>

            </md-card>

        </widget>


    </div>
</template>

<script>
    import PasswordProtection from '../PasswordProtection'
    import Widget from '../../shared/widget'
    import { MiniGridService } from '../../services/MiniGridService'
    import { CityService } from '../../services/CityService'
    import { MappingService } from '../../services/MappingService'
    import { ClusterService } from '../../services/ClusterService'
    import Map from '../../shared/Map'
    import villageIcon from '../../../icons/village.png'
    import miniGridIcon from '../../../icons/miniGrid.png'
    import { EventBus } from '../../shared/eventbus'

    export default {
        name: 'AddVillage',
        components: {
            Widget,
            PasswordProtection,
            Map,
        },
        data () {
            return {
                miniGridIcon: miniGridIcon,
                villageIcon: villageIcon,
                miniGrids: [],
                clusterService: new ClusterService(),
                miniGridService: new MiniGridService(),
                mappingService: new MappingService(),
                geoData: null,
                center: this.appConfig.mapStartingPoint,
                villageSaved: false,
                sending: false,
                lastVillage: null,
                cityName: '',
                selectedMiniGridId: '',
                cityIndex: 0,
                cityService: new CityService(),
                cityLatLng: {
                    lat: null,
                    lon: null
                },
                miniGridLatLng: {
                    lat: null,
                    lon: null
                },
                markerLocations: [],
                constantLocations: [],
                markingInfos: [],
            }
        },
        mounted () {
            this.getMiniGrids()
            EventBus.$on('getDrawedMarker', (geoDataItem) => {
                this.cityLatLng.lat = Number(geoDataItem.geojson.coordinates.lat.toFixed(5))
                this.cityLatLng.lon = Number(geoDataItem.geojson.coordinates.lng.toFixed(5))
            })
            EventBus.$on('markerError', (message) => {
                this.$swal({
                    type: 'warn',

                    text: message,
                })

            })
        },

        methods: {
            async validatePoints () {
                let validator = await this.$validator.validateAll('Points-Form')
                if (validator) {
                    this.setMarker()
                }

            },
            async getMiniGrids () {
                try {
                    this.constantLocations = []
                    this.miniGrids = await this.miniGridService.getMiniGrids()
                    if (this.miniGrids.length > 0) {

                        this.constantLocations = []
                        this.markingInfos = []
                        this.selectedMiniGridId = this.miniGrids[this.miniGrids.length - 1].id
                        let miniGridGeoData = await this.miniGridService.getMiniGridGeoData(this.selectedMiniGridId)
                        let Points = miniGridGeoData.location.points.split(',')
                        this.miniGridLatLng.lat = Points[0]
                        this.miniGridLatLng.lon = Points[1]

                        await this.getGeoData(this.miniGrids[this.miniGrids.length - 1].cluster_id)
                        let markingInfo = this.mappingService.createMarkinginformation(miniGridGeoData.id, miniGridGeoData.name, null, Points[0], Points[1])
                        this.markingInfos.push(markingInfo)
                        this.constantLocations.push([this.miniGridLatLng.lat, this.miniGridLatLng.lon])

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
            async saveVillage () {
                let validator = await this.$validator.validateAll()

                if (validator) {
                    try {
                        this.sending = false

                        await this.cityService.createCity(this.cityName, this.clusterId, this.selectedMiniGridId, this.geoData)

                        this.alertNotify('success', 'The Village you add is stored successfully.')

                    } catch (e) {
                        this.alertNotify('error', e.message)
                    }

                }

            },
            setMarker () {
                this.markerLocations = []
                this.markerLocations.push([this.cityLatLng.lat, this.cityLatLng.lon])
            },
            async selectMiniGrid (miniGridId) {
                this.cityLatLng = {
                    lat: null,
                    lon: null
                }
                this.constantLocations = []
                this.markingInfos = []
                let miniGrid = this.miniGrids.filter(x => x.id === miniGridId)[0]
                this.selectedMiniGridId = miniGridId
                let miniGridGeoData = await this.miniGridService.getMiniGridGeoData(this.selectedMiniGridId)
                let Points = miniGridGeoData.location.points.split(',')
                this.miniGridLatLng.lat = Points[0]
                this.miniGridLatLng.lon = Points[1]
                await this.getGeoData(miniGrid.cluster_id)
                let markingInfo = this.mappingService.createMarkinginformation(miniGridGeoData.id, miniGridGeoData.name, Points[0], Points[1])
                this.markingInfos.push(markingInfo)
                this.constantLocations.push([this.miniGridLatLng.lat, this.miniGridLatLng.lon])
            },
            getValidateVillage () {
                this.sending = true
                let validator = this.$validator.validateAll()
                if (validator) {
                    this.sending = false
                    this.saveVillage()
                }
            },
            alertNotify (type, message) {
                this.$notify({
                    group: 'notify',
                    type: type,
                    title: type + ' !',
                    text: message
                })
            }
        },
    }
</script>

<style lang="scss" scoped>
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
