<template>
    <section id="widget-grid" v-if="isLoaded">
        <div class="md-layout md-gutter">

            <div class="md-layout-item md-size-55">

                <client-personal-data :person="person"/>
                <addresses :person-id="person.id" v-if="person!==null"/>
            </div>
            <div class="md-layout-item md-size-45">
                <payment-flow/>
                <deferred-payments :person-id="person.id" v-if="person!==null"/>
            </div>

            <div class="md-layout-item md-size-100">
                <transactions/>
            </div>

            <div class="md-layout-item md-size-100">
                <sms-history :person-id="personId" person-name="System"/>
            </div>

            <div class="md-layout-item md-size-50">
                <div class="client-detail-card">
                    <payment-detail/>
                </div>
                <div class="client-detail-card">
                    <ticket/>
                </div>
            </div>
            <div class="md-layout-item md-size-50">
                <div class="client-detail-card">
                    <client-meter-list :meterList="meters"/>
                </div>
                <div class="client-detail-card">
                    <mapiko :meters="meters" v-if="meters.length>0"/>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import { Person } from '../../classes/person'
    import { EventBus } from '../../shared/eventbus'
    import PaymentFlow from './PaymentFlow'
    import Transactions from './Transactions'
    import PaymentDetail from './PaymentDetail'
    import Mapiko from './Map'
    import Ticket from './Ticket'
    import Widget from '../../shared/widget'
    import Addresses from './Addresses'

    import Datepicker from 'vuejs-datepicker'
    import ClientMeterList from './ClientMeterList'
    import SmsHistory from './SmsHistory'
    import ClientPersonalData from './ClientPersonalData'
    import DeferredPayments from './DeferredPayments'

    export default {
        name: 'ClientDetail',
        data () {
            return {
                personId: null,
                isLoaded: false,
                editPerson: false,
                person: null,
                meters: [],
                bcd: {
                    Home: {
                        href: '/'
                    },
                    Customers: {
                        href: '/people'
                    },
                    Detail: {
                        href: null
                    }
                }
            }
        },
        components: {
            DeferredPayments,
            ClientPersonalData,
            SmsHistory,
            ClientMeterList,
            Widget,
            PaymentFlow,
            Transactions,
            PaymentDetail,
            Mapiko,
            Ticket,
            Addresses,
            Datepicker
        },
        created () {
            this.personId = this.$route.params.id
            this.getDetails(this.personId)
        },
        mounted () {
            EventBus.$emit('bread', this.bcd)
        },
        destroyed () {
            this.$store.state.person = null
            this.$store.state.meters = null
        },

        methods: {
            getDetails (id) {
                axios.get(resources.person.list + '/' + id).then(response => {
                    let data = response.data.data
                    this.isLoaded = true

                    this.person = new Person().initialize(data)
                    this.$store.state.person = this.person
                    this.meters = []
                    for (let i in data.meters) {
                        this.meters.push(data.meters[i].meter.id)
                    }
                })
            },
            dateForHumans (date) {
                return moment(date, 'YYYY-MM-DD HH:mm:ss').fromNow()
            }
        }
    }
</script>
<style>
    .asd__inner-wrapper {
        margin-left: 0 !important;
    }

    [data-letters]:before {
        content: attr(data-letters);
        display: inline-block;
        font-size: 1em;
        width: 2.5em;
        height: 2.5em;
        line-height: 2.5em;
        text-align: center;
        border-radius: 50%;
        background: plum;
        vertical-align: middle;
        margin-right: 1em;
        color: white;
    }


</style>
