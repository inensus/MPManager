<template>
    <div>
        <AddAgent :addAgent="addNewAgent"/>
        <widget
            :title="'Agents'"
            :search="true"
            :subscriber="subscriber"
            :button="true"
            button-text="New Agent"
            :callback="() => {addNewAgent=true}"
            :paginator="agentService.paginator"
            color="orange"
        >
            <div v-if="agentService.list.length>0">
                <md-table>
                    <md-table-row>
                        <md-table-head v-for="(item, index) in headers" :key="index">{{item}}</md-table-head>
                    </md-table-row>
                    <md-table-row v-for="(agent,index) in agentService.list" style="cursor:pointer;" :key="agent.id"
                                  @click="detail(agent.id)">

                        <md-table-cell> {{ agent.id}}
                        </md-table-cell>

                        <md-table-cell> {{ agent.name}}
                        </md-table-cell>
                        <md-table-cell> {{ agent.email}}
                        </md-table-cell>
                        <md-table-cell> {{ agent.miniGrid}}
                        </md-table-cell>
                        <md-table-cell> {{ agent.balance}}
                        </md-table-cell>
                    </md-table-row>
                </md-table>
            </div>
            <div v-else>
                <no-table-data :headers="headers" :tableName="tableName"/>
            </div>

        </widget>

    </div>

</template>
<script>


    import { EventBus } from '../../shared/eventbus'
    import Widget from '../../shared/widget'
    import { AgentService } from '../../services/AgentService'
    import AddAgent from '../../components/Agent/NewAgent'
    import NoTableData from '../../shared/NoTableData'

    const debounce = require('debounce')

    export default {
        name: 'AgentList',
        components: { Widget, AddAgent, NoTableData },
        data () {
            return {
                subscriber: 'agent-list',
                addNewAgent: false,
                agentService: new AgentService(),
                searchTerm: '',
                headers: ['ID', 'Name', 'Email', 'MiniGrid', 'Balance'],
                tableName: 'Agent Transaction'
            }
        },

        mounted () {

            EventBus.$on('pageLoaded', this.reloadList)
            EventBus.$on('searching', this.searching)
            EventBus.$on('end_searching', this.endSearching)
            EventBus.$on('agentAdded', () => {
                this.agentService.showAll()
            })
            EventBus.$on('closed', () => {
                this.addNewAgent = false
            })
        },
        beforeDestroy () {
            EventBus.$off('pageLoaded', this.reloadList)
            EventBus.$off('searching', this.searching)
            EventBus.$off('end_searching', this.endSearching)
        },

        methods: {
            reloadList (subscriber, data) {

                if (subscriber !== this.subscriber) return
                this.agentService.updateList(data)
            },
            detail (id) {
                this.$router.push({ path: '/agents/' + id })
            },
            searching (searchTerm) {
                this.agentService.search(searchTerm)
            },
            endSearching () {
                this.agentService.showAll()
            },
            clearSearch () {
                this.searchTerm = ''
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


<style lang="scss" scoped>
    .md-app {
        min-height: 100vh;
        border: 1px solid rgba(#000, .12);
    }

    // Demo purposes only
    .md-drawer {
        width: 230px;
        max-width: calc(100vw - 125px);
    }

</style>
