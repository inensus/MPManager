<template>
  <div class="col-sm-12">
    <widget
      :subscriber="subscriber"
      title="User Tickets"
      :paginator="tickets.paginator"
      :button="true"
      :button-text="'New Ticket'"
      :callback="openModal"
    >
      <md-list
        class="md-triple-line ticket-area"
        :data-id="ticket.id"
        v-for="ticket in tickets.list"
        :key="ticket.id"
      >
        <md-list-item>
          <div class="Ticket-Are-Grid md-layout md-size-100">
            <div class="md-layout-item md-size-100">
              <h2 style="display:inline-flex;">
                <span class="semi-bold" v-text="ticket.name"></span>
                <div v-if="!ticket.closed" @click="closeTicket(ticket)">
                  <md-icon>lock</md-icon>
                </div>
              </h2>
            </div>


            <div class="md-layout-item md-size-100"  style="float: right;">
              <small v-if="ticket.category">
                <md-icon>style</md-icon>
                {{ticket.category.label_name}}
              </small>
              <small v-else>-</small>
              <br />
            </div>
              <div class="md-layout-item md-size-100 t-text-area">
                  <p class="t-text" v-text="ticket.description">

                  </p>


                  <div class="t-date">
                      <md-icon>access_time</md-icon>
                      {{ticket.created_at}}
                  </div>
              </div>

            <div class="md-layout-item md-size-100">
              <em class="pull-right-label-primary" style="cursor:pointer">
                <small @click="showComment(ticket)">Comments</small>
                {{ticket.commentCount()}}
              </em>
            </div>
            <div class="md-layout-item md-size-100">
              <div  v-if="ticket.newComment">

                  <div
                    class="comment-box"
                    v-for="comment in ticket.comments"
                    :key="comment.id"
                  >
                      <md-icon>person</md-icon> {{comment.comment}}
                      <br/>
                      <md-icon>access_time</md-icon>
                      <small>{{comment.date}}</small>
                    <div class="clearfix"></div>
                  </div>

              </div>
            </div>
            <div class="md-layout-item md-size-100" v-if="ticket.newComment">


              <md-field>
                <md-textarea v-model="ticket.commentMessage"></md-textarea>
                <md-button
                  type="submit"
                  class="md-primary btn-save"
                  @click="saveComment(ticket)"
                >Save Comment</md-button>
              </md-field>
            </div>
          </div>
        </md-list-item>
      </md-list>

      <div class="well" v-if="tickets.length === 0">No tickets found</div>
      <div class="row" style="text-align: center" v-if="loaded === false">
        <h5>Tickets are Loading</h5>
        <img src="https://loading.io/spinners/dash-ring/index.dash-ring-loading-icon.svg" />
      </div>
    </widget>


    <md-dialog :md-active.sync="showModal">
      <md-dialog-title>New Ticket</md-dialog-title>

      <div class="new-ticet-modal-container">
        <form novalidate class="md-layout">
          <div  class="md-layout md-gutter" >


                <div class="md-layout-item md-size-100 ">
                  <md-field name="title">
                    <label for="title">Title</label>
                    <md-input type="text" v-model="newTicket.title" id="title" name="title" />
                  </md-field>
                </div>







                <div class="md-layout-item md-size-100 " style="display:inline-flex;">

                  <md-datepicker
                    name="ticketDueDate"
                    id="ticketDueDate"
                    v-model="newTicket.dueDate"
                  > <label for="ticketDueDate">Due Date</label></md-datepicker>

                </div>


              <div class="md-layout-item md-size-100 ">
                <md-field name="ticketPriority">
                  <label for="ticketPriority">Category</label>
                  <md-select name="ticketPriority" id="ticketPriority">
                    <md-option value="0" disabled>-- Select Category --</md-option>
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
                    <label for="ticketAssignedTo">Assign To</label>
                    <md-select name="ticketAssignedTo" id="ticketAssignedTo">
                      <md-option disabled selected>No one</md-option>
                      <md-option v-for="user in users" :value="user.id" :key="user.id">{{user.name}}</md-option>
                    </md-select>
                  </md-field>
                </div>


                  <div class="md-layout-item md-size-100 ">
                    <md-field>
                      <label for="description">Description</label>
                      <md-textarea type="text" id="description" name="description" />
                    </md-field>
                  </div>

          </div>
        </form>
      </div>

      <md-dialog-actions>
        <md-button class="md-accent" @click="closeModal()">Close</md-button>

        <md-button class="md-primary btn-lg" @click="saveTicket()">Save</md-button>
      </md-dialog-actions>
    </md-dialog>
  </div>
</template>

<script>
import Widget from "../../shared/widget";

