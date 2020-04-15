<template>
    <widget
        :id="'connection-types-list'"
        :title="'Connection Types'"
        :paginator="connections.paginator"
        :subscriber="subscriber"
        :button="true"
        :button-text="'New Connection Type'"
        :callback="addNew"
    >
        <table-list>
            <thead slot="header">
            <tr>
                <th>ID</th>
                <th>Name</th>
            </tr>
            </thead>

            <tbody slot="body">
            <tr v-for="connection in connections.list" :key="connection.id">
                <td v-text="connection.id"></td>
                <td v-text="connection.name"></td>
            </tr>
            </tbody>
        </table-list>
    </widget>
</template>

<script>
    import Widget from '../../shared/widget'
    import { ConnectionTypes } from '../../classes/connection/ConnectionTypes'
    import { EventBus } from '../../shared/eventbus'
    import TableList from '../../shared/TableList'

    export default {
        name: 'ConnectionTypesList',
        components: { TableList, Widget },
        mounted () {
            EventBus.$on('pageLoaded', this.reloadList)
            EventBus.$on('searching', this.searching)
            EventBus.$on('end_searching', this.endSearching)
        },
        data () {
            return {
                subscriber: 'connection-types-list',
                connections: new ConnectionTypes(),
            }
        },
        methods: {
            reloadList (subscriber, data) {
                if (subscriber !== this.subscriber) return
                this.connections.updateList(data)
            },
            addNew () {
                this.$router.push({ path: '/connection-types/new' })
            }
        }
    }
</script>

<style scoped>

</style>
