<template>
    <div>
        <NewConnectionGroup/>
        <widget
            :id="'connection-Groups-list'"
            :title="'Connection Groups'"
            :paginator="connectionGroups.paginator"
            :subscriber="subscriber"
            :button="true"
            :button-text="'New Connection Group'"
            :callback="addNew"
        >
            <md-table md-card style="margin-left: 0">
                <md-table-row>
                    <md-table-head>ID</md-table-head>
                    <md-table-head>Name</md-table-head>
                </md-table-row>
                <md-table-row v-for="Group in connectionGroups" :key="Group.id">
                    <md-table-cell> {{ Group.id}}</md-table-cell>
                    <md-table-cell> {{ Group.name}}</md-table-cell>

                </md-table-row>

            </md-table>


        </widget>
    </div>

</template>

<script>
    import Widget from '../../shared/widget'
    import {EventBus} from '../../shared/eventbus'
    import TableList from '../../shared/TableList'
    import {ConnectionGroupService} from '../../services/ConnectionGroupService'
    import NewConnectionGroup from './NewConnectionGroup'

    export default {
        name: 'ConnectionGroupsList',
        components: {TableList, Widget, NewConnectionGroup},
        mounted() {
            EventBus.$on('pageLoaded', this.reloadList)
            EventBus.$on('searching', this.searching)
            EventBus.$on('end_searching', this.endSearching)
            EventBus.$on('connectionGroupAdded', this.getConnectionGroups)

            this.getConnectionGroups()
        },

        data() {
            return {
                connectionGroupService: new ConnectionGroupService(),
                subscriber: 'connection-Groups-list',
                connectionGroups: [],
            }
        },
        methods: {
            reloadList(subscriber, data) {
                if (subscriber !== this.subscriber) return
                this.connectionGroups = this.connectionGroupService.updateList(data)
            },
            async getConnectionGroups() {
                try {
                    this.connectionGroups = await this.connectionGroupService.getConnectionGroups()

                } catch (e) {

                    this.alertNotify('error', e.message)
                }
            },
            addNew() {
                EventBus.$emit('showNewConnectionGroup')
            },

        },
        alertNotify(Group, message) {
            this.$notify({
                group: 'notify',
                Group: Group,
                title: Group + ' !',
                text: message
            })
        },
    }
</script>

<style scoped>

</style>
