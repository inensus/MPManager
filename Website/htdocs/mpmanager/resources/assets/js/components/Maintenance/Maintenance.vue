<template>
    <div>


        <new-user :newUser="newUser"
                  @stored="getEmployees"
                  @closed=" () => {this.newUser = false}"></new-user>
        <widget
            title="New Maintenance Request"
            button-text="New User"
            :button="true"
            :callback="openNewUser"
            color="green"
        >

            <form @submit.prevent="submitMaintainForm" data-vv-scope="form-maintain">
                <md-card>
                    <md-card-content>
                        <div class="md-layout md-gutter">
                            <div class="md-layout-item md-size-100">
                                <md-field :class="{'md-invalid': errors.has('form-maintain.title')}">
                                    <label for="title">Title</label>
                                    <md-input v-model="maintenanceData.title" type="text" class="input-w form-control"
                                              id="title"
                                              name="title"
                                              v-validate="'required'"
                                              placeholder="Job Title"></md-input>
                                    <span class="md-error">{{ errors.first('form-maintain.title') }}</span>
                                </md-field>
                            </div>
                            <div class="md-layout-item md-size-50">
                                <md-field :class="{'md-invalid': errors.has('form-maintain.employee')}">
                                    <label for="employee">Assigned To</label>
                                    <md-select id="employee"
                                               name="employee"
                                               v-validate="'required'"
                                               v-model="maintenanceData.assigned">
                                        <md-option value="" disabled selected>-- Select --</md-option>
                                        <md-option v-for="employee in employees" :key="employee.person.id"
                                                   v-if="employee.person"
                                                   :value="employee.person.id">
                                            {{employee.person.name}}
                                            {{employee.person.surname}}
                                        </md-option>
                                    </md-select>
                                    <span class="md-error">{{ errors.first('form-maintain.title') }}</span>
                                </md-field>
                            </div>
                            <div class="md-layout-item md-size-50">
                                <md-field :class="{'md-invalid': errors.has('form-maintain.category')}">
                                    <label for="category">Category</label>
                                    <md-select id="category"
                                               name="category"
                                               v-validate="'required'"
                                               v-model="maintenanceData.category">
                                        <md-option value="" disabled selected>-- Select --</md-option>
                                        <md-option v-for="(category, index) in categories" :key="index"
                                                   :value="category.id">
                                            {{category.label_name}}
                                        </md-option>
                                    </md-select>
                                    <span class="md-error">{{ errors.first('form-maintain.category') }}</span>

                                </md-field>
                            </div>
                            <div class="md-layout-item md-size-50">
                                <md-field :class="{'md-invalid': errors.has('form-maintain.amount')}">
                                    <label for="amount">Amount</label>
                                    <span class="md-prefix">$</span>
                                    <md-input v-model="maintenanceData.amount" type="text" id="amount" name="amount"
                                              v-validate="'required'"
                                              placeholder="Amount"></md-input>
                                    <span class="md-error">{{ errors.first('form-maintain.amount') }}</span>
                                </md-field>
                            </div>
                            <div class="md-layout-item md-size-50">
                                <div>
                                    <md-datepicker v-model="selectedDue" name="date" v-validate="'required'">
                                        <label>Due Date</label>
                                    </md-datepicker>
                                    <span class="md-error">{{ errors.first('form-maintain.date') }}</span>
                                </div>
                            </div>
                            <div class="md-layout-item md-size-100">
                                <md-field :class="{'md-invalid': errors.has('form-maintain.description')}">
                                    <label for="description">Description</label>
                                    <md-textarea id="description" name="description" v-validate="'required'"
                                                 v-model="maintenanceData.description"></md-textarea>
                                </md-field>
                            </div>
                        </div><!-- end layout -->

                    </md-card-content>

                    <md-card-actions>
                        <md-button class="md-raised md-primary" type="submit">
                            <font-awesome-icon icon="save"></font-awesome-icon>
                            Save
                        </md-button>
                    </md-card-actions>
                </md-card>
            </form>
        </widget>
    </div>
</template>

<script>
    import Widget from '../../shared/widget'
    import Datepicker from 'vuejs-datepicker'
    import NewUser from './NewUser'
    import { EventBus } from '../../shared/eventbus'
    import { TicketService } from '../../services/TicketService'
    import { MaintenanceService } from '../../services/MaintenanceService'
    import { SmsService } from '../../services/SmsService'

    export default {
        name: 'Maintenance',
        components: { NewUser, Datepicker, Widget },
        data () {
            return {
                newUser: false,
                categories: [],
                employees: [],
                maintenanceData: null,
                ticketService: new TicketService(),
                maintenanceService: new MaintenanceService(),
                smsService: new SmsService(),
                selectedDue: null
            }
        },
        watch: {
            selectedDue: function (date) {
                this.dueDateSelected(date)
            }
        },
        created () {
            this.maintenanceData = this.maintenanceService.maintenanceData
            this.maintenanceData.creator = this.$store.state.admin.id
        },
        mounted () {
            this.getCategories()
            this.getEmployees()
            EventBus.$on('newUserClosed', (e) => {
                this.newUser = false
                this.getEmployees()
            })
        },
        methods: {
            getCategories () {
                this.ticketService.getCategories().then(data => {
                    this.categories = data
                }).catch(e => {
                    this.alertNotify('error', e)
                })
            },
            getEmployees () {
                this.maintenanceService.getEmployees().then(data => {
                    this.employees = data
                }).catch(e => {
                    this.alertNotify('error', e)
                })

            },
            dueDateSelected (date) {

                if (date === null) {
                    return
                }
                this.maintenanceService.setDueDate(date)
            },
            async submitMaintainForm () {
                await this.validateForm('form-maintain').then(result => {
                    if (result) {
                        this.sending = true
                        this.saveTicket()
                    }
                    if (!result) {
                    }
                })
            },
            saveTicket () {
                this.ticketService.createMaintenanceTicket(this.maintenanceData).then(status => {

                    this.smsService.sendMaintenanceSms(this.maintenanceData).then(response => {

                        this.alertNotify('success', 'The Task created successfully. The Person will also be notified by sms')
                        this.maintenanceService.maintenanceData = {
                            maintenance: true,
                            title: null,
                            assigned: null,
                            category: null,
                            amount: null,
                            description: null,
                            dueDate: null,
                        }
                    })

                }).catch(e => {
                    this.alertNotify('error', e)
                })
                this.sending = false
            },
            async validateForm (scope) {
                return await this.$validator.validateAll(scope)
            },
            openNewUser () {
                EventBus.$emit('getLists')
                this.newUser = true
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
    .label-w {
        width: 84vw;
    }

    .input-w {
        width: 84vw;
    }

    .dp {
        margin-left: 10vw;
        margin-top: -22px;
    }

    .dp-input {
        width: 100% !important;
    }

    .margin {
        margin-top: 7px;
        margin-bottom: 8px;
        content: " ";
        clear: both;
    }
</style>
