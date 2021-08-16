<template>
    <div>

        <widget
            :class="'col-sm-6 col-md-5'"
            :button-text="$tc('phrases.assignAppliance',0)"
            :button="true"
            :title="$tc('phrases.soldAppliances')"
            :button-color="'red'"
            color="green"
            :subscriber="subscriber"
            @widgetAction="soldNewAsset"
        >
            <confirmation-box :title="$tc('phrases.editRate')" @confirmed="editRate"></confirmation-box>
            <!-- assing new asset -->

            <div>
                    <md-table>
                        <md-table-row>
                            <md-table-head>{{ $tc('words.name') }}</md-table-head>
                            <md-table-head>{{ $tc('words.cost') }}</md-table-head>
                            <md-table-head>{{ $tc('words.rate',1) }}</md-table-head>
                        </md-table-row>
                        <md-table-row v-for="(item, index) in assetPersonService.list" :key="index">
                            <md-table-cell md-label="Name" md-sort-by="name">{{item.asset_type.name}}</md-table-cell>
                            <md-table-cell md-label="Cost" md-sort-by="total_cost">{{item.total_cost}} {{ $store.getters['settings/getMainSettings'].currency }}</md-table-cell>
                            <md-table-cell md-label="Rates" md-sort-by="rate_count">
                                {{ item.rate_count}}
                                <div
                                    :class="index=== -999?'text-danger':'text-success'"
                                    style="cursor: pointer; display:inline-block"
                                    @click="showDetails(index)"
                                >
                                    <md-icon>remove_red_eye</md-icon>
                                    {{ $tc('words.detail',1) }}
                                </div>
                            </md-table-cell>
                        </md-table-row>
                    </md-table>
            </div>

        </widget>
        <md-dialog :md-active.sync="showNewAsset" >
            <form novalidate class="md-layout" @submit.prevent="saveAsset">
                <md-card class="md-layout-item" scrollablez>
                    <md-card-header>
                        <div style="float:right; cursor:pointer" @click="soldNewAsset">
                            <md-icon>close</md-icon>&nbsp;Close
                        </div>
                    </md-card-header>
                    <md-card-content>
                        <md-field :class="{'md-invalid': errors.has($tc('phrases.assetType'))}">
                            <label for="asset_types">{{$tc('phrases.assetType')}}</label>
                            <md-select :name="$tc('phrases.assetType')" id="asset_types" v-model="newAsset.id"
                            >
                                <md-option disabled value>--{{ $tc('words.select') }}--</md-option>
                                <md-option
                                    :value="assetType.id"
                                    v-for="assetType in assetService.list"
                                    :key="assetType.id"
                                >{{assetType.name}}
                                </md-option>
                            </md-select>
                            <span class="md-error">{{ errors.first($tc($tc('phrases.assetType'))) }}</span>
                        </md-field>
                        <md-field :class="{'md-invalid': errors.has($tc('words.cost'))}">
                            <label for="Cost">{{$tc('words.cost')}}</label>
                            <md-input type="number"
                                      :name="$tc('words.cost')"
                                      id="Cost"
                                      v-model="newAsset.cost"
                                      v-validate="'required'"/>
                            <span class="md-error">{{ errors.first($tc('phrases.ratesCount')) }}</span>
                        </md-field>

                        <md-field :class="{'md-invalid': errors.has($tc('words.rate'))}">
                            <label for="rate">{{ $tc('phrases.ratesCount') }}</label>
                            <md-input type="number"
                                      :name="$tc('phrases.ratesCount')"
                                      id="rate"
                                      v-model="newAsset.rate"
                                      v-validate="'required|numeric'"
                            />
                            <span class="md-error">{{ errors.first($tc('words.rate')) }}</span>
                        </md-field>
                        <div v-if="newAsset.rate">
                            <div v-for="x in parseInt(newAsset.rate)" :key="x"
                                 class="col-md-3 col-sm-4">
                                <span v-if="x<10" style="opacity: 0;">0</span>
                                {{x}}&nbsp;-&nbsp;{{ readable(getRate(x,
                                newAsset.rate,newAsset.cost ))}} {{ $store.getters['settings/getMainSettings'].currency }}
                            </div>
                        </div>

                    </md-card-content>
                    <md-card-actions>
                        <md-button type="submit" class="md-primary btn-sell">{{ $tc('words.sell') }}</md-button>
                    </md-card-actions>
                </md-card>
            </form>
        </md-dialog>
        <md-dialog v-if="selectedAsset!==null" :md-active.sync="showModal">
            <md-dialog-title>
                Details of
                <strong>{{selectedAsset.asset_type.name}}</strong>
            </md-dialog-title>

            <div class="md-layout md-gutter dialog-place">
                <div class="md-layout-item md-size-100">
                    <span>{{ $tc('phrases.totalCost') }} :</span>
                    <span>{{readable(selectedAsset.total_cost)}}</span>
                    <br/>
                    <span>{{ $tc('phrases.soldDate')}}:</span>
                    <span>{{formatReadableDate(selectedAsset.created_at)}}</span>
                    <br/>
                    <span>{{ $tc('phrases.ratesCount') }} :</span>
                    <span>{{selectedAsset.rate_count}}</span>
                </div>
                <div class="md-layout-item md-size-100">
                    <strong>{{$tc('words.rates',2)}}</strong>
                </div>
                <div class="md-layout-item md-size-100">
                    <md-table>
                        <md-table-row>
                            <md-table-head>
                                <strong>{{ $tc('words.cost') }}</strong>
                            </md-table-head>
                            <md-table-head>
                                <strong>{{ $tc('phrases.remainingAmount') }}</strong>
                            </md-table-head>
                            <md-table-head>
                                <strong>{{ $tc('phrases.dueDate') }}</strong>
                            </md-table-head>
                            <md-table-head>
                                <strong>#</strong>
                            </md-table-head>
                        </md-table-row>
                        <md-table-row v-for="rate in selectedAsset.rates" :key="rate.id">
                            <md-table-cell>{{readable( rate.rate_cost)}} {{ $store.getters['settings/getMainSettings'].currency }}</md-table-cell>
                            <md-table-cell v-if="editRow === 'rate'+'_'+rate.id">
                                <div style="display: inline-flex;">
                                    <input class="form-control" v-model="rate.remaining" type="text"/>
                                    <div @click="() => {editRow = null}">
                                        <md-icon style="color:red">cancel</md-icon>
                                    </div>
                                </div>
                            </md-table-cell>
                            <md-table-cell v-else>{{readable(rate.remaining)}} {{ $store.getters['settings/getMainSettings'].currency }}</md-table-cell>

                            <md-table-cell>{{rate.due_date}}</md-table-cell>

                            <md-table-cell v-if="editRow === 'rate'+'_'+rate.id">
                                <div @click="showConfirm(rate)">
                                    <md-icon style="color:green">save</md-icon>
                                </div>
                            </md-table-cell>
                            <md-table-cell v-else>
                                <div @click="changeRateAmount(rate.id)">
                                    <md-icon>edit</md-icon>
                                </div>
                            </md-table-cell>
                        </md-table-row>
                    </md-table>

                </div>
                <div class="md-layout-item md-size-100">

                    <h4>History</h4>
                    <div class="col-sm-12" v-for="log in selectedAsset.logs" :key="log.id">
                        <li>
                            {{log.action}} on {{formatReadableDate(log.created_at)}} by
                            <strong>{{log.owner.name}}</strong>
                        </li>
                    </div>
                </div>

            </div>
            <md-dialog-actions>
                <md-button class="md-accent" @click="toggleModal()">{{$tc('words.close')}}}</md-button>
            </md-dialog-actions>
        </md-dialog>
    </div>
