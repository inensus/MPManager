import Repository from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'
import { TimeOfUsageService } from './TimeOfUsageService'

export class TariffService {
    constructor () {
        this.touService = new TimeOfUsageService()
        this.repository = Repository.get('tariff')
        this.list = []
        this.tariff = {
            id: null,
            name: '',
            price: null,
            currency: null,
            factor: 1,
            accessRate: {
                id: null,
                amount: null,
                period: null,
            },
            socialTariff: {
                id: null,
                dailyAllowance: null,
                price: null,
                initialEnergyBudget: null,
                maximumStackedEnergy: null
            },
            components: [],
            tous: []
        }
        this.hasAccessRate = false
        this.socialOptions = false
        this.times = this.generateTimes()
        this.conflicts = []
    }

    fromJson (tariffData) {
        let tariff = {
            id: tariffData.id,
            name: tariffData.name,
            price: tariffData.price,
            currency: tariffData.currency,
            factor: tariffData.factor,
            accessRate: {
                id: null,
                amount: null,
                period: null,
            },
            socialTariff: {
                id: null,
                dailyAllowance: null,
                price: null,
                initialEnergyBudget: null,
                maximumStackedEnergy: null
            },
            components: [],
            tous: []
        }

        if (tariffData.access_rate !== undefined && tariffData.access_rate !== null) {
            this.hasAccessRate = true
            tariff.accessRate = {
                id: tariffData.access_rate.id,
                amount: tariffData.access_rate.amount,
                period: tariffData.access_rate.period
            }
        }
        if (tariffData.social_tariff !== undefined && tariffData.social_tariff !== null) {
            tariff.socialTariff = {
                id: tariffData.social_tariff.id,
                dailyAllowance: tariffData.social_tariff.daily_allowance,
                price: tariffData.social_tariff.price,
                initialEnergyBudget: tariffData.social_tariff.initial_energy_budget,
                maximumStackedEnergy: tariffData.social_tariff.maximum_stacked_energy,
            }
            this.socialOptions = true
        }
        if (tariffData.pricing_component.length > 0) {
            for (let i = 0; i < tariffData.pricing_component.length; i++) {
                let component = {
                    id: tariffData.pricing_component[i].id,
                    name: tariffData.pricing_component[i].name,
                    price: tariffData.pricing_component[i].price
                }
                tariff.components.push(component)
            }
        }
        if (tariffData.tou.length > 0) {
            let price = tariffData.price / 100
            for (let i = 0; i < tariffData.tou.length; i++) {
                let tou = {
                    id: tariffData.tou[i].id,
                    start: tariffData.tou[i].start,
                    end: tariffData.tou[i].end,
                    value: tariffData.tou[i].value,
                    cost: (price * tariffData.tou[i].value) / 100
                }
                tariff.tous.push(tou)
            }
        }
        return tariff
    }

