<template>
    <widget
        :subscriber="subscriber"
        :title="$tc('phrases.agentTicket',1)"
        :paginator="agentTicketService.paginator"
    >
        <md-list
            class="md-triple-line ticket-area"
            :data-id="ticket.id"
            v-for="ticket in agentTicketService.list"
            :key="ticket.id"
        >
            <md-list-item>
                <div class="Ticket-Are-Grid md-layout md-size-100">
                    <div class="md-layout-item md-size-100">
                        <h2 style="display:inline-flex;">
                            <span class="semi-bold" v-text="ticket.name"></span>
                            <div v-if="!ticket.closed">
                                <md-icon>lock</md-icon>
                            </div>
                        </h2>
                    </div>


                    <div class="md-layout-item md-size-100" style="float: right;">
                        <small v-if="ticket.category">
                            <md-icon>style</md-icon>
                            {{ticket.category.label_name}}
                        </small>
                        <small v-else>-</small>
                        <br/>
                    </div>
                    <div class="md-layout-item md-size-100 t-text-area">
                        <p class="t-text" v-text="ticket.description">

                        </p>


                        <div class="t-date">
                            <md-icon>access_time</md-icon>
                            {{ticket.created}}
                        </div>
                    </div>

                    <div class="md-layout-item md-size-100">
                        <em class="pull-right-label-primary" style="cursor:pointer">
                            <small @click="showComment(ticket)">{{ $tc('words.comment',1) }}</small>

                            {{ticket.comments.length}}
                        </em>
                    </div>
                    <div class="md-layout-item md-size-100">
                        <div v-if="ticket.newComment">

                            <div
                                class="comment-box"
                                v-for="comment in ticket.comments"
                                :key="comment.id"
                            >
                                <md-icon>person</md-icon>
                                {{comment.comment}}
                                <br/>
                                <md-icon>access_time</md-icon>
                                <small>{{comment.date}}</small>
                                <div class="clearfix"></div>
                            </div>

                        </div>
                    </div>


                </div>
            </md-list-item>
        </md-list>

    </widget>

</template>
<script>
import Widget from '../../shared/widget'
import { AgentTicketService } from '../../services/AgentTicketService'
import { EventBus } from '../../shared/eventbus'

export default {
    name: 'AgentTicketList',
    data () {
        return {
            loaded: false,
            agentTicketService: new AgentTicketService(this.agentId),
            subscriber: 'AgentTickets',
        }
    },
    components: {
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
        showComment (ticket) {
            Vue.set(ticket, 'newComment', !ticket.newComment)
        },
    }
}

</script>
<style scoped>
    .comment-box {
        background-color: #f4fff0;
        border-width: 1px;
        border-style: dotted;
        padding: 10px;
        white-space: initial;
    }

    .save-btn {
        background: green;
        color: white !important;
        float: right;
        margin-top: 0.5rem !important;
    }

    .modal-mask {
        position: fixed;
        z-index: 1001;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: table;
        transition: opacity 0.3s ease;
    }

    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }

    .modal-container {
        width: 45%;
        margin: 0px auto;
        padding: 20px 30px;
        background-color: #fff;
        border-radius: 2px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
        transition: all 0.3s ease;
        font-family: Helvetica, Arial, sans-serif;
        max-height: 85%;
        overflow-y: scroll;
    }

    .modal-header h3 {
        margin-top: 0;
        color: #42b983;
    }

    .modal-body {
        margin: 20px 0;
    }

    .modal-default-button {
        float: right;
    }

    /*
             * The following styles are auto-applied to elements with
             * transition="modal" when their visibility is toggled
             * by Vue.js.
             *
             * You can easily play with the modal transition by editing
             * these styles.
             */

    .modal-enter {
        opacity: 0;
    }

    .modal-leave-active {
        opacity: 0;
    }

    .modal-enter .modal-container,
    .modal-leave-active .modal-container {
        -webkit-transform: scale(1.1);
        transform: scale(1.1);
    }

    .red {
        background-color: red;
        color: white;
    }

    .purple {
        background-color: purple;
        color: white;
    }

    .lime {
        background-color: rgb(191, 230, 31);
    }

    .ticket-area {
        border: 0.05px solid lightgray;
        margin-top: 0.4rem;
    }

    .ticket-area:hover {
        background-color: #f3f3f3;
    }

    .pull-right-label-primary {
        background-color: #3276b1;
        float: right;
        color: white;
        margin: 2vh;
        padding: 0.2em 0.6em 0.3em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25em;
    }

    .Ticket-Are-Grid {
        max-width: 100%;
    }

    .new-ticket-modal-container {
        padding: 2rem;
        overflow-y: scroll !important;
    }

    .t-text-area {
        min-width: 100% !important;
        background-color: #fef2dd;
        padding: 10px;
        border-style: dotted;
        border-width: 1px;
    }

    .t-text {
        min-width: 90%;
        white-space: initial;
    }

    .t-date {
        font-size: x-small;
        color: #2a2a2a;
        float: right;

    }
</style>
