<template>
    <widget title="Target Valid Until"
            id="new-target"
            color="red"
            :show-refresh-button="false"
    >
        <md-card>
            <md-card-content>
                <div class="md-layout md-gutter">
                    <div class="md-layout-item md-size-100" v-if="dataIsLoading">
                        <md-progress-bar md-mode="indeterminate"/>
                    </div>
                    <div class="md-layout-item ">
                        <md-field :class="{'md-invalid': errors.has('assigned_for')}">
                            <label> Assigned for</label>
                            <md-select
                                name="assigned_for"
                                v-model="targetAssignType"
                                @md-selected="onTargetTypeChange"
                                v-validate="'required'"
                            >
                                <md-option selected disabled> -- Select destination for target --</md-option>
                                <md-option value="cluster">Cluster</md-option>
                                <md-option value="mini-grid">Mini Grid</md-option>
                            </md-select>
                            <span class="md-error">{{ errors.first('assigned_for') }}</span>
                        </md-field>
                    </div>

                    <div class="md-layout-item ">
                        <md-field :class="{'md-invalid': errors.has('assigned_to')}">
                            <label> Assigned to</label>
                            <md-select :disabled="targetDestinations.length===0 || dataIsLoading === true"
                                       v-model="targetAssignId"
                                       v-validate="'required'"
                                       name="assigned_to"
                            >
                                <md-option selected disabled> -- Select destination for target --</md-option>
                                <md-option v-for="(targetDestination,index) in targetDestinations"
                                           :key="index"
                                           :value="targetDestination.id">
                                    {{targetDestination.name}}
                                </md-option>
                            </md-select>
                            <span class="md-error">{{ errors.first('assigned_to') }}</span>
                        </md-field>
                    </div>

                    <div class="md-layout-item ">

                        <md-datepicker
                            :class="{'md-invalid': errors.has('valid_to')}"
                            name="valid_to"
                            v-validate.initial="'required'"
                            :md-model-type="String"
                            md-immediately
                            v-model="targetValidUntil"
                        >
                            <label>Valid until</label>
                            <span class="md-error">{{ errors.first('valid_to') }}</span>
                        </md-datepicker>
                    </div>
                </div>

                <h3>Targets for Connection Types</h3>


                <md-table>
                    <md-table-row v-for="(connection) in connectionTypes.list" :key="connection.id">
                        <md-table-cell> {{connection.name}}</md-table-cell>
                        <md-table-cell>
                            <md-field>
                                <label>New Connections</label>
                                <md-input type="number"
                                          class="form-control full-width"
                                          v-model="connection.target.newConnection"
                                />
                            </md-field>
                        </md-table-cell>

                        <md-table-cell>
                            <md-field>
                                <label>Connected Power</label>
                                <md-input type="number"
                                          class="form-control full-width"
                                          v-model="connection.target.connectedPower"
                                />
                            </md-field>
                        </md-table-cell>

                        <md-table-cell>
                            <md-field>
                                <label>Energy(kWh) / Month</label>
                                <md-input type="number"
                                          class="form-control full-width"
                                          v-model="connection.target.energyPerMonth"/>
                            </md-field>
                        </md-table-cell>

                        <md-table-cell>
                            <md-field>
                                <label>Revenue / Month</label>
                                <md-input type="number"
                                          class="form-control full-width"
                                          v-model="connection.target.totalRevenue"/>
                            </md-field>
                        </md-table-cell>

                        <md-table-cell>
                            <md-field>
                                <label>Avg Revenue / Month</label>
                                <md-input type="text"
                                          class="form-control full-width"
                                          v-model="connection.target.averageRevenuePerMonth"/>
                            </md-field>
                        </md-table-cell>

                        <md-table-cell>
                            {{addCustomers(connection.target.newConnection ,
                            numberOfCustomers.findConnectionCustomers(connection.id))
                            }}
                        </md-table-cell>
                    </md-table-row>

                    <md-table-row>
                        <md-table-cell>Total</md-table-cell>
                        <md-table-cell>{{total['newConnection']}}</md-table-cell>
                        <md-table-cell>{{total['connectedPower']}}</md-table-cell>
                        <md-table-cell>{{total['energyPerMonth']}}</md-table-cell>
                        <md-table-cell>{{readable(total['totalRevenue'])}}</md-table-cell>
                        <md-table-cell>{{readable(total['totalRevenue']/total['totalCustomers']+total['newConnection'])
                            }}
                        </md-table-cell>
                        <md-table-cell>{{readable(total['totalCustomers']+total['newConnection'])}}</md-table-cell>
                    </md-table-row>
                </md-table>


            </md-card-content>
            <md-card-actions>

                <md-button class="md-dense md-raised md-primary" @click="submitTarget">
                    <md-icon>save</md-icon>
                    Save Target
                </md-button>
            </md-card-actions>


        </md-card>
    </widget>
