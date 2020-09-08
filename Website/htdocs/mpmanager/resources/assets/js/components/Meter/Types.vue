<template>
    <div>
        <widget v-if="toggleNewType"
                id="add-new-meter-type"
                title="Add New Meter Type"

        >
            <md-card>
                <md-card-content>
                    <div class="md-layout md-gutter md-size-100">
                        <div class="md-layout-item md-size-40">
                            <md-field :class="{'md-invalid': errors.has('max_current')}">
                                <label>Max Current</label>
                                <md-input v-model="meterType.max_current"
                                          id="max_current"
                                          name="max_current"
                                          v-validate="'required|numeric'" ></md-input>
                                <span class="md-error">{{ errors.first('max_current') }}</span>
                                <span class="md-suffix">Amper</span>
                            </md-field>
                        </div>
                        <div class="md-layout-item md-size-40">
                            <md-field :class="{'md-invalid': errors.has('phase')}">
                                <label>Phase</label>
                                <md-input v-model="meterType.phase"
                                          id="phase"
                                          name="phase"
                                          v-validate="'required|numeric'"></md-input>
                                <span class="md-error">{{ errors.first('phase') }}</span>
                            </md-field>
                        </div>
                        <div class="md-layout-item md-size-10">
                            <span class="md-subheader">
                                <md-checkbox v-model="online" class="md-primary">Online</md-checkbox>
                            </span>

                        </div>
                        <div class="md-layout-item md-size-10">
                            <md-button class="md-primary md-dense md-raised" @click="saveMeterType">Save</md-button>
                        </div>
                    </div>
                </md-card-content>


            </md-card>


        </widget>
        <widget id="meter-types-list"
                title="Meter Types"
                :button="true"
                buttonText="Add New Type"
                :callback="showNewType"
                color="red">
            <div v-if="meterTypesList !== null">
                <md-table>
                    <md-table-row>
                        <md-table-head>ID</md-table-head>
                        <md-table-head>Name</md-table-head>
                        <md-table-head>Max Current</md-table-head>
                        <md-table-head>Online</md-table-head>
                    </md-table-row>
                    <md-table-row v-for="(type,index) in meterTypesList">
                        <md-table-cell>{{index+1}}</md-table-cell>
                        <md-table-cell>{{type.name}}</md-table-cell>
                        <md-table-cell>{{type.max_current}}</md-table-cell>
                        <md-table-cell><md-checkbox
                            v-model="type.online === 0 ? true : false"
                            disabled>{{type.online === 0 ? 'Online' : 'Offline'}}</md-checkbox></md-table-cell>
                    </md-table-row>


                </md-table>
            </div>
            <div v-else>

                <md-empty-state
                    md-description="There is no available Meter Type"
                    md-icon="devices_other"
                    md-label="Create your first Meter Type"
                >
                    <md-button
                               @click="showNewType"
                               class="md-primary md-raised">Add New Type</md-button>
                </md-empty-state>
            </div>




        </widget>
    </div>

</template>

<script>

  import Widget from '../../shared/widget'
  import NoTableData from '../../shared/NoTableData'
  import { MeterTypeService } from '../../services/MeterTypeService'

  export default {
    name: 'Types',
    components: { Widget, NoTableData},
    data(){
        return {
            meterTypeService: new MeterTypeService(),
            toggleNewType:false,
            meterType:{
                max_current: null,
                phase: null,
                online: 0,
            },
            online:false,
            meterTypesList:null,

        }
    },
      mounted () {
        this.getMeterTypes()
      },
      methods:{
        showNewType(){
            this.toggleNewType = !this.toggleNewType
        },
       async saveMeterType(){
            let validation = await this.$validator.validateAll()
            if (!validation) {
                return
            }
            this.meterType.online = this.online ? 0 : 1
            try {
                this.meterTypesList = await this.meterTypeService.createMeterType(this.meterType)
                this.showNewType()
                this.meterType.max_current = null
                this.meterType.phase = null
                this.meterType.online = 0
                this.online = false
                this.alertNotify('success', 'Meter Type Added Successful ')
            }catch (e) {
                this.alertNotify('error', e.message)
            }

        },
        async getMeterTypes () {
              try {
                  this.meterTypesList = await this.meterTypeService.getMeterTypes()
              } catch (e) {
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
          }
    }
  }
</script>

<style scoped>

</style>
