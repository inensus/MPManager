import Repository from '../repositories/RepositoryFactory'
import { AccessRate } from '../classes/AccessRate'
import { EventBus } from '../shared/eventbus'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class TariffService {
    constructor () {
        this.repository = Repository.get('tariff')
        this.list = []
        this.tariff = {
            id: 0,
            name: '',
            price: null,
            currency: 'TZS',
            factor: 1,
            accessRate: null

        }
        this.accessRate = {
            id: null,
            amount: null,
            period: null
        }
        this.socialTariff =
            {
                dailyAllowance: null,
                price: null,
                initialEnergyBudget: null,
                maximumStackedEnergy: null
            }
        this.components = []

    }

    async getTariffs () {
        try {
            let response = await this.repository.list()
            if (response.status === 200 || response.status === 201) {
                this.list = []
                let data = response.data.data
                for (let i in data) {

                    let tariffData = data[i]
                    let tariffDataAccessRate = tariffData.access_rate
                    let accessRate = null
                    if (tariffDataAccessRate !== undefined) {
                        accessRate = new AccessRate(
                            tariffDataAccessRate.id,
                            tariffDataAccessRate.amount,
                            tariffDataAccessRate.period
                        )
                    }
                    let tariff = {
                        id: tariffData.id,
                        name: tariffData.name,
                        price: tariffData.price,
                        currency: tariffData.currency,
                        factor: tariffData.factor,
                        accessRate: accessRate,
                    }
                    this.list.push(tariff)
                }

                return this.list
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }

    }

    async createTariff () {
        let tariffPM = {
            'name': this.tariff.name,
            'price': Number(this.tariff.price),
            'currency': this.tariff.currency,
            'factor': this.tariff.factor,

        }
        if (this.components.length) {
            tariffPM.components = this.components.map((x) => {
                return {
                    'name': x.name,
                    'price': Number(x.price)
                }
            })
        }
        if (this.socialTariff.dailyAllowance) {
            tariffPM.social_tariff = {
                'daily_allowance': Number(this.socialTariff.dailyAllowance),
                'price': Number(this.socialTariff.price),
                'initial_energy_budget': Number(this.socialTariff.initialEnergyBudget),
                'maximum_stacked_energy': Number(this.socialTariff.maximumStackedEnergy)
            }

        }
        if (this.tariff.accessRate) {
            tariffPM.access_rate_period = Number(this.tariff.accessRate.period)
            tariffPM.access_rate_amount = Number(this.tariff.accessRate.amount)
        }
        try {

            let response = await this.repository.create(tariffPM)

            if (response.status === 200 || response.status === 201) {

                let tariffData = response.data.data
                let tariffDataAccessRate = tariffData.access_rate
                let accessRate = null
                if (tariffDataAccessRate !== undefined) {
                    accessRate = new AccessRate(
                        tariffDataAccessRate.id,
                        tariffDataAccessRate.amount,
                        tariffDataAccessRate.period
                    )
                }
                this.tariff = {
                    id: tariffData.id,
                    name: tariffData.name,
                    price: tariffData.price,
                    currency: tariffData.currency,
                    factor: tariffData.factor,
                    accessRate: accessRate,
                }
                return this.tariff
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }

        } catch (e) {

            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }

    }

    addToList (tariff) {
        this.list.push(tariff)

        return this.list
    }

    setAccessRate (hasAccessRate, accessRate) {
        this.tariff.accessRate = hasAccessRate ? accessRate : null
    }

    addAdditionalCostComponent () {
        let lastComponent = this.components[this.components.length - 1]
        if (lastComponent !== null && lastComponent !== undefined) {
            let component = {
                id: lastComponent.id + 1,
                name: '',
                price: 0
            }
            this.components.push(component)
        } else {
            let component = {
                id: 1,
                name: '',
                price: null
            }
            this.components.push(component)
        }

        return this.components
    }

    removeAdditionalComponent (id) {
        let component = this.components.filter(x => x.id === id)[0]
        if (component !== null) {
            for (let i = 0; i < this.components.length; i++) {
                if (this.components[i].id === component.id) {
                    this.components.splice(i, 1)
                }
            }
            return this.components
        } else {
            return this.components
        }
    }
}
