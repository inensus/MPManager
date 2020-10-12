import Repository from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class MeterTypeService {
    constructor () {
        this.repository = Repository.get('meterType')
        this.meterTypesList = []
    }

    async getMeterTypes (){
        try {
            this.meterTypesList = []
            let response = await this.repository.index()
            if( response.status === 200 ){
                let data = response.data.data
                for(let i in data){
                    let meterTypeData = data[i]
                    let meterTypeOnline = meterTypeData.online === 0 ? 'Online' : 'Offline'
                    let meterTypes = {
                        id: meterTypeData.id,
                        name: meterTypeData.max_current + 'A ' + meterTypeData.phase + 'P ' + meterTypeOnline,
                        max_current: meterTypeData.max_current,
                        online: meterTypeData.online
                    }
                    this.meterTypesList.push(meterTypes)
                }

                return this.meterTypesList

            }else{
                return new ErrorHandler(response.error, 'http', response.status)
            }
        }catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    async createMeterType (meterType){
        try {
            let response = await this.repository.store(meterType)
            if(response.status === 201 ){
                return this.getMeterTypes()
            }else{
                return new ErrorHandler(response.error, 'http', response.status)
            }

        }catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

}
