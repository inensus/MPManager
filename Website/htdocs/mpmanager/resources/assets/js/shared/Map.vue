<template>
    <div id="map">

    </div>
</template>
<script>

    import 'leaflet/dist/leaflet.css'
    import 'leaflet-draw'
    import 'leaflet-draw/dist/leaflet.draw.css'
    import { MappingService } from '../services/MappingService'
    import { EventBus } from './eventbus'

    const debounce = require('debounce')
    export default {
        name: 'Map',
        components: {},
        props: {
            clusterName: {
                type: String,
                default: '',
            },
            polygon: {
                type: Boolean,
                default: false
            },
            marker: {
                type: Boolean,
                default: false
            },
            constantMarkerUrl: {
                type: String,
                default: ''
            },
            markerUrl: {
                type: String,
                default: 'leaflet/dist/images/layers.png'

            },
            markerCount: {
                type: Number,
                default: 1
            },
            polyline: {
                type: Boolean,
                default: false
            },
            circlemarker: {
                type: Boolean,
                default: false
            },
            constantLocations: {
                default: null
            },
            rectangle: {
                type: Boolean,
                default: false
            },
            circle: {
                type: Boolean,
                default: false
            },
            geoData: {
                default: null
            },
            center: {
                type: Array,
                default: [-2.500381, 32.889060]
            },
            filtered_types: {
                type: Object,
                default: function () {
                    return {}
                }
            },
            edit: {
                type: Boolean,
                default: false
            },
            remove: {
                type: Boolean,
                default: false
            },
            markerLocations: {

                default: null
            },
            markingInfos: {
                default: null
            }
        },
        data () {
            return {
                mappingService: new MappingService(),
                osmUrl: 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
                osmAttrib: '<span style="cursor:pointer">&copy; MpManager</span>',
                osm: null,
                map: null,
                editableLayers: new L.FeatureGroup(),
                geoDataItems: []

            }

        },
        created () {

        },
        mounted () {

            this.drawingOptions = {
                position: 'topright',
                draw: {
                    polygon: this.polygon,
                    polyline: this.polyline,
                    marker: this.marker,
                    circlemarker: this.circlemarker,
                    rectangle: this.rectangle,
                    circle: this.circle,
                },
                edit: {
                    featureGroup: this.editableLayers,
                    remove: this.remove,
                    edit: this.edit
                }
            }
            this.generateMap(this.drawingOptions, this.center)
            this.map.on('draw:edited', function (e) {

                let editedItems = []
                let editedLayers = e.layers
                editedLayers.eachLayer((layer) => {
                    let type = e.layerType
                    if (type === 'marker') {
                        layer.bindPopup('A popup!')
                        type = 'Marker'
                    } else if (type === 'polygon') {
                        type = 'Polygon'
                    }
                    let sumLat = 0
                    let sumLon = 0
                    for (let i = 0; i < layer._latlngs[0].length; i++) {
                        let coordinates = layer._latlngs[0][i]
                        sumLat += coordinates.lat
                        sumLon += coordinates.lng
                    }
                    let avgLat = sumLat / layer._latlngs[0].length
                    let avgLon = sumLon / layer._latlngs[0].length
                    let geoDataItem = service.manualDrawingLocationConvert({
                        _leaflet_id: layer._leaflet_id,
                        type: 'manual',
                        geojson: {
                            type: type,
                            coordinates: layer._latlngs,
                        },
                        display_name: '',
                        selected: false,
                        lat: avgLat,
                        lon: avgLon,
                    })
                    editedItems.push(geoDataItem)
                })
                EventBus.$emit('getEditedGeoDataItems', editedItems)
            })
            this.map.on('draw:deleted', function (e) {
                let deletedItems = []
                let deletedLayers = e.layers
                deletedLayers.eachLayer((layer) => {
                    deletedItems.push(layer._leaflet_id)
                })
                EventBus.$emit('getDeletedGeoDataItems', deletedItems)
            })
            let drawLayer = this.editableLayers
            let service = this.mappingService
            let markerCount = this.markerCount
            this.map.on('draw:created', function (e) {

                let type = e.layerType
                let layer = e.layer
                if (type === 'marker') {
                    type = 'Marker'
                    let drawedLayers = drawLayer.getLayers()
                    let polygon = drawedLayers[0]
                    let bounds = polygon.getBounds()
                    if (markerCount !== 0) {
                        if (drawedLayers.length > markerCount) {
                            for (let i = 0; i <= drawedLayers.length - markerCount; i++) {
                                let marker = drawedLayers[i]

                                if (marker._icon !== undefined && !marker._icon.currentSrc.includes('miniGrid')) {

                                    drawLayer.removeLayer(marker)
                                }

                            }
                        }
                    }

                    if (bounds.contains(layer._latlng)) {
                        drawLayer.addLayer(layer)
                        let geoDataItem = {
                            _leaflet_id: layer._leaflet_id,
                            type: 'manual',
                            geojson: {
                                type: type,
                                coordinates: layer._latlng,
                            },
                            display_name: '',
                            selected: false,
                            drawType: 'draw',
                            lat: 0,
                            lon: 0,
                        }
                        EventBus.$emit('getDrawedMarker', geoDataItem)
                    } else {
                        EventBus.$emit('markerError', 'Please position your mini-grid within the selected cluster boundaries.')
                    }

                } else if (type === 'polygon') {
                    type = 'Polygon'
                    let feature = layer.feature = layer.feature || {}
                    feature.type = feature.type || 'Feature'
                    let props = feature.properties = feature.properties || {}
                    props.drawType = 'draw'
                    props.selected = false

                    drawLayer.addLayer(layer)

                    let sumLat = 0
                    let sumLon = 0
                    for (let i = 0; i < layer._latlngs[0].length; i++) {
                        let coordinates = layer._latlngs[0][i]
                        sumLat += coordinates.lat
                        sumLon += coordinates.lng
                    }

                    let avgLat = sumLat / layer._latlngs[0].length
                    let avgLon = sumLon / layer._latlngs[0].length
                    let geoDataItem = service.manualDrawingLocationConvert({
                        _leaflet_id: layer._leaflet_id,
                        type: 'manual',
                        geojson: {
                            type: type,
                            coordinates: layer._latlngs,
                        },
                        display_name: '',
                        selected: false,
                        drawType: 'draw',
                        lat: avgLat,
                        lon: avgLon,
                    })
                    EventBus.$emit('getDrawedLocation', geoDataItem)
                }

            })

        },
        destroyed () {
            this.map = null
        },
        computed: {},
        methods: {
            generateMap (options, center) {

                this.mapInitialized = true
                this.map = L.map('map').setView(center, 9)
                L.tileLayer(this.osmUrl, { maxZoom: 18, attribution: this.osmAttrib }).addTo(this.map)
                this.editableLayers = new L.FeatureGroup()
                this.map.addLayer(this.editableLayers)

                if (options.draw.marker !== false) {
                    let marker = L.Icon.extend({
                        options: {
                            iconSize: [40.4, 44],
                            iconAnchor: [20, 43],
                            popupAnchor: [0, -51],
                            iconUrl: this.markerUrl
                        }
                    })
                    options.draw.marker = {}
                    options.draw.marker.icon = new marker()

                }
                this.drawControl = new L.Control.Draw(options)
                this.map.addControl(this.drawControl)

            },
            setLocation (geoData) {
                this.geoDataItems = []
                this.editableLayers.clearLayers()
                let editableLayer = this.editableLayers
                for (let i = 0; i < geoData.length; i++) {

                    let geoType = geoData[i].geojson.type
                    let drawing = {
                        'type': 'FeatureCollection',
                        'crs': {
                            'type': 'name',
                            'properties': {
                                'name': 'urn:ogc:def:crs:OGC:1.3:CRS84'
                            }
                        },
                        'features': [{
                            'type': 'Feature',
                            'properties': {
                                'popupContent': geoData[i].display_name,
                                'drawType': geoData[i].drawType === undefined ? 'set' : geoData[i].drawType,
                                'selected': geoData[i].selected === undefined ? false : geoData[i].selected,
                                'clusterId': geoData[i].clusterId === undefined ? -1 : geoData[i].clusterId,
                            },
                            'geometry': {
                                'type': geoType,
                                'coordinates': geoData[i].geojson.coordinates
                            }
                        }]
                    }
                    let polygonColor = this.mappingService.strToHex(geoData[i].display_name)
                    let geoDataItems = this.geoDataItems
                    let router = this.$router
                    L.geoJson(drawing,
                        {
                            style: { fillColor: polygonColor, color: polygonColor },
                            onEachFeature: function (feature, layer) {
                                let type = layer.feature.geometry.type
                                let clusterId = layer.feature.properties.clusterId
                                if (type === 'Polygon' && clusterId !== -1) {
                                    layer.on('click', function (e) {
                                        router.push({ path: '/clusters/' + clusterId })
                                    })
                                }

                                editableLayer.addLayer(layer)
                                let geoDataItem = {
                                    _leaflet_id: layer._leaflet_id,
                                    type: 'manual',
                                    geojson: {
                                        type: geoData[i].geojson.type,
                                        coordinates: geoData[i].geojson.coordinates,
                                    },
                                    display_name: geoData[i].display_name,
                                    selected: feature.properties.selected,
                                    drawType: feature.properties.drawType,
                                    lat: geoData[i].lat,
                                    lon: geoData[i].lon,
                                }
                                geoDataItems.push(geoDataItem)
                            }
                        })

                    this.map.setView([geoData[i].lat, geoData[i].lon], 10)
                }
                EventBus.$emit('getSearchedGeoDataItems', this.geoDataItems)
            },
            setMarker (markerLocations, isConstant) {

                let editableLayer = this.editableLayers
                let drawedLayers = editableLayer.getLayers()
                markerLocations.forEach((e) => {
                    let polygon = drawedLayers[0]
                    let bounds = polygon.getBounds()
                    if (this.markerCount !== 0) {
                        if (drawedLayers.length > this.markerCount) {
                            for (let i = 0; i <= drawedLayers.length - this.markerCount; i++) {
                                let marker = drawedLayers[i]

                                if (marker._icon !== undefined && !marker._icon.currentSrc.includes('miniGrid')) {

                                    editableLayer.removeLayer(marker)
                                }

                            }
                        }
                    }
                    let markerIcon = L.icon({
                        iconSize: [40.4, 44],
                        iconAnchor: [20, 43],
                        popupAnchor: [0, -51],
                        iconUrl: isConstant === true ? this.constantMarkerUrl : this.markerUrl
                    })
                    let markerLayer = L.marker(e, { icon: markerIcon })
                    if (this.markingInfos!==null){
                        let markingInfo = this.markingInfos.filter(x => x.lat === e[0] && x.lon === e[1])[0]
                        if (markingInfo!==undefined){
                            markerLayer.bindTooltip('Mini Grid: ' + markingInfo.name)
                            let parent = this
                            markerLayer.on('click', function (e) {
                                parent.toMiniGridDashboard(markingInfo.id)
                            })
                        }

                    }
                    markerLayer.addTo(editableLayer)
                    if (bounds.contains(markerLayer._latlng)) {
                        markerLayer.addTo(editableLayer)
                    } else {
                        editableLayer.removeLayer(markerLayer)
                        EventBus.$emit('markerError', 'Please position your mini-grid within the selected cluster boundaries.')
                    }

                })
            },
            toMiniGridDashboard (miniGridId) {
                this.$router.push('/dashboards/mini-grid/' + miniGridId)
            },
        },
        watch: {
            geoData: debounce(function (e) {
                this.setLocation(this.geoData)
            }, 10),
            markerLocations: debounce(function (e) {
                this.setMarker(this.markerLocations, false)
            }, 10),
            constantLocations: debounce(function (e) {

                this.setMarker(this.constantLocations, true)
            }, 10),
        }
    }

</script>
<style scoped>
    #map {
        height: 500px;
        width: 100%;
    }

    .leaflet-draw-actions a {
        background: white !important;
    }
</style>
