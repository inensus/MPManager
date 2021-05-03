<template>
    <div>
        <widget :title="$tc('phrases.newSms')" color="red">
            <md-card class="md-size-80">
                <md-card-header>
                    <md-list class="mobile-tabs">
                        <md-list-item @click="tab='person'">{{$tc('words.people')}}</md-list-item>
                        <md-list-item @click="tab='group'">{{$tc('phrases.connectionGroup')}}</md-list-item>
                        <md-list-item @click="tab='type'">{{$tc('phrases.connectionType')}}</md-list-item>
                        <md-list-item @click="tab='all'">{{$tc('phrases.wholeVillage')}}</md-list-item>
                    </md-list>
                    <md-tabs class="tabs">
                        <md-tab
                            role="presentation"
                            :class="tab==='person' ? 'active' :''"
                            :md-label="$tc('words.people')"
                            @click="tab='person'"
                            exact
                        ></md-tab>
                        <md-tab
                            role="presentation"
                            :class="tab==='group' ? 'active' :''"
                            :md-label="$tc('phrases.connectionGroup')"
                            @click="tab='group'"
                        ></md-tab>
                        <md-tab
                            role="presentation"
                            :class="tab==='type' ? 'active' :''"
                            :md-label="$tc('phrases.connectionType')"
                            @click="tab='type'"
                        ></md-tab>
                        <md-tab
                            role="presentation"
                            :class="tab==='all' ? 'active' :''"
                            :md-label="$tc('phrases.wholeVillage')"
                            @click="tab='all'"
                        ></md-tab>
                    </md-tabs>
                </md-card-header>
                <md-card-content>
                    <div class="md-layout">
                        <!-- minigrid select should be displayed on connection groups and connection types too-->
                        <div class="md-layout-item md-size-100" v-if="tab !=='person'">
                            <md-field>
                                <label>{{ $tc('words.miniGrid') }}</label>
                                <md-select id="miniGrid_select" v-model="miniGrid">
                                    <md-option value="0">{{ $tc('words.all') }}</md-option>
                                    <md-option v-for="miniGrid in miniGridService.miniGrids"
                                               :value="miniGrid.id"
                                               :key="miniGrid.id">
                                        {{ miniGrid.name }}
                                    </md-option>
                                </md-select>
                            </md-field>
                        </div>
                        <div class="md-layout-item md-size-100" v-if="tab ==='person'">
                            <div class="row">
                                <span v-for="receiver in smsService.receiverList" style="margin: 3px;"
                                      :key="receiver.display">
                                    <button
                                        @click="smsService.removeReceiver(receiver)"
                                        class="btn btn-primary btn-sm"
                                        style="margin: 0.3vh 0;">
                                            {{ receiver.display }}
                                    </button>
                                </span>
                            </div>
                            <md-autocomplete
                                v-model="customerSearchTerm"
                                :md-options="smsService.resultList"
                                @md-changed="searchForPerson"
                                @md-selected="addReceiver"
                            >
                                <label for="receiver">{{ $tc('words.receiver') }}</label>
                                <template slot="md-autocomplete-item" slot-scope="{ item }" id="receiver">
                                    {{ item.display }}
                                </template>
                            </md-autocomplete>
                        </div>
                        <div class="md-layout-item md-size-100" v-if="tab==='type' || tab ==='group'">
                            <md-field>
                                <label>{{ $tc('words.receiver') }}</label>
                                <md-select id="receiver_select" v-model="smsService.receiverList">
                                    <md-option value selected>-- {{ $tc('words.select') }} --</md-option>
                                    <md-option v-for="connection_group in smsService.resultList"
                                               :value="connection_group.id"
                                               :key="connection_group.id">
                                        {{ connection_group.display }}
                                    </md-option>
                                </md-select>
                            </md-field>
                        </div>
                        <div class="md-layout-item md-size-100">
                            <md-field>
                                <label for="message">{{ $tc('words.message') }}</label>
                                <md-textarea
                                    rows="10"
                                    class="form-control"
                                    id="message"
                                    v-model="message"
                                    placeholder="Message"
                                ></md-textarea>
                            </md-field>
                        </div>
                    </div>
                </md-card-content>
                <md-card-actions>
                    <div class="md-layout">
                        <md-button class="md-raised md-primary" @click="sendConfirm">{{ $tc('words.send') }}</md-button>
                    </div>
                </md-card-actions>
            </md-card>
        </widget>
    </div>
    <!-- modal -->
