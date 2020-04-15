<template>
    <div>
        <widget
            v-if="addNewAssetType"
            title="Add New Asset Type">
            <md-card>
                <md-card-content>
                    <md-field :class="{'md-invalid': errors.has('asset')}">
                        <label>Asset Type Name</label>
                        <md-input v-model="asset.name"
                                  placeholder="Asset Type Name"
                                  type="text"
                                  name="asset"
                                  id="asset"
                                  v-validate="'required|min:4'"
                        ></md-input>
                        <span class="md-error">{{ errors.first('asset') }}</span>
                    </md-field>
                </md-card-content>
                <md-card-actions>
                    <md-button class="md-raised md-primary" @click="saveAsset()">
                        <md-icon>save</md-icon>
                        Save
                    </md-button>
                    <md-button class="md-raised md-accent" @click="closeAddComponent()">
                        <md-icon>cancel</md-icon>
                        Close
                    </md-button>
                </md-card-actions>
            </md-card>
        </widget>

    </div>

</template>
<script>
    import Widget from '../../shared/widget'
    import {AssetService} from '../../services/AssetService'
    import {EventBus} from "../../shared/eventbus";

    export default {
        name: 'AddAssetType',
        components: {Widget},
        props: {
            addNewAssetType: false,
        },
        data() {
            return {
                assetService: new AssetService(),
                asset: null,


            }
        },
        created() {
            this.asset = this.assetService.asset;
        },
        mounted() {

        },
        methods: {
            async saveAsset() {
                let validation = await this.$validator.validateAll();
                if (!validation) {
                    return
                }
                this.assetService.createAsset().then((response) => {
                    this.alertNotify('success', 'AssetType has registered.')
                }).catch((e) => {
                    this.alertNotify('error', e.message)
                });
                this.closeAddComponent();
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
