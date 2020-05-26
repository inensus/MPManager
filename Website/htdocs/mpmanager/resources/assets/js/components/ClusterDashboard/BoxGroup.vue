<template>

    <div class="md-layout md-gutter">

        <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
            <box
                :center-text="true"
                :color="[ '#26c6da','#00acc1']"
                header-text="Mini-Grids"
                :header-text-color="'#dddddd'"
                :sub-text="this.miniGrids.mini_grids.length.toString()"
                :sub-text-color="'#e3e3e3'"
                :box-icon="'sitemap'"
                :box-icon-color="'#578839'"
            />
        </div>
        <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
            <box
                v-if="'people' in boxData"

                :center-text="true"
                :color="[ '#ffa726','#fb8c00']"
                :sub-text="boxData['people'].toString()"
                :header-text-color="'#dddddd'"
                header-text="People"
                :sub-text-color="'#e3e3e3'"
                :box-icon="'user'"
                :box-icon-color="'#385a76'"

            />
        </div>
        <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
            <box
                v-if="'meters' in boxData"

                :center-text="true"
                :color="[ '#ef5350','#e53935']"
                :sub-text="boxData['meters'].toString()"
                :header-text-color="'#dddddd'"
                header-text="Connected Meters"
                :sub-text-color="'#e3e3e3'"
                box-icon="plug"
                :box-icon-color="'#604058'"
            />
        </div>
        <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
            <box
                v-if=" 'revenue' in boxData"

                :center-text="true"
                :color="[ '#6eaa44','#578839']"
                :sub-text="readable(boxData['revenue']['total'])"
                :header-text-color="'#dddddd'"
                :header-text="'Revenue' +  boxData['revenue']['period'] "
                :sub-text-color="'#e3e3e3'"
                :box-icon="'money-bill'"
                :box-icon-color="'#5c5837'"
            />
        </div>
    </div>

</template>

<script>
    import Box from '../Box'
    import {currency} from '../../mixins/currency'

    export default {
        name: 'BoxGroup',
        components: {Box},
        mixins: [currency],
        props: {
            miniGrids: {
                type: Object,
                required: true,
            },
            boxData: {
                type: Object,
                required: true,
            }
        },
        computed: {
            population() {
                return 0
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
                return connections
                for (let c in this.clusters) {
                    for (let city in this.clusters[c].cities) {
                        connections += this.clusters[c].cities[city].metersCount
                    }
                }
                return connections
            },
            revenue() {
                let revenue = 0
                return revenue
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

</style>
