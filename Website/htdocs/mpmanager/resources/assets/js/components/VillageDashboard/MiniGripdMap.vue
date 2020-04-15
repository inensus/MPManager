<template>
    <!--:button-text=" drawPolygon? 'Stop Filtering' : 'Filter Meters'"
     :callback="startDrawPolygon"-->
    <widget
            :button="true"
            :button-text=" replaceMeters ? 'Stop relocating meters' : 'Relocate meters'"
            :callback="() => {replaceMeters =!replaceMeters}"
            title="Mini Grid Map"
            id="cluster-map">
        <div style="padding: 10px;">
            <div id="map" class="map" style="height: 500px;"></div>
        </div>
    </widget>
</template>

<script>
  import { LMap, LTileLayer, LMarker, LGeoJson, LPolygon } from 'vue2-leaflet'
  import { layerGroup } from 'leaflet'

  import Widget from '../../shared/widget'
  import MapWithDrawer from '../../shared/MapWithDrawer'

  export default {
    name: 'MiniGridMap',
    components: {
      MapWithDrawer,
      Widget,
      LMap,
      LTileLayer,
      LGeoJson,
      LMarker
    },
    props: {
      miniGridId: {
        type: String,
        required: true
      }
    },
    data () {
      return {
        loading: false,
        replaceMeters: false,
        show: true,
        enableTooltip: true,
        zoom: 6,
        center: [48, -1.219482],
        geojson: null,
        fillColor: '#e4ce7f',
        url: 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
        map: null,
        polygonCoordinates: [],
        polygon: null,
        drawPolygon: false,
        manualLayer: null,
      }
    },
    computed: {
      options () {
        return {
          onEachFeature: this.onEachFeatureFunction
        }
      },
      onEachFeatureFunction () {
        if (!this.enableTooltip) {
          return () => {}
        }
        return (feature, layer) => {
          layer.bindTooltip('<div>code:' + feature.properties.code + '</div><div>nom: ' + feature.properties.nom + '</div>', {
            permanent: false,
            sticky: true
          })
        }
      },

    },
    mounted () {

      this.initMap()
      this.getMiniGridMeters(this.miniGridId)

    },
    methods: {
      getMiniGridMeters (miniGridId) {
        axios.get(resources.meters.geo + '?mini_grid_id=' + miniGridId).then((response) => {
          this.meters = response.data.data
          this.addMetersToMap(this.meters)
        })
      },

      initMap () {
        //create map
        this.map = L.map('map')
          .setView([38.63, -90.23], 16)
        //set tile
        this.tileLayer = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: ' <span style="cursor:pointer">&copy; MpManager</span>',
          }
        )

        this.tileLayer.addTo(this.map)
        let parent = this
        this.manualLayer = layerGroup().addTo(this.map)

        this.map.on('click', function (e) {
          if (parent.drawPolygon === true) {
            var coord = e.latlng
            var lat = coord.lat
            var lng = coord.lng

            parent.polygonCoordinates.push([lat, lng])

            if (parent.polygon === null) {
              parent.polygon = L.polyline([[lat, lng]
              ]).addTo(parent.map)
            }
            parent.polygon.setLatLngs(parent.polygonCoordinates)

          }

        })

      },
      checkCollision () {
        var points = this.polygon.getLatLngs()
        var insideMeters = []

        let middle_points = [0, 0]

        for (let m in this.meters) {
          if (this.meters[m].meter_parameter.address.geo === null) {
            continue
          }
          let meterPoints = this.meters[m].meter_parameter.address.geo.points.split(',')
          let x = meterPoints[0], y = meterPoints[1]

          for (let i = 0, j = points.length - 1; i < points.length; j = i++) {

            let xi = points[i].lat, yi = points[i].lng
            let xj = points[j].lat, yj = points[j].lng

            let intersect = ((yi > y) != (yj > y))
              && (x < (xj - xi) * (y - yi) / (yj - yi) + xi)
            if (intersect) {
              insideMeters.push(this.meters[m]['serial_number'])
            }
          }
        }

      },
      startDrawPolygon () {

        if (this.drawPolygon) {
          this.polygonCoordinates.push(this.polygonCoordinates[0])

          this.drawnPolygon = new L.Polygon(this.polygon.getLatLngs())
          this.drawnPolygon.addTo(this.manualLayer)

          this.checkCollision()

          if (this.polygon !== null) {
            this.polygon.remove()
            this.polygon = null
            this.polygonCoordinates = []

          }

        }

        this.drawPolygon = !this.drawPolygon

      },
      strToHex (str) {
        str += 'z4795dfjkldfnjk4lnjkl'
        let hash = 0
        for (let i = 0; i < str.length; i++) {
          hash = str.charCodeAt(i) + ((hash << 5) - hash)
        }
        let colour = '#'
        for (let i = 0; i < 3; i++) {
          let value = (hash >> (i * 8)) & 0xFF
          colour += ('00' + value.toString(16)).substr(-2)
        }
        return colour
      },
      getLocation () {
        axios.get(resources.clusters.geo)
          .then((response) => {
            let data = response.data.data
            for (let i = 0; i < data.length; i++) {
              let meter = data[i]
              let popup_flor = 'MyLabel'
              let content_flor = 'MyContent'
              let polygon = L.polygon(meter.geo, {
                'label': popup_flor,
                'popup': content_flor,
                color: this.strToHex(meter.serial_number)
              })
              let parent = this
              polygon.on('click', function (e) {
                parent.meterDetail(meter.serial_number)
              })
              polygon.bindPopup(meter.name)
              polygon.addTo(this.map)
            }

          })

      },
      meterDetail (meterId) {
        this.$router.push('/meters/' + meterId)
      },
      onEachFeature (feature, layer) {
        // does this feature have a property named popupContent?
        if (feature.properties && feature.properties.popupContent) {
          layer.bindPopup(feature.properties.popupContent)
        }
      },

      addMetersToMap (meters) {
        this.mapLayer = layerGroup().addTo(this.map)
        let middle_points = [0, 0]
        for (let i in meters) {
          if (meters[i].meter_parameter.address.geo === null) {
            continue
          }
          let points = meters[i].meter_parameter.address.geo.points.split(',')

          let miniGridMarker = L.marker(
            points, {
              meterId: meters[i].id,
              originalPosition: points,
              //draggable: 'true',
              icon: L.icon({
                iconSize: [20, 20],
                iconUrl: 'https://cdn2.iconfinder.com/data/icons/gadgets-and-devices/48/87-512.png',
                iconAnchor: [20, 20],
              })
            }
          )
          miniGridMarker.bindTooltip('Meter: ' + meters[i].serial_number)

          miniGridMarker.addTo(this.map)
          let parent = this
          miniGridMarker.on('click', function (e) {
              if (parent.replaceMeters && !this.dragging.enabled()) {
                var marker = this
                marker.dragging.enable()
                marker.on('dragend', function (event) {
                  var position = marker.getLatLng()
                  marker.setLatLng(position)

                  parent.$swal({
                    title: 'Done with moving?',
                    text: 'Do you want to place that meter on the current spot?',
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                  }).then((value) => {
                    if (value.value === true) {
                      axios.put(resources.meters.list + '/' + marker.options.meterId, {
                        points: position
                      }).then((response) => {
                        if (response.status === 200) {
                          parent.$swal({
                            title: 'Meter Replaced Successfully',
                            text: 'Replacements success',
                            type: 'success',
                            confirmButtonText: 'Okay',
                            timer: 1000,
                          })
                        }
                      })
                    } else {
                      marker.setLatLng(marker.options.originalPosition)
                    }
                    marker.dragging.disable()
                  })

                })
                return
              }
              if (!this.replaceMeters)
                parent.meterDetail(meters[i].serial_number)
            }
          )
          if (i === 0) {
            middle_points[0] = parseFloat(points[0])
            middle_points[1] = parseFloat(points[1])
          } else {
            middle_points[0] = (middle_points[0] + parseFloat(points[0])) / 2
            middle_points[1] = (middle_points[1] + parseFloat(points[1])) / 2
          }
        }
        this.map.setView({ lat: middle_points[0], lng: middle_points[1] })
      }
    },

  }
</script>
