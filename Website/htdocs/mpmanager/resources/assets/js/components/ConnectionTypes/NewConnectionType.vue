<template>
    <widget
        v-if="showAdd"
        :id="'new-connection-type'"
        :title="$tc('phrases.newConnectionType')"
        color="red"
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
                                v-model="connectionType.name"
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
                this.alertNotify('success', this.$tc('phrases.newConnectionType',2))
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
