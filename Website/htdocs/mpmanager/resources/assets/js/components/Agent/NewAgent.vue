<template>
    <div>
        <widget
            v-if="addAgent"
            title="Add New Agent"
            color="red"
        >
            <md-card>
                <md-card-content>
                    <div class="md-layout md-gutter">
                        <div class="md-layout-item md-large-size-100 md-medium-size-100 md-small-size-100">
                            <form class="md-layout md-gutter" data-vv-scope="Agent-Form">
                                <!--name-->
                                <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                    <md-field :class="{'md-invalid': errors.has('Agent-Form.name')}"
                                    >
                                        <label for="name">Name</label>
                                        <md-input
                                            id="name"
                                            name="name"
                                            v-model="newAgent.name"
                                            v-validate="'required|min:3'"
                                        />
                                        <span class="md-error">{{ errors.first('Agent-Form.name') }}</span>
                                    </md-field>
                                </div>

                                <!--surname-->
                                <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                    <md-field :class="{'md-invalid': errors.has('Agent-Form.surname')}"
                                    >
                                        <label for="surname">Surname</label>
                                        <md-input
                                            id="surname"
                                            name="surname"
                                            v-model="newAgent.surname"
                                            v-validate="'required|min:3'"
                                        />
                                        <span class="md-error">{{ errors.first('Agent-Form.surname') }}</span>
                                    </md-field>
                                </div>

                                <!--minigrid-->
                                <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                    <md-field :class="{'md-invalid': errors.has('Agent-Form.miniGridName')}">
                                        <label>Mini Grid</label>
                                        <md-select
                                            v-model="newAgent.miniGridId"
                                            name="miniGridName"
                                            id="miniGridName"
                                            v-validate="'required'"

                                        >
                                            <md-option v-for="(mg) in miniGrids" :value="mg.id"
                                                       :key="mg.id">
                                                {{mg.name}}
                                            </md-option>
                                        </md-select>
                                        <span class="md-error">{{ errors.first('Agent-Form.miniGridName') }}</span>

                                    </md-field>
                                </div>

                                <!--phone-->
                                <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50">
                                    <md-field :class="{'md-invalid': errors.has('Agent-Form.phone')}">
                                        <label for="phone">Phone</label>

                                        <md-input
                                            type="text"
                                            id="phone"
                                            name="phone"
                                            v-validate="'required'"
                                            v-model="newAgent.phone"
                                            placeholder="Phone (+___ _+9___ ____)"
                                        />
                                        <span class="md-error">{{ errors.first('form-user.phone') }}</span>
                                    </md-field>
                                </div>

                                <!--email-->
                                <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                    <md-field :class="{'md-invalid': errors.has('Agent-Form.email')}"
                                    >
                                        <label for="email">Email</label>
                                        <md-input
                                            id="email"
                                            name="email"
                                            v-model="newAgent.email"
                                            v-validate="'required|min:3'"
                                        />
                                        <span class="md-error">{{ errors.first('Agent-Form.email') }}</span>
                                    </md-field>
                                </div>


                                <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 ">


                                    <md-datepicker name="birthDate" id="birthDate" md-immediately
                                                   v-model="newAgent.birthday"
                                    >
                                        <label for="birth-date">Birthday :</label>
                                    </md-datepicker>


                                </div>
                                <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                    <md-field :class="{'md-invalid': errors.has('Agent-Form.gender')}"
                                    >


                                        <label for="gender">Gender :</label>
                                        <md-select name="gender" id="gender" v-model="newAgent.gender"
                                                   v-validate="'required'">
                                            <md-option disabled v-if="newAgent.gender==null">-- Select --
                                            </md-option>
                                            <md-option value="male">Male</md-option>
                                            <md-option value=" female">Female</md-option>
                                        </md-select>

                                        <span class="md-error">{{ errors.first('Agent-Form.gender') }}</span>

                                    </md-field>
                                </div>
                                <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                    <md-field :class="{'md-invalid': errors.has('Agent-Form.commission')}"
                                    >

                                        <label for="commission">Commission Type :</label>
                                        <md-select name="commission" id="commission" v-validate="'required'"
                                                   v-model="newAgent.commissionTypeId">
                                            <md-option v-for="(commission) in agentCommissions"
                                                       :value="commission.id" :key="commission.id">{{commission.name}}
                                            </md-option>

                                        </md-select>
                                        <span class="md-error">{{ errors.first('Agent-Form.commission') }}</span>

                                    </md-field>
                                </div>
                                <!--password-->
                                <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                    <md-field :class="{'md-invalid': errors.has('Agent-Form.password')}"
                                    >
                                        <label for="email">Password</label>
                                        <md-input
                                            id="password"
                                            name="password"
                                            v-model="newAgent.password"
                                            v-validate="'required|min:3|max:15'"
                                            ref="passwordRef"
                                            type="password"
                                        />
                                        <span class="md-error">{{ errors.first('Agent-Form.password') }}</span>
                                    </md-field>
                                </div>

                                <!--confirmPassword-->
                                <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                    <md-field :class="{'md-invalid': errors.has('Agent-Form.confirmPassword')}"
                                    >
                                        <label for="email">Confirm Password</label>
                                        <md-input
                                            id="confirmPassword"
                                            name="confirmPassword"
                                            v-model="confirmPassword"
                                            v-validate="'required|confirmed:passwordRef|max:15'"
                                            type="password"
                                        />
                                        <span class="md-error">{{ errors.first('Agent-Form.confirmPassword') }}</span>
                                    </md-field>
                                </div>
                            </form>

                        </div>

                    </div>
                    <md-progress-bar md-mode="indeterminate" v-if="loading"/>
                </md-card-content>
                <md-card-actions>

                    <md-button role="button" class="md-raised md-primary" :disabled="loading" @click="saveAgent">Save
                    </md-button>
                    <md-button role="button" class="md-raised" @click="hide">Close</md-button>
                </md-card-actions>
            </md-card>
        </widget>
        <redirection-modal :redirection-url="redirectionUrl" :imperative-item="imperativeItem"
                           :dialog-active="redirectDialogActive"/>
    </div>

