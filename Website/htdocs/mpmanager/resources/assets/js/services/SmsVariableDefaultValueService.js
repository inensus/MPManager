import RepositoryFactory from '../repositories/RepositoryFactory'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class SmsVariableDefaultValueService {

    constructor () {
        this.repository = RepositoryFactory.get('smsVariableDefaultValue')
        this.list = []
        this.shownMessage = 'Your message will seem like..'
    }

    fromJson (smsVariableDefaultValues) {
        this.list = []
        this.list = smsVariableDefaultValues.map(defaultValue => {
            let smsVariableDefaultValue = {
                variable: '[' + defaultValue.variable + ']',
                value: defaultValue.value,
            }
            return smsVariableDefaultValue
        })
    }

    async getSmsVariableDefaultValues () {
        try {
            let response = await this.repository.list()
            if (response.status === 200) {
                this.fromJson(response.data.data)
                return response.data.data
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let erorMessage = e.response.data.message
            return new ErrorHandler(erorMessage, 'http')
        }
    }

    prepareShownMessage (body, list) {
        this.shownMessage=''
        body = list.map((e) => {
            return body.replaceAll(e.variable, e.value)
        })
        this.shownMessage = body
        if (!(body.length)){
            this.shownMessage= 'Your message will seem like..'
        }
        return this.shownMessage
    }
}
