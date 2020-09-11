<template>


    <ol class="breadcrumb">
        <li style="cursor:pointer;" v-for="(item,index) in items" :key="index" @click="navigateToUrl(item.href)">
            {{ index }} {{ item.append !== undefined ? item.append : ''}}
        </li>
    </ol>


</template>

<script>
import {EventBus} from './eventbus'

export default {
    name: 'Breadcrumb',
    data() {
        return {
            items: {}
        }
    },
    created() {
        EventBus.$on('bread', data => {

            this.items = data
        })
    },
    methods: {
        navigateToUrl(url) {
            this.$router.push({path: url})
        }
    }

}
</script>

<style scoped>

</style>
