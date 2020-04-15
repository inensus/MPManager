<template>
  <div style="margin: 1vh;">
    <h2>Filter</h2>
    <div class="md-layout">
      <div class="md-layout-item">
        <md-field>
          <md-input
            type="text"
            placeholder="Meter Serial Number"
            v-model="filter.serial_number"
            v-on:keyup.enter="submitFilter"
          ></md-input>
        </md-field>
      </div>
      <div class="md-layout-item">
        <md-field>
          <md-select v-model="tarrif_" name="tariff" id="tariff" @md-selected="setTarrif">
            <md-option value="all">All Tariffs</md-option>
            <md-option v-for="tariff in tariffs" :key="tariff.id">{{tariff.name}}</md-option>
          </md-select>
        </md-field>
      </div>
      <div class="md-layout-item">
        <md-field>
          <md-select v-model="provider_" name="provider" id="provider" @md-selected="setProvider">
            <md-option value="All Network Providers">All Network Providers</md-option>
            <md-option value="Airtel">Airtel</md-option>
            <md-option value="Vodacom">Vodacom</md-option>
          </md-select>
        </md-field>
      </div>
      <div class="md-layout-item">
        <md-field>
          <md-select
            v-model="transaction_"
            name="transaction"
            id="transaction"
            @md-selected="seTransaction"
          >
            <md-option value="All Transactions">All Transactions</md-option>
            <md-option value="Only Approved">Only Approved</md-option>
            <md-option value="Only Rejected">Only Rejected</md-option>
          </md-select>
        </md-field>
      </div>

      <div class="md-layout-item">
        <md-datepicker v-model="filter.start_date">
          <label>From date</label>
        </md-datepicker>
      </div>

      <div class="md-layout-item">
        <md-datepicker v-model="filter.to">
          <label>To date</label>
        </md-datepicker>
      </div>
    </div>

    <div class="md-layout-item">
      <md-button class="md-raised md-primary " @click="submitFilter">Search</md-button>
    </div>
  </div>
</template>

<script>
import Datepicker from "vuejs-datepicker";
import moment from "moment";

export default {
  name: "FilterTransaction",
  components: { Datepicker },
  mounted() {
    this.getTariffs();
    this.getSearch();
  },

  data() {
    return {
      tariffs: null,
      tarrif_: "All Tariffs",
      provider_: "All Network Providers",
      transaction_: "All Transactions",
      filter: {
        status: null,
        serial_number: this.serial_number,
        tariff: null,
        provider: null,
        from: null,
        to: null
      }
    };
  },

  methods: {
    setStartDate(val) {
      this.filter.from = moment(val).format("Y-MM-DD 00:00:01");
    },
    setEndDate(val) {
      this.filter.to = moment(val).format("Y-MM-DD 23:59:59");
    },
    getTariffs() {
      axios.get(resources.tariff.list).then(response => {
        this.tariffs = response.data.data;
      });
    },
    setTarrif(tarrif) {
      this.filter.tariff = tarrif;
    },
    setProvider(provider) {
      switch (provider) {
        case "All Network Providers":
          this.filter.provider = "-1";
          break;
        case "Airtel":
          this.filter.provider = "airtel_transaction";
          break;
        case "Vodacom":
          this.filter.provider = "vodacom_transaction";
          break;
        default:
          break;
      }
    },
    seTransaction(transaction) {
      switch (transaction) {
        case "All Transactions":
          this.filter.status = "all";
          break;
        case "Only Approved":
          this.filter.status = "1";
          break;
        case "Only Rejected":
          this.filter.status = "-1";
          break;

        default:
          break;
      }
    },
    submitFilter() {
      if (this.filter.serial_number === "") {
        this.filter.serial_number = null;
      }
      if (this.filter.provider === -1 || this.filter.provider === "-1") {
        this.filter.provider = null;
      }
      if (this.filter.tariff === "all") {
        this.filter.tariff = null;
      }
      if (this.filter.status === "all") {
        this.filter.status = null;
      }
      this.$emit("searchSubmit", this.filter);
    },

    getSearch() {
      let search = this.$store.getters.search;

      if (Object.keys(search).length) {
        if ("serial_number" in search) {
          this.filter["serial_number"] = search["serial_number"];
        }
        if ("from" in search) {
          this.filter["from"] = search["from"];
        }
        if ("to" in search) {
          this.filter["to"] = search["to"];
        }
      }
    }
  }
};
</script>

<style>
.kemal {
  width: 100% !important;
}
</style>
