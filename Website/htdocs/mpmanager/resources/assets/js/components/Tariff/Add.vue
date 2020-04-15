<template>

    <widget
        v-if="showAdd"
        title="Add New Tariff"
    >
        <password-protection/>

        <md-card>

            <md-card-content>
                <div class="md-layout md-gutter">
                    <div class="md-layout-item">
                        <md-field :class="{'md-invalid': errors.has('tariff_name')}">
                            <label for="tariff_name">Tariff Name</label>
                            <md-input
                                id="tariff_name"
                                name="tariff_name"
                                v-model="tariff.name"
                                v-validate="'required|min:3'"
                            />
                            <span class="md-error">{{ errors.first('tariff_name') }}</span>
                        </md-field>

                        <md-field :class="{'md-invalid': errors.has('price')}">
                            <label for="kwh_price">kWh Price </label>
                            <md-input id="kwh_price"
                                      name="price"
                                      v-model="tariff.price"
                                      v-validate="'required|integer'"
                            />
                            <span class="md-error">{{ errors.first('price') }}</span>
                        </md-field>

                        <md-checkbox v-model="hasAccessRate">Has is access rate?</md-checkbox>
                    </div>

                    <div class="md-layout-item" v-if="hasAccessRate">
                        <md-field :class="{'md-invalid': errors.has('access_rate_price')}">
                            <label for="ar_price">Access Rate Price </label>
                            <md-input id="ar_price"
                                      name="access_rate_price"
                                      v-model="accessRate.amount"
                                      v-validate="'required|integer'"
                            />
                            <span class="md-error">{{ errors.first('access_rate_price') }}</span>
                        </md-field>

                        <md-field :class="{'md-invalid': errors.has('access_rate_period')}">
                            <label for="ar_period">Access Rate Period in days </label>
                            <md-input id="ar_period"
                                      name="access_rate_period"
                                      v-model="accessRate.period"
                                      v-validate="'required|integer'"
                            />
                            <span class="md-error">{{ errors.first('access_rate_period') }}</span>
                        </md-field>
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
    import {EventBus} from '../../shared/eventbus'
    import {AccessRate} from '../../classes/AccessRate'
    import PasswordProtection from '../PasswordProtection'
    import Widget from '../../shared/widget'
    import {TariffService} from '../../services/TariffService'

    export default {
        name: 'Add',
        components: {Widget, PasswordProtection},
        data() {
            return {
                showAdd: false,
                tariff: null,
                accessRate: new AccessRate(),
                hasAccessRate: false,
                tariffService: new TariffService(),

            }
        },
        created() {
            this.tariff = this.tariffService.tariff;
        },
        mounted() {
            EventBus.$on('showNewTariff', this.show)
        },
        methods: {
            hide() {
                this.showAdd = false
            },
            show() {
                this.showAdd = true;

                this.accessRate = new AccessRate();
            },
            async saveTariff() {
                let validator = await this.$validator.validateAll();
                if (!validator) {

                    return
                }
                this.hide();
                this.tariffService.setAccessRate(this.accessRate);
                this.tariffService.createTariff().then((response) => {
                    this.alertNotify('success', 'Tariff has registered.')
                }).catch((e) => {
                    this.alertNotify('error', e.message)
                })
            },

            alertNotify(type, message) {
                this.$notify({
                    group: "notify",
                    type: type,
                    title: type + " !",
                    text: message
                });
            },
        }

    }
</script>

<style scoped>

</style>
