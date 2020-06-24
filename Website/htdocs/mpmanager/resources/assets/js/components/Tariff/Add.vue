<template>

    <widget
        v-if="showAdd"
        title="Add New Tariff"
    >
        <password-protection/>

        <md-card>

            <md-card-content>
                <div class="md-layout md-gutter">
                    <div class="md-layout-item md-large-size-100 md-medium-size-100 md-small-size-100">
                        <form class="md-layout md-gutter" data-vv-scope="Tariff-Form">
                            <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                <md-field :class="{'md-invalid': errors.has('Tariff-Form.tariff_name')}"
                                >
                                    <label for="tariff_name">Tariff Name</label>
                                    <md-input
                                        id="tariff_name"
                                        name="tariff_name"
                                        v-model="tariff.name"
                                        v-validate="'required|min:3'"
                                    />
                                    <span class="md-error">{{ errors.first('Tariff-Form.tariff_name') }}</span>
                                </md-field>
                            </div>
                            <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                <md-field :class="{'md-invalid': errors.has('Tariff-Form.kwh_price')}">
                                    <label for="kwh_price">kWh Price </label>
                                    <md-input id="kwh_price"
                                              name="kwh_price"
                                              v-model="tariff.price"
                                              v-validate="'required|integer'"
                                    />
                                    <span class="md-error">{{ errors.first('Tariff-Form.kwh_price') }}</span>
                                </md-field>
                            </div>

                        </form>

                    </div>
                    <div class="md-layout-item md-large-size-100 md-medium-size-100 md-small-size-100"
                         v-if="hasAccessRate">
                        <form class="md-layout md-gutter" data-vv-scope="Access-Rate-Form">

                            <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                <md-field :class="{'md-invalid': errors.has('Access-Rate-Form.access_rate_price')}">
                                    <label for="access_rate_price">Access Rate Price </label>
                                    <md-input id="access_rate_price"
                                              name="access_rate_price"
                                              v-model="accessRate.amount"
                                              v-validate="'required|integer'"
                                    />
                                    <span
                                        class="md-error">{{ errors.first('Access-Rate-Form.access_rate_price') }}</span>
                                </md-field>
                            </div>
                            <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 ">
                                <md-field
                                    :class="{'md-invalid': errors.has('Access-Rate-Form.access_rate_period')}">
                                    <label for="ar_period">Access Rate Period in days </label>
                                    <md-input id="ar_period"
                                              name="access_rate_period"
                                              v-model="accessRate.period"
                                              v-validate="'required|integer|min_value:1'"
                                    />
                                    <span
                                        class="md-error">{{ errors.first('Access-Rate-Form.access_rate_period') }}</span>
                                </md-field>
                            </div>
                        </form>


                    </div>
                    <div class="md-layout-item md-large-size-100 md-medium-size-100 md-small-size-100">
                        <md-checkbox v-model="hasAccessRate">Enable access rate?</md-checkbox>
                    </div>
                    <div class="md-layout-item md-large-size-100 md-medium-size-100 md-small-size-100">

                        <md-button role="button" class="md-raised md-secondary" @click="addComponent">
                            <font-awesome-icon icon="plus"/>
                            Add Additional Cost Component
                        </md-button>
                    </div>

                    <div class="md-layout-item md-large-size-100 md-medium-size-100 md-small-size-100"
                         v-for="(component,index) in components" :key="index">
                        <form class="md-layout md-gutter" data-vv-scope="Component-Form">
                            <div class="md-layout-item md-large-size-45 md-medium-size-45 md-small-size-45">

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
                            <div class="md-layout-item md-large-size-45 md-medium-size-45 md-small-size-45">

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
                            <div class="md-layout-item md-large-size-10 md-medium-size-10 md-small-size-10"
                                 @click="removeComponent(component.id)">

                                <md-icon style="margin-top: 1.5rem;color: red;">cancel
                                </md-icon>

                            </div>
                        </form>
                    </div>
                    <div class="md-layout-item md-large-size-100 md-medium-size-100 md-small-size-100">
                        <a @click="showSocialOptions()" v-if="!socialOptions" class="show-tariff-link">Show social
                            tariff options</a>
                        <a @click="showSocialOptions()" v-else class="show-tariff-link">Hide social tariff options</a>
                    </div>
                    <div class="md-layout-item md-large-size-100 md-medium-size-100 md-small-size-100"
                         v-if="socialOptions">
                        <form class="md-layout md-gutter" data-vv-scope="Social-Form">
                            <div class="md-layout-item md-large-size-30 md-medium-size-30 md-small-size-30 ">

                                <h3>Daily allowance at social tariff</h3>


                            </div>
                            <div class="md-layout-item md-large-size-20 md-medium-size-20 md-small-size-20 ">
                                <md-field :class="{'md-invalid': errors.has('Social-Form.daily_allowance')}">

                                    <md-input id="daily_allowance"
                                              type="number"
                                              min="0"
                                              name="daily_allowance"
                                              v-model="socialTariff.dailyAllowance"
                                              v-validate="'required|integer'"
                                              class="social-input"
                                    />

                                    <span class="md-error">{{ errors.first('Social-Form.daily_allowance') }}</span>
                                    <span class="md-suffix">Wh.</span>
                                </md-field>

                            </div>
                            <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 "></div>

                            <div class="md-layout-item md-large-size-30 md-medium-size-30 md-small-size-30 "
                            >

                                <h3>Social tariff</h3>

                            </div>
                            <div class="md-layout-item md-large-size-20 md-medium-size-20 md-small-size-20 "
                            >
                                <md-field :class="{'md-invalid': errors.has('Social-Form.social_price')}">
                                    <label for="social_price">Price per kWh </label>
                                    <md-input
                                        id="social_price"
                                        name="social_price"
                                        min="0"
                                        v-model="socialTariff.price"
                                        v-validate="'required|integer'"
                                        class="social-input"
                                    />
                                    <span class="md-error">{{ errors.first('Social-Form.social_price') }}</span>
                                    <span class="md-suffix">TZS</span>
                                </md-field>
                            </div>
                            <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 "></div>

                            <div class="md-layout-item md-large-size-30 md-medium-size-30 md-small-size-30 "
                            >

                                <h3>Initial energy budget</h3>


                            </div>
                            <div class="md-layout-item md-large-size-20 md-medium-size-20 md-small-size-20 "
                            >
                                <md-field :class="{'md-invalid': errors.has('Social-Form.initial_energy_budget')}">

                                    <md-input id="initial_energy_budget"
                                              type="number"
                                              min="0"
                                              name="initial_energy_budget"
                                              v-model="socialTariff.initialEnergyBudget"
                                              v-validate="'required|integer'"
                                              class="social-input"
                                    />
                                    <span
                                        class="md-error">{{ errors.first('Social-Form.initial_energy_budget') }}</span>
                                    <span class="md-suffix">Wh.</span>
                                </md-field>
                            </div>
                            <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 "></div>

                            <div class="md-layout-item md-large-size-30 md-medium-size-30 md-small-size-30"
                            >

                                <h3>Maximum stacked energy</h3>


                            </div>
                            <div class="md-layout-item md-large-size-20 md-medium-size-20 md-small-size-20 "
                            >
                                <md-field :class="{'md-invalid': errors.has('Social-Form.maximum_stacked_energy')}">

                                    <md-input id="maximum_stacked_energy"
                                              type="number"
                                              min="0"
                                              name="maximum_stacked_energy"
                                              v-model="socialTariff.maximumStackedEnergy"
                                              v-validate="'required|integer'"
                                              class="social-input"
                                    />
                                    <span
                                        class="md-error">{{ errors.first('Social-Form.maximum_stacked_energy') }}</span>
                                    <span class="md-suffix">Wh.</span>
                                </md-field>
                            </div>
                            <div class="md-layout-item md-large-size-50 md-medium-size-50 md-small-size-50 "></div>
                        </form>

                    </div>
                </div>
            </md-card-content>

            <md-card-actions>
                <md-button role="button" class="md-raised md-primary" @click="saveTariff">Save
                </md-button>
                <md-button role="button" class="md-raised" @click="hide">Close</md-button>
            </md-card-actions>
        </md-card>

    </widget>

