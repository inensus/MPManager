import Repository from '../repositories/RepositoryFactory'
import {ErrorHandler} from "../Helpers/ErrorHander";

export class CityService {
    constructor() {
        this.repository = Repository.get('city');
        this.cities = []
        this.cities = [];
        this.city = {
            id: 0,
            name : "",
            cluster_id : 0,
            mini_grid_id : 0,
        }
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
    async createCity() {
        let city_PM = {
            'name': this.city.name,
            'cluster_id': this.city.cluster_id,
            'mini_grid_id': this.city.mini_grid_id,

        };
        try {
            let response = await this.repository.create(city_PM);
            if (response.status === 200 || response.status === 201) {
                this.city.id = response.data.data.id;
                EventBus.$emit('Village Added', this.city)
            } else {
                return new ErrorHandler(response.error, 'http', response.status);
            }

        } catch (e) {
            return new ErrorHandler(e, 'http');
        }

    }

}
