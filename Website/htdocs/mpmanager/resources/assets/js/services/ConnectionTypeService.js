import RepositoryFactory from '../repositories/RepositoryFactory'
import {ErrorHandler} from '../Helpers/ErrorHander'

export class ConnectionTypeService {
    constructor() {
        this.repository = RepositoryFactory.get('connectionTypes')
    }

    getConnectionTypes(){
        try {
                let response =this.repository.list();
                if (response.status===200){
                    return response.data.data
                }else{
                    return new ErrorHandler(response.error, 'http', response.status)
                }
        }catch (e) {
            let erorMessage =   e.response.data.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }
    createConnectionType(name){
        try {
            let response =this.repository.create(name)
            if (response.status===200 || response.status===201){
                return response.data.data;
            }else{
                return new ErrorHandler(response.error, 'http', response.status)
            }
        }catch (e) {
            let erorMessage =   e.response.data.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }
}
