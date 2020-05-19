<template>
    <div>
        <password-protection></password-protection>
        <widget
            title="Add New Mini-Grid"
            color="red">
            <md-card>
                <md-card-header>

                </md-card-header>
                <md-card-content>
                    <div class="md-layout md-gutter md-size-100">
                        <div class="md-layout-item md-size-70">
                            <md-field :class="{'md-invalid': errors.has('miniGrid_name')}">
                                <label for="miniGrid_name">MiniGrid Name</label>
                                <md-input
                                    id="miniGrid_name"
                                    name="miniGrid_name"
                                    v-model="miniGrid_name"
                                    v-validate="'required|min3'"></md-input>
                                <span class="md-error">{{errors.first('miniGrid_name')}}</span>
                            </md-field>

                        </div>
                        <div class="md-layout-item md-size-30">
                            <md-field :class="{'md-invalid': errors.has('cluster_name')}">
                                <label for="cluster_name">Cluster</label>
                                <md-select
                                    v-model="clusterIndex"
                                    name="cluster_name"
                                    id="cluster_name"
                                    v-validate="'required'">
                                    <md-option>All</md-option>
                                    <md-option v-for="(cluster,index) in clustersList" :value="index" :key="index" :id="cluster.clusterId">{{cluster.name}}</md-option>
                                </md-select>
                                <span class="md-error">{{errors.first('cluster_name')}}</span>
                            </md-field>
                        </div>
                    </div>
                    <div class="md-layout md-gutter md-size-100">
                        <div class="md-layout-item md-size-43">
                            <md-field :class="{'md-invalid': errors.has('latitude')}">
                                <label for="latitude">Latitude</label>
                                <md-input
                                    id="latitude"
                                    name="latitude"
                                    v-model="miniGridLatLng.lat"
                                    v-validate="'required|decimal'"></md-input>
                                <span class="md-error">{{errors.first('latitude')}}</span>
                            </md-field>
                        </div>
                        <div class="md-layout-item md-size-42">
                            <md-field :class="{'md-invalid': errors.has('longitude')}">
                                <label for="longitude">Longitude</label>
                                <md-input
                                    id="longitude"
                                    name="longitude"
                                    v-model="miniGridLatLng.lng"
                                    v-validate="'required|decimal'"></md-input>
                                <span class="md-error">{{errors.first('longitude')}}</span>
                            </md-field>
                        </div>
                        <div class="md-layout-item md-size-15">
                            <md-button class="md-raised  md-primary" @click="setMarker" ><md-icon>map</md-icon>Set Marker</md-button>
                        </div>
                    </div>
                    <div class="md-layout md-gutter md-size-100">
                        <div class="md-layout-item md-size-100">
                            <custom-map
                                :geo-cluster-id="this.clustersList[this.clusterIndex].id"
                                :marker-activated="true"
                                :marker-limit="1"
                            >

                            </custom-map>

                        </div>
                    </div>
                </md-card-content>
                <md-progress-bar md-mode="indeterminate" class="md-progress-bar" v-if="sending"/>
                <md-card-actions>
                    <md-button role="button" class="md-raised md-primary" @click="validateForm"> Save </md-button>
                </md-card-actions>
            </md-card>
        </widget>
    </div>
</template>

<script>
    import Widget from "../../shared/widget";
    import PasswordProtection from "../PasswordProtection";
    import customMap from "../../shared/Map/customMap";
    import {EventBus} from "../../shared/eventbus";
    export default {
        name: "AddMiniGrid",
        components: {
            Widget,
            PasswordProtection,
            customMap
        },
        data() {
            return {
                clusterId:14,
                latitude: null,
                longitude: null,
                miniGrid_name:"",
                sending: false,
                clusterName:"",
                clustersList:{},
                miniGridId:null,
                clusterIndex:0,
                miniGridLatLng:{
                    lat:null,
                    lng:null
                }
            }
        },
        methods:{
            validateForm(){
                this.sending = true;
                let validator = this.$validator.validateAll();
                if (validator) {
                    this.sending = false;
                }
            },
            setMarker(){
                EventBus.$emit("setMarker", [this.miniGridLatLng])
            },
            getLocation () {
                axios.get(resources.clusters.geo)
                    .then((response) => {
                        this.clustersList = response.data.data
                        this.clusterIndex = this.clustersList.length -1
                    })
            },
        },
        created() {
            this.getLocation();
            EventBus.$on('latlng set', (data) => {
                if(data.length == 0){
                    this.miniGridLatLng.lat = null;
                    this.miniGridLatLng.lng = null;
                }else{
                    this.miniGridLatLng.lat = data[data.length-1][0];
                    this.miniGridLatLng.lng = data[data.length-1][1];
                }
            })
        },
    }
</script>

<style scoped>
    .md-progress-bar {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
    }
</style>
