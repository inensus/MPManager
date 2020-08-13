<template>
    <div>
        <widget
            :class="'col-sm-6 col-md-5'"
            :button-text="'Assign new Asset'"
            :button="true"
            title="Sold Assets"
            :button-color="'red'"
            :callback="()=>{showNewAsset = true}"
        >

            <!-- assing new asset -->
            <form v-if="showNewAsset" novalidate class="md-layout" @submit.prevent="saveAsset">
                <md-card class="md-layout-item">
                    <md-card-header>
                        <div style="float:right; cursor:pointer" @click="()=>{showNewAsset = false}">
                            <md-icon>close</md-icon>&nbsp;Close
                        </div>
                    </md-card-header>
                    <md-card-content>
                        <md-field>
                            <label for="asset_types">Asset Type</label>
                            <md-select name="asset_types" id="asset_types" v-model="newAsset.id">
                                <md-option disabled value>--Select--</md-option>
                                <md-option
                                    :value="assetType.id"
                                    v-for="assetType in assetTypes"
                                    :key="assetType.id"
                                >{{assetType.name}}
                                </md-option>
                            </md-select>
                        </md-field>

                        <md-field>
                            <label for="Cost">Cost (TZS)</label>
                            <md-input type="text" name="Cost" v-model="newAsset.cost"/>
                        </md-field>

                        <md-field>
                            <label for="rate">Rate Count</label>
                            <md-input type="text" name="rate" v-model="newAsset.rate"/>
                        </md-field>

                        <div  class="md-layout md-gutter">
                            <div class="md-layout-item md-size-33">
                                <md-list v-for="x in parseInt(newAsset.rate)" :key="x"
                                         v-if="Math.ceil(newAsset.rate / 3 )>= x">{{x}}&nbsp;-&nbsp;{{ readable(getRate(x,
                                    newAsset.rate,newAsset.cost ))}} {{appConfig.currency}}</md-list>
                            </div>
                            <div class="md-layout-item md-size-33">
                                <md-list v-for="x in parseInt(newAsset.rate)" :key="x"
                                         v-if="Math.ceil(newAsset.rate / 3)*2 >= x && x > Math.ceil(newAsset.rate / 3)">{{x}}&nbsp;-&nbsp;{{ readable(getRate(x,
                                    newAsset.rate,newAsset.cost ))}} {{appConfig.currency}}</md-list>
                            </div>
                            <div class="md-layout-item md-size-33">
                                <md-list v-for="x in parseInt(newAsset.rate)" :key="x"
                                         v-if="newAsset.rate >= x && x > Math.ceil(newAsset.rate / 3)*2">{{x}}&nbsp;-&nbsp;{{ readable(getRate(x,
                                    newAsset.rate,newAsset.cost ))}} {{appConfig.currency}}</md-list>
                            </div>







                        </div>

                    </md-card-content>
                    <md-card-actions>
                        <md-button type="submit" class="md-primary btn-sell">Sell Asset</md-button>
                    </md-card-actions>
                </md-card>
            </form>

            <div>
                <!-- ana tablo  -->

                <div v-if="personAsset.length !== 0 && personAsset.length !== null && showNewAsset === false">
                    <md-table>

                        <md-table-row>
                            <md-table-head>Name</md-table-head>
                            <md-table-head>Cost</md-table-head>
                            <md-table-head>Rates</md-table-head>
                        </md-table-row>
                        <md-table-row v-for="(item, index) in personAsset" :key="index">
                            <md-table-cell md-label="Name" md-sort-by="name">{{item.asset_type.name}}</md-table-cell>
                            <md-table-cell md-label="Cost" md-sort-by="total_cost">{{item.total_cost}} TZS
                            </md-table-cell>
                            <md-table-cell md-label="Rates" md-sort-by="rate_count">
                                {{ item.rate_count}}
                                <div
                                    :class="index=== -999?'text-danger':'text-success'"
                                    style="cursor: pointer; display:inline-block"
                                    @click="showDetails(index)"
                                >
                                    <md-icon>remove_red_eye</md-icon>
                                    Details
                                </div>
                            </md-table-cell>
                        </md-table-row>
                    </md-table>
                </div>
                <span v-if="personAsset !== null && personAsset.length === 0 && showNewAsset === false"
                      style="alignment: center;"><h3>There is no Asset for this Customer.</h3>  </span>

            </div>
        </widget>

        <md-dialog v-if="selectedAsset!==null" :md-active.sync="showModal" class="md-scrollbar">
            <md-dialog-title>
                Details of
                <strong>{{selectedAsset.asset_type.name}}</strong>
            </md-dialog-title>
            <md-dialog-content class="md-scrollbar">
                <div class="md-layout">
                    <div class="md-layout-item">
                        <span>Total Cost :</span>
                        <span>{{readable(selectedAsset.total_cost)}}</span>
                        <br/>
                        <span>Sold At :</span>
                        <span>{{formatReadableDate(selectedAsset.created_at)}}</span>
                        <br/>
                        <span>Rates Count :</span>
                        <span>{{selectedAsset.rate_count}}</span>
                    </div>


                    <md-table class="md-layout-item md-size-100">
                        <md-table-row>
                            <md-table-head>
                                <strong>Rate</strong>
                            </md-table-head>
                            <md-table-head>
                                <strong>Cost</strong>
                            </md-table-head>
                            <md-table-head>
                                <strong>Remaining Amount</strong>
                            </md-table-head>
                            <md-table-head>
                                <strong>Due Date</strong>
                            </md-table-head>
                            <md-table-head>
                                <strong>#</strong>
                            </md-table-head>
                        </md-table-row>
                        <md-table-row v-for="(rate, index) in selectedAsset.rates" :key="rate.id">
                            <md-table-cell>{{index + 1}}</md-table-cell>
                            <md-table-cell>{{readable( rate.rate_cost)}} TZS</md-table-cell>
                            <md-table-cell v-if="editRow === 'rate'+'_'+rate.id">
                                <div style="display: inline-flex;">
                                    <input class="form-control" v-model="rate.remaining" type="text"/>
                                    <div @click="() => {editRow = null}">
                                        <md-icon style="color:red">cancel</md-icon>
                                    </div>
                                </div>
                            </md-table-cell>
                            <md-table-cell v-else>{{readable(rate.remaining)}} TZS</md-table-cell>

                            <md-table-cell>{{rate.due_date}}</md-table-cell>

                            <md-table-cell v-if="editRow === 'rate'+'_'+rate.id">
                                <div @click="editRate(rate)">
                                    <md-icon style="color:green">save</md-icon>
                                </div>
                            </md-table-cell>
                            <md-table-cell v-else>
                                <div @click="changeRateAmount(rate.id)">
                                    <md-icon>edit</md-icon>
                                </div>
                            </md-table-cell>
                        </md-table-row>
                    </md-table>


                    <div  class="md-layout-item md-size-100">
                        <h4>History</h4>
                        <md-list class="md-size-100" v-for="log in selectedAsset.logs" :key="log.id">
                            <md-list-item>
                                <md-icon>update</md-icon>
                                <div class="md-list-item-text">
                                    <span>{{log.action}} </span>
                                    <small>on {{formatReadableDate(log.created_at)}}   </small>
                                </div>


                            </md-list-item>
                            <md-divider></md-divider>
                        </md-list>
                    </div>
                </div>
            </md-dialog-content>
            <md-dialog-actions>
                <md-button class="md-accent" @click="toggleModal()">Close</md-button>
            </md-dialog-actions>
        </md-dialog>
    </div>
