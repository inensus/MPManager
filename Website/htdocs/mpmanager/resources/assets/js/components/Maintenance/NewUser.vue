<template>
    <div>
        <widget v-if="newUser"
                title="New Maintenance User">
            <div>

                <form @submit.prevent="submitNewUserForm" data-vv-scope="form-user">
                    <md-card>
                        <md-card-content>
                            <div class="md-layout md-gutter">
                                <div class="md-layout-item md-size-50">
                                    <md-field :class="{'md-invalid': errors.has('form-user.name')}">
                                        <label for="name">Name</label>

                                        <md-input type="text" name="name" id="name" v-model="personData.name"
                                                  placeholder="Name"
                                                  v-validate="'required|min:3'"/>
                                        <span class="md-error">{{ errors.first('form-user.name') }}</span>
                                    </md-field>
                                </div>
                                <div class="md-layout-item md-size-50">
                                    <md-field :class="{'md-invalid': errors.has('form-user.surname')}">
                                        <label for="surname">Surname</label>

                                        <md-input type="text" v-validate="'required'" v-model="personData.surname"
                                                  id="surname"
                                                  name="surname"
                                                  placeholder="Surname"/>
                                        <span class="md-error">{{ errors.first('form-user.surname') }}</span>
                                    </md-field>
                                </div>

                                <div class="md-layout-item md-size-50">
                                    <md-field :class="{'md-invalid': errors.has('form-user.mini-grids')}">
                                        <label for="mini-grids" class="control-label">Responsible For (Mini
                                            Grid)</label>

                                        <md-select v-validate="'required'" id="mini-grids" name="mini-grids"
                                                   v-model="personData.mini_grid_id">
                                            <md-option value selected disabled>&#45;&#45; Select &#45;&#45;</md-option>
                                            <md-option
                                                v-for="(miniGrid,index) in miniGrids"
                                                :value="miniGrid.id"
                                                :key="index"
                                            >{{miniGrid.name}}
                                            </md-option>
                                        </md-select>
                                        <span class="md-error">{{ errors.first('form-user.mini-grids') }}</span>
                                    </md-field>
                                </div>
                                <div class="md-layout-item md-size-50">
                                    <md-field :class="{'md-invalid': errors.has('form-user.phone')}">
                                        <label for="phone">Phone</label>

                                        <md-input
                                            type="text"
                                            id="phone"
                                            name="phone"
                                            v-validate="'required'"
                                            v-model="personData.phone"
                                            placeholder="Phone (+___ _+9___ ____)"
                                        />
                                        <span class="md-error">{{ errors.first('form-user.phone') }}</span>
                                    </md-field>
                                </div>
                                <div class="md-layout-item md-size-50">
                                    <md-field :class="{'md-invalid': errors.has('form-user.city')}">
                                        <label for="city">Living In</label>

                                        <md-select id="city" v-validate="'required'" name="city"
                                                   v-model="personData.city_id">
                                            <md-option value selected disabled>&#45;&#45; Select &#45;&#45;</md-option>
                                            <md-option
                                                v-for="(city,index) in cities"
                                                :value="city.id"
                                                :key="index"
                                            >{{city.name}}
                                            </md-option>
                                            <span class="md-error">{{ errors.first('form-user.city') }}</span>
                                        </md-select>
                                    </md-field>
                                </div>
                            </div>
                        </md-card-content>
                        <md-card-actions>
                            <md-button class="md-accent" @click="onClose()">Close</md-button>

                            <md-button class="md-primary btn-lg" type="submit">Save</md-button>
                        </md-card-actions>
                    </md-card>

                </form>

            </div>

        </widget>
        <md-dialog :md-active.sync="ModalVisibility"
        >

            <md-dialog-content>
                <stepper :purchasingType="'maintenance'" v-if="ModalVisibility">

                </stepper>
            </md-dialog-content>
        </md-dialog>
    </div>

</template>

<script>
    import Modal from '../../modal/modal'
    import widget from '../../shared/widget'
    import Stepper from '../../shared/stepper'
    import {CityService} from '../../services/CityService'
    import {MiniGridService} from '../../services/MiniGridService'
    import {MaintenanceService} from '../../services/MaintenanceService'
    import {EventBus} from '../../shared/eventbus'

    export default {
        name: 'NewUser',
        components: {Modal, widget, Stepper},
        props: {
            newUser: false
        },

        data() {
            return {

                miniGrids: [],
                cities: [],
                miniGridService: new MiniGridService(),
                cityService: new CityService(),
                maintenanceService: new MaintenanceService(),
                personData: null,
                ModalVisibility: false,
            }
        },
        created() {
            this.personData = this.maintenanceService.personData
        },
        mounted() {
            EventBus.$on('getLists', () => {
                this.getMiniGrids()
                this.getCities()
            })
            EventBus.$on('closeModal', (data) => {

                this.ModalVisibility = false

            })
        },
        methods: {

            getMiniGrids() {
                this.miniGridService.getMiniGrids().then(data => {

                    this.miniGrids = data

                }).catch(e => {

                    this.alertNotify('error', e)
                })
            },

            getCities() {
                this.cityService.getCities().then(data => {
                    this.cities = data

                }).catch(e => {

                    this.alertNotify('error', e)
                })

            },
            async submitNewUserForm() {

                let validator = await this.$validator.validateAll('form-user')
                if (!validator) {

                    return
                }
                this.maintenanceService.createMaintenance(this.personData).then(response => {
                    this.alertNotify('success', 'Maintenance Person Created')
                    this.personData = {
                        customer_type: 'maintenance',
                        name: null,
                        surname: null,
                        phone: null,
                        city_id: null,
                        mini_grid_id: null,
                        sex: 'male'
                    }
                    this.onClose()
                }).catch((e) => {

                    if (e.status_code === 409) {
                        this.alertNotify('warn', e.message)
                        this.ModalVisibility = true
                    } else {
                        this.alertNotify('error', e.message)
                    }

                })

            },

            onClose() {
                EventBus.$emit('newUserClosed', false)
            },
            alertNotify(type, message) {
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
    .full-width {
        width: 100% !important;
    }
</style>
