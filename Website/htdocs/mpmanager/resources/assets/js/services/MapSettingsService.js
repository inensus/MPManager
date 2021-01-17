import RepositoryFactory from '../repositories/RepositoryFactory'
import {ErrorHandler} from '../Helpers/ErrorHander'

export  class MapSettingsService{
    constructor () {
        this.repository = RepositoryFactory.get('mapSettings')
        this.mapSettings = {
            zoom: null,
            latitude: null,
            longitude: null,
        }
    }


    fromJson(mapSettings){
        this.mapSettings={
            zoom: mapSettings.zoom,
            latitude:mapSettings.latitude,
            longitude: mapSettings.longitude,
        }

        return this.mapSettings
    }

    async list(){
        try {
            let response = await this.repository.list()
            if(response.status === 200 ){
                return this.fromJson(response.data.data[0])
            }else{
                return new ErrorHandler(response.error, 'http', response.status)
            }

        }catch (e) {
            let erorMessage = e.response.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }

    async update(mapSettings){
        try {
            let response = await this.repository.update(mapSettings)
            if(response.status === 200 ){
                return response.data.data
            }
            else{
                return new ErrorHandler(response.error, 'http', response.status)
            }
        }catch (e) {
            let erorMessage = e.response.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }
}
