<template>
    <div>
        <widget
            :id="'connection-type-detail'"
            :title="'Connection Type Details'"
            :subscriber="subscriber.detail"
        >
            <md-card>
                <md-card-content>
            <div class="md-layout md-gutter">
                <div class="md-layout-item md-size-50">
                   <div class="md-layout">
                       <div class="md-layout-item md-subheader">ID</div>
                       <div class="md-layout-item md-subheader n-font">{{subConnectionType.connection_type_id}}</div>
                   </div>
                    <hr class="hr-d">
                    <div class="md-layout">
                       <div class="md-layout-item md-subheader">Name</div>
                       <div v-if="editConnectionType" class="md-layout-item md-subheader n-font">
                           {{connectionType.name}}
                           <md-button class="md-icon-button" @click="toggleEditConnectionType">
                               <md-icon>edit</md-icon>
                           </md-button>

                       </div>
                        <div v-else class="md-layout-item md-subheader n-font">
                            <md-field :class="{'md-invalid': errors.has('connectionTypeName')}">
                                <label for="connectionTypeName">Edit Connection Type Name</label>
                                <md-input
                                    id="connectionTypeName"
                                    name="connectionTypeName"
                                    v-model="connectionType.name"
                                    v-validate="'required|min:3'"
                                />
                                <span class="md-error">{{ errors.first('connectionTypeName') }}</span>
                                <md-button class="md-icon-button" @click="editConnectionTypeName">
                                    <md-icon>save</md-icon>
                                </md-button>
                                <md-button class="md-icon-button" @click="toggleEditConnectionType">
                                    <md-icon>close</md-icon>
                                </md-button>
                            </md-field>

                        </div>
                   </div>
                    <hr class="hr-d">
                    <div class="md-layout">
                       <div class="md-layout-item md-subheader">Created Date</div>
                       <div class="md-layout-item md-subheader n-font">{{formatReadableDate(connectionType.created_at)}}</div>
                   </div>
                </div>
                <div class="md-layout-item md-size-50">
                    <div class="md-layout">
                        <div class="md-layout-item md-subheader">Meters</div>
                        <div class="md-layout-item md-subheader n-font">{{connectionType.meter_parameters_count}}</div>
                    </div>
                    <hr class="hr-d">
                    <div class="md-layout">
                        <div class="md-layout-item md-subheader">Sub Types</div>
                        <div class="md-layout-item md-subheader n-font">{{subConnectionTypeService.subConnectionTypes.length}}</div>
                    </div>

                </div>
            </div>
                </md-card-content>
            </md-card>
        </widget>
        <widget
            :title="'Add New Sub Connection Type' "
            :id="'add-sub-connection-type'"
            color="red"
            v-if = "showNewSubType"
        >
            <md-card>

                <md-card-content>
                    <div class="md-layout md-gutter">
                        <div class="md-layout-item md-size-50">
                            <md-field :class="{'md-invalid': errors.has('name')}">
                                <label for="name">Sub Connection Name</label>
                                <md-input
                                    id="name"
                                    name="name"
                                    v-model="subConnectionType.name"
                                    v-validate="'required|min:3'"
                                />
                                <span class="md-error">{{ errors.first('name') }}</span>
                            </md-field>

                        </div>
                        <div class="md-layout-item md-size-50">
                            <md-field :class="{'md-invalid': errors.has('tariff')}">
                                <label for="tariff">Select Tariff</label>
                                <md-select v-model="subConnectionType.tariff_id" v-validate="'required'" name="tariff" id="tariff">
                                    <md-option v-for="t in tariff" :key="t.id" :value="t.id">{{t.name}}</md-option>
                                </md-select>
                                <span class="md-error">{{ errors.first('tariff') }}</span>
                            </md-field>
                        </div>
                    </div>
                </md-card-content>

                <md-card-actions>
                    <md-button role="button" class="md-raised md-primary" @click="saveSubType(subConnectionType)">Save
                    </md-button>
                    <md-button role="button" class="md-raised" @click="addSubType">Close</md-button>
                </md-card-actions>
            </md-card>

        </widget>
        <widget
            :title="'Sub Connection Types'"
            :id="'sub-connection-types'"
            :button="true"
            :button-text="'New Sub Connection Type'"
            @widgetAction="addSubType"
            :color="'green'"
            :subscriber="subscriber.subTypes">
            <md-card>
                <md-card-content>
                        <md-table>
                            <md-table-row>
                                <md-table-head>#</md-table-head>
                                <md-table-head>ID</md-table-head>
                                <md-table-head>Name</md-table-head>
                                <md-table-head>Tariff Name</md-table-head>
                                <md-table-head></md-table-head>

                            </md-table-row>
                            <md-table-row v-for="(subType,index) in subConnectionTypeService.subConnectionTypes" :key="index">
                                <md-table-cell>{{index+1}}</md-table-cell>
                                <md-table-cell>{{subType.id}}</md-table-cell>
                                <md-table-cell>
                                    <div v-if="editSubConnectionType === subType.id">
                                        <md-field :class="{'md-invalid': errors.has('subConnectionTypeName')}">
                                            <label for="subConnectionTypeName">Edit Sub Connection Type Name</label>
                                            <md-input
                                                id="subConnectionTypeName"
                                                name="subConnectionTypeName"
                                                v-model="subType.name"
                                                v-validate="'required|min:3'"
                                            />
                                            <span class="md-error">{{ errors.first('subConnectionTypeName') }}</span>

                                        </md-field>
                                    </div>
                                    <div v-else>
                                        {{subType.name}}
                                    </div>
                                </md-table-cell>
                                <md-table-cell>
                                    <div v-if="editSubConnectionType === subType.id">
                                        <md-field :class="{'md-invalid': errors.has('tariff')}">
                                            <label for="tariff">Select Tariff</label>
                                            <md-select v-model="subType.tariff_id" v-validate="'required'" name="tariff" id="tariff">
                                                <md-option v-for="t in tariff" :key="t.id" :value="t.id">{{t.name}}</md-option>
                                            </md-select>
                                            <span class="md-error">{{ errors.first('tariff') }}</span>
                                        </md-field>
                                    </div>
                                    <div v-else>
                                        {{subType.tariff.name}}
                                    </div>


                                </md-table-cell>
                                <md-table-cell>
                                    <div v-if="editSubConnectionType === subType.id">
                                        <md-button class="md-icon-button" @click="updateSubConnectionType(subType)">
                                            <md-icon>save</md-icon>
                                        </md-button>
                                        <md-button class="md-icon-button" @click="editSubConnectionType = null">
                                            <md-icon>close</md-icon>
                                        </md-button>
                                    </div>
                                    <div v-else>
                                        <md-button class="md-icon-button" @click="editSubConnectionType = subType.id">
                                            <md-icon>edit</md-icon>
                                        </md-button>
                                    </div>
                                </md-table-cell>
                            </md-table-row>
                        </md-table>
                </md-card-content>
            </md-card>
        </widget>

    </div>
