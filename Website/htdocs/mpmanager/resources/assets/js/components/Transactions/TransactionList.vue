<template>
    <div>
        <div class="md-layout md-gutter">
            <div
                    class="md-layout-item  md-xlarge-size-100  md-large-size-100 md-medium-size-100  md-small-size-100 md-xsmall-size-100">
                <div style="float:right;padding-bottom: 1rem;">
                    <md-field>
                        <label for="period">{{ $tc('words.period') }}</label>
                        <md-select v-model="period" name="period" id="period" @md-selected="getPeriod">
                            <md-option value="Yesterday">{{ $tc('words.yesterday') }}</md-option>
                            <md-option value="Same day last week">{{$tc('phrases.sameDayLastWeek')}}</md-option>
                            <md-option value="Past 7 days">{{ $tc('phrases.lastXDays',1,{x: 7}) }}</md-option>
                            <md-option value="Past 30 days">{{ $tc('phrases.lastXDays',1,{x: 30}) }}</md-option>
                        </md-select>
                    </md-field>
                </div>
            </div>

            <div v-if="analyticsData"
                 class="md-layout-item md-xlarge-size-25 md-large-size-25 md-medium-size-50 md-small-size-100 md-xsmall-size-100 ">
                <box
                        :center-text="true"
                        :color="[ '#26c6da','#00acc1']"
                        :header-text="$tc('phrases.incomingTransactions')"
                        :header-text-color="'#dddddd'"
                        :sub-text="analyticsData.current.total + '/' + analyticsData.past.total"
                        :sub-text-color="'#e3e3e3'"
                        box-icon="add"
                        :box-icon-color="'#578839'"
                        :additional-text="analyticsData.analytics.totalPercentage.percentage + '%' + analyticsPeriods[analyticsPeriod]"
                />
            </div>
            <div v-if="analyticsData"
                 class="md-layout-item md-xlarge-size-25 md-large-size-25 md-medium-size-50 md-small-size-100 md-xsmall-size-100 ">
                <box
                        :center-text="true"
                        :color="[ '#6eaa44','#578839']"
                        :header-text="$tc('words.confirm',2)"
                        :header-text-color="'#dddddd'"
                        :sub-text="analyticsData.current.confirmed + '/' +  analyticsData.past.confirmed"
                        :sub-text-color="'#e3e3e3'"
                        box-icon="check"
                        :box-icon-color="'#578839'"
                        :additional-text="analyticsData.analytics.confirmationPercentage.percentage + '%' + analyticsPeriods[analyticsPeriod]"
                />
            </div>
            <div v-if="analyticsData"
                 class="md-layout-item md-xlarge-size-25 md-large-size-25 md-medium-size-50 md-small-size-100 md-xsmall-size-100 ">
                <box
                        :center-text="true"
                        :color="[ '#ef5350','#e53935']"
                        :header-text="$tc('words.cancel',2)"
                        :header-text-color="'#dddddd'"
                        :sub-text="analyticsData.current.cancelled + '/' +  analyticsData.past.cancelled"
                        :sub-text-color="'#e3e3e3'"
                        box-icon="cancel"
                        :box-icon-color="'#578839'"
                        :additional-text="analyticsData.analytics.cancelationPercentage.percentage + '%' + analyticsPeriods[analyticsPeriod]"
                />
            </div>
            <div v-if="analyticsData"
                 class="md-layout-item md-xlarge-size-25 md-large-size-25 md-medium-size-50 md-small-size-100 md-xsmall-size-100 ">
                <box
                        :center-text="true"
                        :color="[ '#ffa726','#fb8c00']"
                        :header-text="$tc('words.revenue')"
                        :header-text-color="'#dddddd'"
                        :sub-text="readable(analyticsData.current.amount) +                                    appConfig.currency"
                        :sub-text-color="'#e3e3e3'"
                        box-icon="attach_money"
                        :box-icon-color="'#578839'"
                        :additional-text="analyticsData.analytics.amountPercentage.percentage + '%' + analyticsPeriods[analyticsPeriod]"
                />
            </div>

            <div
                    class="md-layout-item  md-xlarge-size-100  md-large-size-100 md-medium-size-100 md-small-size-100  md-xsmall-size-100"
                    v-if="analyticsData === null && loading ===false">
                <h5>{{$tc('phrases.transactionNotify')}}</h5>
            </div>
        </div>


        <widget
                :id="'transaction-list'"
                :title="$tc('words.transaction',2)"
                :paginator="transactionService.paginator"
                :search="false"
                :subscriber="subscriber"
                :route_name="'/transactions'"
                :show_per_page="true"
        >
            <div class="md-layout md-gutter">
                <div
                        class="md-layout-item  md-xlarge-size-20  md-large-size-20 md-medium-size-20  md-small-size-100 md-xsmall-size-100">
                    <filter-transaction @searchSubmit="filterTransaction"></filter-transaction>
                </div>
                <div class="md-layout-item  md-xlarge-size-80  md-large-size-80 md-medium-size-80  md-small-size-100 md-xsmall-size-100">
                    <md-table style="width:100%;" md-card>
                        <md-table-row>
                            <md-table-head>
                                {{ $tc('words.status') }}
                            </md-table-head>
                            <md-table-head>
                                <md-icon>person</md-icon>
                                {{ $tc('words.service') }}
                            </md-table-head>
                            <md-table-head>
                                <md-icon>phone</md-icon>
                                {{ $tc('words.sender') }}
                            </md-table-head>
                            <md-table-head>
                                <md-icon>money</md-icon>
                                {{ $tc('words.amount') }}
                            </md-table-head>
                            <md-table-head> {{ $tc('words.type') }}</md-table-head>
                            <md-table-head> {{ $tc('words.message') }}</md-table-head>
                            <md-table-head>
                                <md-icon>calendar_today</md-icon>
                                {{ $tc('phrases.sentDate') }}
                            </md-table-head>
                            <md-table-head>
                                <md-icon>calendar_view_day</md-icon>
                                {{ $tc('phrases.processTime') }}
                            </md-table-head>
                        </md-table-row>

                        <md-table-row
                                :class="transaction.status===1 ? 'active':'danger'"
                                v-for="transaction in transactionService.list"
                                :key="transaction.id"
                                style="cursor:pointer"
                                @click="transactionDetail(transaction.id)"
                        >
                            <md-table-cell>
                                <md-icon v-if="transaction.status===1" style="color:green" md-toolt>check_circle_outline
                                    <md-tooltip md-direction="right">{{ $tc('words.confirm',2) }}</md-tooltip>
                                </md-icon>
                                <md-icon v-if="transaction.status===0" style="color:goldenrod">contact_support
                                    <md-tooltip md-direction="right">{{ $tc('words.process',3) }}</md-tooltip>
                                </md-icon>
                                <md-icon v-if="transaction.status===-1" style="color:red">cancel
                                    <md-tooltip md-direction="right">{{ $tc('words.reject',2) }}</md-tooltip>
                                </md-icon>
                            </md-table-cell>

                            <md-table-cell style="text-align: center !important;">

                                <img v-if="transaction.service==='vodacom_transaction'" class="logo" alt="logo"
                                     :src="vodacomLogo" style="max-height: 18px;"/>
                                <img v-if="transaction.service==='airtel_transaction'" class="logo" alt="logo"
                                     :src="airtelLogo" style="max-height: 18px;"/>
                                <img v-if="transaction.service==='third_party_transaction'" class="logo" alt="logo"
                                     :src="thirdPartyLogo" style="max-height: 18px;"/>
                                <img v-if="transaction.service==='agent_transaction'"
                                     src="https://image.flaticon.com/icons/svg/99/99395.svg"
                                     style="max-height:18px;"
                                />


                            </md-table-cell>
                            <md-table-cell>{{transaction.sender}}</md-table-cell>
                            <md-table-cell>{{readable(transaction.amount) + appConfig.currency}}
                            </md-table-cell>
                            <md-table-cell>{{transaction.type}}</md-table-cell>
                            <md-table-cell>{{transaction.message}}</md-table-cell>
                            <md-table-cell>
                                <div v-if="transaction!=undefined">
                                    {{timeForHuman(transaction.sentDate)}}
                                    <small>{{transaction.sentDate}}</small>
                                </div>
                            </md-table-cell>
                            <md-table-cell>
                                <div v-if="transaction!=undefined">
                                    {{$tc('phrases.inXSeconds',1,{x: timeDiffForHuman(transaction.sentDate,
                                    transaction.lastUpdate) })}}

                                </div>
                            </md-table-cell>
                        </md-table-row>
                    </md-table>
                </div>

            </div>
        </widget>
    </div>

