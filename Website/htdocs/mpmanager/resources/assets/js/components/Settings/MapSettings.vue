<template>
    <div>
        <h2>Map Settings</h2>

        <div class="md-layout md-gutter md-size-100">
            <div class="md-layout-item md-layout md-gutter md-size-25">
                <md-subheader>Set Map Default Zoom:</md-subheader>
                <div class="md-layout-item">
                    <md-field :class="{'md-invalid': errors.has('Zoom')}" >
                        <label for="Zoom">Default Zoom</label>
                        <md-input
                            type="number"
                            id="Zoom"
                            name="Zoom"
                            maxLength="1"
                            v-model="mapSettings.zoom"
                            v-validate="'integer|between:0,9'"
                            >
                        </md-input>
                        <span class="md-error">{{ errors.first('Zoom')}}</span>
                    </md-field>

                </div>
            </div>

            <div class="md-layout-item md-layout md-size-75" style="border-left: #3C3C3C solid 1px">
                <md-subheader>Set Map Starting Points</md-subheader>
                <div class="md-layout-item md-layout md-gutter md-size-100">
                    <div class="md-layout-item md-size-45">
                        <md-field :class="{'md-invalid': errors.has($tc('words.latitude'))}">
                            <label for="latitude">{{ $tc('words.latitude') }}</label>
                            <md-input
                                type="number"
                                id="latitude"
                                :name="$tc('words.latitude')"
                                v-model="mapSettings.latitude"
                                step="any"
                                maxlength="9"
                                @change="setCenterPoints"
                                v-validate="'required|decimal:5|max:8'"/>
                            <span class="md-error">{{ errors.first($tc('words.latitude')) }}</span>

                        </md-field>
                    </div>
                    <div class="md-layout-item md-size-45">
                        <md-field :class="{'md-invalid': errors.has($tc('words.longitude'))}">
                            <label for="longitude">{{ $tc('words.longitude') }}</label>
                            <md-input
                                type="number"
                                id="longitude"
                                :name="$tc('words.longitude')"
                                v-model="mapSettings.longitude"
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
        <div class="md-layout md-size-100">
            <Map
                :center="center"
                ref="map"
                :zoom="mapSettings.zoom"

            />
        </div>
        <md-progress-bar v-if="progress" md-mode="indeterminate"></md-progress-bar>

    </div>

</template>

<script>
import Map from '../../shared/Map'
import { MapSettingsService } from '../../services/MapSettingsService'

export default {
    name: 'MapSettings',
    components: { Map },
    data (){
        return{
            center:this.$store.state.mapSettings.center,
            mapSettings:{
                zoom:null,
                latitude:null,
                longitude:null

            },
            progress:false,
            mapSettingsService: new MapSettingsService()
        }
    },
    mounted () {
        this.getMapSettings()
    },
    methods:{
        async setCenterPoints(){
            let validator = await this.$validator.validateAll()
            if (!validator) {

                return
            }
            this.center = [this.mapSettings.latitude, this.mapSettings.longitude ]
        },
        async updateMapSettings(){
            this.progress = true
            let validator = await this.$validator.validateAll()
            if (!validator) {

                return
            }
            try {
                await this.mapSettingsService.update(this.mapSettings)
                this.$store.state.mapSettings.zoom = this.mapSettings.zoom
                this.$store.state.mapSettings.center = [this.mapSettings.latitude, this.mapSettings.longitude]
            }catch (e) {
                this.alertNotify('error', e.message)
            }
            this.progress = false
        },
        async getMapSettings(){
            try {
                this.mapSettings = await this.mapSettingsService.list()
            }catch (e) {
                this.alertNotify('error', e.message)
            }
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
