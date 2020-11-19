<template>
    <widget
        v-if="showAdd"
        :id="'new-connection-group'"
        :title="$tc('phrases.newConnectionGroup')"
        :color="'red'"
    >


        <md-card>

            <md-card-content>
                <div class="md-layout md-gutter">
                    <div class="md-layout-item">
                        <md-field :class="{'md-invalid': errors.has($tc('words.name'))}">
                            <label for="name">{{ $tc('words.name') }}</label>
                            <md-input
                                id="name"
                                :name="$tc('words.name')"
                                v-model="connectionGroup.name"
                                v-validate="'required|min:3'"
                            />
                            <span class="md-error">{{ errors.first($tc('words.name')) }}</span>
                        </md-field>

                    </div>
                </div>
            </md-card-content>

            <md-card-actions>
                <md-button role="button" class="md-raised md-primary" @click="store">{{ $tc('words.save') }}
                </md-button>
                <md-button role="button" class="md-raised" @click="hide">{{ $tc('words.close') }}</md-button>
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
                this.alertNotify('success', this.$tc('phrases.newConnectionGroup',2))
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
