<template>

    <!-- book keeping  reports-->
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-size-50">
            <widget
                :id="'book-keeping'"
                :title="'Payment Requests'"
                :paginator="bookKeeping.paginator"
                :search="false"
                :subscriber="subscriberBookKeeping"
                color="orange"
            >


                <md-table>
                    <md-table-row>

                        <md-table-head>ID
                        </md-table-head>
                        <md-table-head>
                            <font-awesome-icon icon icon="barcode"/>
                            Date
                        </md-table-head>
                        <md-table-head>File</md-table-head>


                    </md-table-row>

                    <md-table-row v-for="bk in bookKeeping.list" :key="bk.id">
                        <md-table-cell> {{ bk.id}}</md-table-cell>
                        <md-table-cell> {{ bk.date}}</md-table-cell>
                        <md-table-cell @click="download(bk.id,'bookKeeping')">
                    <span style="cursor:pointer;color: #1b6d85;">
                        Download&nbsp;
                        <font-awesome-icon icon="excel"/>

                    </span>
                        </md-table-cell>


                    </md-table-row>


                </md-table>
            </widget>


            <!-- monthly reports-->
            <widget
                :id="'book-keeping'"
                :title="'Monthly Reports'"
                :paginator="monthlyReport.paginator"
                :search="false"
                :subscriber="subscriberMonthlyReport"
                color="green"
            >


                <md-table id="dt-basic-monthly" class="table table-striped table-bordered table-hover no-footer">

                    <md-table-row>
                        <md-table-head>ID</md-table-head>
                        <md-table-head> Date</md-table-head>
                        <md-table-head> Name</md-table-head>
                        <md-table-head>File</md-table-head>


                    </md-table-row>


                    <md-table-row v-for="mr in monthlyReport.list" :key="mr.id">
                        <md-table-head> {{ mr.id}}</md-table-head>
                        <md-table-head> {{ mr.date}}</md-table-head>
                        <md-table-head> {{ mr.name}}</md-table-head>
                        <md-table-head @click="download(mr.id, 'report')">
                    <span style="cursor:pointer;color: #1b6d85;">
                        Download&nbsp;
                        <img src="https://cdn3.iconfinder.com/data/icons/document-icons-2/30/647714-excel-512.png"
                             width="18px" alt="">
                    </span>
                        </md-table-head>


                    </md-table-row>


                </md-table>
            </widget>
        </div>
        <div class="md-layout-item md-size-50">
            <!-- weekly reports -->
            <widget
                :id="'weekly-report'"
                :title="'Weekly reports'"
                :paginator="weeklyReport.paginator"
                :search="false"
                :subscriber="subscriberWeeklyReport"
                color="red"
            >

                <md-table id="dt-basic-weekly" class="table table-striped table-bordered table-hover no-footer">

                    <md-table-row role="row">
                        <md-table-head>ID</md-table-head>
                        <md-table-head> Date</md-table-head>
                        <md-table-head> Name</md-table-head>
                        <md-table-head>File</md-table-head>

                    </md-table-row>


                    <md-table-row v-for="wr in weeklyReport.list" :key="wr.id">
                        <md-table-cell> {{ wr.id}}</md-table-cell>
                        <md-table-cell> {{ wr.date}}</md-table-cell>
                        <md-table-cell> {{ wr.name}}</md-table-cell>
                        <md-table-cell @click="download(wr.id, 'report')">
                    <span style="cursor:pointer;color: #1b6d85;">
                        Download&nbsp;
                        <img src="https://cdn3.iconfinder.com/data/icons/document-icons-2/30/647714-excel-512.png"
                             width="18px" alt="">
                    </span>
                        </md-table-cell>


                    </md-table-row>


                </md-table>
            </widget>
        </div>

    </div>


</template>

<script>


    import Widget from '../../shared/widget'
    import { BookKeepingList } from '../../classes/BookKeeping/BookKeeping'
    import { EventBus } from '../../shared/eventbus'
    import { Monthly } from '../../classes/Reports/Monthly'
    import { Weekly } from '../../classes/Reports/Weekly'
    import { resources } from '../../resources'

    export default {
        name: 'BookKeepingReportList',
        components: { Widget },
        mounted () {
            EventBus.$emit('bread', this.bcd)
            EventBus.$on('pageLoaded', this.reloadList)

        },
        beforeDestroy () {
            EventBus.$off('pageLoaded', this.reloadList)
        },
        data () {
            return {
                bookKeeping: new BookKeepingList(),
                monthlyReport: new Monthly(),
                weeklyReport: new Weekly(),
                subscriberBookKeeping: 'bookKeeping',
                subscriberMonthlyReport: 'monthlyReport',
                subscriberWeeklyReport: 'weeklyReport',
                bcd: {
                    'Home': {
                        'href': '/'
                    },
                    'Reports': {
                        'href': null
                    },
                },
            }
        },
        methods: {

            reloadList (subscriber, data) {
                if (subscriber === this.subscriberBookKeeping) {
                    this.bookKeeping.updateList(data)
                } else if (subscriber === this.subscriberWeeklyReport) {
                    this.weeklyReport.updateList(data)
                } else if (subscriber === this.subscriberMonthlyReport) {
                    this.monthlyReport.updateList(data)
                }
            },

            endSearching () {
                this.bookKeeping.showAll()
            },
            download (id, reference) {
                if (reference === 'bookKeeping') {
                    window.open(resources.bookKeeping.download + id + '/book-keeping', '_blank')
                } else if (reference === 'report') {
                    window.open(resources.reports.download + id + '/download')
                }
            }
        }
    }
</script>

<style scoped>

</style>
