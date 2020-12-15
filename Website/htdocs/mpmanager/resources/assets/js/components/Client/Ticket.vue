<template>
    <div class="col-sm-12">
        <widget
            :subscriber="subscriber"
            color="green"
            :title="$tc('phrases.userTicket',2)"
            :paginator="tickets.paginator"
            :button="true"
            :button-text="$tc('phrases.newTicket')"
            @widgetAction="openModal"
            :resetKey="resetKey"
        >

                <md-table>
                    <md-table-row>
                        <md-table-head class="md-subheader">{{ $tc('words.subject') }}</md-table-head>
                        <md-table-head >{{ $tc('words.category') }}</md-table-head>
                        <md-table-head>{{ $tc('words.status') }}</md-table-head>
                        <md-table-head>{{ $tc('words.date') }}</md-table-head>
                    </md-table-row>
                    <template v-for="(ticket,index) in tickets.list" >
                        <md-table-row @click="openTicket(index)" :key="index">
                            <md-table-cell><md-icon>{{showTicket === index ? 'keyboard_arrow_down' : 'keyboard_arrow_right'}}</md-icon>{{ticket.name}}</md-table-cell>
                            <md-table-cell v-if="ticket.category">{{ticket.category.label_name}}</md-table-cell>
                            <md-table-cell v-else>-</md-table-cell>
                            <md-table-cell><span  :class="[ticket.status === 0 ? 'open-ticket': 'closed-ticket']">{{ticket.status === 0 ? "Open" : "Closed"}}</span></md-table-cell>
                            <md-table-cell>{{formatDate(ticket.created_at)}}</md-table-cell>
                        </md-table-row>
                        <md-table-row v-if="showTicket === index" :key="index">
                            <md-table-cell colspan="4">

                                <div class="ticket-desc">
                                    <div class="md-layout md-gutter md-size-100">
                                        <div class="md-layout-item md-size-70">
                                            <span class="md-subheader">{{ $tc('phrases.ticketDetails') }}</span>
                                        </div>
                                        <div class="md-layout-item md-size-30">
                                            <div class="md-layout-item md-size-100">
                                                <em class="pull-right-label-primary" style="cursor:pointer">
                                                    <small @click="showComment(ticket)">Comments</small>
                                                    {{ticket.commentCount()}}
                                                </em>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="md-layout-item md-size-100 t-text-area">
                                        <p class="t-text" v-text="ticket.description"></p>
                                    </div>

                                    <div class="md-layout-item md-size-100">
                                        <div  v-if="ticket.newComment">

                                            <div
                                                class="comment-item"
                                                v-for="comment in ticket.comments"
                                                :key="comment.id"
                                            >
                                                <md-icon>person</md-icon> {{comment.comment}}
                                                <br/>
                                                <md-icon>access_time</md-icon>
                                                <small>{{getTimeAgo(comment.date)}}</small>
                                                <div class="clearfix"></div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="md-layout-item md-size-95 new-comment-area"  v-if="ticket.newComment">

                                        <md-field>
                                            <label for="">{{ $tc('phrases.newComment') }}</label>
                                            <md-textarea md-autogrow v-model="ticket.commentMessage"></md-textarea>
                                            <md-button
                                                type="submit"
                                                class="md-primary md-dense"
                                                @click="saveComment(ticket)"
                                            >{{ $tc('words.save') }}</md-button>
                                        </md-field>

                                    </div>
                                </div>
                            </md-table-cell>

                        </md-table-row>
                    </template>

                </md-table>

        </widget>


        <md-dialog :md-active.sync="showModal">
            <md-dialog-title>{{ $tc('phrases.newTicket') }}</md-dialog-title>
            <md-dialog-content class="md-scrollbar">
                <form novalidate class="md-layout md-gutter" >

                        <div class="md-layout-item md-size-100 ">
                            <md-field name="title">
                                <label for="title">{{ $tc('words.title') }}</label>
                                <md-input type="text" v-model="newTicket.title" id="title" name="title" />
                            </md-field>
                        </div>

                        <div class="md-layout-item md-size-100 " style="display:inline-flex;">

                            <md-datepicker
                                name="ticketDueDate"
                                md-immediately
                                id="ticketDueDate"
                                v-model="newTicket.dueDate"> <label for="ticketDueDate">{{ $tc('phrases.dueDate') }}</label></md-datepicker>


                        </div>


                        <div class="md-layout-item md-size-100 ">

                            <md-field name="ticketPriority">
                                <label for="ticketPriority">{{ $tc('words.category') }}</label>
                                <md-select v-model="newTicket.label" name="ticketPriority" id="ticketPriority">
                                    <md-option value="0" disabled>-- {{ $tc('words.select') }} --</md-option>
                                    <md-option
                                        v-for="(label,index) in labels"
                                        :value="label.id"
                                        :key="index"

                                    >{{label.label_name}}</md-option>
                                </md-select>
                            </md-field>
                        </div>

                        <div  class="md-layout-item md-size-100 ">
                            <md-field name="ticketAssignedTo">
                                <label for="ticketAssignedTo">{{ $tc('phrases.assignTo',0) }}</label>
                                <md-select name="ticketAssignedTo" id="ticketAssignedTo" v-model="newTicket.assignedPerson">
                                    <md-option disabled selected>{{ $tc('phrases.noOne') }}</md-option>
                                    <md-option v-for="user in users" :value="user.id" :key="user.id">{{user.name}}</md-option>
                                </md-select>

                            </md-field>
                        </div>


                        <div class="md-layout-item md-size-100">

                            <md-field>
                                <label for="description">{{ $tc('words.description') }}</label>
                                <md-textarea type="text" id="description" name="description" />
                            </md-field>
                        </div>
                        <md-dialog-actions class="md-layout-item md-size-100" >
                        <md-button class="md-accent" @click="closeModal()">{{ $tc('words.close') }}</md-button>

                        <md-button class="md-primary btn-lg" @click="saveTicket()">{{ $tc('words.save') }}</md-button>

                    </md-dialog-actions>
                </form>
            </md-dialog-content>


        </md-dialog>
    </div>
