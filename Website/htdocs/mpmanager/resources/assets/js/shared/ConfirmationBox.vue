<template>

</template>

<script>
  import { EventBus } from './eventbus'

  export default {
    name: 'ConfirmationBox',
    props: {
      title: {
        required: true,
        type: String,
      }
    },
    data () {
      return {}
    },
    created () {
      EventBus.$on('show.confirm', this.showConfirmation)
    },
    methods: {
      showConfirmation (data = null) {
          this.$swal({
          type: 'question',
          title: this.title,
          text: 'Are you sure to do this action?',
          showCancelButton: true,
          confirmButtonText: 'I\'m sure',
          cancelButtonText: 'Cancel',
        }).then((result) => {
          console.log(result)
          if (result.value) {
            this.$emit('confirmed', data)
          } else {
            console.log('user cancelled action')
          }

        })
      }
    },
  }
</script>

<style scoped>

</style>
