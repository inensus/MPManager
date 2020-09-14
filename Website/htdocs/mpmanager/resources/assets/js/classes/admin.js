import { EventBus } from '../shared/eventbus'

export class Admin {

    constructor () {
        this.name = null
        this.id = null
        this.email = null
        this.token = null
        this.remaining_time = 0
    }

    _fetchData (response) {
        if (response.status === 200) {
            let data = response.data
            this.token = data.access_token
            this.remaining_time = data.expires_in
            this.id = data.user.id
            this.name = data.user.name
            this.email = data.user.email

            localStorage.setItem('token', this.token)
            this.startTimer()
            return true
        }
        return false
    }

    //refreshed the JWT token
    refreshToken () {
        let token = localStorage.getItem('token')
        return axios.post(resources.admin.refresh, null, { headers: { Authorization: 'Bearer' + token } })
            .then((response) => {
                return this._fetchData(response)
            }).catch(() => {
                return false
            })
    }

    // login to  system
    authenticate (email, password) {
        return axios.post(resources.admin.login, { email: email, password: password })
            .then((response) => {
                return this._fetchData(response)

            }).catch((error) => {
                console.error('ADMIN PATLADI', error)
                return false
            })
    }

    getDetails () {
        if (this.id !== null)
            return
        axios.get(resources.user.authData)
            .then(response => {
                this.id = response.data.id
                this.name = response.data.name
                this.email = response.data.email
                EventBus.$emit('adminGot')
            }
            )
    }

    getName () {
        if (this.name === null)
            this.getDetails()
        return this.name
    }

    async getId () {
        if (this.id === null)
            await this.getDetails()
        return this.id
    }

    getEmail () {
        if (this.email === null)
            this.getDetails()
        return this.email
    }

    async startTimer () {
        if (this.remaining_time <= 0) return
        let interval = setInterval(() => {
            this.remaining_time--
            if (this.remaining_time <= 300 && this.remaining_time > 0) {
                EventBus.$emit('ask.for.extend', this.remaining_time)
            } else if (this.remaining_time === 0) {
                localStorage.removeItem('token')
                EventBus.$emit('session.end', true)
                clearInterval(interval)
            }
        }, 1000)
    }

    async updateDetails (user) {

        return axios.post('api/admin/' + user.id + '/addresses',
            { phone: user.phone, street: user.street, city_id: user.city_id })
            .then((response) => {
                return response
            })
    }

    getAddress (id) {
        return axios.get('api/admin/' + id + '/addresses').then(response => {

            return response.data.data

        })
    }

    async updatePassword (user, password) {
        return axios.put('api/admin/' + user.id,
            { email: user.email, name: user.name, password: password })
            .then((response) => {

                return response
            })
    }

    async getUserList () {
        return axios.get('api/admin/users?address=1')
            .then((response) => {
                return response.data.data
            })
    }

    async createUser (user) {
        return axios.post('api/admin',
            { email: user.email, name: user.name, password: user.password })
            .then((response) => {
                return response.data.data
            })
    }

    async sendEmail (email) {
        return axios.post('api/admin/forgot-password',
            { email: email })
            .then((response) => {
                return response.data.data

            })
    }
}


