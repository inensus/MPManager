<template>
    <div>
        <widget
            :id="id"
            :title="title"
            :paginator="paginator"
            :search="false"
            :subscriber="subscriber"

        >
                <md-table v-model="list" md-sort="id" md-sort-order="desc">
                    <md-table-row>
                        <md-table-head v-for="(item, index) in headers" :key="index">{{item}}</md-table-head>
                    </md-table-row>

                    <md-table-row slot="md-table-row" slot-scope="{ item }">
                        <md-table-cell md-sort-by="id" :md-label="$tc('words.id')">{{item.id}}</md-table-cell>
                        <md-table-cell :md-label="$tc('words.date')">{{item.date}}</md-table-cell>
                        <md-table-cell :md-label="$tc('words.name')">{{item.name}}</md-table-cell>
                        <md-table-cell :md-label="$tc('words.file')">
                            <div  style="cursor: pointer;" @click="download(item.id, '/download')">
                                <md-icon>save</md-icon>
                                <span> {{ $tc('words.download') }}</span>
                            </div>
                        </md-table-cell>
                    </md-table-row>

                </md-table>
        </widget>
    </div>

</template>

<script>
import Widget from '../../shared/widget'
import { EventBus } from '../../shared/eventbus'
import { ReportsService } from '../../services/ReportsService'

export default {
    name: 'Reports',
    components: {
        Widget
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
            headers: [this.$tc('words.id'), this.$tc('words.date'), this.$tc('words.name'), this.$tc('words.file')],
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
            if (subscriber !== this.subscriber) {
                return
            }
            this.list = this.reportService.updateList(data)
            EventBus.$emit('widgetContentLoaded',this.subscriber,this.reportService.list.length)
        },

        download (id, reference) {
            window.open(this.reportService.exportReport(id, reference))
        }
    }

}
</script>

<style scoped>

</style>
