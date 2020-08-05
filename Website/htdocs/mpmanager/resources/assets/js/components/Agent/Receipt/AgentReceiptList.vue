<template>
    <div>
        <widget
            :class="'col-sm-6 col-md-5'"
            :button-text="'Add Receipt'"
            :button="true"
            title="Last Receipts"
            :button-color="'red'"
            color="orange"
            :callback="()=>{showNewReceipt = true}"
            :paginator="agentReceiptService.paginator"
            :subscriber="subscriber"
            :resetKey="resetKey"
        >


            <div>
                <new-receipt :addNewReceipt.sync="showNewReceipt" :agent-id="agentId"/>
                <md-table md-sort="id" md-sort-order="asc">
                    <md-table-row>
                        <md-table-head>ID</md-table-head>
                        <md-table-head>Amount</md-table-head>
                        <md-table-head>Receiver</md-table-head>
                        <md-table-head>Date</md-table-head>
                    </md-table-row>
                    <md-table-row v-for="(item, index) in agentReceiptService.list" :key="index">
                        <md-table-cell md-sort-by="id" md-label="ID">{{item.id}}</md-table-cell>
                        <md-table-cell md-label="Amount">{{item.amount}}</md-table-cell>
                        <md-table-cell md-label="Amount">{{item.receiver}}</md-table-cell>
                        <md-table-cell md-label="Date">{{item.createdAt}}</md-table-cell>


                    </md-table-row>
                </md-table>
            </div>
        </widget>
    </div>
</template>
<script>
    import Widget from '../../../shared/widget'
    import NewReceipt from './NewReceipt'
    import { AgentReceiptService } from '../../../services/AgentReceiptService'
    import { EventBus } from '../../../shared/eventbus'

    export default {
        name: 'AgentReceiptList',
        data () {
            return {
                subscriber: 'agent-receipts',
                showNewReceipt: false,
                agentReceiptService: new AgentReceiptService(this.agentId),
                resetKey: 0,
            }
        },
        components: {
            NewReceipt,
            Widget
        },
        props: {
            agentId: {
                default: null
            }
        },
        methods: {
            reloadList (subscriber, data) {
                if (subscriber !== this.subscriber) return
                this.agentReceiptService.updateList(data)
            },

            async closeNewReceipt () {

                this.showNewReceipt = false

                if (this.agent.id !== undefined) {

                }

            }
        },
        mounted () {
            EventBus.$on('pageLoaded', this.reloadList)
            EventBus.$on('receiptAdded', this.closeNewReceipt)
            EventBus.$on('newReceiptClosed', () => {
                this.showNewReceipt = false
            })
        },
        beforeDestroy () {

        },
        destroyed () {
            EventBus.$off('receiptAdded', this.closeNewReceipt)
        },
    }

</script>
<style scoped>

</style>
<style scoped>

</style>
