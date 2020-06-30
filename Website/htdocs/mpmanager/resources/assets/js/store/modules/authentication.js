import { AuthenticationService } from '../../services/AuthenticationService'

export const namespaced = true

export const state = {
    service: new AuthenticationService(),
    authenticateUser: {},
    status: '',
}
export const mutations = {
    AUTH_REQUEST (state) {
        state.status = 'loading'
    },
    AUTH_SUCCESS (state, user) {
        state.status = 'success'
        state.token = user.token
        state.authenticateUser = user
    },
    AUTH_ERROR (state) {
        state.status = 'error'
    },
    SET_LOGOUT (state) {
        state.service = new AuthenticationService()
        state.status = ''
        state.token = ''
        state.authenticateUser = {}
    },
}
export const actions = {
    authenticate ({ commit, state, dispatch }, { email, password }) {

        commit('AUTH_REQUEST')
        return new Promise((resolve, reject) => {

            state.service.authenticate(email, password)
                .then(user => {

                    commit('AUTH_SUCCESS', user)
                    resolve(user)
                }).catch((e) => {
                commit('AUTH_ERROR')
                reject(e)
            })
        })

    },
    refreshToken ({ commit, state, dispatch }, token) {

        commit('AUTH_REQUEST')
        return new Promise((resolve, reject) => {
            state.service.refreshToken(token)
                .then(user => {

                    commit('AUTH_SUCCESS', user)
                    resolve(user)
                }).catch((e) => {
                commit('AUTH_ERROR')
                reject(e)
            })
        })

    },
    logOut ({ commit }) {

        return new Promise((resolve, reject) => {

            commit('SET_LOGOUT')
            localStorage.removeItem('token')
            resolve()
        })

    },

}
export const getters = {
    getAuthenticateUser: state => {
        return state.authenticateUser
    },
    getToken: state => {
        return state.authenticateUser.token
    },
    authenticationService: state => state.service,

}
