<template>
    <widget
        title="Tariff Detail"
    >
        <md-card>

            <md-card-content>
                <div class="md-layout md-gutter">
                    <!--Tariff-->
                    <div class="md-layout-item  md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100">
                        <form class="md-layout md-gutter" data-vv-scope="Tariff-Form">
                            <div
                                class="md-layout-item  md-xlarge-size-100 md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                <md-field :class="{'md-invalid': errors.has('Tariff-Form.tariff_name')}"
                                >
                                    <label for="tariff_name">Tariff Name</label>
                                    <md-input
                                        id="tariff_name"
                                        name="tariff_name"
                                        v-model="tariffService.tariff.name"
                                        v-validate="'required|min:3'"
                                    />
                                    <span class="md-error">{{ errors.first('Tariff-Form.tariff_name') }}</span>
                                </md-field>
                            </div>
                            <div
                                class="md-layout-item md-xlarge-size-50 md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                <md-field :class="{'md-invalid': errors.has('Tariff-Form.kwh_price')}">
                                    <label for="kwh_price">kWh Price (last two digits represents two decimals ex: 100 =
                                        1.00)</label>
                                    <md-input id="kwh_price"
                                              name="kwh_price"
                                              v-model="tariffService.tariff.price"
                                              v-validate="'required|integer'"
                                              @change="tariffPriceChange()"
                                    />
                                    <span class="md-error">{{ errors.first('Tariff-Form.kwh_price') }}</span>
                                </md-field>
                            </div>

                        </form>

                    </div>

                    <!--Access-Rate-->
                    <div class="md-layout-item md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100"
                        v-if="hasAccessRate">
                        <form class="md-layout md-gutter" data-vv-scope="Access-Rate-Form">

                            <div
                                class="md-layout-item md-xlarge-size-50 md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                <md-field :class="{'md-invalid': errors.has('Access-Rate-Form.access_rate_price')}">
                                    <label for="access_rate_price">Access Rate Price </label>
                                    <md-input id="access_rate_price"
                                              name="access_rate_price"
                                              v-model="tariffService.tariff.accessRate.amount"
                                              v-validate="'required|integer'"
                                    />
                                    <span
                                        class="md-error">{{ errors.first('Access-Rate-Form.access_rate_price') }}</span>
                                </md-field>
                            </div>
                            <div
                                class="md-layout-item md-xlarge-size-50 md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                <md-field
                                    :class="{'md-invalid': errors.has('Access-Rate-Form.access_rate_period')}">
                                    <label for="ar_period">Access Rate Period in days </label>
                                    <md-input id="ar_period"
                                              name="access_rate_period"
                                              v-model="tariffService.tariff.accessRate.period"
                                              v-validate="'required|integer|min_value:1'"
                                    />
                                    <span
                                        class="md-error">{{ errors.first('Access-Rate-Form.access_rate_period') }}</span>
                                </md-field>
                            </div>
                        </form>


                    </div>
                    <div class="md-layout-item md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100">
                        <md-checkbox v-model="hasAccessRate" @change="accessRateChange($event)">Enable access rate?</md-checkbox>
                    </div>


                    <!--Additional-Components-->
                    <div  class="md-layout-item  md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100">

                        <md-button role="button" class="md-raised md-secondary" @click="addComponent('component')">
                            <font-awesome-icon icon="plus"/>
                            Add Additional Cost Component
                        </md-button>
                    </div>
                    <div class="md-layout-item md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100"
                        v-for="(component,index) in tariffService.tariff.components" :key="'component'+index">
                        <form class="md-layout md-gutter" data-vv-scope="Component-Form">
                            <div
                                class="md-layout-item md-xlarge-size-45 md-large-size-45 md-medium-size-45 md-small-size-45">

                                <md-field :class="{'md-invalid': errors.has('Component-Form.name')}">
                                    <label for="name">Name</label>
                                    <md-input
                                        id="name"
                                        name="name"
                                        v-model="component.name"
                                        v-validate="'required|min:3'"
                                    />
                                    <span class="md-error">{{ errors.first('Component-Form.name') }}</span>
                                </md-field>
                            </div>
                            <div
                                class="md-layout-item md-xlarge-size-45 md-large-size-45 md-medium-size-45 md-small-size-45">

                                <md-field :class="{'md-invalid': errors.has('Component-Form.price')}">
                                    <label for="price">Component Price</label>
                                    <md-input
                                        id="price"
                                        name="price"
                                        v-model="component.price"
                                        v-validate="'required|integer'"
                                    />
                                    <span class="md-error">{{ errors.first('Component-Form.price') }}</span>
                                </md-field>
                            </div>
                            <div
                                class="md-layout-item md-xlarge-size-10 md-large-size-10 md-medium-size-10 md-small-size-10"
                                @click="removeComponent('component',component.id)">

                                <md-icon style="margin-top: 1.5rem;color: red;">cancel
                                </md-icon>

                            </div>
                        </form>
                    </div>

                    <!--TOUS-->
                    <div class="md-layout-item  md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100">
                        <md-button role="button" :disabled="tariffService.conflicts.length>0" class="md-raised md-secondary" @click="addComponent('usage')">
                            <font-awesome-icon icon="plus"/>
                            Add TOU
                        </md-button>
                        <div v-if="tariffService.tariff.tous.length>0" role="alert" class="alert alert-info">
                            <strong> Attention!</strong>
                            In order to use this field, the meters that will use the tariff must be suitable for time of usages.
                        </div>
                    </div>
                    <div class="md-layout-item md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100"
                         v-for="(tou,index) in tariffService.tariff.tous" :key="'tou'+index">
                        <form class="md-layout md-gutter" data-vv-scope="Tou-Form">
                            <div class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-20 md-small-size-20">

                                <md-field :class="{'md-invalid': errors.has('Tou-Form.start'+tou.id)}">
                                    <label for="name">Start</label>
                                    <md-select v-model="tou.start" name="start" id="start" @md-selected="touSelected($event)">
                                        <md-option v-for="time in tariffService.times"
                                                   :value="time.time"
                                                   :key="time.id"
                                        >{{time.time}}</md-option>
                                    </md-select>
                                    <span class="md-error">{{ errors.first('Tou-Form.start'+tou.id) }}</span>
                                </md-field>
                            </div>
                            <div class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-20 md-small-size-20">

                                <md-field :class="{'md-invalid': errors.has('Tou-Form.end'+tou.id)}">
                                    <label for="end">End</label>
                                    <md-select v-model="tou.end" name="end" id="end"  @md-selected="touSelected($event)">
                                        <md-option v-for="time in tariffService.times"
                                                   :value="time.time"
                                                   :key="time.id"
                                        >{{time.time}}</md-option>
                                    </md-select>
                                    <span class="md-error">{{ errors.first('Tou-Form.end'+tou.id) }}</span>
                                </md-field>
                            </div>
                            <div class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-20 md-small-size-20">

                                <md-field :class="{'md-invalid': errors.has('Tou-Form.value')}">
                                    <label for="value">Value </label>
                                    <md-input
                                        placeholder="% of normal tariff"
                                        id="value"
                                        name="value"
                                        type="number"
                                        min="1"
                                        v-model="tou.value"
                                        v-validate="'required|decimal|min_value:1'"
                                        @change="touValueChange(tou)"
                                    />
                                    <span class="md-error">{{ errors.first('Tou-Form.value') }}</span>
                                </md-field>
                            </div>
                            <div class="md-layout-item md-xlarge-size-15 md-large-size-15 md-medium-size-15 md-small-size-15">
                                <md-field >
                                    <label for="value">Cost </label>
                                    <md-input
                                        :disabled="true"
                                        v-model="tou.cost"

                                    />

                                </md-field>
                            </div>
                            <div class="md-layout-item md-xlarge-size-5 md-large-size-5 md-medium-size-5 md-small-size-5"
                                 @click="removeComponent('usage',tou.id)">

                                <md-icon style="margin-top: 1.5rem;color: red;">cancel
                                </md-icon>

                            </div>
                        </form>
                    </div>


                    <!--Social-Tariffs-->
                    <div class="md-layout-item  md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100">
                        <a @click="showSocialOptions()" v-if="!tariffService.socialOptions" class="show-tariff-link">Show social
                            tariff options</a>
                        <a @click="showSocialOptions()" v-else class="show-tariff-link">Hide social tariff options</a>
                    </div>
                    <div class="md-layout-item  md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100"
                        v-if="tariffService.socialOptions">
                        <form class="md-layout md-gutter" data-vv-scope="Social-Form">
                            <div
                                class="md-layout-item md-xlarge-size-30 md-large-size-30 md-medium-size-30 md-small-size-30 ">

                                <h3>Daily allowance at social tariff</h3>


                            </div>
                            <div
                                class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-20 md-small-size-20 ">
                                <md-field :class="{'md-invalid': errors.has('Social-Form.daily_allowance')}">

                                    <md-input id="daily_allowance"
                                              type="number"
                                              min="0"
                                              name="daily_allowance"
                                              v-model="tariffService.tariff.socialTariff.dailyAllowance"
                                              v-validate="'required|integer'"
                                              class="social-input"
                                    />

                                    <span class="md-error">{{ errors.first('Social-Form.daily_allowance') }}</span>
                                    <span class="md-suffix">Wh.</span>
                                </md-field>

                            </div>
                            <div
                                class="md-layout-item md-xlarge-size-50 md-large-size-50 md-medium-size-50 md-small-size-50 "></div>

                            <div
                                class="md-layout-item md-xlarge-size-30 md-large-size-30 md-medium-size-30 md-small-size-30 "
                            >

                                <h3>Social tariff</h3>

                            </div>
                            <div
                                class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-20 md-small-size-20 "
                            >
                                <md-field :class="{'md-invalid': errors.has('Social-Form.social_price')}">
                                    <label for="social_price">Price per kWh </label>
                                    <md-input
                                        id="social_price"
                                        name="social_price"
                                        min="0"
                                        v-model="tariffService.tariff.socialTariff.price"
                                        v-validate="'required|integer'"
                                        class="social-input"
                                    />
                                    <span class="md-error">{{ errors.first('Social-Form.social_price') }}</span>
                                    <span class="md-suffix">{{appConfig.currency}}</span>
                                </md-field>
                            </div>
                            <div
                                class="md-layout-item md-xlarge-size-50 md-large-size-50 md-medium-size-50 md-small-size-50 "></div>

                            <div
                                class="md-layout-item md-xlarge-size-50 md-large-size-30 md-medium-size-30 md-small-size-30 "
                            >

                                <h3>Initial energy budget</h3>


                            </div>
                            <div
                                class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-20 md-small-size-20 "
                            >
                                <md-field :class="{'md-invalid': errors.has('Social-Form.initial_energy_budget')}">

                                    <md-input id="initial_energy_budget"
                                              type="number"
                                              min="0"
                                              name="initial_energy_budget"
                                              v-model="tariffService.tariff.socialTariff.initialEnergyBudget"
                                              v-validate="'required|integer'"
                                              class="social-input"
                                    />
                                    <span
                                        class="md-error">{{ errors.first('Social-Form.initial_energy_budget') }}</span>
                                    <span class="md-suffix">Wh.</span>
                                </md-field>
                            </div>
                            <div
                                class="md-layout-item md-xlarge-size-50 md-large-size-50 md-medium-size-50 md-small-size-50 "></div>

                            <div
                                class="md-layout-item md-xlarge-size-30 md-large-size-30 md-medium-size-30 md-small-size-30"
                            >

                                <h3>Maximum stacked energy</h3>


                            </div>
                            <div
                                class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-20 md-small-size-20 "
                            >
                                <md-field :class="{'md-invalid': errors.has('Social-Form.maximum_stacked_energy')}">

                                    <md-input id="maximum_stacked_energy"
                                              type="number"
                                              min="0"
                                              name="maximum_stacked_energy"
                                              v-model="tariffService.tariff.socialTariff.maximumStackedEnergy"
                                              v-validate="'required|integer'"
                                              class="social-input"
                                    />
                                    <span
                                        class="md-error">{{ errors.first('Social-Form.maximum_stacked_energy') }}</span>
                                    <span class="md-suffix">Wh.</span>
                                </md-field>
                            </div>
                            <div
                                class="md-layout-item md-xlarge-size-50 md-large-size-50 md-medium-size-50 md-small-size-50 "></div>
                        </form>

                    </div>
                </div>
                <md-progress-bar md-mode="indeterminate" v-if="loading"/>
            </md-card-content>

            <md-card-actions>
                <md-button role="button" class="md-raised md-primary" :disabled="loading" @click="showConfirmation">Save
                </md-button>

            </md-card-actions>
        </md-card>

    </widget>
