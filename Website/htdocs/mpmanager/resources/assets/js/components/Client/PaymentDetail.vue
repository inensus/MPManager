<template>
    <div>
        <widget :title="$tc('phrases.paymentOverview',0, {period: periodName}) "
                :subscriber="subscriber"
        >
            <div slot="tabbar">
                <md-field>
                    <md-select class="period-style md-has-value" name="period" id="period" v-model="period"
                               @md-selected="getFlow">
                        <md-option value="D">{{ $tc('words.day',2) }}</md-option>
                        <md-option value="W">{{ $tc('words.week',2) }}</md-option>
                        <md-option value="M">{{ $tc('words.month',2) }}</md-option>
                        <md-option value="Y">{{ $tc('words.annually') }}</md-option>

                    </md-select>
                </md-field>
            </div>
            <div class="md-layout md-gutter">
                <div class="md-layout-item md-size-90">

                    <GChart
                        type="ColumnChart"
                        :data="chartData"
                        :options="chartOptions"
                        :resizeDebounce="500"
                    />


                </div>
            </div>

        </widget>
    </div>

</template>

<script>
import Widget from '../../shared/widget'
import { EventBus } from '../../shared/eventbus'

export default {
    name: 'PaymentDetail',
    data () {
        return {
            subscriber: 'payment-overview',
            contentWidth: 0,
            personId: null,
            period: 'M',
            periodName: 'Monthly',
            chartData: [],
            chartOptions: {
                chart: {
                    title: 'Customer Payment Flow',
                },
                colors: ['#0b920b', '#8b2621', '#0c7cd5']
            },

            barData: [],
        }
    },
    created () {
        this.personId = this.$store.getters.person.id
    },
    mounted () {
        this.getFlow()
        /*  this.contentWidth = document.getElementById('client-payment-detail').clientWidth*/
    },
    components: {
        Widget,
    },
    methods: {

        getFlow (period = 'M') {
            switch (period) {
            case 'Y':
                this.periodName = this.$tc('words.annually')
                break
            case 'M':
                this.periodName = this.$tc('words.month',2)
                break
            case 'W':
                this.periodName = this.$tc('words.week',2)
                break
            case 'D':
                this.periodName = this.$tc('words.day',2)
                break

            }
            axios.get(resources.paymenthistories + this.personId + '/payments/' + period)
                .then(response => {
                    this.chartData = [[this.$tc('words.period'), this.$tc('words.energy'), this.$tc('phrases.accessRate'), this.$tc('phrases.loanRate')]]

                    let data = response.data
                    for (let x in data) {
                        let items = []
                        items = [
                            x,
                            'energy' in data[x] ? parseInt(data[x]['energy']) : 0,
                            'access rate' in data[x] ? parseInt(data[x]['access rate']) : 0,
                            'loan rate' in data[x] ? parseInt(data[x]['loan rate']) : 0,
                        ]
                        this.chartData.push(items)
                        EventBus.$emit('widgetContentLoaded', this.subscriber, this.chartData.length)
                    }
                })

        }
    }
}
</script>
<style scoped>
.payment-period-select {
    float: right;
    padding-right: 2.5rem !important;
    padding-left: 2.5rem !important;
}

.period-style {
    color: white !important;
    -webkit-text-fill-color: white !important;

}

#period input[type="text"] {
    color: white !important;
    -webkit-text-fill-color: white !important;
}
</style>
