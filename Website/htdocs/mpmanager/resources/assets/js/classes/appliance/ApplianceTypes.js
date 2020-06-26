import { Paginator } from '../paginator'
import { ApplianceType } from './ApplianceType'

export class ApplianceTypes {
    constructor (props) {
        this.list = []
        this.paginator = new Paginator(resources.appliances.list)
    }

    async updateList(data) {
        this.list = [];

        for (let a in data) {
            let applianceType = new ApplianceType()
            applianceType  = applianceType.fromJson(data[a])
            this.list.push(applianceType);
        }
    }
}