</template>
<script>
import Widget from '../../shared/widget'
import { TariffService } from '../../services/TariffService'


export default {
    name: 'TariffDetail',
    components: { Widget },
    data () {
        return {
            showAdd: false,
            hasAccessRate: false,
            tariffService: new TariffService(),
            socialOptions: false,
            loading: false,
            tariffId: null
        }
    },
    created () {
        this.tariffId = this.$route.params.id

    },
    mounted () {
        this.getTariff()
    },
    methods: {
        async getTariff () {
            await this.tariffService.getTariff(this.tariffId)
            this.hasAccessRate = this.tariffService.hasAccessRate
        },

        async showConfirmation () {
            let countObject = await this.tariffService.tariffUsageCount(this.tariffId)
            let usageCount = countObject[0].count
            let text = ''
            if (usageCount > 0) {
                text = 'This tariff has using by ' + usageCount + ' of meters. Are you sure update this tariff?'
            } else {
                text = 'Are you sure update this tariff?'
            }
            this.$swal({
                type: 'question',
                title: 'Update',
                text: text,
                showCancelButton: true,
                confirmButtonText: 'I\'m sure',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.value) {
                    this.updateTariff()
                }
            })
        },
        async updateTariff () {
            let validatorTariff = true
            let validatorAccessRate = true
            let validatorComponent = true
            let validatorSocial = true
            let validatorTous = true
            if (this.hasAccessRate)
                validatorAccessRate = await this.$validator.validateAll('Access-Rate-Form')
            if (this.tariffService.socialOptions)
                validatorSocial = await this.$validator.validateAll('Social-Form')
            if (this.tariffService.tariff.components.length > 0)
                validatorComponent = await this.$validator.validateAll('Component-Form')
            if (this.tariffService.tariff.tous.length>0)
                validatorTous = await this.$validator.validateAll('Tou-Form')
            validatorTariff = await this.$validator.validateAll('Tariff-Form')

            if (validatorTariff && validatorAccessRate && validatorComponent && validatorSocial && validatorTous) {
                try {
                    this.loading = true
                    this.tariffService.setCurrency(this.appConfig.currency)
                    await this.tariffService.saveTariff('update')
                    this.loading = false
                    this.alertNotify('success', 'Tariff has registered successfully.')
                    this.$router.push({ path: '/tariffs' })
                } catch (e) {
                    this.loading = false
                    this.alertNotify('error', e.message)
                }
            }
        },

        addComponent (addedType) {
            this.tariffService.addAdditionalCostComponent(addedType)
            this.addConflictErrors()
        },
        removeComponent (addedType,id) {
            this.tariffService.removeAdditionalComponent(addedType,id)
            this.addConflictErrors()
        },
        showSocialOptions () {
            this.tariffService.socialOptions = !this.tariffService.socialOptions
            this.tariffService.resetSocialTariff()
        },
        accessRateChange(event){
            if (!event){
                this.tariffService.resetAccessRate()
            }
        },
        touSelected(event){

            this.tariffService.times.filter(x=>x.time===event)[0].using=true
            this.tariffService.findConflicts()
            this.addConflictErrors()

        },
        addConflictErrors(){
            this.$validator.errors.clear('Tou-Form')
            for (let i=0;i<this.tariffService.conflicts.length;i ++) {
                let errorStart = {
                    field:'start'+this.tariffService.conflicts[i],
                    msg: 'Overlaps !',
                    scope: 'Tou-Form',
                }

                this.$validator.errors.add(errorStart)
                let errorStop = {
                    field:'end'+this.tariffService.conflicts[i],
                    msg: 'Overlaps !',
                    scope: 'Tou-Form',
                }
                this.$validator.errors.add(errorStop)
            }
        },
        touValueChange(tou ){
            if (this.tariffService.tariff.price){
                let price =this.tariffService.tariff.price/100
                tou.cost= (price/100)*tou.value
            }
        },
        tariffPriceChange(){
            if(this.tariffService.tariff.tous){
                let price =this.tariffService.tariff.price/100
                this.tariffService.tariff.tous.forEach((e)=>{
                    e.cost= (price * e.value)/100
                })
            }
        },
        alertNotify (type, message) {
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message
            })
        },
    }
}
</script>
<style scoped>
    input[type="time"]::-webkit-calendar-picker-indicator {
        background: none;
    }
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px
    }

    .alert h4 {
        margin-top: 0;
        color: inherit
    }

    .alert .alert-link {
        font-weight: bold
    }

    .alert>p,.alert>ul {
        margin-bottom: 0
    }

    .alert>p+p {
        margin-top: 5px
    }

    .alert-dismissable,.alert-dismissible {
        padding-right: 35px
    }

    .alert-dismissable .close,.alert-dismissible .close {
        position: relative;
        top: -2px;
        right: -21px;
        color: inherit
    }


    .alert-info {
        background-color: #d9edf7;
        border-color: #bce8f1;
        color: #31708f
    }

    .alert-info hr {
        border-top-color: #a6e1ec
    }

    .alert-info .alert-link {
        color: #245269
    }

</style>
