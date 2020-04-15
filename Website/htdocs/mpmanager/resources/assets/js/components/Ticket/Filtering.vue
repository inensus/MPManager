<template>

            <div class="md-layout md-gutter md-size-100" style="padding: 0.4rem; margin: auto;" >
                <div class="md-layout-item md-size-42">
                    <md-field>
                        <md-select

                            @md-selected="setCategory"
                            id="ticket_categories"
                            name="ticket_categories"
                            placeholder="Category"
                        >
                            <md-option value>-- Any Category --</md-option>
                            <md-option
                                :key="index"
                                :value="category.id"
                                v-for="(category,index) in categories"
                            >{{category.label_name}}
                            </md-option>
                        </md-select>
                    </md-field>
                </div>

                <div class="md-layout-item md-size-42">
                    <md-field class="md-layout-item">
                        <md-select @md-selected="setPerson" id="assigned_to" name="assigned_to"
                                   placeholder="Assigned To">
                            <md-option value>-- Any User --</md-option>
                            <md-option
                                :key="person.id"
                                :value="person.id"
                                v-for="person in people"
                            >{{person.user_name}}
                            </md-option>
                        </md-select>
                    </md-field>
                </div>


                <div class="md-layout-item md-size-16">
                    <md-button @click="filterTickets" class="md-raised md-primary">Filter</md-button>
                    <md-button class="md-raised md-accent" @click="() => {filterTicket = false}">Close</md-button>
                </div>

            </div>

</template>

<script>
    import {resources} from "../../resources";

    export default {
        name: "Filtering",
        created() {
        },
        mounted() {
            this.getCategories();
            this.getPeople();
        },
        data() {
            return {
                categories: [],
                people: [],
                selectedCategory: "",
                selectedPerson: ""
            };
        },
        methods: {
            setCategory(category) {
                this.selectedCategory = category
            },
            setPerson(person) {
                this.selectedPerson = person
            },
            getCategories() {
                axios.get(resources.ticket.labels).then(response => {
                    this.categories = response.data.data;
                });
            },
            getPeople() {
                axios.get(resources.ticket.users).then(response => {
                    this.people = response.data.data;
                });
            },
            filterTickets() {
                let query = "";
                if (this.selectedCategory !== "") {
                    query += "&category=" + this.selectedCategory;
                }
                if (this.selectedPerson !== "") {
                    query += "&person=" + this.selectedPerson;
                }

                if (query !== "") {
                    this.$emit("filtering", query);
                }
            }
        }
    };
</script>

<style scoped>
    .chic-button {
        background-color: #0a0a0c !important;
        color: #fefefe !important;
    }

    .filter-grid {
        padding: 1rem;
    }
</style>