</template>

<script>
import Widget from '../../shared/widget'
import { AvailablityChecker } from '../../classes/target/AvailablityChecker'
import { ConnectionTypes } from '../../classes/connection/ConnectionTypes'
import { NumberOfCustomers } from '../../classes/connection/NumberOfCustomers'
import { currency } from '../../mixins/currency'
import { Targets } from '../../classes/target/Targets'

export default {
    name: 'NewTarget',
    components: {Widget },
    mixins: [currency],
    computed: {
        total: {
            cache: false,
            deep: true,
            get: function () {
                let total = {
                    averageRevenuePerMonth: 0,
                    connectedPower: 0,
                    energyPerMonth: 0,
                    newConnection: 0,
                    totalRevenue: 0,
                    totalCustomers: this.numberOfCustomers.total,
                }

                if (this.connectionTypes.list.length === 0) {
                    return total
                }

                this.connectionTypes.list.reduce(
                    function (prev, next) {
                        prev['averageRevenuePerMonth'] += parseInt(next['target']['averageRevenuePerMonth'])
                        prev['connectedPower'] += parseInt(next['target']['connectedPower'])
                        prev['energyPerMonth'] += parseInt(next['target']['energyPerMonth'])
                        prev['newConnection'] += parseInt(next['target']['newConnection'])
                        prev['totalRevenue'] += parseInt(next['target']['totalRevenue'])
                        return prev
                    }, total
                )
                return total

            }
        },

    },
    mounted () {
        this.connectionTypes.getConnectionTypes()
        this.numberOfCustomers.getList()
    },

    data () {
        return {
            dataIsLoading: false,
            targetDestinations: [], // mini-grid or cluster list
            targetAssignType: null, // determines if the target is whether for a mini-grip or for a cluster.
            targetAssignId: null, //the id of the mini-grid or cluster
            slotChecker: new AvailablityChecker(),
            connectionTypes: new ConnectionTypes(),
            numberOfCustomers: new NumberOfCustomers(),
            targets: new Targets(),
            targetValidUntil: new Date(),
        }
    },
    methods: {
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
        },
        async submitTarget () {
            let validation = await this.$validator.validateAll()
            if (!validation) {
                this.alertNotify('Warning', 'Please fill all required field')
                return
            }

            if (this.targetValidUntil === '') {
                this.$swal('Period not selected', 'Please select the start date for the period from the date picker', 'error')
                return
            }
            if (this.targetAssignId === null) {
                this.$swal('Target not selected', 'Please select either a cluster or a mini-grid.', 'error')
                return
            }

            this.targets.store(this.targetValidUntil, this.targetAssignType, this.targetAssignId,
                this.connectionTypes.list
            )
            //success message
            this.$swal('Success', 'Target stored successfully', 'success')
        },
        addCustomers (newConnections, connections) {
            return parseInt(newConnections) + parseInt(connections)
        },

        onTargetTypeChange (value) {
            this.dataIsLoading = true
            if (value === 'mini-grid') { //get list of mini-grids
                this.updateTargetDestination(resources.miniGrids.list)
            } else { //get list of clusters
                this.updateTargetDestination(resources.clusters.list)
            }
        },

        updateTargetDestination (url) {
            axios.get(url).then((response) => {
                this.targetDestinations = response.data.data
                this.dataIsLoading = false // hide progress bar
            })
        }
    }
}
</script>

<style scoped>


    .full-width {
        width: 100%;
    }

    .mt {
        margin-top: 10px;
    }

    .divider {
        border-right: 1px dashed;
        padding-right: 10px;
    }
</style>
