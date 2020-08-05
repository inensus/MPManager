<template>
    <div>
        <widget :button="true"
                :button-text="'Filter'"
                :callback="() => {filterTicket=true}"
                color="green"
                title="List of Tickets">

                <div class="md-layout-item" v-if="filterTicket" >
                    <filtering @filtering="filtered"></filtering>

                </div>
                <div class="md-layout md-gutter">
                    <div class="md-layout-item md-size-50">
                        <div class="ticket-list-card-l">
                            <md-card>
                                <md-card-header><span class="o-ticket">Opened Tickets</span></md-card-header>
                                <md-card-content>
                                    <div
                                        class="dd"
                                        id="nestable-opened"
                                        style="min-height: 70vh !important; padding-bottom: 70px;"
                                    >

                                        <ticket-item
                                            :allow-comment="true"
                                            :key="ticket.id"
                                            :ticket="ticket"
                                            v-for="ticket in tickets.openedList"
                                        ></ticket-item>
                                        <md-list class="md-triple-line ticket-area">
                                            <md-list-item class="text-center no-ticket"
                                                          data-v-aec15928
                                                          data-v-e03de5b4


                                                          v-if="tickets.openedList.length === 0 && !loading">
                                                <h4 data-v-aec15928 data-v-e03de5b4 style="font-weight: 500;">
                                                    No opened tickets
                                                    found
                                                </h4>
                                            </md-list-item>
                                        </md-list>

                                    </div>

                                    <paginate
                                        :paginatorReference="tickets.openedPaginator"
                                        :subscriber="subscriber.opened"
                                        style="position: absolute; bottom:0; width: 100%"
                                    ></paginate>
                                </md-card-content>
                            </md-card>
                        </div>
                    </div>
                    <div class="md-layout-item md-size-50">
                        <div class="ticket-list-card-r">
                            <md-card>
                                <md-card-header>
                                    <span class="c-ticket">Closed Tickets</span>
                                </md-card-header>
                                <md-card-content>
                                    <div
                                        class="dd"
                                        id="nestable-closed"
                                        style="min-height: 70vh !important; padding-bottom: 70px;"
                                    >

                                        <ticket-item
                                            :allow-comment="false"
                                            :key="ticket.id"
                                            :ticket="ticket"
                                            v-for="ticket in tickets.closedList"
                                        ></ticket-item>

                                    </div>
                                    <paginate
                                        :paginatorReference="tickets.closedPaginator"
                                        :subscriber="subscriber.closed"
                                        style="position: absolute; bottom:0; width: 100%"
                                    ></paginate>
                                </md-card-content>
                            </md-card>
                        </div>
                    </div>
                </div>




        </widget>

        <notifications group="information" position="top right" type="success"></notifications>
    </div>
</template>

<script>
    import Widget from '../../shared/widget'
    import Paginate from '../../shared/Paginate'
    import TicketItem from './TicketItem'
    import {Tickets, Ticket} from '../../classes/Ticket'
    import {EventBus} from '../../shared/eventbus'
    import Filtering from './Filtering'
    import {resources} from '../../resources'

    export default {
        name: 'TicketList',
        components: {Filtering, Widget, Paginate, TicketItem},
        data() {
            return {
                tickets: new Tickets(),
                loading: true,
                filterTicket: false,
                subscriber: {opened: 'ticketListOpened', closed: 'ticketListClosed'},
                bcd: {
                    Home: {
                        href: '/'
                    },
                    Tickets: {
                        href: null
                    }
                }
            }
        },
        mounted() {
            EventBus.$emit('bread', this.bcd)
            EventBus.$on('pageLoaded', this.reloadList)
            EventBus.$on('searching', this.searching)
            EventBus.$on('end_searching', this.endSearching)

            window.Echo.private(`histories`).listen('HistoryEvent', event => {
                let data = event.historyModel
                //its a ticket event so we are interested about the content
                if (data.target_type === 'Inensus\\Ticket\\Models\\Ticket') {
                    if (data.action !== 'create') {
                        // the ticket is been updated
                        if (data.field === 'closed') {
                            //ticket is been closed
                            //get ticket details if the ticket is not on the recently opened tickets
                            let foundTicket = this.tickets.openedList.filter(ticket => {
                                return ticket.id === data.target.ticket_id
                            })
                            if (foundTicket.length === 0) {
                                //ticket is not been displayed in the opened list.
                                let t = new Ticket()
                                t.getDetail(data.target.ticket_id).then(response => {
                                    this.tickets.closedList.unshift(t.fromJson(response.data))
                                    if (this.tickets.closedList.length > 5) {
                                        this.tickets.closedList.pop()
                                    }
                                })
                            } else {
                                this.tickets.closedList.unshift(foundTicket[0])
                                if (this.tickets.closedList.length > 5) {
                                    this.tickets.closedList.pop()
                                }
                                this.tickets.openedList = this.tickets.openedList.filter(
                                    ticket => {
                                        return ticket.id !== data.target.ticket_id
                                    }
                                )
                            }
                        } else {
                            //show alert box what recently happened
                            this.$notify({
                                group: 'information',
                                title: 'New ticketing action',
                                text: data.content,
                                duration: 15000
                            })
                            window.audio.play()
                        }
                    } else {
                        //created event, add the ticket to the list.
                        let t = new Ticket()

                        t.getDetail(data.target.ticket_id).then(response => {

                            let x = t.fromJson(response.data)
                            if (typeof x['owner'] === 'undefined') {
                                x['owner'] = {id: data.target.owner_id, name: 'X'}
                            }
                            this.tickets.openedList.unshift(x)
                            if (this.tickets.openedList.length > 5) {
                                this.tickets.openedList.pop()
                            }
                        })
                    }
                }
            })
        },
        beforeDestroy() {
            EventBus.$off('pageLoaded', this.reloadList)
            EventBus.$off('searching', this.searching)
            EventBus.$off('end_searching', this.endSearching)
        },
        methods: {
            reloadList(subscriber, data) {

                if (
                    subscriber !== 'ticketListOpened' &&
                    subscriber !== 'ticketListClosed'
                )
                    return
                this.loading = false
                this.tickets.updateList(data, subscriber)
            },
            searching(searchTerm) {
            },
            endSearching() {
            },
            filtered(data) {
                this.tickets.openedPaginator.setPaginationBaseUrl(
                    resources.ticket.list + '?status=0' + data
                )
                this.tickets.openedPaginator.loadPage(1).then(response => {
                    this.reloadList(this.subscriber.opened, response.data)
                })
                this.tickets.closedPaginator.setPaginationBaseUrl(
                    resources.ticket.list + '?status=1' + data
                )
                this.tickets.closedPaginator.loadPage(1).then(response => {
                    this.reloadList(this.subscriber.closed, response.data)
                })
            }
        }
    }
</script>

<style scoped>
    .ticket-list-card-r {
       margin-inline-end: 2vh;
        margin-top: 2vh;

    }
    .ticket-list-card-l {
        margin-inline-start: 2vh;
        margin-top: 2vh;

    }
    .no-ticket{
        padding: 30px;
        margin-top: 5vh;
        background: #8c8c8c;
        color: white;
    }

    .o-ticket {
        background-color: #8eb18e;
        padding: 5px;
        font-size: larger;
        font-weight: bold;
        color: white;
    }

    .c-ticket {
        background-color: #9a0325;
        padding: 5px;
        font-size: larger;
        font-weight: bold;
        color: white;
    }
</style>
