<template>
    <div>
        <!-- new balance -->
        <form v-if="addNewBalance" novalidate class="md-layout" @submit.prevent="saveBalance"
              data-vv-scope="Balance-Form">
            <md-card class="md-layout-item">
                <md-card-header>
                    <div style="float:right; cursor:pointer" @click="hide()">
                        <md-icon>close</md-icon>&nbsp;Close
                    </div>
                </md-card-header>
                <md-card-content>

                    <md-field :class="{'md-invalid': errors.has('Balance-Form.amount')}">
                        <label for="amount">Amount </label>
                        <md-input name="amount" id="amount" v-model="agentChargeService.newBalance.amount"
                                  v-validate="'required|min_value:0'" type="number"/>
                        <span class="md-error">{{ errors.first('Balance-Form.amount') }}</span>
                    </md-field>
                    <md-progress-bar md-mode="indeterminate" v-if="loading"/>
                </md-card-content>
                <md-card-actions>
                    <md-button role="button" type="submit" class="md-raised md-primary" :disabled="loading">
                        + Balance
                    </md-button>

                </md-card-actions>
            </md-card>
        </form>
    </div>
</template>
<script>
    import { AgentChargeService } from '../../../services/AgentChargeService'
    import { EventBus } from '../../../shared/eventbus'

    export default {
        name: 'addAgentBalance',
        data () {
            return {
                agentChargeService: new AgentChargeService(),

                loading: false,
            }
        },
        props: {
            agentId: {
                default: null
            },
            addNewBalance: {
                default: false,

            }
        },

        mounted () {
        }, beforeDestroy () {

        },

        methods: {

            async saveBalance () {
                let validator = await this.$validator.validateAll('Balance-Form')

                if (validator) {

                    this.loading = true
                    try {
                        this.agentChargeService.newBalance.userId = this.$store.getters['auth/authenticationService'].authenticateUser.id
                        this.agentChargeService.newBalance.agentId = this.agentId
                        await this.agentChargeService.addNewBalance()
                        this.loading = false
                        this.balanceAdded()
                        this.alertNotify('success', 'Agent added successfully')
                    } catch (e) {
                        this.loading = false
                        this.alertNotify('error', e.message)
                    }

                }
            },
            balanceAdded () {
                EventBus.$emit('balanceAdded')
            },
            hide () {
                EventBus.$emit('addBalanceClosed')
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
