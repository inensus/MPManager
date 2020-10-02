<template>
    <div class="row">
        <widget v-if="newLabel"
            title="New Category Label"
            color="red">
            <md-card >
                <md-card-header>
                    <div class="md-title">Categories for Tickets</div>
                </md-card-header>
                <md-card-content>
                    <div class="md-layout md-gutter">
                        <div class="md-layout-item">
                            <md-field :class="{'md-invalid': errors.has('name')}">
                                <md-input type="text"
                                          v-model="ticketLabelService.newLabelName"
                                          placeholder="Name"
                                          name="name"
                                          id="name"
                                          v-validate="'required|min:3'"
                                ></md-input>
                                <span class="md-error">{{ errors.first('name') }}</span>
                            </md-field>
                        </div>
                        <div class="md-layout-item">
                            <md-field :class="{'md-invalid': errors.has('color')}">
                                <label>Select Color</label>
                                <md-select v-model="ticketLabelService.currentColor"
                                           name="color"
                                           id="color"
                                           v-validate="'required'">

                                    <md-option v-for="(index,colorName) in ticketLabelService.colors" :value="colorName"
                                               :key="colorName">
                                        {{colorName}}
                                        <span class="colored-box" style="margin-left: 1rem;max-width: 100px"
                                              :style="{ backgroundColor: ticketLabelService.colors[colorName]}"> </span>


                                    </md-option>
                                </md-select>
                                <span class="md-error">{{ errors.first('color') }}</span>
                            </md-field>
                        </div>
                    </div>
                    <div class="md-layout md-subheader">

                        <md-checkbox v-model="ticketLabelService.outSourcing" class="form-control"
                                     id="outsourcing">Outsourcing
                        </md-checkbox>

                    </div>
                    <div class="md-layout">

                             <span class="md-subheader">That tickets will be payed out to a third party person (non Employee)
                    </span>

                    </div>
                    <div class="md-layout">
                        <span class="md-subheader">
                            By any question please get in touch with &nbsp; <md-icon>email</md-icon> ako@inensus.com
                    </span>

                    </div>


                </md-card-content>
                <md-progress-bar md-mode="indeterminate" v-if="loading"/>
                <md-card-actions>

                    <md-button class="md-raised md-primary" @click="saveLabel">Add Category</md-button>
                    <md-button class="md-raised md-accent" @click="() => {newLabel = false}">Cancel</md-button>
                </md-card-actions>
            </md-card>
        </widget>

        <widget
            :title="'Ticket Categories'"
            :button="true"
            :button-text="'New Categorie'"
            @widgetAction="() => {newLabel = true}"
            color="green"
            :subscriber="subscriber">
            <md-card>
                <md-card-content>

                        <md-table>
                            <md-table-row>
                                <md-table-head v-for="(item, index) in headers" :key="index">{{item}}</md-table-head>
                            </md-table-row>

                            <md-table-row v-for="(label, index) in ticketLabelService.list" :key="index">
                                <md-table-cell>{{label.id}}</md-table-cell>
                                <md-table-cell>{{label.label_name}}</md-table-cell>
                                <md-table-cell><span class="colored-box"
                                                     :style="{ backgroundColor: ticketLabelService.colors[label.label_color]}"></span>
                                    {{label.label_color}}
                                </md-table-cell>
                                <md-table-cell>
                                    <md-icon v-if="label.out_source===0">cancel</md-icon>
                                    <md-icon v-else>check</md-icon>
                                </md-table-cell>
                            </md-table-row>

                        </md-table>

                </md-card-content>

            </md-card>

        </widget>


    </div>
</template>

<script>
import Widget from '../../shared/widget'
import { TicketLabelService } from '../../services/TicketLabelService'
import { EventBus } from '../../shared/eventbus'

export default {
    name: 'LabelManagement',
    components: { Widget },
    data () {
        return {
            ticketLabelService: new TicketLabelService(),
            newLabel: false,
            headers: ['ID', 'Name', 'Color', 'Outsourcing'],
            tableName: 'Category',
            loading: false,
            subscriber:'ticket-labels'
        }

    },

    created () {
        this.getLabels()
    },

    methods: {
        async getLabels () {
            try {
                await this.ticketLabelService.getLabels()
                EventBus.$emit('widgetContentLoaded',this.subscriber,this.ticketLabelService.list.length)
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async saveLabel () {

            let validator = await this.$validator.validateAll()
            if (validator) {

                try {
                    this.loading = true
                    await this.ticketLabelService.createLabel(this.ticketLabelService.newLabelName, this.ticketLabelService.currentColor, this.ticketLabelService.outSourcing)
                    this.alertNotify('success', 'New category added successfully.')
                    this.loading = false
                } catch (e) {
                    this.loading = false
                    this.alertNotify('error', e.message)
                }
                this.ticketLabelService.resetLabel()
                this.newLabel = false
            }

        },
        alertNotify (type, message) {
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message,
                speed: 0
            })
        },
    },
}
</script>

<style lang="scss">
    .md-list-item-text {
        display: contents !important;
    }

    .colored-box {
        width: 22px;
        height: 22px;
        margin: 0 5px 0 0;
        display: inline-block;
    }

</style>
