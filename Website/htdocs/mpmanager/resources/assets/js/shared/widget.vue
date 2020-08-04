<template>
    <div>
        <md-toolbar :data-color="color" class="md-dense chic" md-elevation="3">
            <div class="tabs">
                <slot name="tabbar"></slot>
            </div>

            <div class="md-toolbar-section-start">
                <font-awesome-icon icon="list"/>
                <h4 class="chic-title" v-text="title"></h4>
                <div class="search-area">
                    <md-button
                        :class="_btnClass"
                        @click="_callback"
                        class="md-dense md-primary chic-button"
                        style="position: absolute; right: 10px; top:10px"
                        v-if="button"
                        v-text="buttonText"
                    />

                    <div class="search-input" v-if="search">
                        <div class="md-layout md-gutter">

                            <md-field>

                                <label style="color: white!important;">Search ..</label>
                                <md-input style="color: white!important;" v-model="searchTerm"></md-input>
                                <div v-if="searching">
                                <span style="margin-right: 15px;">Search Results for: <u>{{searchTerm}}</u>

                                    <font-awesome-icon @click="showAllEntries" class="pointer" icon="times"/></span>

                                </div>
                                <md-icon style="color: white;">search</md-icon>

                            </md-field>


                        </div>


                    </div>

                </div>
            </div>


        </md-toolbar>

        <md-card>
            <md-card-content class="nopadding">
                <slot></slot>
            </md-card-content>
        </md-card>
        <md-toolbar class="md-dense" md-elevation="1" v-if="paginator">
            <paginate
                :paginatorReference="paginator"
                :route_name="route_name"
                :show_per_page="show_per_page"
                :subscriber="subscriber"
                v-if="paginator"
                :key="resetKey"
            ></paginate>
        </md-toolbar>
    </div>
</template>

<script>
    import {Paginator} from '../classes/paginator'
    import {EventBus} from './eventbus'
    import Paginate from './Paginate'

    const debounce = require('debounce');
    export default {
        name: 'Widget',
        components: {Paginate},
        props: {
            color: {
                type: String,
                default: 'default'
            },
            title: String,
            id: String,
            callback: {},
            button: Boolean,
            buttonText: String,
            buttonColor: String,
            paginator: Paginator,
            search: {},
            subscriber: String,
            route_name: String,
            headless: {
                type: Boolean,
                default: false
            },
            show_per_page: {
                type: Boolean,
                default: false
            },
            resetKey:{
                default:0
            }
        },
        mounted() {
            //listen for a remote trigger for ending the search
            EventBus.$on('search.end', this.cancelSearching)
        },
        beforeDestroy() {
            EventBus.$off('search.end', this.cancelSearching)
        },
        data() {
            return {
                searching: false,
                searchTerm: ''
            }
        },
        methods: {
            defaultCallback() {
                alert('default button click')
            },
            doSearch(data) {
                this.searching = true;
                EventBus.$emit('searching', data)
            },
            showAllEntries() {
                this.searching = false;
                this.searchTerm = '';
                EventBus.$emit('end_searching')
            },
            cancelSearching() {
                this.searching = false;
                this.searchTerm = ''
            }
        },
        computed: {
            _callback: function () {
                if (this.callback === undefined) return this.defaultCallback;
                else return this.callback
            },
            _btnClass: function () {
                if (this.buttonColor === undefined) {
                    return 'btn-primary'
                } else if (this.buttonColor === 'green') {
                    return 'btn-success'
                } else if (this.buttonColor === 'yellow') {
                    return 'btn-warning'
                } else if (this.buttonColor === 'red') {
                    return 'btn-danger'
                } else if (this.buttonColor === 'blue') {
                    return 'btn-info'
                }
            }
        },
        watch: {
            searchTerm: debounce(function (e) {
                if (this.searchTerm.length > 0) {
                    this.doSearch(this.searchTerm)
                }
                if (this.searching && this.searchTerm.length == 0) {
                    this.showAllEntries()
                }
            }, 1000)
        }
    }
</script>

