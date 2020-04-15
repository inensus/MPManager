import { Paginator } from '../paginator'
import { resources } from '../../resources'
import { City } from '../Cities/city'
import { Manufacturers } from '../Manufacturer'
import { Meter } from '../Meters'
import { Target } from './Target'

export class Targets {

  constructor () {
    this.list = []
    this.paginator = new Paginator(resources.target.list)

  }

  targetAtIndex (index) {
    return index >= this.list.length ? null : this.list[index]
  }

  store (period, targetType, targetId, list) {
    axios.post(resources.target.store, {
        period: period,
        targetType: targetType,
        targetId: targetId,
        data: list
      })

  }

  async updateList (data) {
    this.list = []

    for (let t in data) {
      let target = new Target()
      let owner = null

      target = target.fromJson(data[t])

      owner = data[t].owner_type

      this.list.push({
        'target': target,
        'owner': owner,
      })
    }
  }
}
