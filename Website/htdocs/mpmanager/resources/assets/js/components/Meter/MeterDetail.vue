<template>
    <div class="page-container" id="widget-grid">
        <div class="md-layout md-gutter">
            <div class="md-layout-item md-size-35 md-small-size-100">
                <widget :title="'Overview'" class="col-sm-5" :id="'meter-overview'">
                    <md-card>
                        <md-card-header>
                            <div class="md-title">Basic</div>
                        </md-card-header>

                        <md-card-content v-if="meter!==null && meter.loaded===true">
                            <div class="md-layout">
                                <div class="md-layout-item">Registered</div>
                                <div class="md-layout-item">{{meter.registered}}</div>
                            </div>
                            <div class="md-layout">
                                <div class="md-layout-item">Owner</div>
                                <div class="md-layout">
                                    <div class="md-layout-item">
                                        <a href="javascript:void(0)" @click="navigateOwner(meter.owner.id)">
                                            {{meter.owner.name}}
                                            {{meter.owner.surname}}
                                        </a>
                                        <i class="fa fa-pencil" @click="showOwnerEdit = true"></i>
                                    </div>
                                    <div class="md-layout-item" v-if="showOwnerEdit" >
                                        <div class="md-layout-item">New Owner</div>

                                        <md-autocomplete
                                            v-model="customerSearchTerm"
                                            :md-options="searchNames"
                                            @md-changed="searchFor"
                                            @md-opened="searchFor"
                                            @md-selected="selectCustomer"
                                        >
                                            <label>Customer Name</label>
                                            <template slot="md-autocomplete-item" slot-scope="{ item }">{{ item.name }}</template>
                                        </md-autocomplete>
                                        <md-button v-if="showOwnerEdit" class="md-icon-button md-primary" @click="saveNewOwner()"><md-icon>save</md-icon></md-button>
                                        <md-button class="md-accent md-icon-button" @click="closeOwnerEdit()"><md-icon>close</md-icon></md-button>
                                    </div>
                                </div>

                            </div>
                            <div class="md-layout">
                                <div class="md-layout-item">Total Revenue</div>
                                <div class="md-layout-item">
                                    <div v-if="meter.totalRevenue">{{ readable(meter.totalRevenue)}} TZS</div>
                                    <div v-else>No Data</div>
                                </div>
                            </div>
                            <div class="md-layout">
                                <div class="md-layout-item">Last Payment</div>
                                <div class="md-layout-item">3 days ago</div>
                            </div>
                        </md-card-content>
                    </md-card>

                    <md-card>
                        <md-card-header>
                            <div class="md-title">Meter Details</div>
                        </md-card-header>

                        <md-card-content v-if="meter!==null && meter.loaded===true">
                            <div class="md-layout">
                                <div class="md-layout-item">Manufacturer</div>
                                <div
                                    class="md-layout-item"
                                >{{meter.manufacturer.name}} ( {{meter.manufacturer.website}})
                                </div>
                            </div>

                            <div class="md-layout">
                                <div class="md-layout-item">Serial Number</div>
                                <div class="md-layout-item">{{meter.serialNumber}}</div>
                            </div>
                            <div class="md-layout">
                                <div class="md-layout-item">Tariff</div>
                                <div class="md-layout-item">
                                    <div v-if="editTariff===false" class="col-sm-6">
                                        {{meter.tariff.name}}
                                        <i class="fa fa-pencil" @click="editTariff = true"></i>
                                    </div>
                                    <div class="md-layout" v-else>
                                        <div class="md-layout-item">
                                            <md-field>
                                                <label for="tariff">Tariff</label>
                                                <md-select name="tariff" v-model="newTariff">
                                                    <md-option v-for="tariff in tariffs"
                                                               :key="tariff.id" :value="tariff.id">
                                                        {{tariff.name}} {{
                                                        tariff.price/100}}
                                                    </md-option>
                                                </md-select>
                                            </md-field>
                                        </div>
                                        <md-button class="md-icon-button" @click="updateTariff(newTariff)">
                                            <md-icon class="md-primary">save</md-icon>
                                        </md-button>
                                        <md-button class="md-icon-button" @click="editTariff=false">
                                            <md-icon class="md-accent">cancel</md-icon>
                                        </md-button>
                                    </div>
                                </div>
                            </div>
                            <div class="md-layout">
                                <div class="md-layout-item">Connection Type</div>
                                <div class="md-layout-item">
                                    <div v-if="editConnection===false" class="col-sm-6">
                                        {{meter.connection.name}}
                                        <i class="fa fa-pencil" @click="editConnection = true"></i>
                                    </div>
                                    <div class="md-layout" v-else>
                                        <div class="md-layout-item">

                                            <md-field>
                                                <label for="connectiontype">Connection Type</label>
                                                <md-select name="connectiontype"
                                                           v-model="newConnectionType">
                                                    <md-option v-for="connectionType in connectionTypes.list"
                                                               :key="connectionType.id" :value="connectionType.id">
                                                        {{connectionType.name}}
                                                    </md-option>
                                                </md-select>
                                            </md-field>
                                        </div>
                                        <md-button class="md-icon-button" @click="updateConnection(newConnectionType)">
                                            <md-icon class="md-primary">save</md-icon>
                                        </md-button>
                                        <md-button class="md-icon-button" @click="editConnection=false">
                                            <md-icon class="md-accent">cancel</md-icon>
                                        </md-button>
                                    </div>
                                </div>
                            </div>
                            <div class="md-layout">
                                <div class="md-layout-item">Last Payment</div>
                                <div class="md-layout-item">3 days ago</div>
                            </div>
                        </md-card-content>
                    </md-card>
                </widget>
            </div>

            <div class="md-layout-item md-size-65 md-small-size-100">
                <widget
                    v-if="transactions!==null"
                    :title="'Meter Transactions'"
                    class="col-sm-7"
                    :id="'meter-transactions'"
                    :paginator="transactions.paginator"
                    :subscriber="subscriber"
                    color="green"
                >
                    <md-card>
                        <md-card-content>

                                    <md-table>
                                        <md-table-row>
                                            <md-table-head v-for="(item, index) in headers" :key="index">{{item}}
                                            </md-table-head>
                                        </md-table-row>
                                        <md-table-row v-for="token in transactions.tokens" :key="token.id">
                                            <md-table-cell v-text="token.transaction.id"></md-table-cell>
                                            <md-table-cell
                                                v-text="token.transaction.original_transaction_type == 'vodacom_transaction' ? 'Vodacom' :'Airtel'"
                                            ></md-table-cell>
                                            <md-table-cell v-text="token.transaction.amount"></md-table-cell>
                                            <md-table-cell
                                                v-if="token.paid_for_type === 'token'"
                                            >Token {{token.paid_for.token}}
                                            </md-table-cell>
                                            <md-table-cell v-else>Access Rate</md-table-cell>
                                            <md-table-cell
                                                v-if="token.paid_for_type === 'token'"
                                                v-text="token.paid_for.energy + 'kWh'"
                                            ></md-table-cell>
                                            <md-table-cell v-else>-</md-table-cell>
                                            <md-table-cell v-text="token.created_at"></md-table-cell>
                                        </md-table-row>
                                    </md-table>

                        </md-card-content>
                    </md-card>
                </widget>
            </div>
        </div>

        <div style="margin-top: 1rem;"></div>

        <widget v-if="meter.meterType.online !== 0" :title="'Meter Readings'" class="col-sm-12" :id="'meter-readings'">
            <div role="menu" slot="tabbar">
                <button
                    class="md-button dropdown-toggle btn-xs"
                    aria-expanded="false"
                    id="datepicker-button-trigger"
                    style="color:white"
                >
                    Period
                    <md-icon>calendar_today</md-icon>
                </button>
            </div>

            <md-card>
                <md-card-content>
                    <div v-if="chartData.length>0">
                        <GChart type="LineChart" :data="chartData" :options="chartOptions"></GChart>
                    </div>

                    <div v-if="chartData.length===0 && loading === false" class="text-center">
                        <h2>NO DATA for {{dates.dateOne}} - {{dates.dateTwo}}</h2>
                    </div>

                </md-card-content>
            </md-card>
        </widget>

        <airbnb-style-datepicker
            :trigger-element-id="'datepicker-button-trigger'"
            :mode="'range'"
            :date-one="dates.dateOne"
            :date-two="dates.dateTwo"
            :min-date="'2018-01-01'"
            :endDate="dates.today"
            :fullscreen-mobile="true"
            :months-to-show="2"
            :offset-y="500"
            v-on:date-one-selected="function(val) { dates.dateOne = val }"
            v-on:date-two-selected="function(val) { dates.dateTwo = val }"
            @apply="getConsumptions"
        ></airbnb-style-datepicker>


    </div>
