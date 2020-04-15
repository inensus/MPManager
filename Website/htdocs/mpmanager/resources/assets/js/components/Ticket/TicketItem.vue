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
                    <div class="md-layout-item md-size-10" @click="lockTicket(ticket)" style="float: right; cursor: pointer;" v-if="!ticket.closed">
                        <md-icon style="color: #9a0325">lock</md-icon>
                    </div>
                </div>
                <div class="md-layout md-size-100" >

                    <div class="md-layout md-gutter md-size-60" >
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


                        <p class="t-text" v-text="ticket.description">

                        </p>


                        <div class="t-date">
                            <md-icon>access_time</md-icon>
                            {{ticket.created}}
                        </div>

                    </div>
                </div>



                    <em class="pull-right-label-primary" style="cursor:pointer">
                        <small @click="showComments=!showComments">Comments</small>
                        {{ticket.commentCount()}}
                    </em>

                <div class="md-layout md-size-100"  style="min-width: 100%!important;" >
                    <div v-if="showComments" style="min-width: inherit;">

                        <div
                            :key="comment.id"
                            class="comment-box"
                            v-for="comment in ticket.comments"
                        >
                           <md-icon>person</md-icon> {{comment.comment}}
                            <br/>
                            <md-icon>access_time</md-icon>
                            <small>{{comment.date}}</small>
                            <div class="clearfix"></div>
                        </div>
                        <div v-if="allowComment">
                            <md-field>
                                <md-textarea v-model="newComment"></md-textarea>
                                <md-button
                                    @click="sendComment"
                                    class="md-primary btn-save"
                                    type="submit"
                                >Send
                                </md-button>
                            </md-field>
                            <div class="clearfix"></div>
                        </div>

                    </div>
                </div>
            </div>
        </md-list-item>
    </md-list>
</template>

<script>
    import {UserTickets} from "../../classes/person/ticket";
    import {resources} from "../../resources";

    export default {
        name: "TicketItem",
        props: ["ticket", "allowComment"],
        data() {
            return {
                showComments: false,
                newComment: ""
            };
        },
        methods: {
            navigateToOwner(id) {
                this.$router.push({path: "/people/" + id});
            },
            lockTicket(ticket) {
                ticket.close();
            },

            sendComment() {
                let comment = {
                    comment: this.newComment,
                    date: new Date(),
                    fullName: this.$store.getters.admin.name,
                    username: this.$store.getters.admin.email,
                    cardId: this.ticket.id
                };

                axios.post(resources.ticket.comments, comment).then(response => {
                    if (this.ticket.category.out_source) {
                        axios.post(resources.sms.send, {
                            message: this.newComment,
                            person_id: this.ticket.owner.id,
                            senderId: this.$store.getters.admin.id
                        });
                    }
                });
            }
        }
    };
</script>

<style scoped>
    .ticket-area {
        border: 0.05px solid lightgray;
        margin-top: 0.4rem;
    }

    .ticket-area:hover {
        background-color: #f3f3f3;
    }
    .comment-box{
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

    .new-ticet-modal-container {
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
