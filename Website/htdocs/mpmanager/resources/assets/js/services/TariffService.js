import Repository from '../repositories/RepositoryFactory'
import {AccessRate} from "../classes/AccessRate";
import {EventBus} from "../shared/eventbus";
import {ErrorHandler} from "../Helpers/ErrorHander";

export class TariffService {
    constructor() {
        this.repository = Repository.get('tariff');
        this.list = [];
        this.tariff = {
            id: 0,
            name: '',
            price: 0,
            currency: 'TZS',
            factor: 1,
            accessRate: null

        }

    }

    async getTariffs() {
        try {
            let response = await this.repository.list();
            if (response.status === 200 || response.status === 201) {
                let data = response.data.data;
                for (let i in data) {

                    let tariff_data = data[i];
                    let tariff_data_access_rate = tariff_data.access_rate;
                    let accessRate = null;
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
                    };
                    this.list.push(tariff)
                }
                return this.list;
            } else {
                return new ErrorHandler(response.error, 'http', response.status);
            }
        } catch (e) {
            return new ErrorHandler(e, 'http');
        }

    }

    async createTariff() {
        let tariff_PM = {
            'name': this.tariff.name,
            'price': this.tariff.price,
            'currency': this.tariff.currency,
            'factor': this.tariff.factor,
            'access_rate_period': this.tariff.accessRate.period,
            'access_rate_amount': this.tariff.accessRate.amount,

        };
        try {
            let response = await this.repository.create(tariff_PM);
            if (response.status === 200 || response.status === 201) {
                this.tariff.id = response.data.data.id;
                EventBus.$emit('tariffAdded', this.tariff)
            } else {
                return new ErrorHandler(response.error, 'http', response.status);
            }

        } catch (e) {
            return new ErrorHandler(e, 'http');
        }

    }

    setAccessRate(accessRate) {
        this.tariff.accessRate = accessRate
    }

}
