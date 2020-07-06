<template>
    <widget
        :id="'appliance_type_list'"
        :title="'List of Appliance Types'"
        :button="true"
        :buttonText="'New Type'"
        :paginator="appliances.paginator"
        :subscriber="subscriber"
        :callback="newAppliance"
        :route_name="'/appliance/types'"
    >
        <md-table id="dt-basic" class="table table-striped table-bordered table-hover no-footer">
            <md-table-row

                <md-table-head>ID
                </md-table-head>


                <md-table-head>
                    <i class="fa fa-fw fa-exchange-alt text-muted hidden-md hidden-sm hidden-xs"></i>
                    Name
                </md-table-head>

                <md-table-head class="hidden-xs">
                    <i class="fa fa-fw fa-clock text-muted hidden-md hidden-sm hidden-xs"></i>
                    Created At
                </md-table-head>


            </md-table-row>

            <md-table-row v-for="appliance in appliances.list" style="cursor:pointer;">
                <md-table-cell> {{ appliance.id}}</md-table-cell>

                <md-table-cell> {{appliance.type}}

                </md-table-cell>

                <md-table-cell class="hidden-xs">{{appliance.created_at}}</md-table-cell>

            </md-table-row>

        </md-table>
    </widget>
</template>

<script>

    import {ApplianceTypes} from '../../classes/appliance/ApplianceTypes'
    import {EventBus} from '../../shared/eventbus'
    import Widget from '../../shared/widget'

    export default {
        name: 'ApplianceTypes',
        components: {Widget},
        data() {
            return {
                appliances: new ApplianceTypes(),
                subscriber: 'appliance-list',
                bcd: {
                    'Home': {
                        'href': '/'
                    },
                    'Meters': {
                        'href': null
                    },
                },
            }
        },
        created() {

        },
        mounted() {

            EventBus.$emit('bread', this.bcd)
            EventBus.$on('pageLoaded', this.reloadList)
            // EventBus.$on('searching', this.searching)
            // EventBus.$on('end_searching', this.endSearching)

        },
        beforeDestroy() {
            EventBus.$off('pageLoaded', this.reloadList)
            //EventBus.$off('searching', this.searching)
            //EventBus.$off('end_searching', this.endSearching)
        },
        methods: {
            reloadList(subscriber, data) {
                if (subscriber !== this.subscriber) return
                this.appliances.updateList(data)

            },
            newAppliance() {
            }
        }
    }
</script>

<style>

</style>
