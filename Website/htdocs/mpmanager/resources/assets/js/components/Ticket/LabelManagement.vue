<template>
    <div class="row">
        <widget
            :title="'Ticket Categories'"
            :button="true"
            :button-text="'New Categorie'"
            :callback="() => {newLabel = true}"
            color="green">


            <md-card v-if="newLabel">
                <md-card-header>
                    <div class="md-title">Categories for Tickets</div>
                </md-card-header>
                <md-card-content>
                    <div class="md-layout md-gutter">
                        <div class="md-layout-item">
                            <md-field>
                                <md-input type="text" v-model="newLabelName" placeholder="Name"></md-input>
                            </md-field>
                        </div>
                        <div class="md-layout-item">
                            <md-field>
                                <label>Select Color</label>
                                <md-select v-model="currentColor">

                                    <md-option v-for="(index,colorName) in colors" :value="colorName" :key="colorName">
                                        {{colorName}}
                                        <span class="colored-box" style="margin-left: 1rem;max-width: 100px"
                                              :style="{ backgroundColor: colors[colorName]}"> </span>


                                    </md-option>
                                </md-select>

                            </md-field>
                        </div>
                    </div>
                    <div class="md-layout md-subheader">

                        <md-checkbox v-model="outSourcing" class="form-control"
                                     id="outsourcing">Outsourcing
                        </md-checkbox>

                    </div>
                    <div class="md-layout">

                             <span class="md-subheader">That tickets will be payed out to a third party person (non Employee)
                    </span>

                    </div>
                    <div class="md-layout">
                        <span class="md-subheader">
                            By any question please get in touch with &nbsp; <font-awesome-icon icon="envelope"/>  ako@inensus.com
                    </span>

                    </div>


                </md-card-content>
                <md-card-actions>

                    <md-button class="md-raised md-primary" @click="saveLabel">Add Categorie</md-button>
                    <md-button class="md-raised md-accent" @click="() => {newLabel = false}">Cancel</md-button>
                </md-card-actions>
            </md-card>

            <md-card>
                <md-card-content>
                    <md-table>
                        <md-table-row>
                            <md-table-head>ID</md-table-head>
                            <md-table-head>Name</md-table-head>
                            <md-table-head>Color</md-table-head>
                            <md-table-head>Outsourcing</md-table-head>
                        </md-table-row>


                        <md-table-row v-if="labels.length == 0">
                            <md-table-cell>No category found</md-table-cell>
                        </md-table-row>
                        <md-table-row v-for="(label, index) in labels" :key="index">
                            <md-table-cell>{{label.id}}</md-table-cell>
                            <md-table-cell>{{label.label_name}}</md-table-cell>
                            <md-table-cell><span class="colored-box"
                                                 :style="{ backgroundColor: colors[label.label_color]}"></span>
                                {{label.label_color}}
                            </md-table-cell>
                            <md-table-cell>
                                <font-awesome-icon v-if="label.out_source===0" icon="times"/>
                                <font-awesome-icon v-else icon="check"/>
                            </md-table-cell>
                        </md-table-row>

                    </md-table>
                </md-card-content>

            </md-card>
            <!-- <notifications group="foo" position="top right"/>
            <notifications group="error" position="top center"/> -->
        </widget>


    </div>
</template>

<script>
    import { EventBus } from '../../shared/eventbus'
    import Widget from '../../shared/widget'

    export default {
        name: 'LabelManagement',
        components: { Widget },
        data () {
            return {
                labels: [],
                newLabel: false,
                newLabelName: '',
                currentColor: null,
                outSourcing: false,
                colors: {
                    nocolor: 'null',
                    yellow: '#ffff00',
                    purple: '#cc00ff',
                    blue: '#0000cc',
                    red: '#ff0000',
                    green: '#00ff00',
                    orange: '#ffb700',
                    black: '#000000',
                    sky: '#00b7cc',
                    pink: '#cc0555',
                    lime: '#bfe61f',
                },
                bcd: {
                    'Home': {
                        'href': '/'
                    },
                    'Tickets': {
                        'href': null
                    },
                    'Settings': {
                        'href': null
                    },
                    'Category Management': {
                        'href': null
                    },
                },
            }

        },

        created () {
            this.getLabels()

        },

        mounted () {
            EventBus.$emit('bread', this.bcd)
        },
        methods: {
            getLabels () {
                axios.get(resources.ticket.labels)
                    .then(response => {
                        this.labels = response.data.data
                    })
            },
            saveLabel () {
                if (this.currentColor === null) {
                    this.$swal({
                        type: 'error',
                        title: 'No color selected',
                        text: 'Please select a category color.',
                        timer: 5000
                    })
                    return
                }
                if (this.newLabelName === '') {
                    this.$swal({
                        type: 'error',
                        title: 'No name entered',
                        text: 'Please enter a category name.',
                        timer: 5000
                    })
                    return
                }

                axios.post(resources.ticket.labels, {
                    'labelName': this.newLabelName,
                    'labelColor': this.currentColor,
                    'outSourcing': this.outSourcing,
                })
                    .then(response => {
                        let labelData = response.data.data
                        this.labels.push({
                            'id': labelData.id,
                            'label_name': labelData.label_name,
                            'label_color': labelData.label_color,
                            'outSourcing': labelData.outSourcing,
                        })
                    })

                this.newLabel = false

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
