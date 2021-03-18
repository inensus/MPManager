<template>
    <div style="margin: 2vh;">
        <md-card>
            <md-card-header>
              <div class="md-layout md-gutter">
                <div class="md-layout-item md-size-70">
                  {{ $tc('words.filter') }}
                </div>
                <div class="md-layout-item md-size-30">
                  <md-button class="md-accent md-icon-button close-button" @click="closeFilter">
                    <md-icon>cancel</md-icon>
                  </md-button>
                </div>
              </div>

            </md-card-header>
            <md-card-content>
                <div class="md-layout">
                    <div
                        class="md-layout-item  md-xlarge-size-100  md-large-size-100 md-medium-size-100  md-small-size-100 md-xsmall-size-100">
                        <md-field>
                          <label for="">{{ $tc('words.serialNumber') }}</label>
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
                          <label for="">{{ $tc('words.tariff') }}</label>
                            <md-select v-model="filter.tariff" name="tariff" id="tariff" @md-selected="setTariff">

                                <md-option v-for="tariff in tariffs" :value="tariff.id" :key="tariff.id">{{tariff.name}}
                                </md-option>
                            </md-select>
                        </md-field>
                    </div>
                    <div
                        class="md-layout-item  md-xlarge-size-100  md-large-size-100 md-medium-size-100  md-small-size-100 md-xsmall-size-100">
                        <md-field>
                          <label for="">{{ $tc('words.provider') }}</label>
                            <md-select name="provider" id="provider"
                                       v-model="filter.provider"
                            >
                                <md-option v-for="(p,i) in transactionProviderService.list" :key="i" :value="p.value">{{p.name}}
                                </md-option>

                            </md-select>


                        </md-field>
                    </div>
                    <div
                        class="md-layout-item  md-xlarge-size-100  md-large-size-100 md-medium-size-100  md-small-size-100 md-xsmall-size-100">
                        <md-field>
                          <label for="">{{ $tc('words.status') }}</label>
                            <md-select
                                v-model="filter.status"
                                name="transaction"
                                id="transaction"
                            >
                                <md-option value="all">{{ $tc('phrases.allTransactions') }}</md-option>
                                <md-option value="1">{{ $tc('phrases.onlyApproved') }}</md-option>
                                <md-option value="-1">{{ $tc('phrases.onlyRejected') }}</md-option>
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
                            <label>{{ $tc('phrases.fromDate') }}</label>
                        </md-datepicker>
                    </div>

                    <div
                        class="md-layout-item  md-xlarge-size-100  md-large-size-100 md-medium-size-100  md-small-size-100 md-xsmall-size-100">
                        <md-datepicker
                            v-model="filter.to"
                            md-immediately
                            :md-model-type="String">
                            <label>{{ $tc('phrases.toDate') }}</label>
                        </md-datepicker>
                    </div>
                </div>
            </md-card-content>
            <md-card-actions>
                <md-button class="md-raised md-primary " v-if="!loading" @click="submitFilter">{{ $tc('words.search') }}
                </md-button>
                <md-button class="md-raised md-accent" @click="clearFilterForm()">clear</md-button>

            </md-card-actions>
            <md-progress-bar md-mode="indeterminate" v-if="loading"/>
        </md-card>
    </div>
</template>

<script>
import { TransactionService } from '../../services/TransactionService'
import { TariffService } from '../../services/TariffService'
import { EventBus } from '../../shared/eventbus'
import { TransactionProviderService } from '../../services/TransactionProviderService'

export default {
    name: 'FilterTransaction',
    mounted () {
        this.getTariffs()
        this.getSearch()
        this.getTransactionProviders()
        EventBus.$on('dataLoaded', this.dataLoaded)
        this.getFilterParams()
    },

    data () {
        return {
            transactionService: new TransactionService(),
            transactionProviderService: new TransactionProviderService(),
            tariffService: new TariffService(),
            tariffs: [],
            loading: false,
            filter: {
                status: 'all',
                serial_number: null,
                tariff: 'all',
                provider: '-1',
                from: null,
                to: null,
            }
        }
    },

    methods: {
        clearFilterForm(){
            this.loading = true
            this.filter = {
                status: 'all',
                serial_number: null,
                tariff: 'all',
                provider: '-1',
                from: null,
                to: null,
            }
            this.loading = false
        },
        getFilterParams(){
            let params = this.$route.query
            for(let param of Object.keys(params)){
                for(let filter of Object.keys(this.filter)){
                    if(param === filter){
                        this.filter[filter] = params[param]
                    }
                }
            }

        },
        dataLoaded () {
            this.loading = false
            this.closeFilter()
        },
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
        async getTransactionProviders () {
            await this.transactionProviderService.getTransactionProviders()
            this.selectedProvider = this.transactionProviderService.list
                .filter(x => x.value === '-1')
                .map(x => x.value)[0]

        },
        setTariff (tariff) {
            this.filter.tariff = tariff
        },
        closeFilter(){
            EventBus.$emit('transactionFilterClosed')
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
                this.filter.from = this.filter.from.toString() + ' 00:00:00'
            }
            if (this.filter.to !== null) {
                this.filter.to = this.filter.to.toString() + ' 23:59:59'

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

.close-button{
  right: 0!important;
  float: right;
}

</style>
