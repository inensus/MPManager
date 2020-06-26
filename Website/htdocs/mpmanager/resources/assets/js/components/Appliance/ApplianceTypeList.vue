<template>
    <div>

        <AddApplianceType :addNewApplianceType="addNewApplianceType"/>
        <widget
            :title="'Appliance Types'"
            :search="true"
            :subscriber="subscriber"
            :route_name="'/appliance/types'"
            :button="true"
            button-text="New Appliance Type"
            :callback="() => {addNewApplianceType = true}"
            :paginator="applianceService.paginator"
            color="green">

            <md-table>
                <md-table-row>
                    <md-table-head>
                        ID
                    </md-table-head>
                    <md-table-head>
                        Appliance Type
                    </md-table-head>
                    <md-table-head>

                    </md-table-head>
                    <md-table-head>
                        Last Update
                    </md-table-head>
                </md-table-row>


                <md-table-row v-for="(appliance_type,index) in applianceService.list" style="cursor:pointer;" :key="index">

                    <md-table-cell> {{ appliance_type.id}}
                    </md-table-cell>

                    <md-table-cell>
                        <div class="md-layout" v-if="!appliance_type.edit">
                            {{ appliance_type.name}}&nbsp;&nbsp;
                            <div class="md-layout-item" style="display: inline-block; cursor: pointer; color: #2b542c"
                                 @click="appliance_type.edit = true">
                                <font-awesome-icon icon="pen"/>&nbsp;Edit
                            </div>

                        </div>
                        <div class="md-layout-item" v-else>
                            <md-field>

                                <md-input type="text" v-model="appliance_type.name"></md-input>
                            </md-field>
                        </div>
                    </md-table-cell>

                    <md-table-cell>
                        <div class="md-layout-item" style="display: inline-block; cursor: pointer; color: #2b542c"
                             v-if="appliance_type.edit"
                             @click="updateApplianceType(appliance_type)">
                            <font-awesome-icon icon="save"/>
                            Save
                        </div>
                        <div class="md-layout-item" v-else
                             style="display: inline-block; cursor: pointer; color:#ac2925; float:right"
                             @click="deleteApplianceType(appliance_type)">
                             <font-awesome-icon icon="trash"/>
                            Delete
                        </div>
                    </md-table-cell>

                    <md-table-cell class="hidden-xs">{{appliance_type.updated_at}}</md-table-cell>


                </md-table-row>
            </md-table>
        </widget>
    </div>

</template>

<script>
    import Widget from '../../shared/widget'
    import AddApplianceType from './AddApplianceType'
    import {EventBus} from '../../shared/eventbus'
    import {ApplianceService} from '../../services/ApplianceService'

    export default {
        name: 'ApplianceTypeList',
        components: {Widget, AddApplianceType},

        data() {
            return {
                addNewApplianceType: false,
                subscriber: 'appliance-list',
                applianceService: new ApplianceService(),
                applianceTypes: [],

            }
        },
        mounted() {
            EventBus.$on('applianceTypeAdded', this.addToList)
            EventBus.$on('pageLoaded', this.reloadList)
            EventBus.$on('addApplianceTypeClosed', this.closeAddComponent)
        },
        beforeDestroy() {
            EventBus.$off('applianceTypeAdded', this.addToList)
            EventBus.$off('pageLoaded', this.reloadList)

        },
        methods: {
            reloadList(subscriber, data) {

                if (subscriber !== this.subscriber) return
                this.applianceService.updateList(data)

            },
            addToList(appliance_type) {
                let appliance_t = {
                        id: appliance_type.id,
                        name: appliance_type.name,
                        edit: false,
                        appliance_type_name: appliance_type.name

                    }
                ;
                this.applianceService.list.push(appliance_t)
            },

            updateApplianceType(appliance_type) {
                appliance_type.edit = false;

                this.applianceService.updateAppliance(appliance_type)
                    .then((response) => {
                        this.alertNotify('success', appliance_type.name + ' has updated.')

                    }).catch(e => {
                    this.alertNotify('error', e.message)
                })
            },

            deleteApplianceType(appliance_type) {

                this.applianceService.deleteAppliance(appliance_type)
                    .then((response) => {
                        this.alertNotify('success', appliance_type.name + ' has deleted.')
                        location.reload()
                    }).catch(e => {
                    this.alertNotify('error', e.message)
                })

            },

            closeAddComponent(data) {

                this.addNewApplianceType = data
            },
            alertNotify(type, message) {
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
