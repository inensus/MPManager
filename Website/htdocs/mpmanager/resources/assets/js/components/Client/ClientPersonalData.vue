<template>

    <widget
        title="Details"
        :button="true"
        button-text="Delete Person"
        :callback="confirmDelete"
        button-icon="delete"
        :show-spinner="false">

        <md-card>
            <md-card-content>
                <div class="md-layout md-gutter" v-if="!editPerson">
                    <div class="md-layout-item md-large-size-15 md-medium-size-20 md-small-size-25">
                        <md-icon class="md-size-3x">account_circle</md-icon>
                    </div>
                    <div class="md-layout-item md-size-65">
                        <h2>{{person.title }} {{ person.name}} {{person.surname}}</h2>
                    </div>
                    <div class="md-layout-item md-large-size-20 md-medium-size-15 md-small-size-10">
                        <md-button @click="editPerson=true" class="md-icon-button" style="float: right">
                            <md-icon>create</md-icon>
                        </md-button>
                    </div>
                    <div class="md-layout-item md-size-100">&nbsp;</div>
                    <div class="md-layout-item md-size-15">
                        <md-icon>wc</md-icon> Gender:
                    </div>
                    <div class="md-layout-item md-size-15">
                        {{person.gender}}
                    </div>

                    <div class="md-layout-item md-size-20">
                        <md-icon>school</md-icon>&nbsp;Education:
                    </div>
                    <div class="md-layout-item md-size-15">
                        {{person.education}}
                    </div>

                    <div class="md-layout-item md-size-20">
                        <md-icon>cake</md-icon>&nbsp;Birth Date:
                    </div>
                    <div class="md-layout-item md-size-10">
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
                                    <md-button type="submit" @click="updatePerson" class="md-primary btn-save">Save</md-button>
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
import Widget from '../../shared/widget'
import { PersonService } from '../../services/PersonService'

export default {
    name: 'ClientPersonalData',
    components: {
        Widget,
    },
    props: {
        person: {
            required: true
        }
    },

    data () {
        return {
            personService: new PersonService(),
            editPerson: false
        }
    },
    methods: {
        updatePerson () {
            this.editPerson = false
            this.personService.updatePerson(this.person)

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
                if(response.status === 200){
                    this.showConfirmation()
                }

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
                console.log(x)
                window.history.back()
            })
        }
    }
}
</script>

<style>
</style>
