import Repository from '../repositories/RepositoryFactory'
import {ErrorHandler} from "../Helpers/ErrorHander";

export class CityService {
    constructor() {
        this.repository = Repository.get('city');
        this.cities = []
    }

    async getCities() {
        try {
            let response = await this.repository.list();
            if (response.status === 200 || response.status === 201) {
                this.cities = response.data.data;
                return this.cities;
            }else {
                return new ErrorHandler(response.error, 'http', response.status);
            }
        }catch (e) {
            return new ErrorHandler(e, 'http');
        }
    }
}
