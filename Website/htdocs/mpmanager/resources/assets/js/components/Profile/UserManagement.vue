<template>
    <div class="row">
        <widget
            title="User Management"
            :button-text="'NEW USER'"
            :button="true"
            @widgetAction="showNewUser = true"
            :subscriber="subscriber"
        >
            <form v-if="showNewUser" @submit.prevent="submitCreateForm" data-vv-scope="form-create">
                <div class="edit-container">
                    <md-card>
                        <md-card-header>
                            <div style="float:right; cursor:pointer" @click="()=>{showNewUser = false}">
                                <md-icon>close</md-icon>&nbsp;Close
                            </div>
                        </md-card-header>
                        <md-card-content class="md-layout md-gutter">
                            <div class="md-layout-item md-size-50 md-small-size-100">
                                <md-field :class="{'md-invalid': errors.has('form-create.name')}">
                                    <label>Name</label>
                                    <md-input
                                        v-model="user.name"
                                        v-validate="'required|min:2|max:20'"
                                        name="name"
                                        id="name"
                                    />
                                    <md-icon>create</md-icon>
                                    <span class="md-error">{{ errors.first('form-create.name') }}</span>
                                </md-field>
                            </div>

                            <div class="md-layout-item md-size-50 md-small-size-100">
                                <md-field :class="{'md-invalid': errors.has('form-create.email')}">
                                    <label>Email</label>
                                    <md-input
                                        type="text"
                                        name="email"
                                        id="email"
                                        v-model="user.email"
                                        v-validate="'required|email'"
                                    />
                                    <md-icon>email</md-icon>
                                    <span class="md-error">{{ errors.first('form-create.email') }}</span>
                                </md-field>
                            </div>
                            <div class="md-layout-item md-size-50 md-small-size-100">
                                <md-field :class="{'md-invalid': errors.has('form-create.password')}">
                                    <label for="password">Password</label>
                                    <md-input
                                        type="password"
                                        name="password"
                                        id="password"
                                        v-validate="'required|min:3|max:15'"
                                        v-model="user.password"
                                        ref="passwordRef"
                                    />

                                    <span class="md-error">{{ errors.first('form-create.password') }}</span>
                                </md-field>
                            </div>

                            <div class="md-layout-item md-size-50 md-small-size-100">
                                <md-field :class="{'md-invalid': errors.has('form-create.confirmPassword')}">
                                    <label for="confirmPassword">Confirm Password</label>
                                    <md-input
                                        type="password"
                                        name="confirmPassword"
                                        id="confirmPassword"
                                        v-model="user.confirmPassword"
                                        v-validate="'required|confirmed:$passwordRef|min:3|max:15'"
                                    />
                                    <span class="md-error">{{ errors.first('form-create.confirmPassword') }}</span>
                                </md-field>
                            </div>
                        </md-card-content>
                        <md-card-actions>
                            <md-button type="submit" class="md-primary btn-sell">Create</md-button>
                        </md-card-actions>
                    </md-card>
                </div>
            </form>

            <div class="md-layout">
                <md-card class="md-layout-item md-size-100">
                    <md-card-content>
                        <div class="md-layout md-gutter">
                            <div
                                class="md-layout-item md-size-100 md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100"
                            >
                                <md-table md-card style="margin-left: 0">
                                    <md-table-row>
                                        <md-table-head>ID</md-table-head>
                                        <md-table-head>Name</md-table-head>
                                        <md-table-head>E-Mail</md-table-head>
                                        <md-table-head>Phone</md-table-head>
                                    </md-table-row>

                                    <md-table-row
                                        v-for="user in users"
                                        :key="user.id"
                                        style="cursor:pointer;"
                                        @click="userDetail(u)"
                                    >
                                        <md-table-cell>{{ user.id}}</md-table-cell>
                                        <md-table-cell>{{ user.name}}</md-table-cell>
                                        <md-table-cell>{{ user.email}}</md-table-cell>
                                        <md-table-cell>{{ user.phone}}</md-table-cell>
                                    </md-table-row>
                                </md-table>
                            </div>
                        </div>
                    </md-card-content>
                </md-card>
            </div>
        </widget>


        <md-dialog :md-active.sync="modalVisibility">
            <md-dialog-title>Edit User</md-dialog-title>

            <div class="edit-container">
                <form class="md-layout md-gutter" data-vv-scope="form-edit">
                    <div class="md-layout-item md-size-50 md-small-size-100">
                        <md-field :class="{'md-invalid': errors.has('form-edit.name')}">
                            <label>Name</label>
                            <md-input
                                disabled
                                v-model="user.name"
                                v-validate="'required|min:2|max:20'"
                                name="name"
                                id="name"
                            />
                            <md-icon>create</md-icon>
                            <span class="md-error">{{ errors.first('form-edit.name') }}</span>
                        </md-field>
                    </div>

                    <div class="md-layout-item md-size-50 md-small-size-100">
                        <md-field>
                            <label>Phone</label>
                            <md-input type="text" name="phone" id="phone" v-model="user.phone"/>
                            <md-icon>phone</md-icon>
                        </md-field>
                    </div>
                    <div class="md-layout-item md-size-50 md-small-size-100">
                        <md-field>
                            <label>street</label>
                            <md-input v-model="user.street" name="street" id="street"/>
                            <md-icon>contacts</md-icon>
                        </md-field>
                    </div>
                    <div class="md-layout-item md-size-50 md-small-size-100">
                        <md-field :class="{'md-invalid': errors.has('form-edit.city')}">
                            <label for="city">City</label>
                            <md-select v-model="selectedCity" name="city" id="city" v-validate="'required'">
                                <md-option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }}</md-option>
                            </md-select>
                            <span class="md-error">{{ errors.first('form-edit.city') }}</span>
                        </md-field>
                    </div>
                </form>
                <md-progress-bar md-mode="indeterminate" v-if="sending"/>
            </div>


            <md-dialog-actions>
                <md-button class="md-accent" @click="closeModal()">Close</md-button>

                <md-button

                    class="md-primary btn-lg"
                    @click="submitEditForm()"

                >Save
                </md-button>
            </md-dialog-actions>
        </md-dialog>

    </div>
