<template>

</template>

<script>
  export default {
    name: 'Selector',
    created () {
      this.getMiniGridList()
    },
    mounted () {

    },
    data () {
      return {
        miniGrids: null
      }
    },
    methods: {
      getMiniGridList () {
        axios.get(resources.miniGrids.list)
          .then((response) => {
            this.miniGrids = response.data.data
            this.showSelector()
          })
      },
      showSelector () {
        this.$swal({
          type: 'question',
          allowOutsideClick: false,
          title: 'Select Mini-Grid',
          text: 'Please select a mini-grid from the list',
          input: 'select',
          inputOptions: this.miniGrids.map(x => x.name),
          inputPlaceholder: 'Select a Mini Grid',

          inputValidator: (value) => {
            return new Promise((resolve) => {
              this.$router.replace('/dashboards/mini-grid/' + this.miniGrids[value].id)
              this.$swal.close()
            })
          }
        })
      }
    }
  }
</script>

<style scoped>

</style>
