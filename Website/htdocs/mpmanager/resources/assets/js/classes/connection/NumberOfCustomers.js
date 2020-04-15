import { resources } from '../../resources'

export class NumberOfCustomers {

    constructor (props) {
        this.list = []
        this.total = 0
    }

    getList () {
        axios.get(resources.connections.number_of_customers)
            .then((response) => {
                this.fromJson(response.data.data)
            })
    }

    fromJson (jsonData) {
        for (let data in jsonData) {
            this.list.push(jsonData[data])
            this.total += jsonData[data]['total']
        }
    }

    findConnectionCustomers (connectionId) {
        let connection = (this.list.filter(c => {
            return c.connection_type_id === connectionId
        }))

        if (connection.length === 0) {
            return 0
        }
        return parseInt(connection[0].total)
    }
}


