<template>
    <widget v-if="addNewCommission"
            title="New Commission"
            :show-refresh-button="false">

        <form novalidate class="md-layout" @submit.prevent="saveCommission"
              data-vv-scope="Commission-Form">
            <md-card class="md-layout-item">
                <md-card-header>
                    <div style="float:right; cursor:pointer" @click="hide()">
                        <md-icon>close</md-icon>&nbsp;Close
                    </div>
                </md-card-header>
                <md-card-content>
                    <md-field :class="{'md-invalid': errors.has('Commission-Form.name')}">
                        <label for="name">Name </label>
                        <md-input name="name" id="name" v-model="agentCommissionService.agentCommission.name"
                                  v-validate="'required|min:3'"
                        />
                        <span class="md-error">{{ errors.first('Commission-Form.name') }}</span>
                    </md-field>

                    <md-field :class="{'md-invalid': errors.has('Commission-Form.energyCommission')}">
                        <label for="energyCommission">Energy Commission </label>
                        <md-input name="energyCommission" id="energyCommission"
                                  v-model="agentCommissionService.agentCommission.energyCommission"
                                  v-validate="'required|min_value:0'" type="number"
                        />
                        <span class="md-error">{{ errors.first('Commission-Form.energyCommission') }}</span>
                    </md-field>

                    <md-field :class="{'md-invalid': errors.has('Commission-Form.applianceCommission')}">
                        <label for="applianceCommission">Appliance Commission </label>
                        <md-input name="applianceCommission" id="applianceCommission"
                                  v-model="agentCommissionService.agentCommission.applianceCommission"
                                  v-validate="'required|min_value:0'" type="number"
                        />
                        <span class="md-error">{{ errors.first('Commission-Form.applianceCommission') }}</span>
                    </md-field>

                    <md-field :class="{'md-invalid': errors.has('Commission-Form.riskBalance')}">
                        <label for="riskBalance">Risk Balance (must be negative)</label>
                        <md-input name="riskBalance"
                                  id="riskBalance"
                                  max="0"
                                  v-model="agentCommissionService.agentCommission.riskBalance"
                                  v-validate="'required|max_value:0'" type="number"
                        />
                        <span class="md-error">{{ errors.first('Commission-Form.riskBalance') }}</span>
                    </md-field>

                    <md-progress-bar md-mode="indeterminate" v-if="loading"/>
                </md-card-content>
                <md-card-actions>
                    <md-button role="button" type="submit" class="md-raised md-primary" :disabled="loading">
                        Save
                    </md-button>

                </md-card-actions>
            </md-card>
        </form>


    </widget>

</template>
<script>

import { EventBus } from '../../../shared/eventbus'
import { AgentCommissionService } from '../../../services/AgentCommissionService'
import widget from '../../../shared/widget'

export default {
    name: 'NewCommission',
    data () {
        return {
            agentCommissionService: new AgentCommissionService(),
            loading: false,

        }
    },
    components: {widget},
    props: {

        addNewCommission: {
            type: Boolean,
            default: false,

        }
    },
    methods: {

        async saveCommission () {
            let validator = await this.$validator.validateAll('Commission-Form')

            if (validator) {
                this.loading = true
                try {
                    await this.agentCommissionService.createAgentCommission()
                    this.loading = false
                    this.commissionAdded()
                    this.alertNotify('success', 'Agent commission added successfully')
                } catch (e) {
                    this.loading = false
                    this.alertNotify('error', e.message)
                }
            }

        },
        hide () {
            EventBus.$emit('newCommissionClosed')
        },
        commissionAdded () {
            EventBus.$emit('commissionAdded')
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
    .success-span {
        font-size: large;
        font-weight: 700;
        color: green;
    }

    .exclamation-div {
        margin-top: 2% !important;
    }

    .exclamation {
        width: 100% !important;
        margin: auto;
        align-items: center;
        display: inline-grid;
        text-align: center;

    }

    .exclamation-div span {
        font-size: medium !important
    }
</style>

