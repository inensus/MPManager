<template>
    <widget
        :subscriber="subscriber"
        :title="$tc('phrases.agentTicket',1)"
        :paginator="agentTicketService.paginator"
        color="green"
    >
        <ticket-item
            :allow-comment="true"
            :allow-lock="false"
            :table-heads="tableHeads"
            :ticket-list="agentTicketService.list"

        >
        </ticket-item>

    </widget>

</template>
<script>
import Widget from '../../shared/widget'
import { AgentTicketService } from '../../services/AgentTicketService'
import { EventBus } from '../../shared/eventbus'
import moment from 'moment'
import { UserTickets } from '../../classes/person/ticket'
import TicketItem from '../../shared/TicketItem'

export default {
    name: 'AgentTicketList',
    data () {
        return {
            loaded: false,
            agentTicketService: new AgentTicketService(this.agentId),
            subscriber: 'AgentTickets',
            showTicket: null,
            tableHeads:[this.$tc('words.subject'), this.$tc('words.category'),
                this.$tc('words.status'), this.$tc('words.date')],
            tickets: new UserTickets(this.$store.getters.person.id),
        }
    },
    components: {
        TicketItem,
        Widget
    },
    props: {
        agentId: {
            default: null
        }
    },
    mounted () {
        EventBus.$on('pageLoaded', this.reloadList)
    },
    beforeDestroy () {
        EventBus.$off('pageLoaded', this.reloadList)
    },
    methods: {
        async reloadList (subscriber, data) {
            if (subscriber !== this.subscriber){
                return
            }
            await  this.agentTicketService.updateList(data)
            this.loaded = true
            EventBus.$emit('widgetContentLoaded',this.subscriber,this.agentTicketService.list.length)

        },
        formatDate(date){
            let d = new Date(date)
            return d.toLocaleDateString()
        },
        getTimeAgo(date){
            return moment(date).fromNow()

        },
        closeTicket(ticket) {
            ticket.close()
        },
        saveComment(ticket) {
            let comment = {
                comment: ticket.commentMessage,
                date: new Date(),
                fullName: this.$store.getters.admin.name,
                username: this.$store.getters.admin.email,
                cardId: ticket.id
            }
            ticket.newComment = false
            ticket.comments.push(comment)

            this.tickets.newComment(comment)
        },
        openTicket(index){
            if(this.showTicket === index){
                this.showTicket = null
            }else{
                this.showTicket = index
            }

        },
        showComment (ticket) {
            Vue.set(ticket, 'newComment', !ticket.newComment)
        },
    }
}

</script>
<style scoped>

</style>
