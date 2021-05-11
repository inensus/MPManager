<template>
    <div>
        <h2>Map Settings</h2>

        <div class="md-layout md-gutter md-size-100">
            <div class="md-layout-item md-layout md-gutter md-size-25 md-small-size-100">
                <md-subheader>Set Map Default Zoom:</md-subheader>
                <div class="md-layout-item md-size-100">
                    <md-field :class="{'md-invalid': errors.has('Zoom')}">
                        <label for="Zoom">Default Zoom</label>
                        <md-input
                                type="number"
                                id="Zoom"
                                name="Zoom"
                                maxLength="1"
                                v-model="mapSettingsService.mapSettings.zoom"
                                v-validate="'integer|between:0,9'"
                        >
                        </md-input>
                        <span class="md-error">{{ errors.first('Zoom')}}</span>
                    </md-field>

                </div>
            </div>

            <div class="md-layout-item md-layout md-size-75 md-small-size-100" style="border-left: #3C3C3C solid 1px">
                <md-subheader>Set Map Starting Points</md-subheader>
                <div class="md-layout-item md-layout md-gutter md-size-100">
                    <div class="md-layout-item md-size-45 md-small-size-100">
                        <md-field :class="{'md-invalid': errors.has($tc('words.latitude'))}">
                            <label for="latitude">{{ $tc('words.latitude') }}</label>
                            <md-input
                                    type="number"
                                    id="latitude"
                                    :name="$tc('words.latitude')"
                                    v-model="mapSettingsService.mapSettings.latitude"
                                    step="any"
                                    @change="setCenterPoints"
                                    maxlength="9"
                                    v-validate="'required|decimal:5|max:8'"/>
                            <span class="md-error">{{ errors.first($tc('words.latitude')) }}</span>

                        </md-field>
                    </div>
                    <div class="md-layout-item md-size-45 md-small-size-100">
                        <md-field :class="{'md-invalid': errors.has($tc('words.longitude'))}">
                            <label for="longitude">{{ $tc('words.longitude') }}</label>
                            <md-input
                                    type="number"
                                    id="longitude"
                                    :name="$tc('words.longitude')"
                                    v-model="mapSettingsService.mapSettings.longitude"
                                    step="any"
                                    @change="setCenterPoints"
                                    maxlength="9"
                                    v-validate="'required|decimal:5|max:8'"/>
                            <span class="md-error">{{ errors.first($tc('words.longitude')) }}</span>
                        </md-field>
                    </div>
                </div>

            </div>


        </div>
        <div class="md-layout md-size-100" @click="getLatLng">
            <Map
                    :center="center"
                    ref="map"
                    :mutating-center="mutatingCenter"
                    :zoom="Number(mapSettingsService.mapSettings.zoom)"
            />
        </div>
        <div class="md-layout md-alignment-bottom-right">
            <md-button class="md-primary md-dense md-raised" @click="updateMapSettings">Save</md-button>
        </div>
        <md-progress-bar v-if="progress" md-mode="indeterminate"></md-progress-bar>

    </div>

</template>

<script>
import Map from '../../shared/Map'
import { MapSettingsService } from '../../services/MapSettingsService'
import { EventBus } from '../../shared/eventbus'

export default {
    name: 'MapSettings',
    components: { Map },
    props: {
        center: {
            type: Array,
        },
        mapSettings: {
            type: Object
        }
    },
    data () {
        return {
            progress: false,
            mapSettingsService: new MapSettingsService(),
            mutatingCenter: []
        }
    },
    mounted () {
        this.fetchMapSettings()
        this.$refs.map.map._onResize()
    },
    created () {
        EventBus.$on('mapEvent', this.setMapLatLng)
        EventBus.$on('mapZoom', this.setMapZoom)
    },
    methods: {
        setMapLatLng (latlng) {
            this.mapSettingsService.mapSettings.latitude = latlng.lat
            this.mapSettingsService.mapSettings.longitude = latlng.lng
        },
        setMapZoom (zoom) {
            this.mapSettingsService.mapSettings.zoom = zoom
        },
        async setCenterPoints () {
            let validator = await this.$validator.validateAll()
            if (!validator) {
                return
            } else {
                this.mutatingCenter = [this.mapSettingsService.mapSettings.latitude, this.mapSettingsService.mapSettings.longitude]
            }
        },
        async updateMapSettings () {
            this.progress = true
            let validator = await this.$validator.validateAll()
            if (!validator) {

                return
            }
            try {
                await this.mapSettingsService.update()
                this.$store.dispatch('settings/setMapSettings', this.mapSettingsService.mapSettings).then(() => {

                }).catch((err) => {
                    console.log(err)
                })
                this.alertNotify('success', 'Updated Successfully')
            } catch (e) {
                this.alertNotify('error', e.message)
            }
            this.progress = false
        },
        getLatLng () {
            this.$refs.map.getLatLng()
        },
        fetchMapSettings () {
            this.mapSettingsService.mapSettings = this.mapSettings
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

<style scoped>

</style>
