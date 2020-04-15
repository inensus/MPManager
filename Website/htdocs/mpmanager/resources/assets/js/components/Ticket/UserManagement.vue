<template>
    <div class="row">
        <widget v-if="showNewUser"
                title="Add New Ticketing User">
            <form class="md-layout">
                <md-card class="md-layout-item md-size-100">
                    <md-card-header>
                        <div class="md-title">Add New User</div>
                    </md-card-header>

                    <!-- start user form-->

                    <md-card-content>
                        <div class="md-layout md-gutter">
                            <div class="md-layout-item md-size-100">
                                <md-field>
                                    <label>Name</label>
                                    <md-input v-model="newUser.name"></md-input>
                                </md-field>
                                <md-field>
                                    <label>Ticketing System Tag</label>
                                    <md-input v-model="newUser.tag"></md-input>
                                </md-field>
                                <md-checkbox v-model="newUser.outsourcing">User for Outsourcing?</md-checkbox>
                            </div>
                        </div>
                    </md-card-content><!-- end user form-->

                    <md-card-actions>
                        <md-button type="button" @click="saveUser" class="md-primary">Create User</md-button>
                        <md-button type="button" @click="showNewUser = false" class="md-accent">Close</md-button>
                    </md-card-actions>
                </md-card>


            </form>

        </widget>

        <!-- TODO: Replace with class -->
        <div v-if="showNewUser" style="margin-top: 1rem;"></div>


        <widget
            title="UserList"
            :button="true"
            button-text="Add new User"
            :callback="showAddUser"
        >
            <div v-model="users">
                <md-table v-model="users" md-sort="name" md-sort-order="asc" md-card>
                    <md-table-toolbar>
                        <h1 class="md-title">Users</h1>
                    </md-table-toolbar>

                    <md-table-row slot="md-table-row" slot-scope="{ item }">
                        <md-table-cell md-label="ID" md-sort-by="id" md-numeric>{{ item.id }}</md-table-cell>
                        <md-table-cell md-label="Name" md-sort-by="name">{{ item.name }}</md-table-cell>
                        <md-table-cell md-label="Tag" md-sort-by="tag">{{ item.tag }}</md-table-cell>
                        <md-table-cell md-label="Registered Since" md-sort-by="created_at">{{ item.created_at }}
                        </md-table-cell>
                    </md-table-row>
                </md-table>
            </div>
        </widget>

        <!-- area to show feedback from add user -->
        <!-- <notifications group="foo" position="top right"/>
        <notifications group="error" position="top center"/> -->

    </div>
</template>

<script>
  import { EventBus } from '../../shared/eventbus'
  import Widget from '../../shared/widget'

  export default {
    name: 'UserManagement',
    components: { Widget },
    data () {
      return {
        users: [],
        showNewUser: false,
        newUser: {
          name: '',
          tag: '',
          outsourcing: null,
        },
        bcd: {
          'Home': {
            'href': '/'
          },
          'Tickets': {
            'href': null
          },
          'Settings': {
            'href': null
          },
          'User Management': {
            'href': null
          },
        },
      }

    },

    created () {
      this.getUsers()

    },

    mounted () {
      EventBus.$emit('bread', this.bcd)

    },
    methods: {
      showAddUser () {
        this.showNewUser = true
      },
      saveUser () {
        axios.post(resources.ticket.createUserTicket, {
          'username': this.newUser.name,
          'usertag': this.newUser.tag,
          'outsourcing': this.newUser.outsourcing
        }).then(response => {
          let userData = response.data.data

          if (userData.error != undefined) {

            this.$notify({
              group: 'error',
              title: 'User Not Found!',
              type: 'warn',
              text: this.newUser.tag + 'not found in the Ticketing system!'
            })
            return
          }

          this.users.push(
            {
              id: userData.id,
              name: userData.user_name,
              tag: userData.user_tag,
              created_at: userData.created_at
            }
          )

          this.$notify({
            group: 'foo',
            title: 'User Added!',
            text: userData.user_name + 'created!'
          })

        })
        this.showNewUser = false
        this.resetNewUser()

      },
      resetNewUser () {
        this.newUser = {
          'name': '',
          'tag': '',
          'outsourcing': null,
        }
      },

      getUsers () {
        axios.get(resources.ticket.users)
          .then(response => {
            let data = response.data.data

            for (let _user in data) {
              let user = data[_user]
              this.users.push({
                id: user.id,
                name: user.user_name,
                tag: user.user_tag,
                created_at: user.created_at
              })
            }
          })
      }
    },
  }
</script>

<style scoped>

</style>
