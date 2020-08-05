<template>
    <div>
        <form v-if="addNewReceipt" novalidate class="md-layout" @submit.prevent="saveReceipt"
              data-vv-scope="Receipt-Form">
            <md-card class="md-layout-item">
                <md-card-header>
                    <div style="float:right; cursor:pointer" @click="hide()">
                        <md-icon>close</md-icon>&nbsp;Close
                    </div>
                </md-card-header>
                <md-card-content>

                    <md-field :class="{'md-invalid': errors.has('Receipt-Form.amount')}">
                        <label for="amount">Amount </label>
                        <md-input name="amount" id="amount" v-model="agentReceiptService.newReceipt.amount"
                                  v-validate="'required|min_value:0'" type="number"/>
                        <span class="md-error">{{ errors.first('Receipt-Form.amount') }}</span>
                    </md-field>
                    <md-progress-bar md-mode="indeterminate" v-if="loading"/>
                </md-card-content>
                <md-card-actions>
                    <md-button role="button" type="submit" class="md-raised md-primary" :disabled="loading">
                        Receive
                    </md-button>

                </md-card-actions>
            </md-card>
        </form>
    </div>

</template>
<script>

    import { EventBus } from '../../../shared/eventbus'
    import { AgentReceiptService } from '../../../services/AgentReceiptService'

    export default {
        name: 'NewReceipt',
        data () {
            return {
                agentReceiptService: new AgentReceiptService(),

                loading: false,

            }
        },
        components: {},
        props: {
            agentId: {
                default: null
            },
            addNewReceipt: {
                type: Boolean,
                default: false,

            }
        },
        mounted () {

        },
        methods: {

            async saveReceipt () {
                let validator = await this.$validator.validateAll('Receipt-Form')

                if (validator) {
                    try {
                        this.loading = true
                        try {

                            this.agentReceiptService.newReceipt.agentId = this.agentId
                            await this.agentReceiptService.addNewBalance()
                            this.loading = false
                            this.balanceAdded()
                            this.alertNotify('success', 'Agent added successfully')
                        } catch (e) {
                            this.loading = false
                            this.alertNotify('error', e.message)
                        }
                    } catch (e) {

                    }
                }
            },
            hide () {
                EventBus.$emit('newReceiptClosed')
            },
            receiptAdded () {
                EventBus.$emit('receiptAdded')
            },
        }
    }

</script>
<style scoped>

</style>
<style scoped>

</style>
