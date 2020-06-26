<template>
    <div>
        <widget
            v-if="addNewApplianceType"
            title="Add New Appliance Type">
            <md-card>
                <md-card-content>
                    <md-field :class="{'md-invalid': errors.has('appliance')}">
                        <label>Appliance Type Name</label>
                        <md-input v-model="appliance.name"
                                  placeholder="Appliance Type Name"
                                  type="text"
                                  name="appliance"
                                  id="appliance"
                                  v-validate="'required|min:4'"
                        ></md-input>
                        <span class="md-error">{{ errors.first('appliance') }}</span>
                    </md-field>
                </md-card-content>
                <md-card-actions>
                    <md-button class="md-raised md-primary" @click="saveAppliance()">
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
    import {ApplianceService} from '../../services/ApplianceService'
    import {EventBus} from "../../shared/eventbus";

    export default {
        name: 'AddApplianceType',
        components: {Widget},
        props: {
            addNewApplianceType: false,
        },
        data() {
            return {
                applianceService: new ApplianceService(),
                appliance: null,


            }
        },
        created() {
            this.appliance = this.applianceService.appliance;
        },
        mounted() {

        },
        methods: {
            async saveAppliance() {
                let validation = await this.$validator.validateAll();
                if (!validation) {
                    return
                }
                this.applianceService.createAppliance().then((response) => {
                    this.alertNotify('success', 'ApplianceType has registered.')
                }).catch((e) => {
                    this.alertNotify('error', e.message)
                });
                this.closeAddComponent();
            },

            closeAddComponent() {
                EventBus.$emit('addApplianceTypeClosed', false);
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
