<template>
    <div class="breadcrumb">

        <ul>
            <li
                v-for="(breadcrumb, index) in breadcrumbList"
                :key="index"
                @click="routeTo(index)"
                :class="{'linked': breadcrumb.type === 'base'}">
                <div v-if="breadcrumb.type === 'final_target'">
                    {{ $route.params[breadcrumb.target]}}
                </div>
                <div v-else>
                    <u>{{ translateItem(breadcrumb.name) }}</u>
                </div>

            </li>
        </ul>
    </div>
</template>

<script>
import { translateItem } from '../Helpers/TranslateItem'

export default {
    name: 'Breadcrumb',
    data () {
        return {
            breadcrumbList: [],
            prevRoute:null,
            translateItem: translateItem
        }
    },
    mounted () {
        this.updateList()
    },
    watch: {
        '$route' () {
            this.updateList()
        }
    },
    methods: {
        routeTo (index) {
            if (this.breadcrumbList[index].link) {
                if(window.history.state !== null){
                    this.$router.back()
                }else {
                    this.$router.push(this.breadcrumbList[index].link)
                }
            }
        },
        updateList () {
            this.breadcrumbList = this.$route.meta.breadcrumb
        }
    }
}
</script>

<style scoped>
.breadcrumb {}
ul {
    display: flex;
    justify-content: center;
    list-style-type: none;
    margin: 0;
    padding: 0;
}
ul > li {
    display: flex;
    float: left;
    height: 10px;
    width: auto;
    color: white;
    font-style: italic;
    font-weight: bold;
    font-size: 1em;
    cursor: default;
    align-items: center;
}
ul > li:not(:last-child)::after {
    content: '/';
    float: right;
    font-size: .8em;
    margin: 0 .5em;
    color: whitesmoke;
    cursor: default;
}
.linked {
    cursor: pointer;
    font-size: 1em;
    font-weight: bold;
}
</style>
