<template>
    <div id="map">

    </div>
</template>
<script>

import 'leaflet/dist/leaflet.css'
import 'leaflet-draw/dist/leaflet.draw.css'
import L from 'leaflet'
import 'leaflet-draw'
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
            default: function () {return this.$store.state.mapSettings.center}

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
        },
        zoom: {
            type: Number,
            default: function () {return this.$store.state.mapSettings.zoom}
        },
        maxZoom: {
            type: Number,
            default: 20
        },
        isMeter: {
            type: Boolean,
            default: false
        },
        parentName: {
            type: String,
            default: ''
        },
        position:{
            type: String,
            default:'topright',
            required:false
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
    mounted () {
        this.drawingOptions = {
            position: this.position,
            draw: {
                polygon: this.polygon,
                polyline: this.polyline,
                marker: this.marker,
                circlemarker: this.circlemarker,
                rectangle: this.rectangle,
                circle: this.circle,
            },
            edit: {
                featureGroup: null,
                remove: this.remove,
                edit: this.edit
            }
        }
        this.generateMap(this.drawingOptions, this.center)
        this.map.on('draw:edited', function (e) {

            let editedItems = []
            let editedLayers = e.layers
            editedLayers.eachLayer((layer) => {

                let type = layer._latlngs === undefined ? 'Marker' : 'Polygon'
                if (type === 'Marker') {

                    let geoDataItem = {
                        id: layer._tooltip._content.split('/')[1],
                        meterSerial: layer._tooltip._content.split('/')[0],
                        lat: layer._latlng.lat,
                        lng: layer._latlng.lng
                    }
                    editedItems.push(geoDataItem)
                } else {

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
                        leaflet_id: layer._leaflet_id,
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
                }
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
                        leaflet_id: layer._leaflet_id,
                        type: 'manual',
                        geojson: {
                            type: type,
                            coordinates: layer._latlng,
                        },
                        display_name: '',
                        selected: false,
                        draw_type: 'draw',
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
                props.draw_type = 'draw'
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
                    leaflet_id: layer._leaflet_id,
                    type: 'manual',
                    geojson: {
                        type: type,
                        coordinates: layer._latlngs,
                    },
                    display_name: '',
                    selected: false,
                    draw_type: 'draw',
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
    methods: {
        generateMap (options, center) {

            this.mapInitialized = true
            this.map = L.map('map').setView(center, this.zoom)
            L.tileLayer(this.osmUrl, { maxZoom: this.maxZoom, attribution: this.osmAttrib }).addTo(this.map)
            this.editableLayers = new L.FeatureGroup()
            this.map.addLayer(this.editableLayers)
            options.edit.featureGroup = this.editableLayers
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
            let nonEditableLayers = new L.FeatureGroup()
            for (let i = 0; i < geoData.length; i++) {
                let geoType = geoData[i].geojson.type
                let coordinatesClone = []
                coordinatesClone[0] = []
                geoData[i].geojson.coordinates[0].forEach(e => {
                    coordinatesClone[0].push([e[1], e[0]])
                })

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
                            'draw_type': geoData[i].draw_type === undefined ? 'set' : geoData[i].draw_type,
                            'selected': geoData[i].selected === undefined ? false : geoData[i].selected,
                            'clusterId': geoData[i].clusterId === undefined ? -1 : geoData[i].clusterId,
                        },
                        'geometry': {
                            'type': geoType,
                            'coordinates': geoData[i].searched === true ? geoData[i].geojson.coordinates : coordinatesClone
                        }
                    }]
                }

                let polygonColor = this.mappingService.strToHex(geoData[i].display_name)
                let geoDataItems = this.geoDataItems
                let router = this.$router
                let map = this.map
                let parent = this.parentName
                L.geoJson(drawing,
                    {
                        style: { fillColor: polygonColor, color: polygonColor },
                        onEachFeature: function (feature, layer) {

                            let type = layer.feature.geometry.type
                            let clusterId = layer.feature.properties.clusterId
                            if (type === 'Polygon' && clusterId !== -1) {
                                layer.on('click', function () {
                                    router.push({ path: '/clusters/' + clusterId })
                                })
                            }

                            if (parent === 'MiniGrid') {
                                nonEditableLayers.addLayer(layer)
                                map.addLayer(nonEditableLayers)
                            } else if (parent === 'Top') {
                                layer.bindTooltip('Cluster :' + layer.feature.properties.popupContent)
                                console.log(layer.feature.properties.popupContent)
                                editableLayer.addLayer(layer)

                            } else {
                                editableLayer.addLayer(layer)

                            }
                            let geoDataItem = {
                                leaflet_id: layer._leaflet_id,
                                type: 'manual',
                                geojson: {
                                    type: geoData[i].geojson.type,
                                    coordinates: geoData[i].searched === true ? coordinatesClone : geoData[i].geojson.coordinates
                                },
                                searched: false,
                                display_name: geoData[i].display_name,
                                selected: feature.properties.selected,
                                draw_type: feature.properties.draw_type,
                                lat: geoData[i].lat,
                                lon: geoData[i].lon,
                            }
                            geoDataItems.push(geoDataItem)
                        }
                    })

                this.map.setView([geoData[i].lat, geoData[i].lon], this.zoom)
            }
            EventBus.$emit('getSearchedGeoDataItems', this.geoDataItems)
        },
        setMarker (markerLocations, isConstant) {
            if (isConstant) {
                markerLocations.forEach((e) => {
                    let markerIcon = L.icon({
                        iconSize: [40.4, 44],
                        iconAnchor: [20, 43],
                        popupAnchor: [0, -51],
                        iconUrl: this.constantMarkerUrl
                    })
                    let markerLayer = L.marker(e, { icon: markerIcon })

                    if (this.markingInfos !== null) {
                        let markingInfo = this.markingInfos.filter(x => x.lat === e[0] && x.lon === e[1])[0]
                        if (markingInfo !== undefined) {
                            markerLayer.bindTooltip('Mini Grid: ' + markingInfo.name)
                            let parent = this
                            markerLayer.on('click', function () {
                                parent.routeToDetail(markingInfo.id, markingInfo.name)
                            })
                        }

                    }
                    markerLayer.addTo(this.map)
                })
            } else {
                let editableLayer = this.editableLayers
                let drawedLayers = editableLayer.getLayers()
                markerLocations.forEach((e) => {

                    let polygon = drawedLayers[0]
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
                        iconUrl: this.markerUrl
                    })
                    let markerLayer = L.marker(e, { icon: markerIcon })
                    if (this.markingInfos !== null) {
                        let markingInfo = this.markingInfos.filter(x => x.lat === e[0] && x.lon === e[1])[0]
                        if (markingInfo !== undefined) {
                            if (this.isMeter) {
                                markerLayer.bindTooltip(markingInfo.serialNumber + '/' + markingInfo.id)
                                let parent = this
                                markerLayer.on('click', function () {
                                    parent.routeToDetail(markingInfo.serialNumber, markingInfo.name)
                                })
                            } else {
                                markerLayer.bindTooltip('Mini Grid: ' + markingInfo.name)
                                let parent = this
                                markerLayer.on('click', function () {
                                    parent.routeToDetail(markingInfo.id, markingInfo.name)
                                })
                            }

                        }

                    }

                    markerLayer.addTo(editableLayer)
                    if (polygon !== undefined) {
                        let bounds = polygon.getBounds()
                        if (bounds.contains(markerLayer._latlng)) {
                            markerLayer.addTo(editableLayer)
                        } else {
                            if (this.isMeter) {
                                markerLayer.addTo(editableLayer)
                                let lat = markerLayer._latlng.lat
                                let lon = markerLayer._latlng.lng
                                this.map.setView({ lat, lon }, this.zoom)
                            } else {
                                editableLayer.removeLayer(markerLayer)
                            }
                            EventBus.$emit('markerError', 'Please position your mini-grid within the selected cluster boundaries.')
                        }
                    } else {
                        let lat = markerLayer._latlng.lat
                        let lon = markerLayer._latlng.lng
                        this.map.setView({ lat, lon }, this.zoom)
                    }
                })
            }

        },
        routeToDetail (Id, name) {
            if (name === null) {
                this.$router.push('/meters/' + Id)
            } else {
                this.$router.push('/dashboards/mini-grid/' + Id)
            }

        },

        reGenerateMap(){
            this.map.flyTo(this.center,this.zoom,this.drawingOptions)
        }
    },

    watch: {
        center(){
            this.reGenerateMap()
        },
        zoom(){
            this.reGenerateMap()
        },
        geoData: debounce(function () {
            this.setLocation(this.geoData)
        }, 10),
        markerLocations: debounce(function () {
            this.setMarker(this.markerLocations, false)
        }, 10),
        constantLocations: debounce(function () {

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
