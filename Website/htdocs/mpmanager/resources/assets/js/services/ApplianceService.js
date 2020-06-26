import Repository from '../repositories/RepositoryFactory'
import {EventBus} from "../shared/eventbus";
import {Paginator} from "../classes/paginator";
import {ErrorHandler} from "../Helpers/ErrorHander";

export class ApplianceService {
    constructor() {
        this.repository = Repository.get('appliance');
        this.list = [];
        this.appliance = {
            id: null,
            name: null,
            updated_at: null,
            edit: false,
            appliance_type_name: null
        };
        this.paginator = new Paginator(resources.appliances.list);

    }

    fromJson(data) {
        this.id = data.id;
        this.name = data.name;
        this.updated_at = data.updated_at;
        return this
    }


    updateList(data) {
        this.list = [];

        for (let a in data) {

            let applianceType = {
                id: data[a].id,
                name: data[a].name,
                updated_at: data[a].updated_at,
                edit: false,
            };
            this.list.push(applianceType);
        }

    }

    async createAppliance() {
        this.appliance.appliance_type_name = this.appliance.name;
        try {
            let response = await this.repository.create(this.appliance);
            if (response.status === 200 || response.status === 201) {
                this.appliance.id = response.data.data.id;
                this.appliance.name = response.data.data.name;
                this.appliance.updated_at = response.data.data.updated_at;
                EventBus.$emit('applianceTypeAdded', this.appliance);
            } else {
                return new ErrorHandler(response.error, 'http', response.status);
            }
        } catch (e) {
            return new ErrorHandler(e, 'http');
        }

    }

    async updateAppliance(appliance) {
        try {
            let response = await this.repository.update(appliance.id, appliance);
            if (response.status === 200 || response.status === 201) {
                return response;
            } else {
                new ErrorHandler(response.error, 'http', response.status);
            }

        } catch (e) {
            return new ErrorHandler(e, 'http');
        }

    }

    async deleteAppliance(appliance) {
        try {
            let response = await this.repository.delete(appliance.id);
            if (response.status === 200 || response.status === 201) {
                return response;
            } else {
                new ErrorHandler(response.error, 'http', response.status);
            }
            return response;
        } catch (e) {
            return new ErrorHandler(e, 'http');
        }

    }
}
