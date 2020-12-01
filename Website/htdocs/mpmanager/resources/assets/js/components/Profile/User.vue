<template>
    <div class="row">
        <widget
            :title="$tc('words.profile')"
        >
            <form class="md-layout" data-vv-scope="address">
                <md-card class="md-layout-item md-size-100">
                    <md-card-content>
                        <div class="md-layout md-gutter">
                            <div
                                class="md-layout-item md-size-50 md-small-size-100">
                                <md-field :class="{'md-invalid': errors.has($tc('words.name'))}">
                                    <label>{{ $tc('words.name') }}</label>
                                    <md-input
                                        v-model="user.name"
                                        v-validate="'required|min:2|max:20'"
                                        :name="$tc('words.name')"
                                        id="name"
                                    />
                                    <md-icon>create</md-icon>
                                    <span class="md-error">{{ errors.first($tc('words.name')) }}</span>
                                </md-field>
                            </div>

                            <div
                                class="md-layout-item md-size-50 md-small-size-100">
                                <md-field>
                                    <label>{{ $tc('words.email') }}</label>
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
                                    <label>{{ $tc('words.phone') }}</label>
                                    <md-input type="number" v-model="user.phone"/>
                                    <md-icon>phone</md-icon>
                                </md-field>
                            </div>
                            <div
                                class="md-layout-item md-size-50 md-small-size-100">
                                <md-field>
                                    <label>{{ $tc('words.street') }}</label>
                                    <md-input v-model="user.street"/>
                                    <md-icon>contacts</md-icon>
                                </md-field>
                            </div>


                            <div class="md-layout-item md-size-50 md-small-size-100">
                                <md-field :class="{'md-invalid': errors.has($tc('words.city'))}">
                                    <label for="city">{{ $tc('words.city') }}</label>
                                    <md-select v-model="selectedCity"
                                               required
                                               :name="$tc('words.city')"
                                               id="city"
                                               v-validate.initial="'required'"
                                               :class="{'md-invalid': errors.has($tc('words.city'))}">
                                        <md-option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }}
                                        </md-option>
                                    </md-select>
                                    <span class="md-error">{{ errors.first($tc('words.city')) }}</span>
                                </md-field>
                            </div>
                            <div
                                class="md-layout-item md-size-100">
                                <md-button class="md-primary save-button" @click="updateDetails()">{{ $tc('words.save') }}</md-button>
                                <md-button
                                    class="md-primary change-button"
                                    @click="modalVisibility=true"
                                >{{ $tc('phrases.changePassword') }}
                                </md-button>
                            </div>
                        </div>
                    </md-card-content>
                </md-card>
            </form>
        </widget>


        <md-dialog :md-active.sync=modalVisibility>
            <md-dialog-title>{{ $tc('phrases.changePassword') }}</md-dialog-title>
            <md-dialog-content>
                <div class="password-edit-container">
                    <form class="md-layout" data-vv-scope="password">
                        <md-field :class="{'md-invalid': errors.has($tc('words.password'))}">
                            <label for="password">{{ $tc('words.password') }}</label>
                            <md-input
                                type="password"
                                :name="$tc('words.password')"
                                id="password"
                                v-validate="'required|min:3|max:15'"
                                v-model="changePassForm.password"
                                ref="passwordRef"
                            />
                            <span class="md-error">{{ errors.first($tc('words.password')) }}</span>
                        </md-field>

                        <md-field :class="{'md-invalid': errors.has($tc('phrases.confirmPassword'))}">
                            <label for="confirmPassword">{{ $tc('phrases.confirmPassword') }}</label>
                            <md-input
                                type="password"
                                :name="$tc('phrases.confirmPassword')"
                                id="confirmPassword"
                                v-model="changePassForm.confirmPassword"
                                v-validate="'required|confirmed:$passwordRef|min:3|max:15'"
                            />
                            <span class="md-error">{{ errors.first($tc('phrases.confirmPassword')) }}</span>
                        </md-field>

                        <!-- <md-progress-bar md-mode="indeterminate" v-if="sending" /> -->
                    </form>
                </div>
            </md-dialog-content>

            <md-dialog-actions>
                <md-button class="md-raised md-primary" @click="submitForm">{{ $tc('words.save') }}</md-button>
                <md-button @click="modalVisibility = false">{{ $tc('words.close') }}</md-button>
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
        this.user.name = this.$store.getters['auth/authenticationService'].authenticateUser.name
        this.user.email = this.$store.getters['auth/authenticationService'].authenticateUser.email
        this.user.id = this.$store.getters['auth/authenticationService'].authenticateUser.id
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
                return
            }
            if (this.selectedCity !== undefined) {
                this.user.city_id = this.selectedCity
            }
            try {
                let response = await this.adminService.updateDetails(this.user)
                if (response.status === 200) {
                    this.alertNotify('success', this.$tc('words.profile',2))
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
                    this.alertNotify('success', this.$tc('words.profile',2))
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
