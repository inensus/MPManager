<template>
    <div>
        <widget v-if="newUser"
                title="New Maintenance User"
                color="red"
              >
            <div>

                <form @submit.prevent="submitNewUserForm" data-vv-scope="form-user">
                    <md-card>
                        <md-card-content>
                            <div class="md-layout md-gutter">
                                <div class="md-layout-item md-size-50">
                                    <md-field :class="{'md-invalid': errors.has('form-user.name')}">
                                        <label for="name">Name</label>

                                        <md-input type="text" name="name" id="name"
                                                  v-model="maintenanceService.personData.name"
                                                  placeholder="Name"
                                                  v-validate="'required|min:3'"/>
                                        <span class="md-error">{{ errors.first('form-user.name') }}</span>
                                    </md-field>
                                </div>
                                <div class="md-layout-item md-size-50">
                                    <md-field :class="{'md-invalid': errors.has('form-user.surname')}">
                                        <label for="surname">Surname</label>

                                        <md-input type="text" v-validate="'required'"
                                                  v-model="maintenanceService.personData.surname"
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
                                                   v-model="maintenanceService.personData.mini_grid_id">
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
                                            v-model="maintenanceService.personData.phone"
                                            placeholder="Phone (+___ _+9___ ____)"
                                        />
                                        <span class="md-error">{{ errors.first('form-user.phone') }}</span>
                                    </md-field>
                                </div>
                                <div class="md-layout-item md-size-50">
                                    <md-field :class="{'md-invalid': errors.has('form-user.city')}">
                                        <label for="city">Living In</label>

                                        <md-select id="city" v-validate="'required'" name="city"
                                                   v-model="maintenanceService.personData.city_id">
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
                            <md-progress-bar md-mode="indeterminate" v-if="loading"/>
                        </md-card-content>
                        <md-card-actions>
                            <md-button class="md-primary btn-lg" :disabled="loading" type="submit">Save</md-button>
                            <md-button class="md-accent" @click="onClose()">Close</md-button>
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
        <redirection-modal :redirection-url="redirectionUrl" :imperative-item="imperativeItem"
                           :dialog-active="redirectDialogActive"/>
    </div>

</template>

<script>
import widget from '../../shared/widget'
import Stepper from '../../shared/stepper'
import { CityService } from '../../services/CityService'
import { MiniGridService } from '../../services/MiniGridService'
import { MaintenanceService } from '../../services/MaintenanceService'
import { EventBus } from '../../shared/eventbus'
import RedirectionModal from '../../shared/RedirectionModal'

export default {
    name: 'NewUser',
    components: { widget, Stepper, RedirectionModal },
    props: {
        newUser: {
            type:Boolean,
            default:false
        }
    },

    data () {
        return {
            miniGrids: [],
            cities: [],
            miniGridService: new MiniGridService(),
            cityService: new CityService(),
            maintenanceService: new MaintenanceService(),
            ModalVisibility: false,
            loading: false,
            imperativeItem: 'Mini-Grid',
            redirectDialogActive: false,
            redirectionUrl: '/locations/add-mini-grid'
        }
    },

    mounted () {
        EventBus.$on('getLists', () => {
            this.getMiniGrids()
            this.getCities()
        })
        EventBus.$on('closeModal', this.closeModal)
    },
    methods: {
        closeModal(){
            this.ModalVisibility = false
        },
        async getMiniGrids () {

            try {
                this.miniGrids = await this.miniGridService.getMiniGrids()
                if (this.miniGrids.length === 0) {
                    this.redirectDialogActive = true
                }
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async getCities () {
            try {
                this.cities = await this.cityService.getCities()
            } catch (e) {
                this.alertNotify('error', e.message)
            }

        },
        async submitNewUserForm () {

            let validator = await this.$validator.validateAll('form-user')
            if (!validator) {

                return
            }
            try {
                this.loading = true
                await this.maintenanceService.createMaintenance(this.maintenanceService.personData)
                this.loading = false
                this.alertNotify('success', 'Maintenance Person Created')
                this.maintenanceService.resetPersonData()
                this.onClose()
            } catch (e) {
                this.loading = false
                if (e.status_code === 409) {
                    this.alertNotify('warn', e.message)
                    this.ModalVisibility = true
                } else {
                    this.alertNotify('error', e.message)
                }
            }

        },

        onClose () {
            EventBus.$emit('newUserClosed', false)
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
    .full-width {
        width: 100% !important;
    }
</style>
