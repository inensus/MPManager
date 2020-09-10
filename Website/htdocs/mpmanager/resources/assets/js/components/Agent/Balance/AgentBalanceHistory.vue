<template>
    <widget
        :class="'col-sm-6 col-md-5'"
        :button-text="'Add Balance'"
        :button="true"
        title="Balance Histories"
        :button-color="'red'"
        :callback="()=>{showNewBalance = true}"
        :paginator="agentBalanceHistoryService.paginator"
        :subscriber="subscriber"
        :resetKey="resetKey"
        :show_per_page="true"
    >


        <div>
            <add-agent-balance :addNewBalance="showNewBalance" :agent-id="agentId"/>
            <div v-if="agentBalanceHistoryService.list.length>0">
                <md-table md-sort="id" md-sort-order="asc">
                    <md-table-row>
                        <md-table-head v-for="(item, index) in headers" :key="index">{{item}}</md-table-head>
                    </md-table-row>
                    <md-table-row v-for="(item, index) in agentBalanceHistoryService.list" :key="index">
                        <md-table-cell md-sort-by="id" md-label="ID">{{item.id}}</md-table-cell>
                        <md-table-cell md-label="Type">{{item.type}}</md-table-cell>
                        <md-table-cell md-label="Amount">{{item.amount}}</md-table-cell>
                        <md-table-cell md-label="Date">{{item.createdAt}}</md-table-cell>


                    </md-table-row>
                </md-table>
            </div>
            <div v-else>
                <no-table-data :headers="headers" :tableName="tableName"/>
            </div>

        </div>
    </widget>

</template>
<script>
import { AgentService } from '../../../services/AgentService'
import { AgentBalanceHistoryService } from '../../../services/AgentBalanceHistoryService'
import Widget from '../../../shared/widget'
import { EventBus } from '../../../shared/eventbus'
import AddAgentBalance from './AddBalance'
import NoTableData from '../../../shared/NoTableData'

export default {
    name: 'agentBalanceHistoryList',
    data () {
        return {
            subscriber: 'agent-balance-histories',
            agentService: new AgentService(),
            agentBalanceHistoryService: new AgentBalanceHistoryService(this.agentId),
            showNewBalance: false,
            agent: {},
            newBalance: {},
            loading: false,
            resetKey: 0,
            headers: ['ID', 'Type', 'Amount', 'Date'],
            tableName: 'Agent Balance History'
        }
    },
    props: {
        agentId: {
            default: null
        }
    },

    mounted () {
        EventBus.$on('balanceAdded', () => {
            this.showNewBalance = false
            this.resetKey += 1
        })

        EventBus.$on('addBalanceClosed', () => {
            this.showNewBalance = false

        })
        EventBus.$on('receiptAdded', () => {
            this.resetKey += 1
        })
        EventBus.$on('pageLoaded', this.reloadList)

    },
    beforeDestroy () {
        EventBus.$off('addBalanceClosed', () => {
            this.showNewBalance = false
        })
        EventBus.$off('pageLoaded', this.reloadList)
    },
    components: {
        NoTableData,
        AddAgentBalance,
        Widget
    },
    methods: {

        reloadList (subscriber, data) {
            if (subscriber !== this.subscriber) return
            this.agentBalanceHistoryService.updateList(data)
        },
        async saveBalance () {
            let validator = await this.$validator('Balance-Form')
            if (validator) {
                console.log(validator)
            }
        },
        hide () {
            this.showNewAppliance = false
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