</template>


<script>


import { timing } from '../../mixins/timing'
import { currency } from '../../mixins/currency'
import { EventBus } from '../../shared/eventbus'
import Widget from '../../shared/widget'
import FilterTransaction from './FilterTransaction'
import Box from '../Box'
import { TransactionService } from '../../services/TransactionService'
import airtelLogo from '../../../../../storage/app/public/icons/airtel.png'
import vodacomLogo from '../../../../../storage/app/public/icons/vodacom.png'
import thirdPartyLogo from '../../../../../storage/app/public/icons/third_party_transaction_icon.png'

export default {
    name: 'transactionList',
    mixins: [timing, currency],
    components: { Box, FilterTransaction, Widget },
    data () {
        return {
            transactionService: new TransactionService(),
            period: 'Yesterday',
            filter: [],
            loading: false,
            subscriber: 'transactionList',
            tab: 'all',
            paginator: null,
            analyticsData: null,
            analyticsPeriod: null,
            analyticsPeriods: [
                'Yesterday',
                'Same day last week',
                'Past 7 days',
                'Past 30 days'
            ],
            airtelLogo: airtelLogo,
            vodacomLogo: vodacomLogo,
            thirdPartyLogo: thirdPartyLogo
        }
    },

    mounted () {
        this.getTransactions()
        this.loadAnalytics()
        this.getPeriod()
        EventBus.$on('pageLoaded', this.reloadList)
    },
    beforeDestroy () {
        EventBus.$off('pageLoaded', this.reloadList)
        EventBus.$off('searching', this.searching)

    },
    methods: {
        filterTransaction (filterData) {
            let data = {}
            for (let i in filterData) {
                if (filterData[i] === null) {
                    continue
                }
                data[i] = filterData[i]
            }

            this.filter = data
            this.transactionService.searchAdvanced(data)

        },
        reloadList (sub, data) {

            if (sub !== this.subscriber) return
            this.transactionService.updateList(data)
            EventBus.$emit('dataLoaded')
            EventBus.$emit('widgetContentLoaded', this.subscriber, this.transactionService.list.length)
        },
        transactionDetail (id) {
            this.$router.push({ path: '/transactions/' + id })
        },

        async getTransactions () {
            try {
                await this.transactionService.getTransactions()

            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async loadAnalytics () {
            this.loading = true
            this.analyticsPeriod =
          this.analyticsPeriod === null ? 0 : this.analyticsPeriod
            try {
                this.analyticsData = await this.transactionService.getAnalytics(this.analyticsPeriod)

            } catch (e) {
                this.alertNotify('error', e.message)
            }

        },
        getPeriod (period = 'Yesterday') {

            switch (period) {
            case 'Yesterday':
                this.analyticsPeriod = 0
                break

            case 'Same day last week':
                this.analyticsPeriod = 1
                break

            case 'Past 7 days':
                this.analyticsPeriod = 2
                break

            case 'Past 30 days':
                this.analyticsPeriod = 3
                break

            default:
                break
            }

            this.loadAnalytics()
        },
        alertNotify (type, message) {
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message,
                speed: 0
            })
        },
    }
}
</script>

<style scoped>
    .box {
        border-right: 2px solid #6d7f94;
        padding-left: 45px;
        color: #6d7f94;
    }

    .information {
        font-size: 2.5rem;
        margin: 0.5rem 0;
    }

    .information.green {
        color: #0dba9a;
    }

    .information.red {
        color: #ba0f0d;
    }

    .information > small {
        font-size: 1.5rem;
    }

    .sub-information > .green {
        color: #61c7b3;
    }

    .sub-information > .red {
        color: #ba0f0d;
    }

    .header {
        clear: both;
    }

    .card-list {
        display: -webkit-inline-box !important;
        width: 100%;
    }

    .card-list-item {
        width: 25% !important;
    }

    .card-list-item-content {
        width: 100% !important;
    }

    .transaction-list-grid {
        padding: 1rem;
    }
</style>