</template>

<script>
import Widget from '../../shared/widget'
import {MiniGridService} from '../../services/MiniGridService'
import {SmsService} from '../../services/SmsService'
const debounce = require('debounce')

export default {
    name: 'NewSms',
    components: {Widget},
    props: {
        show: {
            type: Boolean,
            default: false,
        }
    },
    mounted() {
        this.getMiniGrids()
    },
    data() {
        return {
            customerSearchTerm: '',
            smsService: new SmsService(),
            miniGridService: new MiniGridService(),
            message: '',
            tab: 'person',
            miniGrid: 0,
            senderId: this.$store.getters['auth/getAuthenticateUser'].id
        }
    },

    watch: {
        tab: function () {
            this.smsService.resetLists()
            if (this.tab === 'group') {
                this.searchForConnectionGroup()
            } else if (this.tab === 'type') {
                this.searchForConnectionType()
            }
        },
    },
    methods: {
        getMiniGrids() {
            try {
                this.miniGridService.getMiniGrids()
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },

        searchForPerson: debounce(function (input) {
            if (input.length < 3) {
                return
            }
            this.smsService.searchPerson(input)
            return this.smsService.resultList
        }, 500),

        searchForConnectionGroup() {
            try {
                this.smsService.connectionGroupList()
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },

        async searchForConnectionType() {
            try {
                await this.smsService.connectionTypeList()
            } catch (e) {
                this.alertNotify('error', e.message)
            }
        },

        addReceiver(receiver) {
            this.smsService.addReceiver(receiver)
        },

        sendConfirm() {
            this.$swal({
                type: 'question',
                allowOutsideClick: false,
                title: this.$tc('phrases.bulkSms', 0),
                text: this.$tc('phrases.bulkSms', 1),
                cancelButtonText: this.$tc('words.cancel'),
                showCancelButton: true,
            }).then((value) => {
                if (value.value === true)
                    this.send()
            })
        },
        send() {
            try {
                this.smsService.sendBulk(this.tab, this.message, this.senderId, this.miniGrid)
                this.alertNotify('success', this.$tc('phrases.bulksms', 2))
            }
            catch (exception) {
                this.alertNotify('error', 'SMS service failed with following error' +exception.message)
            }
        },
        alertNotify(type, message) {
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message
            })
        },
    }
}
</script>

<style scoped>
.comment-box {
    border-bottom: 1px dotted #ccc;
    padding: 5px;
    margin-bottom: 5px;
}

.modal-mask {
    position: fixed;
    z-index: 1001;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: table;
    transition: opacity 0.3s ease;
}

.modal-wrapper {
    display: table-cell;
    vertical-align: middle;
}

.modal-container {
    margin: 0px auto;
    padding: 20px 30px;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
    transition: all 0.3s ease;
    font-family: Helvetica, Arial, sans-serif;
    max-height: 85%;
    overflow-y: scroll;
}

@media only screen and (max-width: 1024px) {
    .modal-container {
        width: 99% !important;
    }
}

@media only screen and (min-width: 1024px) {
    .modal-container {
        width: 55% !important;
    }
}

.modal-header h3 {
    margin-top: 0;
    color: #42b983;
}

.modal-body {
    margin: 20px 0;
}

.modal-default-button {
    float: right;
}

/*
     * The following styles are auto-applied to elements with
     * transition="modal" when their visibility is toggled
     * by Vue.js.
     *
     * You can easily play with the modal transition by editing
     * these styles.
     */

.modal-enter {
    opacity: 0;
}

.modal-leave-active {
    opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
    -webkit-transform: scale(1.1);
    transform: scale(1.1);
}
@media screen and (max-width: 600px){
    .tabs{
        display: none;
    }
}
@media screen and (min-width: 601px){
    .mobile-tabs{
        display: none;
    }
}
</style>
