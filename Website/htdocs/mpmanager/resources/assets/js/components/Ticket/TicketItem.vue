<template>
    <md-list :data-id="ticket.id" class="md-triple-line ticket-area">
        <md-list-item>
            <div class="Ticket-Are-Grid">
                <div class="md-layout md-gutter md-size-100">
                    <h2 class="md-layout-item md-size-90" style="display:inline-flex;">
                        <div style="color:limegreen" v-if="ticket.category.out_source">
                            <md-icon>money</md-icon>
                        </div>
                        <span class="semi-bold" v-text="ticket.name"></span>
                    </h2>
                    <div class="md-layout-item md-size-10" @click="lockTicket(ticket.id)"
                         style="float: right; cursor: pointer;" v-if="!ticket.closed">
                        <md-icon style="color: #9a0325">lock</md-icon>
                    </div>
                </div>
                <div class="md-layout md-size-100">

                    <div class="md-layout md-gutter md-size-60">
                        <div class="md-layout-item md-size-30" style="cursor:pointer;" v-if="ticket.assignedTo">
                            <small>
                                <md-icon>attach_file</md-icon>
                                Assigned To:<b>{{ticket.assignedTo.user_name}}</b> </small>

                        </div>
                        <div class="md-layout-item md-size-30" style="float: right;">
                            <small v-if="ticket.category">
                                <md-icon>style</md-icon>
                                {{ticket.category.label_name}}
                            </small>
                            <small v-else>-</small>
                        </div>
                    </div>
                    <div class="md-layout t-text-area">
                        <div
                            @click="navigateToOwner(ticket.owner.id)"
                            class="md-subheader md-size-100"
                            style="cursor:pointer; min-width: 100%;"
                            v-if="ticket.owner">
                            <p class="Ticket-Area-Row-P">
                                <md-icon>person</md-icon>
                                {{ticket.owner.name}} {{ticket.owner.surname}}
                            </p>

                        </div>

                        <p class="t-text" v-text="ticket.description"></p>
                        <div class="t-date">
                            <md-icon>access_time</md-icon>
                            {{ticket.created}}
                        </div>
                    </div>
                </div>


                <em class="pull-right-label-primary" style="cursor:pointer">
                    <small @click="showComments=!showComments">Comments</small>
                    {{ticket.comments.length}}
                </em>

                <div class="md-layout md-size-100" style="min-width: 100%!important;">

                    <div v-if="showComments" style="min-width: inherit;">
                        <div
                            :key="comment.id"
                            class="comment-box"
                            v-for="comment in ticket.comments"
                        >
                            <md-icon>person</md-icon>
                            {{comment.comment}}
                            <br/>
                            <md-icon>access_time</md-icon>
                            <small>{{comment.date}}</small>
                            <div class="clearfix"></div>
                        </div>
                        <div v-if="allowComment">
                            <md-field style="float: left">
                                <label for="comment">Comment</label>
                                <md-textarea v-model="newComment"></md-textarea>
                            </md-field>
                            <md-button
                                @click="sendComment"
                                class="md-primary md-raised"
                                type="submit"
                                style="float:right"
                            >Send
                            </md-button>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </md-list-item>
    </md-list>
</template>

<script>

    import { UserTickets } from '../../classes/person/ticket'
    import { resources } from '../../resources'
    import { TicketCommentService } from '../../services/TicketCommentService'
    import { EventBus } from '../../shared/eventbus'
    import { SmsService } from '../../services/SmsService'
    import { TicketService } from '../../services/TicketService'

    export default {
        name: 'TicketItem',
        props: {

            ticket: {},
            allowComment: {
                type: Boolean
            }
        },
        data () {
            return {
                ticketCommentService: new TicketCommentService(),
                ticketService: new TicketService(),
                smsService: new SmsService(),
                showComments: false,
                senderId: this.$store.getters['auth/authenticationService'].authenticateUser.id,
                newComment: ''
            }
        },
        methods: {
            navigateToOwner (id) {
                this.$router.push({ path: '/people/' + id })
            },
            async lockTicket (id) {
                try {
                    await this.ticketService.closeTicket(id)
                    EventBus.$emit('listChanged')
                    this.alertNotify('success', 'Ticket closed successfully.')

                } catch (e) {

                }

            },

            async sendComment () {

                try {
                    let name = this.$store.getters['auth/authenticationService'].authenticateUser.name
                    let username = this.$store.getters['auth/authenticationService'].authenticateUser.email
                    await this.ticketCommentService.createComment(this.newComment, this.ticket.id, name, username)
                    if (this.ticket.category.out_source) {
                        await this.smsService.sendToPerson(this.newComment, this.ticket.owner.id, this.senderId)

                    }
                    this.showComments = false
                    EventBus.$emit('listChanged')
                    this.alertNotify('success', 'Comment send successfully.')

                } catch (e) {
                    this.alertNotify('error', e.message)
                }
            },
            alertNotify (type, message) {
                this.$notify({
                    group: 'notify',
                    type: type,
                    title: type + ' !',
                    text: message
                })
            },

        }
    }
</script>

<style scoped>
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

    .t-text-area {
        min-width: 100%;
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
