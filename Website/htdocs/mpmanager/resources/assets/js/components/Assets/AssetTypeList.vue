<template>
    <div>
        <add-asset-type :addNewAssetType="addNewAssetType"/>
        <widget
            :title="'Asset Types'"
            :subscriber="subscriber"
            :route_name="'/assets/types'"
            :button="true"
            button-text="New Asset Type"
            @widgetAction="showAddAssetType"
            :paginator="assetService.paginator"
            color="green"
            :reset-key="resetKey">
                <md-table>
                    <md-table-row>
                        <md-table-head  v-for="(item, index) in headers" :key="index">{{item}}</md-table-head>
                    </md-table-row>

                <md-table-row v-for="(asset_type,index) in assetService.list" style="cursor:pointer;" :key="index">

                    <md-table-cell> {{ asset_type.id}}
                    </md-table-cell>

                    <md-table-cell>
                        <div class="md-layout" v-if="!asset_type.edit">
                            {{ asset_type.name}}&nbsp;&nbsp;
                        </div>
                        <div class="md-layout-item" v-else>
                            <md-field>
                                <md-input type="text" v-model="asset_type.name"></md-input>
                            </md-field>
                        </div>
                    </md-table-cell>

                    <md-table-cell>
                        {{asset_type.price}} {{appConfig.currency}}
                    </md-table-cell>

                    <md-table-cell class="hidden-xs">{{asset_type.updated_at}}</md-table-cell>
                    <md-table-cell>
                        <div class="md-layout-item" style="display: inline-block; cursor: pointer; color: #2b542c"
                             v-if="asset_type.edit"
                             @click="updateAssetType(asset_type)">
                            <md-icon>save</md-icon>
                            Save
                        </div>
                        <div class="md-layout-item" v-else :disabled="loading"
                             style="display: inline-block; cursor: pointer; color:#ac2925; float:right"
                             @click="deleteAssetType(asset_type)">
                            <md-icon>delete</md-icon>
                            Delete
                        </div>
                    </md-table-cell>

                </md-table-row>

                <md-progress-bar md-mode="indeterminate" v-if="loading"/>
                </md-table>
        </widget>
    </div>

</template>

<script>
import Widget from '../../shared/widget'
import AddAssetType from './AddAssetType'
import { EventBus } from '../../shared/eventbus'
import { AssetService } from '../../services/AssetService'
export default {
    name: 'AssetTypeList',
    components: { Widget, AddAssetType },

    data () {
        return {
            addNewAssetType: false,
            subscriber: 'asset-list',
            assetService: new AssetService(),
            assetTypes: [],
            headers: ['ID', 'Asset Type', 'Price', 'Last Update', '#'],
            tableName: 'Asset Type',
            resetKey: 0,
            loading: false,
        }
    },
    mounted () {
        EventBus.$on('assetTypeAdded', () => {
            this.resetKey++
        })
        EventBus.$on('pageLoaded', this.reloadList)
        EventBus.$on('addAssetTypeClosed', this.closeAddComponent)

    },
    beforeDestroy () {
        EventBus.$off('assetTypeAdded', this.addToList)
        EventBus.$off('pageLoaded', this.reloadList)

    },
    methods: {
        showAddAssetType(){
            this.addNewAssetType = true
        },
        reloadList (subscriber, data) {
            if (subscriber !== this.subscriber) {
                return
            }
            this.assetService.updateList(data)
            EventBus.$emit('widgetContentLoaded',this.subscriber, this.assetService.list.length)

        },
        addToList (asset_type) {
            let asset_t = {
                id: asset_type.id,
                name: asset_type.name,
                edit: false,
                asset_type_name: asset_type.name
            }
            this.assetService.list.push(asset_t)
        },

        async updateAssetType (asset_type) {
            asset_type.edit = false
            try {
                await this.assetService.updateAsset(asset_type)
                this.alertNotify('success', asset_type.name + ' has updated.')
                this.alertNotify('success', asset_type.name + ' has deleted.')
                this.resetKey++
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },

        async deleteAssetType (asset_type) {
            try {
                this.loading = true
                await this.assetService.deleteAsset(asset_type)
                this.loading = false
                this.alertNotify('success', asset_type.name + ' has deleted.')
                this.resetKey++
            } catch (e) {
                this.loading = false
                this.alertNotify('error', e.message)
            }
        },

        closeAddComponent (data) {

            this.addNewAssetType = data
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