    async getTariffs () {
        try {
            let response = await this.repository.list()

            if (response.status === 200 || response.status === 201) {
                this.list = []
                let data = response.data.data
                for (let i in data) {
                    let tariffData = data[i]
                    let tariff = this.fromJson(tariffData)
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

    async getTariff (tariffId) {
        try {
            let response = await this.repository.get(tariffId)
            if (response.status === 200) {
                let tariffData = response.data.data
                this.tariff = this.fromJson(tariffData)
                return this.tariff
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    async saveTariff (method) {
        let tariffPM = {
            name: this.tariff.name,
            price: Number(this.tariff.price),
            currency: this.currency,
            factor: this.tariff.factor,
        }
        if (this.tariff.components.length > 0)
            tariffPM.components = this.tariff.components
        if (this.tariff.tous.length > 0)
            tariffPM.time_of_usage = this.tariff.tous

        if (this.tariff.socialTariff.dailyAllowance != null) {
            tariffPM.social_tariff = {
                id: this.tariff.socialTariff.id,
                daily_allowance: this.tariff.socialTariff.dailyAllowance,
                price: this.tariff.socialTariff.price,
                initial_energy_budget: this.tariff.socialTariff.initialEnergyBudget,
                maximum_stacked_energy: this.tariff.socialTariff.maximumStackedEnergy
            }
        }
        if (this.tariff.accessRate.period != null && this.tariff.accessRate.amount != null) {
            tariffPM.access_rate = {
                id: this.tariff.accessRate.id,
                access_rate_period: this.tariff.accessRate.period,
                access_rate_amount: this.tariff.accessRate.amount
            }
        }

        try {
            let response
            if (method === 'create') {
                response = await this.repository.create(tariffPM)
            } else {
                tariffPM.id = this.tariff.id
                response = await this.repository.update(tariffPM)
            }
            if (response.status === 200 || response.status === 201) {
                let tariffData = response.data.data
                return this.getTariff(tariffData.id)
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }

        } catch (e) {

            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }

    }

    async removeAdditionalComponent (addedType, id) {
        if (addedType === 'component') {
            let component = this.tariff.components.filter(x => x.id === id)[0]
            if (component !== null) {
                for (let i = 0; i < this.tariff.components.length; i++) {
                    if (this.tariff.components[i].id === component.id) {
                        this.tariff.components.splice(i, 1)
                    }
                }
            }
        } else {
            if (id > 0) {
                await this.touService.deleteTou(id)
            }
            let tou = this.tariff.tous.filter(x => x.id === id)[0]
            if (tou !== null) {
                for (let i = 0; i < this.tariff.tous.length; i++) {
                    if (this.tariff.tous[i].id === tou.id) {
                        this.tariff.tous.splice(i, 1)
                    }
                }
                this.findConflicts()
            }
        }
    }

    async tariffUsageCount (id) {
        try {
            let response = await this.repository.usages(id)
            if (response.status === 200) {
                return response.data.data
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    async deleteTariff (id) {
        try {
            let response = await this.repository.delete(id)
            if (response.status === 200) {
                return response.data.data
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    async changeMetersTariff (currentId, changeId) {
        try {
            let response = await this.repository.change(currentId, changeId)
            if (response.status === 200) {
                return response.data.data
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }

    setCurrency (currency) {
        this.currency = currency
    }

    addToList (tariff) {
        this.list.push(tariff)
        return this.list
    }

    resetAccessRate () {
        this.tariff.accessRate = {
            id: null,
            amount: null,
            period: null,
        }
    }

    addAdditionalCostComponent (addedType) {
        if (addedType === 'component') {
            let component = {
                id: -1 * Math.floor(Math.random() * 10000000),
                name: '',
                price: null
            }
            this.tariff.components.push(component)
        } else {
            let tou = {
                id: -1 * Math.floor(Math.random() * 10000000),
                start: this.getMinimumAvailableTime('start'),
                end: this.getMinimumAvailableTime('end'),
                value: null,
                cost: 0
            }
            let redundantTime = this.tariff.tous.filter(x => x.start === tou.start && x.end === tou.end)[0]
            if (!redundantTime) {
                this.times.forEach((e) => {
                    if (e.time === tou.end || e.time === tou.start) {
                        e.using = true
                    }
                })
                this.tariff.tous.push(tou)
                this.findConflicts()
            }
        }
    }

    getMinimumAvailableTime (type) {
        let endTime = this.tariff.tous.reduce((acc, val) => {
            let timeEnd = Number(val.end.split(':')[0])
            acc = (acc[1] === undefined || timeEnd > acc[1]) ? timeEnd : acc[1]
            return acc
        }, 0)
        endTime = endTime === 23 ? undefined : endTime
        if (type === 'start') {
            if (endTime) {
                let start = endTime + 1
                return start < 10 ? '0' + start + ':00' : start + ':00'
            } else {
                return '00:00'
            }
        } else {
            if (endTime) {
                let end = endTime + 2
                return end < 10 ? '0' + end + ':00' : end + ':00'
            } else {
                return '01:00'
            }

        }
    }

    resetTariff () {
        this.tariff = {
            id: null,
            name: '',
            price: null,
            currency: null,
            factor: 1,
            accessRate: {
                id: null,
                amount: null,
                period: null,
            },
            socialTariff: {
                id: null,
                dailyAllowance: null,
                price: null,
                initialEnergyBudget: null,
                maximumStackedEnergy: null
            },
            components: [],
            tous: []
        }
    }

    resetSocialTariff () {
        this.tariff.socialTariff = {
            id: null,
            dailyAllowance: null,
            price: null,
            initialEnergyBudget: null,
            maximumStackedEnergy: null,
        }
    }

    generateTimes () {
        let times = []
        for (let i = 0; i < 24; i++) {
            let timesObj = { 'id': 0, time: '', using: false }
            timesObj.id = i + 1
            if (i < 10) {
                timesObj.time = '0' + i + ':00'
            } else {
                timesObj.time = i + ':00'
            }
            times[i] = timesObj
        }
        return times
    }

    findConflicts () {
        let overlaps=[]
        let data=[]
        this.tariff.tous.forEach((e)=>{
            overlaps= this.checkOverlaps(e,data)
        })
        this.conflicts = overlaps
    }

    checkOverlaps (usage, data) {
        let overlaps=[]
        let start =Number(usage.start.split(':')[0])
        let end = Number(usage.end.split(':')[0])
        // eslint-disable-next-line no-constant-condition
        while (true) {
            const startTime = start % 24
            const endTime = (end - 1) % 24
            const id = usage.id
            if (data[startTime]) {
                overlaps.push(id)
            }
            data[startTime] = true
            start += 1
            if (endTime === startTime) {
                break
            }
        }
        return overlaps
    }
}
