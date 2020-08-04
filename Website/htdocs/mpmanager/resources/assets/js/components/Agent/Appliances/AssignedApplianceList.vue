<template>

    <widget
        :class="'col-sm-6 col-md-5'"
        :button-text="'Assign new Appliance'"
        :button="true"
        title="Assigned Appliances"
        :button-color="'red'"
        :callback="()=>{showNewAppliance = true}"
    >


        <div>
            <assign-appliance :assignNewAppliance="showNewAppliance" :agent-id="agentId"/>
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
    import AssignAppliance from './AssignAppliance'
    import { EventBus } from '../../../shared/eventbus'

    export default {
        name: 'AssignedApplianceList',
        data () {
            return {
                assignedApplianceService: new AgentAssignedApplianceService(),
                agentService: new AgentService(),

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

            this.getAgentDetail()
            this.getAssignedAppliances(this.agentId)

            EventBus.$on('applianceAssigned', this.closeAssignAppliance)
            EventBus.$on('assignApplianceClosed', () => {
                this.showNewAppliance = false
            })
        },
        destroyed () {
            EventBus.$off('applianceAssigned', this.closeAssignAppliance)
        },
        components: {
            AssignAppliance,
            Widget
        },
        methods: {
            async closeAssignAppliance () {
                this.showNewAppliance = false

                if (this.agent.id !== undefined) {

                    await this.getAssignedAppliances(this.agent.id)
                }

            },
            async getAgentDetail () {
                try {
                    this.agent = await this.agentService.getAgent(Number(this.agentId))

                } catch (e) {
                    this.alertNotify('error', e.message)
                }

            },
            async getAssignedAppliances (agentId) {
                try {
                    this.assignedApplianceTypes = await this.assignedApplianceService.getAssignedAppliances(agentId)
                } catch (e) {
                    this.alertNotify('error', e.message)
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