</template>

<script>
import Widget from '../../shared/widget'
import { Ticket, UserTickets } from '../../classes/person/ticket'
import { resources } from '../../resources'
import { EventBus } from '../../shared/eventbus'
import { Paginator } from '../../classes/paginator'
import moment from 'moment'
import { TicketUserService } from '../../services/TicketUserService'
import { TicketLabelService} from '../../services/TicketLabelService'

export default {
    name: 'Ticket',
    components: {  Widget },
    data() {
        return {
            ticketLabelService: new TicketLabelService(),
            ticketUserService: new TicketUserService(),
            subscriber: 'userTickets',
            tickets: new UserTickets(this.$store.getters.person.id),
            showPriceInput: false,
            paginator: null,
            // tickets: [],
            currentPage: 0,
            totalPages: 0,
            perPage: 0,
            showTicket: null,
            currentFrom: 0,
            currentTo: 0,
            total: 0,
            loaded: false,
            showModal: false,
            users: {},
            labels: [],
            newTicket: {
                title: '',
                description: '',
                dueDate: null,
                label: null,
                assignedPerson: null,
                owner_id: this.$store.getters.person.id, //current person id
                owner_type: 'person',
                creator: '',
                outsourcing: 0,

            },
            resetKey: 0,
        }
    },
    beforeDestroy() {
        EventBus.$off('pageLoaded', this.reloadList)
    },

    mounted() {
        EventBus.$on('pageLoaded', this.reloadList)
        //this.getTickets();
        this.getUsers()
        this.getLabels()
        this.getCreator()
        this.$on('close', function() {
            this.showModal = false
        })
    },
    methods: {
        getTimeAgo(date){
            return moment(date).fromNow()

        },
        formatDate(date){
            let d = new Date(date)
            return d.toLocaleDateString()
        },
        openTicket(index){
            if(this.showTicket === index){
                this.showTicket = null
            }else{
                this.showTicket = index
            }

        },
        ticketCategoryChange(label) {
            // is needed for outsourcing.

            let category = this.labels.filter(l => {
                return l.id == label.target.value
            })

            if (category.length === 0) {
                return
            }

            category = category[0]

            if (category.out_source === 1) {
                this.showPriceInput = true
            }
        },
        reloadList(sub, data) {
            if (sub !== this.subscriber) return
            this.tickets.updateList(data)
            EventBus.$emit('widgetContentLoaded',this.subscriber,this.tickets.list.length)
        },
        closeModal() {
            this.showModal = false
        },
        openModal() {
            this.showModal = true
        },
        async getCreator() {
            this.newTicket.creator = await this.$store.state.admin.getId()
        },
        setToday() {
            let date = new Date()
            let year = date.getUTCFullYear()
            let month =
                    date.getUTCMonth() + 1 < 10
                        ? '0' + (date.getUTCMonth() + 1)
                        : date.getUTCMonth() + 1
            let day =
                    date.getUTCDate() < 10 ? '0' + date.getUTCDate() : date.getUTCDate()
            this.newTicket.dueDate = day + '.' + month + '.' + year
        },
        getTickets(pageNumber = 1) {
            let personId = this.$store.getters.person.id
            this.loaded = false

            if (this.paginator === null)
                this.paginator = new Paginator(resources.ticket.getUser + personId)

            this.paginator.loadPage(pageNumber).then(response => {
                this.loaded = true
                this.tickets = []

                for (let i in response.data) {
                    let t = new Ticket()
                    let data = response.data[i]

                    this.tickets.push(t.fromJson(data))
                }
            })
        },
        closeTicket(ticket) {
            ticket.close()
        },
        fetchTicket() {},
        dateForHumans(date, format = 'YYYY-MM-DD HH:mm:ss') {
            return moment(date, format).fromNow()
        },
        showComment(ticket) {
            Vue.set(ticket, 'newComment', true)
            Vue.set(ticket, 'commentMessage', '')
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
        async getUsers() {
            this.users = await this.ticketUserService.getUsers()

        },
        async getLabels() {
            this.labels = await this.ticketLabelService.getLabels()
        },

        saveTicket() {
            //validate ticket
            if (this.showPriceInput && this.newTicket.outsourcing == 0) {
                this.$swal({
                    type: 'error',
                    title: 'Value Error!',
                    text: 'Please enter the amount in the "Amount" field.'
                })
                return
            }

            axios.post(resources.ticket.create, this.newTicket).then(() => {
                EventBus.$emit('widgetContentLoaded', this.subscriber, this.tickets.list.length)
                this.resetKey++
            })

            this.$emit('close')
        }
    }
}
</script>

<style scoped>
    .new-comment-area{
        margin-top: 30px;
        margin-left: 30px!important;
    }
    .comment-item {
        margin-top: 10px;
        border-width: 1px;
        border-style: solid;
        background-color: rgba(242,248,255,0.79) ;
        margin-left: 30px;
        padding: 10px;
        white-space: initial;
    }
    .closed-ticket {
        background-color: #9d0006;
        color: white;
        padding: 10px;
    }
    .open-ticket{
        background-color: #0aa66e;
        padding: 10px;
        color: white;
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


    }

    .Ticket-Are-Grid {
        max-width: 100%;
    }

    .new-ticet-modal-container {
        padding: 2rem;
        overflow-y: scroll;
    }
    .t-text-area {
        min-width: 100%!important;
        padding:5px;
        border-style: solid;
        border-width: 1px;
        border-color: #9aa7af;
    }
    .t-text{

        min-width: 90%;
        white-space: initial;
    }

    .t-date {
        font-size: x-small;
        color: #2a2a2a;
        float: right;

    }
</style>