</template>

<script>
import Widget from '../../shared/widget'
import {SubConnectionTypeService} from '../../services/SubConnectionTypeService'
import {ConnectionTypeService} from '../../services/ConnectionTypeService'
import {TariffService} from '../../services/TariffService'
import moment from 'moment'
import { EventBus } from '../../shared/eventbus'

export default {
    name: 'ConnectionTypeDetail',
    components: {  Widget },
    data() {
        return{
            subscriber:{
                detail:'connection-type-detail',
                subTypes:'sub-connection-types'
            },
            connectionTypeService: new ConnectionTypeService(),
            subConnectionTypeService: new SubConnectionTypeService(),
            tariffService: new TariffService(),
            showNewSubType: false,
            newConnectionTypeName:null,
            editConnectionType: true,
            editSubConnectionType:null,
            subConnectionType:{
                name:null,
                connection_type_id:null,
                tariff_id:null
            },
            selectedTariff:{},
            connectionTypeId:null,
            connectionType:[],
            tariff:null,

        }
    },
    created () {
        this.subConnectionType.connection_type_id = this.$route.params.id
        console.log(this.subConnectionType.connection_type_id)
        this.getSubConnectionTypes(this.subConnectionType.connection_type_id)
        this.getConnectionTypeDetail(this.subConnectionType.connection_type_id)
        this.getTariffs()
    },
    methods:{
        checkConfirm(result){
            return 'value' in result
        },
        formatReadableDate (date) {
            return moment(date).format('MMMM Do YYYY, h:mm:ss a')
        },
        async updateSubConnectionType(subType){
            let validator = await this.$validator.validateAll()
            if (!validator) {

                return
            }
            this.$swal({
                type: 'question',
                title: 'Edit Sub Connection Type ',
                text: 'Are you sure to changing this sub connection type',
                showCancelButton: true,
                cancelButtonText: 'No',
                confirmButtonText: 'Yes'
            }).then(response => {
                if(this.checkConfirm(response)){
                    try {
                        this.subConnectionTypeService.updateSubConnectionType(subType)
                        this.editSubConnectionType = null
                        this.alertNotify('success', 'Sub Connection Updated Successfully')
                    }catch (e) {
                        this.alertNotify('error', e.message)
                    }
                }else{
                    return
                }
            })
        },
        async editConnectionTypeName(){
            let validator = await this.$validator.validateAll()
            if (!validator) {
                return
            }
            this.$swal({
                type: 'question',
                title: 'Edit Sub Connection Type ',
                text: 'Are you sure to change of connection type name for ' + this.connectionType.name + '?',
                showCancelButton: true,
                cancelButtonText: 'No',
                confirmButtonText: 'Yes'
            }).then(response => {
                if(this.checkConfirm(response))
                {
                    try {
                        this.connectionTypeService.updateConnectionType(this.connectionType)
                        this.toggleEditConnectionType()
                        this.alertNotify('success', 'Connection Type Name Updated Successfully ')
                    }catch (e) {
                        this.alertNotify('error', e.message)
                    }
                }else{
                    return
                }

            })

        },
        toggleEditConnectionType(){
            this.editConnectionType = !this.editConnectionType
            this.newConnectionTypeName = this.connectionType.name
        },
        toggleEditSubConnectionType(){
            this.editSubConnectionType = !this.editConnectionType
        },
        addSubType() {
            this.showNewSubType = !this.showNewSubType
        },
        clearForm(){
            this.subConnectionType.name=null,
            this.subConnectionType.tariff_id=null
        },
        async saveSubType(subConnectionType) {
            let validator = await this.$validator.validateAll()
            if (!validator) {
                return
            }
            try {
                await this.subConnectionTypeService.createSubConnectionType(subConnectionType)
                this.alertNotify('success', 'SubConnectionType has registered.')
                this.addSubType()
                this.clearForm()
            }catch (e) {
                this.alertNotify('error', e.message)
            }

        },
        async getTariffs(){
            try {
                this.tariff = await this.tariffService.getTariffs()
            }catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async getConnectionTypeDetail(connectionTypeId){
            try {
                this.connectionType = await this.connectionTypeService.getConnectionTypeDetail(connectionTypeId)
                EventBus.$emit('widgetContentLoaded',this.subscriber.detail,this.connectionType)
            }catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async getSubConnectionTypes(connectionTypeId){
            try {
                await this.subConnectionTypeService.getSubConnectionTypes(connectionTypeId)
                EventBus.$emit('widgetContentLoaded',this.subscriber.subTypes,this.subConnectionTypeService.subConnectionTypes.length)
            } catch (e) {
                this.alertNotify('error', e.message)
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
.n-font {
    font-weight: 100 !important;
}

.hr-d {
    height: 1pt;
    margin: auto;
    padding: 0;
    display: block;
    border: 0;
    /* transition: margin-left .3s cubic-bezier(.4,0,.2,1); */
    /* will-change: margin-left; */
    background-color: rgba(0, 0, 0, 0.12);
}
</style>
