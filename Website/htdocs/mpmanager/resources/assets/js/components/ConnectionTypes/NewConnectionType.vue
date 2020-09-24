<template>
    <widget
        v-if="showAdd"
        :id="'new-connection-type'"
        :title="'Add New Connection Type'"
        color="red"
        :show-refresh-button="false">


        <md-card>
            <md-card-header>
                Name of the Connection
            </md-card-header>
            <md-card-content>
                <div class="md-layout md-gutter">
                    <div class="md-layout-item">
                        <md-field :class="{'md-invalid': errors.has('name')}">
                            <label for="name">Connection Name</label>
                            <md-input
                                id="name"
                                name="name"
                                v-model="connectionType.name"
                                v-validate="'required|min:3'"
                            />
                            <span class="md-error">{{ errors.first('name') }}</span>
                        </md-field>

                    </div>
                </div>
            </md-card-content>

            <md-card-actions>
                <md-button role="button" class="md-raised md-primary" @click="store">Save
                </md-button>
                <md-button role="button" class="md-raised" @click="hide">Close</md-button>
            </md-card-actions>
        </md-card>
    </widget>


</template>

<script>
import Widget from '../../shared/widget'
import {ConnectionTypeService} from '../../services/ConnectionTypeService'
import {EventBus} from '../../shared/eventbus'

export default {
    name: 'NewConnectionType',
    components: {Widget},
    data() {
        return {
            connectionTypeService: new ConnectionTypeService(),
            connectionType: null,
            showAdd: false,
        }
    },
    created() {
        this.connectionType = this.connectionTypeService.connectionType
    },
    mounted() {
        EventBus.$on('showNewConnectionType', this.show)
    },
    methods: {
        async store() {
            let validator = await this.$validator.validateAll()
            if (!validator) {

                return
            }
            this.hide()
            try {

                await this.connectionTypeService.createConnectionType(this.connectionType.name)
                this.alertNotify('success', 'ConnectionType has registered.')
                EventBus.$emit('connectionTypeAdded', this.connectionType)
            } catch (e) {
                this.alertNotify('error', e.message)
            }

        },
        hide() {
            this.showAdd = false
        },
        show() {
            this.showAdd = true
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
    .full-width {
        width: 100% !important;
    }
</style>
