import Repository from '../repositories/RepositoryFactory'
import { EventBus } from '../shared/eventbus'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class AuthenticationService {
    constructor () {
        this.repository = Repository.get('authentication')
        this.authenticateUser = {
            name: null,
            id: null,
            email: null,
            token: null,
            remaining_time: 0
        }
    }

    _fetchData (data) {
        try {
            this.authenticateUser = {
                id: data.user.id,
                email: data.user.email,
                token: data.access_token,
                remaining_time: data.expires_in,
                name: data.user.name
            }
            localStorage.setItem('token', this.authenticateUser.token)
            this.startTimer()
            return this.authenticateUser
        } catch (e) {
            return this.setAuthenticateUserEmpty()

        }

    }

    async authenticate (email, password) {
        try {
            let userPM = {
                email: email,
                password: password
            }
            let response = await this.repository.login(userPM)
            if (response.status === 200) {
                return this._fetchData(response.data)
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }

    }

    async refreshToken (token) {
        try {
            let response = await this.repository.refresh(token)
            if (response.status === 200) {
                return this._fetchData(response.data)
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            let errorMessage = e.response.data.data.message
            return new ErrorHandler(errorMessage, 'http')
        }
    }


    startTimer () {

        if (this.authenticateUser.remaining_time <= 0) return
        let interval = setInterval(() => {
            this.authenticateUser.remaining_time--
            if (this.authenticateUser.remaining_time <= 300 && this.authenticateUser.remaining_time > 0) {

                EventBus.$emit('ask.for.extend', this.authenticateUser.remaining_time)
            } else if (this.authenticateUser.remaining_time === 0) {
                this.$store.dispatch('auth/logOut')
                EventBus.$emit('session.end', true)
                clearInterval(interval)
            }
        }, 1000)
    }

    setAuthenticateUserEmpty () {
        this.authenticateUser = {}
        return this.authenticateUser
    }
}