</template>

<script>
    import Widget from "../../shared/widget";
    import {AssetTypes} from "../../classes/asset/AssetTypes";
    import {resources} from "../../resources";

    import {currency} from "../../mixins/currency";
    import ConfirmationBox from "../../shared/ConfirmationBox";
    import {EventBus} from "../../shared/eventbus";
    import Modal from "../../modal/modal";
    import moment from "moment";

    export default {
        name: "DeferredPayments",
        mixins: [currency],
        components: {Modal, ConfirmationBox, Widget},
        props: {
            personId: Number
        },
        mounted() {
            this.getAssetTypesList();
            this.getAssetList();

        },
        data() {
            return {
                selectedAsset: null,
                showModal: false,
                editRow: null,
                showNewAsset: false,
                personAsset: null,
                assetTypes: null,
                admin_id: this.$store.getters['auth/authenticationService'].authenticateUser.token,
                newAsset: {
                    id: null,
                    cost: 1,
                    rate: 1
                }
            };
        },


        methods: {
            getRateColumns(){

                let mid = Math.ceil(this.newAsset.rate / 3)
                for (let col = 0; col < 3; col++) {
                    this.rateColumns.push(this.currentRates.slice(col * mid, col * mid + mid))
                }
                return this.rateColumns
            },
            toggleModal() {
                this.showModal = !this.showModal;
            },
            formatReadableDate(date) {
                return moment(date).format("MMMM DD YYYY");
            },
            changeRateAmount(rate_id) {
                this.editRow = "rate_" + rate_id;
            },
            showDetails(index) {
                this.toggleModal();
                this.selectedAsset = this.personAsset[index];
            },
            showConfirm(data) {
                EventBus.$emit("show.confirm", data);
            },
            async editRate(rate) {

                const data ={
                    remaining: rate.remaining,
                    admin_id: this.admin_id,

                }

                this.$swal({
                    type: "question",
                    title: "Edit Rate",
                    text: "Are you sure to change the rate ?",
                    showCancelButton: true,
                    cancelButtonText: "Cancel",
                    confirmButtonText: "Change"
                }).then(response => {
                    axios
                        .put(resources.assets.rate.update + rate.id, data )
                        .then(response => {
                            this.$swal({
                                type: "success",
                                title: "Success",
                                text: "Rate Saved Successfully"

                            })
                            this.getAssetList();
                            this.toggleModal();
                            this.editRow = null;

                        });
                });
            },
            getAssetTypesList() {
                axios.get(resources.assets.type.list).then(response => {
                    this.assetTypes = response.data.data;
                });
            },

            getRate(index, rateCount, cost) {
                if (index === parseInt(rateCount)) {
                    return cost - (rateCount - 1) * Math.round(cost / rateCount);
                } else {
                    return Math.round(cost / rateCount);
                }

            },
            getAssetList() {
                axios.get(resources.assets.type.person + this.personId).then(response => {
                    this.personAsset = response.data.data;

                });
            },
            clearForm() {
                this.newAsset.id = null;
                this.newAsset.cost = 1;
                this.newAsset.rate = 1;
            },
            saveAsset() {
                if (this.newAsset.id === null) {
                    this.$swal({
                        type: "error",
                        title: "Asset not selected",
                        text:
                            "Please select an asset type from the list before you can sell/store it."
                    });

                    return;
                }
                this.$swal({
                    type: "question",
                    title: "Save Asset",
                    text: "Are you sure to sell the asset for " + this.newAsset.cost + "?",
                    showCancelButton: true,
                    cancelButtonText: "Cancel",
                    confirmButtonText: "Sell"
                }).then(response => {
                    axios
                        .post(
                            resources.assets.type.sell +
                            this.newAsset.id +
                            "/people/" +
                            this.personId,
                            this.newAsset
                        )
                        .then(response => {
                            this.$swal({
                                type: "success",
                                title: "Success",
                                text: "Asset Saved Successfully"

                            })
                            this.showNewAsset = false;
                            this.clearForm();
                            this.getAssetList();
                        });
                });
            }
        }
    };
</script>

<style scoped>
    .mb {
        margin-bottom: 15px;
        border-bottom: 1px solid #eceaea;
    }

    .edit {
        color: #2c4074;
        /*position: absolute;*/
        left: 88%;
        cursor: pointer;
    }

    .edit:hover {
        color: #5562c5;
        transform: scale(1.5);
    }

    .detail {
        color: #0d746c;
        /*position: absolute;*/
        left: 92%;
        cursor: pointer;
    }

    .detail:hover {
        color: #1e7f99;
        transform: scale(1.5);
    }

    .save {
        color: #1d8922;
        /*position: absolute;*/
        left: 92%;
        cursor: pointer;
    }

    .save:hover {
        color: #8fac25;
        transform: scale(1.5);
    }

    .cancel {
        color: #74150b;
        /*position: absolute;*/
        left: 92%;
        cursor: pointer;
    }

    .cancel:hover {
        color: #a81e10;
        transform: scale(1.5);
    }

    .details-modal-grid {
        padding: 1rem;
    }
</style>
