<template>
    <div>
        <widget
                color="green"
                title="Settings"
        >
            <div style="padding: 2vh;">
                <md-tabs>
                    <md-tab id="tab-home" md-icon="home" md-label="Main" exact>
                        <main-settings :mainSettings="mainSettings" ref="mainSettings"/>
                    </md-tab>

                    <md-tab id="tab-map" md-icon="map" md-label="Map">
                        <map-settings :center="center" :mapSettings="mapSettings" ref="mapSettings"/>
                    </md-tab>

                    <md-tab id="tab-ticket" name="ticket" md-icon="confirmation_number" md-label="Ticket">
                        <ticket-settings :ticketSettings="ticketSettings" ref="ticketSettings"/>
                    </md-tab>

                </md-tabs>
                <div class="md-layout md-alignment-bottom-right">
                    <md-button class="md-primary md-dense md-raised" @click="saveSettings">Save</md-button>
                </div>
            </div>


        </widget>

    </div>
</template>

<script>
import Widget from '../../shared/widget'
import MainSettings from './MainSettings'
import MapSettings from './MapSettings'
import TicketSettings from './TicketSettings'
import { MainSettingsService } from '../../services/MainSettingsService'
import { MapSettingsService } from '../../services/MapSettingsService'
import { TicketSettingsService } from '../../services/TicketSettingsService'

export default {
    name: 'Settings',
    components: { Widget, MainSettings, MapSettings, TicketSettings },
    data () {
        return {
            mainSettingsService: new MainSettingsService(),
            mapSettingService: new MapSettingsService(),
            ticketSettingsService: new TicketSettingsService(),
            mainSettings:{},
            ticketSettings:{},
            mapSettings:{},
            center:null
        }
    },
    mounted () {
        this.getSettingStates()
    },
    methods: {
        async getSettingStates () {
            this.mainSettings = this.$store.getters['settings/getMainSettings']
            this.mapSettings = this.$store.getters['settings/getMapSettings']
            this.center = [this.mapSettings.latitude, this.mapSettings.longitude]
            this.ticketSettings = this.$store.getters['settings/getTicketSettings']
        },
        saveSettings () {
            try {
                this.$refs.mainSettings.updateMainSettings()
                this.$refs.mapSettings.updateMapSettings()
                this.$refs.ticketSettings.updateTicketSettings()
                this.alertNotify('success', 'Updated Successfully')
            } catch (e) {
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
