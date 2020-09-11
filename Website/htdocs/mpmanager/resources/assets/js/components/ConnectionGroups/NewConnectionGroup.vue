<template>
    <widget
        v-if="showAdd"
        :id="'new-connection-group'"
        :title="'Add New Connection Group'"
        :color="'red'">


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
                                v-model="connectionGroup.name"
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
import {EventBus} from '../../shared/eventbus'
import {ConnectionGroupService} from '../../services/ConnectionGroupService'

export default {
    name: 'NewConnectionGroup',
    components: {Widget},
    data() {
        return {
            connectionGroupService: new ConnectionGroupService(),
            connectionGroup: null,
            showAdd: false,
        }
    },
    created() {
        this.connectionGroup = this.connectionGroupService.connectionGroup
    },
    mounted() {
        EventBus.$on('showNewConnectionGroup', this.show)
    },
    methods: {
        async store() {
            let validator = await this.$validator.validateAll()
            if (!validator) {

                return
            }
            this.hide()
            try {

                await this.connectionGroupService.createConnectionGroup(this.connectionGroup.name)
                this.alertNotify('success', 'ConnectionGroup has registered.')
                EventBus.$emit('connectionGroupAdded', this.connectionGroup)
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