</template>

<script>
import { AssetService } from '../../services/AssetService'
import { AssetRateService } from '../../services/AssetRateService'
import { AssetPersonService } from '../../services/AssetPersonService'
import { currency } from '../../mixins/currency'
import ConfirmationBox from '../../shared/ConfirmationBox'
import { EventBus } from '../../shared/eventbus'
import Widget from '../../shared/widget'
import moment from 'moment'

export default {
    name: 'DeferredPayments',
    mixins: [currency],
    components: { ConfirmationBox, Widget },
    props: {
        personId: Number
    },
    mounted () {
        this.getAssetTypesList()
        this.getAssetList()
    },
    data () {
        return {
            subscriber:'person-asset',
            assetService: new AssetService(),
            assetRateService: new AssetRateService(),
            assetPersonService: new AssetPersonService(),
            adminId: this.$store.getters['auth/authenticationService'].authenticateUser.id,
            selectedAsset: null,
            selectedAssetId: null,
            showModal: false,
            editRow: null,
            showNewAsset: false,
            newAsset: {
                id: null,
                cost: 1,
                rate: 1
            },
            headers: [this.$tc('words.name'), this.$tc('words.cost'), this.$tc('words.rate',1)],

        }
    },

    methods: {
        clearForm(){
            this.newAsset.id = null
            this.newAsset.cost = null
            this.newAsset.rate = null
        },
        soldNewAsset(){
            this.showNewAsset = !this.showNewAsset
        },
        toggleModal () {
            this.showModal = !this.showModal
        },
        formatReadableDate (date) {
            return moment(date).format('MMMM Do YYYY, h:mm:ss a')
        },
        changeRateAmount (rate_id) {
            this.editRow = 'rate_' + rate_id
        },
        showDetails (index) {
            this.toggleModal()

            this.selectedAsset = this.assetPersonService.list[index]
        },
        showConfirm (data) {
            EventBus.$emit('show.confirm', data)
        },
        async editRate (data) {
            try {
                await this.assetRateService.editAssetRate(data.id, data.remaining, this.adminId)
                this.editRow = null
                this.alertNotify('success', this.$tc('phrases.ratesCount',2))
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async getAssetTypesList () {
            try {
                await this.assetService.getAssets()
            } catch (e) {
                this.alertNotify('error', e.message)
            }

        },
        async getAssetList () {
            try {
                await this.assetPersonService.getPersonAssets(this.personId)
                EventBus.$emit('widgetContentLoaded',this.subscriber,this.assetPersonService.list.length)
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async saveAsset () {
            let validator = await this.$validator.validateAll()

            if (validator) {
                this.$swal({
                    type: 'question',
                    title: this.$tc('phrases.sellAsset', 0),
                    text: this.$tc('phrases.sellAsset', 2, { cost: this.newAsset.cost }),
                    showCancelButton: true,
                    cancelButtonText: this.$tc('words.cancel'),
                    confirmButtonText: this.$tc('words.sell')
                }).then(async response => {
                    console.log(response)
                    try {
                        let validator = await this.$validator.validateAll()
                        if (validator) {
                            await this.assetPersonService.saveAsset(this.newAsset.id, this.personId, this.newAsset, this.adminId)
                            this.showNewAsset = false
                            this.clearForm()
                            this.alertNotify('success', this.$tc('phrases.sellAsset', 1))
                            await this.getAssetList()
                        }

                    } catch (e) {
                        this.alertNotify('error', e.message)
                    }
                })
            }
        },
        getRate (index, rateCount, cost) {
            if (index === parseInt(rateCount)) {
                return cost - (rateCount - 1) * Math.floor(cost / rateCount)
            } else {
                return Math.floor(cost / rateCount)
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

    },

}
</script>

<style scoped>
    .dialog-place {
        max-width: 100%;
        margin: auto !important;
        overflow-y: scroll;
    }

    .mb {
        margin-bottom: 15px;
        border-bottom: 1px solid #eceaea;
    }

    .edit {
        color: #2c4074;
        /*position: absolute;*/
        left: 88%;
        cursor: pointer;
    }

    .edit:hover {
        color: #5562c5;
        transform: scale(1.5);
    }

    .detail {
        color: #0d746c;
        /*position: absolute;*/
        left: 92%;
        cursor: pointer;
    }

    .detail:hover {
        color: #1e7f99;
        transform: scale(1.5);
    }

    .save {
        color: #1d8922;
        /*position: absolute;*/
        left: 92%;
        cursor: pointer;
    }

    .save:hover {
        color: #8fac25;
        transform: scale(1.5);
    }

    .cancel {
        color: #74150b;
        /*position: absolute;*/
        left: 92%;
        cursor: pointer;
    }

    .cancel:hover {
        color: #a81e10;
        transform: scale(1.5);
    }

    .details-modal-grid {
        padding: 1rem;
    }
</style>
