<template>
    <div class="page-container">


        <widget
            :id="'client-list-widget'"
            :title="$tc('phrases.customerList')"
            :search="true"
            :subscriber="subscriber"
            :button="false"
            :paginator="people.paginator"
            :route_name="'/people'"
            color="green"
        >

                <md-table md-card style="margin-left: 0">
                    <md-table-row>
                        <md-table-head  md-numeric>{{ $tc('words.id') }}</md-table-head>
                        <md-table-head>{{ $tc('words.name') }}</md-table-head>
                        <md-table-head>{{ $tc('words.phone') }}</md-table-head>
                        <md-table-head>{{ $tc('words.city') }}</md-table-head>
                        <md-table-head>{{ $tc('words.meter') }}</md-table-head>
                        <md-table-head>{{ $tc('phrases.lastUpdate') }}</md-table-head>
                    </md-table-row>


                    <md-table-row v-for="client in people.list" :key="client.id" @click="detail(client.id)"
                                  style="cursor:pointer;">
                        <md-table-cell> {{ client.id}}</md-table-cell>
                        <md-table-cell> {{ client.name}} {{client.surname}}</md-table-cell>
                        <md-table-cell v-if="client.addresses.length>0"> {{ client.addresses[0].phone}}
                        </md-table-cell>
                        <md-table-cell class="hidden-xs" v-if="client.addresses.length>0"> {{
                                client.addresses[0].city ?
                                    client.addresses[0].city.name: '-'}}
                        </md-table-cell>
                        <md-table-cell v-if="client.meters.length>0">
                            {{meterList(client.meters)}}
                        </md-table-cell>
                        <md-table-cell v-if="client.meters.length==0">
                            -
                        </md-table-cell>
                        <md-table-cell class="hidden-xs"> {{ dateForHumans( client.lastUpdate) }}</md-table-cell>
                    </md-table-row>

                </md-table>

        </widget>


    </div>
</template>
<script>

import { resources } from '../../resources'
import { Paginator } from '../../classes/paginator'
import { EventBus } from '../../shared/eventbus'
import Widget from '../../shared/widget'
import { People } from '../../classes/people'
import moment from 'moment'

export default {
    name: 'ClientList',
    components: { Widget },
    data () {
        return {
            subscriber: 'client.list',
            people: new People(),
            clientList: null,
            tmpClientList: null,
            paginator: new Paginator(resources.person.search),
            searchTerm: '',
            currentFrom: 0,
            currentTo: 0,
            total: 0,
            currentPage: 0,
            totalPages: 0,
            params:{
                page:1,
                per_page:15
            }
        }
    },

    mounted () {
        EventBus.$on('pageLoaded', this.reloadList)
        EventBus.$on('searching', this.searching)
        EventBus.$on('end_searching', this.endSearching)

    },
    beforeDestroy () {
        EventBus.$off('pageLoaded', this.reloadList)
        EventBus.$off('searching', this.searching)
        EventBus.$off('end_searching', this.endSearching)
    },

    methods: {
        async reloadList (subscriber, data) {
            if (subscriber !== this.subscriber){
                return
            }
            await this.people.updateList(data)
            EventBus.$emit('widgetContentLoaded',this.subscriber, this.people.list.length)
        },
        searching (subscriber, searchTerm) {
            if (subscriber !== this.subscriber){
                return
            }
            this.searchTerm = this.params.search = searchTerm
            this.pushRoute()
        },
        endSearching () {
            delete this.params.search
            this.pushRoute()
        },

        detail (id) {
            this.$router.push({ path: '/people/' + id })
        },
        dateForHumans (date) {
            return moment(date, 'YYYY-MM-DD HH:mm:ss').fromNow()
        },
        pushRoute(){
            this.$router.push({ query: Object.assign(this.params ) }).catch(error => {
                if (error.name !== 'NavigationDuplicated') {
                    throw error
                }
            })
            this.people.search(this.params)
        },
        meterList (meters) {
            let stringified = ''
            for (let i = 0; i < meters.length; i++) {
                if (meters[i].meter === null || meters[i].meter === 'null') {
                    continue
                }
                stringified += meters[i].meter.serial_number
                if (i !== meters.length - 1) {
                    stringified += ', '
                }
            }
            return stringified
        }

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
