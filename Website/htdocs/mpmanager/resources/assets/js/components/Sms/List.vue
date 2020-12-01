<template>
    <div>

        <widget
            :title="$tc('phrases.smsList')"
            :subscriber="subscriber"
            @widgetAction="() => {this.showModal = true}"
            color="green"
            :paginator="smsService.paginator"
            :route_name="'/sms/list'"
            :button="false"
        >

            <div class="md-layout md-gutter" v-if="!showModal">
                <div class="md-layout-item md-size-35 " style="min-height: 95vh!important;
    border-right: 1px solid #d6d6d6;">
                    <div class="scrollable">

                                <md-table>
                                    <md-table-toolbar>
                                        <md-field>
                                            <md-input v-model="filterNumber" type="text" class="form-control"
                                                      :placeholder="$tc('words.search')"
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
                             v-for="sms in list" :key="sms.id">

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
                                <label>{{ $tc('phrases.messageText') }}</label>
                                <md-textarea v-model="message"
                                ></md-textarea>
                            </md-field>
                            <md-progress-bar md-mode="indeterminate" v-if="loading"/>
                        </div>
                        <div class="md-layout-item md-size-20">
                            <md-button @click="sendSms" :disabled="loading" class="md-raised md-primary"
                                       style=" height: 10vh; width: 100%;">
                                {{ $tc('words.send') }}
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
import { EventBus } from '../../shared/eventbus'
import { SmsService } from '../../services/SmsService'

const debounce = require('debounce')

export default {
    name: 'List',
    components: {  Widget },
    watch: {
        filterNumber: debounce(function () {
            this.searchSms(this.filterNumber)
        }, 250),
    },

    mounted () {
        EventBus.$on('pageLoaded', this.reloadList)
        this.senderId = this.$store.state.admin.id
        /*  this.loadList()*/
    },
    beforeDestroy () {
        EventBus.$off('pageLoaded', this.reloadList)
    },
    data () {
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
            headers: [],
            tableName: 'SMS',
            loading: false
        }
    },
    methods: {
        reloadList (subscriber, data) {

            if (subscriber !== this.subscriber) return
            this.numberList = this.smsService.updateList(data)
            EventBus.$emit('widgetContentLoaded',this.subscriber,this.numberList.length)
            if (this.numberList.length > 0)
                this.list = this.smsDetail(this.numberList[0].number)
        },
        async loadList () {
            this.list = []
            this.numberList = []
            try {
                this.numberList = await this.smsService.getList()
                if (this.numberList.length > 0)
                    this.list = this.smsDetail(this.numberList[0].number)

            } catch (e) {
                this.alertNotify('error', e.message)
            }

        },
        async smsDetail (phone) {
            this.selectedNumber = phone
            this.list = await this.smsService.getDetail(phone)

        },
        async sendSms () {
            if (this.message.length <= 3) {
                this.alertNotify('warn', this.$tc('phrases.smsListNotify',1))
                return
            }
            try {
                this.loading = true
                await this.smsService.sendToNumber('person', this.message, this.selectedNumber, this.senderId)
                this.loading = false
                this.alertNotify('success', this.$tc('phrases.smsListNotify',2))
                this.message = ''
                this.smsDetail(this.selectedNumber)
            } catch (e) {
                this.loading = false
                this.alertNotify('error', e.message)
            }

        },
        alertNotify (type, message) {
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message
            })
        },
        searchSms (text) {
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
