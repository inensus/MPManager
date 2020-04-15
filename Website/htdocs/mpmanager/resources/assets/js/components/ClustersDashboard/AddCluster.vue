<template>
    <div>
        <password-protection></password-protection>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-5">
                    Cluster Name
                </div>
                <div class="col-md-7">
                    <input type="text" style="width: 100%" placeholder="Name"
                           v-model="clusterName">
                </div>
            </div>


            <h5>Assign Villages To Cluster</h5>
            <village-selector @citySelected="citySelected"></village-selector>
            <h5>Assign Cluster Manager</h5>

            <user-list @userSelected="userSelected"></user-list>


        </div>

        <div class="col-md-9">
            <map-with-drawer :cluster_name="clusterName" @locationSelected="locationSelected"
                             :filtered_types="{'polygon': true}"/>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <button style="width: 25%" class="btn btn-lg btn-success" @click="saveCluster">Save</button>
            </div>

        </div>
    </div>


</template>

<script>
  import VillageSelector from './VillageSelector'
  import UserList from './UserList'
  import MapWithDrawer from '../../shared/MapWithDrawer'
  import { resources } from '../../resources'
  import PasswordProtection from '../PasswordProtection'

  export default {
    name: 'AddCluster',
    components: {
      PasswordProtection,
      MapWithDrawer,
      UserList,
      VillageSelector,
    },
    mounted () {

    },
    data () {
      return {
        //flag for manual drawing
        user: null,
        clusterName: '',
        cities: null,
        selectedLocation: null,

      }
    },
    methods: {
      locationSelected (location) {
        console.log(location)
        this.selectedLocation = location
      },
      userSelected (user) {
        this.user = user
      },
      citySelected (cities) {
        this.cities = []
        for (let c in cities) {
          this.cities.push(cities[c].id)
        }

      },
      saveCluster () {
        if (this.selectedLocation === null) {
          this.$swal({
            type: 'error',
            title: 'Cluster Location not selected',
            text: 'Please select/draw a location for the cluster',
          })
          return
        }
        if (this.user === null) {
          this.$swal({
            type: 'error',
            title: 'Cluster Manager not selected',
            text: 'Please select a cluster manager.',
          })
          return
        }

        axios.post(resources.clusters.save, {
            'geo_type': 'external',
            'geo_data': this.selectedLocation,
            'name': this.clusterName,
            'cities': this.cities,
            'manager_id': this.user,
          }
        ).then((response) => {
          if (response.status === 200 || response.status === 201) {
            this.$swal({
              type: 'success',
              title: 'Cluster Saved',
              text: 'The cluster you add is stored successfully!.',
              timer: 3000
            }).then(() => {
              this.$router.replace('/clusters')

            })
          }
        })

      },

    }
  }
</script>

<style scoped>

</style>