</template>

<script>
import Widget from '../../shared/widget'
import { Transactions } from '../../classes/meter/transactions'
import { EventBus } from '../../shared/eventbus'
import { Consumptions } from '../../classes/meter/Consumptions'
import { Meter } from '../../classes/meter/meter'
import { resources } from '../../resources'
import { ConnectionTypes } from '../../classes/connection/ConnectionTypes'
import { currency } from '../../mixins/currency'
import moment from 'moment'


export default {
    name: 'MeterDetail',
    components: { Widget },
    mixins: [currency],
    created () {
        EventBus.$on('pageLoaded', this.reloadList)
        //initialize dates
        let baseDate = moment()
        this.dates.today = baseDate.format('YYYY-MM-DD')
        this.dates.dateTwo = baseDate.add(-1, 'days').format('YYYY-MM-DD')
        this.dates.dateOne = baseDate.add(-1, 'weeks').format('YYYY-MM-DD')
    },
    mounted () {
        EventBus.$emit('bread', this.bcd)
        this.transactions = new Transactions(this.$route.params.id)
        this.consumptions = new Consumptions(this.$route.params.id)
        this.meter = new Meter(this.$route.params.id)
        this.meter.detail().then(() => {
            this.meter.revenue()
        })

        this.getConsumptions()
        this.getTariffs()
        this.connectionTypes.getSubConnectionTypes()
    },
    data () {
        return {
            subscriber: 'meter.transactions',
            transactions: null,
            consumptions: null,
            meter: null,
            chartData: [],
            hideSearch: true,
            showModal: false,
            searchTerm: '',
            customerSearchTerm: '',
            newOwner: null,
            searchNames: [],
            editTariff: false,
            newTariff: null,
            newConnectionType: null,
            tariffs: [],
            showOwnerEdit:false,
            //meter connection controlller
            connectionTypes: new ConnectionTypes(),
            //re-assing connection flag
            editConnection: false,

            bcd: {
                Home: {
                    href: '/'
                },
                Meters: {
                    href: '/meters'
                },
                Detail: {
                    href: null
                }
            },
            chartOptions: {
                chart: {
                    title: 'Company Performance',
                    subtitle: 'Sales, Expenses, and Profit: 2014-2017'
                },
                height: 400,
                colors: ['#1b9e77', '#d95f02', '#7570b3']
            },
            dates: {
                dateTwo: null,
                dateOne: null,
                today: null,
                difference: 0
            },
            loading: true,
            headers: ['ID', 'Provider', 'Amount', 'Paid for', 'In Return', 'Date'],
            tableName: 'Meter Transactions'
        }
    },
    watch: {
        //   searchTerm: debounce(function(e) {
        //     if (this.searchTerm.length < 3) {
        //       this.hideSearch = true;
        //       return;
        //     }
        //     this.searchFor(this.searchTerm);
        //   }, 250)
    },

    methods: {
        navigateOwner (ownerId) {
            this.$router.push('/people/' + ownerId)
        },

        getTariffs () {
            axios.get(resources.tariff.list).then(response => {
                this.tariffs = response.data.data
            })
        },
        reloadList (subscriber, data) {
            if (subscriber !== this.subscriber){
                return
            }
            this.transactions.updateList(data)
            EventBus.$emit('widgetContentLoaded',this.subscriber,this.transactions.tokens.length)
        },
        getConsumptions () {
            this.loading = true

            this.chartData = []
            this.consumptions
                .getData(this.dates.dateOne, this.dates.dateTwo)
                .then(() => {
                    this.loading = false
                    if (this.consumptions.data.length === 0) {
                        this.chartData = []
                        return
                    }
                    this.chartData.push(['Date', 'Consumption', 'Credit'])
                    this.chartData = this.chartData.concat(this.consumptions.data)
                })
        },
        selectCustomer (c) {
            this.customerSearchTerm = c.name
            this.newOwner = c
        },
        searchFor (term) {
            if (term != undefined && term.length > 2) {
                return axios
                    .get(resources.person.search, { params: { term: term, paginate: 0 } })
                    .then(response => {
                        this.searchNames = []
                        for (let i in response.data.data) {
                            let person = response.data.data[i]
                            this.searchNames.push({
                                id: person.id,
                                name: person.name + ' ' + person.surname
                            })
                        }
                        this.hideSearch = false

                        return this.searchNames.map(x => ({
                            id: x.id,
                            name: x.name,
                            toLowerCase: () => x.name.toLowerCase(),
                            toString: () => x.name
                        }))
                        // return this.searchNames;
                    })
            } else {
                this.hideSearch = true
            }
        },
        resetOwner () {
            this.searchTerm = ''
            this.hideSearch = true
            this.newOwner = null
            this.searchNames = []
        },
        setOwner (owner) {
            this.newOwner = owner
        },
        closeOwnerEdit () {
            this.resetOwner()
            this.showOwnerEdit = false
        },
        updateTariff (tariffId) {
            this.updateParameter(this.meter.id, { tariffId: tariffId })
        },
        updateConnection (connectionId) {
            let data = { connectionId: connectionId }
            this.updateParameter(this.meter.id, data)
        },

        updateParameter (meterId, params) {
            axios
                .put(
                    resources.meterparameters.update + this.meter.id + '/parameters',
                    params
                )
                .then(response => {
                    if (response.status === 200) {
                        if ('tariff' in response.data.data) {
                            this.meter.tariff = response.data.data.tariff
                        } else if ('connection_type' in response.data.data) {
                            this.meter.connection = response.data.data.connection_type
                        }
                    } else {
                        this.$swal({
                            type: 'error',
                            title: 'Unexpected error',
                            text: 'Please get in touch with your system admin.'
                        })
                    }
                    this.editTariff = false
                    this.editConnection = false
                })
        },
        saveNewOwner () {
            if (this.newOwner === null) {
                this.$swal({
                    type: 'error',
                    title: 'New Owner is required!',
                    text: 'Please select a new owner.'
                })
                return
            }
            this.$swal({
                type: 'success',
                title: 'Re-Assigning Meter?',
                text:
                        'Are you sure to assign the meter from ' +
                        this.newOwner.name +
                        ' ' +
                        this.newOwner.surname +
                        ' to ' +
                        this.meter.owner.name +
                        ' ' +
                        this.meter.owner.surname,
                showCancelButton: true,
                confirmButtonText: 'I\'m sure',
                cancelButtonText: 'Cancel'
            }).then(result => {
                console.log(result)
                if (result.value) {
                    axios
                        .put(
                            resources.meterparameters.update + this.meter.id + '/parameters',
                            {
                                personId: this.newOwner.id
                            }
                        )
                        .then(response => {
                            if (response.status === 200) {
                                this.meter.owner = response.data.data.owner
                                this.showOwnerEdit = false
                                this.resetOwner()
                            } else {
                                this.$swal({
                                    type: 'error',
                                    title: 'Unexpected error',
                                    text: 'Please get in touch with your system admin.'
                                })
                            }
                        })
                }
            })
        }
    }
}
</script>

<style lang="scss">
    .md-menu-content {
        z-index: 11 !important;
    }

    .asd__inner-wrapper {
        margin-left: 0 !important;
    }

    .asd__wrapper--datepicker-open {
        right: 20px !important;
    }

    .mt-15 {
        margin-top: 15px;
    }

    .list-container {
        max-height: 200px;
        overflow: hidden;
        overflow-y: scroll;
    }

    .list-item {
        padding: 20px;
        margin: 0.5rem 0;
        cursor: pointer;
        border-bottom: 1px dotted;
    }

    .list-item-info {
        padding: 5px;
        color: #514e50;
        font-size: 0.8rem;
    }

    .list-item:hover {
        color: white;
        background-color: rgba(15, 15, 15, 0.8);
    }

    .md-autocomplete-item {
        z-index: 110;
    }
</style>
