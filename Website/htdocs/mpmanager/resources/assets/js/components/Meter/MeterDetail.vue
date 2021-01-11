<template>
    <div class="page-container" id="widget-grid">
        <div class="md-layout md-gutter">
            <div class="md-layout-item md-size-35 md-small-size-100">
                <widget :title="$tc('words.overview')" class="col-sm-5" :id="'meter-overview'">
                    <md-card>
                        <md-card-header>
                            <div class="md-title">{{ $tc('words.basic') }}</div>
                        </md-card-header>

                        <md-card-content v-if="meter!==null && meter.loaded===true">
                            <div class="md-layout">
                                <div class="md-layout-item">{{ $tc('words.register',2) }}</div>
                                <div class="md-layout-item">{{meter.registered}}</div>
                            </div>
                            <div class="md-layout">
                                <div class="md-layout-item">{{ $tc('words.owner') }}</div>
                                <div class="md-layout">
                                    <div class="md-layout-item">
                                        <a href="javascript:void(0)" @click="navigateOwner(meter.owner.id)">
                                            {{meter.owner.name}}
                                            {{meter.owner.surname}}
                                        </a>
                                        <span style="cursor:pointer" @click="showOwnerEdit = true"><md-icon>edit</md-icon></span>
                                    </div>
                                    <div class="md-layout-item" v-if="showOwnerEdit">
                                        <div class="md-layout-item">{{$tc('phrases.newOwner')}}</div>

                                        <md-autocomplete
                                                v-model="customerSearchTerm"
                                                :md-options="searchNames"
                                                @md-changed="searchFor"
                                                @md-opened="searchFor"
                                                @md-selected="selectCustomer"
                                        >
                                            <label>{{ $tc('words.customer') }}</label>
                                            <template slot="md-autocomplete-item" slot-scope="{ item }">{{ item.name
                                                }}
                                            </template>
                                        </md-autocomplete>
                                        <md-button v-if="showOwnerEdit" class="md-icon-button md-primary"
                                                   @click="saveNewOwner()">
                                            <md-icon>save</md-icon>
                                        </md-button>
                                        <md-button class="md-accent md-icon-button" @click="closeOwnerEdit()">
                                            <md-icon>close</md-icon>
                                        </md-button>
                                    </div>
                                </div>

                            </div>
                            <div class="md-layout">
                                <div class="md-layout-item">{{$tc('phrases.totalRevenue')}}</div>
                                <div class="md-layout-item">
                                    <div v-if="meter.totalRevenue">{{ readable(meter.totalRevenue)}} TZS</div>
                                    <div v-else>{{$tc('phrases.noData')}}</div>
                                </div>
                            </div>
                            <div class="md-layout">
                                <div class="md-layout-item">{{$tc('phrases.lastPayment')}}</div>
                                <div class="md-layout-item">{{$tc('phrases.3daysAgo')}}</div>
                            </div>
                        </md-card-content>
                    </md-card>

                    <md-card>
                        <md-card-header>
                            <div class="md-title">{{$tc('phrases.meterDetail',2)}}</div>
                        </md-card-header>

                        <md-card-content v-if="meter!==null && meter.loaded===true">
                            <div class="md-layout">
                                <div class="md-layout-item">{{ $tc('words.manufacturer') }}</div>
                                <div
                                    class="md-layout-item"
                                >{{meter.manufacturer.name}} ( {{meter.manufacturer.website}})
                                </div>
                            </div>

                            <div class="md-layout">
                                <div class="md-layout-item">{{$tc('phrases.serialNumber')}}</div>
                                <div class="md-layout-item">{{meter.serialNumber}}</div>
                            </div>
                            <div class="md-layout">
                                <div class="md-layout-item">{{ $tc('words.tariff') }}</div>
                                <div class="md-layout-item">
                                    <div v-if="editTariff===false" class="col-sm-6">
                                        {{meter.tariff.name}}
                                       <span style="cursor: pointer" @click="editTariff = true"><md-icon>edit</md-icon></span>
                                    </div>
                                    <div class="md-layout" v-else>
                                        <div class="md-layout-item">
                                            <md-field>
                                                <label for="tariff">{{ $tc('words.tariff') }}</label>
                                                <md-select name="tariff" v-model="newTariff">
                                                    <md-option v-for="tariff in tariffService.list"
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
                                <div class="md-layout-item">{{$tc('phrases.connectionType')}}</div>
                                <div class="md-layout-item">
                                    <div v-if="editConnection===false" class="col-sm-6">
                                        {{meter.connection.name}}
                                        <span style="cursor: pointer" @click="editConnection = true"><md-icon>edit</md-icon></span>
                                    </div>
                                    <div class="md-layout" v-else>
                                        <div class="md-layout-item">

                                            <md-field>
                                                <label for="connectiontype">{{$tc('phrases.connectionType')}}</label>
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
                                <div class="md-layout-item">{{$tc('phrases.lastPayment')}}</div>
                                <div class="md-layout-item">{{$tc('phrases.3daysAgo')}}</div>
                            </div>
                        </md-card-content>
                    </md-card>
                </widget>
            </div>

            <div class="md-layout-item md-size-65 md-small-size-100">
                <widget
                        v-if="transactions!==null"
                        :title="$tc('phrases.meterTransaction')"
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

                                            v-text="token.transaction.original_transaction_type"
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

        <widget :title="$tc('phrases.meterReadings')" class="col-sm-12"
                :id="'meter-readings'">
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
                        <h2>{{ $tc('phrases.noData') }} {{dates.dateOne}} - {{dates.dateTwo}}</h2>
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
import { ConnectionTypes } from '../../classes/connection/ConnectionTypes'
import { currency } from '../../mixins/currency'
import moment from 'moment'
import { TariffService } from '../../services/TariffService'
import { PersonService } from '../../services/PersonService'
import { MeterParameterService } from '../../services/MeterParameterService'

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
        this.meter = new Meter(this.$route.params.id)
    },
    mounted () {
        EventBus.$emit('bread', this.bcd)
        this.transactions = new Transactions(this.$route.params.id)
        this.consumptions = new Consumptions(this.$route.params.id)

        this.meter.detail().then(() => {
            this.meter.revenue()
        })
        this.getConsumptions()
        this.getTariffs()
        this.connectionTypes.getSubConnectionTypes()
    },
    data () {
        return {
            meterParameterService: new MeterParameterService(),
            personService: new PersonService(),
            tariffService: new TariffService(),
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
            showOwnerEdit: false,
            //meter connection controlller
            connectionTypes: new ConnectionTypes(),
            //re-assing connection flag
            editConnection: false,

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
            headers: [this.$tc('words.id'), this.$tc('words.provider'), this.$tc('words.amount'),
                this.$tc('phrases.paidFor'), this.$tc('phrases.inReturn'), this.$tc('words.date')],
            tableName: 'Meter Transactions'
        }
    },
    methods: {
        navigateOwner (ownerId) {
            this.$router.push('/people/' + ownerId)
        },

        async getTariffs () {
            try {
                await this.tariffService.getTariffs()
            } catch (e) {
                this.alertNotify('error', e.message)
            }

        },
        reloadList (subscriber, data) {
            if (subscriber !== this.subscriber) {
                return
            }
            this.transactions.updateList(data)
            EventBus.$emit('widgetContentLoaded', this.subscriber, this.transactions.tokens.length)
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
                    this.chartData.push([this.$tc('words.date'), this.$tc('words.consumption'), this.$tc('words.credit')])
                    this.chartData = this.chartData.concat(this.consumptions.data)
                })
        },
        selectCustomer (c) {
            this.customerSearchTerm = c.name
            this.newOwner = c
        },
        searchFor (term) {
            if (term != undefined && term.length > 2) {
                this.personService.searchPerson({ params: { term: term, paginate: 0 } }).then(response => {
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
            this.meterParameterService.update(meterId, params)
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
                            title: this.$tc('phrases.meterDetailNotify', 0),
                            text: this.$tc('phrases.meterDetailNotify', 2)
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
                    title: this.$tc('phrases.meterDetailNotify', 3),
                    text: this.$tc('phrases.meterDetailNotify2', 0)
                })
                return
            }
            this.$swal({
                type: 'success',
                title: this.$tc('phrases.meterDetailNotify2', 1),
                text: this.$tc('phrases.meterDetailNotify2', 3,
                    { newName: this.newOwner.name, name: this.meter.owner.name + ' ' + this.meter.owner.surname }),
                showCancelButton: true,
                confirmButtonText: this.$tc('words.confirm'),
                cancelButtonText: this.$tc('words.cancel'),
            }).then(result => {
                console.log(result)
                if (result.value) {
                    this.meterParameterService.update(this.meter.id, { personId: this.newOwner.id })
                        .then(response => {
                            if (response.status === 200) {
                                this.meter.owner = response.data.data.owner
                                this.showOwnerEdit = false
                                this.resetOwner()
                            } else {
                                this.$swal({
                                    type: 'error',
                                    title: this.$tc('phrases.meterDetailNotify'),
                                    text: this.$tc('phrases.meterDetailNotify', 2)
                                })
                            }
                        })
                }
            })
        }
    },
    computed: {
        transactionType: () =>
        {
            return this.token.transaction.original_transaction_type
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
