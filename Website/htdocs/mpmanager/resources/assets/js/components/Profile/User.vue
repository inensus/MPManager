<template>
    <div class="row">
        <widget
            title="Profile"
        >
            <form class="md-layout" data-vv-scope="address">
                <md-card class="md-layout-item md-size-100">
                    <md-card-content>
                        <div class="md-layout md-gutter">
                            <div
                                class="md-layout-item md-size-50 md-small-size-100">
                                <md-field :class="{'md-invalid': errors.has('address.name')}">
                                    <label>Name</label>
                                    <md-input
                                        v-model="user.name"
                                        v-validate="'required|min:2|max:20'"
                                        name="name"
                                        id="name"
                                    />
                                    <md-icon>create</md-icon>
                                    <span class="md-error">{{ errors.first('address.name') }}</span>
                                </md-field>
                            </div>

                            <div
                                class="md-layout-item md-size-50 md-small-size-100">
                                <md-field>
                                    <label>Email</label>
                                    <md-input
                                        readonly
                                        v-model="user.email"
                                        name="email"
                                        id="email"
                                    />
                                    <md-icon>sms</md-icon>

                                </md-field>
                            </div>
                            <div
                                class="md-layout-item md-size-50 md-small-size-100">
                                <md-field>
                                    <label>Phone</label>
                                    <md-input type="number" v-model="user.phone"/>
                                    <md-icon>phone</md-icon>
                                </md-field>
                            </div>
                            <div
                                class="md-layout-item md-size-50 md-small-size-100">
                                <md-field>
                                    <label>street</label>
                                    <md-input v-model="user.street"/>
                                    <md-icon>contacts</md-icon>
                                </md-field>
                            </div>


                            <div class="md-layout-item md-size-50 md-small-size-100">
                                <md-field :class="{'md-invalid': errors.has('address.city')}">
                                    <label for="city">City</label>
                                    <md-select v-model="selectedCity"
                                               required
                                               name="city"
                                               id="city"
                                               v-validate.initial="'required'"
                                               :class="{'md-invalid': errors.has('address.city')}">
                                        <md-option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }}
                                        </md-option>
                                    </md-select>
                                    <span class="md-error">{{ errors.first('address.city') }}</span>
                                </md-field>
                            </div>
                            <div
                                class="md-layout-item md-size-100">
                                <md-button class="md-primary save-button" @click="updateDetails()">Save</md-button>
                                <md-button
                                    class="md-primary change-button"
                                    @click="modalVisibility=true"
                                >Change Password
                                </md-button>
                            </div>
                        </div>
                    </md-card-content>
                </md-card>
            </form>
        </widget>


        <md-dialog :md-active.sync=modalVisibility>
            <md-dialog-title>Change Password</md-dialog-title>
            <md-dialog-content>
                <div class="password-edit-container">
                    <form class="md-layout" data-vv-scope="password">
                        <md-field :class="{'md-invalid': errors.has('password.password')}">
                            <label for="password">Password</label>
                            <md-input
                                type="password"
                                name="password"
                                id="password"
                                v-validate="'required|min:3|max:15'"
                                v-model="changePassForm.password"
                                ref="passwordRef"
                            />
                            <span class="md-error">{{ errors.first('password.password') }}</span>
                        </md-field>

                        <md-field :class="{'md-invalid': errors.has('password.confirmPassword')}">
                            <label for="confirmPassword">Confirm Password</label>
                            <md-input
                                type="password"
                                name="confirmPassword"
                                id="confirmPassword"
                                v-model="changePassForm.confirmPassword"
                                v-validate="'required|confirmed:$passwordRef|min:3|max:15'"
                            />
                            <span class="md-error">{{ errors.first('password.confirmPassword') }}</span>
                        </md-field>

                        <!-- <md-progress-bar md-mode="indeterminate" v-if="sending" /> -->
                    </form>
                </div>
            </md-dialog-content>

            <md-dialog-actions>
                <md-button class="md-raised md-primary" @click="submitForm">Save</md-button>
                <md-button @click="modalVisibility = false">Close</md-button>
            </md-dialog-actions>

        </md-dialog>

    </div>
</template>

<script>
import Widget from '../../shared/widget'
import { Admin } from '../../classes/admin'
import { City } from '../../classes/Cities/city'

export default {
    name: 'Profile',
    components: { Widget },

    data () {
        return {
            sending: false,
            modalVisibility: false,
            cities: [],
            selectedCity: '',
            changePassForm: {
                password: '',
                confirmPassword: ''
            },
            adminService: new Admin(),
            cityService: new City(),
            user: {
                id: null,
                name: '',
                email: '',
                phone: '',
                street: '',
                city_id: null
            }
        }
    },

    created () {
        this.user.name = this.$store.getters.admin.name
        this.user.email = this.$store.getters.admin.email
        this.user.id = this.$store.getters.admin.id
    },
    mounted () {
        this.fillInformation()
    },
    computed: {},
    methods: {
        async fillInformation () {
            try {
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

                if (data.city_id !== undefined) {
                    this.selectedCity = this.cities
                        .filter(x => x.id === data.city_id)
                        .map(x => x.id)[0]
                }
            } catch (error) {
                this.alertNotify('error', error.message)
            }
        },

        async updateDetails () {
            let validation = await this.$validator.validateAll('address')
            if (!validation) {
                this.alertNotify('Warning', 'Please fill all required field')
                return
            }
            if (this.selectedCity !== undefined) {
                this.user.city_id = this.selectedCity
            }
            try {
                let response = await this.adminService.updateDetails(this.user)
                if (response.status === 200) {
                    this.alertNotify('success', 'The update has been done.')
                } else {
                    this.alertNotify('error', response.error.message)
                }
            } catch (error) {
                this.alertNotify('error', error)
                this.fillInformation()
            }
        },
        submitForm () {
            this.$validator.validateAll('password').then(result => {
                if (result) {
                    this.changePassword()
                }
            })
        },

        async changePassword () {
            let password = this.changePassForm.confirmPassword
            try {
                let response = await this.adminService.updatePassword(
                    this.user,
                    password
                )
                if (response.status === 200) {
                    this.alertNotify('success', 'Password updated.')
                } else {
                    this.alertNotify('error', response.error)
                }
                this.closeModal()
            } catch (error) {
                this.alertNotify('error', error)
                this.closeModal()
            }
        },

        closeModal () {
            this.modalVisibility = false
        },

        alertNotify (type, message, title = null) {
            if (title == null) {
                title = type.toString().charAt(0).toUpperCase() + type.toString().slice(1)
            }
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message
            })
        }
    }
}
</script>

<style scoped>
    .save-button {
        background-color: #325932 !important;
        color: #fefefe !important;
        float: right;
    }

    .change-button {
        background-color: #4f4e94 !important;
        color: #fefefe !important;
        float: right;
    }

    .password-edit-container {
        padding: 1rem;
    }
</style>
