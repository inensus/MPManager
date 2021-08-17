<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-size-40">
            <client-detail-card :person-id="personId" :show-customer-information="false" v-if="personId" />
            <sold-appliances-list :sold-appliances-list="soldAppliancesList" :person-id="personId" />
        </div>
        <div class="md-layout-item md-size-60">
            <widget
                :title="'Details of ' + soldAppliance.asset_type.name "
                color="green">
                <confirmation-box :title="$tc('phrases.editRate')" @confirmed="editRate"></confirmation-box>
                <div class="md-layout md-gutter dialog-place">
                    <div class="md-layout-item md-layout md-gutter md-size-100 " style="padding: 2vw">
                        <div class="md-layout-item md-size-50">
                            <h3><b>{{$tc('phrases.totalCost') }}: </b> {{moneyFormat(soldAppliance.total_cost) + currency}} </h3>
                            <h3><b>Down Payment:</b> {{moneyFormat(soldAppliance.down_payment) + ' ' + currency}}</h3>
                        </div>
                        <div class="md-layout-item md-size-50">
                            <h3><b>{{$tc('phrases.soldDate') }}: </b> {{formatReadableDate(soldAppliance.created_at)}}</h3>
                            <h3><b>{{$tc('phrases.ratesCount') }}: </b> {{soldAppliance.rate_count}}</h3>
                        </div>
                    </div>
                    <div class="md-layout-item md-size-100">
                        <md-table v-if="soldAppliance.rate_count > 0">
                            <md-table-toolbar>
                                <h1 class="md-title">Payment Plan</h1>
                            </md-table-toolbar>
                            <md-table-row>
                                <md-table-head>ID</md-table-head>
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
                            <md-table-row v-for="(rate,index) in getAppliance()" :key="rate.id">
                                <md-table-cell>{{index +1 }}</md-table-cell>
                                <md-table-cell>{{moneyFormat( rate.rate_cost)}} {{ currency }}</md-table-cell>
                                <md-table-cell v-if="editRow === 'rate'+'_'+rate.id">
                                    <div style="display: inline-flex;">
                                        <input class="form-control" v-model="rate.remaining" type="text"/>
                                        <div @click="() => {editRow = null}">
                                            <md-icon style="color:red">cancel</md-icon>
                                        </div>
                                    </div>
                                </md-table-cell>
                                <md-table-cell v-else>{{moneyFormat(rate.remaining)}} {{ currency }}</md-table-cell>

                                <md-table-cell>{{formatReadableDate(rate.due_date)}}</md-table-cell>

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
                        <div v-else>
                        <span class="md-subheader md-layout-item">
                           <h1> <md-icon>price_check</md-icon>Fully paid.</h1>
                        </span>
                        </div>

                    </div>
                    <div class="md-layout-item md-size-100"  v-if="soldAppliance.logs.length > 0">
                        <md-table>
                            <md-table-toolbar>
                                <h1 class="md-title">History</h1>
                            </md-table-toolbar>
                            <md-table-row>
                                <md-table-cell>#</md-table-cell>
                                <md-table-cell>Log</md-table-cell>
                                <md-table-cell>Date</md-table-cell>
                                <md-table-cell>Initiator</md-table-cell>
                            </md-table-row>
                            <md-table-row v-for="(log, index) in soldAppliance.logs" :key="log.id">
                                <md-table-cell>{{index + 1}}</md-table-cell>
                                <md-table-cell>{{log.action}}</md-table-cell>
                                <md-table-cell>{{formatReadableDate(log.created_at)}} </md-table-cell>
                                <md-table-cell>{{log.owner.name}}</md-table-cell>
                            </md-table-row>
                        </md-table>
                    </div>


                </div>

            </widget>
        </div>

    </div>
</template>

<script>
import ConfirmationBox from '../../../shared/ConfirmationBox'
import ClientDetailCard from '../../../shared/ClientDetailCard'
import SoldAppliancesList from './SoldAppliancesList'
import { AssetPersonService } from '../../../services/AssetPersonService'
import { PersonService } from '../../../services/PersonService'
import Widget from '../../../shared/widget'
import { currency} from '../../../mixins/currency'
import { AssetRateService } from '../../../services/AssetRateService'
import moment from 'moment'
import { EventBus } from '../../../shared/eventbus'
export default {
    name: 'SoldApplianceDetail',
    components:{ Widget, SoldAppliancesList, ClientDetailCard, ConfirmationBox},
    mixins: [currency],
    data (){
        return{
            assetRateService: new AssetRateService(),
            assetPersonService: new AssetPersonService(),
            personService: new PersonService(),
            soldAppliance: {
                asset_type: {
                    name:''
                },
                logs:[]
            },
            adminId: this.$store.getters['auth/authenticationService'].authenticateUser.id,
            personId: null,
            editRow: null,
            soldAppliancesList:[],
            subscriber: 'sold-appliance-detail',
            currency: this.$store.getters['settings/getMainSettings'].currency
        }
    },
    watch:{
        $route()  {
            this.selectedApplianceId = this.$route.params.id
            this.getSoldApplianceDetail()
        }
    },
    created () {
        this.selectedApplianceId = this.$route.params.id
        this.getSoldApplianceDetail().then(personId => {
            this.getPersonSoldAppliances(personId)
        })

    },
    methods:{
        getAppliance(){
            if(this.soldAppliance.down_payment > 0){
                return this.soldAppliance.rates.slice(1)
            }else{
                return this.soldAppliance.rates
            }
        },
        showConfirm (data) {
            EventBus.$emit('show.confirm', data)
        },
        formatReadableDate (date) {
            return moment(date).format('LL')
        },
        changeRateAmount (rate_id) {
            this.editRow = 'rate_' + rate_id
        },
        async editRate (data) {
            try {
                await this.assetRateService.editAssetRate(data.id, data.remaining, this.adminId)
                this.editRow = null
                this.alertNotify('success', this.$tc('phrases.ratesCount',2))
                await this.getSoldApplianceDetail()
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async getSoldApplianceDetail(){
            try {
                this.soldAppliance =  await this.assetPersonService.show(this.selectedApplianceId)
                this.personId = this.soldAppliance.person_id
                return this.personId
            }catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async getPersonSoldAppliances(){
            try {
                this.soldAppliancesList = await this.assetPersonService.getPersonAssets(this.personId)
            } catch (e) {
                this.alertNotify('error', e.message)
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

</style>
