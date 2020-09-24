<template>
    <widget
        :class="'col-sm-6 col-md-5'"
        title="Last Transactions"
        color="green"
        :paginator="agentTransactionService.paginator"
        :subscriber="subscriber"
    >
            <empty-state-wrapper
            :subscriber="subscriber"
            data-type="Agent Transaction">
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
            </empty-state-wrapper>
    </widget>
</template>
<script>
import Widget from '../../shared/widget'
import { AgentTransactionService } from '../../services/AgentTransactionService'
import { EventBus } from '../../shared/eventbus'
import EmptyStateWrapper from '../../shared/EmptyStateWrapper'
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
        EmptyStateWrapper
    },
    methods: {
        reloadList (subscriber, data) {
            if (subscriber !== this.subscriber) {
                return
            }
            this.agentTransactionService.updateList(data)
            EventBus.$emit('widgetContentLoaded',this.subscriber, this.agentTransactionService.list.length)
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
