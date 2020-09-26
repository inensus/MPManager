<template>
    <div>
        <widget
            :id="id"
            :title="title"
            :paginator="paginator"
            :search="false"
            :subscriber="subscriber"
            :color="'red'"
        >
            <div v-if="list.length>0">
                <md-table v-model="list" md-sort="id" md-sort-order="desc">
                    <md-table-row>
                        <md-table-head v-for="(item, index) in headers" :key="index">{{item}}</md-table-head>
                    </md-table-row>

                    <md-table-row slot="md-table-row" slot-scope="{ item }">
                        <md-table-cell md-sort-by="id" md-label="ID">{{item.id}}</md-table-cell>
                        <md-table-cell md-label="Date">{{item.date}}</md-table-cell>
                        <md-table-cell md-label="Name">{{item.name}}</md-table-cell>
                        <md-table-cell md-label="File">
                            <div @click="download(item.id, '/download')">
                                <font-awesome-icon icon="save" style="cursor: pointer"/>
                                <span> Download</span>

                            </div>

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
import Widget from '../../shared/widget'
import { EventBus } from '../../shared/eventbus'
import { ReportsService } from '../../services/ReportsService'
import NoTableData from '../../shared/NoTableData'

export default {
    name: 'Reports',
    components: {
        Widget, NoTableData
    },
    props: {
        id: null,
        title: null,
        paginator: null,
        subscriber: null
    },
    data () {
        return {
            reportService: new ReportsService(),
            list: [],
            headers: ['ID', 'Date', 'Name', 'File'],
            tableName: 'Report'
        }
    },
    mounted () {

        EventBus.$on('pageLoaded', this.reloadList)

    },
    beforeDestroy () {
        EventBus.$off('pageLoaded', this.reloadList)
    },

    methods: {
        reloadList (subscriber, data) {
            if (subscriber === this.subscriber) {
                this.list = this.reportService.updateList(data)
            }
        },

        download (id, reference) {
            window.open(this.reportService.exportReport(id, reference))
        }
    }

}
</script>

<style scoped>

</style>
