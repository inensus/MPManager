<template>
    <div>
        <div v-if="clusters">
            <div class="row" style="margin-top: 30px">
                <!-- Holds registered clusters, registered meters etc. -->
                <box-group :clusters="clusters"/>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <financial-overview></financial-overview>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <cluster-map></cluster-map>
                </div>

            </div>
        </div>
        <div v-else>
            <div style="margin-left: 40vw; margin-top: 20vh;">
                <img src="https://loading.io/spinners/curve-bars/lg.curved-bar-spinner.gif" alt="">
            </div>
        </div>
    </div>

</template>

<script>
    import { EventBus } from '../../shared/eventbus'
    import '../../shared/TableList'

    import Widget from '../../shared/widget'
    import BoxGroup from './BoxGroup'
    import FinancialOverview from './FinancialOverview'
    import { resources } from '../../resources'
    import ClusterMap from './ClusterMap'

    export default {
        name: 'ClusterList',

        components: { ClusterMap, FinancialOverview, BoxGroup, Widget },
        data () {
            return {
                bcd: {
                    'Home': {
                        'href': '/'
                    },
                    'Clusters': {
                        'href': null,
                    },
                },
                clusters: null,

            }
        },
        mounted () {
            EventBus.$emit('bread', this.bcd)
            this.getClusterList()

        },
        methods: {
            getClusterList () {
                axios.get(resources.clusters.list).then((response) => {
                    this.clusters = response.data.data
                })
            },

        },

    }
</script>

<style>

</style>
