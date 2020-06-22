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
        this.socialTariff =
            {
                dailyAllowance: null,
                price: 0,
                initialEnergyBudget: 0,
                maximumStackedEnergy: 0
            }
        this.components = []

    }

    async getTariffs ()  {
        try {
            let response = await this.repository.list()
            if (response.status === 200 || response.status === 201) {
                let data = response.data.data
                for (let i in data) {

                    let tariff_data = data[i]
                    let tariff_data_access_rate = tariff_data.access_rate
                    let accessRate = null
                    if (tariff_data_access_rate !== undefined) {
                        accessRate = new AccessRate(
                            tariff_data_access_rate.id,
                            tariff_data_access_rate.amount,
                            tariff_data_access_rate.period
                        )
                    }
                    let tariff = {
                        id: tariff_data.id,
                        name: tariff_data.name,
                        price: tariff_data.price,
                        currency: tariff_data.currency,
                        factor: tariff_data.factor,
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
        let tariff_PM = {
            'name': this.tariff.name,
            'price': this.tariff.price,
            'currency': this.tariff.currency,
            'factor': this.tariff.factor,
            'access_rate_period': this.tariff.accessRate.period,
            'access_rate_amount': this.tariff.accessRate.amount,
            'components': this.components,
            'social_tariff': {
                'daily_allowance': this.socialTariff.dailyAllowance,
                'price': this.socialTariff.price,
                'initial_energy_budget': this.socialTariff.initialEnergyBudget,
                'maximum_stacked_energy': this.socialTariff.maximumStackedEnergy
            }
        }
        try {
            let response = await this.repository.create(tariff_PM)
            if (response.status === 200 || response.status === 201) {
                this.tariff.id = response.data.data.id
                EventBus.$emit('tariffAdded', this.tariff)
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }

        } catch (e) {

            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }

    }

    setAccessRate (accessRate) {
        this.tariff.accessRate = accessRate
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

