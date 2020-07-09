<template>
    <div style="margin: 1vh;">
        <h2 class="filter-header">Filter Transactions</h2>
        <div class="md-layout">
            <div
                class="md-layout-item  md-xlarge-size-100  md-large-size-100 md-medium-size-100  md-small-size-100 md-xsmall-size-100">
                <md-field>
                    <md-input
                        type="text"
                        placeholder="Meter Serial Number"
                        v-model="filter.serial_number"
                        v-on:keyup.enter="submitFilter"
                    ></md-input>
                </md-field>
            </div>
            <div
                class="md-layout-item  md-xlarge-size-100  md-large-size-100 md-medium-size-100  md-small-size-100 md-xsmall-size-100">
                <md-field>
                    <md-select v-model="tarrif_" name="tariff" id="tariff" @md-selected="setTariff">

                        <md-option v-for="tariff in tariffs" :value="tariff.id" :key="tariff.id">{{tariff.name}}
                        </md-option>
                    </md-select>
                </md-field>
            </div>
            <div
                class="md-layout-item  md-xlarge-size-100  md-large-size-100 md-medium-size-100  md-small-size-100 md-xsmall-size-100">
                <md-field>
                    <md-select v-model="provider_" name="provider" id="provider" @md-selected="setProvider">
                        <md-option value="All Network Providers">All Network Providers</md-option>
                        <md-option value="Airtel">Airtel</md-option>
                        <md-option value="Vodacom">Vodacom</md-option>
                    </md-select>
                </md-field>
            </div>
            <div
                class="md-layout-item  md-xlarge-size-100  md-large-size-100 md-medium-size-100  md-small-size-100 md-xsmall-size-100">
                <md-field>
                    <md-select
                        v-model="transaction_"
                        name="transaction"
                        id="transaction"
                        @md-selected="seTransaction"
                    >
                        <md-option value="All Transactions">All Transactions</md-option>
                        <md-option value="Only Approved">Only Approved</md-option>
                        <md-option value="Only Rejected">Only Rejected</md-option>
                    </md-select>
                </md-field>
            </div>

            <div
                class="md-layout-item  md-xlarge-size-100  md-large-size-100 md-medium-size-100  md-small-size-100 md-xsmall-size-100">
                <md-datepicker
                    v-model="filter.from"
                    md-immediately
                    :md-model-type="String"
                >
                    <label>From date</label>
                </md-datepicker>
            </div>

            <div
                class="md-layout-item  md-xlarge-size-100  md-large-size-100 md-medium-size-100  md-small-size-100 md-xsmall-size-100">
                <md-datepicker
                    v-model="filter.to"
                    md-immediately
                    :md-model-type="String">
                    <label>To date</label>
                </md-datepicker>
            </div>
        </div>

        <div class="md-layout-item">
            <md-button class="md-raised md-primary " v-if="!loading" @click="submitFilter">Search</md-button>
            <md-progress-bar md-mode="indeterminate" v-if="loading"/>
        </div>
    </div>
</template>

<script>
    import Datepicker from 'vuejs-datepicker'
    import { TransactionService } from '../../services/TransactionService'
    import { TariffService } from '../../services/TariffService'
    import { EventBus } from '../../shared/eventbus'

    export default {
        name: 'FilterTransaction',
        components: { Datepicker },
        mounted () {
            this.getTariffs()
            this.getSearch()
            EventBus.$on('dataLoaded', e => {
                this.loading = false
            })
        },

        data () {
            return {
                transactionService: new TransactionService(),
                tariffService: new TariffService(),
                tariffs: [],
                tarrif_: '',
                loading: false,
                provider_: 'All Network Providers',
                transaction_: 'All Transactions',
                filter: {
                    status: null,
                    serial_number: null,
                    tariff: null,
                    provider: null,
                    from: null,
                    to: null
                }
            }
        },

        methods: {
            async getTariffs () {
                let tariffs = await this.tariffService.getTariffs()
                tariffs.forEach((e) => {
                    let tariff = {
                        id: e.id,
                        name: e.name
                    }
                    this.tariffs.push(tariff)
                })
                this.tariffs.unshift({ id: 'all', name: 'All Tariffs' })
                this.tarrif_ = this.tariffs[0].id

            },
            setTariff (tariff) {
                this.filter.tariff = tariff
            },
            setProvider (provider) {
                switch (provider) {
                    case 'All Network Providers':
                        this.filter.provider = '-1'
                        break
                    case 'Airtel':
                        this.filter.provider = 'airtel_transaction'
                        break
                    case 'Vodacom':
                        this.filter.provider = 'vodacom_transaction'
                        break
                    default:
                        break
                }
            },
            seTransaction (transaction) {
                switch (transaction) {
                    case 'All Transactions':
                        this.filter.status = 'all'
                        break
                    case 'Only Approved':
                        this.filter.status = '1'
                        break
                    case 'Only Rejected':
                        this.filter.status = '-1'
                        break

                    default:
                        break
                }
            },
            submitFilter () {
                 this.loading = true
                if (this.filter.serial_number === '') {
                    this.filter.serial_number = null
                }
                if (this.filter.provider === -1 || this.filter.provider === '-1') {
                    this.filter.provider = null
                }
                if (this.filter.tariff === 'all') {
                    this.filter.tariff = null
                }
                if (this.filter.status === 'all') {
                    this.filter.status = null
                }
                if (this.filter.from !== null) {
                    this.filter.from += ' 00:00:00'
                }
                if (this.filter.to !== null) {
                    this.filter.to += ' 23:59:59'
                }
                this.$emit('searchSubmit', this.filter)
            },

            getSearch () {
                let search = this.$store.getters.search

                if (Object.keys(search).length) {
                    if ('serial_number' in search) {
                        this.filter['serial_number'] = search['serial_number']
                    }
                    if ('from' in search) {
                        this.filter['from'] = search['from']
                    }
                    if ('to' in search) {
                        this.filter['to'] = search['to']
                    }
                }
            }
        }
    }
</script>

<style scoped>
    .filter-header {
        text-align: center;
        font-size: large;
        text-decoration: underline;
        font-weight: 500
    }
</style>
