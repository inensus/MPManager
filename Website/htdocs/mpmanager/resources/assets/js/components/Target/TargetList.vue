<template>
    <div>
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
        />

        <widget
            :id="'target-list'"
            :title="'List of Targets'"
            :button="true"
            :buttonText="'New Target'"
            :paginator="targets.paginator"
            :subscriber="subscriber"
            :callback="newTarget"
        >

            <!-- list of targets -->
            <div v-if="targets.list.length>0">

                <md-table>

                    <md-table-row>
                        <md-table-head :colspan="expandedRow>=0 ? 3:1">Period</md-table-head>
                        <md-table-head>For</md-table-head>
                        <md-table-head>Sub Targets</md-table-head>
                    </md-table-row>


                    <template v-for="(target,index) in targets.list">
                        <md-table-row>
                            <md-table-cell :colspan="expandedRow>=0 ? 3:1">{{ target.target.targetDate}}</md-table-cell>
                            <md-table-cell>{{ target.target.owner.name}} ({{target.owner}})</md-table-cell>
                            <md-table-cell v-if="target.target.subTargets.length>0">

                                <i v-if="index === expandedRow" @click="collapseTarget()"
                                   class="fa fa-minus-square-o">
                                    Collapse</i>
                                <i v-else @click="expandTarget(index)" class="fa fa-plus-square-o "> Expand</i>

                            </md-table-cell>
                            <md-table-cell v-else>-</md-table-cell>

                        </md-table-row>
                        <md-table-row v-if="index === expandedRow"
                                      v-for="(subTarget, subIndex) in target.target.subTargets"
                                      :key="subIndex">
                            <md-table-cell>{{subTarget.connections.name}}</md-table-cell>
                            <md-table-cell>Revenue</md-table-cell>
                            <md-table-cell> {{subTarget.revenue}}</md-table-cell>
                            <md-table-cell>New connections</md-table-cell>
                            <md-table-cell>{{subTarget.newConnections}}</md-table-cell>
                        </md-table-row>

                    </template>

                </md-table>
            </div>
            <div v-else>
                <no-table-data :headers="headers" :tableName="tableName"/>
            </div>
        </widget>
    </div>

</template>

<script>
    import TableList from '../../shared/TableList'
    import Widget from '../../shared/widget'
    import { Targets } from '../../classes/target/Targets'
    import { EventBus } from '../../shared/eventbus'
    import NoTableData from '../../shared/NoTableData'

    export default {
        name: 'TargetList',
        components: {
            Widget,
            TableList, NoTableData
        },
        computed: {
            expandedTarget: function () {
                return this.expandedRow !== null ? this.expandedRow : -1
            }
        },
        created () {
        },
        mounted () {
            EventBus.$emit('bread', this.bcd)
            EventBus.$on('pageLoaded', this.reloadList)
            EventBus.$on('searching', this.searching)
            EventBus.$on('end_searching', this.endSearching)
        },
        data () {
            return {
                expandedRow: null,
                targets: new Targets(),
                subscriber: 'targets',
                headers: ['Period', 'For', 'Sub Targets'],
                tableName: 'Target',
            }
        },
        methods: {
            reloadList (subscriber, data) {
                if (subscriber !== this.subscriber) return
                this.targets.updateList(data)
            },
            expandTarget (index) {
                let subTarget = this.targets.targetAtIndex(index)
                if (subTarget !== null) {
                    this.expandedRow = index
                }

            },
            collapseTarget () {
                this.expandedRow = null
            },
            newTarget () {
                this.$router.push({ path: '/targets/new' })
            }

        }
    }
</script>

<style scoped>

</style>
