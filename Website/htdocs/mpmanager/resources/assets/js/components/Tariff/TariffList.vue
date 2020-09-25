<template>
    <div>

        <!-- new tariff component -->
        <add/>

        <widget id="tariff-list"
                title="Tariffs"
                :button="true"
                buttonText="New Tariff"
                :callback="showNewTariff"
                color="red">
            <div v-if="tariffList.length>0">
                <md-table
                    v-model="tariffList"
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

                            <div v-if="item.accessRate.amount|| item.accessRate.amount===0">
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
                            <md-button class="md-icon-button" @click="showConfirmation(item.id,'Update')">
                                <md-tooltip md-direction="top">Edit</md-tooltip>
                                <md-icon>edit</md-icon>
                            </md-button>
                            <md-button class="md-icon-button" @click="showConfirmation(item.id,'Delete')">
                                <md-tooltip md-direction="top">Delete</md-tooltip>
                                <md-icon>delete</md-icon>
                            </md-button>
                        </md-table-cell>
                    </md-table-row>
                </md-table>
                <md-progress-bar md-mode="indeterminate" v-if="loading"/>
            </div>

            <div v-else>
                <no-table-data :headers="headers" :tableName="tableName"/>
            </div>
        </widget>


    </div>
</template>

<script>
import Widget from '../../shared/widget'
import { currency } from '../../mixins/currency'
import Add from './Add'
import { EventBus } from '../../shared/eventbus'
import { TariffService } from '../../services/TariffService'
import NoTableData from '../../shared/NoTableData'

export default {
    name: 'TariffList',
    components: { Widget, Add, NoTableData },
    mixins: [currency],
    data () {
        return {
            tariffService: new TariffService(),
            tariffList: [],
            headers: ['ID', 'Name', 'kWh Price', 'Access Rate', 'Access Rate Period in Days','#'],
            tableName: 'Tariff',
            loading:false
        }
    },
    mounted () {

        this.getTariffs()
        EventBus.$on('tariffAdded', this.addToList)
    },
    methods: {

        async getTariffs () {
            try {
                this.tariffList = await this.tariffService.getTariffs()
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        showNewTariff () {
            EventBus.$emit('showNewTariff')
        },
        addToList (tariff) {
            this.tariffList = this.tariffService.addToList(tariff)

        },
        editTariff(id){
            this.$router.push({ path: '/tariffs/' + id })
        },
        async  deleteTariff(id){
            try {
                this.loading = true
                await this.tariffService.deleteTariff(id)
                await  this.getTariffs()
                this.loading = false
                this.alertNotify('success', 'Tariff deleted successfully.')
                await this.tariffService.getTariffs()
            } catch (e) {

                this.loading = false
                this.alertNotify('error', e.message)
            }
        },

        async showConfirmation (id,type) {
            let countObject = await this.tariffService.tariffUsageCount(id)
            let usageCount =countObject[0].count
            let text=''
            if(usageCount>0){
                text = 'This tariff has using by '+ usageCount +' of meters. Are you sure '+type+' this tariff?'
            }else{
                text = 'Are you sure '+type+' this tariff?'
            }
            this.$swal({
                type: 'question',
                title: type,
                text: text ,
                showCancelButton: true,
                confirmButtonText: 'I\'m sure',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.value) {
                    if (type==='Update'){
                        this.editTariff(id)
                    }else{
                        this.deleteTariff(id)
                    }
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