<style lang="scss" scoped>
    .full-width-input-with-icon {
        width: calc(100% - 32px) !important;
    }

    .full-width-input-with-ending-icon {
        width: calc(100% - 70px) !important;
    }

    .full-width-input {
        width: calc(100%) !important;
    }

    .tabs {
        position: absolute;
        right: 1rem;
    }

    .nopadding {
        padding: 30px 0 0 0 !important;
    }

    .chic {
        margin-bottom: -10px !important;
        margin-left: -2px !important;
        margin-top: 0 !important;
        top: 16px !important;
        width: 98% !important;
        left: 1% !important;
        color: white !important;
        border-radius: 3px;
    }

    .chic-title {
        color: white;
        font-weight: 300;
        line-height: 22px;
        font-size: 1rem;
        margin-left: 5px;
    }

    .chic-button {
        padding: 8px !important;
    }

    .chic-icon {
        color: white !important;
    }

    .md-toolbar[data-color="default"] {
        background: rgb(61, 59, 63);
        background: linear-gradient(
                162deg,
                rgba(61, 59, 63, 1) 0%,
                rgba(121, 117, 125, 1) 50%,
                rgba(101, 98, 105, 1) 100%
        );
        box-shadow: 0 12px 20px -10px rgba(130, 130, 130, 0.28),
        0 4px 20px 0 rgba(26, 26, 26, 0.12), 0 7px 8px -5px rgba(83, 80, 84, 0.2);

        h4 {
            color: #fefefe;
        }

        svg {
            color: #fefefe;
        }

        .chic-button {
            background-color: #0a0a0c !important;
            color: #fefefe !important;
        }
    }

    .md-toolbar[data-color="green"] {
        background: rgb(68, 113, 68);
        background: linear-gradient(
                162deg,
                rgba(68, 113, 68, 1) 0%,
                rgba(90, 149, 90, 1) 50%,
                rgba(102, 171, 102, 1) 100%
        );
        box-shadow: 0 12px 20px -10px rgba(76, 175, 80, 0.28),
        0 4px 20px 0 rgba(0, 0, 0, 0.12), 0 7px 8px -5px rgba(76, 175, 80, 0.2);

        h4 {
            color: #fefefe;
        }

        svg {
            color: #fefefe;
        }

        .chic-button {
            background-color: #325932 !important;
            color: #fefefe !important;
        }
    }

    .md-toolbar[data-color="orange"] {
        background: rgb(164, 106, 0);
        background: linear-gradient(
                162deg,
                rgba(164, 106, 0, 1) 0%,
                rgba(218, 142, 1, 1) 50%,
                rgba(255, 165, 0, 1) 100%
        );
        box-shadow: 0 12px 20px -10px rgba(255, 165, 0, 0.28),
        0 4px 20px 0 rgba(255, 165, 0, 0.12), 0 7px 8px -5px rgba(255, 165, 0, 0.2);

        h4 {
            color: #fefefe;
        }

        svg {
            color: #fefefe;
        }

        .chic-button {
            background-color: orangered !important;
            color: #fefefe !important;
        }
    }

    .md-toolbar[data-color="red"] {
        background: rgb(96, 28, 28);
        background: linear-gradient(
                162deg,
                rgba(96, 28, 28, 1) 0%,
                rgba(198, 73, 92, 1) 50%,
                rgba(236, 17, 50, 1) 100%
        );
        box-shadow: 0 12px 20px -10px rgba(255, 0, 39, 0.28),
        0 4px 20px 0 rgba(255, 0, 39, 0.12), 0 7px 8px -5px rgba(255, 0, 39, 0.2);

        h4 {
            color: #fefefe;
        }

        svg {
            color: #fefefe;
        }

        .chic-button {
            background-color: #a81e10 !important;
            color: #fefefe !important;
        }
    }

    .search-area {
        float: right;
        margin: auto;
        width: 60% !important;
    }

    .search-input {
        width: 80% !important;
        margin: auto;

    }

    .pointer {
        cursor: pointer;
    }
</style>
