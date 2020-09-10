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
                            <div v-if="item.accessRate">
                                {{ readable(item.accessRate.amount) }} {{item.currency}}
                            </div>
                            <div v-else>-</div>
                        </md-table-cell>
                        <md-table-cell md-label="Access Rate Period in Days"
                                       md-numeric
                        >
                            <div v-if="item.accessRate">
                                {{ item.accessRate.period }} Days
                            </div>
                            <div v-else>-</div>

                        </md-table-cell>
                    </md-table-row>
                </md-table>
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
            headers: ['ID', 'Name', 'kWh Price', 'Access Rate', 'Access Rate Period in Days'],
            tableName: 'Tariff'
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
            console.log('new tariff', tariff)
            this.tariffList = this.tariffService.addToList(tariff)

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

