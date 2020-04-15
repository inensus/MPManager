<template>
    <ul class="list-group">
        <li class="list-group-item" v-for="user in users" :key="user.id" @click="selectUser(user.id)"
            :class="selectedUser === user.id ?'active': ''">
            {{user.name}}
        </li>
    </ul>
</template>

<script>
    import { resources } from '../../resources'

    export default {
        name: 'UserList',
        mounted () {
            this.getUserList()
        },
        data () {
            return {
                users: null,
                selectedUser: null,
            }
        },
        methods: {
            selectUser (user) {
                this.selectedUser = user
                this.$emit('userSelected', user)
            },
            getUserList () {
                axios.get(resources.admin.list).then((response) => {
                    this.users = response.data.data
                })
            }
        }
    }
</script>

<style scoped>

</style>
