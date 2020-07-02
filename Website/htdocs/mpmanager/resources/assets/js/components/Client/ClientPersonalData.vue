<template>

    <widget
        title="Details"
        :button="true"
        button-text="Delete Person"
        color="red"
        :callback="confirmDelete">

        <md-card>
            <md-card-content>
                <div class="md-layout md-gutter" v-if="!editPerson">
                    <div class="md-layout-item md-large-size-15 md-medium-size-20 md-small-size-25">
                        <font-awesome-icon icon="user" class="fa-4x"/>
                    </div>
                    <div class="md-layout-item md-size-65">
                        <h3>{{person.title }} {{ person.name}} {{person.surname}}</h3>
                    </div>
                    <div class="md-layout-item md-large-size-20 md-medium-size-15 md-small-size-10">
                        <md-button @click="editPerson=true" class="md-icon-button" style="float: right">
                            <font-awesome-icon icon="pen"/>
                        </md-button>
                    </div>
                    <div class="md-layout-item md-size-100">&nbsp;</div>
                    <div class="md-layout-item md-size-15">
                        <font-awesome-icon icon="venus-mars"/>&nbsp;Gender:
                    </div>
                    <div class="md-layout-item md-size-15">
                        {{person.gender}}
                    </div>

                    <div class="md-layout-item md-size-20">
                        <font-awesome-icon icon="graduation-cap"/>&nbsp;Education:
                    </div>
                    <div class="md-layout-item md-size-15">
                        {{person.education}}
                    </div>

                    <div class="md-layout-item md-size-15">
                        <font-awesome-icon icon="birthday-cake"/>&nbsp;Birth Date:
                    </div>
                    <div class="md-layout-item md-size-15">
                        {{person.birthDate}}
                    </div>

                </div>

                <div class="md-layout md-gutter" v-else>
                    <div class="md-layout-item md-size-100">
                        <form novalidate class="md-layout" @submit.prevent="updatePerson">
                            <md-card class="md-layout-item md-size-100">
                                <md-card-content>
                                    <md-field>
                                        <label for="person-title">Title</label>
                                        <md-input
                                            type="text"
                                            name="person-title"
                                            id="person-title"
                                            v-model="person.title"
                                        />
                                    </md-field>

                                    <md-field>
                                        <label for="name">Name</label>
                                        <md-input type="text" name="name" id="name" v-model="person.name"/>
                                    </md-field>

                                    <md-field>
                                        <label for="surname">Surname</label>
                                        <md-input type="text" name="surname" id="surname" v-model="person.surname"/>
                                    </md-field>

                                    <md-datepicker name="birthDate" v-model="person.birthDate">
                                        <label for="birth-date">Birth Date :</label>
                                    </md-datepicker>

                                    <md-field>
                                        <label for="gender">Gender :</label>
                                        <md-select name="gender" id="gender" v-model="person.gender">
                                            <md-option disabled v-if="person.gender==null">-- Select --</md-option>
                                            <md-option value="male">Male</md-option>
                                            <md-option value=" female">Female</md-option>
                                        </md-select>
                                    </md-field>

                                    <md-field>
                                        <label for="education">Education</label>
                                        <md-input
                                            type="text"
                                            name="education"
                                            id="education"
                                            v-model="person.education"
                                        />
                                    </md-field>
                                </md-card-content>
                                <md-card-actions>
                                    <md-button type="submit" class="md-primary btn-save">Save</md-button>
                                    <md-button type="button" @click="editPerson = false" class="md-accent btn-save">
                                        Cancel
                                    </md-button>
                                </md-card-actions>
                            </md-card>
                        </form>
                    </div>
                </div>

            </md-card-content>
        </md-card>

    </widget>

</template>

<script>
    import Datepicker from 'vuejs-datepicker'
    import Widget from '../../shared/widget'

    export default {
        name: 'ClientPersonalData',
        components: {
            Widget,
            Datepicker
        },
        props: {
            person: {
                required: true
            }
        },
        mounted () {},
        data () {
            return {
                editPerson: false
            }
        },
        methods: {
            updatePerson () {
                this.editPerson = false

                this.person.updatePerson()
            },
            confirmDelete () {
                this.$swal({
                    type: 'question',
                    title: 'Delete Customer',
                    width: '35%',
                    confirmButtonText: 'Confirm',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel',
                    focusCancel: true,
                    html:
                        '<div style="text-align: left; padding-left: 5rem" class="checkbox">' +
                        '  <label>' +
                        '    <input type="checkbox" name="confirmation" id="confirmation" >' +
                        '   I confirm that ' +
                        this.person.name +
                        ' ' +
                        this.person.surname +
                        ' will be deleted' +
                        '  </label>' +
                        '</div>'
                }).then(result => {
                    let answer = document.getElementById('confirmation').checked
                    if ('value' in result) {
                        //delete customer
                        if (answer) {
                            this.deletePerson()
                        } else {
                            //not confirmed
                        }
                    }
                })
            },
            deletePerson () {
                axios.delete(resources.person.delete + this.person.id).then(response => {
                    this.showConfirmation()
                })
            },
            showConfirmation () {
                const Toast = this.$swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    onOpen: toast => {
                        toast.addEventListener('mouseenter', this.$swal.stopTimer)
                        toast.addEventListener('mouseleave', this.$swal.resumeTimer)
                    }
                })

                Toast.fire({
                    type: 'success',
                    title: 'Customer Deleted successfully'
                }).then(x => {
                    window.history.back()
                })
            }
        }
    }
</script>

<style>
</style>
