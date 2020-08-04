<template>
    <div>
        <widget
            :id="'client-addresses'"
            :title="'Addresses'"
            :button="true"
            :button-text="'new Address'"
            :button-color="'red'"
            :callback="addNewAddress"
            :paginator="addresses.paginator"
            :subscriber="subscriber"
        >
            <md-table style="width:100%" v-model="addresses.list" md-card md-fixed-header>
                <md-table-row @click="editAddress(item, index)" slot="md-table-row" slot-scope="{ item, index }">
                    <md-table-cell md-label="Id" md-sort-by="id" md-numeric>{{ item.id }}</md-table-cell>
                    <md-table-cell md-label="Street" md-sort-by="street">{{ item.street }}</md-table-cell>
                    <md-table-cell md-label="City" md-sort-by="city">{{ item.city }}</md-table-cell>
                    <md-table-cell md-label="Phone" md-sort-by="phone">{{ item.phone }}</md-table-cell>
                    <md-table-cell md-label="Is Primary" md-sort-by="primary">
                        <input type="checkbox" readonly :checked="item.primary" onclick="return false;"/>
                    </md-table-cell>
                </md-table-row>
            </md-table>
        </widget>
        <md-dialog :md-active.sync="modalVisibility">
            <md-dialog-title v-if="editFlag">Update Address</md-dialog-title>
            <md-dialog-title v-if="!editFlag">Register New Address</md-dialog-title>

            <div class="adress-edit-container">
                <form novalidate class="md-layout">
                    <vue-grid justify="around">
                        <vue-cell width="12of12">
                            <div class="md-layout md-gutter">
                                <div class="md-layout-item md-small-size-100">
                                    <md-field name="city">
                                        <label for="city">City</label>
                                        <md-select name="city" id="city" v-model="newAddress.city_id">
                                            <md-option
                                                value="0"
                                                disabled
                                                v-if="!editFlag || newAddress.city_id===null">
                                                City
                                            </md-option>
                                            <md-option
                                                v-for="city in cities"
                                                :key="city.id"
                                                :value="city.id"
                                            >{{city.name}}
                                            </md-option>
                                        </md-select>
                                    </md-field>
                                </div>

                                <div class="md-layout-item md-small-size-100">
                                    <md-field>
                                        <label for="Street">Street</label>
                                        <md-input type="text" id="Street" name="Street" v-model="newAddress.street"/>
                                    </md-field>
                                </div>
                            </div>
                        </vue-cell>

                        <vue-cell width="12of12">
                            <div class="md-layout md-gutter">
                                <div class="md-layout-item md-small-size-100">
                                    <md-field name="email">
                                        <label for="email">Email</label>
                                        <md-input type="email" name="email"
                                        v-model = "newAddress.email"/>
                                    </md-field>
                                </div>

                                <div class="md-layout-item md-small-size-100">
                                    <md-field name="phone">
                                        <label for="phone">Phone</label>
                                        <md-input
                                            name="phone"
                                            id="phone"
                                            v-model="newAddress.phone"
                                            data-mask="(999) 999-9999"
                                        />
                                    </md-field>
                                </div>
                            </div>
                        </vue-cell>

                        <vue-cell width="12of12">
                            <div class="md-layout md-gutter">
                                <div class="md-layout-item md-small-size-100">
                                    <label>
                                        <input type="checkbox" v-model="newAddress.primary"/>Primary Address
                                    </label>
                                </div>
                            </div>
                        </vue-cell>
                    </vue-grid>
                </form>
            </div>

            <md-dialog-actions>
                <md-button class="md-accent" @click="closeModal()">Close</md-button>

                <md-button
                    class="md-primary btn-lg"
                    @click="saveAddress()"
                    v-text="(editFlag ? 'Update' : 'Save')"
                />
            </md-dialog-actions>
        </md-dialog>
    </div>
</template>


<script>
    import { EventBus } from '../../shared/eventbus'
    import { Address, Addresses } from '../../classes/person/addresses'
    import Widget from '../../shared/widget'
    import Modal from '../../modal/modal'

    export default {
        name: 'Addresses',
        components: { Widget, Modal },
        props: {
            personId: Number
        },
        data () {
            return {
                addresses: new Addresses(this.personId),
                subscriber: 'personAddresses',
                modalVisibility: false,
                newAddress: {},
                cities: [],
                editFlag: false,
                addressIndex: 0,
            }

        },
        mounted () {
            EventBus.$on('pageLoaded', this.reloadList)
        },
        destroyed () {
            EventBus.$off('pageLoaded', this.reloadList)
        },
        methods: {
            reloadList (subscriber, data) {
                if (subscriber !== this.subscriber) return
                this.addresses.updateList(data)
            },
            addNewAddress () {
                this.editFlag = false
                this.showModal()
            },
            showModal () {
                if (this.cities.length === 0) {
                    this.getCities()
                }
                this.modalVisibility = true
            },
            editAddress (address, index) {
                this.editFlag = true
                this.addressIndex = index
                this.newAddress = {
                    id: address.id,
                    email: address.email,
                    street: address.street,
                    phone: address.phone,
                    city_id: address.city_id,
                    primary: address.primary
                }
                this.showModal()
            },
            getCities () {
                axios.get(resources.city.list).then(response => {
                    this.cities = response.data.data
                })
            },
            saveAddress () {
                if (this.validateNewAddress()) {
                    this.modalVisibility = false

                    if (this.editFlag) {

                        axios.put(
                            resources.person.addresses + this.personId + '/' + 'addresses',
                            this.newAddress
                        ).then((response) => {
                            this.addresses.list = this.addresses.list.map(function (
                                item
                            ) {
                                if (response.data.data.is_primary === 1) {
                                    item.primary = false
                                }
                                if (item.id === response.data.data.id) {
                                    let updatedAddress = new Address()
                                    return updatedAddress.fromJson(response.data.data)
                                }
                                return item
                            })

                        })

                    } else {
                        axios
                            .post(
                                resources.person.addresses + this.personId + '/' + 'addresses',
                                this.newAddress
                            )
                            .then(response => {
                                this.addresses.appendList(response.data.data)
                            })
                    }
                    this.newAddress = {}
                }
            },
            validateNewAddress () {
                if ('city_id' in this.newAddress === false) {
                    this.$swal({
                        type: 'error',
                        title: 'Missing Field',
                        text: 'City is required'
                    })
                    return false
                } else if ('phone' in this.newAddress) {
                    if (this.newAddress.phone.length === 0) {
                        this.$swal({
                            type: 'error',
                            title: 'Missing Field',
                            text: 'Phone number is required'
                        })
                        return false
                    }
                    if (!this.newAddress.phone.startsWith('+')) {
                        if (this.newAddress.phone.startsWith('00')) {
                            this.newAddress.phone = this.newAddress.phone.replace('00', '+')
                        } else {
                            this.$swal({
                                type: 'error',
                                title: 'Missing Fields',
                                text: 'Phone format is, +CountryCode Number (+255123123123) '
                            })
                            return false
                        }
                    }
                }
                return true
            },
            closeModal () {
                this.modalVisibility = false
                this.newAddress = {}
            }
        }
    }
</script>

<style lang="css" scoped>
    .adress-edit-container {
        padding: 1rem;
    }
</style>
