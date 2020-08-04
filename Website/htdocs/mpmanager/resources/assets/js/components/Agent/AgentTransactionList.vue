<template>
    <widget
        :class="'col-sm-6 col-md-5'"
        title="Last Transactions"
        color="green"
        :paginator="agentTransactionService.paginator"
        :subscriber="subscriber"
    >
        <div>
            <md-table md-sort="id" md-sort-order="asc">
                <md-table-row>
                    <md-table-head>ID</md-table-head>
                    <md-table-head>Amount</md-table-head>
                    <md-table-head>Meter</md-table-head>
                    <md-table-head>Customer</md-table-head>
                    <md-table-head>Date</md-table-head>
                </md-table-row>
                <md-table-row v-for="(item, index) in agentTransactionService.list" :key="index">
                    <md-table-cell md-sort-by="id" md-label="ID">{{item.id}}</md-table-cell>
                    <md-table-cell md-label="Amount">{{item.amount}}</md-table-cell>
                    <md-table-cell md-label="Meter">{{item.meter}}</md-table-cell>
                    <md-table-cell md-label="Customer">{{item.customer}}</md-table-cell>
                    <md-table-cell md-label="Date">{{item.createdAt}}</md-table-cell>
                </md-table-row>
            </md-table>
        </div>
    </widget>
</template>
<script>
    import Widget from '../../shared/widget'
    import { AgentTransactionService } from '../../services/AgentTransactionService'
    import { EventBus } from '../../shared/eventbus'

    export default {
        name: 'AgentTransactionList',
        data () {
            return {
                subscriber: 'agent-transactions',
                agentTransactionService: new AgentTransactionService(this.agentId)
            }
        },
        mounted () {
            EventBus.$on('pageLoaded', this.reloadList)

        },
        beforeDestroy () {
            EventBus.$off('pageLoaded', this.reloadList)
        },
        components: {
            Widget
        },
        methods: {
            reloadList (subscriber, data) {
                if (subscriber !== this.subscriber) return
                this.agentTransactionService.updateList(data)
            },
        },
        props: {
            agentId: {
                default: null
            }
        },
    }

</script>
<style scoped>

</style>
