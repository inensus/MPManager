import {EventBus} from "../../shared/eventbus";

export class ApplianceType {
    constructor (props) {
        this.id = null
        this.name = null
        this.updated_at = null
        this.edit = false
    }


    fromJson(data) {
        this.id = data.id
        this.name = data.name
        this.updated_at = data.updated_at
        return this
    }

    newAppliance(){
        return axios.post(resources.appliances.type.store, {
            'appliance_type_name': this.name
        }).then((response) => {
            this.id = response.data.data.id
            this.name = response.data.data.name
            this.updated_at = response.data.data.updated_at
            EventBus.$emit('applianceTypeAdded', this);
        })
    }

    update(){
        return axios.put(resources.appliances.type.update+ "/"+this.id, {'appliance_type_name': this.name})
            .then((response) => {
                return response
            })
    }
    delete(){
        return axios.delete(resources.appliances.type.delete+ "/"+this.id)
            .then((response) => {
                return response
            })
    }
}