</template>

<script>
    import { EventBus } from '../../shared/eventbus'
    import { AccessRate } from '../../classes/AccessRate'
    import PasswordProtection from '../PasswordProtection'
    import Widget from '../../shared/widget'
    import { TariffService } from '../../services/TariffService'

    export default {
        name: 'Add',
        components: { Widget, PasswordProtection },
        data () {
            return {
                showAdd: false,
                tariff: null,
                accessRate: null,
                hasAccessRate: false,
                tariffService: new TariffService(),
                components: [],
                socialOptions: false,
                socialTariff: null
            }
        },
        created () {
            this.tariff = this.tariffService.tariff
            this.socialTariff = this.tariffService.socialTariff
        },
        mounted () {
            EventBus.$on('showNewTariff', this.show)
        },
        methods: {
            hide () {
                this.showAdd = false
            },
            show () {
                this.showAdd = true
                this.tariff = this.tariffService.tariff
                this.accessRate = this.tariffService.AccessRate
            },
            async saveTariff () {
                let validatorTariff = true
                let validatorAccessRate = true
                let validatorComponent = true
                let validatorSocial = true

                if (this.hasAccessRate)
                    validatorAccessRate = await this.$validator.validateAll('Access-Rate-Form')

                if (this.socialOptions)
                    validatorSocial = await this.$validator.validateAll('Social-Form')
                if (this.components.length > 0)
                    validatorComponent = await this.$validator.validateAll('Component-Form')

                validatorTariff = await this.$validator.validateAll('Tariff-Form')

                if (validatorTariff && validatorAccessRate && validatorComponent && validatorSocial) {
                    try {
                        this.hide()

                        this.tariffService.setAccessRate(this.hasAccessRate, this.accessRate)
                        this.tariff = await this.tariffService.createTariff()
                        EventBus.$emit('tariffAdded', this.tariff)
                        this.alertNotify('success', 'New tariff registered successfully.')

                    } catch (e) {
                        this.alertNotify('error', e.message)
                    }
                }

            },
            addComponent () {
                this.components = this.tariffService.addAdditionalCostComponent()
            },
            removeComponent (id) {
                this.components = this.tariffService.removeAdditionalComponent(id)
            },
            showSocialOptions () {
                this.socialOptions = !this.socialOptions
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
    .show-tariff-link {
        cursor: pointer
    }

    .social-input {
        text-align: right;
    }
</style>


