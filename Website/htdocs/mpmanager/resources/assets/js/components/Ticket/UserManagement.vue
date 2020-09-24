<template>
    <div class="row">
        <widget v-if="showNewUser"
                title="Add New Ticketing User"
                color="red"
                :show-refresh-button="false">
            <form class="md-layout">
                <md-card class="md-layout-item md-size-100">
                    <md-card-content>
                        <div class="md-layout md-gutter">
                            <div class="md-layout-item md-size-100">
                                <md-field :class="{'md-invalid': errors.has('name')}">
                                    <label>Name</label>
                                    <md-input
                                        v-model="ticketUserService.newUser.name"
                                        name="name"
                                        id="name"
                                        v-validate="'required|min:3'"></md-input>
                                    <span class="md-error">{{ errors.first('name') }}</span>
                                </md-field>
                                <md-field :class="{'md-invalid': errors.has('tag')}">
                                    <label>Ticketing System Tag</label>
                                    <md-input v-model="ticketUserService.newUser.tag"
                                              name="tag"
                                              id="tag"
                                              v-validate="'required|min:3'"></md-input>
                                    <span class="md-error">{{ errors.first('tag') }}</span>
                                </md-field>
                                <md-checkbox v-model="ticketUserService.newUser.outsourcing">User for Outsourcing?
                                </md-checkbox>
                            </div>
                        </div>
                        <md-progress-bar md-mode="indeterminate" v-if="loading"/>
                    </md-card-content>

                    <md-card-actions>
                        <md-button type="button" @click="saveUser" :disabled="loading" class="md-primary">Create User
                        </md-button>
                        <md-button type="button" @click="showNewUser = false" class="md-accent">Close</md-button>
                    </md-card-actions>
                </md-card>


            </form>

        </widget>
        <div v-if="showNewUser" style="margin-top: 1rem;"></div>


        <widget
            title="UserList"
            :button="true"
            button-text="Add new User"
            @widgetAction="showAddUser"
            color="green"
            :subscriber="subscriber"
        >
                <md-table v-model="ticketUserService.list" md-sort="name" md-sort-order="asc" md-card>
                    <md-table-row slot="md-table-row" slot-scope="{ item }">
                        <md-table-cell md-label="ID" md-sort-by="id" md-numeric>{{ item.id }}</md-table-cell>
                        <md-table-cell md-label="Name" md-sort-by="name">{{ item.name }}</md-table-cell>
                        <md-table-cell md-label="Tag" md-sort-by="tag">{{ item.tag }}</md-table-cell>
                        <md-table-cell md-label="Registered Since" md-sort-by="created_at">{{ item.created_at }}
                        </md-table-cell>
                    </md-table-row>
                </md-table>
        </widget>

    </div>
</template>

<script>
import Widget from '../../shared/widget'
import { TicketUserService } from '../../services/TicketUserService'
import { EventBus } from '../../shared/eventbus'

export default {
    name: 'UserManagement',
    components: { Widget },
    data () {
        return {
            subscriber:'ticket-user-list',
            ticketUserService: new TicketUserService(),
            showNewUser: false,
            headers: ['ID', 'Name', 'Tag', 'Registered Since'],
            tableName: 'Ticket User',
            loading: false,
        }

    },

    mounted () {

        this.getUsers()
    },
    methods: {

        async saveUser () {
            let validator = await this.$validator.validateAll()
            if (validator) {
                this.loading = true
                try {
                    let userData = await this.ticketUserService.createUser(this.ticketUserService.newUser.name, this.ticketUserService.newUser.tag, this.ticketUserService.newUser.outsourcing)

                    if (userData.error != undefined) {
                        this.alertNotify('warn', this.ticketUserService.newUser.tag + ' not found in the Ticketing system!')
                        this.loading = false
                        return
                    }
                    await this.getUsers()
                    this.alertNotify('success', 'User added successfully.')
                    this.loading = false
                } catch (e) {
                    this.loading = false
                    this.alertNotify('error', e.message)
                }
                this.showNewUser = false
                this.resetNewUser()
            }
        },
        async getUsers () {
            try {
                this.ticketUserService.list = []
                await this.ticketUserService.getUsers()
                EventBus.$emit('widgetContentLoaded',this.subscriber,this.ticketUserService.list.length)
            } catch (e) {
                this.alertNotify('error', e.message)
            }

        },
        resetNewUser () {
            this.ticketUserService.resetNewUser()
        },
        showAddUser () {
            this.showNewUser = true
        },
        alertNotify (type, message) {
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message,
                speed: 0
            })
        },
    },
}
</script>

<style scoped>

</style>
