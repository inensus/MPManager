<template>
    <div>

        <widget
            title="Sms List"
            :subscriber="subscriber"

            :callback="() => {this.showModal = true}"
            color="red"
            :paginator="smsService.paginator"
            :route_name="'/sms/list'"
        >

            <div class="md-layout md-gutter" v-if="!showModal">
                <div class="md-layout-item md-size-35 " style="min-height: 95vh!important;
    border-right: 1px solid #d6d6d6;">
                    <div class="scrollable">
                        <md-table>
                            <md-table-toolbar>
                                <md-field>
                                    <md-input v-model="filterNumber" type="text" class="form-control"
                                              placeholder="Search"
                                    ></md-input>
                                </md-field>
                            </md-table-toolbar>
                            <md-table-row v-for="sms in numberList" :key="sms.number"
                                          style="cursor:pointer;"
                                          @click="smsDetail( sms.number)">

                                <md-table-cell v-if="sms.owner"
                                               :class="sms.number === selectedNumber?  'active':''">
                                    <img :data-letters="sms.owner.name[0] +sms.owner.surname[0]" src="" alt="">
                                    {{sms.owner.name}}
                                    {{sms.owner.surname}}
                                    <small>({{sms.number}})</small>

                                </md-table-cell>
                                <md-table-cell v-else>
                                    <span data-letters="??" src="" alt=""></span>
                                    {{ sms.number}}
                                    <small style="position: absolute; right: 1vw;" class="badge badge-info"> {{
                                        sms.total}}
                                    </small>
                                </md-table-cell>


                            </md-table-row>
                        </md-table>
                    </div>
                </div>
                <div class="md-layout-item md-size-65">

                    <div class="scrollable" style="height: 75%!important;">
                        <div class="md-layout"
                             v-for="sms in list">

                            <div class="md-layout-item md-size-95"
                                 style="padding: 20px; background-color: #f5e8e8; margin:10px;  d-webkit-border-radius: 16px;-moz-border-radius: 16px;border-radius: 16px; "
                            >
                                {{sms.body}}
                            </div>

                            <div class="md-layout-item">{{sms.created_at}}</div>

                        </div>
                    </div>
                    <div class="md-layout md-gutter">
                        <div class="md-layout-item md-size-75">
                            <md-field>
                                <label>Message Text</label>
                                <md-textarea v-model="message"
                                ></md-textarea>
                            </md-field>
                        </div>
                        <div class="md-layout-item md-size-20">
                            <md-button @click="sendSms" class="md-raised md-primary"
                                       style=" height: 10vh; width: 100%;">
                                Send
                            </md-button>


                        </div>


                    </div>


                </div>
            </div>

        </widget>
    </div>
</template>

<script>
    import Widget from '../../shared/widget'
    import {Smses} from '../../classes/Sms/SmsList'
    import {EventBus} from '../../shared/eventbus'
    import NewSms from './NewSms'
    import {resources} from '../../resources'
    import {SmsService} from "../../services/SmsService";

    const debounce = require('debounce');

    export default {
        name: 'List',
        components: {NewSms, Widget},
        watch: {
            filterNumber: debounce(function (e) {
                this.searchSms(this.filterNumber)
            }, 250),
        },

        mounted() {
            EventBus.$on('pageLoaded', this.reloadList)
            this.senderId = this.$store.state.admin.id;
            /*  this.loadList()*/
        },
        beforeDestroy() {
            EventBus.$off('pageLoaded', this.reloadList)
        },
        data() {
            return {
                smsService: new SmsService(),
                list: [],
                numberList: [],
                filterNumber: '',
                subscriber: 'smsList',
                showModal: false,
                selectedNumber: '',
                senderId: '',
                message: '',

            }
        },
        methods: {
            reloadList(subscriber, data) {

                if (subscriber !== this.subscriber) return;
                this.numberList = this.smsService.updateList(data);
                if (this.numberList.length > 0)
                    this.list = this.smsDetail(this.numberList[0].number)
            },
            async loadList() {
                this.list = [];
                this.numberList = [];
                try {
                    this.numberList = await this.smsService.getList();
                    if (this.numberList.length > 0)
                        this.list = this.smsDetail(this.numberList[0].number)

                } catch (e) {
                    this.alertNotify('error', e.message)
                }

            },
            async smsDetail(phone) {
                this.selectedNumber = phone;
                this.list = await this.smsService.getDetail(phone)

            },
            async sendSms() {
                if (this.message.length <= 3) {

                    this.alertNotify('warn', 'Message should contain more than 3 letters');
                    return
                }
                try {
                    await this.smsService.sendToPerson('person', this.message, this.selectedNumber, this.senderId);
                    this.alertNotify('success', 'The Sms is send out');
                    this.message = '';
                    this.smsDetail(this.selectedNumber)
                } catch (e) {
                    this.alertNotify('error', e.message)
                }

            },
            alertNotify(type, message) {
                this.$notify({
                    group: "notify",
                    type: type,
                    title: type + " !",
                    text: message
                });
            },
            searchSms(text) {
                this.numberList = this.smsService.searchSms(text)
            }
        }
    }
</script>

<style scoped>
    .scrollable {
        max-height: 80vh !important;
        overflow-y: scroll;
    }

    [data-letters]:before {
        content: attr(data-letters);
        display: inline-block;
        font-size: 1em;
        width: 2.5em;
        height: 2.5em;
        line-height: 2.5em;
        text-align: center;
        border-radius: 50%;
        background: plum;
        vertical-align: middle;
        margin-right: 1em;
        color: white;
    }


    td.active {
        background-color: #0000FF !important;
        color: #FFF;
    }

</style>
