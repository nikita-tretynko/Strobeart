import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        stripeKey: 'pk_test_51KoBxjJDWX8dXJe36Nl0GQpegKAG0crdsyGP5fyLrtPxbkrjevFioMqrQgzRS6u6JuAFHzx0QLIfo8uO9rQwRVbC00Z3QGDTwY',
        loading: false,
        user: {},
        phone: null,
        update_user_profile:null,
        isLogged: (_ => {
            return !!localStorage.getItem('strobeart_jwt')
        })(),
    },
    mutations: {
        setUser(state, data) {
            state.user = data
        },
        setUpdateUserProfile(state, data) {
            state.update_user_profile = data
        },
        setPhone(state, data) {
            state.phone = data
        },
        showLoader(state) {
            state.loading = true;
        },
        hideLoader(state) {
            state.loading = false;
        },
        setLogged(state, value) {
            state.isLogged = !!value;
        },
    },
    getters: {
        getUser(state) {
            return state.user;
        },
        getStripeKey(state){
            return state.stripeKey
        },
        isUpdatedUserProfile(state){
           return state.update_user_profile
        },
        getPhone(state) {
            return state.phone;
        },
        isLogged(state) {
            return state.isLogged;
        },
    },
    actions: {},
    modules: {}
})
