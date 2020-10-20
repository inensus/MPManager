<template>
    <md-steppers :md-active-step.sync="activeStep" md-linear>
        <md-step class="stepper-step" id="firstStep" md-label="Activate Date-logger"
                 :md-done.sync="firstStep">
            <div class="exclamation">
                <div>
                    <div id="logger" v-if="purchasingType==='logger'">
                        <div class="md-layout-item md-size-100 exclamation-div">
                            <span>Free limit of MiniGrid Data-logger is exceeded. Your activated Mini-grid list listed below.</span>
                        </div>
                        <div class="md-layout-item md-size-100  exclamation-div">
                            <div>
                                <ul class="watched-miniGrid-List">
                                    <li v-for="(miniGrid,key) in watchingMiniGrids" :key="key">
                                        {{miniGrid.name}}
                                    </li>
                                </ul>

                            </div>

                        </div>
                        <div class="md-layout-item md-size-100 exclamation-div"><span>You can prefer deactivate one of them, or you can order more slots below.</span>
                        </div>
                    </div>
                    <div id="maintenance" v-if="purchasingType==='maintenance'">
                        <div class="md-layout-item md-size-100 exclamation-div">
                            <span>Free limit of Maintenance Users is exceeded..</span>
                        </div>

                        <div class="md-layout-item md-size-100 exclamation-div">
                            <span>You can  order more slots below.</span>
                        </div>
                    </div>
                    <div class="md-layout-item md-size-100 exclamation-div">
                        <md-button class="md-raised md-primary"
                                   v-if="!loadingNextStep"
                                   @click="nextStep('firstStep', 'secondStep')">
                            Continue
                        </md-button>

                        <md-progress-bar md-mode="indeterminate" v-else/>
                    </div>
                </div>
            </div>
        </md-step>
        <md-step class="stepper-step" id="secondStep" md-label="Authorization" :md-done.sync="secondStep">
            <div class="exclamation">
                <div>
                    <div class="md-layout-item md-size-100 exclamation-div">
                        <span>Please complete the transaction by entering the code provided in the purchase. </span>
                    </div>
                    <div class="md-layout-item md-size-100 exclamation-div">
                        <md-field>
                            <label>Purchase Code</label>
                            <md-input v-model="purchaseCode"></md-input>
                        </md-field>
                    </div>
                    <div class="md-layout-item md-size-100 exclamation-div">
                        <md-button class="md-raised md-primary"
                                   v-if="!loadingNextStep"
                                   @click="nextStep('secondStep', 'thirdStep')">
                            Continue
                        </md-button>
                        <md-progress-bar md-mode="indeterminate" v-else/>
                    </div>
                </div>
            </div>
        </md-step>

        <md-step class="stepper-step" id="thirdStep" md-label="Complete" :md-done.sync="thirdStep">

            <div class="exclamation">
                <div>
                    <div class="md-layout-item md-size-100" id="logger-done-success"
                         v-if="PaymentProcess===true">
                        <span class="success-span">Successful
                            <md-icon style="color: green">check</md-icon>
                        </span>

                        <div class="md-layout-item md-size-100 exclamation-div">
                            <span v-if="purchasingType==='logger' ">You have purchased one more slot to logging mini-grid data</span>
                            <span v-if="purchasingType==='maintenance' ">You have purchased five more slot to add new maintainers to system.</span>
                        </div>
                    </div>
                    <div class="md-layout-item md-size-100" id="logger-done-fail"
                         v-if="PaymentProcess===false">
                        <span class="failure-span">Something went wrong
                            <md-icon style="color: red">priority_high</md-icon>
                        </span>

                        <div class="md-layout-item md-size-100 exclamation-div">
                            <span>We were not able to process your Payment. Please contact the administrator.</span>
                        </div>
                    </div>

                    <div class="md-layout-item md-size-100">
                        <md-button class="md-raised md-primary" @click="closeStepper()">Done</md-button>
                    </div>
                </div>
            </div>


        </md-step>
    </md-steppers>
</template>


<script>


import { RestrictionService } from '../services/RestrictionService'
import { EventBus } from './eventbus'

export default {
    name: 'Stepper',

    props: {
        purchasingType: String,
        watchingMiniGrids: Array
    },
    data () {
        return {
            loadingNextStep: false,
            restrictionService: new RestrictionService(),
            activeStep: 'firstStep',
            firstStep: false,
            secondStep: false,
            thirdStep: false,
            purchaseCode: '',
            PaymentProcess: false,
        }
    },
    methods: {
        async nextStep (id, index) {
            this[id] = true
            this.loadingNextStep = true

            if (id === 'firstStep' && index === 'secondStep') {
                if (this.purchasingType === 'logger') {
                    window.open('https://micropowermanager.com/logger', '_blank')
                } else {
                    window.open('https://micropowermanager.com/maintainer', '_blank')
                }
                if (index) {
                    this.activeStep = index
                }

            } else if (id === 'secondStep' && index === 'thirdStep') {

                let email = this.$store.state.admin.email
                try {
                    let data = await this.restrictionService.sendPurchaseCode(this.purchaseCode, email)
                    let productCode = data.display_items[0].custom.description
                    let type = this.purchasingType === 'logger' ? 'mini-grid' : 'maintenance'

                    let codeIsValid = await this.restrictionService.purchaseCodeIsValid(this.purchaseCode, productCode, type)
                    if (codeIsValid) {
                        this.PaymentProcess = true
                    }
                } catch (e) {

                    this.PaymentProcess = false

                }
                if (index) {
                    this.activeStep = index
                }
            }

            this.loadingNextStep = false

        },

        closeStepper () {

            EventBus.$emit('closeModal', this.PaymentProcess)
        }
    },
}
</script>

<style scoped>
    .stepper-step {
        text-align: center !important;
    }

    .md-stepper-content .md-active {
        text-align: center !important;
    }

    .success-span {
        font-size: large;
        font-weight: 700;
        color: green;
    }

    .failure-span {
        font-size: large;
        font-weight: 700;
        color: darkred;
    }

    .exclamation {
        margin: auto;
        align-items: center;
        display: inline-grid;
        text-align: center;

    }

    .watched-miniGrid-List {
        font-size: 11px;
        width: 15%;
        margin: auto;
        font-weight: bold;
    }

    .exclamation-div {
        margin-top: 2% !important;
    }
</style>
