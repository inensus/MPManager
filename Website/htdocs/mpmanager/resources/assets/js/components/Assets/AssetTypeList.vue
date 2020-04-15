<template>
    <div>

        <AddAssetType :addNewAssetType="addNewAssetType"/>
        <widget
            :title="'Asset Types'"
            :search="true"
            :subscriber="subscriber"
            :route_name="'/assets/types'"
            :button="true"
            button-text="New Asset Type"
            :callback="() => {addNewAssetType = true}"
            :paginator="assetService.paginator"
            color="green">

            <md-table>
                <md-table-row>
                    <md-table-head>
                        ID
                    </md-table-head>
                    <md-table-head>
                        Asset Type
                    </md-table-head>
                    <md-table-head>

                    </md-table-head>
                    <md-table-head>
                        Last Update
                    </md-table-head>
                </md-table-row>


                <md-table-row v-for="(asset_type,index) in assetService.list" style="cursor:pointer;" :key="index">

                    <md-table-cell> {{ asset_type.id}}
                    </md-table-cell>

                    <md-table-cell>
                        <div class="md-layout" v-if="!asset_type.edit">
                            {{ asset_type.name}}&nbsp;&nbsp;
                            <div class="md-layout-item" style="display: inline-block; cursor: pointer; color: #2b542c"
                                 @click="asset_type.edit = true">
                                <font-awesome-icon icon="pen"/>&nbsp;Edit
                            </div>

                        </div>
                        <div class="md-layout-item" v-else>
                            <md-field>

                                <md-input type="text" v-model="asset_type.name"></md-input>
                            </md-field>
                        </div>
                    </md-table-cell>

                    <md-table-cell>
                        <div class="md-layout-item" style="display: inline-block; cursor: pointer; color: #2b542c"
                             v-if="asset_type.edit"
                             @click="updateAssetType(asset_type)">
                            <font-awesome-icon icon="save"/>
                            Save
                        </div>
                        <div class="md-layout-item" v-else
                             style="display: inline-block; cursor: pointer; color:#ac2925; float:right"
                             @click="deleteAssetType(asset_type)">
                            <font-awesome-icon icon="trash"/>
                            Delete
                        </div>
                    </md-table-cell>

                    <md-table-cell class="hidden-xs">{{asset_type.updated_at}}</md-table-cell>


                </md-table-row>
            </md-table>
        </widget>
    </div>

</template>

<script>
    import Widget from '../../shared/widget'
    import AddAssetType from './AddAssetType'
    import {EventBus} from '../../shared/eventbus'
    import {AssetService} from '../../services/AssetService'

    export default {
        name: 'AssetTypeList',
        components: {Widget, AddAssetType},

        data() {
            return {
                addNewAssetType: false,
                subscriber: 'asset-list',
                assetService: new AssetService(),
                assetTypes: [],

            }
        },
        mounted() {
            EventBus.$on('assetTypeAdded', this.addToList)
            EventBus.$on('pageLoaded', this.reloadList)
            EventBus.$on('addAssetTypeClosed', this.closeAddComponent)
        },
        beforeDestroy() {
            EventBus.$off('assetTypeAdded', this.addToList)
            EventBus.$off('pageLoaded', this.reloadList)

        },
        methods: {
            reloadList(subscriber, data) {

                if (subscriber !== this.subscriber) return
                this.assetService.updateList(data)

            },
            addToList(asset_type) {
                let asset_t = {
                        id: asset_type.id,
                        name: asset_type.name,
                        edit: false,
                        asset_type_name: asset_type.name

                    }
                ;
                this.assetService.list.push(asset_t)
            },

            updateAssetType(asset_type) {
                asset_type.edit = false;

                this.assetService.updateAsset(asset_type)
                    .then((response) => {
                        this.alertNotify('success', asset_type.name + ' has updated.')

                    }).catch(e => {
                    this.alertNotify('error', e.message)
                })
            },

            deleteAssetType(asset_type) {

                this.assetService.deleteAsset(asset_type)
                    .then((response) => {
                        this.alertNotify('success', asset_type.name + ' has deleted.')
                        location.reload()
                    }).catch(e => {
                    this.alertNotify('error', e.message)
                })

            },

            closeAddComponent(data) {

                this.addNewAssetType = data
            },
            alertNotify(type, message) {
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