import { Ticket, UserTickets } from "../../classes/person/ticket";
import { resources } from "../../resources";
import { EventBus } from "../../shared/eventbus";
import { Paginator } from "../../classes/paginator";
import TicketItem from "../Ticket/TicketItem";
import Paginate from "../../shared/Paginate";
import Modal from "../../modal/modal";

export default {
  name: "Ticket",
  components: { Paginate, TicketItem, Widget, Modal },
  data() {
    return {
      subscriber: "userTickets",
      tickets: new UserTickets(this.$store.getters.person.id),
      showPriceInput: false,
      paginator: null,
      // tickets: [],
      currentPage: 0,
      totalPages: 0,
      perPage: 0,
      currentFrom: 0,
      currentTo: 0,
      total: 0,
      loaded: false,
      showModal: false,
      users: {},
      labels: [],
      newTicket: {
        title: "",
        description: "",
        dueDate: "",
        label: 1,
        assignedPerson: "",
        owner_id: this.$store.getters.person.id, //current person id
        owner_type: "person",
        creator: "",
        outsourcing: 0
      }
    };
  },
  beforeDestroy() {
    EventBus.$off("pageLoaded", this.reloadList);
  },

  mounted() {
    EventBus.$on("pageLoaded", this.reloadList);
    //this.getTickets();
    this.getUsers();
    this.getLabels();
    this.getCreator();
    this.$on("close", function() {
      this.showModal = false;
    });
  },
  methods: {
    ticketCategoryChange(label) {
      // is needed for outsourcing.

      let category = this.labels.filter(l => {
        return l.id == label.target.value;
      });

      if (category.length === 0) {
        return;
      }

      category = category[0];

      if (category.out_source === 1) {
        this.showPriceInput = true;
      }
    },
    reloadList(sub, data) {
      if (sub !== this.subscriber) return;
      this.tickets.updateList(data);
      this.loaded = true;
    },
    closeModal() {
      this.showModal = false;
    },
    openModal() {
      this.showModal = true;
    },
    async getCreator() {
      this.newTicket.creator = await this.$store.state.admin.getId();
    },
    setToday() {
      let date = new Date();
      let year = date.getUTCFullYear();
      let month =
        date.getUTCMonth() + 1 < 10
          ? "0" + (date.getUTCMonth() + 1)
          : date.getUTCMonth() + 1;
      let day =
        date.getUTCDate() < 10 ? "0" + date.getUTCDate() : date.getUTCDate();
      this.newTicket.dueDate = day + "." + month + "." + year;
    },
    getTickets(pageNumber = 1) {
      let personId = this.$store.getters.person.id;
      this.loaded = false;

      if (this.paginator === null)
        this.paginator = new Paginator(resources.ticket.getUser + personId);

      this.paginator.loadPage(pageNumber).then(response => {
        this.loaded = true;
        this.tickets = [];

        for (let i in response.data) {
          let t = new Ticket();
          let data = response.data[i];

          this.tickets.push(t.fromJson(data));
        }
      });
    },
    closeTicket(ticket) {
      ticket.close();
    },
    fetchTicket() {},
    dateForHumans(date, format = "YYYY-MM-DD HH:mm:ss") {
      return moment(date, format).fromNow();
    },
    showComment(ticket) {
      Vue.set(ticket, "newComment", true);
      Vue.set(ticket, "commentMessage", "");
    },
    saveComment(ticket) {
      let comment = {
        comment: ticket.commentMessage,
        date: new Date(),
        fullName: this.$store.getters.admin.name,
        username: this.$store.getters.admin.email,
        cardId: ticket.id
      };
      ticket.newComment = false;
      ticket.comments.push(comment);

      this.tickets.newComment(comment);
    },
    getUsers() {
      axios.get(resources.ticket.users).then(response => {
        let data = response.data.data;
        for (let _user in data) {
          let user = data[_user];
          this.users[user.id] = {
            id: user.extern_id,
            name: user.user_name,
            tag: user.user_tag,
            created_at: user.created_at
          };
        }
      });
    },
    getLabels() {
      axios.get(resources.ticket.labels).then(response => {
        this.labels = response.data.data;
      });
    },

    saveTicket() {
      //validate ticket
      if (this.showPriceInput && this.newTicket.outsourcing == 0) {
        this.$swal({
          type: "error",
          title: "Value Error!",
          text: 'Please enter the amount in the "Amount" field.'
        });
        return;
      }
      axios.post(resources.ticket.create, this.newTicket).then(response => {
        let data = response.data.data[0];
        let t = new Ticket();

        this.tickets.list.unshift(t.fromJson(data));
      });

      this.$emit("close");
    }
  }
};
</script>

<style scoped>
    .comment-box {
        background-color: #f4fff0;
        border-width: 1px;
        border-style: dotted;
        padding: 10px;
        white-space: initial;
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

.new-ticet-modal-container {
  padding: 2rem;
  overflow-y: scroll;
}
.t-text-area {
    min-width: 100%!important;
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
