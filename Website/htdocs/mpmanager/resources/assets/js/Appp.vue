<template>
  <div >

      <component :is="layout">
      <router-view/>
      <notifications group="notify" position="top right" ></notifications>

    </component>
  </div>
</template>

<script>
const default_layout = 'default'

export default {
    name:'Appp.vue',
    computed: {
        layout() {
            if (this.$route.meta.layout==default_layout) {
                return default_layout
            }

            return 'div'
        }
    },

}
</script>


<style lang="scss">

</style>
