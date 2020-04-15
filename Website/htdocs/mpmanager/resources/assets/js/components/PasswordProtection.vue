<template>

</template>

<script>
  export default {
    name: 'PasswordProtection',
    data () {
      return {
        password: '1',
      }
    },
    mounted () {
      this.confirm()
    },
    methods: {
      confirm () {
        this.$swal({
          type: 'question',
          allowOutsideClick: false,
          allowEscapeKey: false,
          title: 'Password Protected Zone',
          text: 'Please enter the password to access this area',
          inputType: 'password',
          input: 'password',
          inputPlaceholder: 'Password',

          inputValidator: (value) => {
            //TODO: get the value from some config
            return
            if (value !== this.password) {
              this.$swal({
                type: 'error',
                text: 'Wrong Password',
                timer: 1000,
              }).then(() => {
                this.$router.replace('/')
              })

            }
          }
        })
      }
    }
  }
</script>

<style scoped>

</style>
