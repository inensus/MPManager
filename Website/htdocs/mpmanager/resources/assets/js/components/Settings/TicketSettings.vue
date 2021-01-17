<template>
    <div>
        <h2>Ticket Settings ( {{ticketSettings.name}} )</h2>
        <div class="md-layout md-size-100">
            <md-field :class="{'md-invalid': errors.has('api_token')}">
                <label> API Token</label>
                <md-input
                    name="api_token"
                    id="api_token"
                    v-validate="'required'"
                    v-model="ticketSettings.api_token"></md-input>
            </md-field>
            <span class="md-error">{{ errors.first('api_token') }}</span>
        </div>
        <div class="md-layout md-size-100">
            <md-field :class="{'md-invalid': errors.has('api_key')}">
                <label for="api_key">API Key</label>
                <md-input
                    name="api_key"
                    id="api_key"
                    v-validate="'required'"
                    v-model="ticketSettings.api_key"></md-input>
            </md-field>
            <span class="md-error">{{ errors.first('api_key') }}</span>
        </div>
        <div class="md-layout md-size-100">
            <md-field :class="{'md-invalid': errors.has('api_url')}">
                <label>API Url</label>
                <md-input
                    name="api_url"
                    id="api_url"
                    v-validate="'required'"
                    v-model="ticketSettings.api_url"></md-input>
            </md-field>
            <span class="md-error">{{ errors.first('api_url') }}</span>
        </div>

    </div>
</template>

<script>

import { TicketSettingsService } from '../../services/TicketSettingsService'

export default {
    name: 'TicketSettings',
    data(){
        return{
            ticketSettingsService: new TicketSettingsService(),
            ticketSettings:null,
        }
    },
    mounted () {
        this.getTicketSettings()
    },
    methods:{
        async getTicketSettings(){
            try {
                this.ticketSettings = await this.ticketSettingsService.list()
            }catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async updateTicketSettings(){
            let validator = await this.$validator.validateAll()
            if (!validator) {
                return
            }
            try {
                await this.ticketSettingsService.update(this.ticketSettings)
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
    }
}
</script>

<style scoped>

</style>
