import RepositoryFactory from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'


export class MapSettingsService {
    constructor () {
        this.repository = RepositoryFactory.get('mapSettings')
        this.mapSettings = {
            zoom: null,
            latitude: null,
            longitude: null,
            provider:null,
            bingMapApiKey:null,
        }
    }

    fromJson (mapSettings) {
        this.mapSettings = {
            id: mapSettings.id,
            zoom: mapSettings.zoom,
            latitude: mapSettings.latitude,
            longitude: mapSettings.longitude,
            provider:mapSettings.provider,
            bingMapApiKey:mapSettings.bingMapApiKey
        }

        return this.mapSettings
    }

    async list () {
        try {
            let response = await this.repository.list()
            if (response.status === 200) {
                this.fromJson(response.data.data[0])
                return this.mapSettings
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }

        } catch (e) {
            let erorMessage = e.response.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }

    async update () {
        try {

            let mapSettingsPm = {
                id: this.mapSettings.id,
                zoom: this.mapSettings.zoom,
                latitude: this.mapSettings.latitude,
                longitude: this.mapSettings.longitude,
                provider: this.mapSettings.provider,
                bingMapApiKey: this.mapSettings.bingMapApiKey
            }
            let response = await this.repository.update(mapSettingsPm.id, mapSettingsPm)
            if (response.status === 200) {
                this.fromJson(response.data.data[0])
                return this.mapSettings
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let erorMessage = e.response.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }

    async checkBingMapApiKey(){
        try{
            let res = await this.repository.checkBingApiKey(this.mapSettings.bingMapApiKey)
            if(res.data.data.authenticationResultCode !== 'ValidCredentials' || res.data.data.length <= 0){
                return true
            }else{
                return false
            }
        }catch (e) {
            let erorMessage = e.response.data.message
            return new ErrorHandler(erorMessage, 'http')
        }


    }

}
