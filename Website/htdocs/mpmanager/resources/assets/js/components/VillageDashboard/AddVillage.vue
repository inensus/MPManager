<template>
    <div>
        <password-protection></password-protection>
        <widget title="Add New Village" color="green">

            <md-card class="md-layout-item md-size-100">

                <md-card-content>


                    <div class="md-layout md-gutter md-size-100">
                        <div class="md-layout-item md-size-70">
                            <md-field :class="{'md-invalid': errors.has('city_name')}">
                                <label for="city_name"> Village Name </label>
                                <md-input
                                    id="city_name"
                                    name="city_name"
                                    v-model="city.name"
                                    v-validate="'required|min:3'"/>
                                <span class="md-error">{{ errors.first('city_name') }}</span>
                            </md-field>
                        </div>
                        <div class="md-layout-item md-size-30">
                            <md-field :class="{'md-invalid': errors.has('city_miniGrid')}">
                                <label>Mini Grid</label>
                                <md-select
                                    id="city_miniGrid"
                                    name="city_miniGrid"
                                    v-model="cityIndex"
                                    v-validate="'required'"
                                >
                                    <md-option>All</md-option>
                                    <md-option v-for="(mg,index) in miniGridList" :value="index" :key="index">{{mg.name}}
                                    </md-option>
                                </md-select>
                                <span class="md-error">{{ errors.first('city_miniGrid') }}</span>

                            </md-field>
                        </div>
                    </div>
                    <div class="md-layout md-gutter md-size-100">
                        <div class="md-layout-item md-size-43">
                            <md-field :class="{'md-invalid': errors.has('city_lat')}">
                                <label for="city_lat">Latitude</label>
                                <md-input
                                    id="city_lat"
                                    name="city_lat"
                                    v-model="cityLatLng.lat"
                                    v-validate="'required|decimal'"/>
                                <span class="md-error">{{ errors.first('city_lat') }}</span>

                            </md-field>
                        </div>
                        <div class="md-layout-item md-size-42">
                            <md-field :class="{'md-invalid': errors.has('city_long')}">
                                <label for="city_long">Longitude</label>
                                <md-input
                                    id="city_long"
                                    name="city_long"
                                    v-model="cityLatLng.lng"
                                    v-validate="'required|decimal'"/>
                                <span class="md-error">{{ errors.first('city_long') }}</span>
                            </md-field>
                        </div>
                        <div class="md-layout-item md-size-15">
                            <md-button class="md-primary md-raised" @click="setMarker"><md-icon>map</md-icon>Set Marker</md-button>
                        </div>
                    </div>
                    <div class="md-layout-item md-size-100">
                        <custom-map
                            :marker-limit="1"
                            :marker-activated="true"
                            :mini-grid-id="miniGridList[cityIndex].id"
                            :geo-cluster-id="miniGridList[cityIndex].cluster_id"
                        ></custom-map>
                    </div>



                </md-card-content>

                <md-progress-bar md-mode="indeterminate" class="md-progress-bar" v-if="sending"/>
                <md-card-actions>
                    <md-button role="button" @click="getValidateVillage" class="md-raised md-primary">Add</md-button>
                </md-card-actions>
            </md-card>

        </widget>
    </div>
</template>

<script>
    import PasswordProtection from '../PasswordProtection';
    import customMap from "../../shared/Map/customMap";
    import Widget from "../../shared/widget";
    import {MiniGridService} from "../../services/MiniGridService";
    import {CityService} from "../../services/CityService";
    import {EventBus} from "../../shared/eventbus";
    export default {
        name: "AddVillage",
        components: {
            Widget,
            PasswordProtection,
            customMap,
        },
        data() {
            return {
                miniGridList: [],
                miniGridService: new MiniGridService(),
                villageSaved: false,
                sending: false,
                lastVillage: null,
                cluster_id: 10,
                city: null,
                cityIndex: 0,
                cityService: new CityService(),
                cityLatLng:{
                    lat:null,
                    lng:null
                }
            }
        },
        mounted() {
            this.getMiniGrids()
        },
        created() {
            this.city = this.cityService.city;
            EventBus.$on('latlng set', (data) => {
                if(data.length == 0){
                    this.cityLatLng.lat = null;
                    this.cityLatLng.lng = null;
                }else{
                    this.cityLatLng.lat = data[data.length-1][0];
                    this.cityLatLng.lng = data[data.length-1][1];
                }
            })
        },
        methods: {
            clearForm() {
                this.city.name = null,
                    this.cityLatLng.lat = null,
                    this.cityLatLng.lng = null,
                    this.city.miniGrid = null,
                    this.sending = false
            },
            setMarker(){
                EventBus.$emit("setMarker", [this.cityLatLng])
            },
            async getMiniGrids() {
                try {
                    this.miniGridList = await this.miniGridService.getMiniGrids();
                    this.cityIndex = this.miniGridList.length-1
                } catch (e) {
                    this.alertNotify('error', e.message)
                }
            },
            getValidateVillage(){
                this.sending = true;
                let validator = this.$validator.validateAll();
                if (validator) {
                    this.sending = false;
                    this.saveVillage();
                }
            },
            async saveVillage() {
                this.cityService.createCity().then((response) => {
                    this.alertNotify('success', 'Village:  Added');
                    console.log(response);
                    this.clearForm();
                }).catch((e) => {
                    this.alertNotify('error', e.message)
                    console.log(e)
                })
            },
            alertNotify(type, message) {
                this.$notify({
                    group: "notify",
                    type: type,
                    title: type + " !",
                    text: message
                });
            }
        },
    }
</script>

<style lang="scss" scoped>
    .md-progress-bar {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
    }
</style>
