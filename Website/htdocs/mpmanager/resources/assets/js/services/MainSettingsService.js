import RepositoryFactory from '../repositories/RepositoryFactory'
import {ErrorHandler} from '../Helpers/ErrorHander'

export  class MainSettingsService{
    constructor () {
        this.repository = RepositoryFactory.get('mainSettings')
        this.mainSettings = {
            site_title: null,
            company_name: null,
            currency: null,
            country: null,
            language: null,
            vat_energy:null,
            vat_appliance:null

        }

    }

    fromJson(mainSettings){
        this.mainSettings={
            site_title: mainSettings.site_title,
            company_name:mainSettings.company_name,
            currency: mainSettings.currency,
            country: mainSettings.country,
            language: mainSettings.language,
            vat_energy: mainSettings.vat_energy,
            vat_appliance: mainSettings.vat_appliance
        }

        return this.mainSettings
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

    async update(mainSettings){
        try {
            let response = await this.repository.update(mainSettings)
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
