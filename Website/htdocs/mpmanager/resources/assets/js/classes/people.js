import { Paginator } from './paginator'
import { resources } from '../resources'
import { EventBus } from '../shared/eventbus'
import { Person } from './person'

export class People {
    constructor () {
        this.list = []
        this.paginator = new Paginator(resources.person.search)
    }

    search (data) {
        EventBus.$emit('loadPage', this.paginator, data)
    }

    async updateList (data) {
        this.list = []
        for (let m in data) {
            let person = new Person().fromJson(data[m])
            person.meters = data[m].meters
            //person.fromJson(data[m])
            this.list.push(person)
        }
    }

}
