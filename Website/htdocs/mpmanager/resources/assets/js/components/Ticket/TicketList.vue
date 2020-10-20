<template>
    <div>
        <widget :button="true"
                :button-text="'Filter'"
                @widgetAction="() => {filterTicket=true}"
                title="List of Tickets"
                button-icon="filter_list"
                >

            <div class="md-layout-item" v-if="filterTicket" >
                <filtering @filtering="filtered"></filtering>

            </div>
            <div class="md-layout md-gutter">
                <div class="md-layout-item md-size-50 md-medium-size-100">
                    <widget title="Opened Ticket"
                            :subscriber="subscriber.opened"
                            :paginator="ticketService.openedPaginator"
                            color="green">
                        <ticket-item
                            :allow-comment="true"
                            :ticket-list="ticketService.openedList"
                        ></ticket-item>
                    </widget>

                </div>
                <div class="md-layout-item md-size-50 md-medium-size-100">
                    <widget title="Closed Ticket"
                            :subscriber="subscriber.closed"
                            :paginator="ticketService.closedPaginator"
                            color="red">
                        <ticket-item
                            :allow-comment="true"
                            :ticket-list="ticketService.closedList"
                        ></ticket-item>
                    </widget>

                </div>

            </div>
        </widget>


    </div>
</template>

<script>
import Widget from '../../shared/widget'
import TicketItem from './TicketItem'
import { EventBus } from '../../shared/eventbus'
import Filtering from './Filtering'
import { resources } from '../../resources'
import { TicketService } from '../../services/TicketService'
import { Paginator } from '../../classes/paginator'
import { Ticket } from '../../classes/Ticket'

export default {
    name: 'TicketList',
    components: { Filtering, Widget, TicketItem },
    data () {
        return {

            ticketService: new TicketService(),
            loading: true,
            filterTicket: false,
            resetKey: 0,
            subscriber: {
                opened: 'ticketListOpened',
                closed: 'ticketListClosed'
            },

        }
    },
    mounted () {

        EventBus.$on('pageLoaded', this.reloadList)
        EventBus.$on('listChanged', () => {

            this.resetKey += 1
            this.ticketService.openedPaginator = new Paginator(resources.ticket.list + '?status=0')
            this.ticketService.closedPaginator = new Paginator(resources.ticket.list + '?status=1')

        })
        window.Echo.private('histories').listen('HistoryEvent', event => {
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
                            x['owner'] = { id: data.target.owner_id, name: 'X' }
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
    beforeDestroy () {
        EventBus.$off('pageLoaded', this.reloadList)
    },
    methods: {
        async reloadList (subscriber, data) {
            if (subscriber !== 'ticketListOpened' && subscriber !== 'ticketListClosed'){
                return
            }
            await this.ticketService.updateList(data, subscriber)
            EventBus.$emit('widgetContentLoaded',this.subscriber.opened,this.ticketService.openedList.length)
            EventBus.$emit('widgetContentLoaded',this.subscriber.closed,this.ticketService.closedList.length)
        },
        filtered (data) {
            this.ticketService.openedPaginator.setPaginationBaseUrl(
                resources.ticket.list + '?status=0' + data
            )
            this.ticketService.openedPaginator.loadPage(1).then(response => {
                this.reloadList(this.subscriber.opened, response.data)
            })
            this.ticketService.closedPaginator.setPaginationBaseUrl(
                resources.ticket.list + '?status=1' + data
            )
            this.ticketService.closedPaginator.loadPage(1).then(response => {
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

    .no-ticket {
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