</template>
<script>
import Widget from '../../shared/widget'
import { AgentService } from '../../services/AgentService'
import { MiniGridService } from '../../services/MiniGridService'
import CountryService from '../../services/CountryService'
import { EventBus } from '../../shared/eventbus'
import { AgentCommissionService } from '../../services/AgentCommissionService'
import RedirectionModal from '../../shared/RedirectionModal'

export default {
    name: 'AddAgent',
    components: { Widget, RedirectionModal },
    props: {
        addAgent: {
            default: false,
            type: Boolean
        },
    },
    data () {
        return {
            agentService: new AgentService(),
            miniGridService: new MiniGridService(),
            countryService: new CountryService(),
            agentCommissionService: new AgentCommissionService(),
            agentCommissions: [],
            newAgent: {},
            users: [],
            selectedUser: null,
            selectedMiniGridId: '',
            miniGrids: [],
            countries: [],
            confirmPassword: null,
            loading: false,
            redirectionUrl: '/locations/add-mini-grid',
            imperativeItem: 'Mini-Grid',
            redirectDialogActive: false
        }
    },

    mounted () {
        this.getMiniGrids()
        this.getCountries()
        this.getAgentCommissions()
        this.newAgent = this.agentService.agent

    },
    methods: {
        async getMiniGrids () {
            try {
                this.miniGrids = await this.miniGridService.getMiniGrids()
                if (this.miniGrids.length < 0) {
                    this.redirectDialogActive = true
                }
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async getCountries () {
            try {
                this.countries = await this.countryService.getCountries()
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async getAgentCommissions () {
            try {
                this.agentCommissions = await this.agentCommissionService.getAgentCommissions()
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async selectMiniGrid (miniGridId) {

            this.selectedMiniGridId = miniGridId
        },
        async selectNationality (miniGridId) {

            this.selectedMiniGridId = miniGridId
        },
        async saveAgent () {

            let validator = await this.$validator.validateAll('Agent-Form')

            if (validator) {
                this.loading = true
                try {
                    await this.agentService.createAgent()
                    this.loading = false
                    this.hide()
                    this.alertNotify('success', 'Agent added successfully')
                } catch (e) {
                    this.loading = false
                    this.alertNotify('error', e.message)
                }
            }

        },
        hide () {
            EventBus.$emit('closed')
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

</style>
