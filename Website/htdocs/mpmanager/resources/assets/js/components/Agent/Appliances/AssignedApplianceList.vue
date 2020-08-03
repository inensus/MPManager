<template>

    <widget
        :class="'col-sm-6 col-md-5'"
        :button-text="'Assign new Appliance'"
        :button="true"
        title="Assigned Appliances"
        :button-color="'red'"
        :callback="()=>{showNewAppliance = true}"
    >

        <!-- assing new appliance -->
        <form v-if="showNewAppliance" novalidate class="md-layout" @submit.prevent="saveAsset"
              data-vv-scope="Appliance-Form">
            <md-card class="md-layout-item">
                <md-card-header>
                    <div style="float:right; cursor:pointer" @click="()=>{showNewAppliance = false}">
                        <md-icon>close</md-icon>&nbsp;Close
                    </div>
                </md-card-header>
                <md-card-content>
                    <md-field :class="{'md-invalid': errors.has('Appliance-Form.applianceTypes')}">
                        <label for="applianceTypes">Appliance </label>
                        <md-select name="applianceTypes" id="applianceTypes" v-model="newAppliance.id"
                                   v-validate="'required'">
                            <md-option disabled value>&#45;&#45;Select&#45;&#45;</md-option>
                            <md-option
                                v-for="(applianceType,index) in applianceTypes"
                                :value="applianceType.id" :key="applianceType.id"
                            >{{applianceType.name}}
                            </md-option>
                        </md-select>
                        <span class="md-error">{{ errors.first('Appliance-Form.applianceTypes') }}</span>
                    </md-field>

                    <md-field :class="{'md-invalid': errors.has('Appliance-Form.cost')}">
                        <label for="cost">Cost </label>
                        <md-input type="text" name="cost" id="cost" v-model="newAppliance.cost"
                                  v-validate="'required'"/>
                        <span class="md-error">{{ errors.first('Appliance-Form.cost') }}</span>
                    </md-field>
                    <md-progress-bar md-mode="indeterminate" v-if="loading"/>
                </md-card-content>
                <md-card-actions>
                    <md-button role="button" class="md-raised md-primary" :disabled="loading" @click="assignAppliance">
                        Assign Appliance
                    </md-button>

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
                <md-table-row v-for="(item, index) in assignedApplianceTypes" :key="index">
                    <md-table-cell md-label="Name" md-sort-by="name">{{item.applianceType}}</md-table-cell>
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
    import { AssetService } from '../../../services/AssetService'

    export default {
        name: 'AssignedApplianceList',
        data () {
            return {
                assignedApplianceService: new AgentAssignedApplianceService(),
                agentService: new AgentService(),
                assetService: new AssetService(),
                showNewAppliance: false,
                agent: {},
                newAppliance: {
                    id: null,
                    name: null,
                    cost: null
                },
                loading: false,
                assignedApplianceTypes: [],
                applianceTypes: []
            }
        },
        props: {
            agentId: {
                default: null
            }
        },
        mounted () {
            this.getApplianceTypes()
            this.getAgentDetail()

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
                    await this.getAssignedAppliances(this.agent)
                } catch (e) {
                    this.alertNotify('error', e.message)
                }

            },
            async getAssignedAppliances (agent) {
                try {

                    this.assignedApplianceTypes = await this.assignedApplianceService.getAssignedAppliances(agent)

                } catch (e) {
                    this.alertNotify('error', e.message)
                }

            },
            async getApplianceTypes () {
                try {
                    this.applianceTypes = await this.assetService.getAssets()
                } catch (e) {
                    this.alertNotify('error', e.message)
                }
            },
            async assignAppliance () {
                let validator = await this.$validator.validateAll('Appliance-Form')

                if (validator) {
                    this.loading = true
                    try {
                        let userId = this.$store.getters['auth/authenticationService'].authenticateUser.id
                        await this.assignedApplianceService.assignAppliance(this.newAppliance, userId, this.agent.id)
                        this.loading = false
                        await this.hide()
                        this.alertNotify('success', 'Agent added successfully')
                    } catch (e) {
                        this.loading = false
                        this.alertNotify('error', e.message)
                    }
                }
            },
            async hide () {
                this.showNewAppliance = false
                await this.getAssignedAppliances(this.agent)
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
