<template>
<div>
    <widget
        :id="'revenue-targets'"
        :headless="true"
        :title="$tc('phrases.revenueTargetsPerCustomerType')"
        color="green">

        <div class="row" v-if="batchRevenues.revenueList !== null"
             style="margin: 2vh;">


            <div class="md-layout" style="margin-bottom: 0.8vh;"
                 v-for="(revenue, index) in getPercentileList()" :key="index">
                <div class="md-layout-item md-size-100">
                    {{index}}
                </div>
                <div class="md-layout-item md-size-100">
                    <div>
                        <md-progress-bar class="md-dense" md-mode="determinate"
                                         :md-value="targetPercentage(batchRevenues.revenueList.revenue[index], revenue.revenue)">

                        </md-progress-bar>
                        <md-tooltip md-delay="300" md-direction="bottom">
                            {{ $tc('phrases.revenueTargetsPerCustomerType',2) }}:
                            {{ targetPercentage(batchRevenues.revenueList.revenue[index],
                            revenue.revenue, false)}} %
                            {{readable(batchRevenues.revenueList.revenue[index])}}
                        </md-tooltip>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-sm-12 text-center">
                            <span v-for="i in totalCircles()" style="margin:5px" :key="i" class="dot compare-color-bg"
                                  :class="currentSelectedTargetCircle=== i-1 ? '':'period-indicator' "
                                  @click="setCircleIndex(i-1)"> </span>

        </div>
        <div class="col-sm-12 text-center">
            {{currentSelectedTargetCircle+1}} of {{totalCircles()}}
        </div>

    </widget>
</div>
</template>

<script>
import Widget from '../../shared/widget'
import { currency } from '../../mixins/currency'
export default {
    name: 'RevenueTargetPerCustomerType',
    components:{ Widget },
    props:{
        batchRevenues:{
            required: true,
        }
    },
    mixins: [currency],
    data(){
        return{
            currentSelectedTargetCircle: 0,
            displayedTargetPercetinles: [0, 5],
        }
    },
    methods:{
        setCircleIndex (index) {
            this.displayedTargetPercetinles[0] = index * 5
            this.displayedTargetPercetinles[1] = this.displayedTargetPercetinles[0] + 5
            this.currentSelectedTargetCircle = index
        },
        //calculates the reached target percentage
        targetPercentage (actualRevenue, targetRevenue, makeHundred = true) {
            if (typeof (targetRevenue) === 'undefined') return 0
            if (targetRevenue === 0) return 100
            let result = parseInt(parseInt(actualRevenue) * 100 / parseInt(targetRevenue))
            if (Number.isNaN(result))
                return 0
            return makeHundred === true ? (result > 100 ? 100 : result) : result
        },
        getPercentileList () {
            let tmpList = {}
            let counter = 0
            for (let t in this.batchRevenues.revenueList.target.targets) {
                if (counter < this.displayedTargetPercetinles[0]) {
                    counter++
                    continue
                }
                if (counter >= this.displayedTargetPercetinles[1]) {
                    break
                }
                tmpList[t] = this.batchRevenues.revenueList.target.targets[t]
                counter++
            }
            return tmpList
        },
        totalCircles () {
            if (this.batchRevenues.revenueList === null) {
                return 0
            }
            return Math.ceil(Object.keys(this.batchRevenues.revenueList.target.targets).length / 4)
        },
    }
}
</script>

<style scoped>

</style>
