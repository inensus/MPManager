import { resources } from '../resources'
import { Paginator } from './paginator'
import { Manufacturer, Manufacturers } from './Manufacturer'
import { EventBus } from '../shared/eventbus'

export class Meter {
  constructor () {

  }

  fromJson (jsonData) {
    this.id = jsonData.id
    this.serialNumber = jsonData.serial_number
    this.inUse = jsonData.in_use
    this.lastUpdate = jsonData.updated_at
    this.manufacturerId = jsonData.manufacturer_id
    this.manufacturer = null
    this.type = jsonData.meter_type.max_current + ' A ' + jsonData.meter_type.phase + ' P '
    this.online = jsonData.meter_type.online
    return this
  }

}

export class Meters {
  constructor () {
    this.list = []
    this.manufacturerList = []
    this.paginator = new Paginator(resources.meters.list)
    this.manufacturers = new Manufacturers()
  }

  addMeter (meter) {
    this.list.add(meter)
  }

  search (term) {
    this.paginator = new Paginator(resources.meters.search)
    EventBus.$emit('loadPage', this.paginator, {'term': term})
  }

  showAll () {
    this.paginator = new Paginator(resources.meters.list)
    EventBus.$emit('loadPage', this.paginator)
  }

  async updateList (data) {
    this.list = []
    if (this.manufacturerList.length === 0) {
      this.manufacturerList = await (new Manufacturers()).getList()
    }
    for (let m in data) {
      let meter = new Meter()
      meter.fromJson(data[m])
      meter.manufacturer = this.manufacturerList.find(function (_meter) {
        return _meter.id === meter.manufacturerId
      })
      if ('meter_parameter' in data[m]) {
        if (data[m].meter_parameter != null && 'tariff' in data[m].meter_parameter) {
          meter.tariff = data[m].meter_parameter.tariff.name + ' ' + (data[m].meter_parameter.tariff.price / 100)
        }
      } else {
        console.log('meter parameter not found ', data[m])
      }
      this.list.push(meter)
    }
  }

}

