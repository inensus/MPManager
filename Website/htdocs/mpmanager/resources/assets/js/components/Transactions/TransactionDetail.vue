<template>
    <section id="widget-grid" v-if="transaction">
        <div class="row">
            <div class="md-layout md-gutter">
                <div class="md-layout-item md-size-50">
                    <div class="transaction-detail-card">
                        <widget title="Provider Specific Information">
                            <md-card>
                                <md-card-content>


                                            <vodacom-transaction-detail
                                                :ot="ot"
                                                v-if="transaction.original_transaction_type === 'vodacom_transaction'"
                                            />

                                            <airtel-transaction-detail
                                                :ot="ot"
                                                v-if="transaction.original_transaction_type === 'airtel_transaction'"
                                            />


                                </md-card-content>
                            </md-card>
                        </widget>
                    </div>

                </div>

                <div class="md-layout-item md-size-50">
                    <div class="transaction-detail-card">
                        <widget color="red" title="Details">
                            <md-card>
                                <md-card-content>
                                    <div class="md-layout">
                                        <div class="md-layout-item md-subheader">Sender</div>
                                        <div class="md-layout-item md-subheader n-font">{{transaction.sender}}</div>
                                    </div>
                                    <hr class="hr-d">
                                    <div class="md-layout">
                                        <div class="md-layout-item md-subheader">Amount</div>
                                        <div class="md-layout-item md-subheader n-font">
                                            {{readable(transaction.amount)}}
                                        </div>
                                    </div>
                                    <hr class="hr-d">
                                    <div class="md-layout">
                                        <div class="md-layout-item md-subheader">Payment Type</div>
                                        <div class="md-layout-item md-subheader n-font"><span
                                            v-text="transaction.type === 'energy' ? 'Energy' : 'Deferred Payment'"></span>
                                        </div>
                                    </div>
                                    <hr class="hr-d">
                                    <div class="md-layout">
                                        <div class="md-layout-item md-subheader">Meter</div>
                                        <div class="md-layout-item md-subheader n-font">
                                            <router-link
                                                :to="{path: '/meters/' + transaction.message}"
                                                class="nav-link"
                                            >{{transaction.message}}
                                            </router-link>
                                        </div>
                                    </div>
                                    <hr class="hr-d">
                                    <div class="md-layout">
                                        <div class="md-layout-item md-subheader">Customer</div>
                                        <div class="md-layout-item md-subheader n-font">
                                            <router-link
                                                :to="{path: '/people/' + personId}"
                                                class="nav-link"
                                            >{{personName}}
                                            </router-link>
                                        </div>
                                    </div>
                                    <hr class="hr-d">
                                    <div class="md-layout">
                                        <div class="md-layout-item md-subheader">Date</div>
                                        <div class="md-layout-item md-subheader n-font">
                                            {{timeForHuman(transaction.created_at)}}
                                            <small>{{transaction.created_at}}</small></div>
                                    </div>
                                </md-card-content>
                            </md-card>
                        </widget>
                    </div>
                </div>
            </div>
            <div class="md-layout md-gutter">
                <div class="md-layout-item md-size-50">
                    <div class="transaction-detail-card">
                        <widget color="green" title="Transaction Processing">
                            <md-card>
                                <md-card-content v-if="ot.status===1">
                                    <div class="md-layout md-gutter md-size-100" justify="around">
                                        <div class="md-layout-item md-size-40" >
                                            <div>
                                                <div class="md-layout">
                                                    <div class="md-layout-item md-subheader">Payment For</div>
                                                    <div class="md-layout-item md-subheader">Amount</div>
                                                </div>
                                                <hr class="hr-d">

                                                <div :key="i" class="md-layout"
                                                     v-for="(p,i) in transaction.payment_histories">
                                                    <div class="md-layout-item md-subheader n-font">{{p.payment_type}}
                                                    </div>
                                                    <div class="md-layout-item md-subheader n-font">
                                                        {{readable(p.amount)}}
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                        <div class="md-layout-item md-size-60" style="margin: auto;" width="7of12">
                                            <payment-history-chart :paymentdata="transaction.payment_histories"/>
                                        </div>
                                    </div>
                                </md-card-content>
                                <md-card-content v-if="ot.status===-1">
                                    <h2>Transaction cancelled</h2>
                                    <md-list class="md-double-line">
                                        <md-subheader style="color:#a81e10">Transaction cancelled</md-subheader>

                                        <md-list-item :key="conflict.id" v-for="conflict in ot.conflicts">
                                            <span class="margin-top-5">{{conflict.state}}</span>
                                        </md-list-item>
                                    </md-list>
                                </md-card-content>
                            </md-card>
                        </widget>
                    </div>
                </div>
                <div class="md-layout-item md-size-50">
                    <div class="transaction-detail-card">
                        <widget color="red" title="Outgoing sms">
                            <md-card>
                                <md-card-content v-if="transaction.sms">

                                            <div class="md-layout md-gutter md-size-100">
                                                <div class="md-layout-item md-subheader md-size-20">To</div>
                                                <div class="md-layout-item md-subheader md-size-80">
                                                    {{transaction.sms.receiver}}
                                                </div>
                                            </div>
                                            <div class="md-layout md-gutter md-size-100">
                                                <div class="md-layout-item md-subheader md-size-20">Body</div>
                                                <div class="md-layout-item md-subheader md-size-75 message-box">

                                                    {{transaction.sms.body}}

                                                </div>
                                            </div>

                                </md-card-content>
                            </md-card>
                        </widget>
                    </div>
                </div>
            </div>


        </div>

    </section>
