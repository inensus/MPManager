import Repository from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'


export class PersonService {
    constructor () {
        this.repository = new Repository.get('person')
        this.person = {
            id: null,
            title: null,
            education: null,
            birthDate: null,
            name: null,
            surname: null,
            gender: null,
            nationality: null,
            city: null,
            meters: []
        }
        this.fullName = null
    }

    async getPerson (personId) {
        try {

            let response = await this.repository.get(personId)

            if (response.status === 200 || response.status === 201) {
                let personData = response.data.data
                this.person = {
                    id: personData.id,
                    title: personData.title,
                    education: personData.education,
                    birthDate: personData.birth_date,
                    name: personData.name,
                    surname: personData.surname,
                    nationality: personData.citizenship != null ? personData.citizenship.country_name : 'No data available',
                    gender: personData.sex,
                    addresses: personData.addresses,
                    meters: personData.meters
                }
                return this.person
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')

        }

    }

    getFullName () {
        this.fullName = this.person.name + ' ' + this.person.surname
        return this.fullName
    }

    getId () {
        return this.person.id
    }

    async updatePerson () {
        try {
            this.updateName(this.person.name)
            if (this.person.birthDate !== null) {
                this.person.birthDate = this.isoYear(this.person.birthDate)
            }
            let response = await this.repository.update(this.person)
            if (response.status === 200 || response.status === 201) {
                return response.data.data
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')

        }
    }

    isoYear (date) {
        return moment(date).format('YYYY-MM-DD')

    }

    updateName (fullName) {
        let x = fullName.split(' ')
        if (x.length < 2) {
            return {
                'success': false,

            }
        }
        this.person.surname = x.splice(-1)
        this.person.name = x.join(' ')
    }
}
