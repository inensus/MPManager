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
                    <cluster-map/>
                </div>

            </div>
        </div>
        <div v-else>
            <div style="margin-left: 40vw; margin-top: 20vh;">
                <img width="48px" src="../../../spinner/spinner.gif" alt="">
            </div>
        </div>

    </div>
</template>

<script>
import '../../shared/TableList'
import BoxGroup from './BoxGroup'
import FinancialOverview from './FinancialOverview'

import ClusterMap from './ClusterMap'
import { ClusterService } from '../../services/ClusterService'

export default {
    name: 'ClusterList',
    components: { ClusterMap, FinancialOverview, BoxGroup },
    data () {
        return {
            clusterService: new ClusterService(),
            clusters: null,
        }
    },
    mounted () {
        this.getClusterList()
    },
    methods: {
        async getClusterList () {
            this.clusters = await this.clusterService.getClusters()
        },
    },
}
</script>

