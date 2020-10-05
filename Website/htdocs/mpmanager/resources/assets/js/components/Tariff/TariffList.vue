<template>
    <div>

        <!-- new tariff component -->
        <add/>

        <widget id="tariff-list"
                title="Tariffs"
                :button="true"
                :subscriber="subscriber"
                buttonText="New Tariff"
                @widgetAction="showNewTariff"
                color="green">

                <md-table
                    v-model="tariffService.list"
                    md-sort="id"
                    md-sort-order="asc"
                    md-card>

                    <md-table-row slot="md-table-row" slot-scope="{ item }">
                        <md-table-cell md-label="ID" md-sort-by="id" md-numeric>{{ item.id }}</md-table-cell>
                        <md-table-cell md-label="Name" md-sort-by="name">{{ item.name }}</md-table-cell>
                        <md-table-cell md-label="kWh Price" md-numeric>
                            {{ readable(item.price/100)}} {{item.currency}}
                        </md-table-cell>
                        <md-table-cell md-label="Access Rate" md-numeric md-sort-by="accessRate.amount">
                            <div v-if="item.accessRate">
                                {{ readable(item.accessRate.amount) }} {{item.currency}}
                            </div>
                            <div v-else>-</div>
                        </md-table-cell>
                        <md-table-cell md-label="Access Rate Period in Days"
                                       md-numeric
                        >
                            <div v-if="item.accessRate.period">
                                {{ item.accessRate.period }} Days
                            </div>
                            <div v-else>-</div>

                        </md-table-cell>
                        <md-table-cell md-label="#">
                            <md-button class="md-icon-button" @click="editTariff(item.id)">
                                <md-tooltip md-direction="top">Edit</md-tooltip>
                                <md-icon>edit</md-icon>
                            </md-button>
                            <md-button class="md-icon-button" @click="showConfirmation(item.id)">
                                <md-tooltip md-direction="top">Delete</md-tooltip>
                                <md-icon>delete</md-icon>
                            </md-button>
                        </md-table-cell>
                    </md-table-row>
                </md-table>
                <md-progress-bar md-mode="indeterminate" v-if="loading"/>

        </widget>
    </div>
</template>

<script>
import Widget from '../../shared/widget'
import { currency } from '../../mixins/currency'
import Add from './Add'
import { EventBus } from '../../shared/eventbus'
import { TariffService } from '../../services/TariffService'

export default {
    name: 'TariffList',
    components: { Widget, Add },
    mixins: [currency],
    data () {
        return {
            subscriber:'tariff-list',
            tariffService: new TariffService(),
            tariffList: [],
            headers: ['ID', 'Name', 'kWh Price', 'Access Rate', 'Access Rate Period in Days','#'],
            tableName: 'Tariff',
            loading:false
        }
    },
    mounted () {

        this.getTariffs()
        EventBus.$on('tariffAdded', ()=>{
            this.getTariffs()
        })
    },
    methods: {

        async getTariffs () {
            try {
                await this.tariffService.getTariffs()
                EventBus.$emit('widgetContentLoaded',this.subscriber,this.tariffService.list.length)
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        showNewTariff () {
            EventBus.$emit('showNewTariff')
        },
        addToList (tariff) {
            this.tariffService.addToList(tariff)
        },
        editTariff(id){
            this.$router.push({ path: '/tariffs/' + id })
        },
        async  deleteTariff(id){
            try {
                this.loading = true
                await this.tariffService.deleteTariff(id)
                this.loading = false
                this.alertNotify('success', 'Tariff deleted successfully.')
                await  this.getTariffs()
            } catch (e) {
                this.loading = false
                this.alertNotify('error', e.message)
            }
        },
        async changeUsingMeterTariff(currentId,changeId){
            try {
                this.loading = true
                await this.tariffService.changeMetersTariff(currentId,changeId)
                this.loading = false
                this.alertNotify('success', 'Tariff changed on using meters successfully.')
            } catch (e) {

                this.loading = false
                this.alertNotify('error', e.message)
            }
        },
        async showConfirmation (id) {
            let countObject = await this.tariffService.tariffUsageCount(id)
            let usageCount =countObject[0].count
            let tariffs=this.tariffService.list
            let tariffObj=  tariffs.reduce((acc,value)=>{
                if (value.id !== id){
                    acc[value.id] = value.name
                }
                return acc
            },{})
            let swalOptions={
                type: 'question',
                title: 'Delete',
                showCancelButton: true,
                confirmButtonText: 'I\'m sure',
                cancelButtonText: 'Cancel',
            }
            if(usageCount>0){
                swalOptions.input= 'select'
                swalOptions.inputOptions= tariffObj
                swalOptions.text= 'This tariff has using by '+ usageCount +' of meters.You have to decide change meters  tariffs. Are you sure delete this tariff? '
            }else{
                swalOptions.text= 'Are you sure delete this tariff?'
            }
            this.$swal(
                swalOptions
            ).then((result) => {
                if (result.value) {
                    // eslint-disable-next-line no-constant-condition
                    if (typeof result.value == 'string'){
                        this.changeUsingMeterTariff(id,Number(result.value))
                    }
                    this.deleteTariff(id)
                }
            })
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

</style>