</template>

<script>
    import {resources} from "../../resources";

    import {EventBus} from "../../shared/eventbus";
    import {timing} from "../../mixins/timing";
    import {currency} from "../../mixins/currency";
    import VodacomTransactionDetail from "./VodacomTransactionDetail";
    import PaymentHistoryChart from "./PaymentHistoryChart";
    import AirtelTransactionDetail from "./AirtelTransactionDetail";
    import Widget from "../../shared/widget";

    export default {
        name: "TransactionDetail",
        components: {
            AirtelTransactionDetail,
            Widget,
            VodacomTransactionDetail,
            PaymentHistoryChart
        },
        mixins: [timing, currency],
        created() {
            this.transactionId = this.$route.params.id;
        },
        mounted() {
            this.getDetail(this.transactionId);
            EventBus.$emit("bread", this.bcd);
            // this.$on("pageLoaded", function() {
            //   window.setTimeout(() => {
            //     pageSetUp();
            //   }, 100);
            // });
            // pageSetup();
        },
        data() {
            return {
                paginator: null,
                bcd: {
                    Home: {
                        href: "/"
                    },
                    Transactions: {
                        href: "/transactions"
                    },
                    Detail: {
                        href: null
                    }
                },
                transactionId: null,
                transaction: null,
                personName: null,
                personId: null
            };
        },
        computed: {
            ot: function () {
                return this.transaction.original_transaction;
            }
        },
        methods: {
            getDetail(id) {
                axios
                    .get(resources.transactions.detail + this.transactionId)
                    .then(response => {
                        this.transaction = response.data.data;

                        if (response.data.data.payment_histories !== null) {
                            this.getRelatedPerson(
                                response.data.data.payment_histories[0].payer_id
                            );
                        }
                    });
            },
            meterDetail() {
            },

            getRelatedPerson(personId) {
                axios.get(resources.person.detail + personId).then(response => {
                    this.personName =
                        response.data.data.name + " " + response.data.data.surname;
                    this.personId = response.data.data.id;
                });
            }
        }
    };
</script>

<style scoped>
    .transaction-detail-card {
        margin-top: 1rem !important;
        margin-right: 1rem !important;
    }

    .n-font {
        font-weight: 100 !important;
    }

    .hr-d {
        height: 1pt;
        margin: auto;
        padding: 0;
        display: block;
        border: 0;
        /* transition: margin-left .3s cubic-bezier(.4,0,.2,1); */
        /* will-change: margin-left; */
        background-color: rgba(0, 0, 0, 0.12);
    }

    .message-box {
        padding: 10px;
        background-color: #f5e8e8;
        -moz-border-radius: 10px;
        border-radius: 14px;
        margin-top: 2vh;


    }
</style>
