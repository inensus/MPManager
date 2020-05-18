<template>
    <div>
        <NewConnectionType/>
        <widget
            :id="'connection-types-list'"
            :title="'Connection Types'"
            :paginator="connectionTypes.paginator"
            :subscriber="subscriber"
            :button="true"
            :button-text="'New Connection Type'"
            :callback="addNew"
        >
            <md-table md-card style="margin-left: 0">
                <md-table-row>
                    <md-table-head>ID</md-table-head>
                    <md-table-head>Name</md-table-head>
                </md-table-row>
                <md-table-row v-for="type in connectionTypes" :key="type.id">
                    <md-table-cell> {{ type.id}}</md-table-cell>
                    <md-table-cell> {{ type.name}}</md-table-cell>

                </md-table-row>

            </md-table>


        </widget>
    </div>

</template>

<script>
    import Widget from '../../shared/widget'
    import {EventBus} from '../../shared/eventbus'
    import TableList from '../../shared/TableList'
    import {ConnectionService} from '../../services/ConnectionsService'
    import NewConnectionType from './NewConnectionType'

    export default {
        name: 'ConnectionTypesList',
        components: {TableList, Widget, NewConnectionType},
        mounted() {
            EventBus.$on('pageLoaded', this.reloadList)
            EventBus.$on('searching', this.searching)
            EventBus.$on('end_searching', this.endSearching)
            EventBus.$on('connectionTypeAdded', this.getConnectionTypes)

            this.getConnectionTypes()
        },

        data() {
            return {
                connectionTypeService: new ConnectionService('types'),
                subscriber: 'connection-types-list',
                connectionTypes: [],
            }
        },
        methods: {
            reloadList(subscriber, data) {
                if (subscriber !== this.subscriber) return
                this.connectionTypes = this.connectionTypeService.updateList(data)
            },
            async getConnectionTypes() {
                try {
                    this.connectionTypes = await this.connectionTypeService.getConnectionTypes()

                } catch (e) {

                    this.alertNotify('error', e.message)
                }
            },
            addNew() {
                EventBus.$emit('showNewConnectionType')
            },

        },
        alertNotify(type, message) {
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message
            })
        },
    }
</script>

<style scoped>

</style>
