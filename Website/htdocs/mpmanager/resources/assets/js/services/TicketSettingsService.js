import RepositoryFactory from '../repositories/RepositoryFactory'
import {ErrorHandler} from '../Helpers/ErrorHander'

export  class TicketSettingsService{
    constructor () {
        this.repository = RepositoryFactory.get('ticketSettings')
        this.ticketSettings = {
            name: null,
            api_token: null,
            api_url: null,
            api_key: null,

        }

    }

    fromJson(ticketSettings){
        this.ticketSettings={
            name: ticketSettings.name,
            api_token:ticketSettings.api_token,
            api_url: ticketSettings.api_url,
            api_key: ticketSettings.api_key
        }

        return this.ticketSettings
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

    async update(ticketSettings){
        try {
            let response = await this.repository.update(ticketSettings)
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
