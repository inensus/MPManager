import { resources } from '../../resources'
import { ConnectionsType } from '../connection/ConnectionsType'

export class Meter {

  constructor (serialNumber) {
    this.id = null
    this.loaded = false
    this.registered = null
    this.owner = null
    this.total_revenue = null
    this.last_payment = null
    this.manufacturer = null
    this.serialNumber = serialNumber
    this.tariff = null
    this.totalRevenue = null
  }

  detail () {
    return axios.get(resources.meters.getMeters + this.serialNumber).then((response) => {
      let connectionType = new ConnectionsType()
      let result = response.data.data
      this.registered = result.created_at
      this.manufacturer = result.manufacturer
      this.tariff = result.meter_parameter.tariff
      this.owner = result.meter_parameter.owner
      this.connection = connectionType.fromJson(result.meter_parameter.connection_type)  // TODO: get connection information
      this.id = result.id
      this.loaded = true

    })
  }

  revenue () {
    return axios.get(resources.meters.revenue + this.serialNumber + '/revenue').then((response) => {
      let result = response.data.data
      this.totalRevenue = result.revenue
    })
  }
}
