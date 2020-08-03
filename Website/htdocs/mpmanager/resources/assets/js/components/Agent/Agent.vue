<template>
    <div>
        <div class="md-layout md-gutter">
            <div
                class="md-layout-item md-large-size-50 md-medium-size-50 md-xlarge-size-50 md-small-size-100 md-small-size-100">
                <agent-detail :agent-id="agentId"/>
            </div>

            <div
                class="md-layout-item md-large-size-50 md-medium-size-50 md-xlarge-size-50 md-small-size-100 md-small-size-100">
                <assigned-appliance-list :agent-id="agentId"/>
            </div>
            <div
                class="md-layout-item md-large-size-100 md-medium-size-100 md-xlarge-size-100 md-small-size-100 md-small-size-100">
                <agent-receipt-list/>
            </div>
            <div
                class="md-layout-item md-large-size-50 md-medium-size-50 md-xlarge-size-50 md-small-size-100 md-small-size-100">
                <sold-appliance-list/>
            </div>

            <div
                class="md-layout-item md-large-size-50 md-medium-size-50 md-xlarge-size-50 md-small-size-100 md-small-size-100">
                <agent-transaction-list/>
            </div>
            <div
                class="md-layout-item md-large-size-100 md-medium-size-100 md-xlarge-size-100 md-small-size-100 md-small-size-100">
                <agent-ticket-list/>
            </div>
        </div>

    </div>
</template>
<script>
    import AgentDetail from './AgentDetail'
    import AssignedApplianceList from './Appliances/AssignedApplianceList'
    import AgentReceiptList from './Receipt/AgentReceiptList'
    import SoldApplianceList from './Appliances/SoldApplianceList'
    import AgentTransactionList from './AgentTransactionList'
    import AgentTicketList from './AgentTicketList'

    export default {
        name: 'Agent',
        data () {
            return {
                agentId: null
            }
        },
        components: {
            AgentTicketList,
            AgentTransactionList,
            SoldApplianceList,
            AgentReceiptList,
            AssignedApplianceList,
            AgentDetail
        },
        created () {
            this.agentId = this.$route.params.id
        }
    }

</script>
<style scoped>

</style>
