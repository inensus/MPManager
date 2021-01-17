<template>

    <widget
        :title="$tc('words.detail',2) + (currentMap === 0 ? ' [Open Street]': ' [Google]')"
        id="client-map"
    >
        <div slot="tabbar">
            <md-field>
                <label for="map">{{ $tc('phrases.mapProvider') }}</label>
                <md-select @md-selected="mapSelected">
                    <md-option value="0">Open Street</md-option>
                    <md-option value="1">Google</md-option>
                </md-select>
            </md-field>
        </div>
        <Map
            :zoom="18"
            :center="center"
            :markerLocations="markerLocations"
            :markingInfos="markingInfos"
            :isMeter="true"
            :edit="true"
            :markerUrl="meterIcon"
        />

    </widget>
</template>

<script>
import Widget from '../../shared/widget'
import { ClusterService } from '../../services/ClusterService'
import { MappingService } from '../../services/MappingService'
import Map from '../../shared/Map'
import { MiniGridService } from '../../services/MiniGridService'
import { PersonService } from '../../services/PersonService'
import { MeterService } from '../../services/MeterService'
import meterIcon from '../../../icons/meter.png'
import { EventBus } from '../../shared/eventbus'

export default {
    name: 'ClientMap',
    components: {
        Widget,
        Map,
    },
    props: {
        meterIds: {
            default: null
        }
    },
    data () {
        return {
            clusterService: new ClusterService(),
            mappingService: new MappingService(),
            miniGridService: new MiniGridService(),
            personService: new PersonService(),
            meterService: new MeterService(),
            markerLocations: [],
            meterIcon: meterIcon,
            meterLatLng: {
                lat: null,
                lon: null
            },
            meters: [],
            markingInfos: [],
            loading: false,
            currentMap: 0,
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

        this.getMeterPoints(this.meterIds)
        EventBus.$on('getEditedGeoDataItems', (editedItems) => {
            this.$swal({
                title: this.$tc('phrases.relocateMeter',0),
                text: this.$tc('phrases.relocateMeter',1),
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: this.$tc('words.relocate'),
                cancelButtonText: this.$tc('words.dismiss'),
            }).then((result) => {

                if (result) {
                    editedItems.forEach(async (e) => {
                        try {
                            await this.meterService.updateMeter(e.id, e.lat, e.lng)
                            this.alertNotify('success', this.$tc('phrases.relocateMeter',2))
                        } catch (e) {
                            this.alertNotify('error', e.message)
                        }
                    })
                }
            })

        })
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
        getMeterPoints (metersIds) {
            this.markingInfos = []
            metersIds.forEach(async (e) => {
                let meter = await this.meterService.getMeterDetails(e)
                this.meters.push(meter)
                for (let i in this.meters) {
                    let points = this.meters[i].geo
                    this.meterLatLng.lat = points[0]
                    this.meterLatLng.lon = points[1]
                    let markingInfo = this.mappingService.createMarkinginformation(this.meters[i].id, null, this.meters[i].serial_number, points[0], points[1])
                    this.markingInfos.push(markingInfo)

                    this.markerLocations.push([this.meterLatLng.lat, this.meterLatLng.lon])
                }
            })

        },
        mapSelected (val) {
            this.currentMap = val
        },
        alertNotify (type, message) {
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message,
                speed: 0
            })
        },
    },

}
</script>


