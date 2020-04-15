<template>
    <widget
        :id="'new-connection-type'"
        :title="'Add New Connection Type'">
        <div class="margin-top-10">
            <div class="col-sm-2">
                Name of the Connection
            </div>
            <div class="col-sm-10">
                <input class="full-width form-control" type="text" v-model="connection.name"
                       placeholder="Connection Name">
            </div>
            <div class="col-sm-12">
                <button class="btn btn-success" @click="store">Save</button>
            </div>
        </div>
    </widget>

</template>

<script>
    import Widget from "../../shared/widget";
    import {ConnectionsType} from "../../classes/connection/ConnectionsType";

    export default {
        name: "NewConnectionType",
        components: {Widget},
        data() {
            return {
                connection: new ConnectionsType(),
            };
        },
        methods: {
            store() {
                this.connection.store().then(() => {
                    this.$router.push({path: '/connection-types'});
                });
            }
        }
    }
</script>

<style scoped>
    .full-width {
        width: 100% !important;
    }
</style>
