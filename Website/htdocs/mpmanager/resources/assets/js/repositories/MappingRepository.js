const resource ={
    'openStreetSearch':'https://nominatim.openstreetmap.org/search.php?q='
}

export default{

    get(name){
        return  axios.get(`${resource.openStreetSearch + name +'&polygon_geojson=1&format=json'}`)
    }

}
