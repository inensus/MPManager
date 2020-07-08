<template>
    <div>
        <div class="md-layout md-gutter ">
            <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25 small-size-style">
                <box
                    :centerText="true"
                    :color="[ '#26c6da','#00acc1']"
                    :subText="clusters.length.toString()"
                    :headerTextColor="'#dddddd'"
                    :headerText="'Registered Clusters'"
                    :subTextColor="'#e3e3e3'"
                    :boxIcon="'sitemap'"
                    :boxIconColor="'#578839'"

                />
            </div>
            <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25 small-size-style">

                <box
                    :centerText="true"
                    :color="[ '#ffa726','#fb8c00']"
                    :subText="readable(population).toString()"
                    :headerTextColor="'#dddddd'"
                    headerText="Registered Customers"
                    :subTextColor="'#e3e3e3'"
                    :boxIcon="'user'"
                    :boxIconColor="'#385a76'"

                />
            </div>
            <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25 small-size-style">
                <box
                    :centerText="true"
                    :color="[ '#ef5350','#e53935']"
                    :subText="readable(connections).toString()"
                    :headerTextColor="'#dddddd'"
                    header-text="Connected Meters"
                    :subTextColor="'#e3e3e3'"
                    boxIcon="plug"
                    :boxIconColor="'#604058'"
                />
            </div>
            <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25 small-size-style">
                <box
                    :centerText="true"
                    :color="[ '#6eaa44','#578839']"
                    :sub-text="readable(revenue).toString() + appConfig.currency "
                    :headerTextColor="'#dddddd'"
                    header-text="Revenue (last 30 days)"
                    :subTextColor="'#e3e3e3'"
                    :boxIcon="'money-bill'"
                    :boxIconColor="'#5c5837'"
                />
            </div>


            <md-speed-dial md-direction="bottom" style="position: fixed; right: 1rem; bottom: 1rem; z-index: 999">
                <md-speed-dial-target @click="newCluster">
                    <md-tooltip md-direction="top">Add a new Cluster</md-tooltip>
                    <md-icon>add</md-icon>
                </md-speed-dial-target>

            </md-speed-dial>

        </div>

    </div>
</template>

<script>
    import Box from '../Box'
    import ChartistBox from '../ChartistBox'
    import {currency} from '../../mixins/currency'

    export default {
        name: 'BoxGroup',
        components: {Box, ChartistBox},
        mixins: [currency],
        props: {
            clusters: {
                type: Array,
                required: true,
            }
        },
        computed: {
            population() {
                let population = 0
                for (let c in this.clusters) {
                    for (let city in this.clusters[c].cities) {
                        population += this.clusters[c].cities[city].population
                    }
                }
                return population
            },
            connections() {
                let connections = 0
                for (let c in this.clusters) {
                    for (let city in this.clusters[c].cities) {
                        connections += this.clusters[c].cities[city].metersCount
                    }
                }
                return connections
            },
            revenue() {
                let revenue = 0
                for (let c in this.clusters) {
                    for (let city in this.clusters[c].cities) {
                        revenue += this.clusters[c].cities[city].revenue
                    }
                }
                return revenue
            },
        },
        methods: {
            newCluster() {
                this.$router.push('/clusters/add')
            }
        }
    }
</script>

<style>
    .box-group {
        display: flex;
        margin-top: 1rem;
    }

    .btn-log {
        background-color: #689f38 !important;

        color: white !important;
        width: 100%;
    }

</style>
