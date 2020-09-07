<template>

    <div v-if="assignNewAppliance">
        <!-- assing new appliance -->
        <form novalidate class="md-layout" @submit.prevent="saveAppliance"
              data-vv-scope="Appliance-Form">
            <md-card class="md-layout-item">
                <md-card-header>
                    <div style="float:right; cursor:pointer" @click="hide()">
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
                    <md-button role="button" type="submit" class="md-raised md-primary" :disabled="loading">
                        Assign Appliance
                    </md-button>

                </md-card-actions>
            </md-card>
        </form>
    </div>

</template>
<script>
    import Widget from '../../../shared/widget'
    import { AgentAssignedApplianceService } from '../../../services/AgentAssignedApplianceService'
    import { EventBus } from '../../../shared/eventbus'
    import { AssetService } from '../../../services/AssetService'

    export default {
        name: 'AssignAppliance',
        data () {
            return {
                assignedApplianceService: new AgentAssignedApplianceService(),
                assetService: new AssetService(),
                loading: false,
                agent: {},
                newAppliance: {
                    id: null,
                    name: null,
                    cost: null
                },

                applianceTypes: []
            }
        },
        props: {
            agentId: {
                default: null
            },
            assignNewAppliance: {
                type: Boolean,
                default: false
            }
        },
        components: {
            Widget
        },
        mounted () {
            this.getApplianceTypes()
        },
        methods: {
            async getApplianceTypes () {
                try {
                    this.applianceTypes = await this.assetService.getAssets()
                } catch (e) {
                    this.alertNotify('error', e.message)
                }
            },
            async saveAppliance () {
                let validator = await this.$validator.validateAll('Appliance-Form')

                if (validator) {
                    this.loading = true
                    try {
                        let userId = this.$store.getters['auth/authenticationService'].authenticateUser.id
                        await this.assignedApplianceService.assignAppliance(this.newAppliance, userId, this.agentId)
                        this.loading = false
                        this.applianceAssigned()
                        this.alertNotify('success', 'Agent added successfully')
                    } catch (e) {
                        this.loading = false
                        this.alertNotify('error', e.message)
                    }
                }
            },
            hide () {
                EventBus.$emit('assignApplianceClosed')
            },
            applianceAssigned () {
                EventBus.$emit('applianceAssigned')
            },
            alertNotify (type, message) {
                this.$notify({
                    group: 'notify',
                    type: type,
                    title: type + ' !',
                    text: message
                })
            }
        }
    }

</script>
<style scoped>

</style>
