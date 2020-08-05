<template>
    <div>
        <widget
            v-if="addNewAssetType"
            title="Add New Asset Type">
            <md-card>
                <md-card-content>
                    <md-field :class="{'md-invalid': errors.has('asset')}">
                        <label>Asset Type Name</label>
                        <md-input v-model="assetService.asset.name"
                                  placeholder="Asset Type Name"
                                  type="text"
                                  name="asset"
                                  id="asset"
                                  v-validate="'required|min:4'"
                        ></md-input>
                        <span class="md-error">{{ errors.first('asset') }}</span>
                    </md-field>

                    <md-field :class="{'md-invalid': errors.has('asset_price')}">
                        <label>Asset Price</label>
                        <md-input v-model="assetService.asset.price"
                                  placeholder="Asset Price"
                                  type="text"
                                  name="asset_price"
                                  id="asset_price"
                                  v-validate="'required|numeric'"
                        ></md-input>
                        <span class="md-error">{{ errors.first('asset_price') }}</span>
                    </md-field>
                </md-card-content>
                <md-card-actions>
                    <md-button class="md-raised md-primary" @click="saveAsset()">
                        Save
                    </md-button>
                    <md-button class="md-raised " @click="closeAddComponent()">
                        Close
                    </md-button>
                </md-card-actions>
            </md-card>
        </widget>

    </div>

</template>
<script>
    import Widget from '../../shared/widget'
    import { AssetService } from '../../services/AssetService'
    import { EventBus } from '../../shared/eventbus'

    export default {
        name: 'AddAssetType',
        components: { Widget },
        props: {
            addNewAssetType: false,
        },
        data () {
            return {
                assetService: new AssetService(),
            }
        },
        created () {
            this.asset = this.assetService.asset
        },
        mounted () {

        },
        methods: {
            async saveAsset () {
                let validation = await this.$validator.validateAll()
                if (!validation) {
                    return
                }

                try {
                    await this.assetService.createAsset()
                    this.alertNotify('success', 'AssetType has registered.')
                } catch (e) {
                    this.alertNotify('error', e.message)
                }

                this.closeAddComponent()
            },

            closeAddComponent() {
                EventBus.$emit('addAssetTypeClosed', false);
            },
            alertNotify(type, message) {
                this.$notify({
                    group: "notify",
                    type: type,
                    title: type + " !",
                    text: message
                });
            },

        }
    }
</script>
