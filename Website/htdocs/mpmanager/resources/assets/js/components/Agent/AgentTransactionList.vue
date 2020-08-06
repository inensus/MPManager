<template>
    <widget
        :class="'col-sm-6 col-md-5'"
        title="Last Transactions"
        color="green"
        :paginator="agentTransactionService.paginator"
        :subscriber="subscriber"
    >
        <div v-if="agentTransactionService.list.length>0">
            <md-table md-sort="id" md-sort-order="asc">
                <md-table-row>
                    <md-table-head v-for="(item, index) in headers" :key="index">{{item}}</md-table-head>
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
        <div v-else>
            <no-table-data :headers="headers" :tableName="tableName"/>
        </div>
    </widget>
</template>
<script>
    import Widget from '../../shared/widget'
    import { AgentTransactionService } from '../../services/AgentTransactionService'
    import { EventBus } from '../../shared/eventbus'
    import NoTableData from '../../shared/NoTableData'

    export default {
        name: 'AgentTransactionList',
        data () {
            return {
                subscriber: 'agent-transactions',
                agentTransactionService: new AgentTransactionService(this.agentId),
                headers: ['ID', 'Amount', 'Meter', 'Customer', 'Date'],
                tableName: 'Agent Transaction'
            }
        },
        mounted () {
            EventBus.$on('pageLoaded', this.reloadList)

        },
        beforeDestroy () {
            EventBus.$off('pageLoaded', this.reloadList)
        },
        components: {
            Widget,
            NoTableData
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
