export class ConnectionsType {

    constructor () {
        this.id = null
        this.name = null
        this.target = {newConnection: 0, totalRevenue: 0, connectedPower: 0, energyPerMonth: 0, averageRevenuePerMonth: 0,}
    }

    fromJson (jsonData) {

        if (jsonData){
            this.id = jsonData.id
            this.name = jsonData.name
        }

        return this
    }

    store () {
        return axios.post(resources.connections.store, {'name': this.name})
    }
}
