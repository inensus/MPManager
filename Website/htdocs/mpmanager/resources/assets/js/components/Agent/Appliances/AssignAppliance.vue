<template>

    <widget
        :class="'col-sm-6 col-md-5'"
        :button-text="'Assign new Appliance'"
        :button="true"
        title="Assigned Appliances"
        :button-color="'red'"
        :callback="()=>{showNewAsset = true}"
    >

        <!-- assing new appliance -->
        <form v-if="showNewAsset" novalidate class="md-layout" @submit.prevent="saveAsset">
            <md-card class="md-layout-item">
                <md-card-header>
                    <div style="float:right; cursor:pointer" @click="()=>{showNewAsset = false}">
                        <md-icon>close</md-icon>&nbsp;Close
                    </div>
                </md-card-header>
                <md-card-content>
                    <md-field>
                        <label for="appliance_types">Appliance </label>
                        <md-select name="appliance_types" id="appliance_types" v-model="newAppliance.id">
                            <md-option disabled value>--Select--</md-option>
                            <md-option
                                v-for="(applianceType,index) in applianceTypes"
                                :value="applianceType.id" :key="applianceType.id"
                            >{{applianceType.name}}
                            </md-option>
                        </md-select>
                    </md-field>

                    <md-field>
                        <label for="Cost">Cost </label>
                        <md-input type="text" name="Cost" v-model="newAppliance.cost"/>
                    </md-field>

                </md-card-content>
                <md-card-actions>
                    <md-button type="submit" class="md-primary btn-sell">Assign Appliance</md-button>
                </md-card-actions>
            </md-card>
        </form>

        <div>
            <!-- ana tablo  -->
            <md-table>
                <md-table-row>
                    <md-table-head>Name</md-table-head>
                    <md-table-head>Cost</md-table-head>
                </md-table-row>
                <md-table-row v-for="(item, index) in applianceTypes" :key="index">
                    <md-table-cell md-label="Name" md-sort-by="name">{{item.name}}</md-table-cell>
                    <md-table-cell md-label="Cost" md-sort-by="total_cost">{{item.cost}}</md-table-cell>

                </md-table-row>
            </md-table>
        </div>
    </widget>


</template>
<script>
    import Widget from '../../../shared/widget'
    import { AgentAssignedApplianceService } from '../../../services/AgentAssignedApplianceService'
    import { AgentService } from '../../../services/AgentService'

    export default {
        name: 'AssignAppliance',
        data () {
            return {
                assignedApplianceService: new AgentAssignedApplianceService(),
                showNewAsset: false,
                agent: {},
                newAppliance: {
                    id: null,
                    name: null,
                    cost: null
                },
                props: {
                    agentId: {
                        default: null
                    }
                },
                applianceTypes: []
            }
        },
        mounted () {
            this.getAgentDetail()
            this.getAssignedAppliances()
        },
        components: {
            Widget
        },
        methods: {
            saveAsset () {

            },
            async getAgentDetail () {
                try {
                    this.agent = await this.agentService.getAgent(Number(this.agentId))
                } catch (e) {
                    this.alertNotify('error', e.message)
                }

            },
            async getAssignedAppliances () {
                try {
                    this.applianceTypes = await this.assignedApplianceService.getAssignedAppliances(this.agent)

                } catch (e) {
                    this.alertNotify('error', e.message)
                }

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

</script>
<style scoped>

</style>
