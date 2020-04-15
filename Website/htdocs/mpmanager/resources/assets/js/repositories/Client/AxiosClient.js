import axios from 'axios'
import { EventBus } from '../../shared/eventbus'

const baseUrl = 'http://mpmanager.local/'

let token = null

EventBus.$on('token.set', (data) => {
    console.log('Token verisi geldi')
    token = data
})

console.log('LOGIN', 'BURADAYIM')

export default axios.create(
    {
        baseUrl,
        headers: {
            'Authorization': `Bearer ${token}`
        }
    }
)
