<template>
    <div>
        <NewConnectionGroup/>
        <widget
            :id="'connection-Groups-list'"
            :title="'Connection Groups'"
            :paginator="connectionGroups.paginator"
            :subscriber="subscriber"
            :button="true"
            :button-text="'New Connection Group'"
            :callback="addNew"
            :color="'green'"
        >
            <md-table md-card style="margin-left: 0">
                <md-table-row>
                    <md-table-head>#</md-table-head>
                    <md-table-head>ID</md-table-head>
                    <md-table-head>Name</md-table-head>
                    <md-table-head></md-table-head>
                </md-table-row>
                <md-table-row v-for="Group,index in connectionGroups" :key="Group.id">
                    <md-table-cell> {{ index+1 }}</md-table-cell>
                    <md-table-cell> {{ Group.id}}</md-table-cell>
                    <md-table-cell>
                        <div v-if="editConnectionGroup === Group.id">
                            <md-field :class="{'md-invalid': errors.has('ConnectionGroup')}">
                                <label for="ConnectionGroup">Edit Connection Group Name</label>
                                <md-input
                                    id="ConnectionGroup"
                                    name="ConnectionGroup"
                                    v-model="Group.name"
                                    v-validate="'required|min:3'"
                                />
                                <span class="md-error">{{ errors.first('ConnectionGroup') }}</span>

                            </md-field>
                        </div>
                        <div v-else>
                            {{ Group.name}}
                        </div>

                        </md-table-cell>
                    <md-table-cell>
                        <div v-if="editConnectionGroup === Group.id">
                            <md-button class="md-icon-button" @click="updateConnectionGroup(Group)">
                                <md-icon>save</md-icon>
                            </md-button>
                            <md-button class="md-icon-button" @click="editConnectionGroup = null">
                                <md-icon>close</md-icon>
                            </md-button>
                        </div>
                        <div v-else>
                            <md-button class="md-icon-button" @click="editConnectionGroup = Group.id">
                                <md-icon>edit</md-icon>
                            </md-button>
                        </div>
                    </md-table-cell>

                </md-table-row>

            </md-table>


        </widget>
    </div>

</template>

<script>
    import Widget from '../../shared/widget'
    import {EventBus} from '../../shared/eventbus'
    import TableList from '../../shared/TableList'
    import {ConnectionGroupService} from '../../services/ConnectionGroupService'
    import NewConnectionGroup from './NewConnectionGroup'

    export default {
        name: 'ConnectionGroupsList',
        components: {TableList, Widget, NewConnectionGroup},
        mounted() {
            EventBus.$on('pageLoaded', this.reloadList)
            EventBus.$on('searching', this.searching)
            EventBus.$on('end_searching', this.endSearching)
            EventBus.$on('connectionGroupAdded', this.getConnectionGroups)

            this.getConnectionGroups()
        },

        data() {
            return {
                connectionGroupService: new ConnectionGroupService(),
                subscriber: 'connection-Groups-list',
                connectionGroups: [],
                editConnectionGroup:null
            }
        },
        methods: {
            alertNotify(type, message) {
                this.$notify({
                    group: 'notify',
                    type: type,
                    title: type + ' !',
                    text: message
                })
            },
            checkConfirm(result){
                return 'value' in result
            },
            async updateConnectionGroup(connectionGroup){
                let validator = await this.$validator.validateAll()
                if (!validator) {

                    return
                }
                this.$swal({
                    type: 'question',
                    title: 'Edit Connection Group ',
                    text: 'Are you sure to changing this sub connection group name ',
                    showCancelButton: true,
                    cancelButtonText: 'No',
                    confirmButtonText: 'Yes'
                }).then(response => {
                    if(this.checkConfirm(response)){
                        try {
                            this.connectionGroupService.updateConnectionGroup(connectionGroup)
                            this.editConnectionGroup = null
                            this.alertNotify('success', 'Connection Group Updated Successfully')
                        }catch (e) {
                            this.alertNotify('error', e)
                        }
                    }else{
                        return
                    }


                })

            },
            reloadList(subscriber, data) {
                if (subscriber !== this.subscriber) return
                this.connectionGroups = this.connectionGroupService.updateList(data)
            },
            async getConnectionGroups() {
                try {
                    this.connectionGroups = await this.connectionGroupService.getConnectionGroups()

                } catch (e) {

                    this.alertNotify('error', e.message)
                }
            },
            addNew() {
                EventBus.$emit('showNewConnectionGroup')
            },

        },

    }
</script>

<style scoped>

</style>
