<template>
    <div>


        <md-table>
            <md-table-row>
                <md-table-head></md-table-head>
                <md-table-head >Subject</md-table-head>
                <md-table-head >Category</md-table-head>
                <md-table-head >Date</md-table-head>
            </md-table-row>
            <template v-for="(ticket,index) in ticketList">
                <md-table-row @click="openTicket(index)">
                    <md-table-cell><md-icon>{{showTicket === index ? 'keyboard_arrow_down' : 'keyboard_arrow_right'}}</md-icon></md-table-cell>
                    <md-table-cell >{{ticket.name}}</md-table-cell>
                    <md-table-cell v-if="ticket.category">{{ticket.category.label_name}}</md-table-cell>
                    <md-table-cell v-else>-</md-table-cell>
                    <md-table-cell >{{formatDate(ticket.created)}}</md-table-cell>
                </md-table-row>
                <md-table-row v-if="showTicket === index">
                    <md-table-cell colspan="4">
                        <hr :class="[!ticket.closed ? 'open-ticket-hr' : 'close-ticket-hr']">


                        <div class="ticket-desc">
                            <div class="md-layout md-gutter md-size-100 " >

                                <div class="md-layout-item md-size-100">
                                    <span class="md-subheader">Ticket Details</span>

                                </div>


                            </div>
                            <div class="md-layout md-gutter md-size-100" >
                                <div class="md-layout-item md-size-70">

                                    <span v-if="ticket.assignedTo" >
                                       <b> Assigned to: {{ticket.assignedTo.user_name}}</b>
                                    </span>
                                    <span v-else>
                                         <b> Assigned to: - </b>
                                    </span>
                                </div>

                                <div class="md-layout-item md-size-30" @click="lockTicket(ticket)"
                                     style=" cursor: pointer;" v-if="!ticket.closed">
                                    <md-icon style="float:right !important; color: #9a0325">lock</md-icon>
                                </div>
                            </div>

                            <div class="md-layout-item md-size-100 t-text-area">
                                <md-icon>person</md-icon>
                                {{ticket.owner.name}} {{ticket.owner.surname}} :
                                <p class="t-text" v-text="ticket.description"></p>
                            </div>
                            <div class="md-layout-item md-size-100" style="min-height:25px;">

                                <em class="pull-right-label-primary" style="cursor:pointer">
                                    <small @click="showComments=!showComments">Comments</small>
                                    {{ticket.commentCount()}}
                                </em>

                            </div>
                            <div class="clear-fix"></div>

                            <div class="md-layout-item md-size-100">
                                <div v-if="showComments">

                                    <div
                                        class="comment-item"
                                        v-for="comment in ticket.comments"
                                        :key="comment.id"
                                    >
                                        <md-icon>person</md-icon>
                                        {{comment.comment}}
                                        <br/>
                                        <md-icon>access_time</md-icon>
                                        <small>{{getTimeAgo(comment.date)}}</small>
                                        <div class="clearfix"></div>
                                    </div>

                                </div>
                            </div>
                            <div class="md-layout-item md-size-95 new-comment-area" v-if="showComments">

                                <md-field>
                                    <label for="">New Comment</label>
                                    <md-textarea md-autogrow v-model="newComment"></md-textarea>
                                    <md-button
                                        type="submit"
                                        class="md-primary md-dense"
                                        @click="sendComment()"
                                    >Save
                                    </md-button>
                                </md-field>

                            </div>
                        </div>
                    </md-table-cell>

                </md-table-row>
            </template>
        </md-table>
    </div>
</template>

<script>
    import { UserTickets } from '../../classes/person/ticket'
    import { resources } from '../../resources'

    export default {
        name: 'TicketItem',
        props: {
            ticket: String,
            allowComment: Boolean,
            ticketList: Array
        },
        data () {
            return {
                showComments: false,
                newComment: '',
                showTicket: null,
            }
        },
        methods: {
            getTimeAgo (date) {
                return moment(date).fromNow()

            },
            formatDate (date) {
                let d = new Date(date)
                return d.toLocaleDateString()
            },
            openTicket (index) {
                if (this.showTicket === index) {
                    this.showTicket = null
                } else {
                    this.showTicket = index
                }

            },
            navigateToOwner (id) {
                this.$router.push({ path: '/people/' + id })
            },
            lockTicket (ticket) {
                ticket.close()
            },

            sendComment () {
                let comment = {
                    comment: this.newComment,
                    date: new Date(),
                    fullName: this.$store.getters.admin.name,
                    username: this.$store.getters.admin.email,
                    cardId: this.ticket.id
                }

                axios.post(resources.ticket.comments, comment).then(response => {
                    if (this.ticket.category.out_source) {
                        axios.post(resources.sms.send, {
                            message: this.newComment,
                            person_id: this.ticket.owner.id,
                            senderId: this.$store.getters.admin.id
                        })
                    }
                })
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
    .ticket-area {
        border: 0.05px solid lightgray;
        margin-top: 0.4rem;
    }

    .ticket-area:hover {
        background-color: #f3f3f3;
    }

    .comment-box {
        background-color: #f4fff0;
        border-width: 1px;
        border-style: dotted;
        padding: 10px;


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
        width: 100%;
    }

    . Ticket-Area-Row-P {
        font-size: small;
        white-space: initial;
        max-width: 100%;
    }

    .new-ticket-modal-container {
        padding: 2rem;
        overflow-y: scroll;
    }
    .ticket-desc{
        margin-bottom: 5vh;

    }
    .open-ticket-hr{
        height: 1vh;
        background-color: #8eb18e;
        width: 100%!important;
    }
    .close-ticket-hr{
        height: 1vh;
        background-color: #9a0325;
        width: 100%!important;
    }
    .expand-ticket{
        padding-right: 5px!important;
        max-width: 10%!important;
    }


</style>
