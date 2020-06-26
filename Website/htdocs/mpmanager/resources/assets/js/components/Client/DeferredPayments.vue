<template>
    <div>
        <widget
            :class="'col-sm-6 col-md-5'"
            :button-text="'Assign new Appliance'"
            :button="true"
            title="Sold Appliance"
            :button-color="'red'"
            :callback="()=>{showNewAppliance = true}"
        >

            <!-- assing new appliance -->
            <form v-if="showNewAppliance" novalidate class="md-layout" @submit.prevent="saveAppliance">
                <md-card class="md-layout-item">
                    <md-card-header>
                        <div style="float:right; cursor:pointer" @click="()=>{showNewAppliance = false}">
                            <md-icon>close</md-icon>&nbsp;Close
                        </div>
                    </md-card-header>
                    <md-card-content>
                        <md-field>
                            <label for="appliance_types">Appliance Type</label>
                            <md-select name="appliance_types" id="appliance_types" v-model="newAppliance.id">
                                <md-option disabled value>--Select--</md-option>
                                <md-option
                                    :value="applianceType.id"
                                    v-for="applianceType in applianceTypes"
                                    :key="applianceType.id"
                                >{{applianceType.name}}
                                </md-option>
                            </md-select>
                        </md-field>

                        <md-field>
                            <label for="Cost">Cost (TZS)</label>
                            <md-input type="text" name="Cost" v-model="newAppliance.cost"/>
                        </md-field>

                        <md-field>
                            <label for="rate">Rate Count</label>
                            <md-input type="text" name="rate" v-model="newAppliance.rate"/>
                        </md-field>

                        <div  class="md-layout md-gutter">
                            <div class="md-layout-item md-size-33">
                                <md-list v-for="x in parseInt(newAppliance.rate)" :key="x"
                                      v-if="Math.ceil(newAppliance.rate / 3 )>= x">{{x}}&nbsp;-&nbsp;{{ readable(getRate(x,
                            newAppliance.rate,newAppliance.cost ))}} {{appConfig.currency}}</md-list>
                            </div>
                            <div class="md-layout-item md-size-33">
                                <md-list v-for="x in parseInt(newAppliance.rate)" :key="x"
                                      v-if="Math.ceil(newAppliance.rate / 3)*2 >= x && x > Math.ceil(newAppliance.rate / 3)">{{x}}&nbsp;-&nbsp;{{ readable(getRate(x,
                            newAppliance.rate,newAppliance.cost ))}} {{appConfig.currency}}</md-list>
                            </div>
                            <div class="md-layout-item md-size-33">
                                <md-list v-for="x in parseInt(newAppliance.rate)" :key="x"
                                      v-if="newAppliance.rate >= x && x > Math.ceil(newAppliance.rate / 3)*2">{{x}}&nbsp;-&nbsp;{{ readable(getRate(x,
                            newAppliance.rate,newAppliance.cost ))}} {{appConfig.currency}}</md-list>
                            </div>







                        </div>

                    </md-card-content>
                    <md-card-actions>
                        <md-button type="submit" class="md-primary btn-sell">Sell Appliance</md-button>
                    </md-card-actions>
                </md-card>
            </form>

            <div>
                <!-- ana tablo  -->

                <div v-if="personAppliance.length !== 0 && personAppliance.length !== null && showNewAppliance === false">
                    <md-table>

                        <md-table-row>
                            <md-table-head>Name</md-table-head>
                            <md-table-head>Cost</md-table-head>
                            <md-table-head>Rates</md-table-head>
                        </md-table-row>
                        <md-table-row v-for="(item, index) in personAppliance" :key="index">
                            <md-table-cell md-label="Name" md-sort-by="name">{{item.appliance_type.name}}</md-table-cell>
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
                <span v-if="personAppliance !== null && personAppliance.length === 0 && showNewAppliance === false"
                      style="alignment: center;"><h3>There is no Appliance for this Customer.</h3>  </span>

            </div>
        </widget>

        <md-dialog v-if="selectedAppliance!==null" :md-active.sync="showModal" class="md-scrollbar">
            <md-dialog-title>
                Details of
                <strong>{{selectedAppliance.appliance_type.name}}</strong>
            </md-dialog-title>
            <md-dialog-content class="md-scrollbar">
                <div class="md-layout">
                    <div class="md-layout-item">
                        <span>Total Cost :</span>
                        <span>{{readable(selectedAppliance.total_cost)}}</span>
                        <br/>
                        <span>Sold At :</span>
                        <span>{{formatReadableDate(selectedAppliance.created_at)}}</span>
                        <br/>
                        <span>Rates Count :</span>
                        <span>{{selectedAppliance.rate_count}}</span>
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
                        <md-table-row v-for="(rate, index) in selectedAppliance.rates" :key="rate.id">
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
                        <md-list class="md-size-100" v-for="log in selectedAppliance.logs" :key="log.id">
                            <md-list-item>
                                <md-icon>update</md-icon>
                                <div class="md-list-item-text">
                                    <span>{{log.action}} </span>
                                    <small>on {{formatReadableDate(log.created_at)}} by <strong>{{log.owner.name}}</strong></small>
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
    import {ApplianceTypes} from "../../classes/appliance/ApplianceTypes";
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
            this.getApplianceTypesList();
            this.getApplianceList();

        },
        data() {
            return {
                selectedAppliance: null,
                showModal: false,
                editRow: null,
                showNewAppliance: false,
                personAppliance: null,
                applianceTypes: null,
                newAppliance: {
                    id: null,
                    cost: 1,
                    rate: 1
                }
            };
        },


        methods: {
            getRateColumns(){

                let mid = Math.ceil(this.newAppliance.rate / 3)
                for (let col = 0; col < 3; col++) {
                    this.rateColumns.push(this.currentRates.slice(col * mid, col * mid + mid))
                }
                return this.rateColumns
            },
            toggleModal() {
                this.showModal = !this.showModal;
            },
            formatReadableDate(date) {
                return moment(date).format("MMMM Do YYYY, h:mm:ss a");
            },
            changeRateAmount(rate_id) {
                this.editRow = "rate_" + rate_id;
            },
            showDetails(index) {
                this.toggleModal();
                this.selectedAppliance = this.personAppliance[index];
            },
            showConfirm(data) {
                EventBus.$emit("show.confirm", data);
            },
            async editRate(rate) {

                const data ={
                    remaining: rate.remaining,
                    admin_id: await this.$store.state.admin.getId()
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
                        .put(resources.appliances.rate.update + rate.id, data )
                        .then(response => {
                            this.$swal({
                                type: "success",
                                title: "Success",
                                text: "Rate Saved Successfully"

                            })
                            this.getApplianceList();
                            this.toggleModal();
                            this.editRow = null;

                        });
                });
            },
            getApplianceTypesList() {
                axios.get(resources.appliances.type.list).then(response => {
                    this.applianceTypes = response.data.data;
                });
            },

            getRate(index, rateCount, cost) {
                if (index === parseInt(rateCount)) {
                    return cost - (rateCount - 1) * Math.round(cost / rateCount);
                } else {
                    return Math.round(cost / rateCount);
                }

            },
            getApplianceList() {
                axios.get(resources.appliances.type.person + this.personId).then(response => {
                    this.personAppliance = response.data.data;
                    console.log(this.personAppliance);
                });
            },
            clearForm() {
                this.newAppliance.id = null;
                this.newAppliance.cost = 1;
                this.newAppliance.rate = 1;
            },
            saveAppliance() {
                if (this.newAppliance.id === null) {
                    this.$swal({
                        type: "error",
                        title: "Appliance not selected",
                        text:
                            "Please select an appliance type from the list before you can sell/store it."
                    });

                    return;
                }
                this.$swal({
                    type: "question",
                    title: "Save Appliance",
                    text: "Are you sure to sell the appliance for " + this.newAppliance.cost + "?",
                    showCancelButton: true,
                    cancelButtonText: "Cancel",
                    confirmButtonText: "Sell"
                }).then(response => {
                    axios
                        .post(
                            resources.appliances.type.sell +
                            this.newAppliance.id +
                            "/people/" +
                            this.personId,
                            this.newAppliance
                        )
                        .then(response => {
                            this.$swal({
                                type: "success",
                                title: "Success",
                                text: "Appliance Saved Successfully"

                            })
                            this.showNewAppliance = false;
                            this.clearForm();
                            this.getApplianceList();
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
