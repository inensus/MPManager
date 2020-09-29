<template>
    <div>
        <widget v-if="toggleNewType"
                id="add-new-meter-type"
                title="Add New Meter Type"
                color="red"


        >
            <md-card>
                <md-card-content>
                    <div class="md-layout md-gutter md-size-100">
                        <div class="md-layout-item md-size-40">
                            <md-field :class="{'md-invalid': errors.has('max_current')}">
                                <label>Max Current</label>
                                <md-input v-model="meterType.max_current"
                                          id="max_current"
                                          name="max_current"
                                          v-validate="'required|numeric'"></md-input>
                                <span class="md-error">{{ errors.first('max_current') }}</span>
                                <span class="md-suffix">Amper</span>
                            </md-field>
                        </div>
                        <div class="md-layout-item md-size-40">
                            <md-field :class="{'md-invalid': errors.has('phase')}">
                                <label>Phase</label>
                                <md-input v-model="meterType.phase"
                                          id="phase"
                                          name="phase"
                                          v-validate="'required|numeric'"></md-input>
                                <span class="md-error">{{ errors.first('phase') }}</span>
                            </md-field>
                        </div>
                        <div class="md-layout-item md-size-10">
                            <span class="md-subheader">
                                <md-checkbox v-model="online" class="md-primary">Online</md-checkbox>
                            </span>

                        </div>
                        <div class="md-layout-item md-size-10">
                            <md-button class="md-primary md-dense md-raised" @click="saveMeterType">Save</md-button>
                        </div>
                    </div>
                </md-card-content>


            </md-card>


        </widget>
        <widget id="meter-types-list"
                title="Meter Types"
                :button="true"
                :subscriber="subscriber"
                buttonText="Add New Type"
                @widgetAction="showNewType"
                color="green"
        >

                    <md-table>
                        <md-table-row>
                            <md-table-head>ID</md-table-head>
                            <md-table-head>Name</md-table-head>
                            <md-table-head>Max Current</md-table-head>
                            <md-table-head>Connectivity</md-table-head>
                        </md-table-row>
                        <md-table-row v-for="(type,index) in meterTypesList" :key="index">
                            <md-table-cell>{{index+1}}</md-table-cell>
                            <md-table-cell>{{type.name}}</md-table-cell>
                            <md-table-cell>{{type.max_current}}</md-table-cell>
                            <md-table-cell>
                                <md-icon>{{type.online === 0 ? 'check_box' : 'check_box_outline_blank'}}
                                </md-icon>
                                <span>{{ connectivity[index] }}</span>
                            </md-table-cell>
                        </md-table-row>
                    </md-table>


        </widget>
    </div>

</template>

<script>

import Widget from '../../shared/widget'
import { MeterTypeService } from '../../services/MeterTypeService'
import { EventBus } from '../../shared/eventbus'

export default {
    name: 'Types',
    components: { Widget },
    data () {
        return {
            meterTypeService: new MeterTypeService(),
            toggleNewType: false,
            subscriber:'meter-type',
            meterType: {
                max_current: null,
                phase: null,
                online: 0,
            },
            online: false,
            meterTypesList: null,

        }
    },
    mounted () {
        this.getMeterTypes()
    },
    methods: {
        showNewType () {
            this.toggleNewType = !this.toggleNewType
        },
        async saveMeterType () {
            let validation = await this.$validator.validateAll()
            if (!validation) {
                return
            }
            this.meterType.online = this.online ? 0 : 1
            try {
                this.meterTypesList = await this.meterTypeService.createMeterType(this.meterType)
                this.showNewType()
                this.meterType.max_current = null
                this.meterType.phase = null
                this.meterType.online = 0
                this.online = false
                this.alertNotify('success', 'Meter Type Added Successful ')
            } catch (e) {
                this.alertNotify('error', e.message)
            }

        },
        async getMeterTypes () {
            try {
                this.meterTypesList = await this.meterTypeService.getMeterTypes()
                EventBus.$emit('widgetContentLoaded',this.subscriber,this.meterTypesList.length)
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
        }
    },
    computed:{
        connectivity: function(){
            return this.meterTypesList.map(function (type){
                return type.online === 0 ? 'Online' : 'Offline'
            })
        }
    },
}
</script>

<style scoped>

</style>
