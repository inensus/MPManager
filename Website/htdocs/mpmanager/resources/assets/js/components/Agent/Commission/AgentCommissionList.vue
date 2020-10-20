<template>
    <div>
        <new-commission :addNewCommission="showNewCommission"/>
        <widget
            :class="'col-sm-6 col-md-5'"
            :button-text="'Add Commission Type'"
            :button="true"
            title="Commission Types"
            @widgetAction="newCommission"
            color="green"
            :subscriber="subscriber"
        >

            <md-progress-bar md-mode="indeterminate" v-if="loading"/>

                    <md-table md-sort="id" md-sort-order="asc">
                        <md-table-row>
                            <md-table-head v-for="(item, index) in headers" :key="index">{{item}}</md-table-head>
                        </md-table-row>
                        <md-table-row v-for="(item, index) in agentCommissionService.list" :key="index">
                            <md-table-cell md-sort-by="id" md-label="ID">{{item.id}}</md-table-cell>
                            <md-table-cell md-label="Name">
                                <div v-if="editCommission === item.id">
                                    <md-field :class="{'md-invalid': errors.has('name')}">
                                        <label for="name">Name </label>
                                        <md-input name="name" id="name"
                                                  v-model="item.name"
                                                  v-validate="'required|min:3'"
                                        />
                                        <span class="md-error">{{ errors.first('name') }}</span>
                                    </md-field>

                                </div>
                                <div v-else>
                                    {{item.name}}
                                </div>
                            </md-table-cell>
                            <md-table-cell md-label="Energy Commission">

                                <div v-if="editCommission === item.id">

                                    <md-field :class="{'md-invalid': errors.has('energyCommission')}">
                                        <label for="energyCommission">Energy Commission </label>
                                        <md-input name="energyCommission" id="energyCommission"
                                                  v-model="item.energyCommission"
                                                  v-validate="'required|min_value:0'" type="number"
                                        />
                                        <span class="md-error">{{ errors.first('energyCommission') }}</span>
                                    </md-field>

                                </div>
                                <div v-else>
                                    {{item.energyCommission}}
                                </div>
                            </md-table-cell>
                            <md-table-cell md-label="Appliance Commission">
                                <div v-if="editCommission === item.id">

                                    <md-field :class="{'md-invalid': errors.has('applianceCommission')}">
                                        <label for="applianceCommission">Appliance Commission </label>
                                        <md-input name="applianceCommission" id="applianceCommission"
                                                  v-model="item.applianceCommission"
                                                  v-validate="'required|min_value:0'" type="number"
                                        />
                                        <span
                                            class="md-error">{{ errors.first('applianceCommission') }}</span>
                                    </md-field>

                                </div>
                                <div v-else>
                                    {{item.applianceCommission}}
                                </div>
                            </md-table-cell>
                            <md-table-cell md-label="Risk Balance">
                                <div v-if="editCommission === item.id">

                                    <md-field :class="{'md-invalid': errors.has('riskBalance')}">
                                        <label for="riskBalance">Risk Balance (must be negative)</label>
                                        <md-input name="riskBalance"
                                                  id="riskBalance"
                                                  max="0"
                                                  v-model="item.riskBalance"
                                                  v-validate="'required|max_value:0'" type="number"
                                        />
                                        <span class="md-error">{{ errors.first('riskBalance') }}</span>
                                    </md-field>

                                </div>
                                <div v-else>
                                    {{item.riskBalance}}
                                </div>
                            </md-table-cell>
                            <md-table-cell md-label="#">
                                <div v-if="editCommission === item.id">

                                    <md-button class="md-icon-button" @click="updateCommission(item)">
                                        <md-tooltip md-direction="top">Save</md-tooltip>
                                        <md-icon>save</md-icon>
                                    </md-button>
                                    <md-button class="md-icon-button" @click="editCommission = null">
                                        <md-tooltip md-direction="top">Close</md-tooltip>
                                        <md-icon>close</md-icon>
                                    </md-button>

                                </div>
                                <div v-else>
                                    <md-button class="md-icon-button" @click="editCommission = item.id">
                                        <md-tooltip md-direction="top">Edit</md-tooltip>
                                        <md-icon>edit</md-icon>
                                    </md-button>
                                    <md-button class="md-icon-button" @click="confirmDelete(item)">
                                        <md-tooltip md-direction="top">Delete</md-tooltip>
                                        <md-icon>delete</md-icon>
                                    </md-button>
                                </div>
                            </md-table-cell>
                        </md-table-row>
                    </md-table>
        </widget>
    </div>
</template>
<script>
import Widget from '../../../shared/widget'
import { EventBus } from '../../../shared/eventbus'
import { AgentCommissionService } from '../../../services/AgentCommissionService'
import NewCommission from '../Commission/NewCommission'

export default {
    name: 'AgentCommissionList',
    data () {
        return {
            subscriber:'agent-commission-list',
            agentCommissionService: new AgentCommissionService(),
            showNewCommission: false,
            headers: ['ID', 'Name', 'Energy Commission', 'Appliance Commission', 'Risk Balance', '#'],
            tableName: 'Agent Commission Types',
            editCommission: null,
            loading: false
        }
    },
    components: {
        Widget,
        NewCommission
    },
    mounted () {
        this.getAgentCommissions()
        EventBus.$on('commissionAdded', this.closeNewCommission)
        EventBus.$on('newCommissionClosed', () => {
            this.showNewCommission = false
        })
    },
    beforeDestroy () {
        EventBus.$off('commissionAdded', this.closeNewCommission)
    },
    methods: {

        async closeNewCommission () {
            await this.getAgentCommissions()
            this.showNewCommission = false
        },
        async newCommission () {
            this.agentCommissionService.resetAgentCommission()
            this.showNewCommission = true
        },
        async getAgentCommissions () {
            try {
                await this.agentCommissionService.getAgentCommissions()
                EventBus.$emit('widgetContentLoaded',this.subscriber,this.agentCommissionService.list.length)
            } catch (e) {
                this.loading = false
                this.alertNotify('error', e.message)
            }
        },
        async updateCommission (commission) {
            try {
                this.loading = true
                await this.agentCommissionService.updateAgentCommission(commission)
                this.alertNotify('success', 'Agent commission updated!')
                await this.getAgentCommissions()
                this.loading = false
            } catch (e) {
                this.loading = false
                this.alertNotify('error', e.message)
            }
        },
        async confirmDelete (commission) {

            this.$swal({
                type: 'question',
                title: 'Delete Agent Commission',
                width: '35%',
                confirmButtonText: 'Confirm',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                focusCancel: true,
                html:
                        '<div style="text-align: left; padding-left: 5rem" class="checkbox">' +
                        '  <label>' +
                        '    <input type="checkbox" name="confirmation" id="confirmation" >' +
                        '   I confirm that ' +
                        commission.name +
                        ' ' +
                        ' will be deleted' +
                        '  </label>' +
                        '</div>'
            }).then(result => {
                let answer = document.getElementById('confirmation').checked
                if ('value' in result) {
                    if (answer) {
                        this.deleteCommission(commission.id)
                    }
                }
            })

        },

        async deleteCommission (commissionId) {
            try {
                this.loading = true
                await this.agentCommissionService.deleteAgentCommission(commissionId)
                this.alertNotify('success', 'Agent commission deleted!')
                await this.getAgentCommissions()
                this.loading = false
            } catch (e) {
                this.loading = false
                this.alertNotify('error', e.message)
            }
        },
        alertNotify (type, message) {
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message
            })
        },
    },
}

</script>
<style scoped>

</style>