</template>
<script>
import Widget from '../../shared/widget'
import { Admin } from '../../classes/admin'
import { City } from '../../classes/Cities/city'
import { EventBus } from '../../shared/eventbus'

export default {
    name: 'ProfileManagement',
    components: { Widget },

    data () {
        return {
            subscriber:'user-management',
            sending: false,
            modalVisibility: false,
            cities: [],
            selectedCity: '',
            adminService: new Admin(),
            cityService: new City(),
            users: [],
            user: {
                id: null,
                name: '',
                email: '',
                phone: '',
                street: '',
                city_id: null,
                password: '',
                confirmPassword: ''
            },
            showNewUser: false
        }
    },

    created () {
        this.getUsers()
    },
    computed: {},
    methods: {
        async getUsers () {
            let users = await this.adminService.getUserList()
            users.forEach(u => {
                let usr = {
                    id: u.id,
                    name: u.name,
                    email: u.email,
                    phone: u.address ? u.address.phone : '-',
                }
                this.users.push(usr)
            })
            EventBus.$emit('widgetContentLoaded',this.subscriber,this.users.length)
        },
        userDetail (user) {
            this.user.name = ''
            this.user.id = ''
            this.user.name = user.name
            this.user.id = user.id
            this.fillInformation()
        },
        async fillInformation () {
            try {
                this.cities = []
                let cities = await this.cityService.getCities()
                cities.forEach(e => {
                    let city = {
                        id: e.id,
                        name: e.name
                    }
                    this.cities.push(city)
                })

                let data = await this.adminService.getAddress(this.user.id)

                if (data.phone !== undefined) this.user.phone = data.phone
                if (data.street !== undefined) this.user.street = data.street
                if (data.email !== undefined) this.user.email = data.email
                if (data.city_id !== undefined) {
                    this.selectedCity = this.cities
                        .filter(x => x.id === data.city_id)
                        .map(x => x.id)[0]
                }
                this.openModal()
            } catch (error) {
                console.log(error)
            }
        },
        openModal () {
            this.modalVisibility = true
        },
        closeModal () {
            this.clearUserModel()
            this.modalVisibility = false
        },
        submitEditForm () {
            this.validateForm('form-edit').then(result => {
                if (result) {
                    this.sending = true
                    this.updateUser()
                }else{
                    return
                }
            })
        },
        async submitCreateForm () {
            await this.validateForm('form-create').then(result => {
                if (result) {
                    this.sending = true
                    this.createUser()
                }else{
                    return
                }
            })
        },

        async updateUser () {
            if (this.selectedCity !== undefined) {
                this.user.city_id = this.selectedCity
            }
            try {
                let response = await this.adminService.updateDetails(this.user)
                if (response.status === 200) {
                    this.alertNotify('success', 'The update has been done.')
                } else {
                    this.alertNotify('error', response.error)
                }
            } catch (error) {
                this.alertNotify('error', error)
            }
            this.closeModal()
            this.clearUserModel()
            this.sending = false
        },
        async createUser () {
            try {
                let response = await this.adminService.createUser(this.user)

                if (response.error == undefined) {
                    this.alertNotify('success', 'New user created.')
                    this.showNewUser = false
                    this.cities = []
                    this.user.id = this.users.length+1
                    this.users.push(this.user)
                } else {
                    this.alertNotify('error', response.error.message)
                }
            } catch (error) {
                this.alertNotify('error', error)
            }
            this.clearUserModel()
            this.sending = false
        },

        validateForm (scope) {
            return this.$validator.validateAll(scope)
        },
        alertNotify (type, message) {
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message
            })
        },
        clearUserModel () {
            this.user = {
                id: null,
                name: '',
                email: '',
                phone: '',
                street: '',
                city_id: null,
                password: '',
                confirmPassword: ''
            }
        }
    }
}
</script>
<style scoped>
    .edit-container {
        padding: 1rem;
    }
</style>
