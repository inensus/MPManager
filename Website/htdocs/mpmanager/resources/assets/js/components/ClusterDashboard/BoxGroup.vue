<template>

    <div class="md-layout md-gutter">

        <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25 small-size-style">
            <box
                :center-text="true"
                :color="[ '#26c6da','#00acc1']"
                header-text="Mini-Grids"
                :header-text-color="'#dddddd'"
                :sub-text="cluster.mini_grids.length.toString()"
                :sub-text-color="'#e3e3e3'"
                :box-icon="'map'"
                :box-icon-color="'#578839'"
            />
        </div>
        <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25 small-size-style">
            <box
                v-if="'population' in clusterData"

                :center-text="true"
                :color="[ '#ffa726','#fb8c00']"
                :sub-text="clusterData['population'].toString()"
                :header-text-color="'#dddddd'"
                header-text="People"
                :sub-text-color="'#e3e3e3'"
                :box-icon="'supervisor_account'"
                :box-icon-color="'#385a76'"

            />
        </div>
        <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25 small-size-style">
            <box
                v-if="'meterCount' in clusterData"

                :center-text="true"
                :color="[ '#ef5350','#e53935']"
                :sub-text="clusterData['meterCount'].toString()"
                :header-text-color="'#dddddd'"
                header-text="Connected Meters"
                :sub-text-color="'#e3e3e3'"
                box-icon="plug"
                :box-icon-color="'#604058'"
            />
        </div>
        <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25 small-size-style">
            <box
                v-if=" 'revenue' in clusterData"
                :center-text="true"
                :color="[ '#6eaa44','#578839']"
                :sub-text="readable(clusterData['revenue']) + appConfig.currency"
                :header-text-color="'#dddddd'"
                :header-text="'Revenue last 30 days' "
                :sub-text-color="'#e3e3e3'"
                :box-icon="'money-bill'"
                :box-icon-color="'#5c5837'"
            />
        </div>
    </div>

</template>

<script>
import Box from '../Box'
import { currency } from '../../mixins/currency'
import { ClusterService } from '../../services/ClusterService'

export default {
    name: 'BoxGroup',
    components: { Box },
    mixins: [currency],
    props: {
        cluster: {
            type: Object,
            required: true,
        },

    },
    mounted () {
        this.getClusterData()
    },
    data: () => ({
        boxData: [],
        clusterService: new ClusterService(),
        clusterData: [],
    }),
    methods: {
        async getClusterData () {
            this.clusterData = await this.clusterService.getClusterRevenues(this.cluster.id, 'monthly')

        },
    },
    computed: {
        population () {
            return 0
            /*let population = 0
            for (let c in this.clusters) {
                for (let city in this.clusters[c].cities) {
                    population += this.clusters[c].cities[city].population
                }
            }
            return population*/
        },
        connections() {
            let connections = 0
            return connections
            /*for (let c in this.clusters) {
                for (let city in this.clusters[c].cities) {
                    connections += this.clusters[c].cities[city].metersCount
                }
            }
            return connections*/
        },
        revenue() {
            let revenue = 0
            return revenue
            /*for (let c in this.clusters) {
                for (let city in this.clusters[c].cities) {
                    revenue += this.clusters[c].cities[city].revenue
                }
            }
            return revenue*/
        },
    },

}
</script>

<style>
    @media screen and (max-width: 1280px) {
        .small-size-style{
            margin-bottom: 1rem !important;
            min-height: unset;
        }
    }

</style>
