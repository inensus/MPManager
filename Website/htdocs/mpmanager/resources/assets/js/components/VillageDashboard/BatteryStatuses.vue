<template>
    <div class="col-md-4 col-sm-12" v-if="batteries !== null && batteries.length >0">
        <widget :id="'batteries'" :headless="true">
            <div class="text-center">
                <h4>Batteries</h4>
            </div>
            <div class="col-sm-12" v-for="battery in this.batteries" style="margin-bottom: 2rem;">
                <div class="col-sm-12" style="text-align: center; margin-bottom: 5px">
                    Battery Id: <strong>{{battery.battery_id}}</strong>
                </div>

                <div class="col-sm-4 col-md-4">
                    <div class="batteryContainer has-tooltip" data-title="Battery Level">
                        <div class="batteryOuter">
                            <div :class="getBatteryClass(battery.state_of_charge)"></div>
                        </div>
                        <div class="batteryBump"></div> &nbsp;
                        {{battery.state_of_charge}}%
                    </div>

                </div> <!-- battery icon -->
                <div class="col-sm-3 has-tooltip" data-title="State of Health">
                    <i class="fa fa-plus-square" style="font-size: 2rem"></i>
                    {{battery.state_of_health}}%
                </div>
                <div class="col-sm-5 has-tooltip" data-title="Last reading date">
                    <i class="fa fa-clock-o" style="font-size: 2rem"></i>
                    {{battery.read_out}}
                </div>

            </div>

        </widget>
    </div>
</template>

<script>
    import Widget from '../../shared/widget'

    $('.has-tooltip').tooltip()

    export default {
        name: 'BatteryStatuses',
        components: { Widget },
        props: {
            mini_grid_id: {
                type: String,
                required: true
            }
        },
        created () {
            this.getBatteries()
        },
        mounted () {
            $('.has-tooltip').tooltip()
        },
        data () {
            return {
                batteries: null
            }
        },
        methods: {
            getBatteries () {
                return
                axios.get(resources.batteries.detail + this.mini_grid_id + '/batteries')
                    .then((response) => {
                        this.batteries = response.data.data
                        setInterval(function () { $('.has-tooltip').tooltip()}, 3000)

                    })
            },
            getBatteryClass (charge) {
                if (charge === 100) {
                    return 'batteryFull'
                } else if (charge > 70) {
                    return 'batteryGood'
                } else if (charge > 40) {
                    return 'batteryMedium'
                } else if (charge > 20) {
                    return 'batteryLow'
                } else {
                    return 'batteryEmpty'
                }

            }
        }
    }
</script>

<style scoped>
    .batteryContainer {
        display: -webkit-box;
        display: -moz-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        flex-direction: row;
        align-items: center;
    }

    .batteryOuter {
        border-radius: 3px;
        border: 1px solid #444;
        padding: 1px;
        width: 45px;
        height: 19px;
    }

    .batteryBump {
        border-radius: 2px;
        background-color: #444;
        margin: 1px;
        width: 2px;
        height: 10px;
    }

    .batteryFull {
        border-radius: 2px;
        background-color: #73AD21;
        width: 41px;
        height: 15px;
    }

    .batteryGood {
        border-radius: 2px;
        background-color: #8fac25;
        width: 35px;
        height: 15px;
    }

    .batteryMedium {
        border-radius: 2px;
        background-color: #ad9b1a;
        width: 17px;
        height: 15px;
    }

    .batteryLow {
        border-radius: 2px;
        background-color: #ad2010;
        width: 11px;
        height: 15px;
    }

    .batteryEmpty {
        border-radius: 2px;
        background-color: #74150b;
        width: 4px;
        height: 15px;
    }
</style>
