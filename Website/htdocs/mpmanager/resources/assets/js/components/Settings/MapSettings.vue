<template>
    <div>
        <h2>Map Settings</h2>

        <div class="md-layout md-gutter md-size-100">
            <div class="md-layout-item md-layout md-gutter md-size-100">
                <div class="md-layout-item md-size-25 md-small-size-50">
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
                <div class="md-layout-item md-size-25 md-small-size-50">
                    <md-field>
                        <label for="provider">Default Provider</label>
                        <md-select v-model="mapSettingsService.mapSettings.provider" name="provider" id="provider" >
                            <md-option v-for="provider in mapProvider" :key="provider" :value="provider">{{provider}}</md-option>
                        </md-select>

                    </md-field>
                </div>
                <div class="md-layout-item md-size-50 md-small-size-100" v-if="bingMaps">
                    <md-field :class="{'md-invalid': errors.has('Bing Api Key')}">
                        <label for="apiKey" class="bing-api-key">Bing Map Api Key (Click <b><a href="https://www.bingmapsportal.com/" target="_blank">here</a></b> to get api key.)</label>
                        <md-input
                        id="apiKey"
                        name="Bing Api Key"
                        v-model="mapSettingsService.mapSettings.bingMapApiKey"
                        v-validate="'required|min:3'"></md-input>
                        <span class="md-error">{{ errors.first('Bing Api Key')}}</span>
                    </md-field>
                </div>

            </div>

            <div class="md-layout-item md-layout md-size-100">
                <md-subheader>Set Map Starting Points</md-subheader>
                <div class="md-layout-item md-layout md-gutter md-size-100">
                    <div class="md-layout-item md-size-35 md-small-size-50">
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
                    <div class="md-layout-item md-size-35 md-small-size-50">
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
                    :zoom="zoom"
                    :key="mapKey"
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
    computed:{
        zoom(){
            return Number(this.mapSettingsService.mapSettings.zoom)
        },
        bingMaps(){
            if(this.mapSettingsService.mapSettings.provider === 'Bing Maps'){
                return true
            }else{
                return false
            }
        }
    },
    data () {
        return {
            progress: false,
            mapSettingsService: new MapSettingsService(),
            mutatingCenter: [],
            mapKey: 1,
            mapProvider:[
                'Bing Maps',
                'Open Street Map']
        }
    },
    mounted () {
        this.$refs.map.map._onResize()
    },
    created () {
        EventBus.$on('mapEvent', this.setMapLatLng)
        EventBus.$on('mapZoom', this.setMapZoom)
        this.fetchMapSettings()
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
                this.progress = false
                return
            }
            if(this.bingMaps){
                if(await this.mapSettingsService.checkBingMapApiKey()){
                    this.alertNotify('error', 'Bing Map Api Key is wrong, please check again')
                    this.progress = false
                    return
                }
            }
            try {
                await this.mapSettingsService.update()
                this.$store.dispatch('settings/setMapSettings', this.mapSettingsService.mapSettings).then(() => {
                    this.mapKey += 1
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
            this.setCenterPoints()
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
.bing-api-key.a{
    color: black;
    font-weight: bolder;
}
</style>
