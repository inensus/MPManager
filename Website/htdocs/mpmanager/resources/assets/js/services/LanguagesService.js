import RepositoryFactory from '../repositories/RepositoryFactory'
import {ErrorHandler} from '../Helpers/ErrorHander'

export  class LanguagesService{
    constructor () {
        this.repository = RepositoryFactory.get('languagesList')
        this.languagesList = []
    }

    async list(){
        try {
            let response = await this.repository.list()
            if(response.status === 200 ){
                return response.data.data
            }else{
                return new ErrorHandler(response.error, 'http', response.status)
            }

        }catch (e) {
            let erorMessage = e.response.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }
    async download(){
        try {
            let response = await this.repository.download()
            if( response.status === 200){
                return response
            }else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        }catch (e) {
            let erorMessage = e.response.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }

}
